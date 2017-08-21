<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/1/9
 * Time: 9:52
 */

namespace Api\Controller;


use Think\Upload;

class OrderController extends CommonController
{
    /**
     *@所有商品订单
     */
    public function mall_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['mid'] = $member['member_id'];
            $map['is_del'] = '1';
            $map['state'] = ['in', ['1', '2', '3', '4']];
            $state = I('state');
            if($state != 5){
                !empty($state) && $map['state'] = I('state');
            }else{
                $map['state'] = ['in',['5','7']];
            }
            $map['address'] = ['neq', ''];
            $p = I('p');
            empty($p) && $p = 1;
            $num = 5;
            $count = M('MallOrder')
                ->where($map)->count();
            $page = ceil($count / $num);
            $list = M('MallOrder')->field('id,order_no,paid,phone,deduction,score,state,type')
                ->where($map)->limit(($p - 1) * $num, $num)->order("intime desc")->select();
            foreach ($list as $k => $v) {
                switch($v['type']){
                    case 1:
                        $order_detail = M("MallOrderDetail")->alias('a')
                            ->field('a.id,a.order_id,a.number,a.goods_id,b.name,b.sale_price,b.price,b.img,b.thumb')
                            ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                            ->where(['a.order_id' => $v['id']])->select();
                        break;
                    case 2:
                        $order_detail = M("MallOrderDetail")->alias('a')
                            ->field('a.id,a.order_id,a.number,a.goods_id,c.name,c.price,c.img,c.thumb,b.presale_price as sale_price')
                            ->join("LEFT JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                            ->join("LEFT JOIN __GOODS__ c on b.goods_id = c.goods_id")
                            ->where(['a.order_id' => $v['id']])->select();
                        break;
                    case 3:
                        $order_detail = M("MallOrderDetail")->alias('a')
                            ->field('a.id,a.order_id,a.number,a.goods_id,b.name,b.price,c.img,c.img as thumb,b.price as sale_price')
                            ->join("LEFT JOIN __GAME_PRICE__ b on a.goods_id = b.price_id")
                            ->join("LEFT JOIN __GAME__ c on b.game_id = c.game_id")
                            ->where(['a.order_id'=>$v['id']])->select();
                        break;
                }
                $number = 0;
                foreach ($order_detail as $key => $val) {
                    $number += $val['number'];
                }
                if($map['state'] == 5){
                    foreach($order_detail as $key=>$val){
                        $check = M('MallReturnsOrder')->where(['order_id'=>$val['order_id'],'goods_id'=>$val['goods_id'],'is_del'=>'1'])->find();
                        if(!empty($check)){
                            if(empty($check['kuaidi'])){
                                if(time()-strtotime($check['intime'])>7*24*3600){
                                    $order_detail[$key]['returns_status'] = 1;
                                }else{
                                    $order_detail[$key]['returns_status'] = 2;
                                }
                            }else{
                                $order_detail[$key]['returns_status'] = 3;
                            }
                        }else{
                            $order_detail[$key]['returns_status'] = 1;
                        }
                        $order_detail[$key]['returns_amount'] = $check['amount'];
                    }
                }
                $list[$k]['order_detail'] = $order_detail;
                $list[$k]['item'] = $number;
            }
            success(['list' => $list, 'page' => $page]);

        }
    }

    /**
     *@订单详情
     */
    public function order_detail()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $IMG_PREFIX = C('IMG_PREFIX');
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,
            kuaidi_state,postage,has_postage,type,deduction,score')
                ->where($map)->find();
            switch($order['type']){
                case 1:
                    $order_detail = M("MallOrderDetail")->alias('a')
                        ->field('a.id,a.order_id,a.number,a.goods_id,b.name,b.sale_price,b.price,b.img,b.thumb,
                        a.kinds_id,a.kinds_id2,b.kinds1,b.kinds2')
                        ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where(['a.order_id' => $order['id']])->select();
                    break;
                case 2:
                    $order_detail = M("MallOrderDetail")->alias('a')
                        ->field('a.id,a.order_id,a.number,a.goods_id,c.name,c.price,c.img,c.thumb,b.presale_price as sale_price,
                        a.kinds_id,a.kinds_id2,c.kinds1,c.kinds2')
                        ->join("LEFT JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                        ->join("LEFT JOIN __GOODS__ c on b.goods_id = c.goods_id")
                        ->where(['a.order_id' => $order['id']])->select();
                    break;
                case 3:
                    $order_detail = M("MallOrderDetail")->alias('a')
                        ->field('a.id,a.order_id,a.number,a.goods_id,b.name,b.price,c.img,c.img as thumb,b.price as sale_price')
                        ->join("LEFT JOIN __GAME_PRICE__ b on a.goods_id = b.price_id")
                        ->join("LEFT JOIN __GAME__ c on b.game_id = c.game_id")
                        ->where(['a.order_id'=>$order['id']])->select();
                    break;
            }
            foreach($order_detail as $k=>$v){
                if (!empty($v['kinds_id'])) {
                    $kinds1 = M('GoodsKinds')->where(['kind_id' => $v['kinds_id']])->find();
                    $order_detail[$k]['kinds_detail1'][0]['kind_detail'] = $kinds1['kinds_detail'];
                    $order_detail[$k]['kinds_detail1'][0]['kind'] = $v['kinds1'];
                }
                if (!empty($v['kinds_id2'])) {
                    $kinds2 = M('GoodsKinds')->where(['kind_id' => $v['kinds_id2']])->find();
                    $order_detail[$k]['kinds_detail1'][1]['kind_detail'] = $kinds2['kinds_detail'];
                    $order_detail[$k]['kinds_detail1'][1]['kind'] = $v['kinds2'];
                }
                if(empty($v['kinds_id']) && empty($v['kinds_id2'])){
                    $order_detail[$k]['kinds_detail1'] = [];
                }
            }
            if($order['state'] == '5'){
                foreach($order_detail as $k=>$v){
                    $check = M('MallReturnsOrder')->where(['order_id'=>$v['order_id'],'goods_id'=>$v['goods_id'],'is_del'=>'1'])->find();
                    if(!empty($check)){
                        if(empty($check['kuaidi'])){
                            if(time()-strtotime($check['intime'])>7*24*3600){
                                $order_detail[$k]['returns_status'] = 1;
                            }else{
                                $order_detail[$k]['returns_status'] = 2;
                            }
                        }else{
                            $order_detail[$k]['returns_status'] = 3;
                        }
                    }else{
                        $order_detail[$k]['returns_status'] = 1;
                    }
                }
                }
            $order['order_detail'] = $order_detail;
        }

        success($order);
    }

    /**
     * @取消订单
     */
    public function cancel_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state,score')
                ->where($map)->find();
            $order['state'] != '1' ? error("该状态下无法操作") : true;
            $result = M('MallOrder')->where(['id' => $order['id']])->save(['state' => '6']);
            if ($result) {
                if (!empty($order['score'])) {
                    M('Member')->where(['member_id' => $member['member_id']])->setInc("score", $order['score']);
                }
                success("操作成功");
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@删除订单
     */
    public function del_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state')
                ->where($map)->find();
            $order['state'] = ['in', ['4', '5']] ? error("该状态下无法操作") : true;
            $result = M('MallOrder')->where(['id' => $order['id']])->save(['is_del' => '2']);
            if ($result) {
                success("操作成功");
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@确认收货
     */
    public function receive_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state,type')
                ->where($map)->find();
            $order['state'] != '3' ? error("该状态下无法操作") : true;
            $result = M('MallOrder')->where(['id' => $order['id']])->save(['state' => '4', 'kuaidi_state' => '4']);
            if ($result) {
                if(in_array($order['type'],['1','2'])){
                    $order_detail = M('MallOrderDetail')->where(['order_id'=>$order['id']])->select();
                    foreach($order_detail as $k=>$v){
                        M('Goods')->where(['goods_id'=>$v['goods_id']])->setInc('sale_number',$v['number']);
                        if(!empty($v['kinds_id'])||!empty($v['kinds_id2'])){
                            $kinds = $v['kinds_id'].','.$v['kinds_id2'];
                            M('GoodsStock')->where(['goods_id'=>$v['goods_id'],'kinds'=>$kinds])->setInc('sale_number',$v['number']);
                        }
                    }

                }
                success("操作成功");
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@催单
     */
    public function hurry_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            $type = I('type');
            empty($type)    &&  $type = 1;
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state')
                ->where($map)->find();
            $order['state'] != '2' ? error("该状态下无法操作") : true;
            $check = M('MallOrderHurry')
                ->where(['mid' => $member['member_id'], 'order_id' => $order['id'],'type'=>$type])
                ->order("intime desc")->limit(1)->find();
            if (time() - strtotime($check['intime']) < 3600) {
                error("催单过于频繁");
            } else {
                $data['mid'] = $member['member_id'];
                $data['order_id'] = $order['id'];
                $data['intime'] = date("Y-m-d H:i:s", time());
                $data['type'] = $type;
                $result = M('MallOrderHurry')->add($data);
                if ($result) {
                    success("操作成功");
                } else {
                    error("催单失败");
                }
            }
        }
    }

    /**
     *@商品评价的商品显示
     */
    public function comment_goods_view()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,
            state,remark,kuaidi,kuaidi_name,kuaidi_state,type')
                ->where($map)->find();
            $order['state'] != '4' ? error("该状态下无法操作") : true;
            if ($order['type'] == 1) {
                $order_detail = M("MallOrderDetail")->alias('a')
                    ->field('a.id,a.order_id,a.number,a.goods_id,b.name,b.sale_price,b.price,b.img,b.thumb')
                    ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->group("b.goods_id")
                    ->where(['a.order_id' => $order['id']])->select();
            } else {
                $order_detail = M("MallOrderDetail")->alias('a')
                    ->field('a.id,a.order_id,a.number,a.goods_id,c.name,c.price,c.img,c.thumb,b.presale_price as sale_price')
                    ->join("LEFT JOIN __GOODS_TAILOR__ b on a.goods_id = b.tailor_id")
                    ->join("LEFT JOIN __GOODS__ c on b.goods_id = c.goods_id")
                    ->group("c.goods_id")->where(['a.order_id' => $order['id']])->select();
            }
            success($order_detail);

        }
    }

    /**
     *@商品评价
     */
    public function comment_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            $data['member_id'] = $member['member_id'];
            if(empty($data['goods_id']))        error("商品id不能为空");
            if(empty($data['order_id']))        error("订单id不能为空");
            $order = M('MallOrder')->where(['id'=>$data['order_id']])->find();
            if(!$order)                         error("订单错误");
//            $data['img'] = join(',',$data['img']);
//            $data['thumb'] = join(',',$data['thumb']);
            if(empty($data['content'])){
                $mark = ($data['goods_mark'] + $data['express_mark'] + $data['send_mark'])/5;
                if($mark>3){
                    $data['content'] = '好评!好评!';
                }else{
                    $data['content'] = '中评';
                }
            }
            $data['object_id'] = $data['goods_id'];
            $data['intime'] = date("Y-m-d H:i:s", time());
            $data['type'] = $order['type'];
            $result = M('Comment')->add($data);
            if($result){
                M('MallOrder')->where(['id' => $data['order_id']])->save(['state' => '5']);
                success("评论成功");
            }else{
                error("评价失败");
            }
        }
    }

    /**
     *@退货按积分比例退钱
     */
    public function check_goods(){
        if(IS_POST){
            $member = checklogin();
            $order_no = I('order_no');
            $goods_id = I('goods_id');
            $order = M('MallOrder')->where(['order_no'=>$order_no])->find();
            $count = I('count');
            empty($count)       &&      $count = 1;
            if(!$order)                 error("参数错误");
            if(!empty($order['deduction'])){
                $a = ($order['amount'] - $order['deduction'])/$order['amount'];
            }else{
                $a = 1;
            }
            switch($order['type']){
                case 1 :
                    $goods = M('Goods')->where(['goods_id'=>$goods_id])->find();
                    $money = sprintf("%.2f", $goods['sale_price'] * $count * $a);
                    break;
                case 2 :
                    $goods = M('GoodsTailor')->where(['tailor_id'=>$goods_id])->find();
                    $money = sprintf("%.2f", $goods['presale_price'] * $count * $a);
                    break;
                case 3 :
                    $goods = M('GamePrice')->where(['price_id'=>$goods_id])->find();
                    $money = sprintf("%.2f", $goods['price'] * $count * $a);
                    break;
            }
            success($money);
        }
    }

    /**
     *@退换货申请
     */
    public function returns_goods()
    {
        if (IS_POST) {
            function add($data){
                $result = M('MallReturnsOrder')->add($data);
                return $result;
            }

            function save($data,$id){
                $result = M('MallReturnsOrder')->where(['id'=>$id])->save($data);
                return $result;
            }
            $member = checklogin();
            $data = $_POST;
            $card = M('BankCard')
                ->where(['member_id'=>$member['member_id'],'type'=>'1','is_default'=>'1'])->find();
            if(empty($card))   error("请先绑定退款账号");
            $map['order_no'] = I('order_no');
            empty($map['order_no']) || empty($data['type']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state')
                ->where($map)->find();
            !in_array($order['state'], array('4', '5')) ? error("该状态下无法操作") : true;
            if(empty($data['goods_id']))    error("商品不能为空");
            if(empty($data['reason']))      error("原因不能为空");
            if(empty($data['img']))         error("凭证图片不能为空");
            if(empty($data['count']))       error("商品数量不能为空");
            if($data['type'] == 1){
                if(empty($data['amount']))  error("退货金额不能为空");
            }
            $data['bank_card'] = $card['bank_card'];
            $data['bank_username'] = $card['realname'];
            $data['pay_type'] = $card['pay_type'];
            $data['mid'] = $member['member_id'];
            $data['order_id'] = $order['id'];
            $check = M('MallReturnsOrder')
                ->where(['mid' => $member['member_id'], 'order_id' => $order['id'],'goods_id'=>$data['goods_id'],'is_del'=>'1'])
                ->order("intime desc")->limit(1)->find();
            if($check){
                if (time() - strtotime($check['intime']) > 7*24*3600) {
                    M('MallReturnsOrder')->where(['id'=>$check['id']])->save(['is_del'=>'2']);
                    $data['intime'] = date("Y-m-d H:i:s", time());
                    $result = add($data);
                }else{
                    $data['uptime'] = date("Y-m-d H:i:s",time());
                    $result = save($data,$check['id']);
                }
            }else{
                $data['intime'] = date("Y-m-d H:i:s", time());
                $result = add($data);
            }
            if ($result) {
                M('MallOrder')->where(['id'=>$order['id']])->save(['state'=>'7']);
                success("操作成功");
            } else {
                error("操作失败");
            }
        }

    }

    /**
     *@退换货时间查询
     */
    public function check_returns_time(){
        if(IS_POST){
            $order_no = I('order_no');
            $goods_id = I('goods_id');
            if(!$order_no || !$goods_id)            error("参数错误");
            $order = M('MallOrder')->where(['order_no'=>$order_no])->find();
            $result = M('MallReturnsOrder')->where(['order_id'=>$order['id'],'goods_id'=>$goods_id,'is_del'=>'1'])->find();
            if(!$result)                            error("记录没有好到");
            $date = strtotime($result['intime']);
            $long = time()-$date;
            if($long/24/3600 > 7){
                error("时间已过期");
            }
            $long = 7*24*3600 - $long;
            if($long > 24*3600){
                $day = (int)($long /24/3600);
                $hour = (int)(($long- $day *24 *3600)/3600);
                $minute = (int)(($long - $day *24 *3600 - $hour*3600)/60);
            }else if($long<3600){
                $minute = (int)$long/60;
            }else{
                $hour = (int)($long/3600);
                $minute = (int)(($long - $hour*3600)/60);
            }
            success(['day'=>$day,'hour'=>$hour,'minure'=>$minute]);
        }
    }

    /**
     *@退换货完善
     */
    public function perfect_returns_order(){
        if(IS_POST){
            $member = checklogin();
            $order_no = I('order_no');
            $goods_id = I('goods_id');
            if(empty($order_no) || empty($goods_id))    error("参数错误");
            $order = M('MallOrder')->where(['order_no'=>$order_no])->find();
            if(!$order)                                 error("订单错误");
            $data['phone'] = I('phone');
            $data['kuaidi_company'] = I('kuaidi_company');
            $data['kuaidi'] = I('kuaidi');
            $data['address'] = I('address');
            $check = M('MallReturnsOrder')->where(['order_id'=>$order['id'],'goods_id'=>$goods_id,'is_del'=>'1'])->find();
            if(!$check)                                 error("请完善上一步内容");
            if(empty($data['kuaidi_company']))          error("物流公司不能为空");
            if(empty($data['kuaidi']))                  error("物流单号不能为空");
            if(empty($data['phone']))                   error("联系电话不能为空");
            if(empty($data['address']))                 error("详细地址不能为空");
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            mt_srand(10000000 * (double)microtime());
            for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 15; $i++) {
                $str .= $chars[mt_rand(0, $lc)];
            }
            $data['number'] = $str;
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('MallReturnsOrder')->where(['id'=>$check['id']])->save($data);
            if($result){
                success("操作成功");
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@退换货上一步信息显示
     */
    public function returns_order_info(){
        $member = checklogin();
        $order_no = I('order_no');
        $goods_id = I('goods_id');
        if(empty($order_no) || empty($goods_id))    error("参数错误");
        $order = M('MallOrder')->where(['order_no'=>$order_no])->find();
        if(!$order)                                 error("订单错误");
        $check = M('MallReturnsOrder')->field('reason,img,type,amount')
            ->where(['order_id'=>$order['id'],'goods_id'=>$goods_id,'is_del'=>'1'])->find();
        success($check);
    }

    /**
     *@退换货详情
     */
    public function returns_view(){
        $member = checklogin();
        $order_no = I('order_no');
        $goods_id = I('goods_id');
        if(empty($order_no) || empty($goods_id))    error("参数错误");
        $order = M('MallOrder')->where(['order_no'=>$order_no])->find();
        if(!$order)                                 error("订单错误");
        $check = M('MallReturnsOrder')->field('reason,img,type,amount,status,intime,number')
            ->where(['order_id'=>$order['id'],'goods_id'=>$goods_id,'is_del'=>'1'])->find();
        success($check);
    }

    /**
     *@我的场地
     */
    public function my_place(){
        $member = checklogin();
        $map['a.mid'] = $member['member_id'];
        $map['a.type'] = 1;
        $p = I('p');
        empty($p)       &&      $p = 1;
        $num = 10;
        $count = M('PlaceOrder')->alias('a')
            ->join("LEFT JOIN __PLACE_LEASE_LIST__ b on a.lease_id = b.lease_id")
            ->join("LEFT JOIN __PLACE_LEASE__ c on b.place_id = c.place_id")
            ->where($map)->order("a.intime desc")->count();
        $page = ceil($count / $num);
        $list = M('PlaceOrder')->alias('a')
            ->field('a.order_no,a.amount,a.paid,a.status,a.count,b.img,b.name,b.price,c.address,c.name as place_name')
            ->join("LEFT JOIN __PLACE_LEASE_LIST__ b on a.lease_id = b.lease_id")
            ->join("LEFT JOIN __PLACE_LEASE__ c on b.place_id = c.place_id")
            ->where($map)->limit(($p-1)*$num,$num)
            ->order("a.intime desc")->select();
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@我的门票
     */
    public function my_ticket(){
        $member = checklogin();
        $map['a.mid'] = $member['member_id'];
        $status = I('status');
        if(empty($status)){
            $map['a.status'] = ['gt',1];
        }else{
            $map['a.status'] = $status+1;
        }
        $map['a.type'] = 2;
        $p = I('p');
        empty($p)       &&      $p = 1;
        $num = 10;
        $count = M('PlaceOrder')->alias('a')
            ->join("LEFT JOIN __PLACE_LEASE__ b on b.place_id = b.place_id")
            ->where($map)->order("a.intime desc")->count();
        $page = ceil($count / $num);
        $list = M('PlaceOrder')->alias('a')
            ->field('a.order_no,a.amount,a.paid,a.status,a.count,b.name,b.price,b.address,b.imgs')
            ->join("LEFT JOIN __PLACE_LEASE__ b on a.lease_id = b.place_id")
            ->where($map)->limit(($p-1)*$num,$num)
            ->order("a.intime desc")->select();
        success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@取消订单
     */
    public function cancel_place_order(){
        $member = checklogin();
        $order_no = I('order_no');
        if(empty($order_no))            error("订单号不能为空");
        $order = M('PlaceOrder')->where(['order_no'=>$order_no])->find();
        if($order['status'] != '1' )    error("订单状态不符合");
        $result = M('PlaceOrder')->where(['id'=>$order['id']])->delete();
        if($result){
            success("操作成功");
        }else{
            error("操作失败");
        }
    }
}