<?php
namespace  Admin\Controller;
/**
 * 财务管理
 * @author 
 *
 */
use Think\Db;

class CaiwuController extends CommonController {
    function _initialize() {
        $nums = ['5','10','15','20','25','30','50','100'];
        $this->assign('nums',$nums);
    }

	// 充值列表
    public function index(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [
            'pay_on'=>['neq','']
        ];
        if (!empty($_GET['username'])){
            $data['b.phone|b.username|b.ID'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['pay_type'])){
            $data['a.pay_type'] = ['eq',$_GET['pay_type']];
            $this->assign('pay_type',$_GET['pay_type']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $data['a.intime'] = ['gt',$start];
            $this->assign('start',$_GET['start']);
        }elseif(empty($_GET['start']) && !empty($_GET['end'])){
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['lt',$end];
            $this->assign('end',$_GET['end']);
        }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['between',[$start,$end]];
            $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Recharge_record')->alias('a')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where($data)
            ->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Recharge_record')
            ->alias('a')
            ->field('a.*,b.username,b.ID,b.img,b.phone')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->limit($p->firstRow.','.$p->listRows)
            ->where($data)
            ->order('a.intime desc')
            ->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '充值列表' );

        $this->display();
    }

    /**
     * @提现记录
     */
    public function withdraw(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        if (!empty($_GET['username'])){
            $data['b.phone|b.username|b.ID'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['status'])){
            $data['a.status'] = ['eq',$_GET['status']];
            $this->assign('status',$_GET['status']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $data['a.intime'] = ['gt',$start];
            $this->assign('start',$_GET['start']);
        }elseif(empty($_GET['start']) && !empty($_GET['end'])){
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['lt',$end];
            $this->assign('end',$_GET['end']);
        }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['between',[$start,$end]];
            $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Withdraw')->alias('a')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where($data)
            ->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Withdraw')
            ->alias('a')
            ->field('a.*,b.username,b.ID,b.img,b.phone')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->limit($p->firstRow.','.$p->listRows)
            ->where($data)
            ->order('a.intime desc')
            ->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '提现记录' );

        $this->display();
    }


    /**
     * @编辑
     */
    public function edit(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $details = M('Withdraw')
            ->alias('a')
            ->field('a.*,b.username,b.img,b.phone,b.ID')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->where(['withdraw_id'=>$id])
            ->find();
        $this->assign('d',$details);
        $this->assign ( 'pagetitle', '详情' );
        $this->display();
    }


    /**
     * @审核
     */
    public function do_eidt(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $status = I('status');
        $wi = M('Withdraw')->find($id);
        $data = [
            'withdraw_id'=>$id,
            'status'=>$status,
            'uptime'=>time()
        ];
        if ($status==3){$data['cash_time']=time();}
        if (M('Withdraw')->save($data)){
            if ($status!=1){
                if ($status==2){
                    $get_money = M('User')->where(['user_id'=>$wi['user_id']])->getField('get_money') + $wi['k'];
                    M('User')->where(['user_id'=>$wi['user_id']])->save(['get_money'=>$get_money,'uptime'=>time()]);
                    $content = "您提现的".$wi['money']."元被驳回,具体请联系平台!";
                }else{
                    $content = "您提现的".$wi['money']."元已返现,请查看!";
                }
                M('Message')->add(['type'=>1,'user_id2'=>$wi['user_id'],'content'=>$content,'intime'=>time(),'date'=>date('Y-m-d',time())]);
            }
            $this->success('成功!',U('withdraw'));
        }else{
            $this->error('失败!',U('withdraw'));
        }
    }




    /**
     * @送礼记录
     */
    public function give_gift_list(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $data = [];
        if (!empty($_GET['username'])){
            $data['b.username|b.ID|c.name|d.username|d.ID'] = ['like','%'.$_GET['username'].'%'];
            $this->assign('username',$_GET['username']);
        }
        if (!empty($_GET['start']) && empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $data['a.intime'] = ['gt',$start];
            $this->assign('start',$_GET['start']);
        }elseif(empty($_GET['start']) && !empty($_GET['end'])){
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['lt',$end];
            $this->assign('end',$_GET['end']);
        }elseif(!empty($_GET['start']) && !empty($_GET['end'])){
            $start = strtotime($_GET['start']);
            $end = strtotime($_GET['end'])+(24*60*60-1);
            $data['a.intime'] = ['between',[$start,$end]];
            $this->assign('start',$_GET['start']);  $this->assign('end',$_GET['end']);
        }
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Give_gift')
            ->alias('a')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->join('__GIFT__ c on a.gift_id=c.gift_id')
            ->join('__USER__ d on a.user_id2=d.user_id')
            ->where($data)
            ->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Give_gift')
            ->alias('a')
            ->field('a.*,b.username,b.ID,b.img,b.phone,c.name,d.username as username2,d.ID as id2')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->join('__GIFT__ c on a.gift_id=c.gift_id')
            ->join('__USER__ d on a.user_id2=d.user_id')
            ->limit($p->firstRow.','.$p->listRows)
            ->where($data)
            ->order('a.intime desc')
            ->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());

        //统计
        $sum = M('Give_gift')
            ->alias('a')
            ->join('__USER__ b on a.user_id=b.user_id')
            ->join('__GIFT__ c on a.gift_id=c.gift_id')
            ->join('__USER__ d on a.user_id2=d.user_id')
            ->where($data)
            ->sum('jewel');
        $this->assign('sum',$sum);

        $this->assign ( 'pagetitle', '送礼记录' );

        $this->display();
    }

    /**
     * @兑换比例
     */
    public function convert_scale(){
        $data = [];
        //每页显示几条
        if (isset($_GET['nums'])){
            $nus  = intval($_GET['nums']);
        }else {
            $nus  = 10;
        }
        $this->assign("nus",$nus);
        $count = M('Convert_scale')->where($data)->count();//一共有多少条记录
        $p = getpage($count,$nus);
        $list =  M('Convert_scale')->limit($p->firstRow.','.$p->listRows)->where($data)->select();
        $this->assign('list',$list);
        $this->assign("show",$p->show());
        $this->assign ( 'pagetitle', '兑换比例' );
        $this->display();
    }

    /**
     * @添加(修改)映射
     */
    public function toadd_convert_scale(){
        $id = I('id');
        if (empty($id)){
            $this->assign ( 'pagetitle', '添加' );
        }else{
            $u = M('Convert_scale')->find($id);
            $this->assign('u',$u);
            $this->assign ( 'pagetitle', '编辑' );
        }
        $this->display();
    }
    /**
     * @添加(修改)
     */
    public function doadd_convert_scale(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('id');
        $data = [
            'k'=>I('k'),
            'meters'=>I('meters'),
        ];
        if (empty($id)){
            $data['intime'] = time();
            M('Convert_scale')->add($data) ? $this->success('添加成功!',U('convert_scale')) : $this->error('添加失败',U('convert_scale'));
        }else{
            $data['uptime'] = time();
            if (M('Convert_scale')->where(['convert_scale_id'=>$id])->save($data)){
                $this->success('编辑成功!',U('convert_scale'));
            }else{
                $this->error('编辑失败',U('convert_scale'));
            }
        }
    }
    /**
     * @删除
     */
    public function del_convert_scale(){
        if( !M()->autoCheckToken($_POST) ) $this->error('禁止站外提交！');
        unset($_POST['__hash__']);
        $id = I('ids');
        $rs = M('Convert_scale')->where(['convert_scale_id'=>['in',$id]])->delete();
        echo $rs ? 1 : 2;
    }





	
	
}