<?php

namespace Home\Controller;


use App\Controller\EmailController;

use Think\Controller;

class WebController extends CommonController {
	/**
	 * @web首页
	 * Enter description here ...
	 */
	public function index() {
		$web1  = M('Web')->where("w_mid=412")->find();
		$this->assign('web1',$web1);
		$web2  = M('Web')->where("w_mid=413")->find();
		$this->assign('web2',$web2);
		$web3  = M('Web')->where("w_mid=414")->find();
		$this->assign('web3',$web3);
		
	    $this->assign('mark',web);
	    $this->display();
	}
	
	
	
	

}