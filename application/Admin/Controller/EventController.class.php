<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/22
 * Time: 16:37
 */

namespace Admin\Controller;
class EventController extends BaseController
{
    public function index(){
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
     *@场地出租
     */
    public function place_lease(){
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('PlaceLease')->where(['place_id'=>'1'])->count();
        $p = $this->getpage($count,$num);
        $data = M("PlaceLease")->limit($p->firstRow,$p->listRows)->where(['place_id'=>'1'])->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@场地出租
     */
    public function book(){
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('PlaceLease')->where(['place_id'=>'2'])->count();
        $p = $this->getpage($count,$num);
        $data = M("PlaceLease")->limit($p->firstRow,$p->listRows)->where(['place_id'=>'2'])->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑场地
     */
    public function edit_place(){
        $id = I('id');
        $sheng = M('Areas')->where("level=1")->select();
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status' => 'error', 'info' => '名称不能为空','class'=>''));
                die;
            }
//            foreach($data['imgs'] as $k=>$v){
//                if(!empty($v)){
//                    $imgs[] = $v;
//                }
//            }
            if(empty($data['imgs'])){
                echo json_encode(array('status' => 'error', 'info' => '图片不能为空','class'=>''));
                die;
            }
            if(empty($data['phone'])){
                echo json_encode(array('status' => 'error', 'info' => '联系电话不能为空','class'=>''));
                die;
            }
            if(empty($data['price'])){
                echo json_encode(array('status' => 'error', 'info' => '预约价格不能为空','class'=>''));
                die;
            }
//            $data['imgs'] = implode(',',$data['imgs']);
            $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
            $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
            $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
            if(empty($data['province']))    return array('status'=>'error','info'=>'请填写省份','class'=>'');
            if(empty($data['city']))    return array('status'=>'error','info'=>'请填写城市','class'=>'');
            if(empty($data['area']))    return array('status'=>'error','info'=>'请填写地区','class'=>'');
            if(empty($data['address'])){
                echo json_encode(array('status' => 'error', 'info' => '详细地址不能为空','class'=>''));
                die;
            }
            if(empty($data['content'])){
                echo json_encode(array('status' => 'error', 'info' => '预约规定不能为空','class'=>''));
                die;
            }
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $ak = 'fbINeDE9oaw2SXIYcfdpe0Td';
            $address = $data['province'].$data['city'].$data['area'].$data['address'];
            $api = "http://api.map.baidu.com/geocoder/v2/?ak={$ak}&output=json&address={$address}";
            $position = file_get_contents($api);
            $position = json_decode($position, true);
            $array = $position['result']['location'];
            $data['lng'] = "{$array['lng']}";//经度
            $data['lat'] = "{$array['lat']}";//纬度
            $result = M('PlaceLease')->where(['place_id'=>$data['id']])->save($data);
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'编辑成功','url'=>session('url')]);
                die;
            }else{
                echo json_encode(['status'=>"error",'info'=>'编辑失败']);
                die;
            }
        }else{
            $re = M('PlaceLease')->where(['place_id'=>$id])->find();
//            $re['imgs'] = explode(',',$re['imgs']);
            $fid = M('Areas')->where(array('name' => $re['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $re['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $re['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $re['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $re['qu'] = null;
            }
            $re['city_id'] = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            $re['area_id'] = M('Areas')->where(array('name' => $re['area'], 'level' => 3))->getField('id');
            $this->assign(['sheng'=>$sheng,'m'=>$re]);
            $this->display();
        }
    }

    /**
     *@套餐列表
     */
    public function lease_list(){
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('PlaceLeaseList')->where(['is_del'=>'1'])->count();
        $p = $this->getpage($count,$num);
        $data = M("PlaceLeaseList")->limit($p->firstRow,$p->listRows)->where(['is_del'=>'1'])->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    private function check_lease(){
        $data = $_POST;
        if(empty($data['place_id'])){
            echo json_encode(array('status' => 'error', 'info' => '参数错误','class'=>''));
            die;
        }
        if(empty($data['name'])){
            echo json_encode(array('status' => 'error', 'info' => '名称不能为空','class'=>''));
            die;
        }
        if(empty($data['img'])){
            echo json_encode(array('status' => 'error', 'info' => '图片不能为空','class'=>''));
            die;
        }
        if(empty($data['price'])){
            echo json_encode(array('status' => 'error', 'info' => '价格不能为空','class'=>''));
            die;
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('PlaceLeaseList')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('PlaceLeaseList')->where(['lease_id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'套餐内容成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'套餐内容失败');
        }
    }

    /**
     *@添加套餐
     */
    public function add_lease(){
        if(IS_POST){
            echo json_encode($this->check_lease());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑套餐
     */
    public function edit_lease(){
        $id = I('id');
        if(IS_POST){
            echo json_encode($this->check_lease());
        }else{
            $m = M('PlaceLeaseList')->where(['lease_id'=>$id])->find();
            $this->assign(['m'=>$m]);
            $this->display('Event/add_lease');
        }
    }

    /**
     *@删除出租列表
     */
    public function del_lease(){
        $id = I('ids');
        $data['lease_id'] = array('in',$id);
        $result = M('PlaceLeaseList')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@赛事活动
     */
    public function game_list(){
        if (empty($num)){
            $num = 10;
        }
        $map = [];
        !empty($_GET['name'])  &&  $map['name'] = ['like','%'.$_GET['name'].'%'];
        !empty($_GET['status'])  &&  $map['status'] = $_GET['status'];
        $map['is_del'] = '1';
        $this->assign('nus',$num);
        $count = M('Game')->where($map)->count();
        $p = $this->getpage($count,$num);
//        $list = M('Game')->where(['status'=>'2','is_del'=>'1'])->select();
//        foreach($list as $k=>$v){
//            if(strtotime($v['end_time'])-time()<0){
//                M('Game')->where(['game_id'=>$v['game_id']])->save(['status'=>'1']);
//            }
//        }
        $data = M("Game")->where($map)->limit($p->firstRow,$p->listRows)->order("status desc,intime desc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加赛事
     */
    public function add_game(){
        if(IS_POST){
            echo json_encode(D('Game')->check());
        }else{
            $sheng = M('Areas')->where("level=1")->select();
            $this->assign(['sheng'=>$sheng]);
            $this->display();
        }
    }

    /**
     *@编辑赛事
     */
    public function edit_game(){
        $id = I('id');
        $sheng = M('Areas')->where("level=1")->select();
        if(IS_POST){
            echo json_encode(D('Game')->check());
        }else{
            $re = M('Game')->where(['game_id'=>$id])->find();
            $fid = M('Areas')->where(array('name' => $re['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $re['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $re['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $re['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $re['qu'] = null;
            }
            $re['city_id'] = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            $re['area_id'] = M('Areas')->where(array('name' => $re['area'], 'level' => 3))->getField('id');
            $this->assign(['sheng'=>$sheng,'m'=>$re]);
            $this->display('Event/add_game');
        }
    }

    /**
     *@删除赛事列表
     */
    public function del_game(){
        $id = I('ids');
        $data['game_id'] = array('in',$id);
        $result = M('Game')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@改变赛事的上架信息
     */
    public function change_game_status(){
        if(IS_POST){
            $id = I('id');
            $game = M('Game')->where(['game_id'=>$id])->find();
            $status = $game['status'];
//            if($status == '1'){
//                if(strtotime($game['end_time'])-time()<0){
//                    echo json_encode(array('status'=>'error','info'=>'赛事已经结束不能上架'));
//                    exit;
//                }
//            }
            $abs = 3 - $status;
            $arr = ['下架中','已上架'];
            $result = M('Game')->where(['game_id'=>$id])->save(['status'=>$abs]);
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
     * @门票列表
     */
    public function price_list(){
        $map   =   array();
        $map['game_id'] = I('uid');
        $map['is_del']  = '1';
        $list = M('GamePrice')->where($map)->select();
        $this->assign(['list'=>$list]);
        $this->display();
    }

    /**
     *@修改赛事门票状态
     */
    public function change_price_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('GamePrice')->where(['price_id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['下架中','已上架'];
            if($status == '1')      $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['status'] = $abs;
            $result = M('GamePrice')->where(['price_id'=>$id])->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$arr[2-$status]));
                exit;
            }else{
                echo json_encode(array('status'=>'error','info'=>'切换状态失败'));
                exit;
            }
        }
    }

    private function check_game_price(){
        $data = $_POST;
        if(empty($data['game_id'])){
            echo json_encode(array('status' => 'error', 'info' => '参数错误','class'=>''));
            die;
        }
        if(empty($data['name'])){
            echo json_encode(array('status' => 'error', 'info' => '名称不能为空','class'=>''));
            die;
        }
        if(empty($data['price'])){
            echo json_encode(array('status' => 'error', 'info' => '价格不能为空','class'=>''));
            die;
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('GamePrice')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('GamePrice')->where(['price_id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>"ok",'info'=>$action.'记录成功!','url'=>session('url'));
        }else{
            return array('status'=>"error",'info'=>$action.'记录失败!');
        }
    }

    /**
     *@添加门票
     */
    public function add_price(){
        if(IS_POST){
            echo json_encode($this->check_game_price());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑门票
     */
    public function edit_price(){
        $id = I('id');
        if(IS_POST){
            echo json_encode($this->check_game_price());
        }else{
            $m = M('GamePrice')->where(['price_id'=>$id])->find();
            $this->assign(['m'=>$m]);
            $this->display('Event/add_price');
        }
    }

    /**
     *@删除赛事价格列表
     */
    public function del_game_price(){
        $id = I('ids');
        $data['price_id'] = array('in',$id);
        $result = M('GamePrice')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }


    /**
     *@编辑场地
     */
    public function edit_book(){
        $id = I('id');
        $sheng = M('Areas')->where("level=1")->select();
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status' => 'error', 'info' => '名称不能为空','class'=>''));
                die;
            }
//            foreach($data['imgs'] as $k=>$v){
//                if(!empty($v)){
//                    $imgs[] = $v;
//                }
//            }
            if(empty($data['imgs'])){
                echo json_encode(array('status' => 'error', 'info' => '图片不能为空','class'=>''));
                die;
            }
            if(empty($data['phone'])){
                echo json_encode(array('status' => 'error', 'info' => '联系电话不能为空','class'=>''));
                die;
            }
//            $data['imgs'] = implode(',',$data['imgs']);
            $data['province'] = M('Areas')->where(array('id' => I('sheng')))->getField('name');
            $data['city'] = M('Areas')->where(array('id' => I('shi')))->getField('name');
            $data['area'] = M('Areas')->where(array('id' => I('qu')))->getField('name');
            if(empty($data['province']))    return array('status'=>'error','info'=>'请填写省份','class'=>'');
            if(empty($data['city']))    return array('status'=>'error','info'=>'请填写城市','class'=>'');
            if(empty($data['area']))    return array('status'=>'error','info'=>'请填写地区','class'=>'');
            if(empty($data['address'])){
                echo json_encode(array('status' => 'error', 'info' => '详细地址不能为空','class'=>''));
                die;
            }
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $ak = 'fbINeDE9oaw2SXIYcfdpe0Td';
            $address = $data['province'].$data['city'].$data['area'].$data['address'];
            $api = "http://api.map.baidu.com/geocoder/v2/?ak={$ak}&output=json&address={$address}";
            $position = file_get_contents($api);
            $position = json_decode($position, true);
            $array = $position['result']['location'];
            $data['lng'] = "{$array['lng']}";//经度
            $data['lat'] = "{$array['lat']}";//纬度
            $result = M('PlaceLease')->where(['place_id'=>$data['id']])->save($data);
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'编辑成功','url'=>session('url')]);
                die;
            }else{
                echo json_encode(['status'=>"error",'info'=>'编辑失败']);
                die;
            }
        }else{
            $re = M('PlaceLease')->where(['place_id'=>$id])->find();
//            $re['imgs'] = explode(',',$re['imgs']);
            $fid = M('Areas')->where(array('name' => $re['province'], 'level' => 1))->getField('id');
            if ($fid) {
                $data['fid'] = $fid;
                $data['level'] = 2;
                $re['shi'] = M('Areas')->where($data)->select();  //市
            } else {
                $re['shi'] = null;
            }
            $fid2 = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            if ($fid2) {
                $date['fid'] = $fid2;
                $date['level'] = 3;
                $re['qu'] = M('Areas')->where($date)->select();  //区
            } else {
                $re['qu'] = null;
            }
            $re['city_id'] = M('Areas')->where(array('name' => $re['city'], 'level' => 2))->getField('id');
            $re['area_id'] = M('Areas')->where(array('name' => $re['area'], 'level' => 3))->getField('id');
            $this->assign(['sheng'=>$sheng,'m'=>$re]);
            $this->display();
        }
    }
}