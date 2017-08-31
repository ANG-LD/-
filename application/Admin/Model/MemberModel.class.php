<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/12
 * Time: 17:41
 */

namespace Admin\Model;
use Think\Model;
class MemberModel extends Model
{
    public function auth()
{
    $data = $_POST;
    if (empty($data['nickname'])) {
        return array('status' => 'error', 'info' => '昵称不能为空，请填写昵称!');
    }
    if (empty($data['phone']) || !(preg_match("/^1[34578]{1}\d{9}$/", $data['phone']))) {
        return array('status' => 'error', 'info' => '账号号填写错误，请填写正确手机号!');
    }

    if(empty($data['uid'])){
        if (M('Member')->where(['nickname' => $data['nickname']])->count() > 0){
            return array('status' => 'error', 'info' => '该昵称已存在，请重新填写昵称!');
        }
        if (M('Member')->where(['phone' => $data['phone']])->count() > 0) {
            return array('status' => 'error', 'info' => '该手机号已存在，请重新填写手机号!');
        }
    }else{
        $nickname = M('Member')->where(['member_id'=>$data['uid']])->getField('nickname');
        $phone = M('Member')->where(['member_id'=>$data['uid']])->getField('phone');
        if($nickname != $data['nickname']){
            if (M('Member')->where(['nickname' => $data['nickname']])->count() > 0){
                return array('status' => 'error', 'info' => '该昵称已存在，请重新填写昵称!');
            }
        }
        if($phone != $data['phone']){
            if (M('Member')->where(['phone' => $data['phone']])->count() > 0){
                return array('status' => 'error', 'info' => '该手机号已存在，请重新填写手机号!');
            }
        }
    }
    $data['token'] = uniqid();
    $data['img'] == '' ? $data['img'] = "/public/admin/touxiang.png" : $data['img'] = $data['img'];
    $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
    $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
    $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
    $data['address'] = I('address');
    if (empty($data['uid'])) {
        if (empty($data['pwd'])) {
            return array('status' => 'error', 'info' => '新增记录时，密码必须填写');
        } else {
            $data['password'] = myencrypt(I('pwd'));
        }
        if (!empty($data['pay_pwd'])) {
            $data['pay_password'] = myencrypt(I('pay_password'));
        }
        $data['intime'] = date("Y-m-d H:i:s", time());
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        mt_srand(10000000 * (double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 12; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        for ($i = 0, $str1 = '', $lc = strlen($chars) - 1; $i < 13; $i++) {
            $str1 .= $chars[mt_rand(0, $lc)];
        }
        $hx_password = "123456";
        $data['hx_password'] = $hx_password;
        $data['hx_username'] = $str;
//            $data['background_img'] = '/Uploads/image/touxiang/background_img.png';
        $data['alias'] = $str;
//        $data['hx_username2'] = $str1;
//        $data['hx_password2'] = $hx_password;
        $data['is_hand'] = 2;
        $result = M('Member')->add($data);
        if ($result) {
            huanxin_zhuce($str, $hx_password); //环信注册
            $action = '新增';
        }
    }else {
        if (!empty($data['pwd'])) {
            $data['password'] = myencrypt(I('pwd'));
        }
        if (!empty($data['pay_pwd'])) {
            $data['pay_password'] = myencrypt(I('pay_pwd'));
        }
        $data['uptime'] = date('Y-m-d H:i:s',time());
        $result = M('Member')->where(['member_id' => $data['uid']])->save($data);
        $action = '编辑';
    }
    if ($result !== false) {
        return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
    } else {
        return array('status' => 'error', 'info' => $action . '记录失败');
    }
}
}