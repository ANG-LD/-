<?php

namespace Admin\Controller;

class EmailhtmlController extends SessionController {
	
	
	function index() {
		$R = D("Emailhtml");
		$list = $R->select();
		$this->assign("list",$list);
		$this->assign("pagetitle","邮件模板管理");
		$this->display ();
	}
	
	function update() {
		$R = D("Emailhtml");
		$where["html"]=I("model");
		$rr = $R->where($where)->find();
		$path =  getcwd()."/Application/App/View/EmailContent/";
		$this->assign("content",file_get_contents($path.$rr["html"].".html"));
		$this->assign("vo",$rr);
		$this->assign("pagetitle","邮件模板管理-修改");
		$this->display ();
	}
	function doupdate() {
		$R = D("Emailhtml");
		$where["html"]=I("model");
		$data["updatetime"]=getCurrentTime();
		$rr = $R->where($where)->save($data);
		$path =  getcwd()."/Application/App/View/EmailContent/";
		file_put_contents($path.I("model").".html", html_entity_decode(I("content")));
		if($rr){
			$this->success("修改成功",U("index"));
		}else{
			$this->success("修改失败");
		}
	}
}
?>