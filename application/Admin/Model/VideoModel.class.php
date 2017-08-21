<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/5
 * Time: 16:02
 */

namespace Admin\Model;


use Think\Model;

class VideoModel extends Model
{
    public function auth(){
        $data['title'] = I('title');
        if(empty($data['title'])){
            return array('status'=>'error','info'=>'标题不能为空','class'=>'title');
        }
        $data['video_img'] = I('video_img');
        if(empty($data['video_img'])){
            return array('status'=>'error','info'=>'视频封面不能为空','class'=>'video_img');
        }
        $data['url'] = I('url');
        if(empty($data['url'])){
            return array('status'=>'error','info'=>'请上传视频文件','class'=>'url');
        }
        $data['content'] = I('content');
        if(empty($data['content'])){
            return array('status'=>'error','info'=>'视频课程简介不能为空','class'=>'content');
        }
        $data['user_id'] = I('user_id');
        if(empty($data['user_id'])){
            return array('status'=>'error','info'=>'请选择导师','class'=>'username');
        }
        $id = I('id');
        if(empty($id)) {
            $data['intime'] = time();
            $result = M('Video')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = time();
            $result = M('Video')->where(['video_id'=>$id])->save($data);
            $action = ' 编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'视频课程成功','url'=>session('url'));
            die;
        }else{
            return array('status'=>'error','info'=>$action.'视频课程失败');
            die;
        }
    }
}