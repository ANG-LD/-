<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/14
 * Time: 17:43
 */

namespace Admin\Model;
use Think\Model;
class CouponModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['title'])) return array('status'=>'error','info'=>'优惠券名称不能为空');
        if(empty($data['img'])) return array('status'=>'error','info'=>'优惠券图片不能为空');
        if(empty($data['value'])) return array('status'=>'error','info'=>'优惠券价值不能为空');
        if(empty($data['limit_value'])) return array('status'=>'error','info'=>'最低限额不能为空');
        if(empty($data['number'])) return array('status'=>'error','info'=>'发放数量不能为空');
        if(empty($data['start_time'])) return array('status'=>'error','info'=>'开始时间不能为空');
        $data['start_time'] = strtotime($data['start_time']);
        if(!empty($data['end_time'])){
            $data['end_time'] = strtotime($data['end_time']);
        }else{
            return array('status'=>'error','info'=>'过期时间不能为空');
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['balance'] = $data['number'];
            $result = M('Coupon')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i;s",time());
            $result = M('Coupon')->where(['id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if ($result) {
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        } else {
            return array('status' => 'error', 'info' => $action . '记录失败');
        }
    }
}