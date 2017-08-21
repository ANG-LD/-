<?php
namespace  Admin\Controller;
/**
 * 活动
 * @author 
 *
 */
use Think\Db;

class ActivityController extends CommonController {
    function _initialize() {
        $nums = ['5','10','15','20','25','30','50','100'];
        $this->assign('nums',$nums);
    }
	// 主页面
	public function index(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);

        $city = M('Unify')->where(['type'=>2])->select();
        $this->assign('city',$city);

        $data = [];
        if (!empty($_GET['username'])){
            $data['title'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['area'])){
            $data['city'] = $_GET['area'];
            $this->assign('area',$_GET['area']);
        }
        if (!empty($_GET['state'])){
            $data['type'] = $_GET['state'];
            $this->assign('state',$_GET['state']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Activity')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Activity')->limit($p->firstRow.','.$p->listRows)->where($data)->order('intime desc')->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '活动列表' );

	    $this->display();
    }
    /**
     * @添加、修改映射
     */
    public function toadd(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);

        $city = M('Unify')->where(['type'=>2])->select();
        $this->assign('city',$city);

        $id = I('id');
        if ($id){
            $a = M('Activity')->find($id);
            $a['background_img1'] = unserialize($a['background_img1']);
            $a['background_img2'] = unserialize($a['background_img2']);
            $a['background_img3'] = unserialize($a['background_img3']);
            $this->assign('a',$a);

            $topic = M('Topic')->where(['activity_id'=>$id])->order('intime asc')->select();
            $this->assign('t',$topic);

            $this->assign ( 'pagetitle', '编辑' );
        }else{
            $this->assign ( 'pagetitle', '添加' );
        }
        $this->display();
    }
    /**
     * @修改(添加)
     */
    public function doadd(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $background_img1 = I('logo');
        $array = getimagesize(C('IMG_PREFIX').$background_img1);
        $dat = ['img'=>$background_img1,'width'=>(string)$array[0],'height'=>(string)$array[1]];
        $background_img2 = I('logo3');
        $array2 = getimagesize(C('IMG_PREFIX').$background_img2);
        $dat2 = ['img'=>$background_img1,'width'=>(string)$array2[0],'height'=>(string)$array2[1]];
        $background_img3 = I('logo4');
        $array3 = getimagesize(C('IMG_PREFIX').$background_img3);
        $dat3 = ['img'=>$background_img3,'width'=>(string)$array3[0],'height'=>(string)$array3[1]];
        $data = [
            'title'=>I('title'),
            'city'=>I('area'),
            'background_img1'=>serialize($dat),
            'background_img2'=>serialize($dat2),
            'background_img3'=>serialize($dat3),
            'dis_time'=>I('dis_time'),
            'start_time'=>strtotime(I('start_time')),
            'end_time'=>strtotime(I('end_time')),
            'sign_up_count'=>I('sign_up_count'),
            'big_count'=>I('big_count')
        ];
        $topic = I('mytext');
        if ($id){
            $data['uptime'] = time();
            $result = M('Activity')->where(['activity_id'=>$id])->save($data);
        }else{
            $data['intime'] = time();
            $result = M('Activity')->add($data);
        }
        if ($result){
            $t = M('Topic')->where(['activity_id'=>$result])->select();
            if ($t){
                $ids = array_map(function($v){ return $v['topic_id'];},$t);
                M('Topic')->where(['topic_id'=>['in',$ids]])->delete();
            }
            foreach ($topic as $k=>$v){
                M('Topic')->add(['activity_id'=>$result,'title'=>$v,'intime'=>time()]);
            }
            $this->success('成功!',U('index'));
        }else{
            $this->error('失败!',U('index'));
        }
    }

    /**
     * @删除
     */
    public function del(){
        $id = I('ids');
        $rs = M('Activity')->where(['activity_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }











	
	
}