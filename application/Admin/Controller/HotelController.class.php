<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/13
 * Time: 10:14
 */

namespace Admin\Controller;
class HotelController extends BaseController
{
    /**
     *酒店列表
     */
    public function index(){
        $map=[];
        !empty($_GET['name']) && $map['name|tags'] = ['like','%'.I('name').'%'];
        !empty($_GET['star']) && $map['star|city'] = ['like','%'.I('star').'%'];
        !empty($_GET['city']) && $map['tag_city'] = ['like','%'.I('city').'%'];
        !empty($_GET['status']) && $map['status'] = I('status');
        $map['is_del'] = '1';
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Hotel')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Hotel")->where($map)->order("status desc,is_tuijian desc,intime asc")->limit($p->firstRow,$p->listRows)->select();
        $act = I("get.act");
        if($act=="download"){
            $dat = M("Hotel")->where($map)->select();
            $str = '酒店表格'.date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF"."序号,酒店名称,酒店星级,标签,酒店地址,最低价格,酒店简介,创建时间\n";
            foreach ($dat as $k=>$v){
                echo $k.","
                    .$v["name"]."\t,"
                    .$v["star"]."\t,"
                    .$v["tags"]."\t,"
                    .$v["province"].$v["city"].$v["area"].$v["address"]."\t,"
                    .$v["min_price"]."\t,"
                    .$v["dis"]."\t,"
                    .$v["intime"]."\t,"
                    ."\n";
            }
        }else {
            $city = M('City')->where(['city_id'=>'0'])->select();
            $this->assign(['list' => $data, 'page' => $p->show(),'city'=>$city]);
            $url = $_SERVER['REQUEST_URI'];
            session('url', $url);
            $this->display();
        }
    }

    /**
     *添加酒店
     */
    public function add_hotel(){
        check_auth();
        if(IS_POST){
            echo json_encode(D('Hotel')->check());
        }else{
            $tags = M('HotelTags')->select();
            $service = M('HotelService')->select();
            $city = M('City')->where(['city_id'=>'0'])->select();
            $sheng = M('Areas')->where("level=1")->select();
            $this->assign(['tags'=>$tags,'city'=>$city,'sheng'=>$sheng,'service'=>$service]);
            $this->display();
        }
    }

    /**
     *编辑酒店
     */
    public function edit_hotel(){
        //省
        $sheng = M('Areas')->where("level=1")->select();
        $id =I('id');
        if(!empty($id)) {
            $hotel = M('Hotel')->where(array('hotel_id' => $id))->find();
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
            $hotel['imgs'] = explode(',',$hotel['imgs']);
            $hotel['service'] = explode(',',$hotel['service']);
            $hotel['city_id'] = M('Areas')->where(array('name' => $hotel['city'], 'level' => 2))->getField('id');
            $hotel['area_id'] = M('Areas')->where(array('name' => $hotel['area'], 'level' => 3))->getField('id');
            $this->assign('m', $hotel);
        }
        if(IS_POST){
            echo json_encode(D('Hotel')->check());
        }else {
            $tags = M('HotelTags')->select();
            $service = M('HotelService')->select();
            $city = M('City')->where(['city_id'=>'0'])->select();
            $sheng = M('Areas')->where("level=1")->select();
            $this->assign(['tags'=>$tags,'city'=>$city,'sheng'=>$sheng,'service'=>$service]);
            $this->display('Hotel/add_hotel');
        }
    }

    /**
     *根据城市找商圈
     */
    public function link_centre(){
        $name = I('city');
        $city_id = M('City')->where(['city'=>$name])->getField('id');
        $centre = M('City')->where(['city_id'=>$city_id])->select();
        if(!empty($centre)){
            foreach($centre as $k=>$v){
                $option.="<option value=".$centre[$k]['centre'].">".$centre[$k]['centre']."</option>";
            }
            $option.= "<option value='其他'>其他</option>";
        }else{
            $option= "<option value=''>暂无商圈记录</option>";
        }
        echo $option;
    }

    /**
     *城市和商圈
     */
    public function city(){
        $map=[];
        !empty($_GET['city']) && $map['city'] = ['like','%'.I('title').'%'];
        $num  = I('num');
        $map['city_id'] = '0';
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('City')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("City")->where($map)->limit($p->firstRow,$p->listRows)->select();
        foreach($data as $key=>$val){
            $data[$key]['centre'] = M('City')->where(['city_id'=>$val['id']])->select();
        }
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加城市
     */
    public function edit_city(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['city'])){
                echo json_encode(array('status'=>'error','info'=>'城市名称不能为空'));
                die;
            }
            if(empty($data['img'])){
                echo json_encode(array('status'=>'error','info'=>'城市图片不能为空'));
                die;
            }
            if(empty($data['id'])){
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('City')->add($data);
                $action = '添加';
            }else{
                $data['uptime'] = date("Y-m-d H:i:s",time());
                $result = M('City')->save($data);
                $action = '编辑';

            }
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$action.'城市记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'城市记录成功'));
            }

        }
    }

    /**
     *@添加和编辑城市商圈
     */
    public function edit_city_centre(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['centre'])){
                echo json_encode(array('status'=>'error','info'=>'商圈名称不能为空'));
                die;
            }
            if(empty($data['id'])){
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('City')->add($data);
                $action = '添加';
                if($result){
                    echo json_encode(array('status'=>'ok','info'=>$action.'城市商圈成功','url'=>session('url')));
                }else{
                    echo json_encode(array('status'=>'error','info'=>$action.'城市商圈失败'));
                }
            }else{
                $data['uptime'] = date("Y-m-d H:i:s",time());
                $result = M('City')->where(['id'=>$data['id']])->save($data);
                $action = '编辑';
                if($result){
                    echo json_encode(array('status'=>'ok','info'=>$action.'城市商圈成功'));
                }else{
                    echo json_encode(array('status'=>'error','info'=>$action.'城市商圈失败'));
                }
            }
        }
    }

    /**
     *删除城市
     */
    public function del_city(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $map['city_id'] = array('in',$id);
        $result = M('City')->where($data)->delete();
        M('City')->where($map)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *删除商圈
     */
    public function del_centre(){
        $data['id'] = I('id');
        $result = M('City')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *酒店评论
     */

    /**
     *酒店标签
     */
    public function tags(){
        $data = M('HotelTags')->select();
        $this->assign(['list'=>$data]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加酒店标签
     */
    public function add_tags(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status'=>'error','info'=>'标签名称不能为空'));
                die;
            }
            if(empty($data['color'])){
                echo json_encode(array('status'=>'error','info'=>'标签背景色不能为空'));
                die;
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('HotelTags')->add($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'添加酒店标签成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'添加酒店标签失败'));
            }
        }else{
            $this->display();
        }
    }

    /**
     *编辑酒店标签
     */
    public function edit_tags(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            if(empty($data['name'])){
                echo json_encode(array('status'=>'error','info'=>'标签名称不能为空'));
                die;
            }
            if(empty($data['color'])){
                echo json_encode(array('status'=>'error','info'=>'标签背景色不能为空'));
                die;
            }
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('HotelTags')->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑酒店标签成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑酒店标签失败'));
            }
        }else{
            $re = M('HotelTags')->where(['id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Hotel/add_tags');
        }
    }

    /**
     *删除标签
     */
    public function del_tags(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('HotelTags')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *删除酒店
     */
    public function del_hotel(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('Hotel')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *改变酒店状态
     */
    public function change_hotel_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('Hotel')->where(['hotel_id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['下架状态','上架状态'];
            $result = M('Hotel')->where(['hotel_id'=>$id])->save(['status'=>$abs]);
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
     *改变酒店推荐
     */
    public function change_hotel_tuijian(){
        if(IS_POST){
            $id = I('id');
            $hotel = M('Hotel')->field('is_tuijian,status')->where(['hotel_id'=>$id])->find();
            if($hotel['status'] == '1'){
                echo json_encode(array('status'=>'error','info'=>'酒店属于下架状态，不能设为推荐'));
                exit;
            }
            $status = $hotel['is_tuijian'];
            $abs = 3 - $status;
            $arr = ['默认状态','推荐状态'];
            $result = M('Hotel')->where(['hotel_id'=>$id])->save(['is_tuijian'=>$abs]);
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
     *酒店房间列表
     */
    public function room_list(){
        $map['is_del'] = '1';
        $map['hotel_id'] = I('uid');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('HotelRoom')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("HotelRoom")->where($map)->order("intime asc")->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加酒店房间
     */
    public function add_hotel_room(){
        check_auth();
        if(IS_POST){
            echo json_encode(D('HotelRoom')->check());
        }else{
            $this->display();
        }
    }

    /**
     *编辑酒店房间
     */
    public function edit_hotel_room(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('HotelRoom')->check());
        }else{
            $m = M('HotelRoom')->where(['id'=>$id])->find();
            $this->assign(['m'=>$m]);
            $this->display('Hotel/add_hotel_room');
        }
    }

    /**
     *删除酒店放房间
     */
    public function del_hotel_room(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('HotelRoom')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *酒店服务
     */
    public function service(){
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('HotelService')->count();
        $p = $this->getpage($count,$num);
        $data = M("HotelService")->order("intime asc")->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加酒店服务
     */
    public function add_service(){
        if(IS_POST){
            echo json_encode(D('HotelService')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *编辑酒店服务
     */
    public function edit_service(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('HotelService')->auth());
        }else{
            $re = M('HotelService')->where(['id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Hotel/add_service');
        }
    }
    /**
     *删除酒店服务
     */
    public function del_service(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('HotelService')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *酒店评论
     */
    public function review(){
        $map=[];
        !empty($_GET['hotel_name']) && $map['b.name|b.english_name'] = ['like','%'.I('hotel_name').'%'];
        !empty($_GET['nickname']) && $map['c.nickname|c.phone'] = ['like','%'.I('nickname').'%'];
        $map['a.type'] = '1';
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Comment')->alias('a')
                ->join("LEFT JOIN __HOTEL__ b on a.hotel_id = b.hotel_id")
                ->join("LEFT JOIN __MEMBER__ c on a.member_id = c.member_id")
                ->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M('Comment')->alias('a')->field('a.comment_id,a.content,a.mark,a.intime,b.name,b.city,b.area,b.address,c.nickname,c.phone')
            ->join("LEFT JOIN __HOTEL__ b on a.hotel_id = b.hotel_id")
            ->join("LEFT JOIN __MEMBER__ c on a.member_id = c.member_id")
            ->where($map)->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *评论详情
     */
    public function reply(){
        $id = I('id');
        $comment = M('Comment')->where(['comment_id'=>$id])->find();
        echo json_encode(array('status'=>'ok','info'=>$comment));
    }

    /**
     *@ 删除回复以及回复信息
     */
    public function del_comment(){
        $id = I('ids');
        $result = M('Comment')->where(['comment_id'=>$id])->delete();
        if($result){
            M('Comment')->where(['fid'=>$id])->delete();
            echo json_encode(array('status'=>'ok','info'=>'删除记录成功','url'=>session('url')));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除记录失败'));
        }
    }

}