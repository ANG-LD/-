<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/3
 * Time: 14:12
 */

namespace Api\Controller;
use Admin\Controller\CommonController;
class PresellController extends CommonController
{
    public function presell_goods_list(){
        if(IS_POST){
            $id = I('id');
            if(empty($id))          error("参数错误");
            $category = M('Category')->where(['id'=>$id])->find();
            if(empty($category))    error("参数错误");
            $p   = I('p');
            empty($p)    &&   $p = '1';
            $num = 10;
            $map['a.is_del'] = 1;
            $map['b.is_del'] = 1;
            $map['a.status'] = 2;
            $map['b.type']   = 2;
            $map['b.second_category'] = $category['id'];
            $value = I('value');
            if(empty($value))   $value = 1;
//            $count = M('GoodsTailor')->alias('a')
//                ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
//                ->where($map)->count();
            switch($value){
                case 1 : //综合
                    $list = M('GoodsTailor')->alias('a')
                        ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                        ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where($map)->select();
                    break;
                case 2: //销量低到高
                    $list = M('GoodsTailor')->alias('a')
                        ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                        ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where($map)->order("b.sale_number asc")
                        ->select();
                    break;
                case 3://销量高到低
                    $list = M('GoodsTailor')->alias('a')
                        ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                        ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where($map)->order("b.sale_number desc")->select();
                    break;
                case 4://价格低到高
                    $list = M('GoodsTailor')->alias('a')
                        ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                        ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where($map)->order("a.presale_price asc")->select();
                    break;
                case 5://价格高到底
                    $list = M('GoodsTailor')->alias('a')
                        ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                        ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where($map)->order("a.presale_price desc")->select();
                    break;
                case 6:
                    $start_price = I('start_price');
//                    if(empty($start_price))     error("缺少价格起始值");
                    $end_price = I('end_price');
                    if(empty($end_price)){
                        $map['a.presale_price'] = ['gt',$start_price];
                        $count = M('GoodsTailor')->alias('a')
                            ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                            ->where($map)->count();
                        $list = M('GoodsTailor')->alias('a')
                            ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                            ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                            ->where($map)->order("a.presale_price asc")->select();
                    }else{
                        $map['a.presale_price'] = ['between',[$start_price,$end_price]];
                        $count = M('GoodsTailor')->alias('a')
                            ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                            ->where($map)->count();
                        $list = M('GoodsTailor')->alias('a')
                            ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price')
                            ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                            ->where($map)->order("a.presale_price asc")->select();
                    }
            }
            foreach($list as $k=>$v){
                if(strtotime($v['end_time'])-time()<0){
                    unset($list[$k]);
                }
            }
            $list = array_values($list);
            $count = count($list);
            $page = ceil($count/5);
            $data = array_slice($list,($p-1)*$num,$num);
            success(['page'=>$page,'category'=>$category,'list'=>$data]);
        }
    }

    /**
     *@预售商品基础信息
     */
    public function goods_info(){
        $id = I('tailor_id');
        $uid = I('uid');
        if (empty($id)) error("商品id不能为空");
        $goods = M('GoodsTailor')->alias('a')
            ->field('a.tailor_id,a.goods_id,b.name,a.presale_price,a.min_count,a.pay_count,a.end_time,b.thumb,b.img,b.price,
            b.imgs,b.image_text,b.param,b.number,b.kinds1,b.kinds2')
            ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
            ->where(['a.tailor_id'=>$id])->find();
        $goods['end_time'] = date("Y/m/d H:i:s",strtotime($goods['end_time']));
        if (empty($goods)) error("商品不存在");
        $imgs = explode(',', $goods['imgs']);
        $kinds1 = M('GoodsKinds')->where(['goods_id'=>$goods['goods_id'],'type'=>'1','is_del'=>'1'])->select();
        if(!empty($kinds1)){
            $goods['kinds_detail'][0]['kind'] = $goods['kinds1'];
            $goods['kinds_detail'][0]['kind_detail'] = $kinds1;
        }

        $kinds2 = M('GoodsKinds')->where(['goods_id'=>$goods['goods_id'],'type'=>'2','is_del'=>'1'])->select();
        if(!empty($kinds2)){
            $goods['kinds_detail'][1]['kind'] = $goods['kinds2'];
            $goods['kinds_detail'][1]['kind_detail'] = $kinds2;
        }
        if(empty($kinds1) && empty($kinds2)){
            $goods['kinds_detail'] = [];
        }
        foreach ($imgs as $k => $v) {
            if(!empty($v)){
                $img[]['img'] = $v;
            }
        }
        $goods['imgs'] = $img;
//        $goods['image_text'] = htmlspecialchars($goods['image_text']);
//        $goods['param'] = htmlspecialchars($goods['param']);
        /*商品和物流评分*/
        $where['type']    = 1;
        $where['object_id'] = $goods['goods_id'];
        $count = M('Comment')->where($where)->count();
        if(empty($count)){
            $goods['goods_mark']    = 5;
            $goods['express_mark']    = 5;
            $goods['send_mark']    = 5;
        }else{
            $goods_mark = M('Comment')->where($where)->sum('goods_mark');
            $express_mark = M('Comment')->where($where)->sum('express_mark');
            $send_mark = M('Comment')->where($where)->sum('send_mark');
            $goods['goods_mark']    = (int)(($goods_mark + 5)/($count+1));
            $goods['express_mark']  = (int)(($express_mark + 5)/($count+1));
            $goods['send_mark']     = (int)(($send_mark + 5)/($count+1));
        }
        $goods['together_mark'] = sprintf("%.1f",($goods_mark+$express_mark+$send_mark+15)/(($count+1)*3));
        $comment = M('Comment')->alias('a')
            ->field('a.comment_id,a.content,a.intime,b.member_id,b.nickname,b.img,a.img as comment_img,a.thumb')
            ->join("INNER JOIN __MEMBER__ b on a.member_id = b.member_id")
            ->where(['a.type'=>'1','a.object_id'=>$goods['goods_id']])->limit(1)
            ->order("a.intime desc")->select();
        foreach($comment as $k=>$v){
            $comment[$k]['comment_img'] = explode(',',$v['comment_img']);
            $comment[$k]['thumb'] = explode(',',$v['thumb']);
        }
        $goods['comment']   =   $comment;
        $goods['is_collect'] = 1;
        /*检测是否收藏*/
        if(!empty($uid)){
            $map['type'] = 1;
            $map['member_id'] = $uid;
            $map['goods_id']    = $goods['goods_id'];
            $check = M('Collection')->where($map)->find();
            if($check){
                $goods['is_collect']  = 2;
            }

        }
        success($goods);
    }

    /**
     *@加入购物车
     */
    public function to_goods_cart(){
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            if (empty($data['tailor_id'])) error("购买商品不能为空");
            $goods = M('GoodsTailor')->where(['tailor_id'=>$data['tailor_id'],'is_del'=>'1','status'=>'2'])->find();
            if(!$goods)                   error("商品已下架");
            if (empty($data['number'])) $data['number'] = '1';
            if (!ctype_digit($data['number'])) error("请填写正确的商品数量");
            $kinds = explode(',',$data['kinds_id']);
            $data['mid'] = $member['member_id'];
            $data['goods_id'] = $data['tailor_id'];
            $data['intime'] = date("Y-m-d H:i:s", time());
            $data['kinds_id'] = $kinds[0];
            $data['kinds_id2'] = $kinds[1];
            $map['goods_id'] = $data['tailor_id'];
            $map['mid'] = $member['member_id'];
            $map['kinds_id'] = $kinds[0];
            $map['kinds_id2'] = $kinds[1];
            $check = M('GoodCart')->where($map)->find();
            if ($check) {
                $result = M('GoodCart')->where($map)->save(['number' => $check['number'] + $data['number']]);
            } else {
                $result = M('GoodCart')->add($data);
            }
            if ($result) {
                success("添加成功");
            } else {
                error("添加失败");
            }
        }
    }

    /**
     *@购物车中的商品
     */
    public function goods_cart()
    {
        if (IS_POST) {
            $member = checklogin();
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,a.kinds_id,a.kinds_id2,
                c.name,c.thumb,c.img,b.goods_id,c.price,c.number as stock,c.kinds1,c.kinds2")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'],'a.type'=>'1'])->order("a.intime asc")->select();
            $price = '';
            $has_invalid = 2;
            $is_all_check = 2;
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if(strtotime($v['end_time']) - time() < 0){       //判断商品是否在有效期
                        $list[$k]['invalid'] = 1;
                        $has_invalid = 1;
                    }else{
                        $list[$k]['invalid'] = 2;
                        if ($v['is_check'] == '2') {
                            $price += $v['presale_price'] * $v['number'];
                            $price = sprintf("%.2f",$price);
                        }else{
                            $is_all_check = 1;
                        }
                    }
                    if (!empty($v['kinds_id'])) {
                        $kinds1 = M('GoodsKinds')->where(['kind_id' => $v['kinds_id']])->find();
                        $list[$k]['kinds_detail1'][0]['kind_detail'] = $kinds1['kinds_detail'];
                        $list[$k]['kinds_detail1'][0]['kind'] = $v['kinds1'];
                    }
                    if (!empty($v['kinds_id2'])) {
                        $kinds2 = M('GoodsKinds')->where(['kind_id' => $v['kinds_id2']])->find();
                        $list[$k]['kinds_detail1'][1]['kind_detail'] = $kinds2['kinds_detail'];
                        $list[$k]['kinds_detail1'][1]['kind'] = $v['kinds2'];
                    }
                    if(empty($v['kinds_id']) && empty($v['kinds_id2'])){
                        $list[$k]['kinds_detail1'] = [];
                    }
                }
               $list =  wpjam_array_multisort($list, 'invalid', $order = SORT_DESC, $sort_flags = SORT_NUMERIC);
            }
            success(['price' => $price, 'list' => $list,'has_invalid'=>$has_invalid,'is_all_check'=>$is_all_check]);
        }
    }

    /**
     *@清除无效的商品
     */
    public function del_invalid_goods(){
        $member = checklogin();
        $list = M('GoodCart')->alias('a')
            ->field("a.id,b.end_time")
            ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
            ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
            ->where(['a.mid' => $member['member_id'],'a.type'=>'1'])->order("a.intime asc")->select();
        if(empty($list))        error("购物车没有数据");
        $price = '';
        foreach ($list as $k => $v) {
            if(strtotime($v['end_time']) - time() < 0){
                $ids[] = $v['id'];
            }
        }
        if(empty($ids))         error("没有无效商品");
        $result = M('GoodCart')->where(['id'=>['in',$ids]])->delete();
        if($result){
            success("操作成功");
        }else{
            error("操作失败");
        }
    }

    /**
     *@商品数量加一
     */
    public function plus_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('GoodCart')->where(['id' => $id])->find();
            if (!$check) error("参数错误");
            $tailor = M('GoodsTailor')->where(['tailor_id'=>$check['goods_id']])->find();
            $goods = M('Goods')->where(['goods_id'=>$tailor['goods_id']])->find();
            if($check['number'] + 1 >$goods['number']) error("商品库存不足");
            $result = M('GoodCart')->where(['id' => $id])->setInc("number");
            if (!$result) error("操作失败");
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
                c.name,c.thumb,c.img,b.goods_id")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'],'a.type'=>'1'])->order("a.intime asc")->select();
            $price = '';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if(strtotime($v['end_time']) - time() < 0){
                        $list[$k]['invalid'] = 1;
                    }else{
                        if ($v['is_check'] == '2') {
                            $price += $v['presale_price'] * $v['number'];
                            $price = sprintf("%.2f",$price);
                        }
                    }
                }
                success(['price' => $price]);
            } else {
                error("购物车中没有数据");
            }
        }
    }

    /**
     *@ 商品数量减
     */
    public function minus_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('GoodCart')->where(['id' => $id])->find();
            if (!$check) error("参数错误");
            if($check['number']>1){
                $result = M('GoodCart')->where(['id' => $id])->setDec("number");
            }else{
                error("已到最小值");
            }
            if (!$result) error("操作失败");
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
                c.name,c.thumb,c.img,b.goods_id")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'],'a.type'=>'1'])->order("a.intime asc")->select();
            $price = '';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if(strtotime($v['end_time']) - time() < 0){
                        $list[$k]['invalid'] = 1;
                    }else{
                        if ($v['is_check'] == '2') {
                            $price += $v['presale_price'] * $v['number'];
                            $price = sprintf("%.2f",$price);
                        }
                    }
                }
                success(['price' => $price]);
            } else {
                success("购物车中没有数据");
            }
        }
    }

    /**
     *@购物车中商品切换默认操作
     */
    public function set_default_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('GoodCart')->where(['id' => $id])->find();
            if (!$check) error("参数错误");
            if ($check['is_check'] == 1) {
                M('GoodCart')->where(['id' => $id])->save(['is_check' => 2]);
            } else {
                M('GoodCart')->where(['id' => $id])->save(['is_check' => 1]);
            }

            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
                c.name,c.thumb,c.img,b.goods_id")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'],'a.type'=>'1'])->order("a.intime asc")->select();
            $price = '';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if(strtotime($v['end_time']) - time() < 0){
                        $list[$k]['invalid'] = 1;
                    }else{
                        if ($v['is_check'] == '2') {
                            $price += $v['presale_price'] * $v['number'];
                            $price = sprintf("%.2f",$price);
                        }
                    }
                }
                success(['price' => $price]);
            } else {
                error("购物车中没有数据");
            }
        }
    }

    /**
     *@购物车中全选 or 全不选
     */
    public function choose_all_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $is_check = I('is_check');
            if (empty($is_check)) error("参数错误");
            $result = M('GoodCart')->where(['user_id' => $member['user_id']])->save(['is_check' => $is_check]);
            if ($result) {
                $list = M('GoodCart')->alias('a')
                    ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
                c.name,c.thumb,c.img,b.goods_id")
                    ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                    ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                    ->where(['a.mid' => $member['member_id'],'a.type'=>'1'])->order("a.intime asc")->select();
                $price = '';
                if (!empty($list)) {
                    foreach ($list as $k => $v) {
                        if(strtotime($v['end_time']) - time() < 0){
                            $list[$k]['invalid'] = 1;
                        }else{
                            if ($v['is_check'] == '2') {
                                $price += $v['presale_price'] * $v['number'];
                                $price = sprintf("%.2f",$price);
                            }
                        }
                    }
                    success(['price' => $price]);
                } else {
                    error("操作失败");
                }
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@购物车中商品删除
     */
    public function del_goods_cart(){
        if(IS_POST){
            $member = checklogin();
            $check = M('GoodCart')->where(['is_check'=>'2','mid'=>$member['member_id']])->select();
            if(empty($check)){
                error("请先选择商品");
            }
            $result = M('GoodCart')->where(['is_check'=>'2','mid'=>$member['member_id']])->delete();
            if($result){
                success("删除成功");
            }else{
                error("删除失败");
            }
        }
    }

    /**
     *@购物车中商品提交确认订单
     */
    public function refer_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
                c.name,c.thumb,c.img,b.goods_id")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'],'a.type'=>'1', 'a.is_check' => '2','b.is_del'=>'1','b.status'=>'2'])->select();
            empty($list)   ?  error("请先选择商品")  : success("ok");
        }

    }

    /**
     *@单件商品直接写入订单
     */
    public function set_goods_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $tailor_id = I('tailor_id');
            if (empty($tailor_id)) error("商品不能为空");
            $tailor = M('GoodsTailor')->where(['tailor_id' => $tailor_id])->find();
            if (empty($tailor)) error("商品错误");
            $code['number'] = I('number');
            $code['goods_id'] = $tailor_id;
            if (empty($code['number']))     $code['number'] = 1;
            $kinds_id = I('kinds_id');
            $kinds_id = explode(',',$kinds_id);
            $code['mid'] = $member['member_id'];
            $code['kinds_id'] = $kinds_id[0];
            $code['kinds_id2'] = $kinds_id[1];
            $code['intime'] = date("Y-m-d H:i:s",time());
            $map['mid'] = $member['member_id'];
            $map['goods_id'] = $tailor_id;
            $map['kinds_id'] = $kinds_id[0];
            $map['kinds_id2'] = $kinds_id[1];
            $check = M('GoodCart')->where($map)->find();
            $result = M('GoodCart')->add($code);
            if($result){
                M('GoodCart')->where(['mid'=>$member['member_id']])->save(['is_check'=>'1']);
                M('GoodCart')->where(['id'=>$result])->save(['is_check'=>'2']);
                if($check){
                    M('GoodCart')->where(['id'=>$check['id']])->delete();
                }
                success("提交成功");
            }else{
                error("提交失败");
            }
        }
    }

    /**
     *@预售确认订单
     */
    public function confirm_info()
    {
        if (IS_POST) {
            $member = checklogin();
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
            c.name,c.thumb,c.img,b.goods_id,a.kinds_id,a.kinds_id2,c.kinds1,c.kinds2")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'], 'a.type' => '1', 'a.is_check' => '2'])->order("a.intime asc")->select();
            if (empty($list)) error("商品不能为空");
            $amount = '';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (strtotime($v['end_time']) - time() < 0) {
                        $list[$k]['invalid'] = 1;
                    } else {
                        if ($v['is_check'] == '2') {
                            $amount += $v['presale_price'] * $v['number'];
                            $amount = sprintf("%.2f", $amount);
                        }
                    }

                    if (!empty($v['kinds_id'])) {
                        $kinds1 = M('GoodsKinds')->where(['kind_id' => $v['kinds_id']])->find();
                        $list[$k]['kinds_detail1'][0]['kind_detail'] = $kinds1['kinds_detail'];
                        $list[$k]['kinds_detail1'][0]['kind'] = $v['kinds1'];
                    }
                    if (!empty($v['kinds_id2'])) {
                        $kinds2 = M('GoodsKinds')->where(['kind_id' => $v['kinds_id2']])->find();
                        $list[$k]['kinds_detail1'][1]['kind_detail'] = $kinds2['kinds_detail'];
                        $list[$k]['kinds_detail1'][1]['kind'] = $v['kinds2'];
                    }
                    if(empty($v['kinds_id']) && empty($v['kinds_id2'])){
                        $list[$k]['kinds_detail1'] = [];
                    }
                }
                $aid = I('address_id');
                if (empty($aid)) {
                    $address = M('AcceptorAddress')->where(['mid' => $member['member_id'], 'is_default' => '2'])->find();
                } else {
                    $type = I('type');
                    if ($type == 1) {
                        $address = M('SpotAddress')->where(['id' => $aid])->find();
                    } else {
                        $address = M("AcceptorAddress")->where(['id' => $aid])->find();
                    }
                }
                $arr = '';
                if (!empty($address)) {
                    empty($address['name']) ? $arr['name'] = $member['nickname'] : $arr['name'] = $address['name'];
                    empty($address['phone']) ? $arr['phone'] = $member['phone'] : $arr['phone'] = $address['phone'];
                    empty($address['postage']) ? $arr['postage'] = '0' : $arr['postage'] = $address['postage'];
                    $arr['address'] = $address['province'] . $address['city'] . $address['area'] . $address['street'] . $address['address'];
                }
                $install_score = M('InstallScore')->where(['id' => 1])->find();
                if ($member['score'] + $member['amount'] < $install_score['score']) {
                    $score = [];
                } else {
                    $int = (int)(($member['score'] + $member['amount']) / $install_score['score']);
                    if ($int * $install_score['money'] < $amount * 0.2) {
                        $score['member_score'] = $member['score'] + $member['amount'];
                        $score['score'] = $int * $install_score['score'];
                        $score['money'] = $int * $install_score['money'];
                    } else {
                        $int = ceil(($amount * 0.2) / $install_score['money']);
                        $score['member_score'] = $member['score'] + $member['amount'];
                        $score['score'] = $int * $install_score['score'];
                        $score['money'] = $int * $install_score['money'];
                    }
                }
                success(['address' => $arr, 'goods' => $list, 'score' => $score, 'amount' => $amount]);
            }
        }
    }


    /**
     *@写入预售订制订单
     */
    public function set_tailor_order(){
        if(IS_POST){
            $member = checklogin();
            $data   = $_POST;
//            if($data['is_agree'] != 1)           error("协议尚未同意");
//            if(empty($data['pattern']))     error("商品数量不能为空");
            if(empty($data['name']))        error("收件人不能为空");
            if(empty($data['phone']))       error("联系方式不能为空");
            if(empty($data['address']))     error("详细地址不能为空");
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id as tailor_id,a.number,a.is_check,b.presale_price,b.end_time,
            c.name,c.thumb,c.img,b.goods_id,a.kinds_id,a.kinds_id2,c.kinds1,c.kinds2")
                ->join("INNER JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                ->join("INNER JOIN __GOODS__ c on b.goods_id = c.goods_id")
                ->where(['a.mid' => $member['member_id'],'a.type'=>'1','a.is_check'=>'2'])->order("a.intime asc")->select();
            if(empty($list))       error("商品不能为空");
            $amount = '';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if(strtotime($v['end_time']) - time() < 0){
                        $list[$k]['invalid'] = 1;
                    }else{
                        if ($v['is_check'] == '2') {
                            $amount += $v['presale_price'] * $v['number'];
                        }
                    }
                }
            }
            $data['amount'] = $amount;
            if(!empty($data['score'])){
                if($data['score']>$member['score'] + $member['amount'])   error("积分不够，不能使用积分"); //查询积分
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                if($member['score'] + $member['amount'] < $install_score['score'])    error("积分不够，不能使用积分"); //查询积分
                $ratio = $install_score['money']/$install_score['score'];
            }
            $data['deduction'] = $data['score'] * $ratio;
            $data['paid'] = $data['amount'] - $data['deduction'];
            $data['order_no'] = date("YmdHis").rand(100000,999999);
            $data['intime'] = date("Y-m-d H:i:s");
            $data['mid'] = $member['member_id'];
            $data['type'] = '2';
            $data['state'] = '1';
            $mall_order  = M('MallOrder');
            $mall_order -> startTrans();
            $result = $mall_order -> add($data);
            if($result){
                if(!empty($data['score'])){
                    if($member['score']<$data['score']){    //判断获取的积分是否足够
                        $tag['score'] = 0;
                        $tag['amount'] = $member['amount'] + $member['score'] -$data['score'];
                    }else{
                        $tag['score'] = $member['score'] - $data['score']; //用户现有积分
                    }
                    $re = M('Member')->where(['member_id' => $member['member_id']])->save($tag);
                    if(!$re){
                        $mall_order -> rollback();
                    }
                }
                $code['order_id'] = $result;
                $code['intime'] = date("Y-m-d H:i:s", time());
                foreach($list as $k=>$v){
                    $code['goods_id'] = $v['tailor_id'];
                    $code['kinds_id'] = $v['kinds_id'];
                    $code['kinds_id2'] = $v['kinds_id2'];
                    $re = M('MallOrderDetail')->add($code);
                    if(!$re){
                        $mall_order -> rollback();
                    }
                }
                $result = M('GoodCart')->where(['mid'=>$member['member_id'],'is_check'=>'2'])->delete();
                if(!$result){
                    $mall_order -> rollback();
                }else{
                    $mall_order -> commit();
                }
            }else{
                $mall_order -> rollback();
            }
            success(['order_no' => $data['order_no']]);
        }
    }

    /**
     *@猜你喜欢
     */
    public function maybe_enjoy(){
        if(IS_POST){
            $member = checklogin();
            $cart = M('GoodCart')->where(['mid'=>$member['member_id']])->select();
            foreach($cart as $k=>$v){
                $goods_id[] = $v['goods_id'];
            }
            !empty($goods_id)   &&  $map['a.tailor_id'] = ['not in',$goods_id];
            $tailor = M('GoodsTailor')->alias('a')
                    ->field("a.tailor_id,a.presale_price,a.end_time,b.name,b.thumb,b.img,b.goods_id,b.price")
                    ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->where($map)->order('rand()')->select();
            foreach($tailor as $k=>$v){
                if(strtotime($v['end_time']) - time()<0){
                    unset($tailor[$k]);
                }
            }
            $tailor = array_values($tailor);
            $list = array_slice($tailor,0,2);
            success($list);
        }
    }

}