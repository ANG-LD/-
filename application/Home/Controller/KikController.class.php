<?php

namespace Home\Controller;


use App\Controller\EmailController;

use Think\Controller;

class KikController extends CommonController {
	/**
	 * @案例首页
	 * Enter description here ...
	 */
	public function index() {
		$intro = M('Introduce')->order('px desc')->select();
		foreach ($intro as $k=>$v){
		    $intro[$k]['i_dis'] = strip_tags($v['i_dis']);
		}
		$this->assign('intro',$intro);
		//标题和介绍读取
		$types = M('Types')->where('ty_mid=410')->find();
		$this->assign('types',$types);
		
		$types2 = M('Types')->where('ty_mid=409')->find();
		$this->assign('types2',$types2);
		
		//微信名片
		$mp = M('Mp')->order('px desc')->select();
		$this->assign('mp',$mp);
		
		//微信案例推荐
		$data['a_tuijian'] = array('like','%'.'3'.'%');
	    $data['a_lxid']    = 4;
	    $wx  = M('Anli')->where($data)->order('a_intime desc')->select();        
	    $this->assign('wx',$wx);
		
	    $this->assign('mark',kik);
	    $this->display();
	}
	
	
	
	

}