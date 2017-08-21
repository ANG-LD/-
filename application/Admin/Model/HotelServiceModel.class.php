<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/11/1
 * Time: 10:40
 */

namespace Admin\Model;
use Think\Model;
class HotelServiceModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['name']))        return array('status'=>'error','info'=>'服务名称不能为空');
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:S",time());
            $result = M('HotelService')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('HotelService')->where(['id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'记录成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'记录失败');
        }
    }
}