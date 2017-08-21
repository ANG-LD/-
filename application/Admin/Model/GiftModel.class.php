<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/19
 * Time: 20:05
 */

namespace Admin\Model;
use Think\Model;
class GiftModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['img'])){
            return array('status'=>'error','info'=>'图片不能为空','class'=>'img');
        }
        if(empty($data['price'])){
            return array('status'=>'error','info'=>'价格不能为空','class'=>'price');
        }
        if(empty($data['id'])){
            $data['intime'] = time();
            $result = M('Gift')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = time();
            $result = M('Gift')->where(['gift_id'=>$data['id']])->save($data);
            $action = '更新';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'记录成功','url'=>session('url'),'class'=>'');
        }else{
            return array('status'=>'ok','info'=>$action.'记录失败','class'=>'');
        }
    }
}