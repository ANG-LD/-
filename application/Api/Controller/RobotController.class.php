<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/7
 * Time: 15:35
 */

namespace Api\Controller;


class RobotController extends CommonController
{
    /**
     *@场地出租
     */
    public function place_lease(){
        if(IS_POST){
//            $member = checklogin();
            $place = M('PlaceLease')->field('place_id,name,address,imgs,province,city,area,price,content,phone')
                ->where(['place_id'=>'1'])->find();
            $re =  M('PlaceLease')->field('place_id,name,address,imgs,province,city,area,price,content,phone')
                ->where(['place_id'=>'2'])->find();
            $re['lease'] = M('PlaceLeaseList')->field('lease_id,name,img,price')
                ->where(['place_id'=>$re['place_id'],'is_del'=>'1'])->select();
            success(['place'=>$place,'re'=>$re]);
        }
    }

    /**
     *@赛事活动
     */
    public function game_list(){
        if(IS_POST){
//            $member = checklogin();
            $type = I('type');
            empty($type)   &&  $type = 1;
            $p = I('p');
            empty($p)      &&   $p = 1;
            $num = 5;
            $map['status']  = 2;
            $map['is_del']  = 1;
            $list = M('Game')->field('game_id,name,img,start_time,end_time,city,area,address')
                ->where($map)->order("intime desc")->select();
            $today = strtotime(date("Y-m-d",time()));
            if($type == 1){
                foreach($list as $k=>$v){
                    if(strtotime($v['end_time'])-time()<0){
                        unset($list[$k]);
                    }else{
                        if(strtotime($v['start_time'])-$today<24*3600 ||strtotime($v['end_time'])-$today<24*3600){
                            $list[$k]['is_top'] = 2;
                        }else{
                            $list[$k]['is_top'] = 1;
                        }
                    }
                }
                $list = array_values($list);
                $list = wpjam_array_desc($list, 'is_top', $order = SORT_DEST,$sort_flags = SORT_NUMERIC);
            }else{
                foreach($list as $k=>$v){
                    if(strtotime($v['end_time'])-time()>0){
                        unset($list[$k]);
                    }
                }
                $list = array_values($list);
            }
            $count = count($list);
            $page = ceil($count/5);
            $data = array_slice($list,($p-1)*$num,$num);
            success(['page'=>$page,'data'=>$data]);
        }
    }

    /**
     *@赛事详情
     */
    public function game_view(){
        if(IS_POST){
//            $member = checklogin();
            $id = I('game_id');
            if(empty($id))      error("参数错误");
            $game = M('Game')->field('game_id,name,imgs,province,city,area,address,dis,content,start_time,end_time')
                ->where(['game_id'=>$id])->find();

            if(strtotime($game['end_time']) - time()<0){
                $game['is_finish'] = 1;
            }else{
                $game['is_finish'] = 2;
            }
            $game['list'] = M('GamePrice')->field('price_id,name,price,game_id,status')
                ->where(['game_id'=>$game['game_id']])->order("price asc")->select();
            success($game);
        }
    }

    /**
     *@赛事门票确认订单
     */
    public function confirm_game_order(){
        if(IS_POST){
            $member = checklogin();
            $id = I('price_id');
            $check = M('GamePrice')->alias('a')
                ->field('a.price_id,a.name,a.price,b.img')
                ->join("INNER JOIN __GAME__ b on a.game_id = b.game_id")
                ->where(['a.price_id'=>$id])->find();
            if(empty($check))       error("商品不能为空");
            $count = I('count');
            if(empty($count))       $count = 1;
            $amount = sprintf("%.2f", $check['price'] * $count);
            $aid   = I('address_id');
            if(empty($aid)){
                $address = M('AcceptorAddress')->where(['mid'=>$member['member_id'],'is_default'=>'2'])->find();
            }else{
                    $address = M("AcceptorAddress")->where(['id'=>$aid])->find();
                }
            }
            $arr = '';
            if(!empty($address)){
                empty($address['name']) ? $arr['name'] = $member['nickname']  : $arr['name'] = $address['name'];
                empty($address['phone']) ? $arr['phone'] = $member['phone']  : $arr['phone'] = $address['phone'];
                empty($address['postage']) ? $arr['postage'] = '0'  : $arr['postage'] = $address['postage'];
                $arr['address'] = $address['province'].$address['city'].$address['area'].$address['street'].$address['address'];
            }
            if(empty($member['score'])){
                $score = [];
            }else{
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                if($member['score'] < $install_score['score']){
                    $score = [];
                }else{
                    $int = (int)($member['score']/$install_score['score']);
                    if($int*$install_score['money']<$amount){
                        $score['member_score'] = $member['score'];
                        $score['score'] = $int * $install_score['score'];
                        $score['money'] = $int * $install_score['money'];
                    }
                    if($int * $install_score['money']>$amount){
                        $int = ceil(($amount)/$install_score['money']);
                        $score['member_score'] = $member['score'];
                        $score['score'] = $int * $install_score['score'];
                        $score['money'] = $int * $install_score['money'];
                    }
                }
            }
            success(['address'=>$arr,'goods'=>$check,'score'=>$score,'amount'=>$amount]);
    }

    /**
     *@写入赛事门票订单
     */
    public function set_game_order(){
        if(IS_POST){
            $member = checklogin();
            $data   = $_POST;
//            if($data['is_agree'] != 1)      error("协议尚未同意");
            if(empty($data['price_id']))    error("商品不能为空");
            if(empty($data['count']))       error("商品数量不能为空");
//            if(empty($data['pattern']))     error("商品数量不能为空");
            if(empty($data['name']))        error("收件人不能为空");
            if(empty($data['phone']))       error("联系方式不能为空");
            if(empty($data['address']))     error("详细地址不能为空");
            $goods = M('GamePrice')->where(['price_id'=>$data['price_id']])->find();
            $data['amount'] = $goods['price'] * $data['count'];
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("积分不够，不能使用积分"); //查询积分
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                if($member['score'] < $install_score['score'])    error("积分不够，不能使用积分"); //查询积分
                $ratio = $install_score['money']/$install_score['score'];
            }
            $data['deduction'] = $data['score'] * $ratio;
            $data['paid'] = $data['amount'] - $data['deduction'];
            $data['order_no'] = date("YmdHis").rand(100000,999999);
            $data['intime'] = date("Y-m-d H:i:s");
            $data['mid'] = $member['member_id'];
            $data['type'] = 3;
            $data['state'] = 1;
            $mall_order  = M('MallOrder');
            $mall_order -> startTrans();
            $result = $mall_order -> add($data);
            if($result){
                if(!empty($data['score'])){
                    $score = $member['score'] - $data['score']; //用户现有积分
                    $re = M('Member')->where(['member_id' => $member['member_id']])->save(['score' => $score]);
                    if(!$re){
                        $mall_order -> rollback();
                    }
                }
                $code['order_id'] = $result;
                $code['intime'] = date("Y-m-d H:i:s", time());
                $code['goods_id'] = $data['price_id'];
                $code['number'] = $data['count'];
//                $code['pattern'] = $data['pattern'];
                $result = M('MallOrderDetail')->add($code);
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
     *@场地出租下单
     */
    public function set_lease_order(){
        if(IS_POST){
            $member = checklogin();
            $data = $_POST;
            if(empty($data['lease_id']))        error("套餐不能为空");
            $check = M('PlaceLeaseList')->where(['lease_id'=>$data['lease_id']])->find();
            if(!$check)                 error("套餐选择错误");
            $data['order_no'] = date("YmdHis").rand(100000,999999);
            $data['mid'] = $member['member_id'];
            $data['amount'] = $check['price'];
            $data['paid']   = $data['amount'];
            $data['status'] = 1;
            $data['type'] = 1;
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('PlaceOrder')->add($data);
            if($result){
                success($data['order_no']);
            }else{
                error("写入订单失败");
            }
        }
    }


    /**
     *@门票预售确认订单
     */
    public function gate_order_confirm(){
        if(IS_POST){
            $member = checklogin();
            $goods_id = I('place_id');
            $check = M('PlaceLease')->field('place_id,name,address,imgs,price,phone')
                ->where(['place_id'=>$goods_id])->find();
            if(empty($check))       error("商品不能为空");
            $count = I('count');
            if(empty($count))       $count = 1;
            $amount = sprintf("%.2f", $check['price'] * $count);
            $aid   = I('address_id');
            if(empty($aid)){
                $address = M('AcceptorAddress')->where(['mid'=>$member['member_id'],'is_default'=>'2'])->find();
            }else{
                $type = I('type');
                if($type == 1){
                    $address = M('SpotPick')->alias('a')
                        ->field('a.name as pick_name,a.address,b.province,b.city,b.area,b.address as spot_address,b.name as spot_name')
                        ->join("LEFT JOIN __SPOT_ADDRESS__ b on a.spot_id = b.id")
                        ->where(['a.pick_id'=>$aid])->find();
                    if(!empty($address)){
                        //$address['name'] = $address['name'].$address['spot_name'];
                        $address['address'] = $address['spot_address'].$address['address'];
                        unset($address['spot_address']);
                        unset($address['spot_name']);
                    }
                }else{
                    $address = M("AcceptorAddress")->where(['id'=>$aid])->find();
                }
            }
            $arr = '';
            if(!empty($address)){
                empty($address['name']) ? $arr['name'] = $member['nickname']  : $arr['name'] = $address['name'];
                empty($address['phone']) ? $arr['phone'] = $member['phone']  : $arr['phone'] = $address['phone'];
                empty($address['postage']) ? $arr['postage'] = '0'  : $arr['postage'] = $address['postage'];
                $arr['address'] = $address['city'].$address['area'].$address['street'].$address['address'];
            }

/*            $install_score = M('InstallScore')->where(['id'=>1])->find();
            if($member['score'] + $member['amount'] < $install_score['score']){
                $score = [];
            }else{
                $int = (int)(($member['score'] + $member['amount'])/$install_score['score']);
                if($int*$install_score['money']<$amount * 0.2){
                    $score['member_score'] = $member['score'] + $member['amount'];
                    $score['score'] = $int * $install_score['score'];
                    $score['money'] = $int * $install_score['money'];
                }else{
                    $int = ceil(($amount *0.2)/$install_score['money']);
                    $score['member_score'] = $member['score'] + $member['amount'];
                    $score['score'] = $int * $install_score['score'];
                    $score['money'] = $int * $install_score['money'];
                }
            }*/
            success(['address'=>$arr,'goods'=>$check,'amount'=>$amount]);
        }

    }

    /**
     *@门票预售下单
     */
    public function set_gate_order(){
        if(IS_POST){
            $member = checklogin();
            $data   =  $_POST;
            if(empty($data['place_id']))        error($data['place_id']);
            if(empty($data['count']))          error("商品数量不能为空");
//            if(empty($data['pattern']))     error("商品数量不能为空");
            if(empty($data['name']))        error("收件人不能为空");
            if(empty($data['phone']))       error("联系方式不能为空");
            if(empty($data['address']))     error("详细地址不能为空");

            $check = M('PlaceLease')->where(['place_id'=>$data['place_id']])->find();
            if(!$check)                         error("参数错误");
            empty($data['count'])       &&      $data['count'] = 1;
            $data['amount'] = $check['price'] * $data['count'];
            $data['order_no'] = date("YmdHis").rand(100000,999999);
            $data['mid'] = $member['member_id'];
            $data['paid']   = $data['amount'];
            $data['status'] = 1;
            $data['lease_id'] = $data['place_id'];
            $data['type'] = 2;
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('PlaceOrder')->add($data);
            if($result){
                success($data['order_no']);
            }else{
                error("写入订单失败");
            }
        }
    }

    /**
     *@活动场地图片
     */
    public function robot_img(){
        $img = M('Module')->where(['module_id'=>'3'])->getField('picture');
        success($img);
    }

}