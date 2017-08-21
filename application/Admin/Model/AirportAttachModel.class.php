<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/25
 * Time: 11:45
 */

namespace Admin\Model;
use Think\Model;
class AirportAttachModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['title']))           error("保险主题不能为空");
        if(empty($data['price']))           error("投保金额不能为空");
        if(empty($data['danwei']))          error("基础单位不能为空");
        if(empty($data['explain']))          error("保险说明不能为空");
        if(empty($data['content']))          error("保险详情不能为空");
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i;s",time());
            $result = M('AirportAttach')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('AirportAttach')->where(['attach_id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'记录成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'记录失败');
        }
    }
}