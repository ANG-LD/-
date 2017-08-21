<?php
/**
 * Created by PhpStorm.
 * User: ljy
 * Date: 17/8/1
 * Time: 下午7:02
 */

namespace Admin\Controller;


class PorderController extends BaseController
{
    public function teach()
    {
        !empty($_GET['state']) && $map['a.state'] = I('state');
        !empty($_GET['order_no']) && $map['a.order_no|b.phone|b.username|c.username|c.phone'] = ['like', '%' . I('order_no') . '%'];
        if (!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s", strtotime(urldecode(I('start_time')))); else $start_time = 0;
        if (!empty($_GET['end_time'])) $end_time = date("Y-m-d H:i:s", strtotime(urldecode(I('end_time')))); else $end_time = date("Y-m-d H:i:s", time());
        $map['a.intime'] = ['between', [$start_time, $end_time]];
        if (empty($num)) {
            $num = 10;
        }
        $map['a.is_del'] = '1';

        $count = M('teach_order')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
            ->where($map)->count();

        $this->assign('nus', $num);
        $p = $this->getpage($count, $num);
        $list = M('teach_order')->alias('a')
            ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.teach_status,b.username,b.phone,c.phone as tphone,c.username as tusername')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
            ->where($map)->limit($p->firstRow, $p->listRows)->order("a.intime desc")->select();
        $this->assign(['list' => $list, 'count' => $count, 'page' => $p->show()]);
        $act = I("get.act");
        if ($act == "download") {
            $dat = M('teach_order')->alias('a')
                ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.teach_status,b.username,b.phone,c.phone as tphone,c.username as tusername')
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
                ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                ->where($map)->order("a.intime desc")->select();
            $str = '名师指点订单' . date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF" . "序号,订单号,订单总金额,订单状态,指点状态,会员,会员手机号,导师,导师手机,下单时间\n";
            foreach ($dat as $k => $v) {
                switch ($v['state']) {
                    case 1 :
                        $v['state'] = '待支付';
                        break;
                    case 2 :
                        $v['state'] = '已支付';
                        break;
                    case 3 :
                        $v['state'] = '已退款';
                        break;
                }
                if ($v['state'] != 1) {
                    if ($v['teach_status'] == 1) {
                        $v['teach_status'] = '正在指点';
                    } else {
                        $v['teach_status'] = '指点结束';
                    }
                }
                echo $k . ","
                    . $v["order_no"] . "\t,"
                    . $v["amount"] . "\t,"
                    . $v["state"] . "\t,"
                    . $v["teach_status"] . "\t,"
                    . $v["username"] . "\t,"
                    . $v["phone"] . "\t,"
                    . $v["tusername"] . "\t,"
                    . $v["tphone"] . "\t,"
                    . $v["intime"] . "\t,"
                    . "\n";
            }

        } else {
            $url = $_SERVER['REQUEST_URI'];
            session('url', $url);
            $this->display();
        }
    }


    /*名师指点详情*/
    public function teach_view()
    {
        $id = I('id');
        $map['a.id'] = $id;
        $re = M('teach_order')->alias('a')
            ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.end_time,a.teach_status,b.username,b.phone,b.sex,b.img,b.autograph,b.user_id,
            c.phone as tphone,c.username as tusername,c.img as timg,c.autograph as tautograph,c.user_id as tuser_id')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
            ->where($map)
            ->find();
        $this->assign(['re' => $re]);
        $this->display();
    }

    /*删除订单*/
    public function del_order()
    {
        if (IS_POST) {
            $type = I('type');
            switch ($type) {
                case 1:
                    $table = 'teach_order';
                    break;
                case 2:
                    $table = 'upgrade_order';
                    break;
                case 3:
                    $table = 'tutor_class_order';
                    break;
            }
            $id = I('ids');
            $map['id'] = array('in', $id);
            $data['is_del'] = 2;
            $result = M($table)->where($map)->save($data);
            if ($result) {
                $id = explode(',', $id);
                if (is_array($id)) {
                    foreach ($id as $val) {
                        work_log($table = $table, $record_id = $val, '1', $work = '删除了订单记录');
                    }
                } else {
                    work_log($table = $table, $record_id = $id, '1', $work = '删除了订单记录');
                }
                echo json_encode(['status' => "ok", 'info' => '删除记录成功!', 'url' => session('url')]);
            } else {
                echo json_encode(['status' => "error", 'info' => '删除记录失败!']);
            }
        }
    }

    /*会员升级*/
    public function upgrade()
    {
        !empty($_GET['state']) && $map['a.state'] = I('state');
        !empty($_GET['order_no']) && $map['a.order_no|b.phone|b.username|c.username|c.phone'] = ['like', '%' . I('order_no') . '%'];
        if (!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s", strtotime(urldecode(I('start_time')))); else $start_time = 0;
        if (!empty($_GET['end_time'])) $end_time = date("Y-m-d H:i:s", strtotime(urldecode(I('end_time')))); else $end_time = date("Y-m-d H:i:s", time());
        $map['a.intime'] = ['between', [$start_time, $end_time]];
        if (empty($num)) {
            $num = 10;
        }
        $map['a.is_del'] = '1';

        $count = M('upgrade_order')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
            ->where($map)->count();

        $this->assign('nus', $num);
        $p = $this->getpage($count, $num);
        $list = M('upgrade_order')->alias('a')
            ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.status,b.username,b.phone,c.phone as tphone,c.username as tusername')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
            ->where($map)->limit($p->firstRow, $p->listRows)->order("a.intime desc")->select();
        $this->assign(['list' => $list, 'count' => $count, 'page' => $p->show()]);
        $act = I("get.act");
        if ($act == "download") {
            $dat = M('upgrade_order')->alias('a')
                ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.status,b.username,b.phone,c.phone as tphone,c.username as tusername')
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
                ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                ->where($map)->order("a.intime desc")->select();
            $str = '会员升级订单' . date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF" . "序号,订单号,订单总金额,订单状态,升级类型,会员,会员手机号,导师,导师手机,下单时间\n";
            foreach ($dat as $k => $v) {
                switch ($v['state']) {
                    case 1 :
                        $v['state'] = '待支付';
                        break;
                    case 2 :
                        $v['state'] = '已支付';
                        break;
                    case 3 :
                        $v['state'] = '已退款';
                        break;
                }
                if ($v['state'] != 1) {
                    if ($v['status'] == 1) {
                        $v['status'] = '高级会员';
                    } else {
                        $v['status'] = '钻石会员';
                    }
                }
                echo $k . ","
                    . $v["order_no"] . "\t,"
                    . $v["amount"] . "\t,"
                    . $v["state"] . "\t,"
                    . $v["status"] . "\t,"
                    . $v["username"] . "\t,"
                    . $v["phone"] . "\t,"
                    . $v["tusername"] . "\t,"
                    . $v["tphone"] . "\t,"
                    . $v["intime"] . "\t,"
                    . "\n";
            }

        } else {
            $url = $_SERVER['REQUEST_URI'];
            session('url', $url);
            $this->display();
        }
    }

    /*会员升级详情*/
    public function upgrade_view()
    {
        $id = I('id');
        $map['a.id'] = $id;
        $re = M('upgrade_order')->alias('a')
            ->field('a.id,a.order_no,a.amount,a.state,a.status,a.intime,b.username,b.phone,b.sex,b.img,b.autograph,b.user_id,
            c.phone as tphone,c.username as tusername,c.img as timg,c.autograph as tautograph,c.user_id as tuser_id')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
            ->where($map)
            ->find();
        //升级信息
        $record = M('upgrade_record')->where(['upgrade_order_id' => $re['id']])->find();
        $this->assign(['re' => $re, 'record' => $record]);
        $this->display();
    }

    /**线下报名订单*/
    public function tutor_class()
    {
        !empty($_GET['state']) && $map['a.state'] = I('state');
        !empty($_GET['order_no']) && $map['a.order_no|b.phone|b.username|c.username|c.phone'] = ['like', '%' . I('order_no') . '%'];
        if (!empty($_GET['start_time'])) $start_time = date("Y-m-d H:i:s", strtotime(urldecode(I('start_time')))); else $start_time = 0;
        if (!empty($_GET['end_time'])) $end_time = date("Y-m-d H:i:s", strtotime(urldecode(I('end_time')))); else $end_time = date("Y-m-d H:i:s", time());
        $map['a.intime'] = ['between', [$start_time, $end_time]];
        if (empty($num)) {
            $num = 10;
        }
        $map['a.is_del'] = '1';

        $count = M('tutor_class_order')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN tk_tutor_class c on a.tutor_class_id = c.id")//导师线下班级
            ->where($map)->count();

        $this->assign('nus', $num);
        $p = $this->getpage($count, $num);
        $list = M('tutor_class_order')->alias('a')
            ->field('a.id,a.order_no,a.amount,a.state,a.number,a.is_vip,a.intime,b.username,b.phone,c.tutor_id,c.name,c.price,c.vip_price,a.number')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN tk_tutor_class c on a.tutor_class_id = c.id")//导师线下班级
            ->where($map)->limit($p->firstRow, $p->listRows)->order("a.intime desc")->select();
        $this->assign(['list' => $list, 'count' => $count, 'page' => $p->show()]);
        $act = I("get.act");
        if ($act == "download") {
            $dat = M('tutor_class_order')->alias('a')
                ->field('a.id,a.order_no,a.amount,a.state,a.number,a.is_vip,a.intime,b.username,b.phone,c.tutor_id,c.name,c.price,c.vip_price,a.number')
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
                ->join("LEFT JOIN tk_tutor_class c on a.tutor_class_id = c.id")//导师线下班级
                ->where($map)->order("a.intime desc")->select();
            $str = '会员升级订单' . date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF" . "序号,订单号,订单总金额,订单状态,会员,会员手机号,是否VIP,报名数量,报名班级,下单时间\n";
            foreach ($dat as $k => $v) {
                switch ($v['state']) {
                    case 1 :
                        $v['state'] = '待支付';
                        break;
                    case 2 :
                        $v['state'] = '已支付';
                        break;
                    case 3 :
                        $v['state'] = '已退款';
                        break;
                }
                switch ($v['is_vip']) {
                    case 1 :
                        $v['is_vip'] = '否';
                        break;
                    case 2 :
                        $v['is_vip'] = '是';
                        break;
                }
                echo $k . ","
                    . $v["order_no"] . "\t,"
                    . $v["amount"] . "\t,"
                    . $v["state"] . "\t,"
                    . $v["username"] . "\t,"
                    . $v["phone"] . "\t,"
                    . $v["is_vip"] . "\t,"
                    . $v["number"] . "\t,"
                    . $v["name"] . "\t,"
                    . $v["intime"] . "\t,"
                    . "\n";
            }

        } else {
            $url = $_SERVER['REQUEST_URI'];
            session('url', $url);
            $this->display();

        }
    }

    public function tutor_class_view(){
        $id = I('id');
        $map['a.id'] = $id;
        $re = M('tutor_class_order')->alias('a')
            ->field('a.id,a.order_no,a.amount,a.state,a.number,a.is_vip,a.intime,b.username,b.phone,b.img,c.tutor_id,
            c.name,c.price,c.vip_price,c.img as timg,c.start_time,c.end_time,c.price,c.vip_price,a.number')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")//学员
            ->join("LEFT JOIN tk_tutor_class c on a.tutor_class_id = c.id")//导师线下班级
            ->where($map)
            ->find();
        //升级信息
        //$record = M('upgrade_record')->where(['upgrade_order_id' => $re['id']])->find();
        $this->assign(['re' => $re]);
        $this->display();
    }
}