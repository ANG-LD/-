<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/14
 * Time: 11:08
 */

namespace Admin\Model;
use Think\Model;
class GameModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['name']))  return array('status'=>'error','info'=>'赛事名称不能为空','class'=>'');

        if(empty($data['img']))   return array('status'=>'error','info'=>'赛事图片不能为空','class'=>'');
        if(empty($data['imgs']))   return array('status'=>'error','info'=>'详情图片不能为空','class'=>'');
        //$data['imgs'] = implode(',',$data['imgs']);
        $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
        $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
        $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
        if(empty($data['province']))    return array('status'=>'error','info'=>'请填写省份','class'=>'');
        if(empty($data['city']))    return array('status'=>'error','info'=>'请填写城市','class'=>'');
        if(empty($data['area']))    return array('status'=>'error','info'=>'请填写地区','class'=>'');
        if(empty($data['address']))    return array('status'=>'error','info'=>'请填写详细地址','class'=>'');
        if(empty($data['address']))    return array('status'=>'error','info'=>'请填写详细地址','class'=>'');
        if(empty($data['start_time']))    return array('status'=>'error','info'=>'赛事开始时间不能为空','class'=>'');
        if(empty($data['end_time']))    return array('status'=>'error','info'=>'赛事结束时间不能为空','class'=>'');
        if(empty($data['dis']))    return array('status'=>'error','info'=>'赛事简介不能为空','class'=>'');
        if(empty($data['content']))    return array('status'=>'error','info'=>'购票相关不能为空','class'=>'');
        /*获取地址的经纬度*/
        $address = $data['province'].$data['city'].$data['area'].$data['address'];
        $ak = 'fbINeDE9oaw2SXIYcfdpe0Td';
        $api = "http://api.map.baidu.com/geocoder/v2/?ak={$ak}&output=json&address={$address}";
        $position = file_get_contents($api);
        $position = json_decode($position, true);
        $array = $position['result']['location'];
        $data['lng'] = "{$array['lng']}";//经度
        $data['lat'] = "{$array['lat']}";//纬度
        if(empty($data['id'])){
            $data['intime'] = date('Y-m-d H:i:s',time());
            $result = M('Game')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date('Y-m-d H:i:s',time());
            $result = M('Game')->where(['game_id' => $data['id']])->save($data);
            $action = '编辑';
        }
        if ($result) {
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        } else {
            return array('status' => 'error', 'info' => $action . '记录失败');
        }
    }
}