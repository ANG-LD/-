<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/20
 * Time: 18:40
 */

namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model
{
    public function auth(){
        $data = $_POST;
        if( !M()->autoCheckToken($_POST) ){
            return array('status'=>'error','info'=>'禁止站外提交！','class'=>'');
        }
        if(empty($data['name'])){
            return array('status'=>'error','info'=>'商品名不能为空','class'=>'');
        }
        if(empty($data['brand'])){
            return array('status'=>'error','info'=>'品牌名称不能为空','class'=>'');
        }
        if(empty($data['code'])){
            return array('status'=>'error','info'=>'商品编码不能为空','class'=>'');
        }
        if(empty($data['first_category'])){
            return array('status'=>'error','info'=>'请选择一级分类','class'=>'');
        }
        if(empty($data['second_category'])){
            return array('status'=>'error','info'=>'请选择二级分类','class'=>'');
        }
        if(empty($data['unit'])){
            return array('status'=>'error','info'=>'请选择计价单位','class'=>'');
        }
        if(!is_numeric($data['price'])){
            return array('status'=>'error','info'=>'商品原价错误','class'=>'');
        }
        if($data['price']<0){
            return array('status'=>'error','info'=>'商品原价错误','class'=>'price1');
        }
        if(!is_numeric($data['sale_price'])){
            return array('status'=>'error','info'=>'商品售价错误','class'=>'');
        }
        if($data['sale_price'] < 0){
            return array('status'=>'error','info'=>'商品售价错误','class'=>'');
        }
        if(!is_numeric($data['cost_price'])){
            return array('status'=>'error','info'=>'商品成本价错误','class'=>'');
        }
        if($data['cost_price'] < 0){
            return array('status'=>'error','info'=>'商品成本价错误','class'=>'');
        }
        if(empty($data['number'])){
            return array('status'=>'error','info'=>'商品库存不能为空','class'=>'');
        }
        if(!ctype_digit($data['number'])){
            return array('status'=>'error','info'=>'商品库存填写错误','class'=>'');
        }
        if(!empty($data['sale_number'])){
            if(!ctype_digit($data['sale_number'])){
                return array('status'=>'error','info'=>'购买人次填写错误','class'=>'');
            }
        }
        if(empty($data['intro'])){
            return array('status'=>'error','info'=>'商品介绍不能为空','class'=>'');
        }
//        if(!empty($data['id'])) {
//            if (empty($data['kinds1'])) {
//                return array('status' => 'error', 'info' => '商品型号名称必填', 'class' => 'param1');
//            }
//        }
//        $data['kinds_detail1'] = implode(',',$data['kinds_detail1']);
//        if(empty($data['parent_category'])){
//            return array('status'=>'error','info'=>'请选择分类','class'=>'category');
//        }
        if(empty($data['img'])){
            return array('status'=>'error','info'=>'请上传商品图片','class'=>'');
        }
        if(empty($data['thumb'])){
            return array('status'=>'error','info'=>'请上传商品缩略图','class'=>'');
        }
        $data['imgs'] = implode(',',$data['imgs']);
        $imgs = explode(',', $data['imgs']);
        foreach ($imgs as $k => $v) {
            if(!empty($v)){
                $img[] = $v;
            }
        }
        if(empty($img)){
            return array('status'=>'error','info'=>'请上传商品轮播图','class'=>'');
        }
        if(empty($data['image_text'])){
            return array('status'=>'error','info'=>'商品图文详情不能为空','class'=>'');
        }
        if(empty($data['param'])){
            return array('status'=>'error','info'=>'商品图文参数不能为空','class'=>'');
        }

        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M("Goods")->add($data);
            M('Goods')->where(['goods_id'=>$result])->save(['sort'=>$result]);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['status'] = 1;
            $result = M("Goods")->where(['goods_id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status' => 'ok', 'info' => $action . '商品记录成功!', 'url' => session('url'),'class'=>'');
        }else{
            return array('status' => 'error', 'info' => $action . '商品记录失败','class'=>'');
        }
    }
}