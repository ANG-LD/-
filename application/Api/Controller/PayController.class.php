<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/11/9
 * Time: 13:38
 */

namespace Api\Controller;


use Com\WechatAuth;

class PayController extends CommonController
{
    private $system=array();
    function _initialize(){
        $this->system = M("system")->where(['id'=>1])->find();
    }
    /**
     *检测支付密码和账户余额
     */
    public function check(){
        if(IS_POST){
            $member = checklogin();
            if(empty($member['pay_password'])){
                error("请设置支付密码");
            }else{
                success($member);
            }
        }
    }

    /**
     *酒店订单余额支付
     */
    public function pay_hotel_order(){
        if(IS_POST){
            $member = checklogin();
            $pay_password = I('pay_password');
            if(empty($pay_password))    error("支付密码为空");
            if(encrypt($pay_password) != $member['pay_password'])  error("支付密码错误");
            $order_no = I('order_no');
            if(empty($order_no))        error("订单号错误");
            $order = M('HotelOrder')->where(['order_no'=>$order_no])->find();
            if(empty($order))           error("订单号没找到");
            if($member['amount']<$order['paid'])    error("余额不足");
            M()->startTrans();
            $result = M('Member')->where(['member_id'=>$member['member_id']])->save(['amount'=>$member['amount']-$order['paid']]);
            if(!$result){
              M()->rollback();
              error("支付失败");
            }else{
                $data['status'] = 3;
                $data['uptime'] = date("Y-m-d H:i:s",time());
                $s = M('HotelOrder')->where(['order_no'=>$order_no]) -> save($data);
                if($s){
                    $code['member_id'] = $order['member_id'];
                    $code['order_no']  = $order_no;
                    $code['type']      = '2';
                    $code['amount']    = $order['paid'];
                    $code['intime']    = date("Y-m-d H:i:s",time());
                    $code['pay_type']  = '余额支付';
                    M('TradeRecord')    -> add($code);
                    $member = M('Member')->where(['member_id'=>$order['member_id']])->find();
                    $times = M('Grade')->where(['grade_id'=>$member['grade']])->getField('times');
                    if(empty($times))       $times = 1;
                    $consumer = $member['consumer'] + $code['amount'];
                    $install_score = M('InstallScore')->where(['id'=>1])->find();
                    $consumer_score = ceil(($consumer/$install_score['consumer']) * $install_score['give_score']);
                    $score_record = ceil((($order['paid']/$install_score['consumer']) * $install_score['give_score']));
                    if(!empty($order['score'])){
                        note_score($member['member_id'],$order['order_id'],2,$order['score'],'','酒店订单积分抵扣',2);
                        $score = $member['score'] + ceil($times * $score_record);
                    }else{
                        $score = $member['score'] + ceil($times * $score_record);
                    }
                    if(!empty($score_record)) note_score($member['member_id'],$order['order_id'],2,$score_record,ceil(($times-1) * $score_record),'酒店订单支付');
                    M('Member')->where(['member_id'=>$order['member_id']])->save(['score'=>$score,'consumer_score'=>$consumer_score,'consumer'=>$consumer]);
                    if(!empty($order['coupon_id'])){
                        M('MemberCoupon')->where(['id'=>$order['coupon_id']])->save(['status'=>2,'uptime'=>date("Y-m-d H:i:s",time())]);
                    }
                    $message = '【途老大】您的订单已支付成功，工作人员会尽快为您预定房间，预定完成后工作人员会与您联系确认入住流程。';
                    set_amount($member['member_id'],$code['amount'],'2','酒店订单支付');
                    set_message($member['member_id'],$message);
                    $this->change_grade($member['member_id']);
                    if(!empty($member['openid'])) {
                        $hotel= M('Hotel')->where(['hotel_id'=>$order['hotel_id']])->find();
                        $hour_long = ceil(((strtotime($order['end_time']) - strtotime($order['start_time'])))/(24*60*60));
                        $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                        $accessToken = S('globals_access_token');
                        if (empty($accessToken)) $accessToken = $weixin->getAccessToken();
                        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
                        $tmp = array(
                            'touser' => $member['openid'],
                            'template_id' => 'SkbKaLUO-tADFvgKCsl3IjU7EahlM7N-wuaAbEBO5OM',
                            'url' => C('IMG_PREFIX') . '/mobile/index.html#/Horders',
                            'topcolor' => '#FF0000',
                            'data' => array(
                                'first' => array('value' => '您的订单已支付成功', 'color' => '#FF0000'),
                                'keyword1' => array('value' => $hotel['name'].$order['room'], 'color' => '#FF0000'),
                                'keyword2' => array('value' => $hour_long.'晚', 'color' => '#FF0000'),
                                'keyword3' => array('value' => $order['start_time'], 'color' => '#FF0000'),
                                'keyword4' => array('value' => $order['order_no'], 'color' => '#FF0000'),
                                'remark' => array('value' => '您的订单已支付成功，工作人员会尽快为您预定房间，预定完成后工作人员会与您联系确认入住流程。', 'color' => '#FF0000'),
                            )
                        );
                        $tmp = json_encode($tmp);
                        $result = curl_post_json($url, $tmp);
                    }
                    M()->commit();
                    success("支付成功");
                }else{
                    M()->rollback();
                    error("支付失败");
                }
            }
        }
    }

    public function pay_plane_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $pay_password = I('pay_password');
            if (empty($pay_password)) error("支付密码为空");
            if (encrypt($pay_password) != $member['pay_password']) error("支付密码错误");
            $order_no = I('order_no');
            if (empty($order_no)) error("订单号错误");
            $order = M('AirportOrder')->where(['order_no' => $order_no])->find();
            if (empty($order)) error("订单号没找到");
            if($order['status'] != 2){
                $order['paid'] = $order['change_price'];
            }
            if ($member['amount'] < $order['paid']) error("余额不足");
            M()->startTrans();
            $result = M('Member')->where(['member_id' => $member['member_id']])->save(['amount' => $member['amount'] - $order['paid']]);
            if (!$result) {
                M()->rollback();
                error("支付失败");
            } else {
                if($order['status'] == '2'){
                    $data['status'] = 3;
                    $code['is_change'] = 1;
                }elseif($order['status'] == 6){
                    $data['status'] = 11;
                    $code['is_change'] = 2;
                }
                set_amount($member['member_id'],$order['paid'],'2','机票订单支付');
                $data['uptime'] = date("Y-m-d H:i:s", time());
                $s = M('AirportOrder')->where(['order_no' => $order_no])->save($data);
                if ($s) {
                    $code['member_id'] = $order['mid'];
                    $code['order_no'] = $order_no;
                    $code['type'] = '1';
                    $code['amount'] = $order['paid'];
                    $code['intime']    = date("Y-m-d H:i:s",time());
                    $code['pay_type'] = '余额支付';
                    M('TradeRecord')->add($code);
                    $member = M('Member')->where(['member_id' => $order['mid']])->find();
                    $times = M('Grade')->where(['grade_id'=>$member['grade']])->getField('times');
                    if(empty($times))       $times = 1;
                    $consumer = $member['consumer'] + $code['amount'];
                    $install_score = M('InstallScore')->where(['id' => 1])->find();
                    $consumer_score = ceil(($consumer / $install_score['consumer']) * $install_score['give_score']);
                    $score_record = ceil((($order['paid']/$install_score['consumer']) * $install_score['give_score']));
                    if($order['status'] == 2) {
                        if (!empty($order['score'])) {
                            note_score($member['member_id'], $order['order_id'], 1, $order['score'],'', '机票支付积分抵扣', 2);
                            $score = $member['score'] + ceil($times * $score_record);
                        } else {
                            $score = $member['score'] + ceil($times * $score_record);
                        }
                        if (!empty($score_record)) note_score($member['member_id'], $order['order_id'], 1, $score_record,ceil(($times-1)*$score_record), '机票订单支付');
                        M('Member')->where(['member_id' => $order['mid']])->save(['score' => $score, 'consumer_score' => $consumer_score, 'consumer' => $consumer]);
                        if (!empty($order['coupon_id'])) {
                            M('MemberCoupon')->where(['id' => $order['coupon_id']])->save(['status' => 2, 'uptime' => date("Y-m-d H:i:s", time())]);
                        }
                    }
                    $message = '【途老大】您的本次行程已经完成，感谢您在途老大订购机票，如您有任何关于出行方面的问题，可随时联系我们的客服人员。';
                    set_message($member['member_id'], $message);
                    $this->change_grade($member['member_id']);
                    if(!empty($member['openid'])) {
                        $flight = M('AirportOrderFlight')->where(['order_id'=>$order['order_id']])->order("flight_id asc")->select();
                        if(count($flight)>1){
                            $hangban = '（去程）'.$flight[0]['hangban'].'  '.'（返程）'.$flight[1]['hangban'];
                            $jichang = '（去程）'.$flight[0]['go_city'].$flight[0]['go_hangzhan'].'  '.'（返程）'.$flight[1]['go_city'].$flight[1]['go_hangzhan'];
                            $gotime = '（去程）'.date("Y-m-d H:i",strtotime(str_replace('T',' ',$flight[0]['go_time']))).'  '.'（返程）'.date("Y-m-d H:i",strtotime(str_replace('T',' ',$flight[1]['go_time'])));
                        }else{
                            $hangban = $flight[0]['hangban'];
                            $jichang = $flight[0]['go_city'].$flight[0]['go_hangzhan'];;
                            $gotime = date("Y-m-d H:i",strtotime(str_replace('T',' ',$flight[0]['go_time'])));
                        }
                        $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                        $accessToken = S('globals_access_token');
                        if (empty($accessToken)) $accessToken = $weixin->getAccessToken();
                        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
                        $tmp = array(
                            'touser' => $member['openid'],
                            'template_id' => 'avGqrSEcgdNet-f_2NwOh6h1rAGLyj07M9TIYzAYtdI',
                            'url' => C('IMG_PREFIX') . '//mobile/index.html#/Aorders',
                            'topcolor' => '#FF0000',
                            'data' => array(
                                'first' => array('value' => '您的订单已支付成功', 'color' => '#FF0000'),
                                'keyword1' => array('value' => $order['order_no'], 'color' => '#FF0000'),
                                'keyword2' => array('value' => $hangban, 'color' => '#FF0000'),
                                'keyword3' => array('value' => $gotime, 'color' => '#FF0000'),
                                'keyword4' => array('value' => $jichang, 'color' => '#FF0000'),
                                'keyword5' => array('value' => $order['paid'], 'color' => '#FF0000'),
                                'remark' => array('value' => '您的订单已支付成功，工作人员会尽快为您出票，出票完成后可在订单中查询机票信息，如有问题请联系客服人员。', 'color' => '#FF0000'),
                            )
                        );
                        $tmp = json_encode($tmp);
                        $result = curl_post_json($url, $tmp);
                    }
                    M()->commit();
                    success("支付成功");
                }
            }
        }
    }

    /**
     *根据用户消费额提升等级
     */
    function change_grade($member_id){
        $amount = M('TradeRecord')->where(['member_id'=>$member_id])->sum('amount');
        $grade_config = M('Grade')->select();
        $grade = 1;
        foreach($grade_config as $key=> $val){
            if($amount>=$val['value'])  $grade = $val['grade_id'];
        }
        M('Member')->where(['member_id'=>$member_id])->save(['grade'=>$grade]);
    }
}