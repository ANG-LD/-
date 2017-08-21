<?php

namespace Home\Controller;


use Org\Util\Date;

use App\Controller\EmailController;

use Think\Controller;

class NewsController extends CommonController {
	/**
	 * @新闻
	 * Enter description here ...
	 */
	public function index() {
		$id = intval($_GET['id']);
		if (isset($id)){
		     $nid = M('Newtype')->where('t_id='.$id)->find(); //年份第一个
		     $this->assign('nid',$nid);
		}
		
		$news = M('News')->where('n_typeid ='.$id)->order('n_intime desc')->limit(0,6)->select();
		foreach ($news as $k=>$v){
		    $news[$k]['n_content'] = strip_tags($v['n_content']);
		}
		$this->assign('news',$news);
	    $this->assign('mark',news);
	    $this->display();
	}
	public function commen(){
	    $id = intval($_POST['id']);
	    //echo $id;
		if (isset($id)){
		     $nid = M('Newtype')->where('t_id='.$id)->find(); //年份
		     $this->assign('nid',$nid);
		}
		
		$news = M('News')->where('n_typeid ='.$id)->order('n_intime desc')->limit(0,6)->select();
		foreach ($news as $k=>$v){
		    $news[$k]['n_content'] = strip_tags($v['n_content']);
		}
		$this->assign('news',$news);
	    $this->display();
	}
	//点击进入详情
	public function sel(){
	    $ids = intval($_GET['nid']);
	    $id  = intval($_GET['id']);

		if (isset($ids)){
		     $nid = M('Newtype')->where('t_id='.$ids)->find(); //年份
		     $this->assign('nid',$nid);
		}
		$ness = M('News')->find($id);
		$data['n_id']   = $ness['n_id'];
		$data['n_nums'] = $ness['n_nums']+1;
		M('News')->save($data);
		$this->assign('ne',$ness);
		
		$front=M('News')->where("n_typeid ='".$ids."' and n_id <".$id)->order('n_id desc')->limit('1')->find(); //上一篇
	    $this->assign('front',$front);

	    $next= M('News')->where("n_typeid ='".$ids."' and n_id >".$id)->order('n_id asc')->limit('1')->find(); //下一篇
	    $this->assign('next',$next);
		
		$this->assign('ids',$ids);
	    $this->assign('mark',news);
	    $this->display();
	}
	
	
    public function getIndex(){
    	$nid = $_POST["nid"];
        $id = $_POST["id"];
        $n = $_POST["n"];
        $count_n = $n*6;
        $query = M('News')->where('n_typeid ='.$id)->order('n_intime desc')->limit($count_n,6)->select();
        
        //$data['count']=count($query);
        $i  = 0;
        //$html.="";
        
        foreach($query as $key=>$val){
        	$time = Date('Y-m-d',$val['n_intime']);
        	$i++;
        	$a=$val['n_id'];
        	if ($i%2==1){  	
        	$html.="<div>";
            $html.="<div class='new_l'>";
            $html.="<dl>";
            $html.="<dt><img src='/Public/home/images/new_yuan.png' /><a href='/index.php/Home/News/sel/id/$a/nid/$nid'>".cut($val['n_title'],35)."</a>".$time."</dt>";
            $html.="<dd>";
            $html.="<p>".cut(strip_tags($val['n_content']), 150)."</p>";
            $html.="<span><a href='/index.php/Home/News/sel/id/$a/nid/$nid'>".'查看详细'."</a></span>";
            $html.="<dd>";
            $html.="</dl>";
            $html.="</div>";
        	}else{
            $html.="<div class='new_r'>";
            $html.="<dl>";
            $html.="<dt><img src='/Public/home/images/new_yuan.png' /><a href='/index.php/Home/News/sel/id/$a/nid/$nid'>".cut($val['n_title'],35)."</a>".$time."</dt>";
            $html.="<dd>";
            $html.="<p>".cut(strip_tags($val['n_content']), 150)."</p>";
            $html.="<span><a href='/index.php/Home/News/sel/id/$a/nid/$nid'>".'查看详细'."</a></span>";
            $html.="<dd>";
            $html.="</dl>";
            $html.="</div>";
            $html.="</div>";
        	}
        } 
        
        //$data['html']=$html;
        echo $html;
        //echo $html;
    }
	
	
	

}