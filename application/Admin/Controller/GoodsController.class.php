<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/12/15
 * Time: 20:00
 */

namespace Admin\Controller;


class GoodsController extends BaseController
{
    /**
     *@商品一级分类
     */
    public function index(){
        $map=[];
        !empty($_GET['title']) && $map['category'] = ['like','%'.I('title').'%'];
        $num  = I('num');
        $map['cate_id'] = '0';
        $map['type'] = '1';
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
     *@商品二级分类
     */
    public function second(){
        $map=[];
        !empty($_GET['title']) && $map['category'] = ['like','%'.I('title').'%'];
        $map['cate_id'] = I('cate_id');
        $map['type'] = '1';
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
        $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'1'])->select();
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
            $this->assign(['re'=>$re]);
            layout(false);
            $this->display();
        }
    }

    /**
     *@编辑商品一级分类
     */
    public function edit_second_category(){
        if(IS_POST){
            echo json_encode(D('Category')->check());
        }else{
            $id = I('id');
            $re = M('Category')->where(['id'=>$id])->find();
            $this->assign(['re'=>$re]);
            layout(false);
            $this->display();
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
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('Category')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@改变分类状态
     */
    public function change_category_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('Category')->where(['id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['1','2'];
            $result = M('Category')->where(['id'=>$id])->save(['status'=>$abs]);
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
        $map['type'] = '1';
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('Goods')->where($map)->count();
        $p = $this->getpage($count, $num);
        $data = M("Goods")->where($map)->order("status desc,sort desc,intime desc")->limit($p->firstRow, $p->listRows)->select();
        foreach($data as $k=>$v){
            $data[$k]['first_category'] = M('Category')->where(['id'=>$v['first_category']])->getField('category');
            $data[$k]['second_category'] = M('Category')->where(['id'=>$v['second_category']])->getField('category');
        }
        $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'1'])->select();
        $second_category = M('Category')->where(['cate_id'=>['neq','0'],'type'=>'1'])->select();
        $this->assign(['list' => $data, 'page' => $p->show(),'count'=>$count,
            'first_category'=>$first_category,'second_category'=>$second_category]);
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
            $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'1'])->order("sort desc,intime asc")->select();
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
            $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'1'])->order("sort desc,intime asc")->select();
            $second_category = M('Category')->where(['cate_id'=>['neq','0'],'type'=>'1'])->order("sort desc,intime asc")->select();
            $kinds_detail = M('GoodsKinds')->where(['goods_id'=>$id,'is_del'=>'1','type'=>'1'])->select();
            $kinds_detail2 = M('GoodsKinds')->where(['goods_id'=>$id,'is_del'=>'1','type'=>'2'])->select();
            $stock = M('GoodsStock')->where(['goods_id'=>$id])->select();
            $this->assign(['re'=>$re,'first_category'=>$first_category,'kinds_detail'=>$kinds_detail,
                'kinds_detail2'=>$kinds_detail2,'stock'=>$stock,'second_category'=>$second_category]);
            $this->display('Goods/add_goods');
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
            $arr = ['1','2'];
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
     *@改变商品的推荐信息
     */
    public function change_goods_tuijian(){
        if(IS_POST){
            $id = I('id');
            $status = M('Goods')->where(['goods_id'=>$id])->getField('is_tuijian');
            $abs = 3 - $status;
            $arr = ['1','2'];
            $result = M('Goods')->where(['goods_id'=>$id])->save(['is_tuijian'=>$abs]);
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
     *@商品型号参数
     */
    public function kinds_param(){
        $id = I('id');
        $re = M('Goods')->where(['goods_id'=>$id])->find();
        $re['kinds'] = explode(',',$re['kinds']);
        $kinds_detail = M('GoodsKinds')->where(['goods_id'=>$id,'is_del'=>'1','type'=>'1'])->select();
        $kinds_detail2 = M('GoodsKinds')->where(['goods_id'=>$id,'is_del'=>'1','type'=>'2'])->select();
        $stock = M('GoodsStock')->where(['goods_id'=>$id])->select();
        $this->assign(['re'=>$re,'kinds_detail'=>$kinds_detail,
            'kinds_detail2'=>$kinds_detail2,'stock'=>$stock]);
        $this->display();
    }

    /**
     *@添加/编辑商品型号参数
     */
    public function edit_kinds(){
        $id = I('id');
        if(IS_POST){
            $goods_id = I('goods_id');
            $data = $_POST;
            if( !M()->autoCheckToken($_POST) ){
                echo json_encode(array('status'=>'error','info'=>"禁止站外提交！"));
                die;
            }
            $kinds = I('kinds');
            if($data['type'] == 1){
                if(empty($kinds[0])){
                    echo json_encode(array('status'=>'error','info'=>"请先填写商品型号一"));
                    die;
                }
            }else if($data['type'] == 2){
                if(empty($kinds[1])){
                    echo json_encode(array('status'=>'error','info'=>"请先填写商品型号二"));
                    die;
                }
            }
            if(empty($data['kinds_detail'])){
                echo json_encode(array('status'=>'error','info'=>"型号名称不能为空"));
                die;
            }
            $kinds = implode(',',$kinds);
            M('Goods')->where(['goods_id'=>$goods_id])->save(['kinds'=>$kinds]);
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
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'商品型号记录失败'));
                die;
            }
        }else{
            $re = M('GoodsKinds')->where(['kind_id'=>$id])->find();
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

//            if(empty($data['template_id'])){
//                echo json_encode(array('status'=>'error','info'=>'型号模板不能为空'));
//                die;
//            }

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
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'商品型号库存记录失败'));
                die;
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
    public function del_kinds(){
        $id = I('id');
        $result = M('GoodsKinds')->where(['kind_id'=>$id])->save(['is_del'=>'2']);
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
                $goods_id = $check['goods_id'];
                unset($check['goods_id']);
                $m  = M('Goods');
                $m -> startTrans();
                $result = $m->add($check);
                if($result){
                    $kinds = M('GoodsKinds')->where(['goods_id'=>$goods_id,'is_del'=>1])->select();
                    if($kinds){
                        foreach($kinds as $k=>$v){
                            $tag[] = [
                                'goods_id' => $result,
                                'price' => $v['price'],
                                'sale_price' => $v['sale_price'],
                                'kinds_detail' => $v['kinds_detail'],
                                'type' => $v['type'],
                            ];
                        }
                        $re = M('GoodsKinds')->addAll($tag);
                        if(!$re){
                            $m->rollback();
                            echo json_encode(array('status'=>'error','info'=>"复制商品失败"));
                            die;
                        }
                    }

                    $stock = M('GoodsStock')->where(['goods_id'=>$goods_id])->select();
                    if($stock){
                        foreach($stock as $k=>$v){
                            $tag1[] = [
                                'goods_id' => $result,
                                'kinds' => $v['kinds'],
                                'number' => $v['number'],
                                'sale_number' => $v['sale_number'],
                                'template_id' => $v['template_id'],
                            ];
                        }
                        $re = M('GoodsStock')->addAll($tag1);
                        if(!$re){
                            $m->rollback();
                            echo json_encode(array('status'=>'error','info'=>"复制商品失败"));
                            die;
                        }
                    }
                    $m->commit();
                    echo json_encode(array('status'=>'ok','info'=>"复制商品成功"));
                    die;
                }else{
                    $m->rollback();
                    echo json_encode(array('status'=>'error','info'=>"复制商品失败"));
                    die;
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
        !empty($_GET['first_category']) && $map['first_category'] = I('first_category');
        !empty($_GET['second_category']) && $map['second_category'] = I('second_category');
        $map['is_del'] = '2';
        $map['type'] = '1';
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $this->assign('nus', $num);
        $count = M('Goods')->where($map)->count();
        $p = $this->getpage($count, $num);
        $data = M("Goods")->where($map)->order("status desc,intime desc")->limit($p->firstRow, $p->listRows)->select();
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
            $first_category = M('Category')->where(['cate_id'=>['eq','0'],'type'=>'1'])->select();
            $second_category = M('Category')->where(['cate_id'=>['neq','0'],'type'=>'1'])->select();
            $this->assign(['list' => $data, 'page' => $p->show(),'first_category'=>$first_category,
                'second_category'=>$second_category,'count'=>$count]);
            $url = $_SERVER['REQUEST_URI'];
            session('url', $url);
            $this->display();

        }
    }

    /**
     *@商城分类
     */
    public function category(){
        $map=[];
        !empty($_GET['category']) && $map['category'] = ['like','%'.I('title').'%'];
        $num  = I('num');
        $map['cate_id'] = ['neq','0'];
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Category')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Category")->where($map)->limit($p->firstRow,$p->listRows)->order("sort desc")->select();
        foreach($data as $key=>$val){
            $data[$key]['son'] = M('Category')->where(['cate_id'=>$val['id']])->select();
        }
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     * @添加或编辑分类
     */
    public function edit_root_category(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['category'])){
                echo json_encode(array('status'=>'error','info'=>'分类名称不能为空','class'=>'category'));
                die;
            }
            if(empty($data['icon'])){
                echo json_encode(array('status'=>'error','info'=>'icon图标不能为空','class'=>'icon'));
                die;
            }
            if(empty($data['picture'])){
                echo json_encode(array('status'=>'error','info'=>'图片不能为空','class'=>'picture'));
                die;
            }
            if(!empty($data['sort'])){
                if(!ctype_digit($data['sort'])){
                    echo json_encode(array('status'=>'error','info'=>'请填写正确数字','class'=>'sort'));
                    die;
                }
            }
            if(empty($data['id'])){
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('Category')->add($data);
                $action = '添加';
            }else{
                $data['uptime'] = date("Y-m-d H:i:s",time());
                $result = M('Category')->save($data);
                $action = '编辑';

            }
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$action.'商城分类记录成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'商城分类记录失败'));
            }
        }
    }

    /**
     *@添加子分类
     */
    public function edit_son_category()
    {
        if (IS_POST) {
            $data = $_POST;
            if (empty($data['category'])) {
                echo json_encode(array('status' => 'error', 'info' => '分类名称不能为空'));
                die;
            }
            if (empty($data['id'])) {
                $data['intime'] = date("Y-m-d H:i:s", time());
                $result = M('Category')->add($data);
                $action = '添加';
            } else {
                $data['uptime'] = date("Y-m-d H:i:s", time());
                $result = M('Category')->where(['id' => $data['id']])->save($data);
                $action = '编辑';
            }
            if ($result) {
                echo json_encode(array('status' => 'ok', 'info' => $action . '分类成功', 'url' => session('url')));
            } else {
                echo json_encode(array('status' => 'error', 'info' => $action . '分类失败'));
            }
        }
    }


    /**
     *@商品上移排序
     */
    public function plus_goods_sort(){
        if(IS_POST){
            $goods_id = I('goods_id');
            $type = I('type');
            $type ? $type = $type   :$type = 1;
            !empty($_POST['first_category'])       &&   $map['first_category'] = I('first_category');
            !empty($_POST['second_category'])       &&   $map['second_category'] = I('second_category');
            !empty($_POST['name'])       &&   $map['code|name'] = ['like','%'.I('name').'%'];
            $check = M('Goods')->where(['goods_id'=>$goods_id])->find();
            $map['sort'] = ['gt',$check['sort']];
            $map['status'] = 2;
            $map['is_del'] = 1;
            $map['type'] = $type;
            $check = M('Goods')->where(['goods_id'=>$goods_id])->find();
            if($check['status'] != '2'){
                echo json_encode(['status'=>"error",'info'=>'请先上架在操作']);
                die;
            }
            $last_goods = M('Goods')->where($map)
                ->order("sort asc,intime desc")->limit(1)->select();
            if(empty($last_goods)){
                echo json_encode(['status'=>"error",'info'=>'商品不能移动']);
                die;
            }else{
                $sort = $last_goods[0]['sort'];
                $result = M('Goods')->where(['goods_id'=>$goods_id])->save(['sort'=>$sort]);
                M('Goods')->where(['goods_id'=>$last_goods[0]['goods_id']])->save(['sort'=>$check['sort']]);
            }
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'操作成功']);
                die;
            }else{
                echo json_encode(['status'=>"error",'info'=>'操作失败']);
                die;
            }
        }
    }

    /**
     *@商品下移排序
     */
    public function minus_goods_sort(){
        if(IS_POST){
            $goods_id = I('goods_id');
            $type = I('type');
            $type ? $type = $type   :$type = 1;
            !empty($_POST['first_category'])       &&   $map['first_category'] = I('first_category');
            !empty($_POST['second_category'])       &&   $map['second_category'] = I('second_category');
            !empty($_POST['name'])       &&   $map['code|name'] = ['like','%'.I('name').'%'];
            $check = M('Goods')->where(['goods_id'=>$goods_id])->find();
            $map['sort'] = ['lt',$check['sort']];
            $map['status'] = 2;
            $map['is_del'] = 1;
            $map['type'] = $type;
            if($check['status'] != '2'){
                echo json_encode(['status'=>"error",'info'=>'请先上架在操作']);
                die;
            }
            $last_goods = M('Goods')->where($map)
                ->order("sort desc,intime asc")->limit(1)->select();
            if(empty($last_goods)){
                echo json_encode(['status'=>"error",'info'=>'商品不能移动']);
                die;
            }else{
                $sort = $last_goods[0]['sort'];
                $result = M('Goods')->where(['goods_id'=>$goods_id])->save(['sort'=>$sort]);
                M('Goods')->where(['goods_id'=>$last_goods[0]['goods_id']])->save(['sort'=>$check['sort']]);
            }
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'操作成功']);
                die;
            }else{
                echo json_encode(['status'=>"error",'info'=>'操作失败']);
                die;
            }
        }
    }

    /**
     *@商品置顶
     */
    public function top(){
        if(IS_POST){
            $goods_id = I('goods_id');
            $type = I('type');
            $type ? $type = $type   :$type = 1;
            $check = M('Goods')->where(['goods_id'=>$goods_id])->find();
            $map['sort'] = ['gt',$check['sort']];
            $map['is_del'] = 1;
            $map['type'] = $type;
            if($check['status'] != '2'){
                echo json_encode(['status'=>"error",'info'=>'请先上架在操作']);
                die;
            }
            $last_goods = M('Goods')->where($map)
                ->order("sort desc,intime asc")->limit(1)->select();
            if(empty($last_goods)){
                echo json_encode(['status'=>"error",'info'=>'商品不能移动']);
                die;
            }else{
                $sort = $last_goods[0]['sort']+1;
                $result = M('Goods')->where(['goods_id'=>$goods_id])->save(['sort'=>$sort]);
            }
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'操作成功']);
                die;
            }else{
                echo json_encode(['status'=>"error",'info'=>'操作失败']);
                die;
            }
        }
    }

    /**
     *@商品置后
     */
    public function after(){
        if(IS_POST){
            $goods_id = I('goods_id');
            $type = I('type');
            $type ? $type = $type   :$type = 1;
            $check = M('Goods')->where(['goods_id'=>$goods_id])->find();
            $map['is_del'] = 1;
            $map['sort'] = ['lt',$check['sort']];
            $map['type'] = $type;
            if($check['status'] != '2'){
                echo json_encode(['status'=>"error",'info'=>'请先上架在操作']);
                die;
            }
            $last_goods = M('Goods')->where($map)
                ->order("sort asc,intime asc")->limit(1)->select();
            if(empty($last_goods)){
                echo json_encode(['status'=>"error",'info'=>'商品不能移动']);
                die;
            }else{
                $sort = $last_goods[0]['sort']-1;
                $result = M('Goods')->where(['goods_id'=>$goods_id])->save(['sort'=>$sort]);
            }
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'操作成功']);
                die;
            }else{
                echo json_encode(['status'=>"error",'info'=>'操作失败']);
                die;
            }
        }
    }


}