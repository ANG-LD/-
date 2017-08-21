<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/14
 * Time: 11:08
 */

namespace Admin\Model;
use Think\Model;
class HotelModel extends Model
{
    public function check(){
        $data = $_POST;
        if(empty($data['name']))  return array('status'=>'error','info'=>'酒店名称不能为空');

        if(empty($data['img']))   return array('status'=>'error','info'=>'酒店图片不能为空');
        if(is_array($data['imgs'])){
            $data['imgs'] = implode(',',$data['imgs']);
//            return array('status'=>'error','info'=>$data['shop_banners']);
        }else{
            return array('status'=>'error','info'=>'轮播图片不能为空');
        }
        if(empty($data['tags']))  return array('status'=>'error','info'=>'酒店标签不能为空');
        if(empty($data['star']))  return array('status'=>'error','info'=>'酒店星级不能为空');
        if(empty($data['tag_city']))  return array('status'=>'error','info'=>'城市不能为空');
        $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
        $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
        $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
        if(empty($data['province']))    return array('status'=>'error','info'=>'请填写省份');
        if(empty($data['city']))    return array('status'=>'error','info'=>'请填写城市');
        if(empty($data['area']))    return array('status'=>'error','info'=>'请填写地区');
        if(empty($data['address']))    return array('status'=>'error','info'=>'请填写详细地址');
        if(empty($data['min_price']))    return array('status'=>'error','info'=>'请填写房间最低价');
        if(empty($data['service']))    return array('status'=>'error','info'=>'请选择酒店服务'); else $data['service'] = implode(',',$data['service']);
        if(empty($data['content']))    return array('status'=>'error','info'=>'酒店详情不能为空');
        /*获取地址的经纬度*/
        $address = $data['province'].$data['city'].$data['area'].$data['address'];
        $ak = 'fbINeDE9oaw2SXIYcfdpe0Td';
        $api = "http://api.map.baidu.com/geocoder/v2/?ak={$ak}&output=json&address={$address}";
        $position = file_get_contents($api);
        $position = json_decode($position, true);
        $array = $position['result']['location'];
        $data['lng'] = "{$array['lng']}";//经度
        $data['lat'] = "{$array['lat']}";//纬度
        $data['tags_color'] = M('HotelTags')->where(['name'=>$data['tags']])->getField('color');
        if(empty($data['id'])){
            $data['intime'] = date('Y-m-d H:i:s',time());
            $result = M('Hotel')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date('Y-m-d H:i:s',time());
            $result = M('Hotel')->where(['hotel_id' => $data['id']])->save($data);
            $action = '编辑';
        }
        if ($result) {
            return array('status' => 'ok', 'info' => $action . '记录成功!', 'url' => session('url'));
        } else {
            return array('status' => 'error', 'info' => $action . '记录失败');
        }
    }
}