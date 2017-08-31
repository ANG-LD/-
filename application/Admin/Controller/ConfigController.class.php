<?php
namespace Admin\Controller;
use Think\Controller;
class ConfigController extends BaseController {
    /**
     * @数据库备份
     */
    public function index(){
        if(IS_POST){
            $database = C('DB_NAME');//数据库名
            $options = array(
                'hostname' => C('DB_HOST'),//ip地址
                'charset'  => C('DB_CHARSET'),//编码
                'filename' => $_POST['name'].'.sql',//文件名
                'username' => C('DB_USER'),
                'password' => C('DB_PWD')       //密码
            );
            mysql_connect($options['hostname'],$options['username'],$options['password'])or die("不能连接数据库!");
            mysql_select_db($database) or die("数据库名称错误!");
            mysql_query("SET NAMES '{$options['charset']}'");

            $tables = list_tables($database);
            $filename = sprintf($options['filename'],$database);
            $fp = fopen($filename, 'w');
            foreach ($tables as $table) {
                dump_table($table, $fp);
            }
            fclose($fp);
            $file_name=$options['filename'];
            Header("Content-type:application/octet-stream");
            Header("Content-Disposition:attachment;filename=".$file_name);
            readfile($file_name);
            exit;
        }else{
            $this->display();
        }
    }
	/**
	 * @修改配置
	 */ 
	public function do_one(){
	    $type = I('type');
	    $value = I('value');
	    $no = M('Notice')->where(array('notice_id'=>$type))->save(array('content'=>$value,'time'=>time()));
	    echo $no ? 1 : 0; 
	}
	/**
	 * @账号配置（映射）
	 */ 
	public function account(){
        if(IS_POST){
            $data = array(
                "title"          =>  I('title'),
                "appid"          =>  I('appid'),
                "secretkey"      =>  I('secretkey'),
                "jg_appkey"      =>  I('jg_appkey'),
                "jg_secret"      =>  I('jg_secret'),
                "hx_client_id"   =>  I('hx_client_id'),
                "hx_secret"      =>  I('hx_secret'),
                "hx_appkey_1"    =>  I('hx_appkey_1'),
                "hx_appkey_2"    =>  I('hx_appkey_2'),
                "default_verify" =>  I('default_verify'),
                "ios_version"    =>  I('ios_version'),
                "gao_watch_num"  =>  I('gao_watch_num'),
                "zuan_watch_num"  =>  I('zuan_watch_num'),
                "pu_watch_num"  =>  I('zuan_watch_num'),
            );
            $result = M('System')->where(['id'=>1])->save($data);
            if ($result) {
                echo json_encode(['status' => "ok", 'info' => '修改账号配置成功!']);
                die;
            } else {
                echo json_encode(['status' => "error", 'info' => '修改账号配置失败!']);
                die;
            }

        }else{
            $system = M('System')->where(array('id'=>1))->find();
            $this->assign('tem',$system);
            $this->display();
        }
	}

    /**
     * @关于我们
     */
    public function about(){
       if(IS_POST){
            $extend = I('extend');
            if(!$extend){
                echo 0;
                die;
            }
            echo M('About_us')->where(array('about_us_id'=>1))->save(array('dis'=>$extend)) ? 1 : 0;
            die;
        }
        $this->assign('content',htmlspecialchars_decode(M('About_us')->where(array('about_us_id'=>1))->getField('dis')));
        $this->display();
    }
    /**
     * @关于我们
     */
    public function clause(){
       if(IS_POST){
            $extend = I('extend');
            if(!$extend){
                echo 0;
                die;
            }
            echo M('About_us')->where(array('about_us_id'=>2))->save(array('dis'=>$extend)) ? 1 : 0;
            die;
        }
        $this->assign('content',htmlspecialchars_decode(M('About_us')->where(array('about_us_id'=>2))->getField('dis')));
        $this->display();
    }


    /**
     *@系统通知
     */
    public function notice(){
        $map = [];
        $map['is_del'] = '1';
        !empty($_GET['status']) && $map['status'] = $_GET['status'];
        !empty($_GET['content']) && $map['content'] = array('like','%'.$_GET['content'].'%');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data = M("Notice")->where($map)->page($page)->limit($num)->order('ctime desc')->select();
        $this->assign('list',$data);
        $count = M("Notice")->where($map)->count(); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加系统通知
     */
    public function add_notice(){
        if(IS_POST){
            echo json_encode(D('Notice')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑系统消息
     */
    public function edit_notice(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Notice')->auth());
        }else{
            $notice = M('Notice')->where(['id'=>$id])->find();
            $this->assign(['re'=>$notice]);
            $this->display('Config/add_notice');
        }
    }

    /**
     *删除系统消息
     */
    public function del_notice(){
        $ids = I('ids');
        $result = M('Notice')->where(['id'=>['in',$ids]])->save(['is_del'=>'2']);
        if($result !==false){
            echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
        }
    }


    /**
     *@协议列表
     */
    public function xieyi(){
        $list = M('Xieyi')->select();
        $this->assign(['list'=>$list]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑协议
     */
    public function edit_xieyi(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Xieyi')->auth());
        }else{
            $re = M('Xieyi')->where(['aid'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     *@搜索热词设置
     */
    public function hot_words(){
        $map=[];
        !empty($_GET['name']) && $map['name'] = ['like','%'.I('name').'%'];
        !empty($_GET['type']) && $map['type'] = I('type');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $page=I("get.p");
        $data=M("HotWord")->where($map)->page($page)->limit($num)->order('intime desc')->select();
        $this->assign('list',$data);
        $count = count($data); // 查询满足要求的总记录数
        $this->page3($count,$num);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display('Config/hot_words');
    }

    /**
     *@添加热词搜索
     */
    public function add_hot_word(){
        if(IS_POST){
            echo json_encode(D('HotWord')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑热词搜索
     */
    public function edit_hot_word(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('HotWord')->auth());
        }else{
            $re = M('HotWord')->where(['hot_word_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Config/add_hot_word');
        }
    }

    /**
     *@删除搜索热词
     */
    public function del_hot_words(){
        $id = I('ids');
        $map['hot_word_id'] = array('in',$id);
        $result = M('HotWord')->where($map)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@修改热词默认状态
     */
    public function change_word_state(){
        $hot_word_id = I('id');
        $check = M('HotWord')->where(['hot_word_id'=>$hot_word_id])->find();
        $result = M('HotWord')->where(['hot_word_id'=>$hot_word_id])->save(['state'=>1]);
        if($result !==false){
            $result =  M('HotWord')->where(['type'=>$check['type'],'hot_word_id'=>array('neq',$check['hot_word_id'])])->save(['state'=>0]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'操作失败!']);
        }

        if($result !==false){
            echo json_encode(['status'=>"ok",'info'=>'操作成功!']);
        }else{
            echo json_encode(['status'=>"error",'info'=>'操作失败!']);
        }
    }

    /**
     *@快递公司
     */
    public function kuaidi(){
        $map = [];
//        isset($_GET['status']) && $map['status'] = I('status');
        !empty($_GET['name']) && $map['name'] = array('like','%'.I('name').'%');
        $list = M('ExpressCompany')->where($map)->select();
        $this->assign(['list'=>$list]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     * @添加快递公司
     */
    public function add_kuaidi(){
        if(IS_POST){
            echo json_encode(D('ExpressCompany')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑快递公司
     */
    public function edit_kuaidi(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('ExpressCompany')->auth());
        }else{
            $re = M('ExpressCompany')->where(['ec_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Config/add_kuaidi');
        }
    }

    /**
     *删除快递公司
     */
    public function del_kuaidi(){
        if(IS_POST){
            $id = I('ids');
            $map['ec_id'] = array('in',$id);
            $result = M('ExpressCompany')->where($map)->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
            }
        }
    }

    /**
     *启动页广告
     */
    public function advert(){
        $list = M('Banner')->where(['type'=>'6'])->select();
        $this->assign(['list'=>$list]);
        $this->display();
    }

    /**
     *编辑启动广告页
     */
    public function edit_advert(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            $check = M('Banner')->where(['banner_id'=>$data['banner_id']])->find();
            if(!$check){
                echo json_encode(['status'=>'error','info'=>'没找到相关的数据']);
                die;
            }
            if(empty($data['img'])){
                echo json_encode(['status'=>'error','info'=>'广告页图片不能为空']);
                die;
            }
            $data['create_time'] = time();
            $data['uptime'] = time();
            $data['type'] = '6';
            $result = M('Banner')->where(['banner_id'=>$data['banner_id']])->save($data);
            if($result){
                echo json_encode(['status'=>'ok','info'=>'编辑记录成功','url'=>U('Config/advert')]);
            }else{
                echo json_encode(['status'=>'ok','info'=>'编辑记录失败']);
            }
        }else{
            $re = M('Banner')->where(['banner_id'=>$id])->find();
            $this->assign(['banner'=>$re]);
            $this->display();
        }
    }

    /**
     *@城市三字码管理
     */
    public function airport_list(){
        $map=[];
        !empty($_GET['name']) && $map['city|country'] = ['like','%'.I('name').'%'];
        !empty($_GET['code']) && $map['code|jichang'] = ['like','%'.I('code').'%'];
        !empty($_GET['type']) && $map['type'] = I('type');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Area')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Area")->where($map)->order("intime desc")->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $act = I("get.act");
        if($act == 'download'){
            $dat = M("Area")->where($map)->select();
            $str = '机场三字码统计表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,机场名称,机场三字码,城市名称,城市类型,城市所属国家或地区,城市首字母\n";
            foreach($dat as $key=>$val){
                switch($val['type']){
                    case 1 :
                        $val['type'] = '国内城市';
                        break;
                    case 2 :
                        $val['type'] = '国际城市';
                        break;
                }
                echo $key.","
                    .$val["jichang"]."\t,"
                    .$val["code"]."\t,"
                    .$val["city"]."\t,"
                    .$val["type"]."\t,"
                    .$val["country"]."\t,"
                    .$val["shouzimu"]."\t,"
                    ."\n";
            }
        }else{
            $url = $_SERVER['REQUEST_URI'];
            session('url',$url);
            $this->display();
        }
    }

    /**
     *@添加机场三字码数据
     */
    public function add_airport(){
        if(IS_POST){
            echo json_encode(D('Area')->check());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑机场三字码数据
     */
    public function edit_airport(){
        if(IS_POST){
            echo json_encode(D('Area')->check());
        }else{
            $id = I('id');
            $re = M('Area')->where(['id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Config/add_airport');
        }
    }

    /**
     *删除机场三字码
     */
    public function del_airport(){
        if(IS_POST){
            $id = I('ids');
            $map['id'] = array('in',$id);
            $result = M('Area')->where($map)->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
            }
        }
    }

    /**
     *短信设置
     */
    public function sms(){
        $data = M("Sms")->select();
        $count = M("Sms")->count();
        $this->assign(['list'=>$data,'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *编辑短信
     */
    public function set_up_sms(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            $result = M('Sms')->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑短信成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑短信失败'));
            }
        }else{
           $re = M('Sms')->where(['id'=>$id])->find();
           $this->assign(['re'=>$re]);
           $this->display();
        }
    }

    /**
     * 用户反馈信息
     */
    public function feedback_list(){
        $map=[];
        !empty($_GET['nickname']) && $map['c.nickname|c.phone'] = ['like','%'.I('nickname').'%'];
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Feedback')->alias('a')
            ->join("LEFT JOIN __USER__ b on a.member_id = b.user_id")
            ->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M('Feedback')->alias('a')->field('a.feedback_id,a.feedback,a.intime,b.username,b.phone')
            ->join("LEFT JOIN __USER__ b on a.member_id = b.user_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show(),'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *反馈详情
     */
    public function feedback_view(){
        $id = I('id');
        $feedback = M('Feedback')->where(['feedback_id'=>$id])->find();
        echo json_encode(array('status'=>'ok','info'=>$feedback));
    }

    /**
     *删除反馈信息
     */
    public function del_feedback(){
        if(IS_POST){
            $id = I('ids');
            $map['feedback_id'] = array('in',$id);
            $result = M('Feedback')->where($map)->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
            }
        }
    }

    /**
     *@景点地址
     */
    public function spot_address(){
        $map=[];
        !empty($_GET['name']) && $map['name'] = ['like','%'.I('name').'%'];
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('SpotAddress')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("SpotAddress")->where($map)->limit($p->firstRow,$p->listRows)->order("is_open desc,intime desc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加景点地址
     */
    public function add_spot(){
        if(IS_POST){
            echo json_encode(D('SpotAddress')->check());
        }else{
            $sheng = M('Areas')->where("level=1")->select();
            $this->assign(['sheng'=>$sheng]);
            $this->display();
        }
    }

    /**
     *@编辑景点地址
     */
    public function edit_spot(){
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $id =I('id');
        if(IS_POST){
            echo json_encode(D('SpotAddress')->check());
        }else {
            $hotel = M('SpotAddress')->where(array('id' => $id))->find();
            $fid = M('Areas')->where(array('name' => $hotel['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $hotel['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $hotel['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $hotel['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $hotel['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $hotel['qu'] = null;
            }
            $hotel['city_id'] = M('Areas')->where(array('name' => $hotel['city'], 'level' => 2))->getField('id');
            $hotel['area_id'] = M('Areas')->where(array('name' => $hotel['area'], 'level' => 3))->getField('id');
            $this->assign(['sheng'=>$sheng,'m'=>$hotel]);
            $this->display('Config/add_spot');
        }
    }

    /**
     *@删除景点
     */
    public function del_spot(){
        if(IS_POST){
            $id = I('ids');
            $map['id'] = array('in',$id);
            $result = M('SpotAddress')->where($map)->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
            }
        }
    }

    /**
     * @改变景点状态
     */
    public function change_spot_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('SpotAddress')->where(['id'=>$id])->getField('is_open');
            $abs = 3 - $status;
            $arr = ['关闭状态','开启状态'];
            $result = M('SpotAddress')->where(['id'=>$id])->save(['is_open'=>$abs]);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$arr[2-$status]));
                exit;
            }else{
                echo json_encode(array('status'=>'error','info'=>'切换状态失败'));
                exit;
            }
        }
    }

    /**
     *@景点取货点
     */
    public function spot_pick(){
        $map=[];
        $map['a.spot_id'] = I('spot_id');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('SpotPick')->alias('a')
            ->join("LEFT JOIN __SPOT_ADDRESS__ b on a.spot_id = b.id")
            ->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("SpotPick")->alias('a')
            ->field('a.pick_id,a.name,a.address,b.name as spot_name,a.intime')
            ->join("LEFT JOIN __SPOT_ADDRESS__ b on a.spot_id = b.id")
            ->where($map)->limit($p->firstRow,$p->listRows)
            ->order("a.intime asc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@ 添加取货点
     */
    public function edit_pick(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status'=>'error','info'=>'名称不能为空','class'=>''));
                die;
            }
            if(empty($data['address'])){
                echo json_encode(array('status'=>'error','info'=>'详细地址不能为空','class'=>''));
                die;
            }
            if(empty($id)){
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('SpotPick')->add($data);
                $action = '新增';
            }else{
                $result = M('SpotPick')->where(['pick_id'=>$id])->save($data);
                $action = '编辑';
            }
            if($result !==false){
                echo json_encode(array('status'=>'ok','info'=>$action.'取货点成功','url'=>session('url')));
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'取货点失败'));
                die;
            }
        }else{
            $re = M('SpotPick')->where(['pick_id'=>$id])->find();
            $this->assign(['m'=>$re]);
            $this->display();
        }
    }

    /**
     *@删除景点
     */
    public function del_pick(){
        if(IS_POST){
            $id = I('ids');
            $map['pick_id'] = array('in',$id);
            $result = M('SpotPick')->where($map)->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
            }
        }
    }

    /**
     *@银行卡设置
     */
    public function bank_card(){
        $map=[];
        !empty($_GET['name']) && $map['name'] = ['like','%'.I('name').'%'];
        $map['is_del'] = 1;
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Bank')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Bank")->where($map)->limit($p->firstRow,$p->listRows)->order("intime desc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加银行信息
     */
    public function add_bank(){
        if(IS_POST){
            echo json_encode(D('Bank')->check());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑银行卡
     */
    public function edit_bank(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Bank')->check());
        }else{
            $re = M('Bank')->where(['bank_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Config/add_bank');
        }
    }

    /**
     *@删除银行卡
     */
    public function del_bank(){
        $id = I('ids');
        $map['bank_id'] = array('in',$id);
        $result = M('Bank')->where($map)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
        }
    }

    /**
     * @礼物列表
     */
    public function gift_list(){
        $p=I("get.p");
        if (empty($num)){
            $num = 10;
        }
        !empty($_GET['name']) && $map['name'] = ['like','%'.I('name').'%'];
        $this->assign('nus',$num);
        $count =M("Gift")->where($map)->count(); // 查询满足要求的总记录数
        $p = $this->getpage($count, $num);
        $data=M("Gift")->limit($p->firstRow, $p->listRows)
            ->where($map)->order('intime desc')->select();
        $url =$_SERVER['REQUEST_URI'];
        $this->assign(['list' => $data, 'page' => $p->show(),'count'=>$count]);
        session('url',$url);
        $this->display();
    }

    /**
     * @添加礼物
     */
    public function add_gift(){
        if(IS_POST){
            echo json_encode(D('Gift')->auth());
        }else{
            $this->display();
        }

    }
    /**
     * @添加(修改礼物)
     */
    public function edit_gift(){
        $gift_id = I('gift_id');
        if(IS_POST){
            echo json_encode(D('Gift')->auth());
        }else{
            $re = M('Gift')->where(['gift_id'=>$gift_id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Config/add_gift');
        }
    }

    /**
     * @删除礼物
     */
    public function del_gift(){
        $id = I('ids');
        $result = M('Gift')->where(['gift_id'=>['in',$id]])->delete();
        if($result){
            echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
        }
    }

    /**
     * @充值列表
     */
    public function price_list(){
        $page=I("get.p");
        $data=M("Price_list")->page($page)->order('intime asc')->select();
        $this->assign('list',$data);
        $count = M("Price_list")->count(); // 查询满足要求的总记录数
        $this->page3($count,10);
        $url =$_SERVER['REQUEST_URI'];
        $this->assign(['count'=>$count]);
        session('url',$url);
        $this->display();
    }

    /**
     * @添加充值
     */
    public function add_price_list(){
        if(IS_POST){
            echo json_encode(D('PriceList')->auth());
        }else{
            $this->display();
        }
    }
    /**
     * @添加(修改充值)
     */
    public function edit_price_list(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('PriceList')->auth());
        }else{
            $re = M('PriceList')->where(['price_list_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Config/add_price_list');
        }
    }

    /**
     * @删除充值
     */
    public function del_price_list(){
        $id = I('ids');
        $result = M('Price_list')->where(['price_list_id'=>['in',$id]])->delete();
        if($result){
            echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
        }
    }

    /**
     *@敏感词
     */
    public function sensitive_word(){
        if(IS_POST){
            $word = I('word');
            $result = M('System')->where(['id'=>1])->save(['sensitive_word'=>$word]);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑记录成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑记录失败'));
            }
        }else{
            $word = M('System')->where(['id'=>1])->getField('sensitive_word');
            $this->assign(['word'=>$word]);
            $this->display();
        }
    }

    public function clean_cache(){
        function rmdirr($dirname) {

            if (!file_exists($dirname)) {
                return false;
            }

            if (is_file($dirname) || is_link($dirname)) {
                return unlink($dirname);
            }

            $dir = dir($dirname);

            while (false !== $entry = $dir->read()) {

                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
            }

            $dir->close();

            return rmdir($dirname);
        }
        if(isset($_GET['portal'])){
            $dirs = array('./data/portal');
        }
        if(isset($_GET['all'])){
            $dirs	=	array('./_runtime');
        }else{
//缓存目录
            $dirs	=	array('./application/Runtime');
        }
//清理缓存
        foreach($dirs as $value)
        {
            rmdirr($value);

            //echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'><!--\"".$value."\"--> 清除成功! </div> <br /><br />";

            @mkdir($value,0777,true);

        }

        echo json_encode(array('status'=>'ok'));
        die;

    }


}