<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/11/3
 * Time: 18:40
 */

namespace Admin\Controller;


use Api\Controller\PingxxController;

class FinanceController extends BaseController
{
    /**
     *充值记录
     */
    public function index(){
        $map = [];
        !empty($_GET['pay_type']) && $map['a.pay_type'] = ['like','%'.I('pay_type').'%'];
        !empty($_GET['username']) && $map['b.username|b.phone|a.pay_number'] = ['like','%'.I('username').'%'];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        $map['a.pay_status'] = '2';
        $map['a.is_del'] = '1';
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Recharge')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->count();
        $p = $this->getpage($count,$num);
        $list  = M('Recharge')->alias('a')
            ->field('a.recharge_id,a.pay_number,a.amount,a.pay_type,b.username,b.phone,a.intime,b.grade')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->order("intime desc")->limit($p->firstRow,$p->listRows)->select();
        foreach($list as $key=>$val){
            $list[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
        }
        $sum = M("Recharge")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where($map)->sum('a.amount');
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show(),'sum'=>$sum]);
        $act = I("get.act");
        if($act == 'download'){
            $dat = M('Recharge')->alias('a')
                ->field('a.recharge_id,a.pay_number,a.amount,a.pay_type,pay_status,b.username,b.phone,a.intime,b.grade')
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                ->where($map)->select();
            $str = '充值记录表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,充值会员,充值账号,订单号,充值金额,充值状态,充值途径,充值时间\n";
            foreach($dat as $key=>$val){
                switch($val['pay_status']){
                    case 1 :
                        $val['pay_status'] = '未完成';
                        break;
                    case 2 :
                        $val['pay_status'] = '充值完成';
                        break;
                }
                echo $key.","
                    .$val["username"]."\t,"
                    .$val["phone"]."\t,"
                    .$val["pay_number"]."\t,"
                    .$val["amount"]."\t,"
                    .$val["pay_status"]."\t,"
                    .$val["pay_type"]."\t,"
                    .$val["intime"]."\t,"
                    ."\n";
            }
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@真实删除订单
     */
    public function del_recharge(){
        if(IS_POST) {
            $id = I('ids');
            $data['recharge_id'] = ['in',$id];
            $result = M('Recharge')->where($data)->save(['is_del'=>2]);
            if ($result) {
                echo json_encode(['status' => "ok", 'info' => '删除记录成功!', 'url' => session('url')]);
            } else {
                echo json_encode(['status' => "error", 'info' => '删除记录失败!']);
            }
        }
    }

    public function trade(){
        function mall_order($order_no){
            $order = M('MallOrder')->where(['order_no'=>$order_no])->find();
            return $order;
        }
        function recharge($order_no){
            $order = M('recharge')->where(['order_no'=>$order_no])->find();
            return $order;
        }
        function upgrade($order_no){
            $order = M('upgrade_order')->where(['order_no'=>$order_no])->find();
            return $order;
        }
        function teach($order_no){
            $order = M('teach_order')->where(['order_no'=>$order_no])->find();
            return $order;
        }
        function tutor_class($order_no){
            $order = M('tutor_class_order')->where(['order_no'=>$order_no])->find();
            return $order;
        }
        $map = [];
        !empty($_GET['type']) && $map['a.type'] = I('type');
        !empty($_GET['pay_type']) && $map['a.pay_type'] = ['like','%'.I('pay_type').'%'];
        !empty($_GET['username']) && $map['b.username|b.phone|a.order_no'] = ['like','%'.I('username').'%'];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('TradeRecord')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.member_id = b.user_id")
            ->where($map)->count();
        $p = $this->getpage($count,$num);
        $list  = M('TradeRecord')->alias('a')
            ->field('a.trade_id,a.order_no,a.amount,a.type,a.pay_type,b.username,b.phone,a.intime,a.is_change,b.grade')
            ->join("LEFT JOIN __USER__ b on a.member_id = b.user_id")
            ->where($map)->order("a.intime desc")->limit($p->firstRow,$p->listRows)->select();
        foreach($list as $key=>$val){
            switch($val['type']){
                case 1:
                    $order = mall_order($val['order_no']);
                    $list[$key]['order_id'] = $order['id'];
                    $list[$key]['paid'] = $order['paid'];
                    $list[$key]['cost'] = $order['cost'];
                    break;
                case 2:
                    $order = recharge($val['order_no']);
                    $list[$key]['order_id'] = $order['recharge_id'];
                    $list[$key]['paid'] = $order['amount'];
                    $list[$key]['cost'] = $order['amount'];
                    break;
                case 3:
                    $order = upgrade($val['order_no']);
                    $list[$key]['order_id'] = $order['id'];
                    $list[$key]['paid'] = $order['amount'];
                    $list[$key]['cost'] = $order['amount'];
                    break;
                case 4:
                    $order = teach($val['order_no']);
                    $list[$key]['order_id'] = $order['id'];
                    $list[$key]['paid'] = $order['amount'];
                    $list[$key]['cost'] = $order['amount'];
                case 5:
                    $order = tutor_class($val['order_no']);
                    $list[$key]['order_id'] = $order['id'];
                    $list[$key]['paid'] = $order['amount'];
                    $list[$key]['cost'] = $order['amount'];
                    break;
            }

            $list[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
        }
        $sum = M("TradeRecord")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.member_id = b.user_id")
            ->where($map)->sum('a.amount');
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show(),'sum'=>$sum]);
        $act = I("get.act");
        if($act == 'download'){
            $dat = M('TradeRecord')->alias('a')
                ->field('a.trade_id,a.order_no,a.amount,a.type,a.pay_type,b.username,b.phone,a.intime,b.grade,a.is_change')
                ->join("LEFT JOIN __USER__ b on a.member_id = b.user_id")
                ->where($map)->order("a.intime desc")->select();
            $str = '交易统计表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,订单号,订单类型,下单会员,会员账号,订单金额,订单成本,交易金额,支付类型,交易时间\n";
            foreach($dat as $key=>$val){

                switch($val['type']){
                    case 1:
                        $order = mall_order($val['order_no']);
                        $val['paid'] = $order['paid'];
                        $val['cost'] = $order['cost'];
                        $val['type'] = '商城订单';
                        break;
                    case 2:
                        $order = recharge($val['order_no']);
                        $val['paid'] = $order['amount'];
                        $val['cost'] = $order['amount'];
                        $val['type'] = '会员充值';
                        break;
                    case 3:
                        $order = upgrade($val['order_no']);
                        $val['paid'] = $order['amount'];
                        $val['cost'] = $order['amount'];
                        $val['type'] = '会员升级';
                        break;
                    case 4:
                        $order = teach($val['order_no']);
                        $val['paid'] = $order['amount'];
                        $val['cost'] = $order['amount'];
                        $val['type'] = '名师指点';
                    case 5:
                        $order = tutor_class($val['order_no']);
                        $val['paid'] = $order['amount'];
                        $val['cost'] = $order['amount'];
                        $val['type'] = '线下班级报名';
                        break;
                }
                echo $key.","
                    .$val["order_no"]."\t,"
                    .$val["type"]."\t,"
                    .$val["username"]."\t,"
                    .$val["phone"]."\t,"
                    .$val["paid"]."\t,"
                    .$val["cost"]."\t,"
                    .$val["amount"]."\t,"
                    .$val["pay_type"]."\t,"
                    .$val["intime"]."\t,"
                    ."\n";
            }
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }


    /**
     * @提现记录
     */
    public function withdraw(){
        $map=[];
        !empty($_GET['status']) && $map['a.status'] = I('status');
        !empty($_GET['username']) && $map['b.username|b.phone'] = ['like','%'.I('username').'%'];
        if(!empty($_GET['start_time'])) $start_time = strtotime(I('start_time')); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = strtotime(I('end_time')); else $end_time = time();
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        if (empty($num)){
            $num = 10;
        }
        $count = M("Withdraw")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->join("LEFT JOIN __BANK_CARD__ c on a.card_id = c.id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $p = $this->getpage($count,$num);
        $data=M("Withdraw")->alias('a')
            ->field('a.*,b.username,b.phone,c.bank_card,c.pay_type,b.user_id as uid')
            ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
            ->join("LEFT JOIN __BANK_CARD__ c on a.card_id = c.id")
            ->where($map)->limit($p->firstRow,$p->listRows)->order('a.intime desc')->select();
        $this->assign(['list'=>$data,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $dat=M("Withdraw")->alias('a')
                ->field('a.*,b.username,b.phone,c.bank_card,c.pay_type,c.realname,
            c.card,c.pay_type,c.bank_card,c.bank_name')
                ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
                ->join("LEFT JOIN __BANK_CARD__ c on a.card_id = c.id")
                ->where($map)->order('a.intime desc')->select();
            $str = '提现统计表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,会员昵称,会员账号,真实姓名,提现享币,兑换金额,提现账户,账户类型,开户行信息,提现状态,提现时间\n";
            foreach($dat as $key=>$val){
                switch($val['pay_type']){
                    case 1:
                        $val['pay_type'] = '支付宝';
                        break;
                    case 2:
                        $val['pay_type'] = '银行卡';
                        break;
                }
                switch($val['status']){
                    case 1:
                        $val['status'] = '申请中';
                        break;
                    case 2:
                        $val['status'] = '冻结中';
                        break;
                    case 3:
                        $val['status'] = '已返现';
                        break;
                }
                echo $key.","
                    .$val["username"]."\t,"
                    .$val["phone"]."\t,"
                    .$val["realname"]."\t,"
                    .$val["score"]."\t,"
                    .$val["amount"]."\t,"
                    .$val["bank_card"]."\t,"
                    .$val["pay_type"]."\t,"
                    .$val["bank_name"]."\t,"
                    .$val["status"]."\t,"
                    .$val["intime"]."\t,"
                    ."\n";
            }
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }


    public function edit_withdraw(){
        if(IS_POST){
            $id = I('id');
            $status = I('status');
            $result = M('Withdraw')->where(['id'=>$id])->save(['status'=>$status,'uptime'=>time()]);
            if ($result) {
                echo json_encode(['status' => "ok", 'info' => '修改记录成功!', 'url' => session('url')]);
                die;
            } else {
                echo json_encode(['status' => "error", 'info' => '修改记录失败!']);
                die;
            }
        }else{
            layout(false);
            $id = I('id');
            $re = M("Withdraw")->alias('a')
                ->field('a.*,b.username,b.phone,c.bank_card,c.pay_type')
                ->join("LEFT JOIN __USER__ b on a.mid = b.user_id")
                ->join("LEFT JOIN __BANK_CARD__ c on a.card_id = c.id")
                ->where(['a.id'=>$id])->find();
            $this->assign('re',$re);
            $this->display();
        }
    }


    public function teach_returns(){
        $map=[];
        !empty($_GET['state']) && $map['c.state'] = I('state');
        !empty($_GET['username']) && $map['b.username|b.phone'] = ['like','%'.I('username').'%'];
        !empty($_GET['tusername']) && $map['d.username|d.phone'] = ['like','%'.I('tusername').'%'];
        if(!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s",strtotime(I('start_time'))); else $start_time = 0;
        if(!empty($_GET['end_time']))   $end_time = date("Y-m-d H:i:s",strtotime(I('end_time'))); else $end_time = date("Y-m-d H:i:s",time());
        $map['a.intime'] = ['between',[$start_time,$end_time]];
        if (empty($num)){
            $num = 10;
        }
        $count = M("teach_order_returns")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")      //用户
            ->join("LEFT JOIN __TEACH_ORDER__ c on a.teach_order_id = c.id")     //指点信息
            ->join("LEFT JOIN __USER__ d on a.user_id2 = d.user_id")      //导师
            ->where($map)->count(); // 查询满足要求的总记录数
        $p = $this->getpage($count,$num);
        $data = M("teach_order_returns")->alias('a')
            ->field("a.*,b.username,b.phone,c.order_no,c.amount,c.state,d.username as tusername,d.phone as tphone,b.user_id")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")      //用户
            ->join("LEFT JOIN __TEACH_ORDER__ c on a.teach_order_id = c.id")     //指点信息
            ->join("LEFT JOIN __USER__ d on a.user_id2 = d.user_id")      //导师
            ->where($map)->limit($p->firstRow,$p->listRows)->order('a.intime desc')->select();
        $this->assign(['list'=>$data,'count'=>$count,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $dat = M("teach_order_returns")->alias('a')
                ->field("a.*,b.username,b.phone,c.order_no,c.amount,c.end_time,c.intime as start_time,c.state,c.return_time,
            d.username as tusername,d.phone as tphone")
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")      //用户
                ->join("LEFT JOIN __TEACH_ORDER__ c on a.teach_order_id = c.id")     //指点信息
                ->join("LEFT JOIN __USER__ d on a.user_id2 = d.user_id")      //导师
                ->where($map)->order('a.intime desc')->select();
            $str = '名师指点退款统计表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,会员昵称,会员账号,退款金额,退款原因,指点导师,导师账号,退款状态,退款时间\n";
            foreach($dat as $key=>$val){
                switch($val['state']){
                    case 2:
                        $val['state'] = '未退款';
                        break;
                    case 3:
                        $val['state'] = '已退款';
                        break;
                }
                echo $key.","
                    .$val["username"]."\t,"
                    .$val["phone"]."\t,"
                    .$val["amount"]."\t,"
                    .$val["reason"]."\t,"
                    .$val["tusername"]."\t,"
                    .$val["tphone"]."\t,"
                    .$val["state"]."\t,"
                    .$val["return_time"]."\t,"
                    ."\n";
            }
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    public function teach_return_view(){
        $id = I('id');
        $map['a.return_id'] = $id;
        $re = M("teach_order_returns")->alias('a')
            ->field("a.*,b.username,b.phone,b.autograph,b.img as bimg,c.order_no,c.amount,c.state,b.user_id,c.end_time,c.return_time,
            d.username as tusername,d.phone as tphone,d.autograph as tautograph,d.img as timg,d.user_id as tuser_id")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")      //用户
            ->join("LEFT JOIN __TEACH_ORDER__ c on a.teach_order_id = c.id")     //指点信息
            ->join("LEFT JOIN __USER__ d on a.user_id2 = d.user_id")      //导师
            ->where($map)->find();
        if(!empty($re['img'])){
            $re['img'] = explode(',',$re['img']);
        }
        $this->assign(['re'=>$re]);
        $this->display();
    }

    /**
     *@退款
     */
    public function return_money()
    {
        if (IS_POST) {
            $id = I('id');
            $record = M('teach_order_returns')->where(['return_id' => $id])->find();
            $order = M('teach_order')->where(['id' => $record['teach_order_id']])->find();
            if (!$order) {
                echo json_encode(['status' => "error", 'info' => '订单没有找到!']);
                die;
            }
            $returns = json_decode($order['returns'], true);
            if (empty($returns['data']['object']['id'])) {
                echo json_encode(['status' => "error", 'info' => '支付信息错误!']);
                die;
            }
            $obj = new PingxxController();
            $amount = $order['amount'];
            $charge = $returns['data']['object']['id'];
            $result = $obj->return_money($amount, $charge);
            $result = json_decode($result, true);
            if (empty($result) || $result['error']) {
                echo json_encode(['status' => "error", 'info' => '退款操作失败!']);
                die;
            } else {
                M('teach_order')->where(['id' => $record['teach_order_id']])->save(['state' => '3', 'return_time' => date("Y-m-d H:i:s")]);
                $earnings['earnings'] = $order['amount'];     //导师收益统计
                $earnings['user_id'] = $order['user_id2'];
                $earnings['content'] = '指点退款';
                $earnings['intime'] = time();
                $earnings['date'] = date("Y-m-d",time());
                $earnings['type'] = 1;
                echo json_encode(['status' => "ok", 'info' => '退款操作成功!', 'url' => session('url')]);
                die;
            }

        }
    }

}
