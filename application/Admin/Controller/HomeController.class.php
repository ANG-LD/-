<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/11
 * Time: 16:39
 */

namespace Admin\Controller;


class HomeController extends BaseController
{
    /**
     *顶部轮播
     */
    public function index(){
        $count = M('Banner')->where(['is_del'=>'1'])->count();
        $num = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list = M('Banner')->where(['is_del'=>'1'])
            ->limit($p->firstRow,$p->listRows)->order('b_intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show(),'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加顶部轮播
     */
    public function add_carousel(){
        if(IS_POST) {
            echo json_encode(D('Banner')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *编辑顶部轮播
     */
    public function edit_carousel(){
        $banner_id = I('id');
        if(IS_POST){
            echo json_encode(D('Banner')->auth());
        }else{
            $banner = M('Banner')->where(['b_id'=>$banner_id])->find();
            $this->assign(['banner'=>$banner]);
            $this->display('Home/add_carousel');
        }
    }

    /**
     *删除carousel
     */
    public function del_carousel(){
        $id = I('ids');
        $data['b_id'] = array('in',$id);
        $result = M('Banner')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@修改Banner推荐状态
     */
    public function change_banner_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('Banner')->where(['b_id'=>$id])->getField('status');
            $abs = 3 - $status;
            //$arr = ['默认状态','开启状态'];
            $result = M('Banner')->where(['b_id'=>$id])->save(['status'=>$abs]);
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
     *@相关协议
     */
    public function xieyi(){
        $map['type'] = 1;
        $map['is_del'] = 1;
        $num = I('num');
        if (empty($num)){
            $num = 10;
        }
        $count = M('Notice')->where($map)->count();
        $p = $this->getpage($count,$num);
        $list  = M('Notice')->where($map)
            ->order("intime desc")->limit($p->firstRow,$p->listRows)
            ->select();
        $this->assign(['list'=>$list,'page'=>$p->show(),'count'=>$count]);
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
            $data = [
                'title' => I('title'),
                'content' => I('content'),
                'id' => I('id'),
            ];
            if(empty($data['title'])){
                echo json_encode(['status'=>'error','info'=>'协议标题不能为空']);
                die;
            }
            if(empty($data['content'])){
                echo json_encode(['status'=>'error','info'=>'协议内容不能为空']);
                die;
            }
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Notice')->where(['id'=>$id])->save($data);
            if($result){
                echo json_encode(['status'=>'ok','info'=>'编辑协议成功','url'=>session('url')]);
            }else{
                echo json_encode(['status'=>'error','info'=>'编辑协议失败']);
            }
        }else{

            $re = M('Notice')->where(['id'=>$id,'type'=>'1'])->find();
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     *关于我们
     */
    public function about_us(){
        $re = M('Aboutus')->where(['id'=>1])->find();
        if(IS_POST){
            echo json_encode(D('Aboutus')->check());
        }else{
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     *广告位管理
     */
    public function advert(){
        $count = M('Banner')->where(['is_del'=>'1'])->count();
        $num = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $p = $this->getpage($count,$num);
        $list = M('Banner')->where(['is_del'=>'1'])
            ->limit($p->firstRow,$p->listRows)->order('b_intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show(),'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加广告位
     */
    public function add_advert(){
        if(IS_POST) {
            echo json_encode(D('Banner')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *编辑广告位
     */
    public function edit_advert(){
        $banner_id = I('id');
        if(IS_POST){
            echo json_encode(D('Banner')->auth());
        }else{
            $banner = M('Banner')->where(['banner_id'=>$banner_id])->find();
            $this->assign(['banner'=>$banner]);
            $this->display('Home/add_advert');
        }
    }

    /**
     *公告信息
     */
    public function notice(){
        $map=[];
        !empty($_GET['title']) && $map['title'] = ['like','%'.I('title').'%'];
        !empty($_GET['state']) && $map['state'] = I('state');
        $map['type'] = '2';
        $map['is_del'] = '1';
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Notice')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Notice")->where($map)->order("state desc,is_top desc")->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show(),'count'=>$count]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加公告
     */
    public function add_notice(){
        if(IS_POST){
            echo json_encode(D('Notice')->check());
        }else{
           $grade = M('Grade')->select();
           $this->assign(['grade'=>$grade]);
           $this->display();
        }
    }

    /**
     *@编辑公告
     */
    public function edit_notice(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Notice')->check());
        }else{
            $re = M('Notice')->where(['id'=>$id])->find();
            $re['object'] = explode(',',$re['object']);
            $grade = M('Grade')->select();
            $this->assign(['re'=>$re,'grade'=>$grade]);
            $this->display('Home/add_notice');
        }
    }

    /**
     *@删除公告
     */
    public function del_notice(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('Notice')->where($data)->save(['is_del'=>2]);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@轮播链接新闻
     */
    public function news(){
        $map=[];
        !empty($_GET['title']) && $map['title'] = ['like','%'.I('title').'%'];
        !empty($_GET['state']) && $map['state'] = I('state');
        $map['type'] = '6';
        $map['is_del'] = '1';
        $num  = I('num');
        if (empty($num)){
            $num = 10;
        }
        $this->assign('nus',$num);
        $count = M('Notice')->where($map)->count();
        $p = $this->getpage($count,$num);
        $data = M("Notice")->where($map)->order("state desc,is_send desc,is_top desc")->limit($p->firstRow,$p->listRows)->select();
        $this->assign(['list'=>$data,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加轮播新闻
     */
    public function add_news(){
        if(IS_POST){
          $data = $_POST;
            if(empty($data['title'])){
                echo json_encode(array('status'=>'error','info'=>'公告主题不能为空'));
                die;
            }
            if(empty($data['content'])){
                echo json_encode(array('status'=>'error','info'=>'公告内容不能为空'));
                die;
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('Notice')->add($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'新增记录成功','url'=>session('url')));
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>'新增记录失败','url'=>session('url')));
            }

        }else{
            $this->display();
        }
    }

    /**
     *@编辑轮播新闻
     */
    public function edit_news(){
        $id = I('id');
        if(IS_POST){
            $data = $_POST;
            if(empty($data['title'])){
                echo json_encode(array('status'=>'error','info'=>'公告主题不能为空'));
                die;
            }
            if(empty($data['content'])){
                echo json_encode(array('status'=>'error','info'=>'公告内容不能为空'));
                die;
            }
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('Notice')->where(['id'=>$id])->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑记录成功','url'=>session('url')));
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑记录失败','url'=>session('url')));
            }

        }else{
            $re = M('Notice')->where(['id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display('Home/add_news');
        }
    }

    /**
     *@切换置顶
     */
    public function change_notice_top(){
        if(IS_POST){
            $id = I('id');
            $status = M('Notice')->where(['id'=>$id])->getField('is_top');
            $abs = 3 - $status;
            $arr = ['1','2'];
            $result = M('Notice')->where(['id'=>$id])->save(['is_top'=>$abs]);
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
     *切换公告状态
     */
    public function change_notice_state(){
        if(IS_POST){
            $id = I('id');
            $status = M('Notice')->where(['id'=>$id])->getField('state');
            $abs = 3 - $status;
            $arr = ['1','2'];
            $result = M('Notice')->where(['id'=>$id])->save(['state'=>$abs]);
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
     *发送公告
     */
    public function send_notice(){
        if(IS_POST){
            $id = I('id');
            $notice = M('Notice')->where(['id'=>$id])->find();
            if($notice['is_send'] == '2'){
                echo json_encode(array('status'=>'error','info'=>'该公告已经发送过了'));
                die;
            }
            $notice['object'] = explode(',',$notice['object']);
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['notice_id'] = $notice['id'];
            if(empty($data['object'])){
                $member = M('Member')->where(['is_del'=>'1'])->select();
            }else{
                $member = M('Member')->where(['is_del'=>'1','grade'=>['in',$notice['object']]])->select();
            }
            foreach($member as $k=>$v){
                $data['member_id'] = $v['member_id'];
                M('MemberNotice')->add($data);
            }
            M('Notice')->where(['id'=>$id])->save(['is_send'=>'2']);
            echo json_encode(array('status'=>'ok','info'=>'发送成功'));
        }
    }

    /**
     *@银行卡协议
     */
    public function bank_xieyi(){
        if(IS_POST){
            $data = $_POST;
            if(empty($data['content'])){
                echo json_encode(['status'=>'error','info'=>'协议内容不能为空']);
                die;
            }
            $result = M('Notice')->where(['id'=>'8'])->save($data);
            if($result){
                echo json_encode(['status'=>'ok','info'=>'编辑银行卡协议成功']);
            }else{
                echo json_encode(['status'=>'error','info'=>'编辑银行卡协议失败']);
            }
        }else{
            $re = M('Notice')->where(['id'=>8])->find();
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     *@功能模块
     */
    public function module(){
        $map['is_del'] = '1';
        $count = M('Module')->where($map)->count();
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $p = $this->getpage($count, $num);
        $list = M('Module')->where($map)
            ->limit($p->firstRow, $p->listRows)->order('is_tuijian desc,sort desc')->select();
        $this->assign('list',$list);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑论坛模块
     */
    public function edit_module(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Module')->auth());
        }else{
            $re = M('Module')->where(['module_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display("Home/add_module");
        }
    }

    /**
     *游记
     */
    public function article(){
        $list = M('Article')->order('uptime desc,intime desc')->select();
        $this->assign('list',$list);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加游记
     */
    public function add_article(){
        if(IS_POST) {
            echo json_encode(D('Article')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *编辑游记
     */
    public function edit_article(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('Article')->auth());
        }else{
            $article = M('Article')->where(['id'=>$id])->find();
            $this->assign(['article'=>$article]);
            $this->display('Home/add_article');
        }
    }

    /**
     *删除游记
     */
    public function del_article(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('Article')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@修改游记状态
     */
    public function change_article_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('Article')->where(['id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['下架状态','发布状态'];
            if($status == '1')      $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['status'] = $abs;
            $result = M('Article')->where(['id'=>$id])->save($data);
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
     *图片模糊
     */
    public function change_img(){
        $srcImg = 'Uploads/image/city/20161014/5800320bd06be.jpg';
        $savepath = 'Uploads/image/city/20161013';
        $savename = '3.png';

        $result = gaussian_blur($srcImg,$savepath,$savename,$blurFactor=3);
        echo $result;
    }

}