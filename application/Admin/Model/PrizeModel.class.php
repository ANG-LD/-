<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/4/1
 * Time: 15:01
 */

namespace Admin\Model;
use Think\Model;
class PrizeModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['name'])){
            return array('status' => 'error', 'info' => '奖品名称不能为空','class'=>'');
        }
        if(empty($data['img'])){
            return array('status' => 'error', 'info' => '请上传图片','class'=>'');
        }
//        if($data['type'] == 1){
//            if(empty($data['goods_id'])){
//                return array('status' => 'error', 'info' => '请选择实物商品','class'=>'');
//            }
//        }
        if($data['type'] == 2){
            if(empty($data['value'])){
                return array('status' => 'error', 'info' => '请设置奖品积分','class'=>'');
            }
            if(!is_numeric($data['value'])){
                return array('status' => 'error', 'info' => '奖品积分错误','class'=>'');
            }
        }
        if($data['type'] == 3){
            unset($data['goods_id']);
            unset($data['value']);
        }
        if(!is_numeric($data['chance'])){
            if(empty($data['chance'])){
                return array('status' => 'error', 'info' => '请设置中奖基数','class'=>'');
            }
            if(!is_numeric($data['chance'])){
                return array('status' => 'error', 'info' => '中奖基数错误','class'=>'');
            }
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:S",time());
            $result = M('Prize')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Prize')->where(['prize_id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'记录成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'记录失败');
        }
    }
}