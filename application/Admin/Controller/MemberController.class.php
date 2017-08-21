<?php

namespace Admin\Controller;

/**
 * (说明)
 *
 * @abstract
 *
 *
 * @access public
 */
class MemberController extends SessionController {
	
	/**
	 * (功能描述)
	 *
	 * @param
	 *        	(类型) (参数名) (描述)
	 */
	function index() {
		$username = trim ( I ( 'username', '' ) );
		$name = trim ( I ( 'name', '' ) );
		$condition = array();
		if ($username != "") {
			$condition["username"] = $username;
		}
		if ($name != "") {
			$condition["name"] = array("like",$name);
		}
		$num = I ( 'num', 20 );
		import ( 'ORG.Util.Page' );
		$user = D ( 'Member' );
		$count = $user->where ( $condition )->count ();

		$page = new \Think\Page ( $count, $num );
		$page->setConfig ( 'header', '条记录' );
		$show = $page->show ();
		$list = $user->where ( $condition )->order ( 'id asc' )->limit ( $page->firstRow . ',' . $page->listRows )->select ();
		$role = D ( "Role" );
		for($i = 0; $i < countArray ( $list ); $i ++) {
			$ll = $list [$i] ["roleid"];
			$rr = $role->where ( array (
					"id" => $ll 
			) )->find ();
			$list [$i] ["r"] = $rr;
		}
		$this->assign ( 'ulist', $list );
		$this->assign ( 'num', $num );
		$this->assign ( 'show', $show );
		$this->assign ( 'pagetitle', '管理员管理' );
		$this->display ();
	}
	
	/**
	 * (功能描述)
	 *
	 * @param
	 *        	(类型) (参数名) (描述)
	 */
	function toadd() {
		$role = D ( 'role' );
		$this->assign ( 'rlist', $role->select () );
		
		$this->assign ( 'pagetitle', '管理员管理-添加' );
		$this->display ();
	}
	
	/**
	 * (功能描述)
	 *
	 * @param
	 *        	(类型) (参数名) (描述)
	 */
	function doadd() {
		$user = D ( 'Member' );
		$data ['password'] = md5 ( I ('password') );
		$data ['create_time'] = getCurrentTime ();
		$data ['roleid'] = I ( "role" );
		$data ['name'] = I ( "name" );
		$data ['username'] = I ( "username" );
		$r = $user->add ( $data );
		if ($r) {
			$this->success ( '添加用户成功！',U ( 'index' ));
		} else {
			$this->error ( "添加用户失败:" . $user->getDbError () );
		}
	}
	function toupdate() {
		$role = D ( 'role' );
		$this->assign ( 'rlist', $role->select () );
		
		$user = D ( "Member" );
		$uid = I ( "uid", 0 );
		$se = $user->where ( array (
				"id" => $uid 
		) )->find ();
		
		$this->assign ( "user", $se );
		
		$this->assign ( 'pagetitle', '管理员管理' );
		$this->display ();
	}
	function doupdate() {
		$user = D ( 'Member' );
		$id = I ( 'id' );
		$vo = $user->getById ( $id );
		$data ['update_time'] = getCurrentTime ();
		if (I ('password') == I ( "pwd2" ) && I ( "pwd2" ) != "") {
			$data ['password'] = md5 ( I ('password') );
		} 
		$data ["roleid"] = I ( "role" );
		$data ["status"] = I ( "status" );
		$data ["username"] = I ( "username" );
		$data ["name"] = I ( "name" );
		$data ["update_time"] = getCurrentTime ();
		$r = $user->where ( array (
				"id" => $id 
		) )->save ( $data );
		if ($r) {
			$this->success ( '修改用户成功！', U ( 'index' ) );
		} else {
			$this->error ( "修改失败:" . $user->getDbError () );
		}
	}
	
	/**
	 * (功能描述)
	 *
	 * @param
	 *        	(类型) (参数名) (描述)
	 */
	function del() {
		$did = I ( "chois", array () );
		if (in_array(1, $did)||$did==1){
			$this->error ( '不能删除admin用户！' );
		}
		$user = D ( "Member" );
		$where ["id"] = array (
				"in",
				$did 
		);
		$r = $user->where ( $where )->delete ();
		if ($r) {
			$this->success ( '删除用户成功', U ( 'index' ));
		} else {
			$this->error ( '删除用户失败！' );
		}
	}
	
	function dochangepass(){
		$p1 = I("password");
		$p2 = I("pwd2");
		if($p1==""||$p2==""){
			$this->error ( '密码不能为空！' );
		}
		if($p1!=$p2){
			$this->error ( '密码输入不一致！' );
		}
		$where["id"] = $_SESSION["user"]["id"];
		$data["password"]=md5($p1);
		$data ["update_time"] = getCurrentTime ();
		$r = D("Member")->where ( $where )->save ( $data );
		if ($r) {
			$this->success ( '修改用户成功！', U ( 'index' ) );
		} else {
			$this->error ( "修改失败!" );
		}
	}
}
?>