<?php
/**
 * 权限管理
 */
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;
class AuthController extends BaseController{

	// 权限规则列表
	public function index(){
		$map['status'] = 1;
		$count = M('AuthRule')->where($map)->count();
		$p = $this->getpage($count,15);
		$list = M('AuthRule')->where($map)->order('path asc,pid asc')->limit($p->firstRow,$p->listRows)->select();
		$this->assign(['list' => $list,'page'=>$p->show(),'count'=>$count]);
		$url =$_SERVER['REQUEST_URI'];
		session('url',$url);
		$this->display();
	}

	public function editGroupRule(){
		$auth_rule_model = M('AuthRule');
		$count = $auth_rule_model->where(['status'=>1])->count();
		$list = $auth_rule_model->where(['pid'=>['gt',0],'status'=>1])->select();
		$one_list = $auth_rule_model->where(['pid'=>0,'status'=>1])->select();
		$data = array();
		foreach($one_list as $val){
			$data[] = $val;
			foreach($list as $v1){
				if($v1['pid'] == $val['id']){
					$data[] = $v1;
					foreach($list as $v2){
						if($v2['pid'] == $v1['id']){
							$data[] = $v2;
						}
					}
				}
			}
		}
		$this->assign(['list'=>$data,'count'=>$count]);

		$id	= I('id');
		$rule = M("AuthGroup")->field("rules")->find($id);
		$this->assign('rules', $rule['rules']);
		layout(false);
		$this->display();
	}

	public function distributeRule(){
		$id = I("id");
		$rules = I("rules");
		$res = M("AuthGroup")->where(['id'=>$id])->save(['rules'=>$rules]);
		if ($res) {
			$this->ajaxReturn(['status'=>'ok','info'=>'更新用户组权限成功']);
		} else {
			$this->ajaxReturn(['status'=>'error','info'=>'更新用户组权限失败']);
		}
	}

	// 编辑规则
	public function editRule(){
		$id = I('id');
		if(IS_POST){
			$obj		= D('AuthRule');
			$validate	= $obj->create();
			if (!$validate){
				$this->ajaxReturn(['code'=>'400','msg'=>$obj->getError()]);
			}
			$name = I('name');
			$arr = explode('/',$name);
			$pid = I('pid');
			if (empty($id)){
				$obj->add();
				$obj->id = $obj->getLastInsID();
				if($pid == 0){
					$obj->path = '0,'.$obj->id;
				}else{
					$obj->path = '0,'.$pid.','.$obj->id;
				}
				$result = $obj->where(['id'=>$obj->id])->save(['path'=>$obj->path,'type'=>$arr[0]]);
				$action	= '添加';
			} else {
				$obj->where(['id'=>$id])->save();
				if($pid == 0){
					$obj->path = '0,'.$id;
				}else{
					$obj->path = '0,'.$pid.','.$id;
				}
				$result		= $obj->where(['id'=>$id])->save(['path'=>$obj->path,'type'=>$arr[0]]);
				$action		= '编辑';
			}
			if ($result !==false){
					$this->ajaxReturn(['status'=>'ok','info'=>$action.'规则成功','url'=>session('url')]);
				}else{
					$this->ajaxReturn(['status'=>'error','info'=>$action.'规则失败',]);
				}

			return ;
		} else {
			$authRule = M('AuthRule');
			!empty($id) && $this->assign('d', $authRule->find($id));
			// 获取所有模块儿
			$blocks	= $authRule->where(['pid'=>'0','status'=>'1'])->select();
			$this->assign('blocks', $blocks);

			$this->display();
		}
	}

	/**
	 *删除分组
	 */
	public function del_group(){
		$id = I('ids');
		$data['id'] = array('in',$id);
		$result = M('AuthGroup')->where($data)->delete();
		if($result){
			echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
		}else{
			echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
		}
	}

	// 删除节点
	public function delRule(){
		$ids = I('ids');
		$map['id'] = ['in',$ids];
		$res = M('AuthRule')->where($map)->save(['status'=>2]);
		if ($res){
			echo json_encode(['status'=>'ok','info'=>'删除成功']);
		} else {
			echo json_encode(['status'=>'error','info'=>'删除失败,请稍后再试']);
		}

		return;
	}

	// 分组规则
	public function authGroup(){
		$list = M('AuthGroup')->select();
		$count = M('AuthGroup')->count();
		foreach ($list as $key=>$val){
			$list[$key]['rules'] = M('AuthRule')->where('id in ('.$val['rules'].')')->select();
		}

		$this->assign(['list'=>$list,'count'=>$count]);
		$url =$_SERVER['REQUEST_URI'];
		session('url',$url);
		$this->display();
	}

	public function editAuthGroup(){
		if (IS_POST){
			$data['title']	= I('title');
			$data['status']	= I('status');
			$data['rules']	= I('rules');
			if (empty($data['title'])){
				$this->ajaxReturn(['status'=>'error', 'info'=>'分组规则名称不能为空']);
			}

			if (empty($data['rules'])){
				$this->ajaxReturn(['status'=>'error', 'info'=>'规则列表不能为空']);
			}

			$map['id']		= I('id');
			if (empty($map['id'])){
				$res = M('AuthGroup')->add($data);
			} else {
				$res = M('AuthGroup')->where($map)->save($data);
			}

			if ($res) {
				$this->ajaxReturn(['status'=>'ok','info'=>'编辑规则分组成功','url'=>U('auth/authGroup')]);
			} else {
				$this->ajaxReturn(['status'=>'error','info'=>'编辑规则分组失败']);
			}

		} else {
			$map['id'] = I('id');
			if (!empty($map['id'])) {
				$data = M('AuthGroup')->where($map)->find();
				$this->assign('d',$data);
			}
			$rules = array();
			if($data){
				$rules = explode(',', $data['rules']);
			}

			$auth_rule_model = M('AuthRule');
			$list = $auth_rule_model->where(['status'=>1, 'pid>0'])->select();
			$one_list = $auth_rule_model->where(['status'=>1, 'pid'=>0])->select();
			foreach($one_list as &$val){
				$val['son'] = array();
				foreach($list as $k => $v1){
					if($v1['pid'] == $val['id']){
						$val['son'][$k] = $v1;
						$val['son'][$k]['son'] = array();
						foreach($list as $v2){
							if($v2['pid'] == $v1['id']){
								$val['son'][$k]['son'][] = $v2;
							}
						}
					}
				}
			}
			$this->assign(['rules'=>$rules,'list'=>$one_list]);
			$this->display();
		}
	}

	public function user(){
		!empty($_GET['username'])	&&	$map['a.name'] = ['like','%'.I('username').'%'];
		$count = M('Member')->alias('a')
			->join('LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id=b.uid')
			->join('LEFT JOIN __AUTH_GROUP__ c ON b.group_id=c.id')->count();
		$p = $this->getpage($count,10);
		$list = M('Member')->alias('a')
            ->field('a.id,a.username,a.name,a.last_login_date,a.last_login_ip,a.login_times,a.status,c.id as gid,c.title')
			->join('LEFT JOIN __AUTH_GROUP_ACCESS__ b ON a.id=b.uid')
			->join('LEFT JOIN __AUTH_GROUP__ c ON b.group_id=c.id')
			->limit($p->firstRow,$p->listRows)
			->order('a.status desc')
			->select();
		$this->assign(['list'=>$list,'page'=>$p->show(),'count'=>$count]);
		$url =$_SERVER['REQUEST_URI'];
		session('url',$url);
		$this->display();
	}

	/**
	 * 更新用户分组
	 */
	public function updateAuthGroup(){
		$map['uid'] = I('uid');
		if (empty($map['uid'])){
			$this->ajaxReturn(['code'=>'400','msg'=>'参数获取失败']);
		}
		$data['group_id'] = I('gid');
		$res = M('AuthGroupAccess')->where($map)->save($data);
		if ($res){
			$this->ajaxReturn(['code'=>'200','msg'=>'修改用户分组成功']);
		} else {
			$this->ajaxReturn(['code'=>'500','msg'=>'用户分组未改变']);
		}
	}

	/**
	 * 修改或添加管理员用户
	 */
	public function editUser(){
		if (IS_POST) {
			$data['username']		= I('username');
			$data['name']	= I('name');
			$data['head_img']	= I('head_img');
			$password			= I('password');
			$data['status']		= I('status');
			$group['group_id']		= I('gid');
			if (empty($data['username'])){
				$this->ajaxReturn(['status'=>'error','info'=>'用户名不能为空']);
			}
			if (M('Member')->where(['username'=>$data['username']])->find()){
				$this->ajaxReturn(['status'=>'error','info'=>'用户名已存在']);
			}
			if (empty($data['name'])){
				$this->ajaxReturn(['status'=>'error','info'=>'管理员姓名不能为空']);
			}
			if (empty($group['group_id'])){
				$this->ajaxReturn(['status'=>'error','info'=>'请选择分组']);
			}
			if (!empty($password)){
				$data['password'] = myencrypt($password);
			}

			$map['uid'] 		= I('id');
			if (empty($map['uid'])){
				if (empty($data['password'])){
					$this->ajaxReturn(['status'=>'error','info'=>'密码不能为空']);
				}

				M('Member')->add($data);
				$group['uid']		= M('Member')->getLastInsID();
				$res = M('AuthGroupAccess')->add($group);
				$action = '添加';
			} else {
				$where['id']	= I("id");
				$res = M('Member')->where($where)->save($data);
				$group['group_id']	= I('gid');
				M('AuthGroupAccess')->where($map)->save(['group_id'=>$group['group_id']]);
				$action = '修改';
			}

			if ($res !==false){
				$this->ajaxReturn(['status'=>'ok','info'=>$action.'管理员成功', 'url'=>U('auth/user')]);
			} else {
				$this->ajaxReturn(['status'=>'error','info'=>$action.'管理员失败']);
			}
		} else {

			$map['id'] = I('id');
			if (!empty($map['id'])){
				$data = M('Member')->where($map)->find();
				$this->assign('d',$data);
			}

			$groups = M('AuthGroup')->where(['status'=>'1'])->select();
			$this->assign('groups', $groups);
			$this->display();
		}
	}

	/**
	 * 删除用户,包括分组
	 */
	public function delUser(){
		$id = I('ids');
		if (empty($id)){
			$this->ajaxReturn(['code'=>'400','msg'=>'参数获取失败']);
		}
		$map['id'] = ['in',$id];
		M('Member')->startTrans();
		$res1 = M('Member')->where($map)->delete();
		$res2 = M('AuthGroupAccess')->where(['uid'=>['in',$id]])->delete();
		if($res1 && $res2){
			M('Member')->commit();
			$this->ajaxReturn(['status'=>'ok','info'=>'删除用户成功','url'=>session('url')]);
		} else {
			M('Member')->rollback();
			$this->ajaxReturn(['status'=>'error','info'=>'删除用户失败']);
		}
	}
}