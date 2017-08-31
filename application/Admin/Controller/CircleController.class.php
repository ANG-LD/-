<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/14
 * Time: 9:50
 */

namespace Admin\Controller;
class CircleController extends BaseController
{
    /**
     *资讯
     */
    public function index(){
        !empty($_GET['title'])  &&  $map['title'] = ['like','%'.$_GET['title'].'%'];
        $p = I('p');
        empty($p) && $p = 1;
        $num = I('num');
        $this->assign('nus',$num);
        $count = M('Article')->where($map)->count();
        $p = $this->getpage($count,$num);
        $list = M('Article')->where($map)->limit($p->firstRow,$p->listRows)
            ->order('uptime desc,intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加资讯
     */
    public function add_article(){
        if(IS_POST) {
            echo json_encode(D('Article')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *编辑资讯
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
     *删除资讯
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
     *@修改资讯状态
     */
    public function change_article_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('Article')->where(['id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['默认状态','头条状态'];
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
     *投票
     */
    public function pull(){
        !empty($_GET['title'])  &&  $map['title'] = ['like','%'.$_GET['title'].'%'];
        $p = I('p');
        empty($p) && $p = 1;
        $num = I('num');
        $this->assign('nus',$num);
        $count = M('TopicalPull')->where($map)->count();
        $p = $this->getpage($count,$num);
        $list = M('TopicalPull')->where($map)->limit($p->firstRow,$p->listRows)
            ->order('uptime desc,intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *添加资讯
     */
    public function add_pull(){
        if(IS_POST) {
            echo json_encode(D('TopicalPull')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *编辑资讯
     */
    public function edit_pull(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('TopicalPull')->auth());
        }else{
            $article = M('TopicalPull')->where(['id'=>$id])->find();
            $this->assign(['article'=>$article]);
            $this->display('Circle/add_pull');
        }
    }

    /**
     *删除资讯
     */
    public function del_pull(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('TopicalPull')->where($data)->delete();
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     *@修改资讯状态
     */
    public function change_pull_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('TopicalPull')->where(['id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['下架状态','发布状态'];
            if($status == '1')      $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['status'] = $abs;
            $result = M('TopicalPull')->where(['id'=>$id])->save($data);
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
     *@投票详情
     */
    public function pull_detail(){
        $id = I('id');
        $list = M('TopicalPullValue')->where(['pull_id'=>$id])->select();
        $this->assign(['list'=>$list]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑投票选项
     */
    public function edit_pull_value(){
        $id = I('id');
        if(IS_POST){
            $data['name'] = I('name');
            if(empty($data['name'])){
                echo json_encode(['status'=>'error','info'=>'名称不能为空','class'=>'name']);
                die;
            }
            $data['pull_id'] = I('pull_id');
            if(empty($data['pull_id'])){
                echo json_encode(['status'=>'error','info'=>'对应资讯不能为空','class'=>'']);
                die;
            }
            if(empty($id)){
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('TopicalPullValue')->add($data);
                $action = '新增';
            }else{
                $data['uptime'] = date("Y-m-d H:i:s",time());
                $result = M('TopicalPullValue')->where(['id'=>$id])->save($data);
                $action = '编辑';
            }
            if($result){
                echo json_encode(array('status'=>'ok','info'=>$action.'记录成功','url'=>session('url')));
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>$action.'记录失败'));
                die;
            }

        }else{
            $re = M('TopicalPullValue')->where(['id'=>$id])->find();
            echo json_encode(array('status'=>'ok','info'=>$re));
            die;
        }
    }

    /**
     *@视频管理
     */
    public function video(){
        !empty($_GET['nickname'])  &&  $map['a.title|b.nickname'] = ['like','%'.$_GET['nickname'].'%'];
        $p = I('p');
        empty($p) && $p = 1;
        $map['a.is_del'] = 1;
        $num = I('num');
        $this->assign('nus',$num);
        $count = M('TopicalVideo')->alias('a')
            ->join("LEFT JOIN __MEMBER__ b on a.mid = b.member_id")
            ->where($map)->count();
        $p = $this->getpage($count,$num);
        $list = M('TopicalVideo')->alias('a')
            ->field("a.id,a.title,a.video,a.img,a.zan,a.browse,b.nickname,b.phone")
            ->join("LEFT JOIN __MEMBER__ b on a.mid = b.member_id")
            ->where($map)->limit($p->firstRow,$p->listRows)
            ->order('a.uptime desc,a.intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    public function del_video(){
        $id = I('ids');
        $data['id'] = array('in',$id);
        $result = M('TopicalVideo')->where($data)->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除记录失败!']);
        }
    }

    /**
     * @论坛模块
     */
    public function module(){
        $map['is_del'] = '1';
        $count = M('TopicalModule')->where($map)->count();
        $num = I('num');
        if (empty($num)) {
            $num = 10;
        }
        $p = $this->getpage($count, $num);
        $list = M('TopicalModule')->where($map)
            ->limit($p->firstRow, $p->listRows)->order('is_tuijian desc,status desc,sort desc')->select();
        $this->assign(['list'=>$list,'count'=>$count,'page'=>$p->show()]);
        $url =$_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@添加论坛模块
     */
    public function add_module(){
        if(IS_POST){
            echo json_encode(D('TopicalModule')->auth());
        }else{
            $this->display();
        }
    }

    /**
     *@编辑论坛模块
     */
    public function edit_module(){
        $id = I('id');
        if(IS_POST){
            echo json_encode(D('TopicalModule')->auth());
        }else{
            $re = M('TopicalModule')->where(['module_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display("Circle/add_module");
        }
    }

    /**
     *@改变模块的推荐信息
     */
    public function change_module_tuijian(){
        if(IS_POST){
            $id = I('id');
            $status = M('TopicalModule')->where(['module_id'=>$id])->getField('is_tuijian');
            $abs = 3 - $status;
            $arr = ['默认','推荐'];
            $result = M('TopicalModule')->where(['module_id'=>$id])->save(['is_tuijian'=>$abs]);
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
     *@改变模块的上下架信息
     */
    public function change_module_status(){
        if(IS_POST){
            $id = I('id');
            $status = M('TopicalModule')->where(['module_id'=>$id])->getField('status');
            $abs = 3 - $status;
            $arr = ['1','2'];
            $result = M('TopicalModule')->where(['module_id'=>$id])->save(['status'=>$abs]);
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
     * @删除模块
     *
     */
    public function del_module(){
        if(IS_POST){
            $id = I('ids');
            $data['module_id'] = array('in',$id);
            $result = M('TopicalModule')->where($data)->save(['is_del'=>'2']);
            if($result){
                echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
            }else{
                echo json_encode(['status'=>"error",'info'=>'删除失败!']);
            }
        }
    }

    /**
     *@帖子列表
     */
    public function posts_list(){
        $map=[];
        !empty($_GET['name']) && $map['a.title|b.username|c.title'] = ['like','%'.I('name').'%'];
        if (empty($num)) {
            $num = 10;
        }
        $map = ['a.is_del'=>'1'];
        $this->assign('nus', $num);
        $count = M("TopicalPosts")->alias('a')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->join("LEFT JOIN __TOPICAL_MODULE__ c on a.module_id = c.module_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $p = $this->getpage($count, $num);
        $list =  M("TopicalPosts")->alias('a')
            ->field("a.post_id,a.title,a.intime,a.zan,a.ping,a.content,b.username,b.phone,b.img,c.title as tags")
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->join("INNER JOIN __TOPICAL_MODULE__ c on a.module_id = c.module_id")
            ->where($map)->limit($p->firstRow, $p->listRows)->order('a.intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show()]);
        $url = $_SERVER['REQUEST_URI'];
        session('url', $url);
        $this->display();
    }

    public function post_view(){
        $id = I('id');
        if(IS_POST){
            $imgs = I('imgs');
            $width = I('width');
            $height = I('height');

            $t_imgs = I('t_imgs');
            $t_width = I('t_width');
            $t_height = I('t_height');

            $arr = array();
            foreach($imgs as $k => $v){
                $arr[$k]['img'] = $v;
                $arr[$k]['width'] = $width[$k];
                $arr[$k]['height'] = $height[$k];
            }
            $arr2 = array();
            foreach($t_imgs as $k => $v){
                $arr2[$k]['img'] = $v;
                $arr2[$k]['width'] = $t_width[$k];
                $arr2[$k]['height'] = $t_height[$k];
            }
            $data['img'] = serialize($arr);
            $data['thumb'] = serialize($arr2);
            $data['content'] = I('content');
            $data['title'] = I('title');
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $result = M('TopicalPosts')->where(['post_id'=>$id])->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑帖子成功','url'=>session('url')));
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑帖子失败'));
            }
        }else{
            $re = M('TopicalPosts')->where(['post_id'=>$id])->find();
            if(!empty($re['thumb'])){
                $re['thumb'] = unserialize($re['thumb']);
            }
            if(!empty($re['img'])){
                $re['img'] = unserialize($re['img']);
            }
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    /**
     *@举报帖子列表
     */
    public function report_list(){
        $map=[];
        !empty($_GET['title']) && $map['a.title|b.username'] = ['like','%'.I('name').'%'];
        if (empty($num)) {
            $num = 10;
        }
        $map = ['a.state'=>'1'];
        $map = ['b.is_del'=>'1'];
        $this->assign('nus', $num);
        $count = M("Report")->alias('a')
            ->join("RIGHT JOIN __TOPICAL_POSTS__ b on a.response_id = b.post_id")
            ->join("LEFT JOIN __USER__ c on a.user_id = c.user_id")
            ->where($map)->count(); // 查询满足要求的总记录数
        $p = $this->getpage($count, $num);
        $list =  M("Report")->alias('a')
            ->field("b.post_id,b.title,a.content,a.intime,c.username,c.phone,c.img")
            ->join("RIGHT JOIN __TOPICAL_POSTS__ b on a.response_id = b.post_id")
            ->join("LEFT JOIN __USER__ c on a.user_id = c.user_id")
            ->where($map)->limit($p->firstRow, $p->listRows)->order('a.intime desc')->select();
        $this->assign(['list'=>$list,'page'=>$p->show()]);
        $url = $_SERVER['REQUEST_URI'];
        session('url', $url);
        $this->display();
    }

    /**
     *@删除帖子
     */
    public function del_post(){
        $id = I('post_id');
        $result = M('TopicalPosts')->where(['post_id'=>$id])->save(['is_del'=>'2']);
        if($result){
            echo json_encode(['status'=>"ok",'info'=>'删除记录成功!','url'=>session('url')]);
        }else{
            echo json_encode(['status'=>"error",'info'=>'删除失败!']);
        }
    }

}