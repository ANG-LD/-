<?php
namespace  Admin\Controller;
/**
 * (说明)
 * @abstract
 * @access public
 */
class RoleController extends SessionController {
	
	
	function index() {
		$R = D("Role");
		$roles = 	$R->select();
		$this->assign("roles",$roles);
		$this->assign("pagetitle","角色管理");
		$this->display ();
	}
	
	
	function toadd() {
		
		$menus = D ( "Menus" );
		$menulist = $menus->where ( array (
				'level' => 2,
				'status' => 1
		) )->order ( 'px asc' )->select ();
		for($i = 0; $i < countArray ( $menulist ); $i ++) {
			$menulist [$i] ['xjmenus'] = $menus->where ( array (
					'pid' => $menulist [$i] ['id'],
					'status' => 1
			) )->select ();
		}
		$this->assign ( 'list', $menulist );
		$RES = D("Res");
		$ress  = $RES->select();
		$this->assign("ress",$ress);

        $this->assign("pagetitle","添加");
		$this->display ();
	}
	
	
	function toupdate() {
		$R = D("Role");
		$wherer["id"]=I("id");
		$role= $R->where($wherer)->find();
		$this->assign ( "role", $role );
		
		$menus = D ( "Menus" );
		$menulist = $menus->where ( array (
				'level' => 2,
				'status' => 1 
		) )->order ( 'px asc' )->select ();
		for($i = 0; $i < countArray ( $menulist ); $i ++) {
			$menulist [$i] ['xjmenus'] = $menus->where ( array (
					'pid' => $menulist [$i] ['id'],
					'status' => 1 
			) )->select ();
		}
		$this->assign ( 'list', $menulist );
		
		$UM = D("RoleMenu");
		$where["roleid"]=$role["id"];
		$ums=$UM->where($where)->select();
		$this->assign("yixuan",$ums);
		
		
		
		
		$RES = D("Res");
		$ress  = $RES->select();
		$this->assign("ress",$ress);
		
		$RRES = D("RoleRes");
		$rress = $RRES->where(array("roleid"=>I("id")))->select();		
		$this->assign("rress",$rress);

        $this->assign("pagetitle","修改");
		$this->display ();
	}
	function doupdate() {
		$R = D("Role");
		$R->startTrans();
		$where["id"]=I("id");
		$rolename = $R->where($where)->getField("name");
		
		$data["name"]=I("name");
		$data["update_time"]=getCurrentTime();
		$r1 = $R->where($where)->save($data);
		$RM = D("RoleMenu");
		$RRES = D("RoleRes");
		$whererm["roleid"]=I("id");
		$rd = $RM->where($whererm)->delete();
		$rd1 = $RRES->where($whererm)->delete();
		if($rd===0){
			$rd = true;
		}
		if($rd1===0){
			$rd1 = true;
		}
		$ra =true;
		
		$menus = I ( "menuIds", array () );
		$data1["roleid"]=I("id");
		for($i = 0; $i < countArray ( $menus ); $i ++) {
			$data1 ["menuid"] = $menus [$i];
			$ra = $ra&&$RM->add ( $data1 );
		}
		
		$ra1 =true;
		$data1 = array();
		$res = I ( "res", array () );
		$data1["roleid"]=I("id");
		for($i = 0; $i < countArray ( $res ); $i ++) {
			$data1 ["resid"] = $res [$i];
			$ra1 = $ra1&&$RRES->add ( $data1 );
		}
		
		if($r1&&$ra&&$rd&&$rd1&&$ra1){
			$R->commit();
			$this->success("修改成功");
		}else{
			$R->rollback();
			$this->success("修改失败");
		}
	}
	function doadd() {
		$R = D("Role");
		$R->startTrans();
		$data["name"]=I("name");
		$data["create_time"]=getCurrentTime();
		$r1 = $R->add($data);
		$RM = D("RoleMenu");
		
		$RRES = D("RoleRes");
		$whererm["roleid"]=$r1;
		$rd1 = $RRES->where($whererm)->delete();
		if($rd1==0){
			$rd1=true;
		}
		$rd2 = $RM->where($whererm)->delete();
		if($rd2==0){
			$rd2=true;
		}
		$ra =true;
		$menus = I ( "menuIds", array () );
		$data1["roleid"]=$r1;
		for($i = 0; $i < countArray ( $menus ); $i ++) {
			$data1 ["menuid"] = $menus [$i];
			$ra = $ra&&$RM->add ( $data1 );
		}
		
		
		$ra1 =true;
		$data1 = array();
		$res = I ( "res", array () );
		$data1["roleid"]=$r1;
		for($i = 0; $i < countArray ( $res ); $i ++) {
			$data1 ["resid"] = $res [$i];
			$ra1 = $ra1&&$RRES->add ( $data1 );
		}
		
		
		
		if($r1&&$ra&&$ra1&&$rd1&&$rd2){
			$R->commit();
			$this->success("添加成功","index");
		}else{
			$R->rollback();
			$this->success("添加失败");
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
		$R = D ( "Role" );
		$R->startTrans();
		$RM = D ( "RoleMenu" );
		$where ["id"] = array (
				"in",
				$did 
		);
		
		$where1 ["roleid"] = array (
				"in",
				$did 
		);
		$r=$R->where ( $where )->delete ();
		$r2 = $RM->where($where1)->delete();
		if($r2===0){
			$r2=true;
		}
		if ($r&&$r2) {
			$R->commit();
			$this->success ( '删除角色成功', U('index') );
		} else {$R->rollback();
			$this->error ( '删除角色失败'  );
		}
	}
}
?>