<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/12
 * Time: 9:52
 */

namespace Admin\Model;
use Think\Model;
class AboutusModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['company'])){
            return array('status'=>'error','info'=>'企业名称不能为空');
        }
        if(empty($data['tel'])){
            return array('status'=>'error','info'=>'企业电话不能为空');
        }
        if(empty($data['wechat'])){
            return array('status'=>'error','info'=>'企业微信不能为空');
        }
        if(empty($data['record'])){
            return array('status'=>'error','info'=>'企业备案号不能为空');
        }
        if(empty($data['record'])){
            return array('status'=>'error','info'=>'企业备案号不能为空');
        }
        if(empty($data['address'])){
            return array('status'=>'error','info'=>'企业地址不能为空');
        }
        if(empty($data['address'])){
            return array('status'=>'error','info'=>'企业地址不能为空');
        }
        $result = M('Aboutus')->where(['id'=>'1'])->save($data);
        if($result){
            return array('status'=>'ok','info'=>'编辑企业信息成功');
        }else{
            return array('status'=>'error','info'=>'编辑企业信息失败');
        }
    }
}