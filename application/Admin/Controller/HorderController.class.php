<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/25
 * Time: 18:02
 */

namespace Admin\Controller;


use Com\WechatAuth;

class HorderController extends BaseController
{
    protected function check_diy($item){
        $code['appid'] = "xiangba";
        $code['appsecret'] = "41p$77!P@0h9P9G9zY";
        ksort($code);
        $count = count($code);
        $c = array_keys($code);
        $d = array_values($code);
        $b = '';
        foreach ($c as $k => $v) {
            if ($count - $k > 1) {
                $b .= $v . "=" . $d[$k] . "&";
            } else {
                $b .= $v . "=" . $d[$k];
            }
        }
        $data['appid'] = $code['appid'];
        $data['sign'] = md5($b);
        $data['items'] = $item;
        $data = json_encode($data);
        $url = 'http://api.crossprint.cn/files';
        $arr = curl_post_json($url,$data);
        return json_decode($arr,true);
    }

    protected function down_horder($map){

        $dat = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take,a.kuaidi,a.kuaidi_name,a.kuaidi_state')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->order("intime desc")->select();
        $str = '商品订单'.date('YmdHis');
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:filename={$str}.csv");
        echo "\xEF\xBB\xBF"."序号,订单号,订单收件人,收件人电话,订单总金额,实付金额,使用积分,成本价,下单会员,会员手机号,订单状态,取货方式,商品信息,快递公司,快递单号,快递状态,下单时间\n";
        foreach ($dat as $k=>$v){
            $goods = M('MallOrderDetail')->alias('a')
                ->field('a.goods_id,a.number,b.name,b.sale_price,b.thumb,a.kinds_id,b.kinds,b.code')
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.order_id'=>$v['id']])->select();
            foreach($goods as $key=>$val){
                $kinds_id = explode(',',$val['kinds_id']);
                $kinds = explode(',',$val['kinds']);
                $name = '';
                foreach($kinds_id as $key1=>$val1){
                    $kinds1 = M('GoodsKinds')->where(['kind_id' => $val1])->find();
                    $val['sale_price'] += $kinds1['sale_price'];
                    $val['price'] += $kinds1['price'];
                    $goods[$key]['kinds_detail'][$key1]['kind'] = $kinds[$key1];
                    $goods[$key]['kinds_detail'][$key1]['kind_detail'] = $kinds1['kinds_detail'];
                    if(!empty($kinds1)){
                        $name .= $kinds1['kinds_detail'].' ';
                    }

                }
                $v['detail'] .= '商品名：'.$val['name'].'，数量：'.$val['number'].'，商品编号：'.$val['code'].'，型号：'.$name;
            }
            //$v['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
            switch($v['state']){
                case 1 :
                    $v['state'] = '待支付';
                    break;
                case 2 :
                    $v['state'] = '待发货';
                    break;
                case 3 :
                    $v['state'] = '待收货';
                    break;
                case 4 :
                    $v['state'] = '待评价';
                    break;
                case 5 :
                    $v['state'] = '已完成';
                    break;
                case 6 :
                    $v['state'] = '已取消';
                    break;
                case 7 :
                    $v['state'] = '退换货';
                    break;
            }
            if($v['is_take'] == 1) {
                $v['is_take'] = '景点自取';
            }else{
                $v['is_take'] = '快递寄送';
            }
            switch($v['kuaidi_state']){
                case 1:
                    $v['kuaidi_state'] = '待发货';
                    break;
                case 2:
                    $v['kuaidi_state'] = '已发货';
                    break;
                case 3:
                    $v['kuaidi_state'] = '派送中';
                    break;
                case 4:
                    $v['kuaidi_state'] = '已签收';
                    break;
            }
            echo $k.","
                .$v["order_no"]."\t,"
                .$v["name"]."\t,"
                .$v["phone"]."\t,"
                .$v["amount"]."\t,"
                .$v["paid"]."\t,"
                .$v["score"]."\t,"
                .$v["cost"]."\t,"
                .$v["username"]."\t,"
                .$v["m_phone"]."\t,"
                .$v["state"]."\t,"
                .$v["is_take"]."\t,"
                .$v["detail"]."\t,"
                .$v["kuaidi_name"]."\t,"
                .$v["kuaidi"]."\t,"
                .$v["kuaidi_state"]."\t,"
                .$v["intime"]."\t,"
                ."\n";
        }

    }
    /**
     *@今日新增订单
     */
    public function index(){
        $today = date("Y-m-d 00:00:00");
        $map['a.intime'] = ['gt',$today];
        $map['a.type'] = 1;
        !empty($_GET['state'])      &&      $map['a.state'] = I('state');
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act=I("get.act");
        if($act=="download"){
            $this->down_horder($map);
        }else{
            $url =$_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@今日新增订单
     */
    public function to_all_order(){
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(urldecode(I('start_time')))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(urldecode(I('end_time')))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        !empty($_GET['state'])      &&      $map['a.state'] = I('state');
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();
        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("a.intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act=I("get.act");
        if($act=="download"){
            $this->down_horder($map);
        }else{
            $url =$_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@订单详情
     */
    public function order_view(){
        $id = I('id');
        if(IS_POST){
            $this->check_hotel_locker($id);
        }else {
            $re = M('MallOrder')->alias('a')
                ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.paid,a.score,a.state,a.intime,a.beizhu,a.uuid,
            a.coupon_id,a.cost,b.username,b.phone as m_phone,a.is_take,a.remark,a.deduction,a.kuaidi,a.kuaidi_name,a.kuaidi_state,a.address,a.kuaidi_node')
                ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
                ->where(['a.id' => $id])->find();
            if (!empty($re['coupon_id'])) {
                $coupon = M('MemberCoupon')->alias('a')
                    ->field('title,value')
                    ->join("LEFT JOIN __COUPON__ b on a.coupon_id = b.id")
                    ->where(['a.id' => $re['coupon_id']])->find();
                $re['coupon_title'] = $coupon['title'];
                $re['coupon_value'] = $coupon['value'];
            }

            /***
             * 商品信息
             */
            $goods = M('MallOrderDetail')->alias('a')
                ->field('a.goods_id,a.number,b.name,b.sale_price,b.thumb,a.kinds_id,b.kinds')
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.order_id'=>$re['id']])->select();
            foreach($goods as $key=>$val){
                $kinds_id = explode(',',$val['kinds_id']);
                $kinds = explode(',',$val['kinds']);
                foreach($kinds_id as $key1=>$val1){
                    $kinds1 = M('GoodsKinds')->where(['kind_id' => $val1])->find();
                    $val['sale_price'] += $kinds1['sale_price'];
                    $val['price'] += $kinds1['price'];
                    $goods[$key]['kinds_detail'][$key1]['kind'] = $kinds[$key1];
                    $goods[$key]['kinds_detail'][$key1]['kind_detail'] = $kinds1['kinds_detail'];
                }
                $goods[$key]['sale_price'] = sprintf("%.2f",$val['sale_price']);
                $goods[$key]['price'] = sprintf("%.2f",$val['price']);
            }
            $re['down_url'] = "http://www.kkmove.com/module_special/dl/{$re['uuid']}/".$re['order_no']."?shop=1893&sec=".crypt($re['uuid'],'91xiangba');

            /*退换货信息*/
            if($re['state'] == 7){
                $returns = M('MallReturnsOrder')->alias('a')
                    ->field('a.id,a.type,a.pay_type,a.bank_card,a.bank_name,a.bank_username,a.reason,a.img,a.amount,a.phone,
                    a.kuaidi_company,a.kuaidi,a.kuaidi,a.status,b.goods_id,b.name,b.sale_price,a.count,a.number,a.address')
                    ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->where(['a.order_id'=>$re['id'],'a.is_del'=>'1'])->select();
            }

            /*物流*/
            $express_node = M('ExpressNode')->select();

            /*日志*/
            $work_log = M('WorkLog')->alias('a')
                ->field('a.title,a.intime,b.name')
                ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.id")
                ->where(['table' => 'MallOrder', 'record_id' => $id])->order("a.intime desc")->select();
            $url = session('url');
            $this->assign(['re' => $re, 'log' => $work_log,'goods'=>$goods,'returns'=>$returns,'url'=>$url,'express_node'=>$express_node]);
            $this->display();
        }
    }

    /**
     *@查找快递
     */
    public function getExpressNode(){
        $express = I('express');
        $re = M("ExpressNode")->where(['express'=>$express])->find();
        if($re){
            echo $re['node'];
        }else{
            echo '';
        }
    }

    /**
     *@查找快递
     */
    public function getExpress(){
        $express = I('express');
        !empty($express)    &&  $map['express'] = ['like','%'.$express.'%'];
        $list = M('ExpressNode')->where($map)->select();
        $code = '<option value="">请选择快递</option>';
        if($list){
            foreach($list as $k=>$v){
                $code .= "<option value=".$v['express'].">".$v['express']."</option>";
            }
        }
        echo $code;
    }

    /**
     *@下载
     */
    public function down_diy()
    {
        $order_no = I('order_no');
        $url = I('url');
        $arr = explode('?',$url);
        $new_url = $arr[0];
        $array = explode('.',$new_url);
        $str = $array[count($array) - 1];
        $result = httpcopy($url);
        $file = $result['fileName'];
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
    }

    /**
     *@修改物流信息
     */
    public function edit_kuaidi(){
        $id = I('id');
        $info = M('MallOrder')->where(['id'=>$id])->find();
        $data = $_GET;
        if(empty($data['kuaidi_name'])){
            echo json_encode(array('status'=>'error','info'=>'物流公司不能为空','class'=>'kuaidi_name'));
            die;
        }
        if(empty($data['kuaidi_node'])){
            echo json_encode(array('status'=>'error','info'=>'物流公司标志','class'=>'kuaidi_node'));
            die;
        }
        if(empty($data['kuaidi'])){
            echo json_encode(array('status'=>'error','info'=>'物流单号不能为空','class'=>'kuaidi'));
            die;
        }
        $result = M('MallOrder')->where(['id'=>$id])->save($data);
        if($result){
            $work = '修改了物流信息：';
            if($data['kuaidi_name'] != $info['kuaidi_name'])      $work .= '原物流公司:'.$info['kuaidi_name'].'；';
            if($data['kuaidi'] != $info['kuaidi'])      $work .= '原物流单号是:'.$info['kuaidi'].'；';
            if($data['kuaidi_state'] != $info['kuaidi_state']){
                switch($info['kuaidi_state']){
                    case 1:
                        $work .= '原物流状态是:待发货；';
                        break;
                    case 2:
                        $work .= '原物流状态是:已发货；';
                        break;
                    case 3:
                        $work .= '原物流状态是:派送中；';
                        break;
                    case 4:
                        $work .= '原物流状态是:已签收；';
                        break;
                }
            }
            work_log($table='MallOrder',$record_id = $data['id'],'1',$work);
            echo json_encode(array('status'=>'ok','info'=>'修改物流信息成功'));
        }else{
            echo json_encode(array('status'=>'error','info'=>'修改物流信息失败'));
        }
    }

    /**
     *@退换货凭证图片
     */
    public function get_returns_img(){
        $id = I('id');
        $img = M('MallReturnsOrder')->where(['id'=>$id])->getField('img');
        $img = explode(',',$img);
        foreach($img as $k=>$v){
            $arr['data'][$k]['src'] = $v;
        }
        $arr['title'] = '';
        $arr['start'] = 0;
        echo json_encode($arr);
    }

    /**
     *@订单改价
     */
    public function change_paid(){
        if(IS_POST){
            $data = $_POST;
            $result = M('MallOrder')->where(['id'=>$data['id']])->save($data);
            if($result){
                work_log($table='MallOrder',$record_id = $data['id'],'1',$work='修改了订单价格');
                echo json_encode(array('status'=>'ok','info'=>'订单改价成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>'订单改价失败'));
            }
        }else{
            $id = I('id');
            $info = M('MallOrder')->where(['id'=>$id])->find();
            echo json_encode(['status'=>'ok','info'=>$info]);
        }
    }

    /**
     *@修改退换货订单
     */
    public function change_returns_state(){
        if(IS_POST){
            $id = I('id');
            $map['status'] = I('status');
            $map['uptime'] = date("Y-m-d H:i:s",time());
            $check = M('MallReturnsOrder')->where(['id'=>$id])->find();
            $result = M('MallReturnsOrder')->where(['id'=>$id])->save($map);
            if($result){
                if($check['type'] == 1){
                    $action = '退货';
                }else{
                    $action = '换货';
                }
                work_log($table='MallOrder',$record_id = $check['order_id'],'1',$work='修改了'.$action.'编号'.$check['number'].'的状态');
                echo json_encode(['status'=>'ok','info'=>$action."状态更改操作成功"]);
                die;
            }else{
                echo json_encode(['status'=>'error','info'=>"状态更改操作失败"]);
                die;
            }
        }
    }

    /**
     *@修改订单状态
     */
    public function change_order_status(){
        if(IS_POST){
            $data = $_POST;
            $check = M('MallOrder')->where(['id'=>$data['id']])->find();
            //$member = M('Member')->where(['member_id'=>$check['member_id']])->find();
            if($check['state'] == $data['state']){
                echo json_encode(array('status'=>'error','info'=>'修改失败，订单状态未改变'));
                die;
            }
            $user = session('user');
//            if($data['state'] == '3'){
//                if($user['uname'] != 'admin'){
//                    echo json_encode(array('status'=>'error','info'=>'你的权限不足，不能操作'));
//                    die;
//                }
//            }
            $result = M('MallOrder')->where(['id'=>$data['id']])->save($data);
            if($result){
                if($check['state'] != $data['state']) {
                    switch ($data['state']) {
                        case 3:
                            $message = '你的订单已发货';
                            set_message($check['mid'], $message,$check['id'],$check['type']);
                            $this->send_SMS($check['phone'], $message);
                            break;
                        case 4 :
                            if(in_array($check['type'],['1','2'])){
                                $order_detail = M('MallOrderDetail')->where(['order_id'=>$check['id']])->select();
                                foreach($order_detail as $k=>$v){
                                    M('Goods')->where(['goods_id'=>$v['goods_id']])->setInc('sale_number',$v['number']);
                                    if(!empty($v['kinds_id'])){
                                        $kinds = $v['kinds_id'];
                                        M('GoodsStock')->where(['goods_id'=>$v['goods_id'],'kinds'=>$kinds])->setInc('sale_number',$v['number']);
                                    }
                                }

                            }
                            break;
                        case 5:
                            $message = '你的订单已完成';
                            set_message($check['mid'], $message,$check['id'],$check['type']);
                            break;
                        case 6:
                            $message = '你的订单已取消';
                            set_message($check['mid'], $message,$check['id'],$check['type']);
                            break;
                    }
                }
                if(!empty($member['openid'])){
                    $weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
                    $accessToken= S('globals_access_token');
                    if(empty($accessToken))     $accessToken = $weixin->getAccessToken();
                    $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$accessToken;
                    //$tmp = json_encode($tmp);
                    //$result = curl_post_json($url,$tmp);
                }
                work_log($table='MallOrder',$record_id = $data['id'],'1',$work='修改了订单状态');
                echo json_encode(array('status'=>'ok','info'=>'修改订单状态成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'修改订单状态失败'));
            }
        }
    }

    /**
     *@删除订单
     */
    public function del_order(){
        if(IS_POST) {
            $id = I('ids');
            $map['id'] = array('in', $id);
            $data['is_del'] = 2;
            $list = M('MallOrder')->where($map)->select();
            $user = session('user');
//            if($user['uname'] != 'admin'){
//                foreach($list as $k=>$v){
//                    if($v['is_lock'] == 1){
//                        echo json_encode(['status' => "error", 'info' => '所选订单锁定状态不符合']);
//                        die;
//                    }
//                    if($v['locker_id'] != $user['user_id']){
//                        echo json_encode(['status' => "error", 'info' => '所选订单锁定人不符合']);
//                        die;
//                    }
//                }
//            }
//        $data['del_time'] = date("Y-m-d H:i:s",time());
            $result = M('MallOrder')->where($map)->save($data);
            if ($result) {
                $id = explode(',',$id);
                if (is_array($id)) {
                    foreach ($id as $val) {
                        work_log($table = 'MallOrder', $record_id = $val,'1', $work = '删除了订单记录');
                    }
                } else {
                    work_log($table = 'MallOrder', $record_id = $id,'1', $work = '删除了订单记录');
                }
                echo json_encode(['status' => "ok", 'info' => '删除记录成功!', 'url' => session('url')]);
            } else {
                echo json_encode(['status' => "error", 'info' => '删除记录失败!']);
            }
        }
    }

    /**
     *@订单备注
     */
    public function beizhu(){
        if(IS_POST){
            $id = I('id');
            $beizhu = I('beizhu');
            $check = M('MallOrder')->where(['id'=>$id])->find();
            if($check['beizhu'] == $beizhu){
                echo json_encode(['status' => "error", 'info' => '备注未做改变!']);
                die;
            }
            $result = M('MallOrder')->where(['order_id'=>$id])->save(['beizhu'=>$beizhu]);
            if($result){
                work_log($table = 'MallOrder', $record_id = $id,'1', $work = '备注了订单');
                echo json_encode(['status' => "ok", 'info' => '备注成功!']);
            }else{
                echo json_encode(['status' => "error", 'info' => '备注信息失败!']);
            }
        }
    }

    /**
     *@删除订单列表
     */
    public function is_del_order(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(urldecode(I('start_time')))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(urldecode(I('end_time')))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.is_del'] = 2;
        !empty($_GET['state'])      &&      $map['a.state'] = I('state');
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('Member')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@真实删除订单
     */
    public function del_order_true(){
        if(IS_POST) {
            $id = I('ids');
            $data['id'] = array('in', $id);
//        $data['del_time'] = date("Y-m-d H:i:s",time());
            $result = M('MallOrder')->where($data)->delete();
            if ($result) {
                echo json_encode(['status' => "ok", 'info' => '删除记录成功!', 'url' => session('url')]);
            } else {
                echo json_encode(['status' => "error", 'info' => '删除记录失败!']);
            }
        }
    }

    /**
     *@恢复订单
     */
    public function recovery_order(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('MallOrder')->where($data)->save(array('is_del'=>1));
        if($result){
            $id = explode(',',$id);
            if (is_array($id)) {
                foreach ($id as $val) {
                    work_log($table = 'MallOrder', $record_id = $val,'1', $work = '恢复了订单记录');
                }
            } else {
                work_log($table = 'MallOrder', $record_id = $id,'1', $work = '恢复了订单记录');
            }
            echo json_encode(['status'=>"ok",'info'=>'记录恢复成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'记录恢复失败!']);
        }
    }

    /**
     *@待确认订单
     */
    public function to_be_confirm(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 1;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@待支付订单
     */
    public function to_be_pay(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 1;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
           $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@待出票订单
     */
    public function to_be_drawer(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 2;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@已完成订单
     */
    public function to_be_accept(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 3;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@待入住订单
     */
    public function to_be_check(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 4;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@已完成订单
     */
    public function complete(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 5;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.is_take,a.score')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@已完成订单
     */
    public function quxiao_order(){
        $map = [];
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 6;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.score,a.is_take')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@已完成订单
     */
    public function to_be_returns(){
        $map = [];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.type'] = 1;
        $map['a.state'] = 7;
        !empty($_GET['order_no'])   &&      $map['a.order_no|a.name|b.username'] = ['like','%'.I('order_no').'%'];
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }

        $count = M('MallOrder')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->count();

        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list  = M('MallOrder')->alias('a')
            ->field('a.id,a.order_no,a.name,a.phone,a.amount,a.state,a.intime,a.paid,b.username,b.phone as m_phone,a.cost,a.uuid,a.is_take,a.score')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        foreach($list as $k=>$v){
            if($v['is_lock'] == '2'){
                $list[$k]['locker'] = M('User')->where(['id'=>$v['locker_id']])->getField('realname');
            }
            $list[$k]['down_url'] = "http://www.kkmove.com/module_special/dl/{$v['uuid']}/".$v['order_no']."?shop=1893&sec=".crypt($v['uuid'],'91xiangba');
        }
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $this->down_horder($map);
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }
    /**
     *@锁定订单
     */
    public function lock_order(){
        if(IS_POST){
            $id = I('id');
            $check = M('HotelOrder')->where(['order_id'=>$id])->find();
            $user = session('user');
            if($check['is_lock'] == '1'){
//                if($user['uname'] == 'admin'){
//                    echo json_encode(array('status'=>'error','info'=>'超级管理员不能锁定订单'));
//                    die;
//                }
                $result = M('HotelOrder')->where(['order_id'=>$id])->save(['is_lock'=>'2','locker_id'=>$user['id']]);
                $action = '锁定';
            }else{
                if($user['id'] == $check['locker_id'] || $user['uname'] == 'admin'){
                    $result = M('HotelOrder')->where(['order_id'=>$id])->save(['is_lock'=>'1','locker_id'=>'']);
                    $action = '解绑';
                }else{
                    echo json_encode(array('status'=>'error','info'=>'你没有权限解绑该订单'));
                    die;
                }
            }
            if($result){
                work_log($table='HotelOrder',$record_id = $id,'1',$work=$action.'了航班订单');
                echo json_encode(array('status'=>'ok','info'=>$action.'该订单成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'该订单失败'));
            }
        }
    }

}