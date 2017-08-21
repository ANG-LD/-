<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/14
 * Time: 15:23
 */

namespace Admin\Model;
use Think\Model;
class HotelRoomModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['name'])) return array('status'=>'error','info'=>'房间型号名不能为空');
        if(empty($data['img'])) return array('status'=>'error','info'=>'房间图片不能为空');
        if(empty($data['price'])) return array('status'=>'error','info'=>'房间价格不能为空');
        if(!M('Hotel')->where(['hotel_id'=>$data['hotel_id']])->find()){
            return array('status'=>'error','info'=>'对应酒店uid没有找到');
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('HotelRoom')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('HotelRoom')->where(['id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if ($result !== false) {
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        } else {
            return array('status' => 'error', 'info' => $action . '记录失败');
        }
    }
}