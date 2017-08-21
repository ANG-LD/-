<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/19
 * Time: 14:15
 */

namespace Admin\Model;
use Think\Model;
class AreaModel extends Model
{
    public function check(){
        $data = $_POST;
        empty($data['id']) ? $count = 0 : $count = 1;
        if(empty($data['jichang']))   return array('status'=>'error','info'=>'机场名称不能为空');
        if (M('Area')->where(['jichang' => $data['jichang']])->count() > $count) {
            return array('status' => 'error', 'info' => '该机场名称已存在，请重新填写昵称!');
        }
        if(empty($data['code']))   return array('status'=>'error','info'=>'机场三字码不能为空');
        if (M('Area')->where(['code' => $data['code']])->count() > $count) {
            return array('status' => 'error', 'info' => '该机场三字码已存在，请重新填写昵称!');
        }
        if(empty($data['city']))   return array('status'=>'error','info'=>'城市名称不能为空');
        if($data['type'] == '1'){
            $data['country'] = '中国';
        }else{
            if(empty($data['country']))   return array('status'=>'error','info'=>'国家或地区名称不能为空');
        }
        if(empty($data['shouzimu']))   return array('status'=>'error','info'=>'城市首字母不能为空');
        if(empty($data['id'])){
            $data['intime'] = date('Y-m-d H:i:s',time());
            $result = M('Area')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date('Y-m-d H:i:s',time());
            $result = M('Area')->where(['id' => $data['id']])->save($data);
            $action = '编辑';
        }
        if ($result) {
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        } else {
            return array('status' => 'error', 'info' => $action . '记录失败');
        }
    }
}