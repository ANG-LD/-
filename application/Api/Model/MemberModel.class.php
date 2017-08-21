<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/17
 * Time: 15:33
 */

namespace Api\Model;
use Think\Model;
class MemberModel extends Model
{
    public function check(){
        if(empty($data['phone']))       error("手机号不能为空");
        if(!preg_match('#13^[0-9]\d{8}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}|^17[0-9]\d{8}$#', $data['phone'])) error("手机号错误");
        if(M('Member')->where(['phone'=>$data['phone']])->find()) error("该手机号已注册");
        if(empty($data['verify']))      error("手机号验证码不能为空");
        if(empty($data['password']))    error("密码不能为空");
        if(!preg_match('/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{8,16}$/',$data['password'])) error("请输入字母和数字组合的8-16位密码!");
        if(empty($data['repassword']))  error("确认密码不能为空");
    }
}