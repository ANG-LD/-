<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/17
 * Time: 18:25
 */

namespace Api\Controller;
class HomeController extends CommonController
{
    /**
     *@轮播图
     *@param
     */
    public function banner_list(){
        /****轮播****/
        $list = M('Banner')->field("b_id,b_img,url,b_type,title,value")
            ->where(['is_del'=>'1','status'=>'2','type'=>1])->order("sort asc")->select();
        foreach($list as &$v){
            $v['b_img'] = $this->url.$v['b_img'];
                switch($v['b_type']){
                    case 1:
                        $v['jump'] = '';
                        break;
                    case 2:
                        $v['jump'] = $this->url.'/api.php/Home/banner_url/id/'.$v['b_id'];
                        break;
                    case 3:
                        $v['jump'] = $v['value'];
                        break;
                    case 4:
                        $v['jump'] = $v['value'];
                        break;
                }
        }
        success($list);
    }

    /**
     *@轮播web跳转页
     */
    public function banner_url(){
        $b_id = I('b_id');
        $content = M('Banner')->where(['b_id'=>$b_id])->getField('content');
        $this->assign(['content'=>htmlspecialchars_decode($content)]);
        $this->display();
    }

    /**
     *@导师热门直播列表&关注列表
     *@type  1:热门  2:导师
     */
    public function live_list(){
        $user = checklogin();
        $type = I('type'); (empty($type))? error('参数错误') : true;
        //$pageSize = I('pagesize');
        //$pageSize ? $pageSize : $pageSize = 10;

        import('Vendor.Qiniu.Pili');
        $system = M('System')->where(['id' => 1])->find();
        $ak = $system['ak'];
        $sk = $system['sk'];
        $hubName = $system['hubname'];
        $mac = new \Qiniu\Pili\Mac($ak, $sk);
        $client = new \Qiniu\Pili\Client($mac);
        $hub = $client->hub($hubName);
        $resp = $hub->listLiveStreams("php-sdk-test", 100000, "");
        $resp = $resp[keys];
        if ($resp){
            $rs = M('Live')->where(['stream_key'=>['in',$resp],'is_normal_exit'=>2])->select();
            foreach ($rs as $k=>$v){
                if ($v['live_status']==2){
                    M('Live')->where(['live_id'=>$v['live_id']])->save(['live_status'=>1,'uptime'=>2]);
                }
            }
        }

        $regular = M('System')->where(['id'=>1])->getField('regular');
        switch ($regular){
            case 1:
                $order = 'a.intime desc';
                break;
            case 2:
                $order = 'a.watch_nums desc';
                break;
            case 3:
                $order = 'b.get_money desc';
                break;
        }

        $p = I('p');
        empty($p)  && $p = 1;
        switch ($type) {
            case 1:
                $data = [
                    'a.live_status' => 1,
                    'b.type' => 2,
                    'b.is_del' => 1,
                    'a.is_hot' => 1,
                    'a.is_tuijian' => 1,
                ];

                break;
            case 2:
                $follow = M('Follow')->where(['user_id' => $user['user_id']])->select();
                if($follow) {
                    foreach ($follow as $k => $v) {
                        $ids[] = $v['user_id2'];
                    }
                    $data = [
                        'a.user_id' => ['in', $ids],
                        'b.type' => 2,
                        'b.is_del' => 1,
                        'a.live_status' => 1
                    ];
                }else{
                    success(['list'=>[],'page'=>0]);
                }
                break;
            }
                $count = M('Live')->alias('a')
                    ->join('__USER__ b on a.user_id = b.user_id')
                    ->where($data)
                    ->count();
                $list =  M('Live')
                    ->alias('a')
                    ->field('a.*,b.phone,b.img,b.sex,b.username,b.ID,b.hx_username,b.grade,b.province,b.city,b.zan,b.money,b.get_money,b.url')
                    ->join('__USER__ b on a.user_id = b.user_id')
                    ->where($data)
                    ->order($order)->select();
                if ($list){
                    foreach ($list as $k=>$v){
                        $list[$k]['play_img'] = $this->url.$v['play_img'];
                        $list[$k]['img'] = $this->url.$v['img'];
                        $list[$k]['url'] = $this->url."/App/Index/share_live/live_id/" . base64_encode($v['live_id']);
                        $list[$k]['qrcode_path'] = $this->url.$v['qrcode_path'];
                        $follow = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$v['user_id']])->find();
                        $follow ? $list[$k]['is_follow'] = "2" : $list[$k]['is_follow'] = "1";
                        $get_gradeinfo = get_gradeinfo($v['grade']);
                        $list[$k]['grade_img'] = $get_gradeinfo['img'];
                        $list[$k]['name'] = $get_gradeinfo['name'];
                    }
                }else{$list=[];}
                success(['list'=>$list]);
        }

    /**
     *@精彩课程
     */
    public function video(){
        $user = checklogin();
        $p = I('p');
        empty($p)   && $p = 1;
        $pageSize = I('pagesize');
        $pageSize ? $pageSize : $pageSize = 10;
        $title = I('title');
        !empty($title)      &&  $map['a.title|b.username'] = ['like','%'.$title.'%'];
        $map['a.is_del'] = 1;
        $map['a.is_shenhe'] = 2;
        $count = M('Video')->alias('a')
            ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)
            ->count();
        $page = ceil($count/$pageSize);
        $list = M('Video')->alias('a')
            ->field("a.video_id,a.title,a.video_img,a.url,a.watch_nums,a.comments,a.share,a.zan,
            b.user_id,b.username")
            ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)
            ->limit(($p-1)*$pageSize,$pageSize)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['video_img'] = $this->url.$v['video_img'];
            $list[$k]['url'] = $this->url.$v['url'];
        }
        success(['list'=>$list,'page'=>$page]);
    }

    /**
     *@课程详情
     */
    public function  video_detail(){
        $user = checklogin();
        $video_id = I('video_id');
        if(empty($video_id))        error("参数错误");
        M('Video')->where(['video_id'=>$video_id])->setInc('watch_nums');
        $video = M('Video')->alias('a')
            ->field("a.video_id,a.title,a.video_img,a.url,a.watch_nums,a.comments,a.share,a.zan,a.content,
            b.user_id,b.username,b.mark,b.sex,b.img")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['video_id'=>$video_id])->find(); //视频信息
        $count = M('UserMark')->where(['user_id2'=>$video['user_id']])->count();      //导师评分
        if($count){
            $mark = M('UserMark')->where(['user_id2'=>$video['user_id']])->sum('mark');
            $video['mark']    = sprintf("%.1f",(($mark + 5)/($count+1)));
        }
        $video['collect_nums'] = M('Collection')->where(['type'=>2,'goods_id'=>$video_id])->count();
        //判断是否收藏
        $check = M('Collection')->where(['user_id'=>$user['user_id'],'type'=>2,'goods_id'=>$video_id])->find();
        if($check){
            $video['is_collect'] = '1';    //1是收藏
        }else{
            $video['is_collect'] = '2';    //2未收藏
        }
        $check = M('Follow')->where(['user_id'=>$user['user_id'],'user_id2'=>$video['user_id']])->find();
        if($check){     //判断是否关注
            $video['is_follow'] = '1';
        }else{
            if($user['user_id'] != $video['user_id']){
                $video['is_follow'] = '2';
            }else{
                $video['is_follow'] = '3';
            }
        }

        //视频评论
        $list = M('CommentPosts')->alias('a')
            ->field('a.content,a.comment_id,a.user_id,a.intime,a.zan,b.username,b.sex,b.img')
            ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.post_id' => $video_id, 'a.response_id' => '0','a.type'=>2])
            ->order("a.intime desc")->select();
        if($list){
            foreach ($list as $k => $v) {
                $response = M('CommentPosts')->alias('a')
                    ->field('a.content,a.comment_id,a.user_id,a.intime,a.zan,b.username,b.img,b.sex')
                    ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                    ->where(['a.response_id' => $v['comment_id']])
                    ->order("a.intime asc")->select();
                foreach ($response as $key => $val) {
                    $response[$key]['img'] = $this->url . $val['img'];
                    $response[$key]['date_value'] = translate_date($val['intime']);

                }
                $list[$k]['img'] = $this->url . $v['img'];
                $list[$k]['response'] = $response;;

                //判断时间
                $list[$k]['date_value'] = translate_date($v['intime']);

            }
        }else{
            $list = [];
        }
        $video['comment'] = $list;
        $video['video_img'] = $this->url.$video['video_img'];
        $video['img'] = $this->url.$video['img'];
        $video['url'] = $this->url.$video['url'];
        success($video);
    }

    /**
     *@收藏和取消收藏课程
     */
    public function collect_video(){
        if(IS_POST){
            $member = checklogin();
            $id = I('video_id');
            $check = M('Collection')->where(['goods_id'=>$id,'type'=>'2','user_id'=>$member['user_id']])->find();
            if($check){
                $result = M('Collection')->where(['collection_id'=>$check['collection_id']])->delete();
                if($result){
                    success(2);
                }else{
                    error("取消失败");
                }
            }else{
                $data['user_id'] = $member['user_id'];
                $data['type'] = 2;
                $data['goods_id'] = $id;
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('Collection')->add($data);
                if($result){
                    success(1);
                }else{
                    error("收藏失败");
                }
            }
        }
    }

    /**
     *@名师现场
     * @parse city
     */
    public function tutor_scene(){
        !empty($_GET['city'])   &&      $map['a.city'] = ['like','%'.I('city').'%'];
        $map['a.is_del'] = 1;
        $map['a.status'] = 2;
        $pageSize = I('pagesize');
        $pageSize ? $pageSize : $pageSize = 10;
        $p = I('p');
        $p ? $p : $p = 1;
        $count = M('TutorClass')->alias('a')
            ->join("INNER JOIN __USER__ b on a.tutor_id = b.user_id")
            ->where($map)->count();
        $page = ceil($count/$pageSize);
        $list = M('TutorClass')->alias('a')
            ->field('a.id,a.name,a.img,a.intro,a.value,a.limit_value,a.address,a.city')
            ->join("INNER JOIN __USER__ b on a.tutor_id = b.user_id")
            ->where($map)->order("a.intime desc")
            ->limit(($p-1)*$pageSize,$pageSize)->select();
        foreach($list as $k=>$v){
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@名师现场城市列表
     */
    public function city_list(){
        $list = M('Areas')->alias('a')
            ->field("b.city,a.shouzimu")
            ->join("INNER JOIN __TUTOR_CLASS__ b on b.city = a.name")
            ->where(['a.level'=>2])->group("b.city")
            ->order("a.shouzimu asc")
            ->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $arr[] = $v['shouzimu'];
            }
            $arr = array_unique($arr);
            foreach($arr as $key=>$val){
                $array[$key]['shouzimu'] = $val;
                foreach($list as $k=>$v){
                    if($val == $v['shouzimu']){
                        $array[$key]['list'][] = $v;
                    }
                }
            }
        }
        success($array);
    }

    /**
     *@名师现场详情
     *@param id
     */
    public function tutor_scene_detail(){
        $user = checklogin();
        $id = I('id');
        if(empty($id))          error("参数错误");
        $check = M('TutorClass')->field('id,img,name,address,intro,value,limit_value,price,vip_price,content,tutor_id')
            ->where(['id'=>$id])->find();
        if(!$check)             error("参数错误");
        $record = M('UpgradeRecord')
            ->where(['user_id'=>$user['user_id'],'user_id2'=>$check['tutor_id']])
            ->order("id desc")->find();         //查询是否缴费
        if($record['state'] == 2){
            $class = M('TutorLiveClass')->where(['tutor_id'=>$check['tutor_id'],'is_del'=>1])
                ->limit(1)->order("intime desc")->find();
            if(strtotime($class['end_time'])>time()){
                $check['is_vip'] = '1';
            }else{
                $check['is_vip'] = '2';
            }
        }else{
            $check['is_vip'] = '2';
        }
        $check['img'] = $this->url.$check['img'];
        $amount = '0';
        $count = '0';
        $order = M('TutorClassOrder')->where(['user_id'=>$user['user_id'],'tutor_class_id'=>$id,'state'=>2])->select();
        foreach ($order as $key=>$val){
            $amount +=  $val['amount'];
            $count +=  $val['number'];
        }
        $check['amount'] = (string)$amount;
        $check['number'] = (string)$count;

        success($check);
    }

    /*名师现场申请*/
    public function tutor_class_ask(){
        if(IS_POST){
            $user = checklogin();
            $data['address'] = I('address');
            $data['ask_time'] = I('ask_time');
            $data['user_id'] = $user['user_id'];
            if(empty($data['address']))         error("申请开班地址不能为空");
            if(empty($data['ask_time']))        error("申请开班时间不能为空");
            $data['ask_time'] = strtotime($data['ask_time']);
            $data['intime'] = date("Y-m-d H:i:s");
            $result = M('tutor_class_ask')->add($data);
            if($result){
                success("申请成功");
            }else{
                error("申请失败");
            }
        }
    }

    public function tutorWebView(){
        $id = I('id');
        $check = M('TutorClass')->field('id,img,name,address,intro,value,limit_value,price,vip_price,content,tutor_id')
            ->where(['id'=>$id])->find();
        $check['content'] = htmlspecialchars_decode($check['content']);
        $this->assign(['content'=>$check['content']]);
        $this->display();
    }

    /**
     *@名师现场下单显示金额
     */
    public function tutor_scene_order_show(){
        $user = checklogin();
        $number = I('number');
        $number  ? $number = $number : $number = 1;
        $id = I('id');
        if(empty($id))          error("参数错误");
        $check = M('TutorClass')->where(['id'=>$id])->find();
        if(!$check)             error("参数错误");
        $order = M('TutorClassOrder')->where(['user_id'=>$user['user_id'],'tutor_class_id'=>$id,'state'=>2])->select();
        if($order){
            $is_vip = 2;
        }else{
            $record = M('UpgradeRecord')
                ->where(['user_id'=>$user['user_id'],'user_id2'=>$check['user_id']])
                ->order("id desc")->find();         //查询是否缴费
            if($record['state'] == 2){
                $class = M('TutorLiveClass')->where(['tutor_id'=>$check['user_id'],'is_del'=>1])
                    ->limit(1)->order("intime desc")->find();
                if(strtotime($class['end_time'])>time()){        //判断是否到期
                    $is_vip= 1;
                }else{
                    $is_vip = 2;
                }
            }
        }

        if($is_vip == 1){
            $amount = $check['vip_price'] + ($number-1)*$check['price'];
        }else{
            $amount = $number*$check['price'];
        }
        $amount = sprintf("%.2f",$amount);

        success($amount);

    }

    /**
     *@线下班级下单
     */
    public function add_class_order(){
        if(IS_POST){
            $user = checklogin();
            $id = I('id');
            $number = I('number');
            $number  ? $number = $number : $number = 1;
            if(empty($id))          error("参数错误");
            $check = M('TutorClass')->where(['id'=>$id])->find();
            if(!$check)             error("参数错误");
            $amount = I('amount');
            $order_no = date("YmdHis", time()) . rand(100000, 999999);
            $record = M('UpgradeRecord')
                ->where(['user_id'=>$user['user_id'],'user_id2'=>$check['user_id']])
                ->order("id desc")->find();         //查询是否缴费
            if($record['state'] == 2){
                $class = M('TutorLiveClass')->where(['tutor_id'=>$check['user_id'],'is_del'=>1])
                    ->limit(1)->order("intime desc")->find();
                if(strtotime($class['end_time'])<time()){        //判断是否到期
                    $is_vip= '1';
                }else{
                    $is_vip = '2';
                }
            }else{
                $is_vip = '1';
            }
            $data = [
                'number'            => $number,
                'user_id'           => $user['user_id'],
                'tutor_class_id'    => $id,
                'amount'            => $amount,
                'state'             => 1,
                'intime'            => date("Y-m-d H:i:s"),
                'order_no'          => $order_no,
                'is_vip'            => $is_vip,
            ];
            $result = M('TutorClassOrder')->add($data);
            if($result){
                success($order_no);
            }else{
                error("下单失败");
            }
        }
    }


    /**
     *@名师指点
     */
    public function tutor_teach(){
        $uid = I('uid');
        $banner = M('Banner')->field('b_id,b_img,url,b_type,title,value')->where(['is_del'=>'1','status'=>'2','type'=>2])->select();  //banner图
        foreach($banner as $k=>$v){
            $banner[$k]['b_img'] = $this->url.$v['b_img'];
            switch($v['b_type']){
                case 1:
                    $banner[$k]['jump'] = '';
                    break;
                case 2:
                    $banner[$k]['jump'] = $this->url.'/api.php/Home/banner_url/id/'.$v['b_id'];
                    break;
                case 3:
                    $banner[$k]['jump'] = $v['value'];
                    break;
                case 4:
                    $banner[$k]['jump'] = $v['value'];
                    break;
            }
        }

        $p = I('p');
        $pageSize = I('pagesize');
        $p ? $p : $p =1;
        $pageSize  ?  $pageSize  : $pageSize = 10;
        $map['is_del'] = 1;
        $map['type'] = 2;
        $map['user_id'] = ['neq',$uid];
        $count = M('User')->where($map)->count();
        $page = ceil($count/$pageSize);
        $list = M('User')->field('user_id,sex,img,autograph,mark,hx_username,hx_password,username')
            ->where($map)->order("intime desc")
            ->limit(($p-1)*$pageSize,$pageSize)->select();
        $price = M('System')->where(['id'=>1])->getField('teach_price');
        $price = sprintf("%.2f",$price);
        foreach($list as $k=>$v){
            $list[$k]['img'] = $this->url.$v['img'];
            $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
            $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
            $list[$k]['mark'] = sprintf("%.1f",($v['mark'] + $sum)/($count+1));
            $list[$k]['fee'] = $price;
        }
        success(['banner'=>$banner,'page'=>$page,'list'=>$list]);
    }

    /**
     *@名师指点判断
     */
    public function check_teach(){
        $user = checklogin();
        if($user['type'] == 1){ //判断是学生还是导师
            $user_id = I('user_id');//导师id;
            if(empty($user_id))         error("参数错误");
            $record = M('UpgradeRecord')
                ->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])
                ->order("id desc")->find();         //查询是否缴费
            if($record){
                $class = M('TutorLiveClass')->where(['tutor_id'=>$user_id,'is_del'=>1])
                    ->limit(1)->order("intime desc")->find();
                if(strtotime($class['end_time'])>time()){
                    $month = date("Y-m-01",time());     //当月时间
                    $where['user_id'] = $user['user_id'];
                    $where['user_id2'] = $user_id;
                    $where['state'] = 2;
                    $where['intime'] = ['gt',$month];
                   $count = M('TeachOrder')->where($where)->count();
                   if(!$count){
                        $data = [
                            'user_id'   => $user['user_id'],
                            'user_id2'  => $user_id,
                            'order_no'  => date("YmdHis").rand(100000,999999),
                            'amount'    =>  '0',
                            'state'     =>  2,
                            'is_free'   =>  2,
                            'intime'    =>  date("Y-m-d H:i:s",time()),
                        ];

                        $result = M('TeachOrder')->add($data);
                        if($result){
                            success('1');
                        }
                   }

                }
            }

            $check = M('TeachOrder')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])
                ->limit(1)->order("intime desc")->find();
            if(!$check){
                success("2");//不能聊天
            }else{
                if($check['state']== 2 && $check['teach_status'] == 1){
                    success("1");//能聊天
                }else{
                    if(M('TeachOrder')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id,'state'=>2])->select()){
                        success('3');//不能聊天但是能查看记录
                    }else{
                        success("2");//不能聊天；
                    }
                }
            }
        }else{ //导师
            $user_id = I('user_id');//学员id;
            if(empty($user_id))         error("参数错误");
            $check = M('TeachOrder')->where(['user_id'=>$user_id,'user_id2'=>$user['user_id']])
                ->limit(1)->order("intime desc")->find();
            if(!$check){
                success("2");//不能聊天
            }else{
                if($check['state']== 2 && $check['teach_status'] == 1){
                    success("1");//能聊天
                }else{
                    if(M('TeachOrder')->where(['user_id'=>$user_id,'user_id2'=>$user['user_id'],'state'=>2])->select()){
                        success('3');//不能聊天但是能查看记录
                    }else{
                        success("2");//不能聊天；
                    }
                }
            }
        }

    }

    /**
     * @名师指点付费
     */
    public function setup_teach_order(){
        if(IS_POST){
            $user = checklogin();
            $user_id = I('user_id');
            if(empty($user_id))             error("参数错误");
            $data['amount'] = M('System')->where(['id'=>1])->getField('teach_price');
            $data['user_id'] = $user['user_id'];
            $data['user_id2'] = $user_id;
            $data['order_no'] = date("YmdHis").rand(100000,999999);
            $data['intime'] = date("Y-m-d H:i:s");
            $result = M('TeachOrder')->add($data);
            if($result){
                success($data['order_no']);
            }else{
                error("下单失败");
            }

        }
    }

    /**
     *@名师指点点评
     */
    public function tutor_mark(){
        if(IS_POST){
            $user = checklogin();
            $user_id = I('user_id');   //导师
            $data['mark1'] = I('mark1');//讲解
            $data['mark2'] = I('mark2');//指点
            $data['mark3'] = I('mark3');//教学
            $data['mark'] = sprintf("%.1f",($data['mark1'] + $data['mark2'] + $data['mark3'])/3);
            $check = M('TeachOrder')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])
                ->limit(1)->order("intime desc")->find();
            if($check['state']== 2){
                $re = M('user_mark')->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id,'teach_order_id'=>$check['id']])->find();
                if($re){
                    error("该指点已经评论过了");
                }
                //M('TeachOrder')->where(['id'=>$check['id']])->save(['teach_status'=>'2']);
                $data['user_id'] = $user['user_id'];
                $data['user_id2'] = $user_id;
                $data['intime'] = date("Y-m-d H:i:s");
                $data['teach_order_id'] = $check['id'];
                $result = M('user_mark')->add($data);
                if($result){
                    success("点评成功");
                }else{
                    error("点评失败");
                }
            }else{
                error("不能评论");//不能聊天
            }
        }
    }

    /**
     *@名师指点退款
     */
    public function tutor_returns(){
        if(IS_POST){
            $user = checklogin();
            $data['reason'] = I('reason');
            $data['img'] = I('img');
            $data['user_id2'] = I('user_id');
            file_put_contents('6.txt',json_encode($data),FILE_APPEND);
            if(empty($data['user_id2']))        error("请选择导师");
            $check = M('TeachOrder')->where(['user_id'=>$user['user_id'],'user_id2'=>$data['user_id2']])
                ->limit(1)->order("intime desc")->find();
            if($check['state'] == 2){
                if($check['is_free'] == 2)          error("免费指点不支持退款");
                if(empty($data['reason']))          error("请写明退款原因");
                $data['intime'] = date("Y-m-d H:i:s");
                $data['user_id'] = $user['user_id'];
                $re = M('teach_order_returns')->where(['user_id'=>$user['user_id'],'user_id2'=>$data['user_id2'],'teach_order_id'=>$check['id']])->find();
                if($re){
                    error("该指点已申请退款");
                }else{
                    $data['teach_order_id'] = $check['id'];
                    $result = M('teach_order_returns')->add($data);
                    if($result){
                        M('TeachOrder')->where(['id'=>$check['id']])->save(['teach_status'=>'2','end_time'=>date("Y-m-d H:i:s")]);
                        success("信息提交成功");
                    }else{
                        error("信息提交失败");
                    }
                }
            }else{
                error("申请退款状态失败");
            }
        }
    }

    /**
     *@导师指点结束
     */
    public function teach_end(){
        if(IS_POST){
            $user = checklogin();
            $user_id = I('user_id');    //指点学员id
            $check = M('teach_order')->where(['user_id'=>$user_id,'user_id2'=>$user['user_id']])
                ->order("intime desc")->find();
            if($check['state'] ==2 && $check['teach_status'] ==1){
                $result = M('TeachOrder')->where(['id'=>$check['id']])->save(['teach_status'=>'2','end_time'=>date("Y-m-d H:i:s")]);
                if($result){
                    success("指点结束操作成功");
                }else{
                    error("操作失败");
                }
            }else{
                error("当前状态不符合结束操作");
            }
        }
    }

    /**
     *@名师指点费用
     */
    public function teach_fee(){
        $user = checklogin();
        $price = M('System')->where(['id'=>1])->getField('teach_price');
        $price = sprintf("%.2f",$price);
        success($price);
    }

    /**
     *@名师推荐
     */
    public function tutor_tui(){
        $uid = I('uid');
        $p = I('p');
        $pageSize = I('pagesize');
        $p ? $p : $p =1;
        $pageSize  ?  $pageSize  : $pageSize = 10;
        $map['is_del'] = 1;
        $map['type'] = 2;
        $map['tuijian'] = 2;
        $map['user_id'] = ['neq',$uid];
        $name = I('name');
        $price = M('System')->where(['id'=>1])->getField('teach_price');
        $price = sprintf("%.2f",$price);
        !empty($name)       &&      $map['username|ID'] = ['like','%'.$name.'%'];
        $count = M('User')->where($map)->count();
        $page = ceil($count/$pageSize);
        $list = M('User')->field('user_id,sex,img,autograph,mark,hx_username,hx_password,username')
            ->where($map)->order("intime desc")
            ->limit(($p-1)*$pageSize,$pageSize)->select();
        foreach($list as $k=>$v){
            $list[$k]['img'] = $this->url.$v['img'];
            $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
            $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
            $list[$k]['mark'] = sprintf("%.1f",($v['mark'] + $sum)/($count+1));
            $list[$k]['fee'] = $price;
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@名师详情
     *@param user_id
     */
    public function tutor_detail(){
        $member = checklogin();
        $user_id = I('user_id');//导师id
        if(empty($user_id))     error("参数错误");
        $re = M('User')->field('user_id,img,username,ID,sex,phone,autograph,hx_username,hx_password,mark,type')
            ->where(['user_id'=>$user_id])->find();
        $re['img'] = $this->url.$re['img'];
        $re['auth'] = $re['type'];
        if($re['type'] == 2){
            $record = M('UpgradeRecord')
                ->where(['user_id'=>$member['user_id'],'user_id2'=>$user_id])
                ->order("id desc")->find();         //查询是否缴费
            if($record){
                $class = M('TutorLiveClass')->where(['tutor_id'=>$user_id,'is_del'=>1])
                    ->limit(1)->order("intime desc")->find();
                if(strtotime($class['end_time'])>time()){
                    $re['is_vip'] = '2';
                }else{
                    $re['is_vip'] = '1';
                }
            }else{
                $re['is_vip'] = '1';
            }
        }else{
            $re['is_vip'] = '1';
        }
        $check = M('Live')->where(['user_id'=>$user_id])->limit(1)->order("live_id desc")->find();
        if($check['live_status']==1){
            $re['is_live'] = '1';
            $re['live_id'] = $check['live_id'];
            $check['play_img'] = $this->url.$check['play_img'];
            if($check){
                $re['live'] = $check;
            }else{
                $re['live'] = (object)null;
            }

            $top = M('GiveGift')->alias('a')
                ->field("b.img,b.username,b.hx_username,b.hx_password,a.user_id,sum(a.jewel) as jewel,b.type")
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                ->where(['a.user_id2'=>$user_id])
                ->order("jewel desc")->limit(3)
                ->group("a.user_id")->select();
            if($top){
                foreach($top as $k=>$v){
                    $top[$k]['img'] = $this->url.$v['img'];
                }
                $re['top'] = $top;
            }else{
                $re['top'] = [];
            }

        }else{
            $re['is_live'] = '2';
            $re['live'] = (object)null;
            $re['top'] = [];
        }
        $sum = M('UserMark')->where(['user_id2'=>$user_id])->sum('mark');
        $count = M('UserMark')->where(['user_id2'=>$user_id])->count();
        $re['mark'] = sprintf("%.1f",($re['mark'] + $sum)/($count+1));
        $re['live_count'] = M('LiveStore')->where(['user_id'=>$user_id,'is_del'=>1])->count();
        $re['video_count'] = M('Video')->where(['user_id'=>$user_id,'is_del'=>1,'is_shenhe'=>2])->count();
        $check = M('Follow')->where(['user_id'=>$member['user_id'],'user_id2'=>$user_id])->find();
        if($check){     //判断是否关注
            $re['is_follow'] = '1';
        }else{
            if($member['user_id'] != $re['user_id']){
                $re['is_follow'] = '2';
            }else{
                $re['is_follow'] = '3';
            }
        }

        $shield = M('shield')->where(['user_id'=>$member['user_id'],'user_id2'=>$user_id])->find();
        if($shield){
            $re['shield'] = '2';
        }else{
            $re['shield'] = '1';
        }

        $re['follow_count'] = M('Follow')->where(['user_id'=>$user_id])->count();
        $re['fans_count'] = M('Follow')->where(['user_id2'=>$user_id])->count();
        success($re);
    }

    /**
     *导师录播列表
     */
    public function live_store(){
        $member = checklogin();
        $user_id = I('user_id');//导师id
        if(empty($user_id))     error("参数错误");
        $p = I('p');
        $pageSize = I('pagesize');
        $p      ?   $p = $p  :   $p = 1;
        $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
        $count = M('LiveStore')
            ->where(['user_id'=>$user_id])->count();
        $page = ceil($count/$pageSize);
        $live = M('LiveStore')->field('live_id,title,url,intime,play_img')
            ->where(['user_id'=>$user_id])
            ->limit(($p-1)*$pageSize,$pageSize)
            ->order("intime desc")
            ->select();
        if($live){
            foreach($live as $k=>$v){
                $live[$k]['play_img'] = $this->url.$v['play_img'];
                $live[$k]['date_value'] = translate_date(date("Y-m-d H:i:s",$v['intime']));
            }
        }else{
            $live = [];
        }
        success(['page'=>$page,'list'=>$live,'count'=>$count]);
    }

    /**
     *@ 导师视频列表
     */
    public function tutor_video(){
        $member = checklogin();
        $user_id = I('user_id');//导师id
        if(empty($user_id))     error("参数错误");
        $p = I('p');
        $pageSize = I('pagesize');
        $p      ?   $p = $p  :   $p = 1;
        $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
        $count = M('Video')->where(['user_id'=>$user_id,'is_del'=>1,'is_shenhe'=>2])->count();
        $page = ceil($count/$pageSize);
        $video = M('Video')->where(['user_id'=>$user_id,'is_del'=>1,'is_shenhe'=>2])
            ->limit(($p-1)*$pageSize,$pageSize)->order("intime desc")
            ->select();
        if($video){
            foreach($video as $k=>$v){
                $video[$k]['video_img'] = $this->url.$v['video_img'];
                $video[$k]['url'] = $this->url.$v['url'];
                $video[$k]['date_value'] = translate_date(date("Y-m-d H:i:s",$v['intime']));
            }
        }else{
            $video = [];
        }
        success(['page'=>$page,'list'=>$video,'count'=>$count]);
    }

    /**
     *@贡献榜
     *@param user_id //导师id
     */
    public function top(){
        $user = checklogin();
        $user_id = I('user_id');
        $p = I('p');
        $pageSize = I('pagesize');
        $p      ?   $p = $p  :   $p = 1;
        $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
        if(empty($user_id))         error("参数错误");
        $count = M('GiveGift')->alias('a')
            ->field('a.user_id')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.user_id2'=>$user_id])->group("a.user_id")->count();
        $page = ceil($count/$pageSize);
        $top = M('GiveGift')->alias('a')
            ->field("b.img,b.username,b.sex,b.hx_username,b.hx_password,a.user_id,sum(a.jewel) as jewel")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->order("jewel desc")->limit(($p-1)*$pageSize,$pageSize)
            ->where(['a.user_id2'=>$user_id])->group("a.user_id")->select();
        if($top){
            foreach($top as $k=>$v){
                if(!empty($pageSize)){
                    $v['ranking'] = ($p-1)*$pageSize + ($k+1)%$pageSize;
                }else{
                    $v['ranking'] = $k+1;
                }
                $top[$k]['ranking'] = "{$v['ranking']}";
                $top[$k]['img'] = $this->url.$v['img'];
            }
        }else{
            $top = [];
        }

        $count = M('GiveGift')->where(['user_id2'=>$user_id])->sum('jewel');

        success(['page'=>$page,'list'=>$top,'count'=>$count]);

    }

    /**
     *@协议内容
     */
    public function xieyi(){
        $id = I('id');
        $map['id'] = $id;
        $map['type'] = 1;
        $xieyi = M('notice')->where($map)->find();
        $xieyi['content'] = htmlspecialchars_decode($xieyi['content']);
        $this->assign(['re'=>$xieyi]);
        $this->display();
    }

    /**
     *@会员升级显示
     */
    public function upgrade_show(){
        $user = checklogin();
        $user_id = I('user_id'); //导师id
        if(empty($user_id))         error("参数错误");
        file_put_contents('7.txt',$user_id,FILE_APPEND);
        $class = M('TutorLiveClass')
            ->where(['tutor_id'=>$user_id,'is_del'=>1])
            ->limit(1)->order("id desc")
            ->find();
        if(!$class || time()>strtotime($class['end_time'])){
            error("导师尚未开班或开班已结束");
        }
        $check = M('UpgradeRecord')
            ->where(['user_id'=>$user['user_id'],'user_id2'=>$user_id])
            ->order("id desc")->find();         //查询是否缴费
        $system = M('System')->field('zuan_price,gao_price')->where(['id'=>1])->find();
        $re = M('User')->field('user_id,username,img,ID,sex')->where(['user_id'=>$user_id])->find();
        $end_time =  strtotime($class['end_time']);
        $start_time =  strtotime($class['start_time']);
        $count = get_month_value($end_time,$start_time);        //开班总月份
        $used = get_month_value(time(),$start_time);       //已开班总月份
        if($check){
            $time = strtotime($check['intime']);
            if($time<$start_time){     //缴费时间小于开班时间，未缴费
                if(!$used){       //现在是否开班
                    $re['zuan_price'] = $system['zuan_price'];
                    $re['gao_price'] = $system['gao_price'];
                    $re['status'] = '1';
                }else{
                    $re['zuan_price'] = sprintf("%.0f",$system['zuan_price']/$count*($count-$used + 1/2*$used));
                    $re['gao_price'] = sprintf("%.0f",$system['gao_price']/$count*($count-$used + 1/2*$used));
                    $re['status'] = '1';
                }
            }else{
                if($check['state'] == 2){      //已升级钻石会员
                    $re['zuan_price'] = '0';
                    $re['gao_price'] = '0';
                    $re['status'] = '3';
                }else{ //高级会员
                    $re['zuan_price'] = sprintf("%.0f",($system['zuan_price']-$system['gao_price'])/$count*($count-$used));
                    $re['gao_price'] = '0';
                    $re['status'] = '2';
                }
            }

        }else{
            if(!$used){
                $re['zuan_price'] = $system['zuan_price'];
                $re['gao_price'] = $system['gao_price'];
                $re['status'] = '1';
            }else{
                $re['zuan_price'] = sprintf("%.0f",$system['zuan_price']/$count*($count-$used + 1/2*$used));
                $re['gao_price'] = sprintf("%.0f",$system['gao_price']/$count*($count-$used + 1/2*$used));
                $re['status'] = '1';
            }
        }
        $re['img'] = $this->url.$re['img'];
        $re['date_value'] = '一年';
        success($re);
    }

    /**
     *@会员升级下单
     */
    public function setup_upgrade_order(){
        if(IS_POST){
            $user = checklogin();
            $user_id = I('user_id');
            if(empty($user_id))         error("参数错误");
            $data['amount'] = I('amount');
            if(empty($data['amount']))  error("金额错误");

            $data['status'] = I('status');
            if(empty($data['status']))      error("参数错误");
            $data['order_no'] = date("YmdHis", time()) . rand(100000, 999999);
            $data['user_id'] = $user['user_id'];
            $data['status'] = $data['status']-1;
            $data['user_id2'] = $user_id;
            $data['intime'] = date("Y-m-d H:i:s");

            $result = M('UpgradeOrder')->add($data);
            if($result){
                success($data['order_no']);
            }else{
                error("下单失败");
            }
        }
    }

    /*进入直播间判断*/
    public function check_watch(){

    }



    /**
     *@首页搜索
     *@param name
     */
    public function search(){
        $name = I('name');
        if(empty($name))        error("请输入搜索关键字");
        $map['username'] = ['like','%'.$name.'%'];
        $map['is_del'] = 1;
        $map['is_stop'] = 2;
        $map['type'] = 2;
        $p = I('p');
        $pageSize = I('pagesize');
        $p  ? $p  : $p = 1;
        $pageSize  ? $pageSize  : $pageSize = 10;
        $count = M('User')->where($map)->count();
        $page = ceil($count/$pageSize);
        $list = M('User')->field('user_id,username,sex,img,autograph,type,ID,mark')
            ->where($map)->limit(($p-1)*$pageSize,$pageSize)->order("type desc")->select();
        foreach($list as $k=>$v){
            if($v['type'] == 2){
                $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
                $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
                $list[$k]['mark'] = sprintf("%.1f",(5 + $sum)/($count+1));
            }
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@功能个模块
     */
    public function module(){
        $module = M('Module')->field('module_id,title,picture,url')->order("sort desc")->select();
        success($module);
    }

    /**
     *@定制页顶部一级分类
     */
    public function first_category(){
        $map = array();
        $map['cate_id'] = '0';
        $map['type']    = I('type');
        if(empty($map['type']))         error("参数错误");
        $list = M('Category')->field('id,category,picture')
            ->where($map)->select();
        success($list);
    }


    /**
     *@广告advert图
     *@param
     */
    public function advert(){
        $advert = M('Banner')->where(['type'=>'2','status'=>'2'])->order('sort asc')->select();
        if(empty($advert)){
            error('未找到相应数据');
        }else{
            success($advert);
        }
    }




    /**
     *城市列表
     */
//    public function city_list(){
//        $type = I('type');
//        $city = [];
//        foreach(range('A','Z') as $key=>$val){
//            if(in_array($val,array('I','U','V'))){
//                unset($key);
//            }else{
//                $city[$key]['zimu'] = $val;
//                $data = M('Area')->field('id,city,shouzimu,code,type')->where(['shouzimu'=>['like','%'.$val.'%'],'type'=>$type])->group('city')->select();
//                $city[$key]['data'] = $data;
//            }
//        }
//        $city = array_values($city);
//        success($city);
//    }



    /**
     *根据三字码/城市/机场模糊搜索城市名
     */
    public function search_city(){
        if(IS_POST){
            $city = I('city');
            if(preg_match("/^[a-zA-Z\s]+$/",$city)){
                $city = strtoupper($city);
            }
            $list = M('Area')->field("type,city,country,code")->where(['city|jichang|code|shouzimu'=>['like','%'.$city.'%']])->group("city")->select();
            if($list){
                success($list);
            }else{
                error('操作失败');
            }
        }
    }

    /**
     *首页搜索默认城市
     */
    public function default_city(){
        if(IS_POST){
            $type = I('type');
            if(!in_array($type,array('1','2')))     error("城市类型错误");
            $default_city = M('Area')->field('type,city,country,code')
                ->where(['type'=>$type,'is_default'=>'2'])->select();
            success($default_city);
        }
    }

    /**
     *@按经纬度获取地址
     */
    public function get_address(){
        $position['lng'] = I('lng');//经度
        $position['lat'] = I('lat');//纬度
        if(!empty($position)){
            session('position',$position);
            $ak = 'fbINeDE9oaw2SXIYcfdpe0Td';
            $api = "http://api.map.baidu.com/geocoder/v2/?ak={$ak}&location={$position['lat']},{$position['lng']}&output=json";
            $position = file_get_contents($api);
            $position = json_decode($position, true);
            session('addressComponent',$position['result']['addressComponent']);
            success($position['result']['addressComponent']);
        }else{
            error("获取定位失败");
        }
    }


    /**
     *资讯列表
     */
    public function article(){
        if(IS_POST) {
            !empty($_POST['title'])         && $map['title'] = ['like','%'.$_POST['title'].'%'];
            //$map['status'] = 2;
            $p = I('p');
            empty($p) && $p = 1;
            $num = 10;
            $count = M('Article')->where($map)->count();
            $page = ceil($count / $num);
            $data = M('Article')->field('id,title,browse,uptime,author,img,author,intime')
                ->where($map)->limit(($p - 1) * $num, $p * $num)
                ->order("status desc,intime desc")->select();
            if(!empty($data)){
                foreach($data as $k=>$v){
                    $data[$k]['date_value'] = translate_date($v['intime']);
                }
            }
            success(['page'=>$page,'data'=>$data]);
        }
    }

    /**
     *资讯详情
     */
    public function article_view(){
            $id = I('id');
            M('Article')->where(['id'=>$id])->setInc('browse');
            $article = M('Article')->where(['id'=>$id])->find();
            if(!$article)       error("参数错误");
            $article['intime'] = date("Y-m-d H:i",strtotime($article['intime']));
            success($article);

    }

    /**
     *@轮播新闻
     */
    public function news(){
        if(IS_POST){
            $id = I('id');
            $news = M('Notice')->where(['id'=>$id])->find();
            success($news);
        }
    }

    /**
     *@diy跳转地址
     */
    public function diy_url(){
        if (IS_POST) {
            $a = "http://www.kkmove.com/?";
            $map = $_POST;
            if (empty($map)) error('参数不能为空');

            $goods_info = M('Goods')->field('template_id,name')->where(['goods_id'=>$map['goods_id']])->find();
            if(empty($map['kinds_id'])){
                $map['cate'] = $goods_info['template_id'];
                $title = $goods_info['name'];
            }else{
                $kinds = explode(',',$map['kinds_id']);
                if(count($kinds) == 1){
                    $map['kinds_id'] = $map['kinds_id'].',';
                }
                foreach($kinds as $k=>$v){
                    $where  = M('GoodsKinds')->where(['kind_id'=>$v])->find();
                    if($k>0){
                        $title += $where['kinds_detail']."+";
                    }else{
                        $title = $where['kinds_detail'];
                    }
                }
                $code = M('GoodsStock')->where(['goods_id'=>$map['goods_id'],'kinds'=>$map['kinds_id']])->find();
                $map['cate'] = $code['template_id'];
            }
            $map['shop'] = '1893';
            $data = http_build_query($map);
            $map['ext_param'] = urlencode($data);
            $url = $a . $map['cate'] . '&shop=' . $map['shop'] . '&ext_param=' . $map['ext_param'].'&title='.urlencode($title);
            success($url);
        }
    }



    /**
     *@首页搜索
     */
    public function home_search(){
        $type = I('type');
        $key_word = I('key_word');
        $p = I('p');
        empty($p)   &&  $p=1;
        $num = 10;
        empty($type)    &&  $type =1;
        switch($type){
            case 1 :
                $map['name'] = ['like','%'.$key_word.'%'];
                $map['is_del'] = 1;
                $map['status'] = 2;
                $map['type'] = 1;
                $count = M('Goods')->where($map)->count();
                $list = M('Goods')->field('goods_id,name,sale_price,price,thumb,number,sale_number')
                    ->where($map)->limit(($p-1)*$num,$num)->select();
            break;
            case 2:
                $map['b.name'] = ['like','%'.$key_word.'%'];
                $map['a.is_del'] = 1;
                $map['a.status'] = 2;
                $count = M('GoodsTailor')->alias('a')
                    ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->where($map)->count();
                $list = M('GoodsTailor')->alias('a')
                    ->field("a.tailor_id as goods_id,b.name,b.price,b.thumb,a.presale_price as sale_price,b.number,a.pay_count,b.sale_number")
                    ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->where($map)->limit(($p-1)*$num,$num)->select();
                break;
            case 3:
                $map['b.nickname'] = ['like','%'.$key_word.'%'];
                $list = M('Live')->alias('a')
                    ->field('a.live_id,a.play_img,a.title,a.play_address_m3u8,a.live_status,a.room_id,a.nums,a.watch_nums,a.qrcode_path,
                b.nickname,b.img,b.hx_username,b.member_id')
                    ->join('INNER JOIN __MEMBER__ b on a.user_id = b.member_id')
                    ->where($map)->limit(($p - 1) * $num, $num)
                    ->order("a.intime desc")->group("a.user_id")
                    ->select();
                if ($list) {
                    foreach ($list as $k => $v) {
                        $list[$k]['play_img'] = C('IMG_PREFIX') . $v['play_img'];
                        $list[$k]['img'] = C('IMG_PREFIX') . $v['img'];
                        //$list[$k]['url'] = C('IMG_PREFIX')."/App/Index/share_live/live_id/" . base64_encode($v['live_id']);
                        //$list[$k]['qrcode_path'] = C('IMG_PREFIX').$v['qrcode_path'];
                    }
                } else {
                    $list = [];
                }
            break;
            case 4:
                $map['a.is_del'] = 1;
                $map['a.title'] = ['like','%'.$key_word.'%'];
                $count = M('TopicalVideo')->alias('a')
                    ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                    ->where($map)->count();
                $list = M('TopicalVideo')->alias('a')
                    ->field("a.id,a.video,a.img,a.title,a.zan,a.browse,a.intime,b.nickname,b.img as head_img,b.member_id")
                    ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                    ->where($map)->limit(($p-1)*$num,$num)->order("a.intime desc")->select();
            break;

        }
        $page = ceil($count/$num);
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@大转盘商品
     */
    public function prize(){
        $uid = I('uid');
        $map['is_del'] = 1;
        $list = M('Prize')->field('prize_id,name,img')
            ->where($map)->order("intime desc")->select();
        if(!empty($uid)){
            $member = M('Member')->where(['member_id'=>$uid])->find();
            $amount = (int)($member['score'] + $member['amount']);
        }else{
            $amount = '';
        }
        success(['amount'=>$amount,'list'=>$list]);
    }



    /**
     *@大转盘抽奖
     */
    public function prize_draw(){
        function get_rand($proArr) {
            $result = '';
            $proSum = array_sum($proArr);//概率数组的总概率精度
            foreach ($proArr as $key => $val) {
                $randNum = mt_rand(1, $proSum); //抽取随机数
                if ($randNum <= $val) {
                    $result = $key; //得出结果
                    break;
                } else {
                    $proSum -= $val;
                }
            }
            unset ($proArr);
            return $result;
        }
        if(IS_POST){
            $member = checklogin();
            $time = date("Y-m-d",time());
            $count = M('PrizeRecord')->where(['mid'=>$member['member_id'],'intime'=>['gt',$time]])->count();
            if($count>2){
                error("抽奖次数已满");
            }
            if($member['score'] > 10){
                M('Member')->where(['member_id'=>$member['member_id']])->setDec('score',10);
            }else{
                error("积分不足,不能抽奖");

            }
            $record['state'] = 2;
            $map['is_del'] = 1;
            $list = M('Prize')->where($map)->order("intime desc")->select();
            foreach ($list as $k=>$v) {
                $arr[$v['prize_id']] = $v['chance']; //中奖基数集合
            }
            $prize_id = get_rand($arr); //根据概率获取奖项id
            foreach($list as $k=>$v){ //获取前端奖项位置
                if($v['prize_id'] == $prize_id){
                    $prize_site = $k;
                    break;
                }
            }
            $res = $list[$prize_site]; //中奖项
            $data['name'] = $res['name'];
            $data['type'] = $res['type'];
            $member = M('Member')->where(['member_id'=>$member['member_id']])->find();
            if($res['type'] == 2){
                $score = $member['score'] + $res['value'];
                $member['score'] = $score;
                M('Member')->where(['member_id'=>$member['member_id']])->save(['score'=>$score]);
                $record['is_used'] = 2;
            }
            $data['site'] = $prize_site;//前端奖项从0开始
            $record['mid'] = $member['member_id'];
            $record['prize_id'] = $prize_id;
            $record['type'] = $res['type'];
            $record['intime'] = date("Y-m-d H:i:s");
            $result = M('PrizeRecord')->add($record);
            if($result){
                success(['data'=>$data]);
            }else{
                error("抽奖失败");
            }
        }
    }

    /**
     *@判断是否超过3次
     */
    public function check_draw_time(){
        $member = checklogin();
        $time = date("Y-m-d",time());
        $count = M('PrizeRecord')->where(['mid'=>$member['member_id'],'intime'=>['gt',$time]])->count();
        $count = 3 - $count;
        success($count);
    }

    /**
     *@中奖记录
     */
    public function draw_record(){
        $p = I('p');
        empty($p)      &&      $p = 1;
        $num = 6;
        $map['a.type'] = ['in',['1','2']];
        $count = M('PrizeRecord')->alias('a')
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->join("LEFT JOIN __MEMBER__ c on a.mid = c.member_id")
            ->where($map)
            ->count();
        $page = ceil($count/$num);
        $list = M('PrizeRecord')->alias('a')
            ->field("a.intime,b.name,c.phone")
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->join("LEFT JOIN __MEMBER__ c on a.mid = c.member_id")
            ->where($map)->limit(($p - 1)*$num,$num)
            ->order("a.intime desc")
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['phone'] = hideStr($v['phone'], $bengin = 3, $len = 4);
            $list[$k]['intime'] = date("Y/m/d",strtotime($v['intime']));
        }
        success(['count'=>$count,'page'=>$page,'list'=>$list]);
    }

    /**
     *@客服
     */
    public function kefu(){
        $user = checklogin();
        $kefu = M('User')->field('user_id,username,sex,img,autograph,ID,hx_username,hx_password,alias')
            ->where(['type'=>3])->find();
        $kefu['img'] = $this->url.$kefu['img'];
        success($kefu);
    }


}