<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/16
 * Time: 10:53
 */

namespace Admin\Model;
use Think\Model;
class ModuleModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['title'])){
            return array('status'=>'error','info'=>'请必须填写模块名称','class'=>'');
        }
        if(empty($data['picture'])){
            return array('status'=>'error','info'=>'请必须上传图片','class'=>'');
        }
        if(empty($data['module_id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Module')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Module')->where(['module_id'=>$data['module_id']])->save($data);
            $action = '编辑';
        }

        if($result){
            return array('status'=>'ok','info'=>$action.'模块成功','url'=>session('url'),'class'=>'');
        }else{
            return array('status'=>'error','info'=>$action.'模块失败','class'=>'');
        }
    }
}