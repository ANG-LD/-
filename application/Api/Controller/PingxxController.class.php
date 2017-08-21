<?php
namespace Api\Controller;


use Com\WechatAuth;
use Think\Controller;
use Pingpp\Charge;
use Pingpp\Pingpp;

class PingxxController extends CommonController

{

    /**
     * pingxx支付
     * @param $orderNo
     * @param $type
     * @param $openid
     */

    /*充值*/
    public function ping(){
        if(IS_POST){
            $member = checklogin();
            $data['user_id'] = $member['user_id'];
            $price_list_id = I('price_list_id');
            $note = M('PriceList')->where(['price_list_id' => $price_list_id])->find();
            if (empty($note)) error("参数错误");
            $data['amount'] = $note['price'];
            $data['score'] = $note['diamond'];
            $data['zeng'] = $note['zeng'];
            $data['pay_number'] = date("YmdHis", time()) . rand(100000, 999999);
            $data['pay_type'] = I('type');
            if (empty($data['pay_type'])) $data['pay_type'] = 'wx';
            if (strpos($data['pay_type'], 'alipay') !== false) $data['pay_type'] = 'alipay';
            if (strpos($data['pay_type'], 'wx') !== false) $data['pay_type'] = 'wx';
            if (strpos($data['pay_type'], 'applepay') !== false) $data['pay_type'] = 'applepay';
            $data['intime'] = date("Y-m-d H:i:s", time());
            $result = M("Recharge")->add($data);
            if (!$result) {
                error("下单失败");
            } else {
//                $this->pings($data['pay_type'],$data['pay_number']."Y".time(),($data['amount']*100),I("openid"));
                $this->pings(I('type'), $data['pay_number'] . "Y" . time(), (0.01 * 100), I("openid"));
            }
        }
    }

    /**
     *商城订单支付
     */
    public function ping1(){
        if(IS_POST) {
            $member = checklogin();
            $order_no   = I('order_no');
            $type       = I('type');
            $openid     = I('openid');
            $hotel_order = M('MallOrder')->where(['order_no' => $order_no])->find();
            if (!$hotel_order) error("订单错误");
            //$this->pings($type, $order_no . "Y" . time(), $hotel_order['paid'] * 100, $openid);
            $this->pings($type, $order_no . "Y" . time(), 0.01 * 100, $openid);
        }
    }

    /**
     *名师指点订单支付
     */
    public function ping2(){
        if(IS_POST) {
            $member = checklogin();
            $order_no   = I('order_no');
            $type       = I('type');
            $openid     = I('openid');
            $order = M('TeachOrder')->where(['order_no' => $order_no])->find();
            if (!$order) error("订单错误");
//            $this->pings($type, $order_no . "Y" . time(), $order['amount'] * 100, $openid);
            $this->pings($type, $order_no . "Y" . time(), 0.01 * 100, $openid);
        }
    }

    /**
     *会员升级订单支付
     */
    public function ping3(){
        if(IS_POST) {
            $member = checklogin();
            $order_no   = I('order_no');
            $type       = I('type');
            $openid     = I('openid');
            $order = M('UpgradeOrder')->where(['order_no' => $order_no])->find();
            if (!$order) error("订单错误");
//            $this->pings($type, $order_no . "Y" . time(), $order['amount'] * 100, $openid);
            $this->pings($type, $order_no . "Y" . time(), 0.01 * 100, $openid);
        }
    }

    /**
     *线下课程班级订单支付
     */
    public function ping4(){
        if(IS_POST) {
            $member = checklogin();
            $order_no   = I('order_no');
            $type       = I('type');
            $openid     = I('openid');
            $order = M('TutorClassOrder')->where(['order_no' => $order_no])->find();
            if (!$order) error("订单错误");
//            $this->pings($type, $order_no . "Y" . time(), $order['amount'] * 100, $openid);
            $this->pings($type, $order_no . "Y" . time(), 0.01 * 100, $openid);
        }
    }



    function pings($type,$order_number,$amount,$openid)
    {
        vendor("Pingpp.init");
        Pingpp::setApiKey($this->system['secretkey']);
        \Pingpp\Pingpp::setPrivateKeyPath(__DIR__ . '/your_rsa_private_key.pem');

        if($type==null){
            $type="wx";
        }
        switch ($type) {
            case 'alipay_wap':
                $extra = array(
                    'success_url' => 'http://jipiao.tstmobile.com/mobile/#/account',
                    'cancel_url' => 'http://jipiao.tstmobile.com/mobile/#/account'
                );
                break;
            case 'alipay':
                $extra = array();
                break;
            case 'alipay_pc_direct':
                $extra = array(
                    'success_url' => 'http://www.mychnyx.com/wap/index.html#/offline_record'
                );
                break;
            case 'upmp_wap':
                $extra = array(
                    'result_url' => 'http://www.mychnyx.com/api.php/pingxx/callback'
                );
                break;
            case 'bfb_wap':
                $extra = array(
                    'result_url' => 'http://www.mychnyx.com/api.php/pingxx/callback',
                    'bfb_login' => true
                );
                break;
            case 'upacp_wap':
                $extra = array(
                    'result_url' => 'http://www.mychnyx.com/api.php/pingxx/callback'
                );
                break;
            case 'upacp_pc':
                $extra = array(
                    'result_url' => 'http://www.mychnyx.com/wap/index.html#/offline_record'
                );
                break;
            case 'wx_pub':
                $extra = array(
                    'open_id' => $openid
                );
                break;
            case 'wx_pub_qr':
                $extra = array(
                    'product_id' => 'Productid'
                );
                break;
            case 'wx':
                $extra = array(
                );
                break;
        }

        if($amount==null)
        {
            $amount=1;
        }
        if($order_number==null){
            $order_number="m".time();
        }

        try {
            $ch = Charge::create([
                'order_no' => $order_number,
                'amount' => $amount,
                'channel' => $type,
                'currency' => 'cny',
                'client_ip' => get_client_ip(),
                'subject' => "名师传艺订单",
                'body' => 'Your Body',
                'app' => ['id' => $this->system['apiid']],
                'extra'=> $extra
            ]);
            $ch = json_decode($ch,true);
            success($ch);
        } catch (\Pingpp\Error\Base $e) {
            header('Status: ' . $e->getHttpStatus());
            $data = json_decode($e->getHttpBody(),true);
            error($data['error']);
        }
    }

    /**
     *@退款
     */
    public function return_money($amount,$charge){
        vendor("Pingpp.init");
        Pingpp::setApiKey($this->system['secretkey']);
        \Pingpp\Pingpp::setPrivateKeyPath(__DIR__ . '/your_rsa_private_key.pem');
        // 创建退款
        try {
            // 通过发起一次退款请求创建一个新的 refund 对象，只能对已经发生交易并且没有全额退款的 charge 对象发起退款
            $ch = \Pingpp\Charge::retrieve($charge);
            $re = $ch->refunds->create(array(
                'description'=>'Refund Description',
                'amount'    => $amount * 100
            ));
            return $re;// 输出 Ping++ 返回的退款对象 Refund;
        } catch (\Pingpp\Error\Base $e) {
            //header('Status: ' . $e->getHttpStatus());
            return $e->getHttpBody();
        }
    }

    /**
     * 充值返回值
     */
    public function recharge_callback()
    {
        $result = json_decode(file_get_contents('php://input'), true);
        $text="\n\n".date("y-m-d H:i:s",time())."\n".var_export($result,true);
        file_put_contents("callback.txt", $text, FILE_APPEND);
        if ($result['type'] == 'charge.succeeded') {
            $data['pay_status'] = 2;
            $data['pay_return'] = json_encode($result);
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $a = explode("Y",$result["data"]["object"]['order_no']);
            file_put_contents("order.txt", $a[0], FILE_APPEND);
            $s = M('Recharge')->where(['pay_number'=>$a[0]]) -> save($data);
            if($s){
                $record = M('Recharge')->where(['pay_number'=>$a[0]])->find();
                $user = M('User')->where(['user_id'=>$record['user_id']])->find();
                $money = $result['data']['object']['amount'] / 100; //支付金额
                $score = $user['money'] + $record['score'];    //充值币相加
                set_amount($user['user_id'],$record['score'],'1','充值');
                M('User')->where(['user_id'=>$user['user_id']])->save(['money'=>$score]);
                $code['member_id'] = $record['user_id'];
                $code['order_no']  = $a[0];
                $code['type']      = 2;
                $code['intime']    = date("Y-m-d H:i:s",time());
                $code['pay_no']    = $result["data"]["object"]['order_no'];
                $code['amount']    = $result['data']['object']['amount'] / 100;
                $code['pay_return']= json_encode($result);
                M('TradeRecord')    -> add($code);
                success("支付成功");
            }else{
                error("支付失败");
            }
        }
    }

    /**
     *@商城订单支付返回值
     */
    public function mall_callback(){
        $result = json_decode(file_get_contents('php://input'), true);
        $text="\n\n".date("y-m-d H:i:s",time())."\n".var_export($result,true);
        file_put_contents("callback.txt", $text, FILE_APPEND);
        if ($result['type'] == 'charge.succeeded') {
            $a = explode("Y",$result["data"]["object"]['order_no']);
            $type = $result["data"]["object"]['channel'];
            if(strpos($type,'alipay')!==false){
                $code['pay_type'] = '支付宝';
                $data['pay_type'] = '支付宝';
            }else if(strpos($type,'wx')!==false) {
                $code['pay_type'] = '微信';
                $data['pay_type'] = '微信';
            }
            file_put_contents("order.txt", $a[0], FILE_APPEND);
            $order = M('MallOrder')->where(['order_no'=>$a[0]])->find();
            $data['state'] = 2;
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['returns'] = json_encode($result);
            $s = M('MallOrder')->where(['order_no'=>$a[0]]) -> save($data);
            if($s){
                $code['member_id'] = $order['mid'];
                $code['order_no']  = $a[0];
                $code['type']      = 1;
                $code['intime']    = date("Y-m-d H:i:s",time());
                $code['pay_no']    = $result["data"]["object"]['order_no'];
                $code['amount']    = $result['data']['object']['amount'] / 100;
                $code['pay_return']= json_encode($result);
                M('TradeRecord')    -> add($code);
                $member = M('User')->where(['user_id'=>$order['mid']])->find();
                if(in_array($order['type'],['1','2'])){
                    $order_detail = M('MallOrderDetail')->where(['order_id'=>$order['id']])->select();
                    foreach($order_detail as $k=>$v){
                        $goods = M('Goods')->where(['goods_id'=>$v['goods_id']])->find();
                        if($goods){
                            if($goods['number']>$v['number']){
                                M('Goods')->where(['goods_id'=>$v['goods_id']])->setDec('number',$v['number']);
                                if(!empty($v['kinds_id'])||!empty($v['kinds_id2'])){
                                    $kinds = $v['kinds_id'].','.$v['kinds_id2'];
                                    M('GoodsStock')->where(['goods_id'=>$v['goods_id'],'kinds'=>$kinds])->setDec('number',$v['number']);
                                }
                            }else{
                                M('Goods')->where(['goods_id'=>$v['goods_id']])->save(['number'=>'0']);
                            }
                        }
                    }
                }

//                $times = M('Grade')->where(['grade_id'=>$member['grade']])->getField('times');
//                if(empty($times))       $times = 1;
//                $consumer = $member['consumer'] + $code['amount'];
//                $install_score = M('InstallScore')->where(['id'=>1])->find();
//                $consumer_score = ceil((($consumer/$install_score['consumer']) * $install_score['give_score']));
//                $score_record = ceil((($code['amount']/$install_score['consumer']) * $install_score['give_score']));
//                if(!empty($order['score'])){
//                    $score = $member['score'] + ceil($times * $score_record);
//                    note_score($member['member_id'],$order['order_id'],2,$order['score'],'','酒店订单积分抵扣',2);
//                }else{
//                    $score = $member['score'] + ceil($times * $score_record);
//                }
//                if(!empty($score_record))       note_score($member['member_id'],$order['order_id'],2,$score_record,ceil(($times-1) * $score_record),'酒店订单支付');
//                M('Member')->where(['member_id'=>$order['member_id']])->save(['score'=>$score,'consumer_score'=>$consumer_score,'consumer'=>$consumer]);
//                if(!empty($order['coupon_id'])){
//                    M('MemberCoupon')->where(['id'=>$order['coupon_id']])->save(['status'=>2,'uptime'=>date("Y-m-d H:i:s",time())]);
//                }
//                $message = '【91享吧】您的订单已支付成功，工作人员会尽快为您处理订单相关事宜';
//                set_message($member['member_id'],$message);
                //$this->change_grade($member['member_id']);
                /*                if(!empty($member['openid'])) {
                                    $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                                    $accessToken = S('globals_access_token');
                                    if (empty($accessToken)) $accessToken = $weixin->getAccessToken();
                                    $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
                                    $tmp = array(
                                        'touser' => $member['openid'],
                                        'template_id' => 'SkbKaLUO-tADFvgKCsl3IjU7EahlM7N-wuaAbEBO5OM',
                                        'url' => C('IMG_PREFIX') . '/mobile/index.html#/Horders',
                                        'topcolor' => '#FF0000'
                                    );
                                    $tmp = json_encode($tmp);
                                    $result = curl_post_json($url, $tmp);
                                }*/
                success("支付成功");
            }else{
                error("支付失败");
            }
        }
    }

    /**
     *名师指点返回
     */
    public function teach_callback(){
        $result = json_decode(file_get_contents('php://input'), true);
        $text="\n\n".date("y-m-d H:i:s",time())."\n".var_export($result,true);
        file_put_contents("callback.txt", $text, FILE_APPEND);
        if ($result['type'] == 'charge.succeeded') {
            $a = explode("Y",$result["data"]["object"]['order_no']);
            $type = $result["data"]["object"]['channel'];
            file_put_contents("order.txt", $a[0], FILE_APPEND);
            $order = M('TeachOrder')->where(['order_no'=>$a[0]])->find();
            $data['state'] = 2;
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['returns'] = json_encode($result);
            $s = M('TeachOrder')->where(['order_no'=>$a[0]]) -> save($data);
            if($s){
                if(strpos($type,'alipay') !==false)      $code['pay_type'] = '支付宝';
                if(strpos($type,'wx')!==false)          $code['pay_type'] = '微信';
                $code['member_id'] = $order['user_id'];
                $code['order_no']  = $a[0];
                $code['intime']    = date("Y-m-d H:i:s",time());
                $code['pay_no']    = $result["data"]["object"]['order_no'];
                $code['amount']    = $result['data']['object']['amount'] /100;
                $code['pay_return']= json_encode($result);
                $code['type']      = 4;
                M('TradeRecord')    -> add($code);
                $member = M('User')->where(['user_id'=>$order['user_id']])->find();
                $earnings['earnings'] = $order['amount'];     //导师收益统计
                $earnings['user_id'] = $order['user_id2'];
                $earnings['content'] = '一对一指点';
                $earnings['intime'] = time();
                $earnings['date'] = date("Y-m-d",time());
                $earnings['type'] = 1;
                M('earnings')->add($earnings);
//                $times = M('Grade')->where(['grade_id'=>$member['grade']])->getField('times');
//                if(empty($times))       $times = 1;
//                $consumer = $member['consumer'] + $code['amount'];
//                $install_score = M('InstallScore')->where(['id'=>1])->find();
//                $consumer_score = ceil((($consumer/$install_score['consumer']) * $install_score['give_score']));
//                $score_record = ceil((($code['amount']/$install_score['consumer']) * $install_score['give_score']));
//                if($order['status'] == '2') {
//                    if (!empty($order['score'])) {
//                        $score = $member['score'] + ceil($times * $score_record); //用户现有积分
//                        note_score($member['member_id'], $order['order_id'], 1, $order['score'],0, '机票订单积分抵扣', 2);
//                    } else {
//                        $score = $member['score'] + ceil($times * $score_record);
//                    }
//                    if (!empty($score_record)) note_score($member['member_id'], $order['order_id'], 1, $score_record,ceil(($times-1)*$score_record), '机票订单支付');
//                    M('Member')->where(['member_id' => $order['mid']])->save(['score' => $score, 'consumer_score' => $consumer_score, 'consumer' => $consumer]);
//                    if (!empty($order['coupon_id'])) {
//                        M('MemberCoupon')->where(['id' => $order['coupon_id']])->save(['status' => 2, 'uptime' => date("Y-m-d H:i:s", time())]);
//                    }
//                }
//                set_message($member['member_id'],$message);
                success("支付成功");
            }else{
                error("支付失败");
            }
        }
    }

    /**
     *会员升级返回
     */
    public function upgrade_callback(){
        $result = json_decode(file_get_contents('php://input'), true);
        $text="\n\n".date("y-m-d H:i:s",time())."\n".var_export($result,true);
        file_put_contents("callback.txt", $text, FILE_APPEND);
        if ($result['type'] == 'charge.succeeded') {
            $a = explode("Y",$result["data"]["object"]['order_no']);
            $type = $result["data"]["object"]['channel'];
            file_put_contents("order.txt", $a[0], FILE_APPEND);
            $order = M('UpgradeOrder')->where(['order_no'=>$a[0]])->find();
            $data['state'] = 2;
            $data['returns'] = json_encode($result);
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $s = M('UpgradeOrder')->where(['order_no'=>$a[0]]) -> save($data);
            if($s){
                if(strpos($type,'alipay') !==false)      $code['pay_type'] = '支付宝';
                if(strpos($type,'wx')!==false)          $code['pay_type'] = '微信';
                $code['member_id'] = $order['user_id'];
                $code['order_no']  = $a[0];
                $code['intime']    = date("Y-m-d H:i:s",time());
                $code['pay_no']    = $result["data"]["object"]['order_no'];
                $code['amount']    = $result['data']['object']['amount'] /100;
                $code['pay_return']= json_encode($result);
                $code['type']      = 3;
                M('TradeRecord')    -> add($code);
                //$user = M('User')->where(['user_id'=>$order['user_id']])->find();

                /*会员升级记录*/
                $class = M('TutorLiveClass')->where(['tutor_id'=>$order['user_id2'],'is_del'=>1])
                    ->limit(1)->order("intime desc")->find();
                $upgrade_record['user_id'] = $order['user_id'];
                $upgrade_record['user_id2'] = $order['user_id2'];
                $upgrade_record['state'] = $order['status'];
                $upgrade_record['intime'] = date("Y-m-d H:i:s");
                $upgrade_record['amount'] = $order['amount'];
                $upgrade_record['upgrade_order_id'] = $order['id'];
                $upgrade_record['date_value'] = $class['end_time'];
                M('UpgradeRecord')->add($upgrade_record);
                $earnings['earnings'] = $order['amount'];     //导师收益统计
                $earnings['user_id'] = $order['user_id2'];
                $order['status'] == '1'  ? $content = '高级会员'   : $content = '钻石会员';
                $earnings['content'] = $content;
                $earnings['intime'] = time();
                $earnings['date'] = date("Y-m-d",time());
                $earnings['type'] = 1;
                M('earnings')->add($earnings);
                success("支付成功");
            }else{
                error("支付失败");
            }
        }
    }

    /**
     *会员升级返回
     */
    public function tutor_class_callback(){
        $result = json_decode(file_get_contents('php://input'), true);
        $text="\n\n".date("y-m-d H:i:s",time())."\n".var_export($result,true);
        file_put_contents("callback.txt", $text, FILE_APPEND);
        if ($result['type'] == 'charge.succeeded') {
            $a = explode("Y",$result["data"]["object"]['order_no']);
            $type = $result["data"]["object"]['channel'];
            file_put_contents("order.txt", $a[0], FILE_APPEND);
            $order = M('TutorClassOrder')->where(['order_no'=>$a[0]])->find();
            $data['state'] = 2;
            $data['returns'] = json_encode($result);
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $s = M('TutorClassOrder')->where(['order_no'=>$a[0]]) -> save($data);
            if($s){
                if(strpos($type,'alipay') !==false)      $code['pay_type'] = '支付宝';
                if(strpos($type,'wx')!==false)          $code['pay_type'] = '微信';
                $code['member_id'] = $order['user_id'];
                $code['order_no']  = $a[0];
                $code['intime']    = date("Y-m-d H:i:s",time());
                $code['pay_no']    = $result["data"]["object"]['order_no'];
                $code['amount']    = $result['data']['object']['amount'] /100;
                $code['pay_return']= json_encode($result);
                $code['type']      = 5;
                M('TradeRecord')    -> add($code);
                //$user = M('User')->where(['user_id'=>$order['user_id']])->find();

                /*线下报名记录*/
                $record['user_id'] = $order['user_id'];
                $record['tutor_class_id'] = $order['tutor_class_id'];
                $record['number'] = $order['number'];
                $record['intime'] = date("Y-m-d H:i:s");
                $record['amount'] = $order['amount'];
                M('TutorClassSign')->add($record);
                M('TutorClass')->where(['id'=>$order['tutor_class_id']])->setInc('value',$order['number']);
                success("支付成功");
            }else{
                error("支付失败");
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
            if($amount>=$val['value']){
                $grade = $val['grade_id'];
            }
        }
        M('Member')->where(['member_id'=>$member_id])->save(['grade'=>$grade]);
    }

    /**
     * @apple_recharge 苹果充值
     */
    public function apple_recharge(){
        $user = checklogin();
        $price_list_id = I('price_list_id');
        $note = M('PriceList')->where(['price_list_id' => $price_list_id])->find();
        if (empty($note)) error("参数错误");
        $data['amount'] = $note['price'];
        $data['score'] = $note['diamond'];
        $data['zeng'] = $note['zeng'];
        $data['pay_number'] = date("YmdHis", time()) . rand(100000, 999999);
        $data['pay_type'] = 'applepay';
        $data['pay_status'] = 2;
        $data['intime'] = date("Y-m-d H:i:s", time());
        $result = M("Recharge")->add($data);
        if($result){
            set_amount($user['user_id'],$data['score'],'1','充值');
            $score = $user['money'] + $data['score'];    //充值币相加
            M('User')->where(['user_id'=>$user['user_id']])->save(['money'=>$score]);
            $code['member_id'] = $user['user_id'];
            $code['order_no']  = $data['pay_number'];
            $code['type']      = 2;
            $code['intime']    = date("Y-m-d H:i:s",time());
            $code['pay_no']    = $data['pay_number'];
            $code['amount']    = $data['mount'];
            M('TradeRecord')    -> add($code);
            success("充值成功");
        }else{
            error("充值失败");
        }

    }

}