<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/9
 * Time: 9:22
 */

namespace Admin\Model;
use Think\Model;
class ArticleModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['title'])){
            return array('status'=>'error','info'=>'标题不能为空');
        }
        if(empty($data['img'])){
            return array('status'=>'error','info'=>'图片不能为空');
        }
        if(empty($data['content'])){
            return array('status'=>'error','info'=>'内容不能为空');
        }
        if(empty($data['author'])){
            return array('status'=>'error','info'=>'作者不能为空');
        }
        if(empty($data['id'])){
            $data['intime'] = date('Y-m-d H:i:s',time());
            $result = M('Article')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date('Y-m-d H:i:s',time());
            $result = M('Article')->where(['id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'资讯操作成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'资讯操作失败');
        }


    }
}