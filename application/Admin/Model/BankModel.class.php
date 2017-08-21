<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/8
 * Time: 17:39
 */

namespace Admin\Model;
use Think\Model;
class BankModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['name'])){
            return array('status'=>'error','info'=>'银行名称不能为空','class'=>'');
        }
        if(empty($data['img'])){
            return array('status'=>'error','info'=>'图片名称不能为空','class'=>'');
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('Bank')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = date("Y-m-d h:i:s",time());
            $result = M('Bank')->where(['bank_id'=>$data['id']])->save($data);
            $action = '编辑';
        }

        if($result){
            return array('status'=>'ok','info'=>$action.'成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'失败');
        }
    }
}