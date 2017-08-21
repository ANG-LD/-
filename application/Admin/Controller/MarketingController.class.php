<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/14
 * Time: 16:13
 */

namespace Admin\Controller;
class MarketingController extends BaseController
{
    public function index(){
        $map=[];
        $map['is_del'] = '1';
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Coupon')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Coupon")->where($map)->order("status desc,intime desc")->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加优惠券
     */
    public function add_coupon(){
        if(IS_POST){
            echo json_encode(D('Coupon')->check());
        }else{
            $this->display();
        }
    }

    /**
     *编辑优惠券
     */
    public function edit_coupon(){
        if(IS_POST){
            echo json_encode(D('Coupon')->check());
        }else{
            $id = I('id');
            $re = M('Coupon')->where(['id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Marketing/add_coupon');
        }
    }

    /**
     *删除优惠券
     */
    public function del_coupon(){
        if(IS_POST){
            $id = I('ids');
            $data['id'] = array('in',$id);
            $result = M('Coupon')->where($data)->save(['is_del'=>'2']);
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
            }else{
                echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
            }
        }
    }

    /**
     *发送优惠券
     */
    public function send_coupon(){
        if(IS_POST){
            $id = I('id');
            $coupon = M('Coupon')->where(['id'=>$id])->find();
            if(empty($coupon)){
                echo json_encode(array('status'=>'error','info'=>'优惠券错误'));
                die;
            }
            if($coupon['is_send'] == '2'){
                echo json_encode(array('status'=>'error','info'=>'该优惠券已经发送过了'));
                die;
            }
            if($coupon['end_time'] - time()<0){
                echo json_encode(array('status'=>'error','info'=>'该优惠券已经过期了'));
                die;
            }
            $member = M('Member')->field('member_id')->select();
            $data['coupon_id'] = $coupon['id'];
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['status'] = 1;
            foreach($member as $k=>$v){
                $member_id = $v['member_id'];
                if($coupon['number']>0) {
                    $check = M('MemberCoupon')->where(['coupon_id' => $coupon['id'], 'member_id' => $member_id])->find();
                    if (!$check) {
                        $data['member_id'] = $member_id;
                        $result = M('MemberCoupon')->add($data);
                        if ($result) {
                            M('Coupon')->where(['id' => $id])->setDec('number');
                        }
                    }
                }
            }
            M('Coupon')->where(['id'=>$id])->save(['is_send'=>'2']);
            echo json_encode(array('status'=>'ok','info'=>'发送成功'));
        }
    }

    /**
     * @编辑发送奖品
     */
    public function edit_record(){
        $map['a.id'] = I('id');
        $re = M('PrizeRecord')->alias('a')
            ->field("a.id,b.name,b.img,a.beizhu,a.intime,c.nickname,c.phone,a.type,
            a.beizhu,a.kuaidi,a.kuaidi_state,a.kuaidi_name")
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->join("LEFT JOIN __MEMBER__ c on a.mid = c.member_id")
            ->where($map)->find();
        $work_log = M('WorkLog')->alias('a')
            ->field('a.title,a.intime,b.uname')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.id")
            ->where(['table' => 'PrizeRecord', 'record_id' => I('id')])->order("a.intime desc")->select();
        $this->assign(['re'=>$re,'log' => $work_log]);
        $this->display();
    }

    /**
     *@修改物流信息
     */
    public function edit_kuaidi(){
        $id = I('id');
        $info = M('PrizeRecord')->where(['id'=>$id])->find();
        $data = $_GET;
        if(empty($data['kuaidi_name'])){
            echo json_encode(array('status'=>'error','info'=>'物流公司不能为空','class'=>'kuaidi_name'));
            die;
        }
        if(empty($data['kuaidi'])){
            echo json_encode(array('status'=>'error','info'=>'物流单号不能为空','class'=>'kuaidi'));
            die;
        }
        $result = M('PrizeRecord')->where(['id'=>$id])->save($data);
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
            work_log($table='PrizeRecord',$record_id = $data['id'],'1',$work);
            echo json_encode(array('status'=>'ok','info'=>'修改物流信息成功'));
        }else{
            echo json_encode(array('status'=>'error','info'=>'修改物流信息失败'));
        }
    }

    /**
     *@订单备注
     */
    public function beizhu(){
        if(IS_POST){
            $id = I('id');
            $beizhu = I('beizhu');
            $check = M('PrizeRecord')->where(['id'=>$id])->find();
            if($check['beizhu'] == $beizhu){
                echo json_encode(['status' => "error", 'info' => '备注未做改变!']);
                die;
            }
            $result = M('PrizeRecord')->where(['id'=>$id])->save(['beizhu'=>$beizhu]);
            if($result){
                work_log($table = 'PrizeRecord', $record_id = $id,'1', $work = '备注了记录');
                echo json_encode(['status' => "ok", 'info' => '备注成功!']);
            }else{
                echo json_encode(['status' => "error", 'info' => '备注信息失败!']);
            }
        }
    }

    /**
     *@删除记录
     */
    public function del_record(){
        if(IS_POST){
            $id = I('ids');
            $data['id'] = array('in',$id);
            $result = M('PrizeRecord')->where($data)->delete();
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
            }else{
                echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
            }
        }
    }

    /**
     *积分设置
     */
    public function integral(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['score']))           error("消费积分值设置不能为空");
            if(empty($data['money']))           error("抵扣金额值设置不能为空");
            if(empty($data['consumer']))        error("用户消费值设置不能为空");
            if(empty($data['give_score']))      error("得到积分值设置不能为空");
            $result = M('InstallScore')->where(['id'=>1])->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'设置积分数据成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>'设置积分数据失败'));
            }
        }else{
            $re = M('InstallScore')->where(['id'=>1])->find();
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     *@奖品设置
     */
    public function prize(){
        !empty($_GET['name'])       &&      $map['name'] = ['like','%'.I('name').'%'];
        $num = I('num');
        $map['is_del'] = 1;
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('Prize')->where($map)->count();
        $p = $this->getpage($count, $num);
        $count_chance = M('Prize')->where($map)->sum('chance');  //总基数
        $list = M('Prize')->where($map)->order("intime desc")
            ->limit($p->firstRow, $p->listRows)->select();
        $url = $_SERVER['REQUEST_URI'];
        $this->assign(['list'=>$list,'count_chance'=>$count_chance]);
        session('url', $url);
        $this->display();
    }

    /**
     * @添加转盘奖品
     */
    public function add_prize(){
        if(IS_POST){
            echo json_encode(D('Prize')->auth());
        }else{
            $goods = M('Goods')->where(['number'=>['gt',0],'is_del'=>'1'])->select();
            $this->assign(['goods'=>$goods]);
            $this->display();
        }
    }

    /**
     * @编辑转盘奖品
     */
    public function edit_prize(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Prize')->auth());
        }else{
            $m = M('Prize')->where(['prize_id'=>$id])->find();
            if($m['type'] == 1){
                $check = M('Goods')->where(['goods_id'=>$m['goods_id']])->find();
                $m['number'] = $check['number'];
                $m['sale_number'] = $check['sale_number'];
                $m['price'] = $check['price'];
                $m['sale_price'] = $check['sale_price'];
                $m['goods_img'] = $check['goods_img'];
            }
            $goods = M('Goods')->where(['number'=>['gt',0],'is_del'=>'1'])->select();
            $this->assign(['goods'=>$goods,'m'=>$m]);
            $this->display("Event/add_prize");
        }
    }

    /**
     *@ 中奖记录
     */
    public function prize_record(){
        !empty($_GET['name'])       &&      $map['b.name|b.nickname|b.phone'] = ['like','%'.I('name').'%'];
        $num = I('num');
        $map['a.type'] = 1;
        $map['a.is_used'] = 2;
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('PrizeRecord')->alias('a')
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->join("LEFT JOIN __MEMBER__ c on a.mid = c.member_id")
            ->where($map)->count();
        $p = $this->getpage($count, $num);
        $list =  M('PrizeRecord')->alias('a')
            ->field("a.id,b.name,b.img,a.beizhu,a.intime,c.nickname,c.phone,a.type")
            ->join("LEFT JOIN __PRIZE__ b on a.prize_id = b.prize_id")
            ->join("LEFT JOIN __MEMBER__ c on a.mid = c.member_id")
            ->where($map)->order("a.intime desc")
            ->limit($p->firstRow, $p->listRows)->select();
        $url = $_SERVER['REQUEST_URI'];
        $this->assign(['list'=>$list,'page'=>$p->show()]);
        session('url', $url);
        $this->display();
    }

    /**
     *@删除奖励
     */
    public function del_prize(){
        $id = I('ids');
        $data['prize_id'] = array('in',$id);
        $result = M('Prize')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@获得实物奖品
     */


}