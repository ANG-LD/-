<?php
namespace Admin\Widget;
use Think\Controller;
class UnitWidget extends Controller{
	// 百度编辑器
	public function ueditor($id,$content=''){
		$this->assign('id',$id);
		if (!empty($content))
			$this->assign('content',htmlspecialchars_decode($content));
		layout(false);
		$this->display("Unit/ueditor");
	}
	
	// 图片管理器
	public function photoModal(){
		$user = session('user');
		$image_dir = cookie('image_dir');
		empty($image_dir) && $image_dir['id'] = 0;
		$images = M('image')->where(['image_dir_id'=>$image_dir['id'],'user_id'=>$user['id']])
							->order('ctime desc')
							->select();
		$this->assign('images',$images);
		layout(false);
		$this->display("Unit/photoModal");		
	}
	
	// 权限规则
	public function rulesModal(){
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
		$this->assign('list',$one_list);
		layout(false);
		$this->display("Unit/rulesModal");
	}
	
	// 修改分组
	public function authGroupModal(){
		$groups = M('AuthGroup')->where(['status'=>'1'])->select();
		$this->assign('groups',$groups);
		layout(false);
		$this->display("Unit/authGroupModal");
	}
}
?>