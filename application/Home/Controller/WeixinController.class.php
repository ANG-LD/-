<?php

namespace Home\Controller;


use App\Controller\EmailController;

use Think\Controller;

class WeixinController extends CommonController {
	/**
	 * @微信
	 * @author:songxingwei
	 * @throws:2015-7-31 12:57:59
	 * Enter description here ...
	 */
	public function anli() {
		$_SESSION['code']=$_GET['code'];
		
			$anli = M('Anli')->order('px desc')->limit('0,16')->select();
			$this->assign('anli',$anli);
			
	        $this->display();
	}
    function getxia(){
    	if ($_GET['id']==1){
    		echo "1";
    		exit();
    	}
    	//下拉的时候的条件
    	if($_GET['nextrow']){
    		$pagestart=$_GET['nextrow'];
    	}else{
    		$pagestart=0;
    	}
    	$data=M('Anli')->limit($pagestart,16)->order("px desc")->select();
    	echo json_encode( $data );
    	
    
     }
	public function anli2(){
		
		$typeid = I('typeid');
		if ($typeid==1){
		   $title = "PC";
		}if($typeid==2){
		    $title = "APP";
		}if($typeid==3){
		    $title = "手机版";
		}if($typeid==4){
		    $title = "微信版";
		}
		$this->assign('title',$title);
		
	    $anli = M('Anli')->where(array('a_lxid'=>$typeid))->limit('0,16')->order('px desc')->select();
		$this->assign('anli',$anli);
		
		$this->assign('tp',$typeid);
		
	    $this->display();
	}
      function getxia2(){
    	if ($_GET['id']==1){
    		echo "1";
    		exit();
    	}
    	//下拉的时候的条件
    	if($_GET['nextrow']){
    		$pagestart=$_GET['nextrow'];
    	}else{
    		$pagestart=0;
    	}
    	$data=M('Anli')->limit($pagestart,16)->where(array('a_lxid'=>$_GET['tyid']))->order("px desc")->select();
    	echo json_encode( $data );
    	
    
     }
	//关于我们
	public function about(){
	    $this->display();
	}

}