<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/27
 * Time: 18:05
 */

namespace Admin\Model;
use Think\Model;
class GoodsTailorModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['goods_id'])){
            return array('status'=>'error','info'=>'商品不能为空','class'=>'');
        }
        if(empty($data['presale_price'])){
            return array('status'=>'error','info'=>'预售价不能为空','class'=>'');
        }
        if(empty($data['min_count'])) {
            return array('status'=>'error','info'=>'参团人数不能为空','class'=>'');
        }
        if(empty($data['end_time'])) {
            return array('status'=>'error','info'=>'结束时间不能为空','class'=>'');
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('GoodsTailor')->add($data);
            $action = "添加";
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['status'] = 1;
            $result = M('GoodsTailor')->where(['tailor_id'=>$data['id']])->save($data);
            $action = "编辑";
        }
        if($result){
            return array('status' => 'ok', 'info' => $action . '商品记录成功!', 'url' => session('url'),'class'=>'');
        }else{
            return array('status' => 'error', 'info' => $action . '商品记录失败','class'=>'');
        }
    }
}