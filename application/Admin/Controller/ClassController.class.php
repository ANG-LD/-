<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/6
 * Time: 11:37
 */

namespace Admin\Controller;


class ClassController extends BaseController
{
    /**
     *@线上班级
     */
    public function online(){
        $map = array();
        !empty($_GET['username']) && $map['a.title|b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = urldecode(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = urldecode(I('end_time')); else $end_time = date("Y-m-d H:i:s");
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.is_del'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data= M("TutorLiveClass")->alias('a')
            ->field('a.id as class_id,a.name,a.start_time,a.end_time,a.intime,b.username,b.phone,b.ID')
            ->join("LEFT JOIN __USER__ b on a.tutor_id = b.user_id")
            ->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $k=>$v){
            if(time()>strtotime($v['end_time'])){
                $data[$k]['is_end'] = 1;
            }else{
                $data[$k]['is_end'] = 2;
            }
        }
        $count = M("TutorLiveClass")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.tutor_id = b.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $this->assign(['list'=>$data,'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加线上班级
     */
    public function add_online(){
        check_auth();
        if(IS_POST){
            echo json_encode(D('TutorLiveClass')->auth());
        }else{
            $user = M('User')->field('user_id,username')->where(['type'=>'2','username'=>['neq','']])->select();
            $this->assign(['user'=>$user]);
            $this->display();
        }
    }

    /**
     *@编辑线上班级
     */
    public function edit_online(){
        check_auth();
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('TutorLiveClass')->auth());
        }else{
            $user = M('User')->field('user_id,username')->where(['type'=>'2','username'=>['neq','']])->select();
            $re = M('TutorLiveClass')->where(['id'=>$id])->find();
            $this->assign(['user'=>$user,'re'=>$re]);
            $this->display('Class/add_online');
        }
    }

    /**
     *@删除线上班级
     */
    public function del_online_class(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $user = M('TutorLiveClass')->where($data)->save(['is_del'=>2]);
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@线下班级
     */
    public function offline(){
        $map = array();
        !empty($_GET['username']) && $map['a.name|b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = urldecode(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = urldecode(I('end_time')); else $end_time = date("Y-m-d H:i:s");
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.is_del'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data= M("TutorClass")->alias('a')
            ->field('a.id as class_id,a.name,a.start_time,a.end_time,a.intime,a.limit_value,a.value,a.price,a.vip_price,
            a.status,a.city,a.address,b.username,b.phone,b.ID')
            ->join("LEFT JOIN __USER__ b on a.tutor_id = b.user_id")
            ->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $k=>$v){
            if(time()>strtotime($v['end_time'])){
                $data[$k]['status'] = 3;
            }
        }
        $count = M("TutorClass")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.tutor_id = b.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $this->assign(['list'=>$data,'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加线上班级
     */
    public function add_offline(){
        check_auth();
        if(IS_POST){
            echo json_encode(D('TutorClass')->auth());
        }else{
            $user = M('User')->field('user_id,username')->where(['type'=>'2','username'=>['neq','']])->select();
            $sheng = M('Areas')->where(['level'=>1])->select();
            $this->assign(['user'=>$user,'sheng'=>$sheng]);
            $this->display();
        }
    }

    /**
     *@编辑线上班级
     */
    public function edit_offline(){
        check_auth();
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('TutorClass')->auth());
        }else{
            $user = M('User')->field('user_id,username')->where(['type'=>'2','username'=>['neq','']])->select();
            $sheng = M('Areas')->where(['level'=>1])->select();
            $re = M('TutorClass')->where(['id'=>$id])->find();
            $fid = M('Areas')->where(array('name' => $re['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['pid'] = $fid;
                $data['level'] = 2;
                $re['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $re['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['pid'] = $fid2;
                $date['level'] = 3;
                $re['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $re['qu'] = null;
            }
            $re['city_id'] = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            $re['area_id'] = M('Areas')->where(array('name' => $re['area'], 'level' => 3))->getField('id');
            $this->assign(['user'=>$user,'sheng'=>$sheng,'user'=>$user,'re'=>$re]);
            $this->display('Class/add_offline');
        }
    }

    /**
     *@改变班级状态
     */
    public function change_offline_status(){
        if(IS_POST){
            $id = I('id');
            $check = M('TutorClass')->where(['id'=>$id])->find();
            if(time()>strtotime($check['end_time'])){
                M('TutorClass')->where(['id'=>$id])->save(['status'=>2]);
                echo json_encode(array('status'=>'error','info'=>'班级已结束，不能改变状态'));
                exit;
            }
            $status = $check['status'];
            $abs = 3 - $status;
            //$arr = ['默认状态','开启状态'];
            $result = M('TutorClass')->where(['id'=>$id])->save(['status'=>$abs]);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$abs));
                exit;
            }else{
                echo json_encode(array('status'=>'error','info'=>'切换状态失败'));
                exit;
            }
        }
    }

    /**
     *@删除班级
     */
    public function del_offline_class(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $user = M('TutorLiveClass')->where($data)->save(['is_del'=>2]);
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@报名列表
     */
    public function sign_list(){
        $map = array();
        $id = I('id');
        $map['a.tutor_class_id'] = $id;
        !empty($_GET['username']) && $map['b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = urldecode(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = urldecode(I('end_time')); else $end_time = date("Y-m-d H:i:s");
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data= M("TutorClassSign")->alias('a')
            ->field('a.id as class_id,a.number,a.amount,b.username,b.phone,b.ID,a.intime')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->page($page)->limit($num)->order('a.intime desc')->select();
        $count = M("TutorClassSign")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $class = M('TutorLiveClass')->where(['id'=>$id])->find();
        $this->assign(['list'=>$data,'count'=>$count,'class'=>$class]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /*申请开班*/
    public function ask_class(){
        $map = array();
        $id = I('id');
        //$map['a.tutor_class_id'] = $id;
        !empty($_GET['username']) && $map['a.address,b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = strtotime(urldecode(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = strtotime(urldecode(I('end_time'))); else $end_time = time();
        $map['a.ask_time'] = ['between',[$start_time,$end_time]];
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data= M("TutorClassAsk")->alias('a')
            ->field('a.id as ask_id,b.username,b.phone,b.ID,a.intime,a.ask_time,a.address')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->page($page)->limit($num)->order('a.intime desc')->select();
        $count = M("TutorClassAsk")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $this->assign(['list'=>$data,'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    public function del_ask_class(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $user = M('TutorClassAsk')->where($data)->delete();
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }
}