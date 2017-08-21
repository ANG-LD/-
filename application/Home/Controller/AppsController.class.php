<?php

namespace Home\Controller;


use App\Controller\EmailController;

use Think\Controller;

class AppsController extends CommonController {
	/**
	 * @案例首页
	 * Enter description here ...
	 */
	public function index() {
		//APP案例推荐
	    $date['a_tuijian'] = array('like','%'.'2'.'%');
	    $date['a_lxid']    = 2;
	    $app  = M('Anli')->where($date)->order('a_intime desc')->select(); 
	    $this->assign('app',$app);
	    
	    $this->assign('mark',app);
	    $this->display();
	}
	
	
	
	

}