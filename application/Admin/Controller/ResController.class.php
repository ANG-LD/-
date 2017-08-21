<?php

namespace Admin\Controller;

class ResController extends SessionController {
	
	
	function index() {
		$R = D("Res");
		
		$num=I('num',30);//每页显示行数
		import('ORG.Util.Page');//导入分页类
		$count = $R->count();//一共有多少条记录
		$page = new \Think\Page($count,$num);
		$page->setConfig('header','条记录');
		$show = $page->show();
		$list = $R->order('title')->limit($page->firstRow.','.$page->listRows)->select();
		
		$roles = $R->select();
		$this->assign("roles",$list);
		$this->assign("show",$show);
		$this->assign("pagetitle","资源管理");
		$this->display ();
	}
	
	
	function toadd() {
		$this->assign("pagetitle","资源管理-添加");
		$this->display ();
	}
	
	
	function toupdate() {
		$R = D("Res");
		$where["id"]=I("id");
		$rr = $R->where($where)->find();
		$this->assign("res",$rr);
		$this->assign("pagetitle","资源管理-修改");
		$this->display ();
	}
	function doupdate() {
		$R = D("Res");
		$where1["id"]=array("neq",I("id"));
		$where1["url"]=I("url");
		$resu = $R->where($where1)->find();
		if($resu){
			$this->error("url重名  ".$resu["title"]);
		}
		
		$where["id"]=I("id");
		$data["title"]=I("title");
		$data["url"]=I("url");
		$data["updatetime"]=getCurrentTime();
		$rr = $R->where($where)->save($data);
		if($rr){
			$this->success("修改成功");
		}else{
			$this->success("修改失败");
		}
	}
	function doadd() {
		$R = D("Res");
		$data["title"]=I("title");
		$data["url"]=I("url");
		$data["createtime"]=getCurrentTime();
		$r1 = $R->add($data);
		if($r1){
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
		$R = D ( "Res" );
		$R->startTrans();
		$RR = D ( "RoleRes" );
		$where ["id"] = array (
				"in",
				$did 
		);
		$where1 ["resid"] = array (
				"in",
				$did 
		);
		$r=$R->where ( $where )->delete ();
		$r2 = $RR->where($where1)->delete();
		if($r2===0){
			$r2=true;
		}
		
		if ($r&&$r2) {
			$R->commit();
			$this->success ( '删除资源成功',U('index') );
		} else {
			$R->rollback();
			$this->error ( '删除资源失败'  );
		}
	}
}
?>