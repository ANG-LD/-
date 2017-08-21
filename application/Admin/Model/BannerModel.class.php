<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/8/18
 * Time: 15:57
 */

namespace Admin\Model;
use Think\Model;
class BannerModel extends Model
{
    public function auth(){
        $data = array(
            'title' =>      I('title'),
            'b_img' =>      I('b_img'),
            'type'  =>      I('type'),
            'b_type'=>      I('b_type'),
            'url' =>        I('url'),
            'sort' =>       I('sort'),
            'b_id' =>       I('b_id'),
            'content' =>       I('content'),
        );
        $good = I('good');
        $user = I('user');
        if(empty($data['title'])){
            return array('status'=>'error','info'=>'请必须填写标题！');
        }
        if(empty($data['b_img'])){
            return array('status'=>'error','info'=>'请必须上传图片！');
        }
        if(empty($data['b_type'])){
            return array('status'=>'error','info'=>'类型不能为空！');
        }
        if($data['b_type'] == 2){
            if(empty($data['content'])){
                return array('status'=>'error','info'=>'跳转内容不能为空！');
            }
        }
        if($data['b_type'] == 3){
            $re = M('User')->where(['user_id'=>$user])->find();
            if(!$re){
                return array('status'=>'error','info'=>'不存在该导师！');
            }
            $data['value'] = $user;
        }

        if($data['b_type'] == 4){
            $re = M('Goods')->where(['goods_id'=>$good])->find();
            if(!$re){
                return array('status'=>'error','info'=>'不存在该商品！');
            }
            $data['value'] = $good;
        }

        if(empty($data['b_id'])){
            $data['b_intime'] = date("Y-m-d h:i:s",time());
            $result = M('Banner')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = date("Y-m-d h:i:s",time());
            $result = M('Banner')->where(['b_id'=>$data['b_id']])->save($data);
            $action = '编辑';
        }

        if($result !==false){
            return array('status'=>'ok','info'=>$action.'成功！','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'失败！');
        }

    }
}