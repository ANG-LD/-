<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/18
 * Time: 14:57
 */

namespace Api\Controller;
use Com\WechatAuth;

class PlaneController extends CommonController
{
    private $system=array();
    function _initialize(){
        header("Content-type: text/html; charset=utf-8");
        $this->system = M("system")->where(['id'=>1])->find();
    }
    /**
     *用户乘机人信息
     */
    public function passenger(){
        if(IS_POST){
            checklogin();
            $data = $_POST;
            $data = M('Passenger')->where(['uid'=>$data['uid']])->order("intime desc")->select();
            success($data);
        }
    }

    /**
     *新增乘机人信息
     */
    public function add_passenger(){
        if(IS_POST){
            checklogin();
            $data = $_POST;
            $model = D('Passenger')->auth();
            if(empty($data['id'])){
                $data['intime'] = date("Y-m-d H:i;s",time());
                $result = M('Passenger')->add($data);
                if($result){
                    success($result);
                }else{
                    error("新增信息失败");
                }
            }else{
                error("新增信息失败");
            }
        }
    }

    /**
     *乘机人集合查询
     */
    public function passenger_list(){
        if(IS_POST){
            $ids = I('ids');
            $ids = explode(',',$ids);
            $list = M('Passenger')->where(['id'=>['in',$ids]])->select();
            success($list);
        }
    }

    /**
     *编辑乘机人信息
     */
    public function edit_passenger(){
        $id = I('id');
        if(IS_POST){
            checklogin();
            $data = $_POST;
            $model = D('Passenger')->auth();
            if(!empty($data['id'])){
                $data['uptime'] = date("Y-m-d H:i;s",time());
                $result = M('Passenger')->where(['id'=>$data['id']])->save($data);
                if($result){
                    success($result);
                }else{
                    error("编辑乘机人信息失败");
                }
            }else{
                error("编辑信息失败");
            }
        }else{
            $re = M('Passenger')->where(['id'=>$id])->find();
            if(!empty($re)){
                success($re);
            }else{
                error("参数错误");
            }
        }
    }

    /**
     *切换乘机人默认状态
     */
    public function change_passenger_default(){
        if(IS_POST){
            $member = checklogin();
            $is_default = I('is_default');
            if($is_default == '1'){
                $count = M('Passenger')->where(['uid'=>$member['member_id'],'is_default'=>2])->count();
                if($count > 4)       error("最多只能选择5个乘机人");

            }
            $id = I('id');
            $status = M('Passenger')->where(['id'=>$id])->getField('is_default');
            $abs = 3 - $status;
            if($abs == '2'){
                cookie('abs',$abs,300);
            }
            $arr = ['普通','默认'];
            $result = M('Passenger')->where(['id'=>$id])->save(['is_default'=>$abs]);
            if($result){
                success($arr[2-$status]);
            }else{
                error("切换失败");
            }
        }
    }

    /**
     *查找默认的乘机人
     */
    public function default_passenger(){
        if(IS_POST){
            $member = checklogin();
            $abs = cookie('abs');
            if(empty($abs)){
                M('Passenger')->where(['uid'=>$member['member_id']])->save(['is_default'=>'1']);
            }
            $list = M('Passenger')->where(['uid'=>$member['member_id'],'is_default'=>'2'])->select();
            success($list);
        }
    }

    /**
     *删除乘机人
     */
    public function del_passenger(){
        if(IS_POST){
            $member = checklogin();
            $id = I('id');
            $result = M('Passenger')->where(['id'=>$id])->delete();
            if($result){
                success("删除记录成功");
            }else{
                error("删除失败");
            }
        }
    }

    /**
     *根据身份证判断年龄
     */
    public function check_age(){
        if(IS_POST){
            $member = checklogin();
            $ids = I('ids');
            if(empty($ids)){
                $count = 0;
            }else{
                $ids = explode(',',$ids);
                $count = count($ids);
            }
            $data = M('Passenger')->where(['id'=>['in',$ids]])->select();
            $a = 0;
            $b = 0;
            foreach($data as $key=>$val){
                if($val['type'] == '身份证'){
                    if(isMeetAgeByIDCard($val['card'],2)==false){
                        $a++;
                    }
                    if(isMeetAgeByIDCard($val['card'],2) == true && isMeetAgeByIDCard($val['card'],12)==false){
                        $b++;
                    }
                }
            }
            $c = $count - $a - $b;
            success(['cheng_ren'=>$c,'er_tong'=>$b,'ying_er'=>$a]);
        }
    }

    /**
     *@保险和附加收费显示
     */
    public function airport_attach(){
        if(IS_POST) {
            $go_city = I('go_city');
            $arrive_city = I('arrive_city');
            $check1 = M('Area')->where(['city'=>['like','%'.$go_city.'%']])->select();
            $check2 = M('Area')->where(['city'=>['like','%'.$arrive_city.'%']])->select();
            foreach($check1 as $key=>$val){
                $city1[] = $val['type'];
            }
            foreach($check2 as $key=>$val){
                $city2[] = $val['type'];
            }
            if(in_array('2',$city1))    $type = 2;
            if(in_array('2',$city2))    $type = 2;
            if ($type != 2) {
                $type = 1;
            }
            $data = M('AirportAttach')
                ->field("attach_id,title,explain,price,danwei,content")
                ->where(['type' => $type, 'is_del' => 1])->select();
            success($data);
        }
    }


    public function place_order(){
        if(IS_POST){
            $member = checklogin();
            $data['name'] = I('name');
            $data['phone'] = I('phone');
            $data['num'] = I('num');
            $data['attach_id'] = I('attach_id');
            $data['mid'] = I('uid');
            $data['passenger_detail'] = I('passenger_detail');
            if(empty($data['name']))        error("联系人姓名不能为空");
            if(empty($data['phone']))       error("联系人手机号不能为空");
            if(!preg_match('/^1[34578]{1}\d{9}$/',$data['phone']))  error("联系人手机错误");
//            if(empty($data['paid']))        error("订单实收金额不能为空");
            if(empty($data['num']))         error("乘机人数不能为空");
            if(empty($data['passenger_detail']))    error("乘机人详情不能为空");
            $passenger_id = I('passenger_id');
            if(empty($passenger_id))        error("乘机人信息不能为空"); else $passenger_id = explode(',',$passenger_id);
            if(count($passenger_id) != $data['num'])    error("乘机人数量不相符");
            $data['coupon_id'] = I('coupon_id');
            $data['score'] = I('score');
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("用户积分不够，不能使用积分");
            }
            $code['detail'] = I('detail');
            $code['detail'] = strip_tags($code['detail']);
            $code['detail'] = str_replace('&quot;','"',$code['detail']);
            if(empty($code['detail']))      error("航班详情不能为空");
            $detail = json_decode($code['detail'],true);  //单程
            $data['amount'] = ($detail['fares'][0]['sale']+$detail['fares'][0]['taxs']) * $data['num'];
            $detail2 = I('detail2');
            $detail2 = strip_tags($detail2);
            $detail2 = str_replace('&quot;','"',$detail2);
            if(!empty($data['attach_id'])){
                $attach_id  = explode(',',$data['attach_id']);
                foreach($attach_id as $val){
                    $attach_price = M('AirportAttach')->where(['attach_id'=>$val])->getField('price');
                    $attach_total += $attach_price * $data['num'];
                }
            }
            if(!empty($detail2))    { //返程
                $code['type'] = '2';
                $detail2 = json_decode($detail2,true);
               $data['amount'] += ($detail2['fares'][0]['sale']) * $data['num'];
                $attach_total += $attach_total;
            }
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("用户积分不够，不能使用积分"); //查询积分
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                $ratio = $install_score['money']/$install_score['score'];
            }
            if(!empty($data['coupon_id'])){  //查询优惠券
                $coupon = M('MemberCoupon')->alias('a')
                    ->field('b.value,b.limit_value,b.type')
                    ->join("LEFT JOIN __COUPON__ b on a.coupon_id = b.id")
                    ->where(['a.id'=>$data['coupon_id']])->find();
                if($data['amount']<$coupon['limit_value'])  error('定单不满足优惠券使用规则');
                if($coupon['type'] !=1)          error("优惠券使用场景错误");
            }
            $data['amount'] = $data['amount'] + $attach_total;
            $data['paid'] = $data['amount'] - $coupon['value'] - $data['score']*$ratio;
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['order_no'] = date("YmdHis",time()).rand(100000,999999);
            M()->startTrans();
            $order_id = M('AirportOrder')->add($data);
            if($order_id){
                foreach($passenger_id as $val){
                    $result = M('AirportOrderPassenger')->add(['passenger_id'=>$val,'order_id'=>$order_id]);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                $code['order_id'] = $order_id;
                $voyageId1 = explode('-',$detail['voyageId']);
                $gocity1 = M('Area')->where(['code'=>['like','%'.$voyageId1[0].'%']])->find();
                $arrivecity1 = M('Area')->where(['code'=>['like','%'.$voyageId1[count($voyageId1)-1].'%']])->find();
                $code['go_city'] = $gocity1['city'];
                $is_nation = 1;
                if($gocity1['type'] == 2 || $arrivecity1['type'] ==2){
                    $is_nation = 2;
                }
                $code['go_hangzhan'] = $detail['sements'][0]['depPort']['portDesc'].' '.$detail['sements'][0]['depPort']['terminal'];
                $code['go_time'] = $detail['sements'][0]['depPort']['flightTime'];
                $code['arrive_city'] = $arrivecity1['city'];
                $code['arrive_hangzhan'] = $detail['sements'][count($detail['sements'])-1]['arrPort']['portDesc'].' '.$detail['sements'][count($detail['sements'])-1]['arrPort']['terminal'];;
                $code['arrive_time'] = $detail['sements'][count($detail['sements'])-1]['arrPort']['flightTime'];
                $code['hangban'] = $detail['flightNo'];
                $code['is_nation'] = $is_nation;
                switch($detail['cabin']['cabinType']){
                    case 0 :
                        $code['cangwei'] = '经济舱';
                        break;
                    case 1 :
                        $code['cangwei'] = '商务舱';
                        break;
                    case 2 :
                        $code['cangwei'] = '头等舱';
                        break;
                }
                $code['price'] = $detail['fares'][0]['sale'];
                $code['shui'] = $detail['fares'][0]['taxs'];
                $code['flytime'] = $detail['flyTime'];
                $code['intime']   = $data['intime'];
                $result = M('AirportOrderFlight')->add($code);
                if(!$result){
                    M()->rollback();
                    error("下单失败");
                }
                if(!empty($detail2)){
                    $code2['type'] = 3;
                    $code2['order_id'] = $order_id;
                    $voyageId2 = explode('-',$detail2['voyageId']);
                    $gocity2 = M('Area')->where(['code'=>['like','%'.$voyageId2[0].'%']])->find();
                    $arrivecity2 = M('Area')->where(['code'=>['like','%'.$voyageId1[count($voyageId2)-1].'%']])->find();
                    $code2['go_city'] = $gocity2['city'];
                    $code2['go_hangzhan'] = $detail2['sements'][0]['depPort']['portDesc'].' '.$detail2['sements'][0]['depPort']['terminal'];
                    $code2['go_time'] = $detail2['sements'][0]['depPort']['flightTime'];
                    $code2['arrive_city'] = $arrivecity2['city'];
                    $code2['arrive_hangzhan'] = $detail2['sements'][count($detail2['sements'])-1]['arrPort']['portDesc'].' '.$detail['sements'][count($detail2['sements'])-1]['arrPort']['terminal'];;
                    $code2['arrive_time'] = $detail2['sements'][count($detail2['sements'])-1]['arrPort']['flightTime'];
                    $code2['hangban'] = $detail2['flightNo'];
                    $code2['is_nation'] = $is_nation;
                    switch($detail2['cabin']['cabinType']){
                        case 0 :
                            $code2['cangwei'] = '经济舱';
                            break;
                        case 1 :
                            $code2['cangwei'] = '商务舱';
                            break;
                        case 2 :
                            $code2['cangwei'] = '头等舱';
                            break;
                    }
                    $code2['price'] = $detail2['fares'][0]['sale'];
                    $code2['shui'] = $detail2['fares'][0]['taxs'];
                    $code2['flytime'] = $detail2['flyTime'];
                    $code2['intime']   = $data['intime'];
                    $result = M('AirportOrderFlight')->add($code2);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                if (!empty($data['score'])) {
                    $score = $member['score'] - $data['score']; //用户现有积分
                    M('Member')->where(['member_id' => $member['member_id']])->save(['score' => $score]);
                }
                if (!empty($data['coupon_id'])) {
                    M('MemberCoupon')->where(['id' => $data['coupon_id']])->save(['status' => 2, 'uptime' => date("Y-m-d H:i:s", time())]);
                }
                $message = '【途老大】您的订单已提交，工作人员会在15分钟内确认您的预定是否成功，预定成功后可在我的订单中待支付进行付款，如有问题请联系客服人员。';
                set_message($member['member_id'],$message);
                $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                $accessToken= S('globals_access_token');
                if(empty($accessToken))     $accessToken = $weixin->getAccessToken();
                $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
                $tmp = array(
                    'touser' => $member['openid'],
                    'template_id'=> 'z0pTP4gru7tfQsVOZiOp7AzVjXc0jVhJTxeH_AOky1k',
                    'url' => C('IMG_PREFIX').'/mobile/index.html#/Aorders',
                    'topcolor' => '#FF0000',
                    'data'=> array(
                        'first'=> array('value'=>'你的机票订单下单成功','color'=>'#FF0000'),
                        'from'=> array('value'=>$member['nickname'],'color'=>'#FF0000'),
                        'event'=> array('value'=>'需要等待30分钟确认机位，稍后请留意短信提示。','color'=>'#FF0000'),
                        'remark'=> array('value'=>'感谢你的下单，请留意订单提示信息。','color'=>'#FF0000'),
                    )
                );
                $tmp = json_encode($tmp);
                $result = curl_post_json($url,$tmp);
                M()->commit();
                success("需要等待30分钟确认机位，稍后请留意短信提示。");
            }else{
                M()->rollback();
                error("下单失败");
            }
        }
    }

    /**
     *访问机票接口
     */
    public function curl_plane(){
        if(IS_POST) {
            $go_city = I('go_city');
            if(empty($go_city))                 error("起飞城市不能为空");
            $data['flightType'] = I('flightType');
            $data['cabinType'] = I('cabinType');
            $data['depPort'] = I('depPort');
            if(empty($data['depPort']))         error("起飞城市不能为空");
            $data['arrPort'] = I('arrPort');
            if(empty($data['arrPort']))         error("目的城市不能为空");
            $data['depDate'] = I('depDate');
            if(empty($data['depDate']))         error("起飞时间不能为空");
            $data['retDate'] = I('retDate');
            $depPort = $data['depPort'];
            $arrPort = $data['arrPort'];
            $depDate = $data['depDate'];
            switch ($data['flightType']) {
                case 0 :
                    $flightType = 'OW';
                    break;
                case 1 :
                    $flightType = 'RT';
                    break;
            }
            switch ($data['cabinType']) {
                case 0 :
                    $cabinType = 'Economy';
                    break;
                case 1 :
                    $cabinType = 'Business';
                    break;
                case 2 :
                    $cabinType = 'FirstClass';
                    break;
            }
            if($data['flightType'] == '0')     $data['retDate'] = $data['depDate'];
            $retDate = $data['retDate'];
            if($data['flightType'] == '1'){
                if(strtotime($retDate)-strtotime($depDate)<24*3600){
                    error("返程时间必须大于出发时间");
                }
            }
            $data['sessionId'] = "$flightType,$cabinType,$depPort,$arrPort,$depDate,$retDate";
            $data = json_encode($data);
            $url = 'http://15901s125v.51mypc.cn:46808/';
            $result = curl_post_json($url,$data);
            if (!empty($result)) {
                $result = strtr($result, array('
	\t' => ''));
                $result = json_decode($result, true);
                foreach($result['freightList'] as $key => $val){
                    if($go_city != $val['sements'][0]['depPort']['cityDesc'])     unset($result['freightList'][$key]);
                }
                $result['freightList'] = array_values($result['freightList']);
                session('plane', $result);
                echo json_encode($result);
            } else {
                echo '{"status":"error","info":"未获取到数据"}';
            }
        }
    }

    /**
     *机票筛选
     */
    public function check_plane(){
        $plane = session('plane');
//        $plane = json_decode($plane, true);
        if(IS_POST){
            !empty($_POST['from_time'])  && $from_time = I('from_time');
            !empty($_POST['to_time'])  && $to_time = I('to_time');
            !empty($_POST['jichang'])  && $jichang = I('jichang');
            $jichang = trimall($jichang);
            !empty($_POST['date'])    &&  $date = I('date');
            !empty($_POST['price'])    &&  $price = I('price');
//            !empty($_POST['cangwei'])  && $cangwei = I('cangwei');
            !empty($_POST['flightNo']) && $flightNo = I('flightNo');
            !empty($_POST['company'])  && $company = I('company');
            foreach($plane['freightList'] as $key => $val){
                if(!empty($date)){
                    $id[$key] = $key;
                    $val['time'] = strtotime(str_replace('T',' ',$val['sements'][0]['depPort']['flightTime']));
                    $plane['freightList'][$key]['time'] = $val['time'];
                    $refer[$key] = $val['time'];
                }
                if(!empty($price)){
                    $val['price'] = $val['fares'][0]['sale'];
                    $plane['freightList'][$key]['price'] = $val['price'];
                }
                if(!empty($from_time)){
                    $time = strstr($val['sements'][0]['depPort']['flightTime'],'T');
                    $time = substr($time,1);
                    if(!in_quarters($time,$from_time,$to_time))     unset($plane['freightList'][$key]);
                }
                if(!empty($jichang)){
                    if($jichang != $val['sements'][0]['depPort']['cityDesc'].$val['sements'][0]['depPort']['portDesc'])     unset($plane['freightList'][$key]);
                }
                if(!empty($flightNo)){
                    if($flightNo != $val['sements'][0]['operat']['flightNo'] &&  $flightNo != $val['sements'][0]['market']['flightNo'])     unset($plane['freightList'][$key]);
                }
                if(!empty($company)){
                    if($company != $val['sements'][0]['operat']['airlineDesc'] &&  $company != $val['sements'][0]['market']['airlineDesc'])   unset($plane['freightList'][$key]);
                }
        }
            $plane['freightList'] = array_values($plane['freightList']);
            if(!empty($price)){
                    foreach($plane['freightList'] as $k => $v){
                        $id[$k] = $k;
                        $refer[$k] = $v['price'];
//                        unset($plane['freightList'][$k]);
                    }
                if($price == 1){
                    array_multisort($refer,SORT_NUMERIC,SORT_ASC,$id,SORT_STRING,SORT_ASC,$plane['freightList']);
                }else if($price == 2){
                    array_multisort($refer,SORT_NUMERIC,SORT_DESC,$id,SORT_STRING,SORT_DESC,$plane['freightList']);
                }
            }
            if(!empty($date)){
                foreach($plane['freightList'] as $k => $v){
                    $id[$k] = $k;
                    $refer[$k] = $v['time'];
//                    unset($plane['freightList'][$k]);
                }
                if($date == 1){
                    array_multisort($refer,SORT_NUMERIC,SORT_ASC,$id,SORT_STRING,SORT_ASC,$plane['freightList']);
                }else if($date == 2){
                    array_multisort($refer,SORT_NUMERIC,SORT_DESC,$id,SORT_STRING,SORT_DESC,$plane['freightList']);
                }
            }
            $plane['freightList'] = array_values($plane['freightList']);
            success($plane);

        }else{
            $go_city = $plane['freightList'][0]['sements'][0]['depPort']['cityDesc'];
            $company = [];
            $flightNo = [];
            foreach($plane['freightList'] as $key=>$val){
                foreach($val['sements'] as $k=>$v){
                    $company[] = $v['operat']['airlineDesc'];
                    $company[] = $v['market']['airlineDesc'];
                    $flightNo[] = $v['operat']['flightNo'];
                    $flightNo[] = $v['market']['flightNo'];
                }
            }
            $company = array_values(array_unique($company));        //航空公司
            $flightNo = array_values(array_unique($flightNo));        //航班类型
            $arrive_city = $plane['freightList'][0]['sements'][0]['arrPort']['cityDesc'];
            $go_jichang = M('Area')->field('jichang')->where(['city'=>['like','%'.$go_city.'%']])->select();    //出发机场
            $arrive_jichang = M('Area')->field('jichang')->where(['city'=>['like','%'.$arrive_city.'%']])->select();  //到达机场
            success(['go_jichang'=>$go_jichang,'arrive_jichang'=>$arrive_jichang,'company'=>$company,'flightNo'=>$flightNo]);
        }
    }

    public function rule(){
        if(IS_POST){
            $type = I('type');
            if(!in_array($type,array('1','2')))   error("参数错误");
            $rule = M('Notice')->field('title,content')->where(['id'=>4])->find();
            $rule['content'] = htmlspecialchars_decode($rule['content']);
            success($rule);
        }
    }

    /**
     *机票申请
     */
    public function apply_plane_order(){
        if(IS_POST){
            $member = checklogin();
            $data['name'] = I('name');
            $data['phone'] = I('phone');
            $data['num'] = I('num');
            $data['attach_id'] = I('attach_id');
            $data['mid'] = I('uid');
            $data['passenger_detail'] = I('passenger_detail');
            if(empty($data['name']))        error("联系人姓名不能为空");
            if(empty($data['phone']))       error("联系人手机号不能为空");
            if(!preg_match('/^1[34578]{1}\d{9}$/',$data['phone']))  error("联系人手机错误");
//            if(empty($data['paid']))        error("订单实收金额不能为空");
            if(empty($data['num']))         error("乘机人数不能为空");
            if(empty($data['passenger_detail']))    error("乘机人详情不能为空");
            $passenger_id = I('passenger_id');
            if(empty($passenger_id))        error("乘机人信息不能为空"); else $passenger_id = explode(',',$passenger_id);
            if(count($passenger_id) != $data['num'])    error("乘机人数量不相符");
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['order_no'] = date("YmdHis",time()).rand(100000,999999);
            $go_city = I('go_city');
            if(empty($go_city))             error("出发城市不能为空");
            $arrive_city = I('arrive_city');
            if(empty($arrive_city))         error("目的城市不能为空");
            $date1 = I('date1');
            $date2 = I('date2');
            if(empty($date1))               error("出发日期不能为空");
            $cangwei = I('cangwei');
            M()->startTrans();
            $data['is_special'] = '2';
            $order_id = M('AirportOrder')->add($data);
            $is_nation = 1;
            $go_city = M('Area')->where(['city'=>['like','%'.$go_city.'%']])->select();
            foreach($go_city as $k=>$v){
                $type1[] = $v['type'];
            }
            $arrive_city = M('Area')->where(['city'=>['like','%'.$arrive_city.'%']])->select();
            foreach($arrive_city as $k=>$v){
                $type2[] = $v['type'];
            }
            if(in_array('2',$type1) || in_array('2',$type2)){
                $is_nation = 2;
            }
            if($order_id){
                foreach($passenger_id as $val){
                    $result = M('AirportOrderPassenger')->add(['passenger_id'=>$val,'order_id'=>$order_id]);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                if(!empty($date2))     $code['type'] = 2;
                $code['order_id'] = $order_id;
                $code['go_city'] = $go_city;
                $code['go_time'] = $date1.'T';
                $code['arrive_city'] = $arrive_city;
                $code['is_nation'] =  $is_nation;
                switch($cangwei){
                    case 0 :
                        $code['cangwei'] = '经济舱';
                        break;
                    case 1 :
                        $code['cangwei'] = '商务舱';
                        break;
                    case 2 :
                        $code['cangwei'] = '头等舱';
                        break;
                }
                $code['intime']   = $data['intime'];
                $result = M('AirportOrderFlight')->add($code);
                if(!$result){
                    M()->rollback();
                    error("下单失败");
                }
                if(!empty($date2)){
                    $code2['type'] = 3;
                    $code2['order_id'] = $order_id;
                    $code2['go_city'] = $arrive_city;
                    $code2['go_time'] = $date2.'T';
                    $code2['arrive_city'] = $go_city;
                    $code2['is_nation'] =  $is_nation;
                    switch($cangwei){
                        case 0 :
                            $code2['cangwei'] = '经济舱';
                            break;
                        case 1 :
                            $code2['cangwei'] = '商务舱';
                            break;
                        case 2 :
                            $code2['cangwei'] = '头等舱';
                            break;
                    }
                    $code2['intime']   = $data['intime'];
                    $result = M('AirportOrderFlight')->add($code2);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                $message = '【途老大】您的订单已提交，工作人员会在15分钟内确认您的预定是否成功，预定成功后可在我的订单中待支付进行付款，如有问题请联系客服人员。';
                set_message($member['member_id'],$message);
                $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                $accessToken= S('globals_access_token');
                if(empty($accessToken))     $accessToken = $weixin->getAccessToken();
                $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
                $tmp = array(
                    'touser' => $member['openid'],
                    'template_id'=> 'z0pTP4gru7tfQsVOZiOp7AzVjXc0jVhJTxeH_AOky1k',
                    'url' => C('IMG_PREFIX').'/mobile/index.html#/Aorders',
                    'topcolor' => '#FF0000',
                    'data'=> array(
                        'first'=> array('value'=>'你的机票订单申请成功','color'=>'#FF0000'),
                        'from'=> array('value'=>$member['nickname'],'color'=>'#FF0000'),
                        'event'=> array('value'=>'需要等待30分钟确认机位，稍后请留意短信提示。','color'=>'#FF0000'),
                        'remark'=> array('value'=>'感谢你的下单，请留意订单提示信息。','color'=>'#FF0000'),
                    )
                );
                $tmp = json_encode($tmp);
                $result = https_request($url,$tmp);
                M()->commit();
                success("需要等待30分钟确认机位，稍后请留意短信提示。");
            }else{
                M()->rollback();
                error("下单失败");
            }
        }
    }

    /**
     *特价机票申请
     */
    public function apply_specil_plane_order(){
        if(IS_POST){
            $member = checklogin();
            $id = I('id');
            if(empty($id))                  error("特价机票id不能为空");
            $special_ticket = M("SpecialTicket")->where(['id'=>$id])->find();

            if(empty($special_ticket))      error("特价机票id不能为空");
            $date1 = I('date1');
            $hangban1 = I('hangban1');
            if(empty($date1))                error("启程日期不能为空");
            if(empty($hangban1))                error("启程航司不能为空");
            if($special_ticket['type'] == '2'){
                $date2 = I('date2');
                if(empty($date2))           error("返程日期不能为空");
                $hangban2 = I('hangban2');
                if(empty($hangban2))           error("返程航司不能为空");
            }
            $data['name'] = I('name');
            $data['phone'] = I('phone');
            $data['num'] = I('num');
            $data['attach_id'] = I('attach_id');
            $data['mid'] = I('uid');
            $data['passenger_detail'] = I('passenger_detail');
            if(empty($data['name']))        error("联系人姓名不能为空");
            if(empty($data['phone']))       error("联系人手机号不能为空");
            if(!preg_match('/^1[34578]{1}\d{9}$/',$data['phone']))  error("联系人手机错误");
//            if(empty($data['paid']))        error("订单实收金额不能为空");
            if(empty($data['num']))         error("乘机人数不能为空");
            if(empty($data['passenger_detail']))    error("乘机人详情不能为空");
            $passenger_id = I('passenger_id');
            if(empty($passenger_id))        error("乘机人信息不能为空"); else $passenger_id = explode(',',$passenger_id);
            if(count($passenger_id) != $data['num'])    error("乘机人数量不相符");
            $data['coupon_id'] = I('coupon_id');
            $data['score'] = I('score');
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("用户积分不够，不能使用积分");
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['order_no'] = date("YmdHis",time()).rand(100000,999999);
            if(empty($special_ticket))      error("没找到特价机票");
            $is_nation = 1;
            $go_city = M('Area')->where(['city'=>['like','%'.$special_ticket['go_city'].'%']])->select();
            foreach($go_city as $k=>$v){
                $type1[] = $v['type'];
            }
            $arrive_city = M('Area')->where(['city'=>['like','%'.$special_ticket['arrive_city'].'%']])->select();
            foreach($arrive_city as $k=>$v){
                $type2[] = $v['type'];
            }
            if(in_array('2',$type1) || in_array('2',$type2)){
                $is_nation = 2;
            }
            if($special_ticket['type'] == 2)    $code1['type'] = 2;
            if(!empty($data['attach_id'])){
                $attach_id  = explode(',',$data['attach_id']);
                foreach($attach_id as $val){
                    $attach_price = M('AirportAttach')->where(['attach_id'=>$val])->getField('price');
                    $attach_total += $attach_price * $data['num'];
                }
            }
            $data['amount'] = $data['num'] * $special_ticket['price'];
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("用户积分不够，不能使用积分"); //查询积分
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                $ratio = $install_score['money']/$install_score['score'];
            }
            if(!empty($data['coupon_id'])){  //查询优惠券
                $coupon = M('MemberCoupon')->alias('a')
                    ->field('b.value,b.limit_value,b.type')
                    ->join("LEFT JOIN __COUPON__ b on a.coupon_id = b.id")
                    ->where(['a.id'=>$data['coupon_id']])->find();
                if($data['amount']<$coupon['limit_value'])  error('定单不满足优惠券使用规则');
                if($coupon['type'] !=1)          error("优惠券使用场景错误");
            }

            $data['amount'] = $data['amount'] + $attach_total;
            $data['paid'] = $data['amount'] - $coupon['value'] - $data['score']*$ratio;
            $data['is_special'] = '3';
            M()->startTrans();
            $order_id = M('AirportOrder')->add($data);
            if($order_id){
                foreach($passenger_id as $val){
                    $result = M('AirportOrderPassenger')->add(['passenger_id'=>$val,'order_id'=>$order_id]);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                if($special_ticket['type'] == 2)     $code['type'] = 2;
                $code['order_id'] = $order_id;
                $code['go_city'] = $special_ticket['go_city'];
                $code['go_time'] = $date1.'T';
                $code['arrive_city'] = $special_ticket['arrive_city'];
                $code['cangwei'] =$special_ticket['cangwei'];
                $code['hangban'] = $hangban1.'|';
                $code['intime']   = $data['intime'];
                $code['is_nation']   = $is_nation;
                $result = M('AirportOrderFlight')->add($code);
                if(!$result){
                    M()->rollback();
                    error("下单失败");
                }
                if($special_ticket['type'] == 2){
                    $code2['type'] = 3;
                    $code2['order_id'] = $order_id;
                    $code2['go_city'] = $special_ticket['arrive_city'];
                    $code2['go_time'] = $date2.'T';
                    $code2['arrive_city'] = $special_ticket['go_city'];
                    $code2['cangwei'] = $special_ticket['cangwei'];
                    $code2['hangban'] = $hangban2.'|';
                    $code2['intime']   = $data['intime'];
                    $code2['is_nation']   = $is_nation;
                    $result = M('AirportOrderFlight')->add($code2);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                if (!empty($data['score'])) {
                    $score = $member['score'] - $data['score']; //用户现有积分
                    M('Member')->where(['member_id' => $member['member_id']])->save(['score' => $score]);
                }
                if (!empty($data['coupon_id'])) {
                    M('MemberCoupon')->where(['id' => $data['coupon_id']])->save(['status' => 2, 'uptime' => date("Y-m-d H:i:s", time())]);
                }
                $message = '【途老大】您的订单已提交，工作人员会在15分钟内确认您的预定是否成功，预定成功后可在我的订单中待支付进行付款，如有问题请联系客服人员。';
                set_message($member['member_id'],$message);
                $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                $accessToken= S('globals_access_token');
                if(empty($accessToken))     $accessToken = $weixin->getAccessToken();
                $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
                $tmp = array(
                    'touser' => $member['openid'],
                    'template_id'=> 'z0pTP4gru7tfQsVOZiOp7AzVjXc0jVhJTxeH_AOky1k',
                    'url' => C('IMG_PREFIX').'/mobile/index.html#/Aorders',
                    'topcolor' => '#FF0000',
                    'data'=> array(
                        'first'=> array('value'=>'你的特价机票订单申请成功','color'=>'#FF0000'),
                        'from'=> array('value'=>$member['nickname'],'color'=>'#FF0000'),
                        'event'=> array('value'=>'需要等待30分钟确认机位，稍后请留意短信提示。','color'=>'#FF0000'),
                        'remark'=> array('value'=>'感谢你的下单，请留意订单提示信息。','color'=>'#FF0000'),
                    )
                );
                $tmp = json_encode($tmp);
                $result = curl_post_json($url,$tmp);
                M()->commit();
                success("需要等待30分钟确认机位，稍后请留意短信提示。");
            }else{
                M()->rollback();
                error("下单失败");
            }
        }
    }

    public function post_plane(){
        if(IS_POST) {
            $go_city = I('go_city');
            if(empty($go_city))                 error("起飞城市不能为空");
            $data['flightType'] = I('flightType');
            $data['cabinType'] = I('cabinType');
            $data['depPort'] = I('depPort');
            if(empty($data['depPort']))         error("起飞城市不能为空");
            $data['arrPort'] = I('arrPort');
            if(empty($data['arrPort']))         error("目的城市不能为空");
            $data['depDate'] = I('depDate');
            if(empty($data['depDate']))         error("起飞时间不能为空");
            $data['retDate'] = I('retDate');
            $depPort = $data['depPort'];
            $arrPort = $data['arrPort'];
            $depDate = $data['depDate'];
            switch ($data['flightType']) {
                case 0 :
                    $flightType = 'OW';
                    break;
                case 1 :
                    $flightType = 'RT';
                    break;
            }
            switch ($data['cabinType']) {
                case 0 :
                    $cabinType = 'Economy';
                    break;
                case 1 :
                    $cabinType = 'Business';
                    break;
                case 2 :
                    $cabinType = 'FirstClass';
                    break;
            }
            if($data['flightType'] == '0')     $data['retDate'] = $data['depDate'];
            $retDate = $data['retDate'];
            if($data['flightType'] == '1'){
                if(strtotime($retDate)-strtotime($depDate)<24*3600){
                    error("返程时间必须大于出发时间");
                }
            }
            $data['sessionId'] = "$flightType,$cabinType,$depPort,$arrPort,$depDate,$retDate";
            $data = json_encode($data);
            $ch = curl_init('http://15901s125v.51mypc.cn:46808/');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20); //超时
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
            $result = curl_exec($ch);
            curl_close($ch);//释放句柄
            if (!empty($result)) {
                echo $result;
                die;
                $result = strtr($result, array('
	\t' => ''));
                echo $result;
            } else {
                echo '{"status":"error","info":"未获取到数据"}';
            }
        }
    }

    public function place_nation_order(){
        if(IS_POST){
            $member = checklogin();
            $data['name'] = I('name');
            $data['phone'] = I('phone');
            $data['num'] = I('num');
            $data['attach_id'] = I('attach_id');
            $data['mid'] = I('uid');
            $data['passenger_detail'] = I('passenger_detail');
            if(empty($data['name']))        error("联系人姓名不能为空");
            if(empty($data['phone']))       error("联系人手机号不能为空");
            if(!preg_match('/^1[34578]{1}\d{9}$/',$data['phone']))  error("联系人手机错误");
//            if(empty($data['paid']))        error("订单实收金额不能为空");
            if(empty($data['num']))         error("乘机人数不能为空");
            if(empty($data['passenger_detail']))    error("乘机人详情不能为空");
            $passenger_id = I('passenger_id');
            if(empty($passenger_id))        error("乘机人信息不能为空"); else $passenger_id = explode(',',$passenger_id);
            if(count($passenger_id) != $data['num'])    error("乘机人数量不相符");
            $data['coupon_id'] = I('coupon_id');
            $data['score'] = I('score');
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("用户积分不够，不能使用积分");
            }
            $code['detail'] = I('detail');
            $code['detail'] = strip_tags($code['detail']);
            $code['detail'] = str_replace('&quot;','"',$code['detail']);
            if(empty($code['detail']))      error("航班详情不能为空");
            $detail = json_decode($code['detail'],true);  //单程
            $data['amount'] = ($detail['fares'][0]['sale']+$detail['fares'][0]['taxs']) * $data['num'];
            $detail2 = I('detail2');
            $detail2 = strip_tags($detail2);
            $detail2 = str_replace('&quot;','"',$detail2);
            if(!empty($data['attach_id'])){
                $attach_id  = explode(',',$data['attach_id']);
                foreach($attach_id as $val){
                    $attach_price = M('AirportAttach')->where(['attach_id'=>$val])->getField('price');
                    $attach_total += $attach_price * $data['num'];
                }
            }
            if(!empty($detail2)){ //返程
                $code['type'] = '2';
                $detail2 = json_decode($detail2,true);
//                $data['amount'] += ($detail2['fares'][0]['sale'] + $detail2['fares'][0]['taxs']) * $data['num'];
                $attach_total += $attach_total;
            }
            if(!empty($data['score'])){
                if($data['score']>$member['score'])   error("用户积分不够，不能使用积分"); //查询积分
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                $ratio = $install_score['money']/$install_score['score'];
            }
            if(!empty($data['coupon_id'])){  //查询优惠券
                $coupon = M('MemberCoupon')->alias('a')
                    ->field('b.value,b.limit_value,b.type')
                    ->join("LEFT JOIN __COUPON__ b on a.coupon_id = b.id")
                    ->where(['a.id'=>$data['coupon_id']])->find();
                if($data['amount']<$coupon['limit_value'])  error('定单不满足优惠券使用规则');
                if($coupon['type'] !=1)          error("优惠券使用场景错误");
            }
            $data['amount'] = $data['amount'] + $attach_total;
            $data['paid'] = $data['amount'] - $coupon['value'] - $data['score']*$ratio;
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['order_no'] = date("YmdHis",time()).rand(100000,999999);
            M()->startTrans();
            $order_id = M('AirportOrder')->add($data);
            if($order_id){
                foreach($passenger_id as $val){
                    $result = M('AirportOrderPassenger')->add(['passenger_id'=>$val,'order_id'=>$order_id]);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                $code['order_id'] = $order_id;
                $detail['voyageId'] = explode('/',$detail['voyageId'])[0];
                $voyageId1 = explode('-',$detail['voyageId']);
                $gocity1 = M('Area')->where(['code'=>['like','%'.$voyageId1[0].'%']])->find();
                $arrivecity1 = M('Area')->where(['code'=>['like','%'.$voyageId1[count($voyageId1)-1].'%']])->find();
                $code['go_city'] = $gocity1['city'];
                $is_nation = 1;
                if($gocity1['type'] == 2 || $arrivecity1['type'] ==2){
                    $is_nation = 2;
                }
                $code['go_hangzhan'] = $detail['sements'][0]['depPort']['portDesc'].' '.$detail['sements'][0]['depPort']['terminal'];
                $code['go_time'] = $detail['sements'][0]['depPort']['flightTime'];
                $code['arrive_city'] = $arrivecity1['city'];
                foreach($detail['sements'] as $k=>$v){
                    if($v['segType'] == '1'){
                        $key = $k;
                        break;
                    }
                }
                $code['arrive_hangzhan'] = $detail['sements'][$key-1]['arrPort']['portDesc'].' '.$detail['sements'][$key-1]['arrPort']['terminal'];;
                $code['arrive_time'] = $detail['sements'][$key-1]['arrPort']['flightTime'];
                $code['hangban'] = explode('/',$detail['flightNo'])[0];
                $code['is_nation'] = $is_nation;
                switch($detail['cabin']['cabinType']){
                    case 0 :
                        $code['cangwei'] = '经济舱';
                        break;
                    case 1 :
                        $code['cangwei'] = '商务舱';
                        break;
                    case 2 :
                        $code['cangwei'] = '头等舱';
                        break;
                }
                $code['price'] = $detail['fares'][0]['sale'];
                $code['shui'] = $detail['fares'][0]['taxs'];
                $code['flytime'] = '往返程共'.$detail['flyTime'];
                $code['intime']   = $data['intime'];
                $result = M('AirportOrderFlight')->add($code);
                if(!$result){
                    M()->rollback();
                    error("下单失败");
                }
                if(!empty($detail2)){
                    $code2['type'] = 3;
                    $code2['order_id'] = $order_id;
                    $detail2['voyageId'] = explode('/',$detail2['voyageId']);
                    $voyageId2 = explode('-',$detail2['voyageId'][1]);
                    $gocity2 = M('Area')->where(['code'=>['like','%'.$voyageId2[0].'%']])->find();
                    $arrivecity2 = M('Area')->where(['code'=>['like','%'.$voyageId2[count($voyageId2)-1].'%']])->find();
                    $code2['go_city'] = $gocity2['city'];
                    $code2['go_hangzhan'] = $detail2['sements'][$k]['depPort']['portDesc'].' '.$detail2['sements'][$k]['depPort']['terminal'];
                    $code2['go_time'] = $detail2['sements'][$k]['depPort']['flightTime'];
                    $code2['arrive_city'] = $arrivecity2['city'];
                    $code2['arrive_hangzhan'] = $detail2['sements'][count($detail2['sements'])-1]['arrPort']['portDesc'].' '.$detail['sements'][count($detail2['sements'])-1]['arrPort']['terminal'];;
                    $code2['arrive_time'] = $detail2['sements'][count($detail2['sements'])-1]['arrPort']['flightTime'];
                    $code2['hangban'] = explode('/',$detail['flightNo'])[1];
                    $code2['is_nation'] = $is_nation;
                    switch($detail2['cabin']['cabinType']){
                        case 0 :
                            $code2['cangwei'] = '经济舱';
                            break;
                        case 1 :
                            $code2['cangwei'] = '商务舱';
                            break;
                        case 2 :
                            $code2['cangwei'] = '头等舱';
                            break;
                    }
                    $code2['price'] = 0;
                    $code2['shui'] = 0;
                    $code2['flytime'] = 0;
                    $code2['intime']   = $data['intime'];
                    $result = M('AirportOrderFlight')->add($code2);
                    if(!$result){
                        M()->rollback();
                        error("下单失败");
                    }
                }
                if (!empty($data['score'])) {
                    $score = $member['score'] - $data['score']; //用户现有积分
                    M('Member')->where(['member_id' => $member['member_id']])->save(['score' => $score]);
                }
                if (!empty($data['coupon_id'])) {
                    M('MemberCoupon')->where(['id' => $data['coupon_id']])->save(['status' => 2, 'uptime' => date("Y-m-d H:i:s", time())]);
                }
                $message = '【途老大】您的订单已提交，工作人员会在15分钟内确认您的预定是否成功，预定成功后可在我的订单中待支付进行付款，如有问题请联系客服人员。';
                set_message($member['member_id'],$message);
                $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                $accessToken= S('globals_access_token');
                if(empty($accessToken))     $accessToken = $weixin->getAccessToken();
                $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
                $tmp = array(
                    'touser' => $member['openid'],
                    'template_id'=> 'z0pTP4gru7tfQsVOZiOp7AzVjXc0jVhJTxeH_AOky1k',
                    'url' => C('IMG_PREFIX').'/mobile/index.html#/Aorders',
                    'topcolor' => '#FF0000',
                    'data'=> array(
                        'first'=> array('value'=>'你的机票订单下单成功','color'=>'#FF0000'),
                        'from'=> array('value'=>$member['nickname'],'color'=>'#FF0000'),
                        'event'=> array('value'=>'需要等待30分钟确认机位，稍后请留意短信提示。','color'=>'#FF0000'),
                        'remark'=> array('value'=>'感谢你的下单，请留意订单提示信息。','color'=>'#FF0000'),
                    )
                );
                $tmp = json_encode($tmp);
                $result = curl_post_json($url,$tmp);
                M()->commit();
                success("需要等待30分钟确认机位，稍后请留意短信提示。");
            }else{
                M()->rollback();
                error("下单失败");
            }

        }
    }

    public function test1(){
        $data = array('cabinType'=>'0');
        $data = json_encode($data);
        $url = "http://15901s125v.51mypc.cn:46808/";
        $result = curl_post_json($url,$data);
        var_dump($result);
    }

}