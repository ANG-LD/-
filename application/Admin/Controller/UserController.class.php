<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/12
 * Time: 17:10
 */

namespace Admin\Controller;

class UserController extends BaseController
{
    /**
     * 用户列表
     */
    public function index(){
        $map=[];
        !empty($_GET['username']) && $map['username|phone'] = ['like','%'.I('username').'%'];
        $map['type'] = 1;
        $map['is_del'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data=M('User')->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $key=>$val){
            $data[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
        }
        $count = M('User')->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $this->assign(['list'=>$data,'count'=>$count]);
        $act = I("get.act");
        if($act=="download"){
            $dat=M('User')->where($map)->order('user_id desc')->select();
            foreach($dat as $key=>$val){
                $dat[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
            }
            $str = '会员表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,用户名称,手机号码,性别,省,市,区(县),具体地址,充值总额,消费总额,充值积分,普通积分,注册时间\n";
            foreach ($dat as $k=>$v){
                switch($v['sex']){
                    case 1 :
                        $v['sex'] = '男';
                        break;
                    case 2 :
                        $v['sex'] = '女';
                        break;
                    case 3 :
                        $v['sex'] = '保密';
                        break;
                }
                $v['recharge'] = M('Recharge')->where(['member_id'=>$v['member_id'],'pay_status'=>2])->sum('amount');
                echo $k.","
                    .$v["nickname"]."\t,"
                    .$v["phone"]."\t,"
                    .$v["sex"]."\t,"
                    .$v["province"]."\t,"
                    .$v["city"]."\t,"
                    .$v["area"]."\t,"
                    .$v["address"]."\t,"
                    .$v["recharge"]."\t,"
                    .$v["consumption"]."\t,"
                    .$v["amount"]."\t,"
                    .$v["score"]."\t,"
                    .$v["intime"]."\t,"
                    ."\n";
            }
        }else {
            $url =$_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     * 用户列表
     */
    public function tutor(){
        $map=[];
        !empty($_GET['username']) && $map['username|phone'] = ['like','%'.I('username').'%'];
        $map['type'] = 2;
        $map['is_del'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data=M('User')->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $key=>$val){
            $data[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
        }
        $count = M('User')->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $this->assign(['list'=>$data,'count'=>$count]);
        $act = I("get.act");
        if($act=="download"){
            $dat=M('User')->where($map)->order('user_id desc')->select();
            foreach($dat as $key=>$val){
                $dat[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
            }
            $str = '会员表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,用户名称,手机号码,性别,省,市,区(县),具体地址,充值总额,消费总额,充值积分,普通积分,注册时间\n";
            foreach ($dat as $k=>$v){
                switch($v['sex']){
                    case 1 :
                        $v['sex'] = '男';
                        break;
                    case 2 :
                        $v['sex'] = '女';
                        break;
                    case 3 :
                        $v['sex'] = '保密';
                        break;
                }
                $v['recharge'] = M('Recharge')->where(['member_id'=>$v['member_id'],'pay_status'=>2])->sum('amount');
                echo $k.","
                    .$v["nickname"]."\t,"
                    .$v["phone"]."\t,"
                    .$v["sex"]."\t,"
                    .$v["province"]."\t,"
                    .$v["city"]."\t,"
                    .$v["area"]."\t,"
                    .$v["address"]."\t,"
                    .$v["recharge"]."\t,"
                    .$v["consumption"]."\t,"
                    .$v["amount"]."\t,"
                    .$v["score"]."\t,"
                    .$v["intime"]."\t,"
                    ."\n";
            }
        }else {
            $url =$_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }
    /**
     * @修改用户
     */
    public function edit_user(){
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $this->assign('sheng',$sheng);
        $id =I('uid');
        if(!empty($id)) {
            $user = M('User')->where(array('user_id' => $id))->find();
            $fid = M('Areas')->where(array('name' => $user['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['pid'] = $fid;
                $data['level'] = 2;
                $user['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $user['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['pid'] = $fid2;
                $date['level'] = 3;
                $user['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $user['qu'] = null;
            }
            $user['city_id'] = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            $user['area_id'] = M('Areas')->where(array('name' => $user['area'], 'level' => 3))->getField('id');
            $this->assign('re', $user);
        }
        if(IS_POST){
            echo json_encode(D('User')->auth());
        }else {
            $this->display('User/add_user');
        }
    }

    /**
     * @添加会员
     */
    public function add_user(){
        $sheng = M('Areas')->where(['level'=>1])->select();
        $this->assign('sheng',$sheng);
        if(IS_POST){
            echo json_encode(D('User')->auth());
        }else{
            $this->display();
        }
    }

    /**
     * @添加导师
     */
    public function add_tutor(){
        $sheng = M('Areas')->where(['level'=>1])->select();
        $this->assign('sheng',$sheng);
        if(IS_POST){
            echo json_encode(D('User')->auth());
        }else{
            $this->display();
        }
    }

    /**
     * @修改用户
     */
    public function edit_tutor(){
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $this->assign('sheng',$sheng);
        $id =I('uid');
        if(!empty($id)) {
            $user = M('User')->where(array('user_id' => $id))->find();
            $fid = M('Areas')->where(array('name' => $user['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['pid'] = $fid;
                $data['level'] = 2;
                $user['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $user['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['pid'] = $fid2;
                $date['level'] = 3;
                $user['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $user['qu'] = null;
            }
            $user['city_id'] = M('Areas')->where(array('name' => $user['city'], 'level' => 2))->getField('id');
            $user['area_id'] = M('Areas')->where(array('name' => $user['area'], 'level' => 3))->getField('id');
            $this->assign('re', $user);
        }
        if(IS_POST){
            echo json_encode(D('User')->auth());
        }else {
            $this->display('User/add_user');
        }
    }

    /**
     *@修改会员账户状态
     */
    public function change_stop_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('User')->where(['user_id'=>$id])->getField('is_stop');
            $abs = 3 - $status;
            //$arr = ['默认状态','开启状态'];
            $result = M('User')->where(['user_id'=>$id])->save(['is_stop'=>$abs]);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$abs));
                exit;
            }else{
                echo json_encode(array('status'=>'error','info'=>'切换状态失败'));
                exit;
            }
        }
    }

    /**
     *@用户信息
     */
    public function user_show(){
        layout(false);
        $this->display();
    }

    /**
     * @获取市
     */
    public function get_area(){
        $value = I('value');
        $type = I('type');
        if (isset($value)){
            if ($type==1){
                $data['level'] = 2;
                $data['pid'] = array('eq',$value);
                $type_list="<option value=''>请选择（市）</option>";
                $shi = M('Areas')->where($data)->select();
            }else {
                $data['level'] = 3;
                $data['pid'] = array('eq',$value);
                $type_list="<option value=''>请选择（区/县）</option>";
                $shi = M('Areas')->where($data)->select();
            }
            foreach($shi as $k=>$v){
                $type_list.="<option value=".$shi[$k]['id'].">".$shi[$k]['name']."</option>";
            }
            echo $type_list;
        }
    }

    /**
     * @添加用户历史
     * Enter description here ...
     */
    public function histroy(){
        //今天的数据
        $a = Date('Y-m-d',time()); //今天开始时间
        $b = Date(strtotime("+1 day")); //今天结束时间
        $data['intime'] = array('between',array($a,$b));
        $data['is_hand'] = 2;
        $mem = M('Member')->where($data)->select();
        $this->assign('list',$mem);
        //昨天的数据
        $c = Date('Y-m-d H:i:s',strtotime("-1 day")); //昨天开始时间
        $d = Date('Y-m-d',time()); //昨天结束时间
        $dat['intime'] = array('between',array($c,$d));
        $dat['is_hand'] = 2;
        $mem2 = M('Member')->where($dat)->select();
        $this->assign('list2',$mem2);
        //昨天之前的数据
        $e = Date('Y-m-d',strtotime("-1 day")); //昨天开始时间
        $da['intime'] = array('lt',$e);
        $da['is_hand'] = 2;
        $mem3 = M('Member')->where($da)->select();
        $this->assign('list3',$mem3);
        $this->display();
    }
    /**
     * @删除用户(伪删除)
     */
    public function del_user(){
        $id = I('ids');
        $data['user_id'] = array('in',$id);
        $data['is_del'] = 2;
        $data['del_time'] = date("Y-m-d H:i:s",time());
        $user = M('User')->save($data);
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }
    /**
     * @会员详情
     */
    public function view(){
        $uid    =   I('get.uid');
        $view   = M('User')->find($uid);
        $view['grade'] = M('Grade')->where(['id'=>$view['grade']])->getField('name');
        $view['recharge'] = M('Recharge')->where(['member_id'=>$uid,'pay_status'=>2])->sum('amount');
        $this->assign(['view'=>$view]);
        $type = I('type');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        switch($type){
            case 1:       //充值
                $map['user_id'] = $uid;
                $map['pay_status'] = 2;
                $page=I("get.p");
                $data=M("Recharge")
                    ->field('pay_number,amount,pay_type,intime,score')
                    ->where($map)->page($page)->limit($num)->order('intime desc')->select();
                $count =M("Recharge")->where($map)->count(); // 查询满足要求的总记录数
                $sum = M('Recharge')->where($map)->sum('amount');
                $tag = '充值总额';
                $this->assign(['list'=>$data,'sum'=>$sum,'tag'=>$tag]);
                $this->page3($count,$num);
                break;
            case 2:      //提现
                $map['a.mid'] = $uid;
                $map['a.status'] = 3;
                $page=I("get.p");
                $data=M("Withdraw")->alias('a')
                    ->field('a.score,a.amount,a.date,b.bank_card,b.pay_type')
                    ->join("LEFT JOIN __BANK_CARD__ b on a.card_id = b.id")
                    ->where($map)->page($page)->limit($num)->order('a.intime desc')->select();
                $count = M("Withdraw")->alias('a')
                    ->join("LEFT JOIN __BANK_CARD__ b on a.card_id = b.id")
                    ->where($map)->count(); // 查询满足要求的总记录数
                $sum = M("Withdraw")->alias('a')
                    ->join("LEFT JOIN __BANK_CARD__ b on a.card_id = b.id")
                    ->where($map)->sum('a.amount');
                $tag = '提现总额';
                $this->assign(['list'=>$data,'sum'=>$sum,'tag'=>$tag]);
                $this->page3($count,$num);
                break;
            case 3:       //指点记录
                $map=[];
                $map['a.user_id']   =  $uid;
                $map['a.state']     =   2;
                $map['a.is_del']    = '1';
                $count = M('teach_order')->alias('a')
                    ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                    ->where($map)->count();
                $p = $this->getpage($count, $num);
                $data = M('teach_order')->alias('a')
                    ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.teach_status,c.phone as tphone,c.username as tusername')
                    ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                    ->where($map)->limit($p->firstRow, $p->listRows)->order("a.intime desc")->select();
                // 查询满足要求的总记录数
                $sum = M('teach_order')->alias('a')
                    ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.teach_status,c.phone as tphone,c.username as tusername')
                    ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")
                    ->where($map)->sum('a.amount');
                $tag = '指点总额';
                $this->assign(['list'=>$data,'sum'=>$sum,'tag'=>$tag,'page'=>$p->show()]);;

                break;
            case 4:        //升级
                $map=[];
                $map['a.user_id']   =  $uid;
                $map['a.state']     =   2;
                $map['a.is_del']    = 1;
                $count = M('upgrade_order')->alias('a')
                    ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                    ->join("LEFT JOIN __UPGRADE_RECORD__ d on a.id = d.upgrade_order_id")//记录
                    ->where($map)->count();
                $p = $this->getpage($count, $num);
                $data = M('upgrade_order')->alias('a')
                    ->field('a.id,a.order_no,a.amount,a.state,a.intime,a.status,c.phone as tphone,c.username as tusername,d.date_value')
                    ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                    ->join("LEFT JOIN __UPGRADE_RECORD__ d on a.id = d.upgrade_order_id")//记录
                    ->where($map)->limit($p->firstRow, $p->listRows)->order("a.intime desc")->select();
                // 查询满足要求的总记录数
                $sum = M('upgrade_order')->alias('a')
                    ->join("LEFT JOIN __USER__ c on a.user_id2 = c.user_id")//导师
                    ->join("LEFT JOIN __UPGRADE_RECORD__ d on a.id = d.upgrade_order_id")//记录
                    ->where($map)->sum('a.amount');
                $tag = '升级总额';
                $this->assign(['list'=>$data,'sum'=>$sum,'tag'=>$tag,'page'=>$p->show()]);;
                break;

            case 5:         //关注
                $map=[];
                $map['a.user_id']   =  $uid;
                $count = M('Follow')->alias('a')
                    ->join('__USER__ b on a.user_id2=b.user_id')
                    ->where($map)
                    ->count();//一共有多少条记录
                $p = $this->getpage($count, $num);
                $list = M('Follow')->alias('a')
                    ->field('a.*,b.username,b.phone')
                    ->join('__USER__ b on a.user_id2=b.user_id')
                    ->where($map)->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $this->assign(['list'=>$list,'page'=>$p->show()]);
                break;
            case 6:       // 粉丝
                $map=[];
                $map['a.user_id2']   =  $uid;
                $count = M('Follow')->alias('a')
                    ->join('__USER__ b on a.user_id2=b.user_id')
                    ->where($map)
                    ->count();//一共有多少条记录
                $p = $this->getpage($count, $num);
                $list = M('Follow')->alias('a')
                    ->field('a.*,b.username,b.phone')
                    ->join('__USER__ b on a.user_id2=b.user_id')
                    ->where($map)->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $this->assign(['list'=>$list,'page'=>$p->show()]);
                break;
            case 7:        //送礼
                $map=[];
                $map['a.user_id']   =  $uid;
                $count = M('Give_gift')->alias('a')
                    ->join('left join tk_live as b on a.live_id=b.live_id')
                    ->join('left join tk_user as c on a.user_id=c.user_id')
                    ->join('left join tk_gift as d on a.gift_id=d.gift_id')
                    ->where($map)
                    ->count();//一共有多少条记录
                $p = getpage($count, $num);
                $list = M('Give_gift')->alias('a')
                    ->field('a.*,b.title,c.username,c.phone,d.name')
                    ->join('left join tk_live as b on a.live_id=b.live_id')
                    ->join('left join tk_user as c on a.user_id2=c.user_id')
                    ->join('left join tk_gift as d on a.gift_id=d.gift_id')
                    ->where($map)
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $sum = M('Give_gift')->alias('a')
                    ->join('left join tk_live as b on a.live_id=b.live_id')
                    ->join('left join tk_user as c on a.user_id2=c.user_id')
                    ->join('left join tk_gift as d on a.gift_id=d.gift_id')
                    ->where($map)->sum('a.jewel');
                $tag = '送礼总额';
                $this->assign(['list'=>$list,'sum'=>$sum,'tag'=>$tag,'page'=>$p->show()]);;
                break;
            case 8:       //收礼
                $map=[];
                $map['a.user_id2']   =  $uid;
                $count = M('Give_gift')->alias('a')
                    ->join('left join tk_live as b on a.live_id=b.live_id')
                    ->join('left join tk_user as c on a.user_id=c.user_id')
                    ->join('left join tk_gift as d on a.gift_id=d.gift_id')
                    ->where($map)
                    ->count();//一共有多少条记录
                $p = getpage($count, $num);
                $list = M('Give_gift')->alias('a')
                    ->field('a.*,b.title,c.username,c.phone,d.name')
                    ->join('left join tk_live as b on a.live_id=b.live_id')
                    ->join('left join tk_user as c on a.user_id=c.user_id')
                    ->join('left join tk_gift as d on a.gift_id=d.gift_id')
                    ->where($map)
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->order('a.intime desc')
                    ->select();
                $sum = M('Give_gift')->alias('a')
                    ->join('left join tk_live as b on a.live_id=b.live_id')
                    ->join('left join tk_user as c on a.user_id=c.user_id')
                    ->join('left join tk_gift as d on a.gift_id=d.gift_id')
                    ->where($map)->sum('a.jewel');
                $tag = '收礼总额';
                $this->assign(['list'=>$list,'sum'=>$sum,'tag'=>$tag,'page'=>$p->show()]);;
                break;
            case 9:
                $map=[];
                $map['a.user_id']   =  $uid;
                $count = M('Live')->alias('a')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->where($map)->count();//一共有多少条记录
                $p = getpage($count, $num);
                $list = M('Live')->alias('a')
                    ->field("a.*")
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->where($map)->order('a.intime desc')
                    ->select();
                foreach ($list as $k => $v) {
                    $gift_count = M('Give_gift')->where(['live_id' => $v['live_id']])->sum('jewel');
                    $gift_count ? $list[$k]['gift_count'] = $gift_count : $list[$k]['gift_count'] = '0';
                }
                $this->assign(['list'=>$list,'page'=>$p->show()]);
                break;
            case 10:
                $map = [];
                $map['a.user_id'] = $uid;
                $map['a.is_del'] = '1';
                $count = M('Live_store')->alias('a')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->join('__LIVE__ c on a.live_id=c.live_id')
                    ->where($map)->count();//一共有多少条记录
                $p = getpage($count, $num);
                $list = M('Live_store')->alias('a')
                    ->field('a.*,b.username,b.img,b.sex,b.phone,b.ID,c.title')
                    ->join('__USER__ b on a.user_id=b.user_id')
                    ->join('__LIVE__ c on a.live_id=c.live_id')
                    ->limit($p->firstRow . ',' . $p->listRows)
                    ->where($map)->order('a.intime desc')
                    ->select();
                $this->assign(['list'=>$list,'page'=>$p->show()]);
                break;
            case 11:
                $map = [];
                $map['a.user_id'] = $uid;
                $map['a.is_del'] = '1';
                $count = M("Video")->alias('a')
                    ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                    ->where($map)->count(); // 查询满足要求的总记录数
                $p = getpage($count, $num);
                $list= M("Video")->alias('a')
                    ->field('a.title,a.video_id,a.video_img,a.url,a.watch_nums,a.zan,a.intime,a.is_shenhe,b.username,b.phone,b.ID')
                    ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                    ->where($map)->limit($p->firstRow . ',' . $p->listRows)->order('a.intime desc')->select();
                $this->assign(['list'=>$list,'page'=>$p->show()]);
                break;
        }
        $this->display();
    }

    //成为导师
    public function change_type(){
        if(IS_POST){
            $ids = I('ids');
            $result = M('User')->where(['user_id'=>$ids])->save(['type'=>2]);
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'操作成功!','url'=>session('url')]);
             }else{
                echo json_encode(['status'=>"error",'info'=>'操作失败!']);
            }
        }
    }

    /**
     * @已删除会员
     */
    public function is_del_user(){
        $map=array();

        !empty($_GET['nickname']) && $map['nickname|phone'] = $_GET['nickname'];
        $map['is_del'] = 2;
        $map['type'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data=M("User")->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $key=>$val){
            $data[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
        }
        $this->assign('list',$data);
        $count =M("User")->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $act=I("get.act");
        if($act=="download"){
            $dat=M('User')->where($map)->order('member_id desc')->select();
            foreach($dat as $key=>$val){
                $dat[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
            }
            $str = '已删除会员表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,用户名称,手机号码,性别,省,市,区(县),具体地址,充值总额,消费总额,充值积分,普通积分,注册时间\n";
            foreach ($dat as $k=>$v){
                switch($v['sex']){
                    case 1 :
                        $v['sex'] = '男';
                        break;
                    case 2 :
                        $v['sex'] = '女';
                        break;
                    case 3 :
                        $v['sex'] = '保密';
                        break;
                }
                $v['recharge'] = M('Recharge')->where(['member_id'=>$v['member_id'],'pay_status'=>2])->sum('amount');
                echo $k.","
                    .$v["nickname"]."\t,"
                    .$v["phone"]."\t,"
                    .$v["sex"]."\t,"
                    .$v["province"]."\t,"
                    .$v["city"]."\t,"
                    .$v["area"]."\t,"
                    .$v["address"]."\t,"
                    .$v["recharge"]."\t,"
                    .$v["consumption"]."\t,"
                    .$v["amount"]."\t,"
                    .$v["score"]."\t,"
                    .$v["intime"]."\t,"
                    ."\n";
            }
        }else {
            $url =$_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }
    /**
     * @删除（彻底删除）
     */
    public function del_user_true(){
        $id = I('ids');
        $data['user_id'] = array('in',$id);
        $user = M('User')->where($data)->delete();
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }
    /**
     * @恢复会员记录
     *
     */
    public function recovery_user(){
        $id = I('ids');
        $data['user_id'] = array('in',$id);
        $user = M('User')->where($data)->save(array('is_del'=>1));
        if($user){
            echo json_encode(['status'=>"ok",'info'=>'记录恢复成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'记录恢复失败!']);
        }
    }

    /**
     *会员等级记录
     */
    public function grade_config(){
        $list = M('Grade')->select();
        $this->assign(['list'=>$list]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加会员等级记录
     */
    public function add_grade(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status'=>'error','info'=>'等级名称不能为空'));
                die;
            }
            if(!is_numeric($data['value'])){
                echo json_encode(array('status'=>'error','info'=>'消费金额不是数字类型'));
                die;
            }
            if(empty($data['times']))       $data['times'] = 1;
            $last = M('Grade')->order("grade_id desc")->limit(1)->find();
            if($data['value']<=$last['value']){
                echo json_encode(array('status'=>'error','info'=>'消费金额不能小于前一等级'));
                die;
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('Grade')->add($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'添加记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'添加记录失败'));
            }
        }else{
            $this->display();
        }
    }

    /**
     *编辑会员等级记录
     */
    public function edit_grade(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status'=>'error','info'=>'等级名称不能为空'));
                die;
            }
            if(!is_numeric($data['value'])){
                echo json_encode(array('status'=>'error','info'=>'消费金额不是数字类型'));
                die;
            }
            if($id>1) {
                $last = M('Grade')->where(['grade_id'=>$id-1])->find();
                if ($data['value'] <= $last['value']) {
                    echo json_encode(array('status' => 'error', 'info' => '消费金额不能小于前一等级'));
                    die;
                }
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('Grade')->where(['grade_id'=>$id])->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑记录失败'));
            }
        }else{
            $re = M('Grade')->where(['grade_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('User/add_grade');
        }
    }

    /**
     *账号安全
     */
    public function change_account(){
        check_auth();
        $uid = I('uid');
        if(IS_POST){
            $data['phone'] = I('phone');
            $user = M('User')->where(['user_id'=>$uid])->find();
//            if(empty($data['phone'])){
//                echo json_encode(array('status'=>'error','info'=>'会员账号不能为空'));
//                die();
//            }
            $data['money'] = I('money');
            if($data['amount']<0){
                echo json_encode(array('status'=>'error','info'=>'余额数不正确'));
                die();
            }
            $data['score'] = I('score');
            $data['score'] ? $data['score'] : $data['score'] = 0;
            $code['content'] = I('content1');
            $code2['detail'] = I('content2');
            if($data['score']<0){
                echo json_encode(array('status'=>'error','info'=>'积分数不正确'));
                die();
            }
            $password = I('password');
            $pay_password = I('pay_password');
            if(!empty($pay_password)){
                if(!ctype_digit($pay_password))         error("支付密码请设置6位数字");
                if(strlen($pay_password) != 6)          error("请设置6位数字密码");
            }
            if(!empty($password))		$data['password'] = encrypt($password);
            if(!empty($pay_password))		$data['pay_password'] = encrypt($pay_password);
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('User')->where(['user_id'=>$uid])->save($data);
            if($result){
                if($data['money'] > $user['money']){
                    $code['type'] = 1;
                    $amount = $data['money']-$user['money'];
                }
                if($data['money'] < $user['money']){
                    $code['type'] = 2 ;
                    $amount = $user['money']- $data['money'];
                }
                if($user['money'] != $data['money']){
                    if(empty($code['content'])){
                        echo json_encode(array('status'=>'error','info'=>'请填写余额变更说明'));
                        die();
                    }
                    $result = set_amount($user['user_id'],$amount,$code['type'],$code['content']);
                }

                if($data['score'] > $user['score']){
                    $code2['type'] = 1;
                    $code2['number'] = $data['score']-$user['score'];
                }
                if($data['score'] < $user['score']){
                    $code2['type'] = 2 ;
                    $code2['number'] = $user['score']-$data['score'];
                }
//                if($user['score'] != $data['score']){
//                    if(empty($code2['detail'])){
//                        echo json_encode(array('status'=>'error','info'=>'请填写积分变更说明'));
//                        die();
//                    }
//                    $code2['member_id'] = $user['usre_id'];
//                    $code2['intime']    = date("Y-m-d H:i:s",time());
//                    $code2['order_type']    = '2';
//                    M('ScoreRecord')->add($code2);
//                }
                if($data['phone'] != $user['phone']) $work= '修改了登录账号,原账号是：'.$user['phone'] .'。';
                if($data['money'] != $user['money']) $work.= '修改了余额,原余额是：'.$user['money'] .'。';
                if($data['score'] != $user['score']) $work.= '修改了积分,原积分是：'.$user['score'] .'。';
                if(!empty($password)){
                    if($data['password'] != $user['password'])		 $work .= '修改了登录密码。';
                }
                if(!empty($pay_password)){
                    if($data['pay_password'] != $user['pay_password'])		 $work .= '修改了支付密码。';
                }
                work_log($table='User',$record_id = $uid,$type='2',$work);
                echo json_encode(array('status'=>'ok','info'=>'修改记录成功','url'=>session('url')));
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>'修改记录失败'));
                die;
            }
        }else{
            $re = M('User')->where(['user_id'=>$uid])->find();
            $work_log = M('WorkLog')->alias('a')
                ->field('a.title,a.intime,b.name')
                ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.id")
                ->where(['a.table'=>'User','record_id'=>$uid,'a.type'=>2])->order("a.intime desc")->select();
            $this->assign(['re'=>$re,'log'=>$work_log]);
        $this->display();
    }
    }

    /**
     *@会员等级说明
     */
    public function grade_explain(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['content'])){
                echo json_encode(['status'=>'error','info'=>'协议内容不能为空']);
                die;
            }
            $result = M('Notice')->where(['id'=>'12'])->save($data);
            if($result){
                echo json_encode(['status'=>'ok','info'=>'编辑会员等级说明成功']);
            }else{
                echo json_encode(['status'=>'error','info'=>'编辑会员等级说明失败']);
            }
        }else{
            $re = M('Notice')->where(['id'=>12])->find();
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     * @已删除导师
     */
    public function is_del_tutor(){
        $map=array();
        !empty($_GET['nickname']) && $map['nickname|phone'] = $_GET['nickname'];
        $map['is_del'] = 2;
        $map['type'] = 2;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data=M("User")->where($map)->page($page)->limit($num)->order('intime desc')->select();
        foreach($data as $key=>$val){
            $data[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
        }
        $this->assign('list',$data);
        $count =M("User")->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $act=I("get.act");
        if($act=="download"){
            $dat=M('User')->where($map)->order('member_id desc')->select();
            foreach($dat as $key=>$val){
                $dat[$key]['grade'] = M('Grade')->where(['grade_id'=>$val['grade']])->getField('name');
            }
            $str = '已删除会员表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,用户名称,手机号码,性别,省,市,区(县),具体地址,充值总额,消费总额,充值积分,普通积分,注册时间\n";
            foreach ($dat as $k=>$v){
                switch($v['sex']){
                    case 1 :
                        $v['sex'] = '男';
                        break;
                    case 2 :
                        $v['sex'] = '女';
                        break;
                    case 3 :
                        $v['sex'] = '保密';
                        break;
                }
                $v['recharge'] = M('Recharge')->where(['member_id'=>$v['member_id'],'pay_status'=>2])->sum('amount');
                echo $k.","
                    .$v["nickname"]."\t,"
                    .$v["phone"]."\t,"
                    .$v["sex"]."\t,"
                    .$v["province"]."\t,"
                    .$v["city"]."\t,"
                    .$v["area"]."\t,"
                    .$v["address"]."\t,"
                    .$v["recharge"]."\t,"
                    .$v["consumption"]."\t,"
                    .$v["amount"]."\t,"
                    .$v["score"]."\t,"
                    .$v["intime"]."\t,"
                    ."\n";
            }
        }else {
            $url =$_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }




}