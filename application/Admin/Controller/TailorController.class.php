<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/27
 * Time: 15:55
 */

namespace Admin\Controller;


class TailorController extends BaseController
{
    /**
     * @预售列表
     */
    public function index(){
        $map = [];
        !empty($_GET['name']) && $map['b.name'] = ['like', '%' . I('name') . '%'];
        !empty($_GET['first_category']) && $map['b.first_category'] = I('first_category');
        !empty($_GET['second_category']) && $map['b.second_category'] = I('second_category');
        $map['a.is_del'] = '1';
        $map['b.is_del'] = '1';
        $map['b.status'] = '2';
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('GoodsTailor')->alias('a')
            ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
            ->where($map)->count();
        $p = $this->getpage($count, $num);
        $data = M("GoodsTailor")->alias('a')
            ->field('a.tailor_id,a.goods_id,a.presale_price,a.min_count,a.pay_count,a.start_time,a.end_time,a.intime,a.status,
            b.name,b.img,b.number,b.first_category,b.second_category,b.sale_number')
            ->join("LEFT JOIN __GOODS__ b on a.goods_id = b.goods_id")
            ->where($map)->order("a.status desc,a.intime asc")->limit($p->firstRow, $p->listRows)->select();
        foreach($data as $k=>$v){
            $data[$k]['first_category'] = M('Category')->where(['id'=>$v['first_category']])->getField('category');
            $data[$k]['second_category'] = M('Category')->where(['id'=>$v['second_category']])->getField('category');
            if($v['status'] == '2'){
                $time = strtotime($v['end_time']);
                if($time - time() < 0){
                    M('GoodsTailor')->where(['tailor_id'=>$v['tailor_id']])->save(['status'=>'1']);
                }
            }
        }
        $first_category = M('Category')->where(['cate_id'=>['eq','0']])->select();
        $second_category = M('Category')->where(['cate_id'=>['neq','0']])->select();
        $this->assign(['list' => $data, 'page' => $p->show(),'first_category'=>$first_category,'second_category'=>$second_category]);
        $url = $_SERVER['REQUEST_URI'];
        session('url', $url);
        $this->display();
    }

    /**
     *@添加预售
     */
    public function add_tailor(){
        if(IS_POST){
            echo json_encode(D('GoodsTailor')->check());
        }else{
            $goods = M('Goods')->where(['number'=>['gt',0],'status'=>'2','is_del'=>'1','type'=>'2'])->select();
            $this->assign(['goods'=>$goods]);
            $this->display();
        }
    }

    /**
     *@编辑预售
     */
    public function edit_tailor(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('GoodsTailor')->check());
        }else{
            $m = M('GoodsTailor')->where(['tailor_id'=>$id])->find();
            $check = M('Goods')->where(['goods_id'=>$m['goods_id']])->find();
            $m['number'] = $check['number'];
            $m['sale_number'] = $check['sale_number'];
            $m['price'] = $check['price'];
            $m['sale_price'] = $check['sale_price'];
            $m['img'] = $check['img'];
            $goods = M('Goods')->where(['number'=>['gt',0],'status'=>'2','is_del'=>'1','type'=>'2'])->select();
            $this->assign(['goods'=>$goods,'m'=>$m]);
            $this->display('Tailor/add_tailor');
        }
    }

    /**
     *@编辑预售
     */
    public function edit_ajax_tailor(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('GoodsTailor')->check());
        }else{
            $m = M('GoodsTailor')->where(['tailor_id'=>$id])->find();
            echo json_encode(array('status'=>'ok','info'=>$m));
        }
    }

    /**
     *@改变商品的上架信息
     */
    public function change_tailor_status(){
        if(IS_POST){
            $id = I('id');
            $check = M('GoodsTailor')->where(['tailor_id'=>$id])->find();
            $status = $check['status'];
            if($status == 1 && strtotime($check['end_time']) - time() < 0){
                echo json_encode(array('status'=>'error','info'=>'预售时间结束'));
                exit;
            }
            $abs = 3 - $status;
            $arr = ['下架中','已上架'];
            $result = M('GoodsTailor')->where(['tailor_id'=>$id])->save(['status'=>$abs]);
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
     *@删除定制预售
     */
    public function del_tailor(){
        if(IS_POST){
            $ids = I('ids');
            $map['tailor_id'] = ['in',$ids];
            $result = M('GoodsTailor')->where([$map])->save(['is_del'=>"2"]);
            if ($result) {
                echo json_encode(['status' => "ok", 'info' => '删除记录成功!', 'url' => session('url')]);
            } else {
                echo json_encode(['status' => "error", 'info' => '删除记录失败!']);
            }
        }
    }

    /**
     * @根据商品id查找商品
     */
    public function link_goods(){
        $id = I('goods_id');
        $goods = M('Goods')->where(['goods_id'=>$id])->find();
        success($goods);
    }

    /**
     *@商品一级分类
     */
    public function first(){
        $map=[];
        !empty($_GET['title']) && $map['category'] = ['like','%'.I('title').'%'];
        $num  = I('num');
        $map['cate_id'] = '0';
        $map['type'] = '2';
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Category')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Category")->where($map)->limit($p->firstRow,$p->listRows)->order("sort desc")->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@商品一级分类
     */
    public function second(){
        $map=[];
        !empty($_GET['title']) && $map['category'] = ['like','%'.I('title').'%'];
        $map['cate_id'] = I('cate_id');
        $map['type'] = '2';
        !empty($_GET['first_id']) && $map['cate_id'] = I('first_id');
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Category')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Category")->where($map)->limit($p->firstRow,$p->listRows)->order("sort desc")->select();
        foreach($data as $k=>$v){
            $data[$k]['first_category'] = M('Category')->where(['id'=>$v['cate_id']])->getField('category');
        }
        $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'2'])->select();
        $this->assign(['list'=>$data,'page'=>$p->show(),'first_category'=>$first_category]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑商品一级分类
     */
    public function edit_first_category(){
        if(IS_POST){
            echo json_encode(D('Category')->check());
        }else{
            $id = I('id');
            $re = M('Category')->where(['id'=>$id])->find();
            success($re);
        }
    }

    /**
     *@删除父分类
     */
    public function del_first_category(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $map['cate_id'] = array('in',$id);
        $result = M('Category')->where($data)->delete();
        M('Category')->where($map)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@删除子分类
     */
    public function del_second_category(){
        $data['id'] = I('id');
        $result = M('Category')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }



    /**
     *@商品列表
     */
    public function goods_list()
    {
        $map = [];
        !empty($_GET['name']) && $map['name'] = ['like', '%' . I('name') . '%'];
        !empty($_GET['status']) && $map['status'] = I('status');
        !empty($_GET['first_category']) && $map['first_category'] = I('first_category');
        !empty($_GET['second_category']) && $map['second_category'] = I('second_category');
        $map['is_del'] = '1';
        $map['type'] = '2';
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('Goods')->where($map)->count();
        $p = $this->getpage($count, $num);
        $data = M("Goods")->where($map)->order("status desc,sort desc,intime asc")->limit($p->firstRow, $p->listRows)->select();
        foreach($data as $k=>$v){
            $data[$k]['first_category'] = M('Category')->where(['id'=>$v['first_category']])->getField('category');
            $data[$k]['second_category'] = M('Category')->where(['id'=>$v['second_category']])->getField('category');
        }
        $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'2'])->select();
        $second_category = M('Category')->where(['cate_id'=>['neq','0'],'type'=>'2'])->select();
        $this->assign(['list' => $data, 'page' => $p->show(),'first_category'=>$first_category,'second_category'=>$second_category]);
        $url = $_SERVER['REQUEST_URI'];
        session('url', $url);
        $this->display();
    }

    /**
     *分类联动
     */
    public function get_son_category(){
        if(IS_POST){
            $first = I('first');
            $second = M('Category')->where(['cate_id'=>$first])->select();
            $option= "<option value=''>选择二级分类</option>";
            if(!empty($second)){
                foreach($second as $k=>$v){
                    $option.="<option value=".$second[$k]['id'].">".$second[$k]['category']."</option>";
                }
            }else{
                $option= "<option value=''>暂无二级分类</option>";
            }
            echo $option;
        }
    }

    /**
     * @添加商品
     */
    public function add_goods(){
        check_auth();
        if(IS_POST){
            echo json_encode(D('Goods')->auth());
        }else{
            $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'2'])->order("sort desc,intime asc")->select();
            $this->assign(['first_category'=>$first_category]);
            $this->display();
        }
    }

    /**
     *@编辑商品
     */
    public function edit_goods(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Goods')->auth());
        }else{
            $re = M('Goods')->where(['goods_id'=>$id])->find();
            $re['imgs'] = explode(',',$re['imgs']);
            $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'2'])->order("sort desc,intime asc")->select();
            $second_category = M('Category')->where(['cate_id'=>['neq','0'],'type'=>'2'])->order("sort desc,intime asc")->select();
            $kinds_detail = M('GoodsKinds')->where(['goods_id'=>$id,'is_del'=>'1','type'=>'1'])->select();
            $kinds_detail2 = M('GoodsKinds')->where(['goods_id'=>$id,'is_del'=>'1','type'=>'2'])->select();
            $stock = M('GoodsStock')->where(['goods_id'=>$id])->select();
            $this->assign(['re'=>$re,'first_category'=>$first_category,'kinds_detail'=>$kinds_detail,
                'kinds_detail2'=>$kinds_detail2,'stock'=>$stock,'second_category'=>$second_category]);
            $this->display('Tailor/add_goods');
        }
    }

    /**
     *@改变商品的上架信息
     */
    public function change_goods_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('Goods')->where(['goods_id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['下架中','已上架'];
            $result = M('Goods')->where(['goods_id'=>$id])->save(['status'=>$abs]);
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
     *@添加/编辑商品型号参数
     */
    public function edit_kinds(){
        $id = I('id');
        if(IS_POST){
            $goods_id = I('goods_id');
            $kinds1 = I('kinds1');
            $kinds2 = I('kinds2');
            M('Goods')->where(['goods_id'=>$goods_id])->save(['kinds1'=>$kinds1,'kinds2'=>$kinds2]);
            $kinds1 = M('Goods')->where(['goods_id'=>$goods_id])->getField('kinds1');
            if(empty($kinds1)){
                echo json_encode(array('status'=>'error','info'=>"请先填写商品型号一"));
                die;
            }
            $data = $_POST;
            if(empty($data['kinds_detail'])){
                echo json_encode(array('status'=>'error','info'=>"型号名称不能为空"));
                die;
            }
            if(!is_numeric($data['price']) || $data['price'] < 0){
                echo json_encode(array('status'=>'error','info'=>"请填写正确的商品原价"));
                die;
            }
            if(!is_numeric($data['sale_price']) || $data['sale_price'] < 0) {
                echo json_encode(array('status'=>'error','info'=>"请填写正确的商品原价"));
                die;
            }
//            if(!is_numeric($data['number']) || $data['number'] < 0) {
//                echo json_encode(array('status'=>'error','info'=>"请填写正确的库存数量"));
//                die;
//            }
            if(empty($data['id'])){
//                $check = M('GoodsKinds')->where(['goods_id'=>$goods_id,'is_del'=>'1'])->count();
//                if($check>9) {
//                    echo json_encode(array('status'=>'error','info'=>"商品型号只能添加10个"));
//                    die;
//                }
                $result = M('GoodsKinds')->add($data);
                $action = '新增';
            }else{
                $result = M('GoodsKinds')->where(['kind_id'=>$id])->save($data);
                $action = '编辑';
            }
            if($result !== false){
                echo json_encode(array('status'=>'ok','info'=>$action.'商品型号记录成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'商品型号记录失败'));
            }
        }else{
            $re = M('GoodsKinds')->where(['kind_id'=>$id])->find();
            echo json_encode(array('status'=>'ok','data'=>$re));
        }
    }


    public function edit_kinds2(){
        $id = I('id');
        if(IS_POST){
            $goods_id = I('goods_id');
            $kinds1 = I('kinds1');
            $kinds2 = I('kinds2');
            M('Goods')->where(['goods_id'=>$goods_id])->save(['kinds1'=>$kinds1,'kinds2'=>$kinds2]);
            $kinds2 = M('Goods')->where(['goods_id'=>$goods_id])->getField('kinds2');
            if(empty($kinds2)){
                echo json_encode(array('status'=>'error','info'=>"请先填写商品型号二"));
                die;
            }
            $data = $_POST;
            if(empty($data['kinds_detail'])){
                echo json_encode(array('status'=>'error','info'=>"型号名称不能为空"));
                die;
            }

            if(!is_numeric($data['price']) || $data['price'] < 0){
                echo json_encode(array('status'=>'error','info'=>"请填写正确的商品原价"));
                die;
            }
            if(!is_numeric($data['sale_price']) || $data['sale_price'] < 0) {
                echo json_encode(array('status'=>'error','info'=>"请填写正确的商品售价"));
                die;
            }
//            if(!is_numeric($data['number']) || $data['number'] < 0) {
//                echo json_encode(array('status'=>'error','info'=>"请填写正确的库存数量"));
//                die;
//            }
            if(empty($data['id'])){
//                $check = M('GoodsKinds2')->where(['goods_id'=>$goods_id,'is_del'=>'1'])->count();
//                if($check>9) {
//                    echo json_encode(array('status'=>'error','info'=>"商品型号只能添加10个"));
//                    die;
//                }
                $result = M('GoodsKinds2')->add($data);
                $action = '新增';
            }else{
                $result = M('GoodsKinds2')->where(['kind_id'=>$id])->save($data);
                $action = '编辑';
            }
            if($result !== false){
                echo json_encode(array('status'=>'ok','info'=>$action.'商品型号记录成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'商品型号记录失败'));
            }
        }else{
            $re = M('GoodsKinds2')->where(['kind_id'=>$id])->find();
            echo json_encode(array('status'=>'ok','data'=>$re));
        }
    }

    /**
     * @添加商品型号库存
     */
    public function edit_goods_stock(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            if(empty($data['kinds_id1']) && empty($data['kinds2'])){
                echo json_encode(array('status'=>'error','info'=>'型号不能为空'));
                die;
            }else{
                $data['kinds'] = $data['kinds_id1'].','.$data['kinds_id2'];
            }
            if(!is_numeric($data['number']) || $data['number'] < 0) {
                echo json_encode(array('status'=>'error','info'=>"请填写正确的库存数量"));
                die;
            }

            if(!is_numeric($data['sale_number']) || $data['sale_number'] < 0) {
                echo json_encode(array('status'=>'error','info'=>"请填写正确的出售数量"));
                die;
            }

            if(empty($data['id'])){
                $check = M('GoodsStock')->where(['kinds'=>$data['kinds']])->find();
                if($check){
                    echo json_encode(array('status'=>'error','info'=>"该型号库存已存在"));
                    die;
                }
                $result = M('GoodsStock')->add($data);
                $action = '新增';
            }else{
                $result = M('GoodsStock')->where(['stock_id'=>$id])->save($data);
                $action = '编辑';
            }
            if($result !== false){
                echo json_encode(array('status'=>'ok','info'=>$action.'商品型号库存记录成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'商品型号库存记录失败'));
            }

        }else{
            $re = M('GoodsStock')->where(['stock_id'=>$id])->find();
            $kinds = explode(',',$re['kinds']);
            $re['kinds1'] = $kinds[0];
            $re['kinds2'] = $kinds[1];
            echo json_encode(array('status'=>'ok','data'=>$re));
        }
    }

    /**
     * @删除型号
     */
    public function del_goods_stock(){
        if(IS_POST){
            $id = I('id');
            $result = M('GoodsStock')->where(['stock_id'=>$id])->delete();
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'删除商品型号库存成功'));
            }else{
                echo json_encode(array('status'=>'error','info'=>'删除商品型号库存失败'));
            }
        }
    }

    /**
     *@删除商品型号
     */
    public function del_kinds1(){
        $id = I('id');
        $result = M('GoodsKinds')->where(['kind_id'=>$id])->save(['is_del'=>'2']);
        if($result){
            echo json_encode(array('status'=>'ok','info'=>'删除商品型号成功'));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除商品型号失败'));
        }
    }

    /**
     *@删除商品型号
     */
    public function del_kinds2(){
        $id = I('id');
        $result = M('GoodsKinds2')->where(['kind_id'=>$id])->save(['is_del'=>'2']);
        if($result){
            echo json_encode(array('status'=>'ok','info'=>'删除商品型号成功'));
        }else{
            echo json_encode(array('status'=>'error','info'=>'删除商品型号失败'));
        }
    }

    /**
     *@删除商品
     */
    public function del_goods(){
        if(IS_POST) {
            $id = I('ids');
            $map['goods_id'] = array('in', $id);
            $data['is_del'] = 2;
//        $data['del_time'] = date("Y-m-d H:i:s",time());
            $result = M('Goods')->where($map)->save($data);
            if ($result) {
//                $id = explode(',',$id);
//                if (is_array($id)) {
//                    foreach ($id as $val) {
//                        work_log($table = 'AirportOrder', $record_id = $val,'1', $work = '删除了订单记录');
//                    }
//                } else {
//                    work_log($table = 'AirportOrder', $record_id = $id, '1',$work = '删除了订单记录');
//                }
                echo json_encode(['status' => "ok", 'info' => '删除记录成功!', 'url' => session('url')]);
            } else {
                echo json_encode(['status' => "error", 'info' => '删除记录失败!']);
            }
        }
    }

    /**
     *@恢复
     */
    public function recovery_goods(){
        if(IS_POST){
            $id = I('ids');
            $data['goods_id'] = array('in',$id);
            $user = M('Goods')->where($data)->save(['is_del'=>'1']);
            if($user){
                echo json_encode(['status'=>"ok",'info'=>'记录恢复成功!','url'=>session('url')]);
            }else{
                echo json_encode(['status'=>"error",'info'=>'记录恢复失败!']);
            }
        }
    }

    public function del_goods_true(){
        if(IS_POST){
            $id = I('ids');
            $data['goods_id'] = array('in',$id);
            $user = M('Goods')->where($data)->delete();
            if($user){
                echo json_encode(['status'=>"ok",'info'=>'彻底删除记录成功!','url'=>session('url')]);
            }else{
                echo json_encode(['status'=>"error",'info'=>'删除失败!']);
            }
        }
    }

    /**
     *@复制商品
     */
    public function copy_goods(){
        if(IS_POST){
            $id = I('id');
            $check = M('Goods')->where(['goods_id'=>$id])->find();
            if($check){
                unset($check['goods_id']);
                $result = M('Goods')->add($check);
                $kinds = M('GoodsKinds')->where(['goods_id'=>$id])->select();
                if(!empty($kinds)){
                    foreach($kinds as $k => $v){
                        $data['goods_id'] = $result;
                        $data['price'] = $v['price'];
                        $data['sale_price'] = $v['sale_price'];
                        $data['kinds_detail'] = $v['kinds_detail'];
                        M('GoodsKinds')->add($data);
                    }
                }
                $kinds2 = M('GoodsKinds2')->where(['goods_id'=>$id])->select();
                if(!empty($kinds2)){
                    foreach($kinds as $k => $v){
                        $data['goods_id'] = $result;
                        $data['price'] = $v['price'];
                        $data['sale_price'] = $v['sale_price'];
                        $data['kinds_detail'] = $v['kinds_detail'];
                        M('GoodsKinds2')->add($data);
                    }
                }
                if($result){
                    echo json_encode(array('status'=>'ok','info'=>"复制商品成功"));
                }else{
                    echo json_encode(array('status'=>'error','info'=>"复制商品失败"));
                }
            }else{
                echo json_encode(array('status'=>'error','info'=>"要复制的商品没有找到"));
            }
        }
    }

    /**
     *@已删除的商品列表
     */
    public function is_del_goods(){
        $map = [];
        !empty($_GET['name']) && $map['name'] = ['like', '%' . I('name') . '%'];
        !empty($_GET['status']) && $map['status'] = I('status');
        $map['is_del'] = '2';
        $map['type'] = '1';
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('Goods')->where($map)->count();
        $p = $this->getpage($count, $num);
        $data = M("Goods")->where($map)->order("status desc,intime asc")->limit($p->firstRow, $p->listRows)->select();
        $act = I("get.act");
        if ($act == "download") {
            $dat = M("Goods")->where($map)->select();
            $str = '已删除商品列表' . date('YmdHis');
            header("Content-type:application/vnd.ms-excel");
            header("Content-Disposition:filename={$str}.csv");
            echo "\xEF\xBB\xBF" . "序号,商品名称,品牌,分类,基础价,基础售价,库存,已售,创建时间\n";
            foreach ($dat as $k => $v) {
                echo $k . ","
                    . $v["name"] . "\t,"
                    . $v["brand"] . "\t,"
                    . $v["son_category"] . "\t,"
                    . $v["price"] . "\t,"
                    . $v["sale_price"] . "\t,"
                    . $v["number"] . "\t,"
                    . $v["sale_number"] . "\t,"
                    . $v["intime"] . "\t,"
                    . "\n";
            }
        } else {
            $this->assign(['list' => $data, 'page' => $p->show()]);
            $url = $_SERVER['REQUEST_URI'];
            session('url', $url);
            $this->display();

        }
    }
}