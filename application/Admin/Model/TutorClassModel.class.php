<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/6
 * Time: 16:06
 */

namespace Admin\Model;


use Think\Model;

class TutorClassModel extends Model
{
    public function auth(){
        $data['name'] = I('name');
        if(empty($data['name'])){
            return array('status'=>'error','info'=>'班级名不能为空','class'=>'title');
        }
        $data['img'] = I('img');
        if(empty($data['img'])){
            return array('status'=>'error','info'=>'图片不能为空','class'=>'img');
        }
        $data['tutor_id'] = I('user_id');
        if(empty($data['tutor_id'])){
            return array('status'=>'error','info'=>'请选择导师','class'=>'username');
        }
        $data['start_time'] = I('start_time');
        $data['end_time'] = I('end_time');
        if(empty($data['start_time'])){
            return array('status'=>'error','info'=>'开始时间不能为空','class'=>'start_time');
        }
        if(empty($data['end_time'])){
            return array('status'=>'error','info'=>'结束时间不能为空','class'=>'end_time');
        }
        $data['price'] = I('price');
        $data['vip_price'] = I('vip_price');
        if(empty($data['price'])){
            return array('status'=>'error','info'=>'价格不能为空','class'=>'price');
        }
        if(empty($data['vip_price'])){
            return array('status'=>'error','info'=>'VIP价格不能为空','class'=>'vip_price');
        }
        $data['limit_value'] = I('limit_value');
        $data['value'] = I('value');
        if(empty($data['limit_value'])){
            return array('status'=>'error','info'=>'限制不能为空','class'=>'limit_value');
        }
        if(!empty($data['value'])){
            if(!is_numeric($data['value'])){
                return array('status'=>'error','info'=>'报名人数错误','class'=>'value');
            }
        }
        $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
        $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
        $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
        $data['province'] ? $data['province'] : $data['province'] = '';
        $data['city'] ? $data['city'] : $data['city'] = '';
        $data['area'] ? $data['area'] : $data['area'] = '';
        $data['address'] = I('address');
        if(empty($data['province'])){
            return array('status'=>'error','info'=>'省份不能为空','class'=>'province');
        }
        if(empty($data['city'])){
            return array('status'=>'error','info'=>'城市不能为空','class'=>'city');
        }
        if(empty($data['address'])){
            return array('status'=>'error','info'=>'详细地址不能为空不能为空','class'=>'address');
        }
        $data['intro'] = I('intro');
        $data['content'] = I('content');
        if(empty($data['intro'])){
            return array('status'=>'error','info'=>'简介不能为空','class'=>'intro');
        }
        if(empty($data['content'])){
            return array('status'=>'error','info'=>'图文详情不能为空','class'=>'content');
        }
        $id = I('id');
        if(empty($id)) {
            $check = M('TutorClass')->where(['tutor_id'=>$data['tutor_id']])->order("intime desc")->limit(1)->find();
            if(time()<strtotime($check['end_time']) && $check){
                return array('status'=>'error','info'=>'该导师还在授课中,不能重新开班');
                die;
            }
            $data['intime'] = date("Y-m-d");
            $result = M('TutorClass')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = date("Y-m-d");
            $result = M('TutorClass')->where(['id'=>$id])->save($data);
            $action = ' 编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'线下班级成功','url'=>session('url'));
            die;
        }else{
            return array('status'=>'error','info'=>$action.'线下班级失败');
            die;
        }
    }
}