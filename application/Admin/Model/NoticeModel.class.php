<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/12
 * Time: 16:42
 */

namespace Admin\Model;
use Think\Model;
class NoticeModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['title'])){
            return array('status'=>'error','info'=>'公告标题不能为空');
        }
        if(empty($data['summary'])){
            return array('status'=>'error','info'=>'公告详情不能为空');
        }
//        if(empty($data['content'])){
//            return array('status'=>'error','info'=>'公告内容不能为空');
//        }
        if(empty($data['object'])){
            $data['object'] = '0';
        }else{
            $data['object'] = implode(',',$data['object']);
        }
        if(empty($data['id'])){
            $data['type'] = '2';
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('Notice')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Notice')->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'公告信息成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'公告信息失败');
        }
    }
}