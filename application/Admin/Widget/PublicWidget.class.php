<?php
namespace Admin\Widget;
use Think\Controller;
use Think\Auth;
class PublicWidget extends Controller{
	// 头部导航
	public function head(){
		$user = session('user');
		layout(false);
		$system=M("system")->where("id=1")->find();
		$this->assign('system',$system);
		$this->display("Public/_header");
	}

	
	// 左侧菜单
	public function menu(){
		$user	= session('user');
		$nav = M("AuthRule")->where(array('pid'=>0,'status'=>1))->order('id asc')->select();
		$auth = new Auth();
		$mode = 'url';
		//'or' 表示满足任一条规则即通过验证;
		//'and'则表示需满足所有规则才能通过验证
		$relation = 'or';
		if($user['username'] != 'admin') {
			foreach ($nav as $key => $val) {
				$arr = explode("/", $val['name']);
				$type = $arr[0];
				$res = $auth->check($val['name'], $user['id'], $type, $mode, $relation);
				if (!$res) {
					unset($nav[$key]);
				}else{
					$map["pid"] = $val['id'];
					$map["is_button"]='1';
					$map["status"]=1;
					$menu = M("auth_rule")->where($map)->order("id asc")->select();
					if ($user['username'] != 'admin') {
					foreach($menu as $k=>$v){
								$arr = explode("/", $v['name']);
								$type = $arr[0];
								$res = $auth->check($v['name'], $user['id'], $type, $mode, $relation);
								if (!$res) {
									unset($menu[$k]);
								}
							}
					}
					$nav[$key]['menu'] = array_values($menu);
				}

			}
		}else{
			foreach ($nav as $key => $val) {
				$map["pid"] = $val['id'];
				$map["is_button"]='1';
				$map["status"]=1;
				$menu = M("auth_rule")->where($map)->order("id asc")->select();
				$nav[$key]['menu'] = $menu;
			}
		}
		$nav = array_values($nav);
		$this->assign('nav',$nav);
		layout(false);
		$this->display("Public/_menu");
	}
	
	// 面包屑
	public function breadcrumbs($action){
		if (empty($action)){
			$menu = $this->currentMenuList();
			if (!empty($menu)){
				if (isset($menu[ACTION_NAME])){
					$this->assign('action', $menu[ACTION_NAME]['text']);
				}
			}
		} else {
			$this->assign('action', $action);
		}

		$fields = C('search');
		if(isset($fields[CONTROLLER_NAME][ACTION_NAME]['fields'])){
			$this->assign('url',$fields[CONTROLLER_NAME][ACTION_NAME]['url']);
			$this->assign('table',$fields[CONTROLLER_NAME][ACTION_NAME]['table']);
			$this->assign('fields',$fields[CONTROLLER_NAME][ACTION_NAME]['fields']);
		}
		//外来文件比对
        if(isset($fields[CONTROLLER_NAME][ACTION_NAME]['type'])){
            $this->assign('url',$fields[CONTROLLER_NAME][ACTION_NAME]['url']);
            $this->assign('type',$fields[CONTROLLER_NAME][ACTION_NAME]['type']);
        }
		layout(false);
		$this->display("Public/breadcrumbs");
	}
	
	// 浮动层
	public function float(){
		layout(false);
		$this->display("Public/float");
	}
	
	// 提示层
	public function notice(){
		layout(false);
		$this->display("Public/notice");
	}
	
	// 页脚
	public function foot(){
		layout(false);
		$this->display("Public/_footer");
	}

}