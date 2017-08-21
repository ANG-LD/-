<?php
namespace  Admin\Controller;
/**
 * 资讯
 * @author 
 *
 */
use Think\Db;

class InfoController extends CommonController {
    function _initialize() {
        $nums = ['5','10','15','20','25','30','50','100'];
        $this->assign('nums',$nums);
    }
    public function index(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        if (!empty($_GET['username'])){
            $data['title'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['type'])){
            $data['lebel'] = $_GET['type'];
            $this->assign('type',$_GET['type']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $data['intime'] = ['gt',$start];
            $this->assign('start',$_GET['start']);
        }elseif(empty($_GET['start']) && !empty($_GET['end'])){
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['intime'] = ['lt',$end];
            $this->assign('end',$_GET['end']);
        }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['intime'] = ['between',[$start,$end]];
            $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Information')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Information')->limit($p->firstRow.','.$p->listRows)->where($data)->order('intime desc')->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '资讯列表' );

        //标签
        $lebel = M('Lebel')->where(['type'=>2])->select();
        $this->assign('lebel',$lebel);

        $this->display();
    }

    /**
     * @添加、修改映射
     */
    public function toadd(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        //标签
        $lebel = M('Lebel')->where(['type'=>2])->select();
        $this->assign('lebel',$lebel);
        $id = I('id');
        if ($id){
            $info = M('Information')->find($id);
            $this->assign('u',$info);
            $sta = '编辑';
        }else{
            $sta = '添加';
        }
        $this->assign ( 'pagetitle', $sta );
        $this->display();
    }
    /**
     * @修改
     */
    public function doadd(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $logo = I('logo');
        $data = [
            'title'=>I('title'),
            'lebel'=>I('lebels'),
            'img'=>$logo,
            'content'=>I('content'),
            'source'=>I('source'),
        ];
        if ($id){
            $data['uptime'] = time();
            M('Information')->where(['information_id'=>$id])->save($data) ? $this->success('成功!',U('index')) : $this->error('失败!',U('index'));
        }else{
            $data['intime'] = time();
            $data['date'] = date('Y-m-d',time());
            M('Information')->add($data) ? $this->success('成功!',U('index')) : $this->error('失败!',U('index'));
        }
    }

    /**
     * @删除
     */
    public function del(){
        $id = I('ids');
        $rs = M('Information')->where(['information_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }


/****************************************预告列表****************************************************/

     public function prevue(){
         if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
         unset($_POST['__hash__']);
         $data = [];
         if (!empty($_GET['username'])){
             $data['b.username|a.content'] = ['like','%'.$_GET['username'].'%'];
             $this->assign('username',$_GET['username']);
         }
         if (!empty($_GET['type'])){
             $data['lebel'] = $_GET['type'];
             $this->assign('type',$_GET['type']);
         }
         if (!empty($_GET['start']) && empty($_GET['end'])){
             $start = strtotime($_GET['start']);
             $data['a.start_time'] = ['gt',$start];
             $this->assign('start',$_GET['start']);
         }elseif(empty($_GET['start']) && !empty($_GET['end'])){
             $end = strtotime($_GET['end'])+(24*60*60-1);
             $data['a.start_time'] = ['lt',$end];
             $this->assign('end',$_GET['end']);
         }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
             $start = strtotime($_GET['start']);
             $end = strtotime($_GET['end'])+(24*60*60-1);
             $data['a.start_time'] = ['between',[$start,$end]];
             $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
         } //每页显示几条
         if (isset($_GET['nums'])){
             $nus  = intval($_GET['nums']);
         }else {
             $nus  = 10;
         }
         $this->assign("nus",$nus);
         $count = M('Prevue')->alias('a')->join('__USER__ b on a.user_id=b.user_id')->where($data)->count();//一共有多少条记录
         $p = getpage($count,$nus);
         $list =  M('Prevue')
             ->alias('a')
             ->field('a.*,b.username,b.ID')
             ->join('__USER__ b on a.user_id=b.user_id')
             ->limit($p->firstRow.','.$p->listRows)
             ->where($data)
             ->order('intime desc')
             ->select();
         $this->assign('list',$list);
         $this->assign("show",$p->show());
         $this->assign ( 'pagetitle', '预告列表' );

         //标签
         $lebel = M('Lebel')->where(['type'=>2])->select();
         $this->assign('lebel',$lebel);

         $this->display();
     }
    /**
     * @添加、修改映射
     */
    public function toadd_prevue(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        //标签
        $lebel = M('Lebel')->where(['type'=>2])->select();
        $this->assign('lebel',$lebel);
        $id = I('id');
        if ($id){
            $info = M('Prevue')->find($id);
            $info['id'] = M('User')->where(['user_id'=>$info['user_id']])->getField('ID');
            $this->assign('u',$info);
            $sta = '编辑';
        }else{
            $sta = '添加';
        }
        $this->assign ( 'pagetitle', $sta );
        $this->display();
    }

    /**
     * @验证主播ID是否存在
     */
    public function yzid(){
        $zhubo_id = I('zhubo_id');
        $me = M('User')->where(array('ID'=>$zhubo_id,'is_del'=>1))->find();
        echo $me ? 1 : 2;
    }


    /**
     * @修改
     */
    public function doadd_prevue(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $logo = I('logo');
        $data = [
            'lebel'=>I('lebels'),
            'img'=>$logo,
            'content'=>I('content'),
            'user_id'=>M('User')->where(['ID'=>I('zhubo_id')])->getField('user_id'),
            'time_dis'=>I('time_dis'),
            'start_time'=>strtotime(I('start_time')),
        ];
        if ($id){
            $data['uptime'] = time();
            M('Prevue')->where(['prevue_id'=>$id])->save($data) ? $this->success('成功!',U('prevue')) : $this->error('失败!',U('prevue'));
        }else{
            $data['intime'] = time();
            $data['date'] = date('Y-m-d',time());
            M('Prevue')->add($data) ? $this->success('成功!',U('prevue')) : $this->error('失败!',U('prevue'));
        }
    }

    /**
     * @删除
     */
    public function del_prevue(){
        $id = I('ids');
        $rs = M('Prevue')->where(['prevue_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }






	
}