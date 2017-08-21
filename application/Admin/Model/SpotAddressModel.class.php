<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/1
 * Time: 19:55
 */

namespace Admin\Model;
use Think\Model;
class SpotAddressModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['name'])){
            return array('status'=>'error','info'=>'景点名称不能为空','class'=>'');
        }
        if(empty($data['img'])){
            return array('status'=>'error','info'=>'景点图片不能为空','class'=>'');
        }
        $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
        $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
        $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
        if(empty($data['province']))    return array('status'=>'error','info'=>'请填写省份','class'=>'');
        if(empty($data['city']))    return array('status'=>'error','info'=>'请填写城市','class'=>'');
        if(empty($data['area']))    return array('status'=>'error','info'=>'请填写地区','class'=>'');
        if(empty($data['address'])){
            return array('status'=>'error','info'=>'详细地址不能为空','class'=>'');
        }
        $address = $data['province'].$data['city'].$data['area'].$data['address'];
        $ak = 'fbINeDE9oaw2SXIYcfdpe0Td';
        $api = "http://api.map.baidu.com/geocoder/v2/?ak={$ak}&output=json&address={$address}";
        $position = file_get_contents($api);
        $position = json_decode($position, true);
        $array = $position['result']['location'];
        $data['lng'] = "{$array['lng']}";//经度
        $data['lat'] = "{$array['lat']}";//纬度
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('SpotAddress')->add($data);
            $action = '添加';
        }else{
            $data['uptime'] = date('Y-m-d H:i:s',time());
            $result = M('SpotAddress')->where(['id' => $data['id']])->save($data);
            $action = '编辑';
        }
        if ($result) {
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        } else {
            return array('status' => 'error', 'info' => $action . '记录失败');
        }

    }
}