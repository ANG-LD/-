<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/16
 * Time: 14:50
 */

namespace Admin\Controller;
class LiveController extends BaseController
{
    /**
     *@直播分类
     */
    public function index(){
        $map=[];
        $num  = I('num');
        $map['cate_id'] = '0';
        $map['type'] = '3';
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Category')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Category")->where($map)->limit($p->firstRow,$p->listRows)->order("sort desc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑直播分类一级分类
     */
    public function add_class(){
        if(IS_POST){
            echo json_encode(D('Category')->check());
        }else{
            $id = I('id');
            $re = M('Category')->where(['id'=>$id])->find();
            success($re);
        }
    }

    /**
     * @直播商品设置
     */
    public function goods(){
        $map=[];
        $num  = I('num');
        $map['a.cate_id'] = I('cate_id');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('LiveGoods')->alias('a')
            ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
            ->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M('LiveGoods')->alias('a')
            ->field("a.id,a.goods_id,b.name,b.thumb,a.intime,b.sale_price")
            ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
            ->where($map)->order("a.intime desc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@选择商品
     */
    public function check_goods(){
        $id = I('id');
        if(empty($id)){
            echo '';
            die;
        }
        $check = M('LiveGoods')->where(['cate_id'=>$id])->select();
        if($check){
            foreach($check as $k=>$v){
                $goods[] = $v['goods_id'];
            }
            $map['goods_id'] = ['not in',$goods];
        }
        $map['is_del']   = 1;
        $map['status']   = 2;
        $list = M('Goods')->where($map)->select();
        if(!empty($list)){
            $goods_list = '';
            foreach($list as $k=>$v){
                $goods_list.="<option value=".$v['goods_id'].">".$v['name']."</option>";
            }
            echo $goods_list;
        }else{
            echo '';
            die;
        }
    }

    /**
     *@添加商品
     */
    public function add_live_goods(){
        $data = $_POST;
        if(empty($data['cate_id'])){
            echo json_encode(array('status'=>'error','info'=>'分类id不能为空','class'=>''));
            die;
        }
        if(empty($data['goods_id'])){
            echo json_encode(array('status'=>'error','info'=>'商品不能为空','class'=>''));
            die;
        }
        $data['intime'] = date("Y-m-d H:i:s",time());
        $result = M('LiveGoods')->add($data);
        if($result){
            echo json_encode(array('status'=>'ok','info'=>'商品添加成功','class'=>''));
        }else{
            echo json_encode(array('status'=>'error','info'=>'商品添加失败','class'=>''));
        }
    }

    /**
     *@删除直播商品
     */
    public function del_live_goods(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('LiveGoods')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    public function live(){
        $map = array();
        !empty($_GET['username']) && $map['a.title|b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = strtotime(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = strtotime(I('end_time')); else $end_time = time();
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        //!empty($_GET['cate_id']) && $map['a.cate_id'] = I('cate_id');
        //$map['a.live_status'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M("Live")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            //->join("LEFT JOIN __CATEGORY__ c on a.cate_id = c.id")
            ->where($map)->count();
        $p = $this->getpage($count, $num);
        $data= M("Live")->alias('a')
            ->field("a.live_id,a.intime,a.play_img,a.play_address_m3u8,a.stream_key,a.start_time,a.end_time,a.title,
            a.watch_nums,a.nums,b.username,b.phone,b.ID,a.live_status,a.is_hot")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            //->join("LEFT JOIN __CATEGORY__ c on a.cate_id = c.id")
            ->where($map)->limit($p->firstRow, $p->listRows)->order('a.live_status asc,a.intime desc')->select();
        $hubName = "vxiu1";
        foreach($data as $k => $v){
            $data[$k]['view_img'] = 'http://pili-live-snapshot.tstmobile.com/'.$hubName.'/'.$v['stream_key'].'.jpg';
            $gift_count = M('Give_gift')->where(['live_id'=>$v['live_id']])->sum('jewel');
            $gift_count ? $data[$k]['gift_count'] = $gift_count : $data[$k]['gift_count'] = '0';
        }
        $this->assign(['list'=>$data,'page'=>$p->show(),'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@监测主播封面
     */
    public function check_img(){
        if(IS_POST){
            $url = I('img');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($output,true);
            if(empty($data)){
                echo json_encode(array('status'=>'ok','info'=>$data));
            }else{
                echo json_encode(array('status'=>'error','info'=>$data));
            }
        }
    }

    /**
     *@强制下线
     */
    public function offline(){
        $id = I('id');
        $live = M('Live')->where(['live_id'=>$id])->find();
        if($live['live_status'] == 1){
            $rs = M('Live')->where(['live_id'=>$id])->save(['live_status'=>2,'end_time'=>time(),'is_normal_exit'=>1,'is_offline'=>2]);
            //强制下线
            import('Vendor.Qiniu.Pili');
            $system = M('System')->where(['id' => 1])->find();
            $ak = $system['ak'];
            $sk = $system['sk'];
            $hubName = $system['hubname'];
            $mac = new \Qiniu\Pili\Mac($ak, $sk);
            $client = new \Qiniu\Pili\Client($mac);
            $hub = $client->hub($hubName);
            //获取stream
            $streamKey = $live['stream_key'];
            $stream = $hub->stream($streamKey);
            $result = $stream->disable();
        }
        echo $rs ? 1 : 2;
    }

    /**
     *@修改热门状态
     */
    public function change_live_hot(){
        if(IS_POST){
            $id = I('id');
            $status = M('Live')->where(['live_id'=>$id])->getField('is_hot');
            $abs = 3 - $status;
            //$arr = ['默认状态','开启状态'];
            $result = M('Live')->where(['live_id'=>$id])->save(['is_hot'=>$abs]);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$abs));
                exit;
            }else{
                echo json_encode(array('status'=>'error','info'=>'切换状态失败'));
                exit;
            }
        }
    }

    public function play_live(){
        $id = I('id');
        $live = M('Live')->find($id);
        $this->assign('l',$live);
        layout(false);
        $this->display();
    }



    /**
     *@录播管理
     */
    public function record(){
        $map = array();
        !empty($_GET['username']) && $map['a.title|b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = strtotime(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = strtotime(I('end_time')); else $end_time = time();
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $count = M("LiveStore")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $data= M("LiveStore")->alias('a')
            ->field("a.live_store_id,a.intime,a.play_img,a.url,a.title,a.play_number,b.username,b.phone,b.ID")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->page($page)->limit($num)->order('intime desc')->select();
        $this->assign(['list'=>$data,'count'=>$count]);
        $this->page3($count,$num);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@删除录播
     */
    public function del_live_store(){
        $id = I('ids');
        $data['live_store_id'] = array('in',$id);
        $user = M('LiveStore')->where($data)->delete();
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@观看录播
     */
    public function play_record(){
        $id = I('id');
        $re = M('LiveStore')->where(['live_store_id'=>$id])->find();
        $this->assign(['re'=>$re]);
        layout(false);
        $this->display();
    }

    /**
     *@课程管理
     */
    public function video(){
        $map = array();
        !empty($_GET['username']) && $map['a.title|b.username|b.phone|b.ID'] = array("like","%".I('username')."%");
        if(!empty($_GET['start_time'])) $start_time = strtotime(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = strtotime(I('end_time')); else $end_time = time();
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.is_del'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data= M("Video")->alias('a')
            ->field('a.title,a.video_id,a.video_img,a.url,a.watch_nums,a.zan,a.intime,a.is_shenhe,b.username,b.phone,b.ID')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $k=>$v){
            $data[$k]['url'] = C('IMG_PREFIX').$v['url'];
        }
        $count = M("Video")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $this->assign(['list'=>$data,'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加视频课程
     */
    public function add_video(){
        check_auth();
        if(IS_POST){
            echo json_encode(D('Video')->auth());
        }else{
            $user = M('User')->field('user_id,username')->where(['type'=>'2','username'=>['neq','']])->select();
            $this->assign(['user'=>$user]);
            $this->display();
        }
    }

    /**
     *@编辑视频课程
     */
    public function edit_video(){
        check_auth();
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Video')->auth());
        }else{
            $user = M('User')->field('user_id,username')->where(['type'=>'2','username'=>['neq','']])->select();
            $re = M('Video')->where(['video_id'=>$id])->find();
            $this->assign(['user'=>$user,'re'=>$re]);
            $this->display("Live/add_video");
        }
    }


    /**
     *@修改审核状态
     */
    public function change_video_shenhe(){
        if(IS_POST){
            $id = I('id');
            $status = M('Video')->where(['video_id'=>$id])->getField('is_shenhe');
            $abs = 3 - $status;
            //$arr = ['默认状态','开启状态'];
            $result = M('Video')->where(['video_id'=>$id])->save(['is_shenhe'=>$abs]);
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
     *@删除视频
     */
    public function del_video(){
        $id = I('ids');
        $data['video_id'] = array('in',$id);
        $user = M('Video')->where($data)->save(['is_del'=>2]);
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     * 送礼记录
     */
    public function give_gifts_list(){
        $map = [];
        !empty($_GET['nickname'])  && $map['c.username|c.ID|c.phone'] = ['like','%'.$_GET['nickname'].'%'];
        !empty($_GET['cname'])  && $map['d.username|d.ID|d.phone'] = ['like','%'.$_GET['cname'].'%'];
        $_GET['start_time'] ? $start_time = strtotime($_GET['start_time']) : $start_time = 0;
        $_GET['end_time']   ? $end_time   = strtotime($_GET['end_time'])  : $end_time = time();
        $map['a.intime'] = ['between',array($start_time,$end_time)];
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('GiveGift')->alias('a')
            ->join("LEFT JOIN __GIFT__ b on a.gift_id = b.gift_id")//礼物表
            ->join("LEFT JOIN __USER__ c on a.user_id = c.user_id") //送礼会员
            ->join("LEFT JOIN __USER__ d on a.user_id = d.user_id") //收礼主播
            ->where($map)->count();
        $p = $this->getpage($count, $num);
        $data = M('GiveGift')->alias('a')
            ->field("a.give_gift_id,a.intime,a.jewel,b.name,b.img,c.username,c.ID,c.phone,d.username as actor,
                d.phone as actor_phone,d.ID as actor_ID")
            ->join("LEFT JOIN __GIFT__ b on a.gift_id = b.gift_id")//礼物表
            ->join("LEFT JOIN __USER__ c on a.user_id = c.user_id") //送礼会员
            ->join("LEFT JOIN __USER__ d on a.user_id2 = d.user_id") //收礼主播
            ->where($map)->order("a.intime desc")->limit($p->firstRow, $p->listRows)->select();
        $this->assign(['list' => $data, 'page' => $p->show()]);
        $url = $_SERVER['REQUEST_URI'];
        session('url', $url);
        $this->display();
    }

    /**
     *@删除送礼记录
     */
    public function del_give_gift(){
        $id = I('ids');
        $data['give_gift_id'] = array('in',$id);
        $result = M('GiveGift')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }
}