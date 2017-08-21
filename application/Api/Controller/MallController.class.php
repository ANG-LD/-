<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/1
 * Time: 15:21
 */

namespace Api\Controller;
class MallController extends CommonController
{
    /**
     *@商城首页
     *
     */
    public function index(){
        $banner = M('Banner')->field("b_id,b_img,url,b_type,title,value")
            ->where(['is_del'=>'1','status'=>'2','type'=>4])->order("sort asc")->select();
        foreach($banner as $k=>$v){
            $banner[$k]['b_img'] = $this->url.$v['b_img'];
            switch($v['b_type']){
                case 1:
                    $banner[$k]['jump'] = '';
                    break;
                case 2:
                    $banner[$k]['jump'] = $this->url.'/api.php/Home/banner_url/id/'.$v['b_id'];
                    break;
                case 3:
                    $banner[$k]['jump'] = $v['value'];
                    break;
                case 4:
                    $banner[$k]['jump'] = $v['value'];
                    break;
            }
        }
        $category = M('Category')->field('id,category,picture')  //分类
            ->where(['cate_id'=>'0','type'=>1])->select();
        foreach($category as $k=>$v){
            $category[$k]['picture'] = $this->url.$v['picture'];
        }
        $goods = M('Goods')->field('goods_id,thumb,img,name,sale_price')
                ->where(['is_del'=>1,'status'=>2,'is_tuijian'=>2])->select();
        foreach($goods as $k=>$v){
            $goods[$k]['thumb'] = $this->url.$v['thumb'];
            $goods[$k]['img'] = $this->url.$v['img'];
        }
        success(['banner'=>$banner,'category'=>$category,'goods'=>$goods]);

    }

    /**
     * @商城下级分类
     * @param id
     */
    public function second_category(){
        $id = I('id');
        if(empty($id))          error("参数错误");
        $category = M('Category')->field('id,category,picture,banner_img')  //分类
        ->where(['cate_id'=>'0','type'=>1])->select();
        foreach($category as $k=>$v){
            $category[$k]['picture'] = $this->url.$v['picture'];
            $category[$k]['banner_img'] = $this->url.$v['banner_img'];
        }
        $second_category = M('Category')->field('id,category,picture')  //分类
        ->where(['cate_id'=>$id,'type'=>1])->select();
        foreach($second_category as $k=>$v){
            $second_category[$k]['picture'] = $this->url.$v['picture'];
        }
        success(['category'=>$category,'second'=>$second_category]);
    }

    /**
     *@商品列表
     *@param category_id商品分级分类id
     */
    public function goods_list(){
        $id = I('id');
        if(empty($id))          error("参数错误");
        $map['second_category'] = $id;
        $map['is_del'] = 1;
        $map['status'] = 2;
        $p = I('p');
        $pageSize = I('pagesize');
        $p  ?   $p  :   $p = 1;
        $pageSize   ?   $pageSize   :   $pageSize = 10;
        $count = M('Goods')->where($map)->count();
        $goods = M('Goods')->field('goods_id,name,thumb,img,sale_price')
            ->where($map)->order("sale_number desc")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->select();
        $page = ceil($count/$pageSize);
        if($goods){
            foreach($goods as $k=>$v){
                $goods[$k]['thumb'] = $this->url.$v['thumb'];
                $goods[$k]['img'] = $this->url.$v['img'];
            }
        }else{
            $goods = [];
        }
        success(['page'=>$page,'goods'=>$goods]);
    }

    /**
     *@搜索商品
     *@param name商品名称
     */
    public function search_goods(){
        $name = I('name');
        if(empty($name))          error("搜索关键为空");
        $map['name'] = ['like','%'.$name.'%'];
        $map['is_del'] = 1;
        $map['status'] = 2;
        $p = I('p');
        $pageSize = I('pagesize');
        $p  ?   $p  :   $p = 1;
        $pageSize   ?   $pageSize   :   $pageSize = 10;
        $count = M('Goods')->where($map)->count();
        $goods = M('Goods')->field('goods_id,name,thumb,img,sale_price')
            ->where($map)->order("sale_number desc")
            ->limit(($p-1)*$pageSize,$pageSize)
            ->select();
        $page = ceil($count/$pageSize);
        if($goods){
            foreach($goods as $k=>$v){
                $goods[$k]['thumb'] = $this->url.$v['thumb'];
                $goods[$k]['img'] = $this->url.$v['img'];
            }
        }else{
            $goods = [];
        }
        success(['page'=>$page,'goods'=>$goods]);
    }


    /**
     *@商品基础信息
     */
    public function goods_info(){
        if (IS_POST) {
            $goods_id = I('goods_id');
            if (empty($goods_id)) error("商品id不能为空");
            $uid = I('uid');
            /*商品基础信息*/
            $goods = M('Goods')
                ->field('goods_id,name,intro,brand,imgs,number,sale_number,price,sale_price,kinds,
                image_text,param')
                ->where(['goods_id' => $goods_id])->find();
            if (empty($goods)) error("商品不存在");
            $imgs = explode(',', $goods['imgs']);
            foreach ($imgs as $k => $v) {
                if(!empty($v)){
                    $img[]['img'] = $this->url.$v;
                }
            }
            $goods['imgs'] = $img;
            $kind = explode(',',$goods['kinds']);
            if(!empty($kind)){
               foreach($kind as $k=>$v){
                   $kinds1 = M('GoodsKinds')->where(['goods_id'=>$goods_id,'type'=>$k+1,'is_del'=>'1'])->select();
                   $goods['kinds_detail'][$k]['kind'] = $v;
                   $goods['kinds_detail'][$k]['kind_detail'] = $kinds1;
               }
            }else{
                $goods['kinds_detail'] = [];
            }

            /*商品和物流评分*/
/*            $where['type']    = 1;
            $where['object_id'] = $goods_id;
            $count = M('Comment')->where($where)->count();
            if(empty($count)){
                $goods['goods_mark']    = 5;
                $goods['express_mark']    = 5;
                $goods['send_mark']    = 5;
            }else{
                $goods_mark = M('Comment')->where($where)->sum('goods_mark');
                $express_mark = M('Comment')->where($where)->sum('express_mark');
                $send_mark = M('Comment')->where($where)->sum('send_mark');
                $goods['goods_mark']    = (int)(($goods_mark + 5)/($count+1));
                $goods['express_mark']  = (int)(($express_mark + 5)/($count+1));
                $goods['send_mark']     = (int)(($send_mark + 5)/($count+1));
            }
            $goods['together_mark'] = sprintf("%.1f",($goods_mark+$express_mark+$send_mark+15)/(($count+1)*3));*/
            $goods['is_collect'] = 2;
            /*检测是否收藏*/
            if(!empty($uid)){
                $map['type'] = 1;
                $map['user_id'] = $uid;
                $map['goods_id']    = $goods_id;
                $check = M('Collection')->where($map)->find();
                if($check){
                    $goods['is_collect']  = 1;
                }

            }
            success($goods);
        }
    }

    /**
     *@商品图文详情
     */
    public function goods_text_image()
    {
        $id = I('goods_id');
        $text_image = M('Goods')->where(['goods_id' => $id])->getField('image_text');
        $this->assign(['text' => $text_image]);
        $this->display();
    }

    /**
     * @商品参数图文
     */

    public function goods_param()
    {
        $id = I('goods_id');
        $text_image = M('Goods')->where(['goods_id' => $id])->getField('param');
        $this->assign(['text' => $text_image]);
        $this->display();
    }

    /**
     *@ 商品评论
     */
    public function goods_comment(){
        if(IS_POST){
            $goods_id = I('goods_id');
            $p        = I('p');
            empty($p)   &&  $p = 1;
            $num = I('pagesize');
            $num    ?   $num    :   $num = 10;
            $map      = [];
            $map['a.type'] = 1;
            $map['a.object_id'] =   $goods_id;
            $count = M('Comment')->alias('a')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->where($map)
                ->count();
            $page = ceil($count/$num);
            $list = M('Comment')->alias('a')
                ->field('a.comment_id,a.content,a.intime,b.user_id,b.username,b.img,a.img as comment_img,a.thumb as comment_thumb,a.goods_mark')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->where($map)->limit(($p-1)*$num , $num)
                ->order("a.intime desc")->select();
            foreach($list as $k=>$v){
                $a = array();
                if(!empty($v['comment_img'])){
                    $img = explode(',',$v['comment_img']);
                    foreach($img as $key=>$val){
                        $a[$key]['img'] = $this->url.$val;
                    }

                }
                $list[$k]['comment_img'] = $a;
                $b = array();
                if(!empty($v['comment_thumb'])){
                    $thumb = explode(',',$v['comment_thumb']);
                    foreach($thumb as $ke=>$va){
                        $b[$ke]['img'] = $this->url.$va;
                    }
                }
                $list[$k]['comment_thumb'] = $b;
                $list[$k]['date_value'] = translate_date($v['intime']);
                $list[$k]['img'] = $this->url.$v['img'];
                $list[$k]['goods_mark'] = (int)($v['goods_mark']);
            }
            success(['list'=>$list,'page'=>$page]);
        }
    }

    /**
     *@收藏和取消收藏商品
     */
    public function collect_goods(){
        if(IS_POST){
            $member = checklogin();
            $id = I('goods_id');
            $check = M('Collection')->where(['goods_id'=>$id,'type'=>'1','user_id'=>$member['user_id']])->find();
            if($check){
                $result = M('Collection')->where(['collection_id'=>$check['collection_id']])->delete();
                if($result){
                    success(2);
                }else{
                    error("取消失败");
                }
            }else{
                $data['user_id'] = $member['user_id'];
                $data['type'] = 1;
                $data['goods_id'] = $id;
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('Collection')->add($data);
                if($result){
                    success(1);
                }else{
                    error("收藏失败");
                }
            }
        }
    }

    /**
     *@收藏列表
     */
    public function collect(){
            $member = checklogin();
            $map['b.is_del'] = '1';
            $map['b.status'] = '2';
            $map['a.type'] = '1';
            $map['a.user_id'] = $member['user_id'];
            $p = I('p');
            $count = M('Collection')->alias('a')
                ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where($map)->count();
            empty($p)   &&  $p = 1;
            $num = I('pagesize');
            $num    ?   $num    :   $num = 10;
            $page = ceil($count/$num);
            $list = M('Collection')->alias('a')
                ->field("a.collection_id,a.goods_id,b.name,b.price,b.sale_price,b.thumb,b.img,b.sale_number,b.number")
                ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where($map)->limit(($p-1)*$num,$num)
                ->select();
            foreach($list as $k=>$v){
                $list[$k]['thumb'] = $this->url.$v['thumb'];
                $list[$k]['img'] = $this->url.$v['img'];
            }
            success(['page'=>$page,'list'=>$list]);
    }

    /**
     *@删除收藏
     */
    public function del_collect(){
        if(IS_POST){
            $member = checklogin();
            $ids = I('ids');
            $map['collection_id'] = ['in',$ids];
            $result = M('Collection')->where($map)->delete();
            if($result){
                success("操作成功");
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@景点地址
     */
    public function spot_address(){
        if(IS_POST){
            $member = checklogin();
            $list = M('SpotAddress')->field('id,name,address,lat,lng,province,city,area')
                ->where(['is_open'=>'2'])->select();
            success($list);
        }
    }

    /**
     *@景点地址详情
     */
    public function spot_pick(){
        $member = checklogin();
        $spot_id = I('spot_id');
        if(empty($spot_id))                         error("参数错误");
        $spot = M('SpotAddress')->field(['id,img,name,address'])
            ->where(['id'=>$spot_id])->find();
        if(!empty($spot)){
            $spot['list'] = M('SpotPick')->field('pick_id,name,address')
                ->where(['spot_id'=>$spot['id']])->select();
        }
        success($spot);
    }

    /**
     *@邮费设置
     *@param  string 地址
     */
    private function set_pastage($address){
        $string1 = '上海市、浙江省、江苏省';
        $string2 = '西藏自治区、青海省、新疆维吾尔自治区、宁夏回族自治区、内蒙古自治区';
        $postage = 10;
        if (strpos($string1, $address)!== false)       $postage = 5;
        if (strpos($string2, $address) !== false)      $postage = 15;
        return $postage;
    }

    /**
     *@添加收获地址
     */
    public function add_receive_address()
    {
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            if (empty($data['name'])) error("联系人不能为空");
            if (empty($data['phone'])) error("联系人手机号不能为空");
            if (!(preg_match("/^1[34578]{1}\d{9}$/", $data['phone']))) {
                error("联系人手机号错误");
            }
            if (empty($data['province'])) error("省份不能为空");
            if (empty($data['city'])) error("城市不能为空");
//            if (empty($data['area'])) error("区域不能为空");
            if (empty($data['address'])) error("详细地址不能为空");
            $data['postage'] = $this->set_pastage($data['province']);
            $data['mid'] = $member['user_id'];
            $data['intime'] = date("Y-m-d H:i:s", time());
            $check = M('AcceptorAddress')->where(['mid'=>$member['user_id']])->select();
            empty($check)       && $data['is_default'] = 2;
            $result = M('AcceptorAddress')->add($data);
            if ($result) {
                success("添加成功");
            } else {
                error("添加失败");
            }
        }
    }

    /**
     *@收货地址列表
     */
    public function receive_address()
    {
        if (IS_POST) {
            $member = checklogin();
            $list = M('AcceptorAddress')->field("id,name,phone,province,city,area,street,address,is_default")
                ->where(['mid'=>$member['user_id']])->order("is_default desc")->select();
            success($list);
        }
    }

    /**
     *@编辑收货地址
     */
    public function edit_receive_address()
    {
        $id = I('id');
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            if (empty($id)) error("参数错误");
            if (empty($data['name'])) error("联系人不能为空");
            if (empty($data['phone'])) error("联系人手机号不能为空");
            if (!(preg_match("/^1[34578]{1}\d{9}$/", $data['phone']))) {
                error("联系人手机号错误");
            }
            if (empty($data['province'])) error("省份不能为空");
            if (empty($data['city'])) error("城市不能为空");
//            if (empty($data['area'])) error("区域不能为空");
            if (empty($data['address'])) error("详细地址不能为空");
            $data['postage'] = $this->set_pastage($data['province']);
            $data['uptime'] = date("Y-m-d H:i:s", time());
            $result = M('AcceptorAddress')->where(['id' => $id])->save($data);
            if ($result) {
                success("编辑成功");
            } else {
                error("编辑失败");
            }
        } else {
            if (empty($id)) error("参数错误");
            $address = M('AcceptorAddress')->field("id,name,phone,province,city,area,street,address,is_default")
                ->where(['id' => $id])->find();
            success($address);
        }
    }

    /**
     * @设置默认收货地址
     */
    public function set_default_address()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('AcceptorAddress')->where(['id' => $id])->find();
            if (empty($check)) error("数据没找到");
            if ($check['is_default'] == '2') error("该地址已经是默认地址");
            M('AcceptorAddress')->where(['mid'=>$member['user_id']])->save(['is_default' => '1']);
            $result = M('AcceptorAddress')->where(['id' => $id])->save(['is_default' => '2']);
            if ($result) {
                success("操作成功");
            } else {
                success("操作失败");
            }
        }
    }

    /**
     *@判断是否有默认的收货地址
     */
    public function check_default_address(){
        if(IS_POST){
            $member = checklogin();
            $check = M('AcceptorAddress')->where(['mid' => $member['member_id'],'is_default'=>'2'])->find();
            if ($check) {
                success(1);
            } else {
                success(2);
            }
        }
    }

    /**
     *@删除收货地址
     */
    public function del_address()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数不能为空");
            $check = M('AcceptorAddress')->where(['id' => $id,'mid'=>$member['user_id']])->find();
            if (empty($check)) error("参数错误");
            $result = M('AcceptorAddress')->where(['id' => $id])->delete();
            if ($result) {
                success("删除成功");
            } else {
                error("删除失败");
            }
        }
    }


    /**
     *@支付协议
     */
    public function xieyi(){
        $id = I('id');
        $map['id'] = $id;
        $map['type'] =
            1;
        $xieyi = M('notice')->where($map)->find();
        success($xieyi);
    }

    /**
     *判断型号库存
     */
    public function check_stock(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['goods_id']))         error("参数错误");
            if(empty($data['kinds_id']))       error("参数错误");
            $goods = M('Goods')->where(['goods_id'=>$data['goods_id']])->find();
            if(!$goods)                     error("参数错误");
            $kinds = explode(',',$data['kinds_id']);
            if(count($kinds) < 2){
                $data['kinds_id'] = $data['kinds_id'].',';
            }
            $check = M('GoodsStock')->where(['kinds'=>$data['kinds_id'],'goods_id'=>$data['goods_id']])->find();
            if($check['number']>0){
                success(['stock'=>$check['number']]);
            }else{
                success(['stock'=>'0']);
            }

        }
    }

    /**
     *@加入购物车
     */
    public function to_goods_cart(){
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            if (empty($data['goods_id'])) error("购买商品不能为空");
            $goods = M('Goods')->where(['goods_id'=>$data['goods_id'],'is_del'=>'1','status'=>'2'])->find();
            if(!$goods)                   error("商品已下架");
            if (empty($data['number'])) $data['number'] = '1';
            if (!ctype_digit($data['number'])) error("请填写正确的商品数量");
            if(!empty($data['kinds_id'])){
                $map['kinds_id'] = $data['kinds_id'];
            }
            $data['mid'] = $member['user_id'];
            $data['goods_id'] = $data['goods_id'];
            $data['intime'] = date("Y-m-d H:i:s", time());
            $map['goods_id'] = $data['goods_id'];
            $map['mid'] = $member['user_id'];
            $check = M('GoodCart')->where($map)->find();
            if ($check) {
                $result = M('GoodCart')->where($map)->save(['number' => $check['number'] + $data['number']]);
            } else {
                $result = M('GoodCart')->add($data);
            }
            if ($result) {
                success("添加成功");
            } else {
                error("添加失败");
            }
        }
    }

    /**
     *@购物车中的商品
     */
    public function goods_cart()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['a.mid'] = $member['user_id'];
            $map['a.type'] = 1;
            $map['b.is_del'] = 1;
            $map['b.status'] = 2;
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id,a.number,a.is_check,a.kinds_id,
                b.name,b.thumb,b.img,b.price,b.sale_price,b.number as stock,b.kinds")
                ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where($map)->order("a.intime asc")->select();
            $price = '0';
            $is_all_check = 2;
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (!empty($v['kinds_id'])) {
                        $kinds_id = explode(',',$v['kinds_id']);
                        $kinds = explode(',',$v['kinds']);
                        foreach($kinds_id as $key=>$val){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                            $v['sale_price'] += $kinds1['sale_price'];
                            $v['price'] += $kinds1['price'];
                            $list[$k]['kinds_detail'][$key]['kind'] = $kinds[$key];
                            $list[$k]['kinds_detail'][$key]['kind_detail'] = $kinds1['kinds_detail'];
                        }

                    }else{
                        $list[$k]['kinds_detail'] = [];
                    }

                    if ($v['is_check'] == '2') {
                        $price += $v['sale_price'] * $v['number'];
                    }else{
                        $is_all_check = 1;
                    }
                    $list[$k]['thumb'] = $this->url.$v['thumb'];
                    $list[$k]['sale_price'] = sprintf("%.2f",$v['sale_price']);
                    $list[$k]['price'] = sprintf("%.2f",$v['price']);
                    $list[$k]['img'] = $this->url.$v['img'];
                }
                if($price){
                    $price = sprintf("%.2f",$price);
                }
            }
            success(['price' => $price, 'list' => $list,'is_all_check'=>$is_all_check]);
        }
    }

    /**
     *@商品数量增加1
     */
    public function plus_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('GoodCart')->where(['id' => $id])->find();
            if (!$check) error("参数错误");
            $result = M('GoodCart')->where(['id' => $id])->setInc("number");
            if (!$result) error("操作失败");
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id,a.number,a.is_check,b.name,b.img,b.kinds,b.price,b.sale_price,b.goods_id,a.kinds_id")
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.mid' => $member['user_id']])->order("a.intime asc")->select();
            $price = '0';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (!empty($v['kinds_id'])) {
                        $kinds_id = explode(',',$v['kinds_id']);
                        foreach($kinds_id as $key=>$val){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                            $v['sale_price'] += $kinds1['sale_price'];
                        }
                    }
                    if ($v['is_check'] == '2') {
                        $price += $v['sale_price'] * $v['number'];
                    }

                }
                if($price){
                    $price = sprintf("%.2f",$price);
                }
                success(['price' => $price]);
            } else {
                success("购物车中没有数据");
            }
        }
    }

    /**
     *@商品数量减1
     */
    public function minus_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('GoodCart')->where(['id' => $id])->find();
            if (!$check) error("参数错误");
            if ($check['number'] < 2) error("操作失败");
            $result = M('GoodCart')->where(['id' => $id])->setDec("number");
            if (!$result) error("操作失败");
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id,a.number,a.is_check,b.name,b.img,b.kinds,b.price,b.sale_price,b.goods_id,a.kinds_id")
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.mid' => $member['user_id']])->order("a.intime asc")->select();
            $price = '0';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (!empty($v['kinds_id'])) {
                        $kinds_id = explode(',',$v['kinds_id']);
                        foreach($kinds_id as $key=>$val){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                            $v['sale_price'] += $kinds1['sale_price'];
                        }
                    }
                    if ($v['is_check'] == '2') {
                        $price += $v['sale_price'] * $v['number'];
                    }

                }
                if($price){
                    $price = sprintf("%.2f",$price);
                }
                success(['price' => $price]);
            } else {
                success("购物车中没有数据");
            }
        }
    }

    /**
     *@购物车中商品默认操作
     */
    public function set_default_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $id = I('id');
            if (empty($id)) error("参数错误");
            $check = M('GoodCart')->where(['id' => $id])->find();
            if (!$check) error("参数错误");
            if ($check['is_check'] == 1) {
                M('GoodCart')->where(['id' => $id])->save(['is_check' => 2]);
            } else {
                M('GoodCart')->where(['id' => $id])->save(['is_check' => 1]);
            }
            $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id,a.number,a.is_check,b.name,b.img,b.kinds,b.price,b.sale_price,b.goods_id,a.kinds_id")
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.mid' => $member['user_id']])->order("a.intime asc")->select();
            $price = '0';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (!empty($v['kinds_id'])) {
                        $kinds_id = explode(',',$v['kinds_id']);
                        foreach($kinds_id as $key=>$val){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                            $v['sale_price'] += $kinds1['sale_price'];
                        }
                    }
                    if ($v['is_check'] == '2') {
                        $price += $v['sale_price'] * $v['number'];
                    }

                }
                if($price){
                    $price = sprintf("%.2f",$price);
                }
                success(['price' => $price]);
            } else {
                success("购物车中没有数据");
            }
        }
    }

    /**
     *@购物车中全选 or 全不选
     */
    public function choose_all_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $is_check = I('is_check');
            if (empty($is_check)) error("参数错误");
            $result = M('GoodCart')->where(['mid' => $member['user_id']])->save(['is_check' => $is_check]);
            if ($result) {
                $list = M('GoodCart')->alias('a')
                    ->field("a.id,a.goods_id,a.number,a.is_check,b.name,b.img,b.kinds,b.price,b.sale_price,b.goods_id,a.kinds_id")
                    ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->where(['a.mid' => $member['user_id']])->order("a.intime asc")->select();
                $price = '0';
                if (!empty($list)) {
                    foreach ($list as $k => $v) {
                        if (!empty($v['kinds_id'])) {
                            $kinds_id = explode(',',$v['kinds_id']);
                            foreach($kinds_id as $key=>$val){
                                $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                                $v['sale_price'] += $kinds1['sale_price'];
                            }
                        }
                        if ($v['is_check'] == '2') {
                            $price += $v['sale_price'] * $v['number'];
                        }

                    }
                    if($price){
                        $price = sprintf("%.2f",$price);
                    }
                    success(['price' => $price]);
                } else {
                    error("操作失败");
                }
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@购物车中商品删除
     */
    public function del_goods_cart(){
        if(IS_POST){
            $member = checklogin();
            $check = M('GoodCart')->where(['is_check'=>'2','mid'=>$member['user_id']])->count();
            if(empty($check)){
                error("请先选择商品");
            }
            $result = M('GoodCart')->where(['is_check'=>'2','mid'=>$member['user_id']])->delete();
            if($result){
                success("删除成功");
            }else{
                error("删除失败");
            }
        }
    }

    /**
     *@单件商品直接写入订单
     */
    public function set_goods_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $goods_id = I('goods_id');
            if (empty($goods_id)) error("商品不能为空");
            $goods = M('Goods')->where(['goods_id' => $goods_id])->find();
            if (empty($goods)) error("商品错误");
            $code['number'] = I('number');
            $code['goods_id'] = $goods_id;
            if (empty($code['number']))     $code['number'] = 1;
            $kinds_id = I('kinds_id');
            $code['mid'] = $member['user_id'];
            $code['intime'] = date("Y-m-d H:i:s",time());
            $map['mid'] = $member['user_id'];
            $map['goods_id'] = $goods_id;
            $map['kinds_id'] = $kinds_id;
            $code['kinds_id'] = $kinds_id;
            $check = M('GoodCart')->where($map)->find();
            $result = M('GoodCart')->add($code);
            if($result){
                M('GoodCart')->where(['mid'=>$member['user_id']])->save(['is_check'=>'1']);
                M('GoodCart')->where(['id'=>$result])->save(['is_check'=>'2']);
                if($check){
                    M('GoodCart')->where(['id'=>$check['id']])->delete();
                }
                success("提交成功");
            }else{
                error("提交失败");
            }
        }
    }

    /**
     *@确认订单
     */
    public function confirm_info()
    {
        if (IS_POST) {
            $member = checklogin();
            $list = $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id,a.number,a.is_check,b.name,b.img,b.thumb,b.kinds,b.price,b.sale_price,b.goods_id,a.kinds_id,b.has_postage")
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.mid' => $member['user_id'], 'a.type' => '1', 'a.is_check' => '2'])->order("a.intime asc")->select();
            if (empty($list)) error("商品不能为空");
            $amount = '0';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (!empty($v['kinds_id'])) {
                        $kinds_id = explode(',',$v['kinds_id']);
                        $kinds = explode(',',$v['kinds']);
                        foreach($kinds_id as $key=>$val){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                            $v['sale_price'] += $kinds1['sale_price'];
                            $v['price'] += $kinds1['price'];
                            $list[$k]['kinds_detail'][$key]['kind'] = $kinds[$key];
                            $list[$k]['kinds_detail'][$key]['kind_detail'] = $kinds1['kinds_detail'];
                        }

                    }else{
                        $list[$k]['kinds_detail'] = [];
                    }

                    $list[$k]['thumb'] = $this->url.$v['thumb'];
                    $list[$k]['sale_price'] = sprintf("%.2f",$v['sale_price']);;
                    $list[$k]['price'] = sprintf("%.2f",$v['price']);
                    $amount += $v['sale_price'] * $v['number'];
                    $list[$k]['img'] = $this->url.$v['img'];
                    $postage[] = $v['has_postage'];
                }
            }
            if(in_array('1',$postage)){
                $has_postage = '1';
            }else{
                $has_postage = '2';
            }
            $amount = sprintf("%.2f",$amount);
                $aid = I('address_id');
                if (empty($aid)) {
                    $address = M('AcceptorAddress')->where(['mid' => $member['user_id'], 'is_default' => '2'])->find();
                } else {
                    $address = M("AcceptorAddress")->where(['id' => $aid])->find();
                }
                $arr = array();
                if (!empty($address)) {
                    empty($address['name']) ? $arr['name'] = $member['username'] : $arr['name'] = $address['name'];
                    empty($address['phone']) ? $arr['phone'] = $member['phone'] : $arr['phone'] = $address['phone'];
                    if($has_postage == 1){
                        $arr['postage'] = '0';
                    }else{
                        empty($address['postage']) ? $arr['postage'] = '0' : $arr['postage'] = $address['postage'];
                        if($address['postage']){
                            $arr['postage'] = $address['postage'];
                        }else{
                            $arr['postage'] = '0';
                            $has_postage = '1';
                        }
                    }
                    $arr['address'] = $address['province'] . $address['city'] . $address['area'] . $address['street'] . $address['address'];
                }else{
                    $arr = (object)null;
                }
//                $install_score = M('InstallScore')->where(['id' => 1])->find();
//                if ($member['score'] < $install_score['score']) {
//                    $score = [];
//                } else {
//                    $int = (int)(($member['score']  / $install_score['score']));
//                    if ($int * $install_score['money'] < $amount * 0.2) {
//                        $score['member_score'] = $member['score'];
//                        $score['score'] = $int * $install_score['score'];
//                        $score['money'] = $int * $install_score['money'];
//                    } else {
//                        $int = ceil(($amount * 0.2) / $install_score['money']);
//                        $score['member_score'] = $member['score'];
//                        $score['score'] = $int * $install_score['score'];
//                        $score['money'] = $int * $install_score['money'];
//                    }
//                }
                success(['address' => $arr, 'goods' => $list, 'amount' => $amount,'has_postage'=>$has_postage]);
            }
    }

    /**
     *@写入订单
     */
    public function set_confirm_order(){
        if(IS_POST){
            $member = checklogin();
            $data   = $_POST;
//            if($data['is_agree'] != 1)           error("协议尚未同意");
//            if(empty($data['pattern']))     error("商品数量不能为空");
            if(empty($data['name']))        error("收件人不能为空");
            if(empty($data['phone']))       error("联系方式不能为空");
            if(empty($data['address']))     error("详细地址不能为空");
            $list = $list = M('GoodCart')->alias('a')
                ->field("a.id,a.goods_id,a.number,a.is_check,b.name,b.img,b.thumb,b.kinds,b.price,b.sale_price,b.goods_id,a.kinds_id")
                ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                ->where(['a.mid' => $member['user_id'], 'a.type' => '1', 'a.is_check' => '2'])->order("a.intime asc")->select();
            if (empty($list)) error("商品不能为空");
            $amount = '0';
            if (!empty($list)) {
                foreach ($list as $k => $v) {
                    if (!empty($v['kinds_id'])) {
                        $kinds_id = explode(',',$v['kinds_id']);
                        foreach($kinds_id as $key=>$val){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val])->find();
                            $v['sale_price'] += $kinds1['sale_price'];
                        }

                    }
                    $amount += $v['sale_price'] * $v['number'];
                }
            }
            $amount = sprintf("%.2f",$amount);
            $data['amount'] = $amount;
            if(!empty($data['score'])){
                if($data['score']>$member['score'] + $member['amount'])   error("积分不够，不能使用积分"); //查询积分
                $install_score = M('InstallScore')->where(['id'=>1])->find();
                if($member['score'] + $member['amount'] < $install_score['score'])    error("积分不够，不能使用积分"); //查询积分
                $ratio = $install_score['money']/$install_score['score'];
            }
            $coupon_amount = 0;
            if(!empty($data['coupon'])){
                $coupon = std_class_object_to_array(json_decode($data['coupon']));
                foreach($coupon as $k=>$v){
                    $check = M('Coupon')->where(['id'=>$v['id']])->find();
                    $coupon_amount += $check['value'] * $v['num'];
                }
            };

            $data['deduction'] = $data['score'] * $ratio;
            $data['paid'] = $data['amount'] - $data['deduction'] - $coupon_amount + $data['postage'];
            $data['coupon_id'] = json_encode($coupon);
            $data['coupon_amount'] = $coupon_amount;
            $data['order_no'] = date("YmdHis").rand(100000,999999);
            $data['intime'] = date("Y-m-d H:i:s");
            $data['mid'] = $member['user_id'];
            $data['type'] = '1';
            $data['state'] = '1';
            $mall_order  = M('MallOrder');
            $mall_order -> startTrans();
            $result = $mall_order -> add($data);
            if($result){
//                if(!empty($data['score'])){
//                    if($member['score']<$data['score']){    //判断获取的积分是否足够
//                        $tag['score'] = 0;
//                        $tag['amount'] = $member['amount'] + $member['score'] -$data['score'];
//                    }else{
//                        $tag['score'] = $member['score'] - $data['score']; //用户现有积分
//                    }
//                    $re = M('Member')->where(['member_id' => $member['member_id']])->save($tag);
//                    if(!$re){
//                        $mall_order -> rollback();
//                    }
//                }
//                if($coupon){
//                    $map['member_id'] = $member['member_id'];
//                    $map['status'] = 1;
//                    $field['uptime'] = date("Y-m-d H:i:s",time());
//                    $field['status'] = 2;
//                    foreach($coupon as $k=>$v){
//                        $map['coupon_id'] = $v['id'];
//                        $re = M('MemberCoupon')->where($map)->limit($v['num'])->save($field);
//                        if(!$re){
//                            $mall_order -> rollback();
//                        }
//                    }
//                }
                $code['order_id'] = $result;
                $code['intime'] = date("Y-m-d H:i:s", time());
                foreach($list as $k=>$v){
                    $code['goods_id'] = $v['goods_id'];
                    $code['kinds_id'] = $v['kinds_id'];
                    $code['number'] = $v['number'];
                    $re = M('MallOrderDetail')->add($code);
                    if(!$re){
                        $mall_order -> rollback();
                    }
                }
                $result = M('GoodCart')->where(['mid'=>$member['user_id'],'is_check'=>'2'])->delete();
                if(!$result){
                    $mall_order -> rollback();
                }else{
                    $mall_order -> commit();
                }
            }else{
                $mall_order -> rollback();
            }
            success(['order_no' => $data['order_no']]);
        }
    }


    /**
     *@所有商品订单
     */
    public function mall_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['mid'] = $member['user_id'];
            $map['is_del'] = '1';
            $map['state'] = ['in', ['1', '2', '3', '4','5']];
            $state = I('state');
//            if($state != 5){
//                !empty($state) && $map['state'] = I('state');
//            }else{
//                $map['state'] = ['in',['5','7']];,
//            }
            $state == 1      &&      $map['state'] = 1;
            $state == 2      &&      $map['state'] = ['in',['2','3']];
            $state == 3      &&      $map['state'] = 4;
            $map['address'] = ['neq', ''];
            $p = I('p');
            empty($p) && $p = 1;
            $pageSize = I('pagesize');
            $pageSize ?  $pageSize  :   $pageSize = 5;
            $count = M('MallOrder')
                ->where($map)->count();
            $page = ceil($count / $pageSize);
            $list = M('MallOrder')->field('id,order_no,paid,phone,deduction,score,state,type,has_postage,postage,kuaidi,kuaidi_name,kuaidi_node,kuaidi_state')
                ->where($map)->limit(($p - 1) * $pageSize, $pageSize)
                ->order("intime desc")->select();
            foreach ($list as $k => $v) {
                switch($v['type']){
                    case 1:
                        $order_detail = M("MallOrderDetail")->alias('a')
                            ->field('a.id,a.order_id,a.number,a.goods_id,a.kinds_id,b.name,b.sale_price,b.price,b.img,b.thumb,b.kinds')
                            ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                            ->where(['a.order_id' => $v['id']])->select();
                        break;
                }
                $number = 0;
                foreach ($order_detail as $key => $val) {
                    if (!empty($val['kinds_id'])) {
                        $kinds_id = explode(',',$val['kinds_id']);
                        $kinds = explode(',',$val['kinds']);
                        foreach($kinds_id as $key1=>$val1){
                            $kinds1 = M('GoodsKinds')->where(['kind_id' => $val1])->find();
                            $val['sale_price'] += $kinds1['sale_price'];
                            $val['price'] += $kinds1['price'];
                            $order_detail[$key]['kinds_detail'][$key1]['kind'] = $kinds[$key1];
                            $order_detail[$key]['kinds_detail'][$key1]['kind_detail'] = $kinds1['kinds_detail'];
                        }
                        $order_detail[$key]['sale_price'] = sprintf("%.2f",$val['sale_price']);
                        $order_detail[$key]['price'] = sprintf("%.2f",$val['price']);

                    }else{
                        $order_detail[$key]['kinds_detail'] = [];
                    }
                    $number += $val['number'];
                    $order_detail[$key]['thumb'] = $this->url.$val['thumb'];
                    $order_detail[$key]['img'] = $this->url.$val['img'];
                }
                if($map['state'] == 5){
                    foreach($order_detail as $key=>$val){
                        $check = M('MallReturnsOrder')->where(['order_id'=>$val['order_id'],'goods_id'=>$val['goods_id'],'is_del'=>'1'])->find();
                        if(!empty($check)){
                            if(empty($check['kuaidi'])){
                                if(time()-strtotime($check['intime'])>7*24*3600){
                                    $order_detail[$key]['returns_status'] = 1;
                                }else{
                                    $order_detail[$key]['returns_status'] = 2;
                                }
                            }else{
                                $order_detail[$key]['returns_status'] = 3;
                            }
                        }else{
                            $order_detail[$key]['returns_status'] = 1;
                        }
                        $order_detail[$key]['returns_amount'] = $check['amount'];
                    }
                }
                $list[$k]['order_detail'] = $order_detail;
                $list[$k]['item'] = $number;
            }
            success(['list' => $list, 'page' => $page]);

        }
    }

    /**
     *@订单详情
     */
    public function order_detail()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_node,
            kuaidi_state,postage,has_postage,type,deduction,score,send_bill,bill_name')
                ->where($map)->find();
            switch($order['type']){
                case 1:
                    $order_detail = M("MallOrderDetail")->alias('a')
                        ->field('a.id,a.order_id,a.number,a.goods_id,a.kinds_id,b.name,b.sale_price,b.price,b.img,b.thumb,b.kinds')
                        ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->where(['a.order_id' => $order['id']])->select();
                    break;
            }
            foreach($order_detail as $key=>$val){
                if (!empty($val['kinds_id'])) {
                    $kinds_id = explode(',',$val['kinds_id']);
                    $kinds = explode(',',$val['kinds']);
                    foreach($kinds_id as $key1=>$val1){
                        $kinds1 = M('GoodsKinds')->where(['kind_id' => $val1])->find();
                        $val['sale_price'] += $kinds1['sale_price'];
                        $val['price'] += $kinds1['price'];
                        $order_detail[$key]['kinds_detail'][$key1]['kind'] = $kinds[$key1];
                        $order_detail[$key]['kinds_detail'][$key1]['kind_detail'] = $kinds1['kinds_detail'];
                    }
                    $order_detail[$key]['sale_price'] = sprintf("%.2f",$val['sale_price']);
                    $order_detail[$key]['price'] = sprintf("%.2f",$val['price']);

                }else{
                    $order_detail[$key]['kinds_detail'] = [];
                }
                $order_detail[$key]['thumb'] = $this->url.$val['thumb'];
                $order_detail[$key]['img'] = $this->url.$val['img'];
            }
            if($order['state'] == '5'){
                foreach($order_detail as $k=>$v){
                    $check = M('MallReturnsOrder')->where(['order_id'=>$v['order_id'],'goods_id'=>$v['goods_id'],'is_del'=>'1'])->find();
                    if(!empty($check)){
                        if(empty($check['kuaidi'])){
                            if(time()-strtotime($check['intime'])>7*24*3600){
                                $order_detail[$k]['returns_status'] = 1;
                            }else{
                                $order_detail[$k]['returns_status'] = 2;
                            }
                        }else{
                            $order_detail[$k]['returns_status'] = 3;
                        }
                    }else{
                        $order_detail[$k]['returns_status'] = 1;
                    }
                }
            }
            $order['order_detail'] = $order_detail;
        }

        success($order);
    }

    /**
     * @取消订单
     */
    public function cancel_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state,score')
                ->where($map)->find();
            $order['state'] != '1' ? error("该状态下无法操作") : true;
            $result = M('MallOrder')->where(['id' => $order['id']])->save(['state' => '6']);
            if ($result) {
                if (!empty($order['score'])) {
                    M('User')->where(['user_id' => $member['user_id']])->setInc("score", $order['score']);
                }
                success("操作成功");
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@删除订单
     */
    public function del_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state')
                ->where($map)->find();
            if(!$order)                             error("订单错误");
            !in_array($order['state'],['4','5'])  ? error("该状态下无法操作") : true;
            $result = M('MallOrder')->where(['id' => $order['id']])->save(['is_del' => '2']);
            if ($result) {
                success("操作成功");
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@确认收货
     */
    public function receive_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state,type')
                ->where($map)->find();
            $order['state'] != '3' ? error("该状态下无法操作") : true;
            $result = M('MallOrder')->where(['id' => $order['id']])->save(['state' => '4', 'kuaidi_state' => '4']);
            if ($result) {
                if(in_array($order['type'],['1','2'])){
                    $order_detail = M('MallOrderDetail')->where(['order_id'=>$order['id']])->select();
                    foreach($order_detail as $k=>$v){
                        M('Goods')->where(['goods_id'=>$v['goods_id']])->setInc('sale_number',$v['number']);
                        if(!empty($v['kinds_id'])){
                            if(strpos(',',$v['kinds_id']) == false){
                                $kinds = $v['kinds_id'].',';
                            }else{
                                $kinds =  $v['kinds_id'];
                            }
                            M('GoodsStock')->where(['goods_id'=>$v['goods_id'],'kinds'=>$kinds])->setInc('sale_number',$v['number']);
                            M('GoodsStock')->where(['goods_id'=>$v['goods_id'],'kinds'=>$kinds])->setDec('number',$v['number']);
                        }
                    }

                }
                success("操作成功");
            } else {
                error("操作失败");
            }
        }
    }

    /**
     *@催单
     */
    public function hurry_order()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            $type = I('type');
            empty($type)    &&  $type = 1;
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,state,remark,kuaidi,kuaidi_name,kuaidi_state')
                ->where($map)->find();
            $order['state'] != '2' ? error("该状态下无法操作") : true;
            $check = M('MallOrderHurry')
                ->where(['mid' => $member['user_id'], 'order_id' => $order['id'],'type'=>$type])
                ->order("intime desc")->limit(1)->find();
            if (time() - strtotime($check['intime']) < 3600) {
                error("催单过于频繁");
            } else {
                $data['mid'] = $member['user_id'];
                $data['order_id'] = $order['id'];
                $data['intime'] = date("Y-m-d H:i:s", time());
                $data['type'] = $type;
                $result = M('MallOrderHurry')->add($data);
                if ($result) {
                    success("催单成功");
                } else {
                    error("催单失败");
                }
            }
        }
    }

    /**
     *@商品评价的商品显示
     */
    public function comment_goods_view()
    {
        if (IS_POST) {
            $member = checklogin();
            $map['order_no'] = I('order_no');
            empty($map['order_no']) ? error("参数错误") : true;
            $order = M('MallOrder')->field('id,order_no,amount,paid,name,phone,address,send_bill,
            state,remark,kuaidi,kuaidi_name,kuaidi_state,type')
                ->where($map)->find();
            $order['state'] != '4' ? error("该状态下无法操作") : true;
            if ($order['type'] == 1) {
                $order_detail = M("MallOrderDetail")->alias('a')
                    ->field('a.id,a.order_id,a.number,a.goods_id,b.name,b.sale_price,b.price,b.img,b.thumb')
                    ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
                    ->group("b.goods_id")
                    ->where(['a.order_id' => $order['id']])->select();
                foreach($order_detail as $k=>$v){
                    $order_detail[$k]['img'] = $this->url.$v['img'];
                    $order_detail[$k]['thumb'] = $this->url.$v['thumb'];
                }
            }
            success($order_detail);

        }
    }

    /**
     *@商品评价
     */
    public function comment_goods()
    {
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            $data['user_id'] = $member['user_id'];
//            $data['content'] = json_decode($data['content'],true);
            $data1 = json_encode($data);
            file_put_contents('1.txt',$data1,FILE_APPEND);
            if(empty($data['order_no']))        error("商品订单不能为空");
            $order = M('MallOrder')->where(['order_no'=>$data['order_no']])->find();
            if(!$order)                         error("订单号错误");
            $content = json_decode($data['content'],true);
            foreach($content as $k=>$v){
                if(empty($v['content'])){
                    if($v['goods_mark']>3){
                        $content[$k]['content'] = '好评!好评!';
                    }else{
                        $content[$k]['content'] = '中评';
                    }
                }
                if(empty($v['goods_mark'])){
                    $content[$k]['goods_mark'] = 1;
                }
                if(empty($v['img']) && empty($content[$k]['content'])){
                    unset($content[$k]);
                }
            }
            $content = array_values($content);
            file_put_contents('3.txt',json_encode($content),FILE_APPEND);
            if(empty($content))                 error("评论不能为空");
            $time = date("Y-m-d H:i:s");
            foreach($content as $k=>$v){
                $code[] = [
                    'user_id' => $member['user_id'],
                    'intime'  => date("Y-m-d H:i:s"),
                    'object_id' => $v['goods_id'],
                    'content' => $v['content'],
                    'goods_mark' => $v['goods_mark'],
                    'img' => $v['img'],
                    'thumb' => $v['thumb'],
                    'intime' => $time,
                ];
            }
            if(count($code)>1){
                $result = M('Comment')->addAll($code);
            }else{
                $result = M('Comment')->add($code);
            }
            if($result){
                M('MallOrder')->where(['id' => $order['id']])->save(['state' => '5']);
                success("评论成功");
            }else{
                error("评价失败");
            }
        }
    }

    /**
     *@商品评价
     */
    public function comment_goods_ios()
    {
        if (IS_POST) {
            $member = checklogin();
            $data = $_POST;
            $data['user_id'] = $member['user_id'];

            if(empty($data['order_no']))        error("商品订单不能为空");
            $order = M('MallOrder')->where(['order_no'=>$data['order_no']])->find();
            if(!$order)                         error("订单号错误");
            foreach($data['content'] as $v){
                $content[] = json_decode($v,true);
            }
            foreach($content as $k=>$v){
                if(empty($v['content'])){
                    if($v['goods_mark']>3){
                        $content[$k]['content'] = '好评!好评!';
                    }else{
                        $content[$k]['content'] = '中评';
                    }
                }
                if(empty($v['goods_mark'])){
                    $content[$k]['goods_mark'] = 1;
                }
                if(empty($v['img']) && empty($content[$k]['content'])){
                    unset($content[$k]);
                }else{
                    $content[$k]['content'] = is_sensitive_word($v['content']);
                }
            }
            $content = array_values($content);
//            $content = json_encode($content);
//            file_put_contents('1.txt',$content);
//            die;
            if(empty($content))                 error("评论不能为空");
            foreach($content as $k=>$v){
                $code[] = [
                    'user_id' => $member['user_id'],
                    'intime'  => date("Y-m-d H:i:s"),
                    'object_id' => $v['goods_id'],
                    'content' => $v['content'],
                    'goods_mark' => $v['goods_mark'],
                    'img' => $v['img'],
                    'thumb' => $v['thumb'],
                ];
            }
            if(count($code)>1){
                $result = M('Comment')->addAll($code);
            }else{
                $result = M('Comment')->add($code);
            }
            if($result){
                M('MallOrder')->where(['id' => $order['id']])->save(['state' => '5']);
                success("评论成功");
            }else{
                error("评价失败");
            }
        }
    }

    /**
     *@猜你喜欢
     */
    public function maybe_enjoy(){
        if(IS_POST){
            //$member = checklogin();
            $pagesize = I('pagesize');
            $pagesize ? $pagesize = $pagesize : $pagesize = 4;
            $uid = I('uid');
            $cart = M('GoodCart')->where(['mid'=>$uid])->select();
            foreach($cart as $k=>$v){
                $goods_id[] = $v['goods_id'];
            }
            !empty($goods_id)   &&  $map['goods'] = ['not in',$goods_id];
            $map['is_del'] = 1;
            $map['status'] = 2;
            $goods = M('Goods')
                ->field("goods_id,sale_price,name,thumb,img,price")
                ->where($map)->order('rand()')
                ->limit($pagesize)
                ->select();
            foreach($goods as $k=>$v){
                $goods[$k]['thumb'] = $this->url.$v['thumb'];
                $goods[$k]['img'] = $this->url.$v['img'];
            }
            success($goods);
        }
    }

}