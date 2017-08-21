<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/20
 * Time: 14:31
 */

namespace Admin\Model;
use Think\Model;

class PriceListModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['price'])){
            return array('status'=>'error','info'=>'价格不能为空','class'=>'price');
        }
        if(!ctype_digit($data['price'])){
            return array('status'=>'error','info'=>'请填写整型数字','class'=>'price');
        }
        if(empty($data['diamond'])){
            return array('status'=>'error','info'=>'充值秘豆不能为空','class'=>'diamond');
        }
        if(!ctype_digit($data['diamond'])){
            return array('status'=>'error','info'=>'请填写整型数字','class'=>'diamond');
        }
        if(empty($data['id'])){
            $data['intime'] = time();
            $result = M('PriceList')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = time();
//            if(empty($data['sign'])){
//                return array('status'=>'error','info'=>'请填写内购标签','class'=>'sign');
//            }
            $result = M('PriceList')->where(['price_list_id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        }else{
            return array('status' => 'error', 'info' => $action . '记录失败','class'=>'');
        }
    }
}