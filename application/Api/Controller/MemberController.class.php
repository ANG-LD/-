<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/20
 * Time: 10:40
 */

namespace Api\Controller;
use Think\Controller;
use Think\Upload;
class MemberController extends CommonController
{

    public function index()
    {
        $member = checklogin();
        $member['count1'] = M('MallOrder')->where(['mid'=>$member['user_id'],'state'=>'1','is_del'=>'1'])->count();
        $member['count2'] = M('MallOrder')->where(['mid'=>$member['user_id'],'state'=>['in',['2','3']],'is_del'=>'1'])->count();
        $member['count3'] = M('MallOrder')->where(['mid'=>$member['user_id'],'state'=>'4','is_del'=>'1'])->count();
        $member['count4'] = M('MallOrder')->where(['mid'=>$member['user_id'],'state'=>['in',['5','7']],'is_del'=>'1'])->count();
        $member['follow_count'] = M('Follow')->where(['user_id'=>$member['user_id']])->count();
        $member['fans_count'] = M('Follow')->where(['user_id2'=>$member['user_id']])->count();
        $member['video_count'] = M('Collection')->alias('a')
                               ->join("INNER JOIN __VIDEO__ b on a.goods_id = b.video_id")
                               ->where(['a.user_id'=>$member['user_id'],'b.is_del'=>'1','a.type'=>'2'])
                               ->count();
        $member['score'] = $member['score'];
        $member['img'] = $this->url.$member['img'];
        if($member['type'] == 2){
            $sum = M('UserMark')->where(['user_id2'=>$member['user_id']])->sum('mark');
            $count = M('UserMark')->where(['user_id2'=>$member['user_id']])->count();
            $member['mark'] = sprintf("%.1f",(5 + $sum)/($count+1));
            $top = M('GiveGift')->alias('a')
                ->field("b.img,b.username,b.hx_username,b.hx_password,a.user_id,sum(a.jewel) as jewel")
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                ->where(['a.user_id2'=>$member['user_id']])
                ->order("jewel desc")->limit(3)
                ->group("a.user_id")->select();
        }
        if($top){
            foreach($top as $k=>$v){
                $top[$k]['img'] = $this->url.$v['img'];
            }
            $member['top'] = $top;
        }else{
            $member['top'] = [];
        }
        success($member);
    }

    /**
     *@视频收藏列表
     */
    public function collect_video(){
        $member = checklogin();
        $map['b.is_del'] = '1';
        $map['a.type'] = '2';
        $map['a.user_id'] = $member['user_id'];
        $p = I('p');
        $count = M('Collection')->alias('a')
            ->join("INNER JOIN __VIDEO__ b on a.goods_id = b.video_id")
            ->where($map)->count();
        empty($p)   &&  $p = 1;
        $num = I('pagesize');
        $num    ?   $num    :   $num = 10;
        $page = ceil($count/$num);
        $list = M('Collection')->alias('a')
            ->field("a.collection_id,b.video_id,b.title,b.video_img,b.url,b.comments,b.watch_nums,c.user_id,c.username")
            ->join("INNER JOIN __VIDEO__ b on a.goods_id = b.video_id")
            ->join("LEFT JOIN __USER__ c on b.user_id = c.user_id")
            ->where($map)->limit(($p-1)*$num,$num)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['url'] = $this->url.$v['url'];
            $list[$k]['video_img'] = $this->url.$v['video_img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@我的关注
     */
    public function my_follow(){
        $member = checklogin();
        $user_id = I('user_id');
        $user_id  ?  $user_id   : $user_id = $member['user_id'];
        $p = I('p');
        empty($p)   &&  $p = 1;
        $num = 10;
        $count = M('Follow')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->where(['a.user_id'=>$user_id])->count();
        $page = ceil($count/$num);
        $list = M('Follow')->alias('a')
            ->field("b.user_id,b.img,b.username,b.sex,b.autograph,b.type,b.mark")
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->limit(($p-1)*$num,$num)
            ->where(['a.user_id'=>$user_id])->select();
        foreach($list as $k=>$v){
            if($v['type'] == 2){
                $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
                $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
                $list[$k]['mark'] = sprintf("%.1f",($v['mark'] + $sum)/($count+1));
            }else{
                $list[$k]['mark'] = "";
            }
            //$check = M('Follow')->where(['user_id'=>$v['user_id'],'user_id2'=>$member['user_id']])->find(); //判断对方是否关注ni
            $list[$k]['is_follow'] = '1';
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }
    /**
     * @我的粉丝
     */
    public function my_fans(){
        $member = checklogin();
        $user_id = I('user_id');
        $user_id  ?  $user_id   : $user_id = $member['user_id'];
        $p = I('p');
        empty($p)   &&  $p = 1;
        $num = 10;
        $count = M('Follow')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.user_id2'=>$user_id])->count();
        $page = ceil($count/$num);
        $list = M('Follow')->alias('a')
            ->field("b.user_id,b.img,b.username,b.sex,b.autograph,b.type,b.mark")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->limit(($p-1)*$num,$num)
            ->where(['a.user_id2'=>$user_id])->select();
        foreach($list as $k=>$v){
            if($v['type'] == 2){
                $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
                $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
                $list[$k]['mark'] = sprintf("%.1f",($v['mark'] + $sum)/($count+1));
            }else{
                $list[$k]['mark'] = "";
            }
            $check = M('Follow')->where(['user_id'=>$member['user_id'],'user_id2'=>$v['user_id']])->find();
            if($check){
                $list[$k]['is_follow'] = '1';
            }else{
                $list[$k]['is_follow'] = '2';
            }
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@他人关注列表
     */
    public function user_follow(){
        $member = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $user_id = I('user_id');
        if(empty($user_id))         error("参数错误");
        empty($p)   &&  $p = 1;
        $pageSize   ?   $pageSize   :   $pageSize = 10;
        $count = M('Follow')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->where(['a.user_id'=>$user_id])->count();
        $page = ceil($count/$pageSize);
        $list = M('Follow')->alias('a')
            ->field("b.user_id,b.img,b.username,b.sex,b.autograph,b.type,b.mark")
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->where(['a.user_id'=>$user_id])->select();
        foreach($list as $k=>$v){
            if($v['type'] == 2){
                $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
                $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
                $list[$k]['mark'] = sprintf("%.1f",($v['mark'] + $sum)/($count+1));
            }else{
                $list[$k]['mark'] = "";
            }
            //$check = M('Follow')->where(['user_id'=>$v['user_id'],'user_id2'=>$member['user_id']])->find(); //判断对方是否关注ni
            $list[$k]['is_follow'] = '1';
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     * @我的粉丝
     */
    public function user_fans(){
        $member = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $user_id = I('user_id');
        if(empty($user_id))         error("参数错误");
        empty($p)   &&  $p = 1;
        $pageSize   ?   $pageSize   :   $pageSize = 10;
        $count = M('Follow')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.user_id2'=>$user_id])->count();
        $page = ceil($count/$pageSize);
        $list = M('Follow')->alias('a')
            ->field("b.user_id,b.img,b.username,b.sex,b.autograph,b.type,b.mark")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->where(['a.user_id2'=>$user_id])->select();
        foreach($list as $k=>$v){
            if($v['type'] == 2){
                $sum = M('UserMark')->where(['user_id2'=>$v['user_id']])->sum('mark');
                $count = M('UserMark')->where(['user_id2'=>$v['user_id']])->count();
                $list[$k]['mark'] = sprintf("%.1f",($v['mark'] + $sum)/($count+1));
            }else{
                $list[$k]['mark'] = "";
            }
            $check = M('Follow')->where(['user_id'=>$member['user_id'],'user_id2'=>$v['user_id']])->find();
            if($check){
                $list[$k]['is_follow'] = '1';
            }else{
                $list[$k]['is_follow'] = '2';
            }
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@关注和取消关注
     */
    public function follow_user(){
        if(IS_POST){
            $member = checklogin();
            $data['user_id'] = $member['user_id'];
            $data['user_id2'] = I('user_id2');
            if(empty($data['user_id2']))        error("被关注者不能为空");
            if($data['user_id'] == $data['user_id2'])   error("参数错误");
            $check = M('Follow')->where($data)->find();
            if($check){
                $result = M('Follow')->where(['follow_id'=>$check['follow_id']])->delete();
                $code = 2;
            }else{
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('Follow')->add($data);
                $code = 1;
            }
            if($result){
                success($code);
            }else{
                error("操作失败");
            }
        }
    }

    //@上传头像
    public function upload()
    {
        $config = [
            'maxSize' => 3145728,
            'rootPath' => './',
            'savePath' => '/Uploads/image/' . 'user' . '/' . $_REQUEST[''],
            'saveName' => ['uniqid', ''],
            'exts' => ['jpg', 'gif', 'png', 'jpeg'],
            'autoSub' => true,
            'subName' => ['date', 'Ymd'],
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info) {
            $url = $info['imgFile']['savepath'] . $info['imgFile']['savename'];
            $result = M('Member')->where(['member_id' => I('uid')])->save(['img' => $url]);
            if ($result) {
                success($url);
            } else {
                error("上传失败");
            }
        } else {
            error($uploader->getError());
        }
    }

    /**
     *@修改名称
     */
    public function change_user()
    {
        if (IS_POST) {
            $member = checklogin();
            $config = [
                'maxSize' => 3145728,
                'rootPath' => './',
                'savePath' => '/Uploads/image/' . 'user' . '/' . $_REQUEST[''],
                'saveName' => ['uniqid', ''],
                'exts' => ['jpg', 'gif', 'png', 'jpeg'],
                'autoSub' => true,
                'subName' => ['date', 'Ymd'],
            ];
            $uploader = new Upload($config);
            $info = $uploader->upload();
            if ($info) {
                foreach($info as $k=>$v){
                    $url[] = $v['savepath'].$v['savename'];
                    $array = getimagesize('.' . $v['savepath'].$v['savename']);
                    if ($array[0] > 500) {
                        $image = new \Think\Image();
                        $image->open('.' . $v['savepath'].$v['savename']);
                        $image->thumb(500, 500, \Think\Image::IMAGE_THUMB_SCALE)
                            ->save('./Uploads/image/comment/thumb/' . time() . '_' . $v["savename"]);
                        $thumb = '/Uploads/image/comment/thumb/' . time() . '_' . $v["savename"];
                    } else {
                        $thumb = $v['savepath'].$v['savename'];
                    }
                    $arr[] = $thumb;
                }
                $data['img'] = $thumb;
            }

            $username = I('username');

            if(!empty($username))                   $data['username'] = $username;
            $phone = I('phone');
            if(!empty($phone)){
                $data['phone'] = $phone;
                if(!preg_match('/^1[3|4|5|7|8]\d{9}$/', $data['phone']))               error("手机号错误");
                if($data['phone'] != $member['phone']){
                    if(M('User')->where(['phone'=>$data['phone']])->find())            error("该手机号已注册");
                }
            }
            $data['sex'] = I('sex');
            $data['autograph'] = I('autograph');
            $data['uptime'] = date("Y-m-d H:i:s", time());
            $result = M('User')->where(['user_id' => $member['user_id']])->save($data);
            if ($result) {
                success("修改成功");
            } else {
                error("修改失败");
            }
        }
    }

    /**
     *@学习档案
     */
    public function learn_list(){
        $user = checklogin();
        $map['a.user_id'] = $user['user_id'];
        $map['b.is_del'] = 1;
        $map['b.type'] = 2;
        $map['a.state'] = 2;
        $p = I('p');
        $pageSize = I('pagesize');
        $p      ?   $p = $p  :   $p = 1;
        $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
        $count = M('TeachOrder')->alias('a')
            ->join("INNER JOIN __USER__ b on a.user_id2 = b.user_id")
            ->group("a.user_id2")
            ->where($map)->count();
        $page = ceil($count/$pageSize);
        $list = M('TeachOrder')->alias('a')
            ->field("b.user_id,b.username,b.sex,a.intime,b.img,b.hx_username,b.hx_password")
            ->join("INNER JOIN __USER__ b on a.user_id2 = b.user_id")
            ->where($map)->limit(($p-1)*$pageSize,$pageSize)
            ->group("a.user_id2")
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['img'] = $this->url.$v['img'];
            $list[$k]['date_value'] = translate_date($v['intime']);
        }
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@充值列表
     */
    public function price_list(){
        $user = checklogin();
        $list = M('PriceList')->order("price asc")->select();
        if (!$list){$list=[];}
        success(['money'=>$user['money'],'list'=>$list]);
    }

    /**
     *@参与的名师现场
     */
    public function tutor_class_list(){
        $user = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $p  ?   $p     :   $p = 1;
        $pageSize  ?   $pageSize      :   $pageSize = 10;
        $map['a.user_id'] = $user['user_id'];
        $map['b.is_del'] = 1;
        $count = M('TutorClassSign')->alias('a')
            ->join("LEFT JOIN __TUTOR_CLASS__ b on a.tutor_class_id = b.id")
            ->where($map)
            ->count();
        $page = ceil($count/$pageSize);
        $list = M('TutorClassSign')->alias('a')
            ->field('a.number,a.amount,b.id,b.name,b.img,b.intro,b.value,b.limit_value,b.province,b.city,b.address')
            ->join("LEFT JOIN __TUTOR_CLASS__ b on a.tutor_class_id = b.id")
            ->where($map)->order("a.id desc")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['img'] = $this->url.$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);

    }

    /**
     *@会员升级
     */
    public function user_upgrade_record(){
        $user = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $p  ?   $p     :   $p = 1;
        $pageSize  ?   $pageSize      :   $pageSize = 10;
        $map['a.user_id'] = $user['user_id'];
        $count = M('UpgradeRecord')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->group("a.user_id2")
            ->where($map)->count();
        $page = ceil($count/$pageSize);
        $list = M('UpgradeRecord')->alias('a')
            ->field('a.user_id2,b.username,b.ID')
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->order("a.id desc")
            ->group("a.user_id2")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->where($map)
            ->select();
        $date = date("Y-m-d",time());
        foreach($list as $k=>$v){
            $code = M('UpgradeRecord')->field('state,date_value')->where(['user_id2'=>$v['user_id2']])->order("id desc")->limit(1)->find();
            $list[$k]['state'] = $code['state'];
            $list[$k]['date_value'] = $code['date_value'];
            if($code['date_value']<$date){
                $check = M('TutorLiveClass')->where(['user_id'=>$v['user_id2']])->limit(1)->order("id desc")->find();
                if(strtotime($check['end_time']<time())){
                    unset($list[$k]);
                }else{
                    $list[$k]['is_xu'] = '1';
                }
            }else{
                $list[$k]['is_xu'] = '2';
            }
        }
        $list = array_values($list);
        success(['page'=>$page,'list'=>$list]);

    }

    /**
     *@推荐人
     */
    public function referee(){
        $user = checklogin();
        if(IS_POST){
            $phone = I('phone');
            $check = M('User')->where(['phone'=>$phone])->find();
            if(!$check)         error("手机号错误");
            $re = M('Recommend')->where(['user_id2'=>$user['user_id']])->find();
            if($re)             error("已绑定推荐人，不支持更换");
            $data['user_id'] = $check['user_id'];
            $data['user_id2'] = $user['user_id'];
            $data['intime'] = time();
            $result = M('Recommend')->add($data);
            if($result){
                success("绑定成功");
            }else{
                error("绑定失败");
            }
        }else{
            $re = M('Recommend')->where(['user_id2'=>$user['user_id']])->find();
            if($re){
                $phone = M('User')->where(['user_id'=>$re['user_id']])->getField('phone');
            }else{
                $phone = '';
            }
            success($phone);
        }
    }

    public function show_referee(){
        $user = checklogin();
        $re = M('Recommend')->where(['user_id2'=>$user['user_id']])->limit(1)->order('intime desc')->find();
        if($re){
            $phone = M('User')->where(['user_id'=>$re['user_id']])->getField('phone');
        }else{
            $phone = '';
        }
        success($phone);
    }


    /**
     *@修改手机号
     */
    public function change_phone()
    {
        if (IS_POST) {
            checklogin();
            $old_phone = I('old_phone');
            $uid = I('uid');
            $member = M('Member')->where(['member_id' => $uid, 'phone' => $old_phone])->find();
            if (!$member) error("旧手机号错误");
            $verify1 = I('verify1');
            if (empty($verify1)) error("旧手机号验证码不能为空");
            $check1 = M('Code')->where(['mobile' => $old_phone])->find();
            if (!$check1) error("旧手机号验证码未发送");
            if ($verify1 != $check1['verify']) error("旧手机号验证码错误");
            if (time() - $check1['addtime'] > 60 * 20) {
                error("旧手机号验证码已过期");
            }
            $new_phone = I('new_phone');
            if ($old_phone == $new_phone) error("新手机号不能和旧手机号相同");
            if (!preg_match('/^1[3|4|5|7|8]\d{9}$/', $new_phone)) error("手机号错误");
            if (M('Member')->where(['phone' => $new_phone])->find()) error("该手机号已注册");
            $verify2 = I('verify2');
            if (empty($verify2)) error("新手机号验证码不能为空");
            $check2 = M('Code')->where(['mobile' => $new_phone])->find();
            if (!$check2) error("新手机号验证码未发送");
            if ($verify2 != $check2['verify']) error("新手机号验证码错误");
            if (time() - $check2['addtime'] > 60 * 20) {
                error("新手机号验证码已过期");
            }
            $result = M('Member')->where(['member_id' => $uid])->save(['phone' => $new_phone]);
            if ($result) {
                success("修改成功");
            } else {
                error("修改失败");
            }
        }
    }

    /**
     *邮箱信息
     */
    public function email()
    {
        if (IS_POST) {
            checklogin();
            $uid = I('uid');
            $email = M('Member')->where(['member_id' => $uid])->getField('email');
            if (!empty($email)) {
                success($email);
            } else {
                success("未绑定邮箱");
            }
        }
    }

    /**
     *@绑定或修改邮箱
     */
    public function edit_email()
    {
        if (IS_POST) {
            $member = checklogin();
            $uid = I('uid');
            $email = I('email');
            $verify = I('verify');
            empty($email) && error('邮箱为空');
            empty($verify) && error('邮箱验证码为空');
            if (!is_email($email)) error("邮箱格式错误"); else true;
            if ($member['email'] == $email) error("请换成新邮箱");
            if (M('Member')->where(['email' => $email])->find()) error("该邮箱已存在");
            $email_info = session("email_info");
            if ($email_info['email'] != $email) error("邮箱验证码和邮箱不匹配");
            if (time() - $email_info['time'] > 20 * 60) error("邮箱验证码已过期");
            if ($verify != $email_info['verify']) error("邮箱验证码错误");
            $result = M('Member')->where(['member_id' => $uid])->save(['email' => $email]);
            if ($result) {
                session('email_info', null);
                success("修改成功");
            } else {
                error("修改失败");
            }
        }
    }

    /**
     *@修改密码
     */
    public function set_new_password()
    {
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            if (empty($data['old_password']))   error("旧密码不能为空");
            if (empty($data['new_password']))   error("新密码不能为空");
            if (empty($data['repassword']))     error("确认新密码不能为空");
            if (empty($data['verify']))         error("验证码不能为空");
            if($data['verify'] != $this->system['default_verify']){
                $check = M('Code')->where(['mobile'=>$member['phone']])->find();
                if(!$check)                     error("验证码未发送");
                if($data['verify']!= $check['verify']) error("验证码错误");
                if(time()-strtotime($check['intime']) > 60*20){
                    error("验证码已过期");
                }
            }
            if (!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/', $data['new_password'])) error("请输入字母和数字组合的6-20位密码!");

            if ($data['new_password'] != $data['repassword']) error("两次新密码不一样");
            if ($data['old_password'] == $data['new_password']) error("新密码不能和旧密码相同");
            if ($member['password'] != encrypt($data['old_password'])) error("旧密码错误");
            $data['password'] = encrypt($data['new_password']);
            $result = M('Member')->where(['member_id' => $data['uid']])->save(['password' => $data['password']]);
            if ($result) {
                success("修改密码成功");
            } else {
                error("修改密码失败");
            }
        }

    }

    /**
     *@绑定提现的银行卡或支付宝
     */
    public function set_default_withdraw_card(){
        if(IS_POST){
            $member = checklogin();
            $data['pay_type'] = I('pay_type');
            $data['bank_card'] = I('bank_card');
            $data['realname'] = I('realname');
            $data['verify'] = I('verify');
            if(empty($data['pay_type']))       $data['pay_type'] = 1;
            if($data['pay_type'] == 1){
                if(empty($data['bank_card']))   error("支付宝账号不能为空");
                if(empty($data['realname']))    error("真实姓名不能为空");
            }else if($data['pay_type'] == 2){
                if(empty($data['bank_name']))   error("银行名称不能为空");
                if(empty($data['bank_card']))   error("银行卡号不能为空");
                if(empty($data['realname']))    error("持卡人姓名不能为空");
                if(empty($data['message']))     error("开户信息不能为空");
                if(!preg_match('/^(\d{16}|\d{19})$/',$data['bank_card'])) error("银行卡号填写有误");
            }
            if(empty($data['verify']))          error("验证码不能为空");
            if($data['verify'] != $this->system['default_verify']){
                $check = M('Code')->where(['mobile'=>$member['phone']])->find();
                if(!$check)                     error("验证码未发送");
                if($data['verify']!= $check['verify']) error("验证码错误");
                if(time()-strtotime($check['intime']) > 60*20){
                    error("验证码已过期");
                }
            }
            $data['type'] = 1;
            $data['member_id'] = $member['user_id'];
            $data['intime'] = date("Y-m-d H:i:s",time());
            $check = M('BankCard')->where(['member_id'=>$member['user_id'],'type'=>'1'])->select();
            if($check){
                M('BankCard')->where(['member_id'=>$member['user_id'],'type'=>'1'])->save(['is_default'=>1]);
            }
            $data['is_default'] = 2;
            $result = M('BankCard')->add($data);
            if($result){
                success("操作成功");
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@银行卡
     */
    public function bank(){
        if(IS_POST){
            $member = checklogin();
            $bank = M('Bank')->field('bank_id,name,img,bg_color')->where(['is_del'=>'1'])->select();
            success($bank);
        }
    }

    /**
     *@添加银行卡
     */
    public function add_bank_card(){
        if(IS_POST){
            $member = checklogin();
            $data = $_POST;
            if(empty($data['bank_id']))     error("请选择银行卡");
            $bank = M('Bank')->where(['bank_id'=>$data['bank_id']])->find();
            if(!$bank)                      error("参数错误");
            if(empty($data['realname']))    error("持卡人不能为空");
            if(empty($data['bank_card']))   error("银行卡号不能为空");
            $check = M('BankCard')->where(['bank_card'=>$data['bank_card'],'type'=>'2'])->find();
            if($check)                      error("该卡号已绑定");
            if(empty($data['message']))     error("开户信息不能为空");
            if(empty($data['phone']))       error("预留手机号不能为空");
            if(empty($data['verify']))      error("验证码不能为空");
            if(!preg_match('/^1[3|4|5|7|8]\d{9}$/',$data['phone'])) error("手机号填写有误");
            if(!preg_match('/^(\d{16}|\d{19})$/',$data['bank_card'])) error("银行卡号填写有误");
            if($data['verify'] != $this->system['default_verify']){
                $check = M('Code')->where(['mobile'=>$data['phone']])->find();
                if(!$check)                     error("验证码未发送");
                if($data['verify']!= $check['verify']) error("验证码错误");
                if(time()-strtotime($check['intime']) > 60*20){
                    error("验证码已过期");
                }
            }
            $data['type'] = 2;
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['bank_name'] = $bank['name'];
            $data['member_id'] = $member['member_id'];
            if(!M('BankCard')->where(['member_id'=>$member['member_id'],'type'=>'2'])->select()){
                $data['is_default'] = '2';
            }
            $result = M('BankCard')->add($data);
            if($result){
                success("添加成功");
            }else{
                error("添加失败");
            }
        }
    }

    /**
     *@我的银行卡列表
     */
    public function bank_card(){
        if(IS_POST){
            $member = checklogin();
            $list = M('BankCard')->alias('a')
                ->field('a.id,a.bank_name,a.bank_card,a.realname,a.is_default,a.message,a.phone,b.img,b.bg_color')
                ->join("INNER JOIN __BANK__ b on a.bank_id = b.bank_id")
                ->where(['a.member_id'=>$member['member_id'],'type'=>'2'])
                ->select();
            foreach($list as $k=>$v){
                $list[$k]['bank_card'] = hideStr($v['bank_card'],0,strlen($v['bank_card'])-4);
            }
            success($list);
        }
    }

    /**
     *@银行卡默认操作
     */
    public function set_default_bank(){
        if(IS_POST){
            $member = checklogin();
            $id = I('id');
            if(empty($id))              error("参数错误");
            $result = M('BankCard')->where(['id'=>$id])->save(['is_default'=>'2']);
            if($result){
                M('BankCard')->where(['member_id'=>$member['member_id'],'id'=>['neq',$id]])->save(['is_default'=>'1']);
                success("操作成功");
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@编辑银行卡
     */
    public function edit_bank(){
        $member = checklogin();
        if(IS_POST){
            $data = $_POST;
            if(empty($data['id']))          error("参数错误");
            if(!M('BankCard')->where(['id'=>$data['id'],'member_id'=>$member['member_id']])->find()){
                error("参数错误");
            };
            if(empty($data['bank_id']))     error("请选择银行卡");
            $bank = M('Bank')->where(['bank_id'=>$data['bank_id']])->find();
            if(!$bank)                      error("参数错误");
            if(empty($data['realname']))    error("持卡人不能为空");
            if(empty($data['bank_card']))   error("银行卡号不能为空");
            if($bank['bank_card'] != $data['bank_card']){
                $check = M('BankCard')->where(['bank_card'=>$data['bank_card'],'type'=>'2'])->find();
                if($check)                      error("该卡号已绑定");
            }
            if(empty($data['message']))     error("开户信息不能为空");
            if(empty($data['phone']))       error("预留手机号不能为空");
            if(empty($data['verify']))      error("验证码不能为空");
            if(!preg_match('/^1[3|4|5|7|8]\d{9}$/',$data['phone'])) error("手机号填写有误");
            if(!preg_match('/^(\d{16}|\d{19})$/',$data['bank_card'])) error("银行卡号填写有误");
            $check = M('Code')->where(['mobile'=>$data['phone']])->find();
            if(!$check)                     error("验证码未发送");
            if($data['verify']!= $check['verify']) error("验证码错误");
            if(time()-strtotime($check['intime']) > 60*20){
                error("验证码已过期");
            }
            $data['bank_name'] = $bank['name'];
            $result = M('BankCard')->save($data);
            if($result){
                success("编辑成功");
            }else{
                error("编辑失败");
            }
        }else{
            $id = I('id');
            if(empty($id))      error("参数错误");
            $re = M('BankCard')->alias('a')
                ->field('a.id,a.realname,a.bank_card,a.message,a.bank_name,b.bank_id')
                ->join("LEFT JOIN __BANK__ b on a.bank_id = b.bank_id")
                ->where(['a.id'=>$id,'a.member_id'=>$member['member_id']])
                ->find();
            success($re);
        }
    }

    /**
     *@删除银行卡
     */
    public function del_bank(){
        if(IS_POST){
            $member = checklogin();
            $id = I('id');
            $map['id'] = $id;
            $map['member_id'] = $member['member_id'];
            $result = M('BankCard')->where($map)->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除银行卡成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除银行卡失败'));
            }
        }
    }



    /**
     *@判断是否实名认证
     */
    public function is_auth_card(){
        if(IS_POST){
            $member = checklogin();
            if(empty($member['realname']) || empty($member['id_card'])){
                error("请先实名认证");
            }else{
                success("认证成功");
            }
        }
    }

    /**
     *@实名认证
     */
    public function auth_card()
    {
        if (IS_POST) {
            $member = checklogin();
            $uid = I('uid');
            $data['realname'] = I('realname');
            $data['id_card'] = I('card');
            if (empty($data['realname'])) error("真实姓名不能为空");
            if (empty($data['id_card'])) error("身份证信息不能为空");
            if (!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/', $data['id_card'])) error("请输入正确的身份证号");
            if ($data['id_card'] == $member['id_card'] && $data['realname'] == $member['realname']) error("认证信息未改变");
            $result = M('Member')->where(['member_id' => $uid])->save($data);
            if ($result) {
                success("实名认证成功");
            } else {
                error("实名认证失败");
            }
        } else {

        }
    }

    /**
     *@我的钱包
     */
    public function my_wallet()
    {
        if (IS_POST) {
            checklogin();
            $uid = I('uid');
            $member = M('Member')->field('member_id,nickname,realname,img,amount')
                ->where(['member_id' => $uid])->find();
            $bank_list = M('BankCard')->field('id,bank_name,bank_card,realname,card')->where(['member_id' => $uid])->select();
            success(['member' => $member, 'bank' => $bank_list]);
        }
    }

    /**
     *@银行卡协议
     */
    public function bank_xieyi(){
        $xieyi = M('Notice')->field('content')->where(['id'=>8])->find();
        success($xieyi);
    }


    /**
     *@设置支付密码
     */
    public function set_pay_password(){
        if(IS_POST){
            $member = checklogin();
//            $phone = I('phone');
//            if (empty($phone))  error("手机号不能为空");
//            if (!preg_match('/^1[3|4|5|7|8]\d{9}$/', $phone)) error("手机号错误");
//            if($phone != $member['phone'])      error("绑定手机号不匹配");
//            $verify = I('verify');
//            if (empty($verify)) error("手机验证码不能为空");
//            $check = M('Code')->where(['mobile' => $phone])->find();
//            if (!$check)        error("手机验证码未发送");
//            if ($verify != $check['verify']) error("手机验证码错误");
//            if (time() - $check['addtime'] > 60 * 20) {
//                error("手机验证码已过期");
//            }
            $password = I('password');
//            $repassword = I('repassword');
            if(empty($password))            error("支付密码不能为空");
//            if(empty($repassword))          error("确认密码不能为空");
            if(!ctype_digit($password))     error("请填写数字密码");
//            if($password != $repassword)    error("两次密码不一样");
            if(strlen($password) != '6')    error("请设置6位长度的支付密码");
            $data['pay_password'] = encrypt($password);
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Member')->where(['member_id'=>$member['member_id']])->save($data);
            if($result){
                success("设置支付密码成功");
            }else{
                error("设置支付密码失败");
            }
        }
    }

    /**
     *@修改支付密码
     */
    public function set_new_pay_password(){
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            if (empty($data['old_password'])) error("旧密码不能为空");
            if (empty($data['new_password'])) error("新密码不能为空");
            if (empty($data['repassword'])) error("确认新密码不能为空");
            if ($data['new_password'] != $data['repassword']) error("确认支付密码不一致");
            if(!ctype_digit($data['new_password']))     error("请填写数字密码");
            if(strlen($data['new_password']) != '6')    error("请设置6位长度的支付密码");
            if ($member['pay_password'] != encrypt($data['old_password'])) error("旧密码错误");
            if ($data['old_password'] == $data['new_password']) error("新密码不能和旧密码相同");
            $data['password'] = encrypt($data['new_password']);
            $result = M('Member')->where(['member_id' => $data['uid']])->save(['pay_password' => $data['password']]);
            if ($result) {
                success("修改支付密码成功");
            } else {
                error("修改支付密码失败");
            }
        }
    }

    /**
     *@找回支付密码
     */
    public function get_new_pay_password(){
        if(IS_POST){
            $member = checklogin();
            $phone = I('phone');
            if (empty($phone))  error("手机号不能为空");
            if (!preg_match('/^1[3|4|5|7|8]\d{9}$/', $phone)) error("手机号错误");
            if($phone != $member['phone'])      error("绑定手机号不匹配");
            $verify = I('verify');
            if (empty($verify)) error("手机验证码不能为空");
            $check = M('Code')->where(['mobile' => $phone])->find();
            if (!$check)        error("手机验证码未发送");
            if ($verify != $check['verify']) error("手机验证码错误");
            if (time() - $check['addtime'] > 60 * 20) {
                error("手机验证码已过期");
            }
            $password = I('password');
            $repassword = I('repassword');
            if(empty($password))            error("支付密码不能为空");
            if(empty($repassword))          error("确认密码不能为空");
            if($password != $repassword)    error("两次密码不一样");
            $data['pay_password'] = encrypt($password);
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Member')->where(['member_id'=>$member['member_id']])->save($data);
            if($result){
                success("修改支付密码成功");
            }else{
                error("修改支付密码失败");
            }
        }

    }




    /**
     *@优惠券
     */
    public function my_coupon(){
        if(IS_POST){
            checklogin();
            $uid = I('uid');
            $status = I('status');
            $data = M('MemberCoupon')->alias('a')
                ->field('a.intime,b.end_time,a.id')
                ->join("LEFT JOIN __COUPON__ b on a.coupon_id = b.id")
                ->where(['a.member_id'=>$uid,'a.status'=>$status,'b.is_del'=>'1'])->select();
            foreach($data as $key=>$val){
//                $intime = strtotime($val['intime']);
                if(time()>$val['end_time']){
                    M('MemberCoupon')->where(['id'=>$val['id']])->save(['status'=>3]);
                }
            }
            $list = M('MemberCoupon')->alias('a')
                  ->field('a.id,b.title,b.img,b.limit_value,b.value,b.start_time,b.end_time')
                  ->join("LEFT JOIN __COUPON__ b on a.coupon_id = b.id")
                ->where(['a.member_id'=>$uid,'a.status'=>$status,'b.is_del'=>'1'])->select();
            foreach($list as $key => $val){
                $list[$key]['start_time'] = date("Y-m-d",$val['start_time']);
                $list[$key]['end_time']   = date("Y-m-d",$val['end_time']);
            }
            if(empty($list)){
                error("暂无数据");
            }else{
                success($list);
            }
        }
    }

    /**
     *@我的系统公告消息
     */
    public function notice(){
        $member = checklogin();
        $p = I('p');
        empty($p) && $p = 1;
        $map['type'] = '2';
        $map['is_del'] = '1';
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $count = M('Notice')->where($map)->count();
        $page = ceil($count/$num);
        $list = M("Notice")->field('summary,intime')
            ->where($map)->order("state desc,is_top desc")
             ->limit(($p - 1) * $num, $num)
             ->select();
        foreach($list as $key => $val){
            $list[$key]['intime'] = date("m-d H:i",strtotime($val['intime']));
        }
        success(['page'=>$page,'data'=>$list]);
    }

    /**
     *@系统公告消息详情
     */
    public function notice_detail(){
        checklogin();
        $notice_id = I('notice_id');
        $detail = M('MemberNotice')->alias('a')
                ->field("b.title,b.content,b.title,a.intime")
                ->join("LEFT JOIN __NOTICE__ b on a.notice_id = b.id")
                ->where(['a.id'=>$notice_id])
                ->find();
//        $detail['content'] =  htmlspecialchars($detail['content']);
        M('MemberNotice')->where(['id'=>$notice_id])->save(['status'=>'2','read_time'=>date("Y-m-d H:i;s",time())]);
        success($detail);
    }

    public function about_us(){
        $about_us = M("Aboutus")->field("tel,wechat,qq,hotline,email,intro")->where(['id'=>1])->find();
        success($about_us);
    }

    public function my_message()
    {
        if (IS_POST) {
            $member = checklogin();
            $p = I('p');
            empty($p) && $p = 1;
            $num = 10;
            $count = M('Message')->alias('a')
                ->where(['user_id' => $member['user_id']])->count();
            $page = ceil($count / $num);
            $list = M('Message')
                ->where(['user_id' => $member['user_id']])
                ->order("intime desc")
                ->limit(($p - 1) * $num, $num)
                ->select();
            M('Message')->where(['user_id' => $member['user_id']])->save(['is_read' => '2']);
            success(['list' => $list, 'page' => $page]);
        }
    }

    public function has_read_message(){
        $user = checklogin();
        $count = M('Message')->where(['user_id'=>$user['user_id'],'is_read'=>1])->count();
        if($count){
//            $count = (string)$count;
            success("1");
        }else{
            success("2");
        }
    }

    /**
     *@积分规则说明
     */
    public function score_explain(){
        if(IS_POST){
            $member = checklogin();
            $explain = M('Notice')->field('content')->where(['id'=>7])->find();
            success($explain);
        }
    }

    /**
     *@积分记录
     */
    public function score_record(){
        if(IS_POST){
            $member = checklogin();
            $uid = I('uid');
            $p = I('p');
            empty($p) && $p = 1;
            $num = 10;
            $count = M('ScoreRecord')->where(['member_id'=>$uid])->count();
            $page = ceil($count / $num);
            $list = M('ScoreRecord')
                ->field('type,number,intime,detail,order_id,order_type,extra_number')
                ->where(['member_id'=>$uid])->order("intime desc")
                ->limit(($p - 1) * $num, $p * $num)->select();
            foreach($list as $key=>$val){
                if($val['order_type'] == 1){
                    $airport = M("AirportOrderFlight")->where(['order_id'=>$val['order_id']])->find();
                    $list[$key]['go_hangzhan'] = $airport['go_hangzhan'];
                    $list[$key]['arrive_hangzhan'] = $airport['arrive_hangzhan'];
                    $list[$key]['go_time'] = date("m-d",strtotime(str_replace("T",' ',$airport['go_time'])));
                    $list[$key]['week'] = get_week(str_replace("T",' ',$airport['go_time']));
                }else{
                  $hotel = M('HotelOrder')->alias('a')
                      ->field("a.room,b.address,a.start_time,b.name")
                      ->join("LEFT JOIN __HOTEL__ b on a.hotel_id = b.hotel_id")
                      ->where(['a.order_id'=>$val['order_id']])
                      ->find();
                    $list[$key]['start_time'] = date("m-d",strtotime($hotel['start_time']));
                    $list[$key]['hotel_name'] = $hotel['name'];
                    $list[$key]['week'] = get_week($hotel['start_time']);
                    $list[$key]['room'] = $hotel['room'];
                }
            }
            success(['page'=>$page,'data'=>$list]);
        }
    }

    /**
     *@余额记录
     */
    public function amount_record(){
        if(IS_POST){
            $member = checklogin();
            $uid = I('uid');
            $p = I('p');
            empty($p) && $p = 1;
            $num = 10;
            $count = M('AmountRecord')->where(['member_id'=>$uid])->count();
            $page = ceil($count / $num);
            $list = M('AmountRecord')
                ->field('type,amount,intime,content')
                ->where(['member_id'=>$uid])->order("intime desc")
                ->limit(($p - 1) * $num, $p * $num)->select();
            if(!empty($list)){
                success(['page'=>$page,'data'=>$list]);
            }else{
               error("暂无数据");
            }
        }
    }

    /**
     *@反馈信息
     */
    public function feedback(){
        if(IS_POST){
            $member = checklogin();
            $data['feedback'] = I('feedback');
            if(empty($data['feedback']))        error("反馈信息不能为空");
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['member_id'] = $member['member_id'];
            $result = M('Feedback')->add($data);
            if($result){
                success("反馈成功");
            }else{
                error("反馈失败");
            }
        }
    }

    /**
     * @绑定手机号
     */
    public function set_phone(){
        if(IS_POST){
            $member = checklogin();
            $phone = I('phone');
            if (empty($phone))                  error("手机号不能为空");
            if (!preg_match('/^1[3|4|5|7|8]\d{9}$/', $phone)) error("手机号错误");
            $verify = I('verify');
            if (empty($verify))                 error("手机验证码不能为空");
            $check = M('Code')->where(['mobile' => $phone])->order('code_id desc')->limit(1)->find();
            if (!$check)                        error("该手机验证码未发送");
            if ($verify != $check['verify'])    error("手机验证码错误");
            if (time() - strtotime($check['intime']) > 60 * 20) {
                error("手机验证码已过期");
            }
            $result = M('Member')->where(['member_id'=>$member['member_id']])->save(['phone'=>$phone]);
            if($result){
                M('Code')->where(['mobile' => $phone])->delete();
               success("绑定成功");
            }else{
                error("绑定失败");
            }
        }
    }

    /**
     *@换绑手机号
     */
    public function edit_phone(){
        if(IS_POST){
            $member = checklogin();
            $phone = I('phone');
            if (empty($phone))                  error("绑定手机号不能为空");
            if (!preg_match('/^1[3|4|5|7|8]\d{9}$/', $phone)) error("手机号错误");
            if(M('Member')->where(['phone'=>$phone])->find()) error("该手机号已使用");
            $verify = I('verify');
            $old_verify = I('old_verify');
            if(empty($old_verify))              error("解绑手机号证码为空");
            $check = M('Code')->where(['mobile' => $member['phone']])->find();
            if (!$check)                        error("验证码未发送");
            if ($$old_verify != $check['verify'])    error("解绑手机号验证码错误");
            if (time() - strtotime($check['intime']) > 60 * 20) {
                error("解绑手机号验证码已过期");
            }
            if (empty($verify))                 error("绑定手机号验证码为空");
            $check = M('Code')->where(['mobile' => $phone])->find();
            if (!$check)                        error("验证码未发送");
            if ($verify != $check['verify'])    error("绑定手机号验证码错误");
            if (time() - strtotime($check['intime']) > 60 * 20) {
                error("绑定手机号验证码已过期");
            }
            $result = M('Member')->where(['member_id'=>$member['member_id']])->save(['phone'=>$phone]);
            if($result){
                M('Code')->where(['mobile' => $phone])->delete();
                success("操作成功");
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@等级规则说明
     */
    public function grade_explain(){
        if(IS_POST){
            $member = checklogin();
            $explain = M('Notice')->field('content')->where(['id'=>12])->find();
            success($explain);
        }
    }

    /**
     *@会员消费明细
     */
    public function trade_record(){
        if(IS_POST){
            $member = checklogin();
            $count = M('TradeRecord')->where(['member_id'=>$member['member_id']])->count();
            $num = 10;
            $p = I('p');
            if(empty($p)){
                $p = 1;
            }
            $page = ceil($count/$num);
            $amount = M('TradeRecord')->where(['member_id'=>$member['member_id']])->sum("amount");
            $list = M('TradeRecord')->where(['member_id'=>$member['member_id']])
                ->order("intime desc")->limit(($p-1)*$num,$p*$num)->select();
            foreach($list as $k=>$v){
                if($v['type'] == '1'){
                    $list[$k]['type'] = '机票订单';
                }else if($v['type'] == '2'){
                    $list[$k]['type'] = '酒店订单';
                }

                if($v['is_change'] == '1'){
                    $list[$k]['is_change'] = '正常支付';
                }else if($v['is_change'] == '2'){
                    $list[$k]['is_change'] = '改签支付';
                }else{
                    $list[$k]['is_change'] = '';
                }
            }

            success(['list'=>$list,'page'=>$page,'amount'=>$amount]);

        }
    }

    /**
     *@教学收益
     */
    public function earnings_list(){
        $member = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $p  ?   $p  :   $p = 1;
        $pageSize   ?   $pageSize   :   $pageSize = 10;
        $count = M('Earnings')->where(['user_id'=>$member['user_id']])->count();
        $page = ceil($count/$pageSize);
        $list = M('Earnings')->where(['user_id'=>$member['user_id']])
            ->limit(($p-1)*$pageSize,$pageSize)->order("earnings_id desc")
            ->select();
        if(empty($list))            $list = [];
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@提现信息
     */
    public function withdraw_info(){
        $member = checklogin();
        $amount = $member['money'];
        $card = M('BankCard')->where(['type'=>'1','member_id'=>$member['user_id'],'is_default'=>2])->find();
        if(empty($card))        $card = (object)null;
        $ratio = M('System')->where(['id'=>1])->getField('withdraw_ratio');
        success(['amount'=>$amount,'card'=>$card,'ratio'=>$ratio]);
    }

    /**
     *@课程通知（短信）
     * @param string 内容
     */
    public function send_notice(){
        if(IS_POST){
            $user = checklogin();
            $content = I('content');
            $live_class = M('TutorLiveClass')->where(['tutor_id'=>$user['user_id']])
                    ->order('id desc')->limit(1)->find();
            $start_time = strtotime($live_class['start_time']);
            $end_time = strtotime($live_class['end_time']);
            $list = M('UpgradeRecord')->alias('a')
                  ->field('b.phone')
                  ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                  ->where(['a.user_id2'=>$user['user_id'],'a.date_value'=>['between',[$start_time,$end_time]]])
                  ->select();
            if(!empty($list)){
                foreach($list as $k=>$v){
                    zhutong_sendSMS($content,$v['phone']);
                }
            }
            success("发送成功");
        }
    }

    /**
     *@我的学员
     */
    public function my_students(){
        $user = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $p      ?   $p = $p  :   $p = 1;
        $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
        $live_class = M('TutorLiveClass')->where(['tutor_id'=>$user['user_id']])
            ->order('id desc')->limit(1)->find();
        $start_time = $live_class['start_time'];
        $end_time = $live_class['end_time'];
        $count = M('UpgradeRecord')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.user_id2'=>$user['user_id'],'a.date_value'=>['between',[$start_time,$end_time]]])
            ->group("a.user_id")
            ->count();
        $page = ceil($count/$pageSize);
        $list = M('UpgradeRecord')->alias('a')
            ->field("b.user_id,b.username,b.sex,b.img,b.autograph")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.user_id2'=>$user['user_id'],'a.date_value'=>['between',[$start_time,$end_time]]])
            ->group("a.user_id")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->order("a.intime desc")
            ->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['img'] = $this->url.$v['img'];
                $list[$k]['state'] = M('UpgradeRecord')->where(['user_id'=>$v['user_id']])->order('intime desc')->limit(1)->getField('state');
            }
        }else{$list = [];};
        success(['list'=>$list,'page'=>$page]);
    }

    /**
     *@禁言名单
     */
    public function banned_list(){
        $member = checklogin();
        $p = I('p');
        $pageSize = I('pagesize');
        $p      ?   $p = $p  :   $p = 1;
        $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
        $count = M('Banned')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->where(['a.user_id'=>$member['user_id']])
            ->group("a.user_id2")
            ->count();
        $page = ceil($count/$pageSize);
        $list = M('Banned')->alias('a')
            ->field("b.user_id,b.username,b.sex,b.img,b.autograph")
            ->join("LEFT JOIN __USER__ b on a.user_id2 = b.user_id")
            ->where(['a.user_id'=>$member['user_id']])
            ->group("a.user_id2")
            ->select();
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['img'] = $this->url.$v['img'];
            }
        }else{$list = [];};
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@取消禁言
     */
    public function cancel_banned(){
        if(IS_POST){
            $member = checklogin();
            $user_id = I('user_id');
            if(empty($user_id))         error("参数错误");
            $result = M('Banned')->where(['user_id'=>$member['user_id'],'user_id2'=>$user_id])->delete();
            if($result){
                success("删除成功");
            }else{
                error("删除失败");
            }
        }
    }

    /**
     * @提现申请
     */
    public function withdraw(){
        if(IS_POST){
            $member = checklogin();
            $card_id = I('card_id');
            $score = I('money');
            if($score > $member['money'])              error("提现金额不足");
            if($score<1000)                             error("提现金额不能少于1000");
            $ratio = M('System')->where(['id'=>1])->getField('withdraw_ratio');
            $amount = sprintf("%.2f",$score*$ratio);
            if(empty($card_id))                         error("提现账户不能为空");
            if(empty($amount))                          error("提现金额不能为空");
            $data['score'] = $score;
            $data['amount'] = $amount;
            $data['status'] = 1;
            $data['card_id'] = $card_id;
            $data['mid'] = $member['user_id'];
            $data['intime'] = time();
            $data['date'] = date("Y-m-d H:i:s",time());
            M('Withdraw')->startTrans();//开启事务
            if (M('Withdraw')->add($data)){
                $result = M('User')->where(['user_id'=>$member['user_id']])->save(['money'=>$member['money']-$score]);
                if($result){
                    M('Withdraw')->commit();  //提交事务
                    success('提交成功!');
                }else {
                    M('Withdraw')->rollback();  //事务回滚
                    error('失败!');
                }
            }else {
                M('Withdraw')->rollback();  //事务回滚
                error('失败!');
            }
        }
    }

    /**
     *@充值记录
     */
    public function recharge_record()
    {
        if (IS_POST) {
            $member = checklogin();
//            if (!empty($_POST['start_time'])) $start_time = I('start_time'); else $start_time = 0;
//            if (!empty($_POST['end_time'])) $end_time = I('end_time'); else $end_time = date("Y-m-d H:i:s", time());
//            $map['intime'] = ['between', [$start_time, $end_time]];
            $map['member_id'] = $member['user_id'];
            $map['pay_status'] = '2';
            $p = I('p');
            empty($p) && $p = 1;
            $pagesize = I('pagesize');
            $pagesize ? $pagesize = $pagesize : $pagesize = 10;
            $count = M('Recharge')->where($map)->count();
            $page = ceil($count / $pagesize);
            $data = M('Recharge')->field('amount,uptime,pay_type,score')
                ->where($map)->limit(($p - 1) * $pagesize, $pagesize)->select();
            foreach($data as $key=>$val){
                switch($val['pay_type']){
                    case 'wx':
                        $data[$key]['pay_type'] = '微信';
                        break;
                    case 'alipay':
                        $data[$key]['pay_type'] = '支付宝';
                        break;
                    case 'uppay':
                        $data[$key]['pay_type'] = '银联';
                        break;
                    case 'online':
                        $data[$key]['pay_type'] = '后台';
                        break;
                }
            }
            success(['data' => $data, 'page' => $page]);
        }

    }

    /**
     *@提现记录
     */
    public function withdraw_record()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['a.mid'] = $member['user_id'];
//            $map['a.status'] = 3;
            $p = I('p');
            $pageSize = I('pagesize');
            $p      ?   $p = $p  :   $p = 1;
            $pageSize     ?   $pageSize = $pageSize  :   $pageSize = 10;
            $count = M('Withdraw')->alias('a')
                ->join("INNER JOIN __BANK_CARD__ b on a.card_id = b.id")
                ->where($map)->count();
            $page = ceil($count / $pageSize);
            $data = M('Withdraw')->alias('a')
                ->field('a.amount,a.date,a.score,b.pay_type,b.bank_card,a.status')
                ->join("INNER JOIN __BANK_CARD__ b on a.card_id = b.id")
                ->where($map)->limit(($p - 1) * $pageSize, $pageSize)->select();
            foreach($data as $key=>$val){
                switch($val['pay_type']){
                    case 'wx':
                        $data[$key]['pay_type'] = '微信';
                        break;
                    case 'alipay':
                        $data[$key]['pay_type'] = '支付宝';
                        break;
                    case 'uppay':
                        $data[$key]['pay_type'] = '银联';
                        break;
                }

            }
            success(['list' => $data, 'page' => $page]);
        }

    }

    /**
     *@提现审核
     */
    public function withdraw_review(){
        if(IS_POST){
            $member = checklogin();
            $time = time();
            $s_time = $time - 48*3600;
            $map['mid'] = $member['member_id'];
            $map['status'] = ['in',['1','3']];
            $map['intime'] = ['between',[$s_time,$time]];
            $list = M('Withdraw')->field('status,amount,score,intime')
                ->where($map)->order("intime desc")->select();
            foreach($list as $k=>$v){
                if($v['status'] == 1){
                    if(time() - $v['intime']>24*3600){
                        $list[$k]['status'] = 2;
                    }
                }
                $list[$k]['intime'] = date("Y-m-d H:i",$v['intime']);
                $list[$k]['date_vale'] = translate_date($list[$k]['intime']);
            }
            success($list);
        }
    }

    /**
     *@我的积分
     */
    public function my_score(){
        $member = checklogin();
        $card = M('BankCard')->field('bank_card')
            ->where(['type'=>'1','is_default'=>'2','member_id'=>$member['member_id']])->find();
        success(['amount'=>(int)$member['amount'],'score'=>(int)$member['score'],'card'=>$card['bank_card']]);
    }



    /**
     *@我的分享二维码
     */
    public function share_code(){
        $uid = I('uid');
        $prefix = C('IMG_PREFIX');
        $qrcode = M('Member')->where(['member_id'=>$uid])->getField('share_qrcode');
        if(!empty($qrcode)){
            $qrcode = $prefix.$qrcode;
        }
        success($qrcode);
    }

    /**
     *@我的邀请名单
     */
    public function my_share_list(){
        $member = checklogin();
        $map['a.mid'] = $member['member_id'];
        $p = I('p');
        empty($p)   &&  $p = 1;
        $count = M('Share')->alias('a')
            ->join("INNER JOIN __MEMBER__ b on a.share_id = b.member_id")
            ->where($map)->count();
        $num = 10;
        $page = ceil($count/$num);
        $list = M('Share')->alias('a')
              ->field("b.member_id,a.intime,a.score,b.img,b.nickname")
              ->join("INNER JOIN __MEMBER__ b on a.share_id = b.member_id")
              ->limit(($p-1)*$num,$num)->where($map)
              ->order("a.intime desc")->select();
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@中奖记录
     */
    public function my_prize_record(){
        $member = checklogin();
        $map['a.mid'] = $member['member_id'];
        $p = I('p');
        $map['a.type'] = ['in',['1','2']];
        empty($p)      &&   $p = 1;
        $num = 10;
        $count = M('PrizeRecord')->alias('a')
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->where($map)
            ->count();
        $page = ceil($count/$num);
        $list = M('PrizeRecord')->alias('a')
            ->field("a.id,a.intime,b.name,a.state,a.is_used,b.img,a.type")
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->where($map)->limit(($p - 1)*$num,$num)
            ->order("a.intime desc")->select();
        success(['page'=>$page,'list'=>$list]);

    }
    /**
     *@中奖详情
     */
    public function my_prize_detail(){
        $member = checklogin();
        $id = I('id');
        if(empty($id))              error("参数错误");
        $map['a.id'] = $id;
        $map['a.mid'] = $member['member_id'];
        $detail = M('PrizeRecord')->alias('a')
            ->field("a.id,a.intime,b.name,a.state,a.is_used,b.img,a.type")
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->where($map)->find();
        success($detail);
    }

    /**
     *@奖品领取注意事项
     */
    public function prize_rule(){
        $member = checklogin();
        $explain = M('Notice')->field('content')->where(['id'=>17])->find();
        success($explain);
    }

    /**
     *@使用
     */
    public function use_prize(){
        $member = checklogin();
        $id = I('id');
        if(empty($id))              error("参数错误");
        $check = M('PrizeRecord')->alias('a')
            ->field("a.id,a.intime,b.name,a.state,a.is_used,b.img,a.type,b.value")
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->where(['a.id'=>$id,'a.mid'=>$member['member_id']])->find();
        if(!$check)                 error("参数错误");
        if($check['is_used'] == 2){
            error("请勿重复使用");
        }
        if($check['type'] == 2){
            $score = $member['score'] + $check['value'];
            $member['score'] = $score;
            M('Member')->where(['member_id'=>$member['member_id']])->save(['score'=>$score]);
        }
        $result = M('PrizeRecord')->where(['id'=>$id])->save(['is_used'=>'2','uptime'=>date("Y-m-d H:i:s",time())]);
        if($result){
            success("成功");
        }else{
            error("失败");
        }
    }


    /**
     *我的道具
     */
    public function my_prop(){
        $member = checklogin();
        $type = I('type');
        empty($type) && $type = 1;
        $p = I('p');
        empty($p)  && $p = 1;
        if($type == 1){
            $map['a.user_id2'] = $member['member_id'];
        }else{
            $map['a.user_id'] = $member['member_id'];
        }
        $count = M('GiveGift')->alias('a')
            ->join("LEFT JOIN __GIFT__ b on a.gift_id = b.gift_id")
            ->where($map)
            ->count();
        $page = ceil($count/10);
        $num = 10;
        $list = M('GiveGift')->alias('a')
            ->field("b.name,b.img,b.price,a.intime")
            ->join("LEFT JOIN __GIFT__ b on a.gift_id = b.gift_id")
            ->limit(($p-1)*$num,$num)
            ->where($map)->order("a.intime desc")
            ->select();
        foreach($list as $k=>$v){
            $list[$k]['intime'] = date("Y-m-d H:i:s",$v['intime']);
        }
        $total = M('GiveGift')->alias('a')
            ->field("b.name,b.img,b.price,a.intime")
            ->join("LEFT JOIN __GIFT__ b on a.gift_id = b.gift_id")
            ->limit(($p-1)*$num,$num)
            ->where($map)->sum('b.price');
        success(['page'=>$page,'list'=>$list,'total'=>$total]);
    }

    /**
     * @判断版本号
     */
    public function is_this(){
        $version = I('version');
        empty($version) ? error('参数错误!') : true;
        $ve = M('System')->where(['id'=>1])->getField('ios_version');
        if ($ve==$version){
            $result = "1";
        }else{
            $result = "2";
        }
        success($result);
    }


}