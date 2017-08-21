<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/21
 * Time: 17:22
 */

namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['category'])){
            return array('status'=>'error','info'=>'分类名称不能为空','class'=>'');
        }
        if(empty($data['picture'])){
            return array('status'=>'error','info'=>'图片不能为空','class'=>'');
        }

        if(empty($data['cate_id'])){
            if(empty($data['banner_img'])){
                return array('status'=>'error','info'=>'banner图片不能为空','class'=>'');
            }
        }

        if($data['cate_id'] != '0'){
            if(empty($data['cate_id'])){
                return array('status'=>'error','info'=>'请选择一级分类','class'=>'');
            }
        }
        if(!empty($data['sort'])){
            if(!ctype_digit($data['sort'])){
                return array('status'=>'error','info'=>'请填写正确数字','class'=>'');
            }
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('Category')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Category')->save($data);
            $action = '编辑';
        }
        if($data['type'] == 3 ){
            $a = '直播';
        }else{
            $a = '商城';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.$a.'分类记录成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.$a.'分类记录失败','class'=>'');
        }
    }
}