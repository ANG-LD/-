<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/17
 * Time: 11:45
 */

namespace Api\Controller;


class PublicController extends CommonController
{
    /**
     * @发送短信
     * @type 1:注册  2:找回密码
     * Enter description here ...
     */
    public function sendSMS()
    {
        function random($length = 6, $numeric = 0)
        {
            PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
            if ($numeric) {
                $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
            } else {
                $hash = '';
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
                $max = strlen($chars) - 1;
                for ($i = 0; $i < $length; $i++) {
                    $hash .= $chars[mt_rand(0, $max)];
                }
            }
            return $hash;
        }


        function xml_to_array($xml)
        {
            $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
            if (preg_match_all($reg, $xml, $matches)) {
                $count = count($matches[0]);
                for ($i = 0; $i < $count; $i++) {
                    $subxml = $matches[2][$i];
                    $key = $matches[1][$i];
                    if (preg_match($reg, $subxml)) {
                        $arr[$key] = xml_to_array($subxml);
                    } else {
                        $arr[$key] = $subxml;
                    }
                }
            }
            return $arr;
        }

        //发送验证码
        function Post($curlPost, $url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }


        $mobile = I('mobile');

        if (empty($mobile) || !preg_match('/^1[3|4|5|7|8]\d{9}$/', $mobile)) {
            error("手机格式不正确");
        }
        $type = I("type");
        empty($type) ? error('参数错误!') : true;
        $data = M("User")->where(array('phone'=>$mobile))->find();
        $check = M("code")->where(['mobile' => $mobile])->find();
        if($check){
            if (time() - strtotime($check['intime']) < 1200) {
               error("上一个验证码未过期");
            }
        }
        $mobile_code = random(6, 1);
        switch($type){
            case 1 :
                !empty($data) ? error("已注册") : true;//注册时验证是否出册
                break;
            case 2 :
                empty($data) ? error("未注册") : true;//修改密码验证是否已经注册
                break;
            case 3 :
                break;
        }
        $content = "您的短信验证码为：".$mobile_code."，请妥善保存。【名师传艺】";
            //用户密码 $password
        if (!empty($check)) {
            M('code')->where(['mobile' => $_POST['mobile']])->delete();
        }
        $gateway =zhutong_sendSMS($content,$mobile);
        $arr = explode(',',$gateway);
        //$result = substr($gateway,0,2);
        switch ($arr['0']){
            case 1:
                M("Code")->where(['mobile'=>$mobile])->delete();
                $data['mobile'] = $mobile;
                $data['content'] = "操作成功";
                $data['verify'] = $mobile_code;
                $data['intime'] = date("Y-m-d H:i:s");
                M("code")->add($data);
                success($mobile_code);
                break;
            case 12:
                error('提交号码错误!');
                break;
            case 13:
                error('短信内容为空!');
                break;
            case 17:
                error('一分钟内一个手机号只能发两次!');
                break;
            case 19:
                error('号码为黑号!');
                break;
            case 26:
                error('一小时内只能发五条!');
                break;
            case 27:
                error('一天一手机号只能发20条');
                break;
            default:
                error('发送失败!');
        }

    }

    /**
     *发送邮件验证码
     */
    public function send_email_verify(){
        $title = '邮箱验证信息';
        $verify = rand(100000,999999);
        $content = '你的邮箱验证码是：'.'<br/>'.$verify.'&nbsp;（验证码有效期为20分钟，请在有效期内使用）';
        $email = I('email');
        empty($email)   &&       error("邮箱为空");
        if(!is_email($email))    error("邮箱格式错误"); else true;
        $email_info = session('email_info');
        if(time()-$email_info['time']<20){
            error("邮件发送还在间隔期");
        }
        if(sendSimpleEmail($email, $title, $content)['sta'] =='1'){
            $info['verify'] = $verify;
            $info['time'] = time();
            $info['email'] = $email;
            session('email_info',$info);
            success("邮件发送成功");
        }else{
            error("邮件发送失败");
        };

    }
    /**
     * 发送简单邮件
     *
     * @param string $email
     * @param string $title
     * @param string $content
     * @return array $data["sta"]=1; $data["sta"]=2;
     *         $data["msg"]="发送失败".$r;
     */
    public function sendEmail(){
        $title = '告白气球';
        $content = "周杰伦《告白气球》歌词：
词：方文山
曲：周杰伦
塞纳河畔 左岸的咖啡
我手一杯 品尝你的美
留下唇印的嘴
花店玫瑰 名字写错谁
告白气球 风吹到对街
微笑在天上飞
你说你有点难追 想让我知难而退
礼物不需挑最贵 只要香榭的落叶
喔 营造浪漫的约会 不害怕搞砸一切
拥有你就拥有 全世界
亲爱的 爱上你 从那天起
甜蜜的很轻易
亲爱的 别任性 你的眼睛
在说我愿意
塞纳河畔 左岸的咖啡
我手一杯 品尝你的美
留下唇印的嘴
花店玫瑰 名字写错谁
告白气球 风吹到对街
微笑在天上飞
你说你有点难追 想让我知难而退
礼物不需挑最贵 只要香榭的落叶
喔 营造浪漫的约会 不害怕搞砸一切
拥有你就拥有 全世界
亲爱的 爱上你 从那天起
甜蜜的很轻易
亲爱的 别任性 你的眼睛
在说我愿意
亲爱的 爱上你 恋爱日记
飘香水的回忆
一整瓶 的梦境 全都有你
搅拌在一起
亲爱的别任性 你的眼睛
在说我愿意";
//        $email = I('email');
        $email = '2473682890@qq.com';
        if(!is_email($email))    error("邮箱格式错误"); else true;
        success(sendSimpleEmail($email, $title, $content));
    }

    /**
     *@用户相册:上传的图片
     */
    public function user_images(){
        if(IS_POst){
            $member = checklogin();
            $p = I('p');
            empty($p)   && $p = 1;
            $num = 15;
            $count = M('Images')->where(['mid'=>$member['member_id']])->count();
            $page = ceil($count/$num);
            $list = M('Images')->field('id,img,thumb')
                ->where(['mid'=>$member['member_id']])
                ->order("intime desc")->limit(($p-1)*$num,$num)
                ->select();
            success(['page'=>$page,'list'=>$list]);
        }
    }

    public function del_images(){
        if(IS_POST){
            $member = checklogin();
            $ids = I('ids');
            $map['id'] = ['in',$ids];
            $map['mid'] = $member['member_id'];
            $result = M('Images')->where($map)->delete();
            if($result){
                success("删除成功");
            }else{
                error("删除失败");
            }
        }
    }


}