<?php

namespace Home\Controller;


use App\Controller\EmailController;

use Think\Controller;

class AboutController extends CommonController {
	/**
	 * @案例首页
	 * Enter description here ...
	 */
	public function index() {
	    $this->assign('mark',about);
	    $this->display();
	}
	
	
	
	

}