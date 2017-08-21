<?php

namespace Home\Controller;


use App\Controller\EmailController;

use Think\Controller;

class AnliController extends CommonController {
	/**
	 * @案例首页
	 * Enter description here ...
	 */
	public function index() {
		//类型
		$lx  = M('Altype')->order("px desc")->select();
		$this->assign('lx',$lx);
		//行业
		$hy  = M('Alhy')->order("px desc")->select();
		$this->assign('hy',$hy);
		
	    
		//案例
//		$count = M('Anli')->count();//一共有多少条记录
//		$p = getpage($count,12);
//		$al  = M('Anli')->limit($p->firstRow.','.$p->listRows)->order("px desc")->select();
//		foreach ($al as $k=>$v){
//		    $al[$k]['leixing'] = M('Altype')->where("t_id=".$v['a_lxid'])->getField('t_title'); //类型名称
//		    $al[$k]['a_dis']   = strip_tags($v['a_dis']);
//		    $data['h_id']  = array('in',$v['a_hyid']);
//		    $al[$k]['hy']  = M("Alhy")->where($data)->field("h_title")->select(); //行业
//		}
//		$this->assign('al',$al);
//		
//		$this->assign("show",$p->show());
        $this->assign('mark',anli);
	    $this->display();
	}
	/**
	 * @搜索
	 */
	public function selal(){
		$leixing = $_GET['leixing'];
		$hangye  = $_GET['hangye'];
	
		if ($leixing!=0 && $hangye==-1 ){
		    $data['a_lxid'] = $leixing;
		}
		if ($leixing==0 && $hangye!=-1){
		    $data['a_hyid'] =array('like','%'.$hangye.'%');
		}
		if ($leixing!=0 && $hangye!=-1){
		    $data['a_lxid'] = $leixing;
		    $data['a_hyid'] =array('like','%'.$hangye.'%');
		}
		$count = M('Anli')->where($data)->count();//一共有多少条记录
		$p = getpage($count,12);
	    $al  = M('Anli')->limit($p->firstRow.','.$p->listRows)->where($data)->order("a_intime desc")->select();
	    foreach ($al as $k=>$v){
		    $al[$k]['leixing'] = M('Altype')->where("t_id=".$v['a_lxid'])->getField('t_title'); //类型名称
		    $al[$k]['a_dis']   = strip_tags($v['a_dis']);
		    $data['h_id']  = array('in',$v['a_hyid']);
		    $al[$k]['hy']  = M("Alhy")->where($data)->field("h_title")->select(); //行业
		}
		
		$this->assign('al',$al);
		$this->assign("show",$p->show());
		$this->assign('mark',anli);
		$this->display();
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	

}