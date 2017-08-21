<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/2/23
 * Time: 15:39
 */

namespace Api\Controller;


use Think\Upload;

class TopicalController extends CommonController
{

    /**
     * @发表帖子
     */
    public function set_topical_post(){
        if(IS_POST){
            $member = checklogin();
//            $data = $_POST;
//            $data = json_encode($data);
//            file_put_contents('1.txt',$data);
//            die;
            $data['user_id'] = $member['user_id'];
//            $data['title'] = I('title');
//            if(empty($data))        error("标题不能为空");
//            if(strlen($data['title'])<4)        error("最少4个字节");
            $state = I('state');
            $data['module_id'] = I('module_id');
            if(empty($data['module_id']))       error("请选择论坛模块");
            $data['content'] = I('content');
            if($state == 1) {
                $config = [
                    'maxSize' => 300 * 3145728,
                    'rootPath' => './Uploads/image/topical/',
                    'savePath' => '',
                    'saveName' => ['uniqid', ''],
                    'exts' => ['png', 'jpg', 'jpeg', 'git', 'gif'],
                    'autoSub' => true,
                    'subName' => ['date', 'Ymd'],
                ];
                $uploader = new Upload($config);
                $info = $uploader->upload();
                $b = json_encode($info);
                file_put_contents('2.txt',$b);
                $imgs = array();
                if ($info) {
                    $info = array_values($info);
                    $a = json_encode($info);
                    file_put_contents('1.txt',$a);
                    foreach ($info as $k => $v) {
                        $img = '/Uploads/image/topical/' . date("Ymd", time()) . '/' . $v["savename"];
                        $array = getimagesize('.' . $img);
                        if ($array[0] > 200) {
                            $image = new \Think\Image();
                            $image->open('.' . $img);
                            $image->thumb(200, 200, \Think\Image::IMAGE_THUMB_SCALE)
                                ->save('./Uploads/image/topical/thumb/' . time() . '_' . $v["savename"]);
                            $thumb = '/Uploads/image/topical/thumb/' . time() . '_' . $v["savename"];
                            $arr2 = getimagesize('.' . $thumb);

                        } else {
                            $thumb = $img;
                            $arr2 = $array;
                        }
                        $imgs[$k]['img'] =  $img;
                        $imgs[$k]['width'] = (string)($array[0]);
                        $imgs[$k]['height'] = (string)($array[1]);
                        $thumbs[$k]['img'] = $thumb;
                        $thumbs[$k]['width'] = (string)($arr2[0]);
                        $thumbs[$k]['height'] = (string)($arr2[1]);
                    }
                } else {
                    error($uploader->getError());
                }
                $data['img'] = serialize($imgs);
                $data['thumb'] = serialize($thumbs);
            }
            if(empty($data['content']) && empty($data['img'])){
                error("参数错误");
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('TopicalPosts')->add($data);
            if($result){
                success("发布成功");
            }else{
                error("发布失败");
            }

        }
    }

    /**
     *@帖子的模块
     */
    public function topical_module(){
        $list = M('TopicalModule')->field("module_id,title,picture")
            ->where(['is_del'=>'1'])->order("is_tuijian desc,sort desc")->select();
        $this->url = C('IMG_PREFIX');
        foreach($list as $k=>$v){
            $list[$k]['picture'] = $this->url.$v['picture'];
        }
        success($list);
    }

    /**
     * @发现首页
     */
    public function index(){
        $member = checklogin();
        $banner = M('Banner')->field("b_id,b_img,url,b_type,title,value")
            ->where(['is_del'=>'1','status'=>'2','type'=>3])->order("sort asc")->select();
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
        $module = M('TopicalModule')->field('module_id,title,picture,is_tuijian')
                ->where(['is_del'=>'1'])->order("is_tuijian desc")->select();
        foreach($module as $k=>$v){
            $module[$k]['picture']      = $this->url.$v['picture'];
            $module[$k]['post_count']   = M('TopicalPosts')
                        ->where(['module_id'=>$v['module_id'],'intime'=>['gt',date("Y-m-d",time())]])->count();
        }

        $num = I('pagesize');
        $p = I('p');
        empty($p)   && $p = 1;
        $num    ?   $num    : $num = 10;
        $count = M('TopicalPosts')->alias('a')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->count();
        $page = ceil($count/$num);
        $list = M('TopicalPosts')->alias('a')
                ->field('b.user_id,b.username,b.img as head_img,b.sex,b.hx_username,b.alias,b.hx_password,
                a.title,a.post_id,a.thumb,a.img,a.zan,a.ping,a.content,a.img,a.intime')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->limit(($p-1)*$num,$num)->where(['a.is_del'=>'1'])
                ->order("a.intime desc")
                ->select();
        foreach($list as $k=>$v){
            $check = M('Shield')->where(['user_id'=>$member['user_id'],'user_id2'=>$v['user_id']])->find();
            if($check){
                unset($list[$k]);
            }else {
                //判断时间
                $list[$k]['date_value'] = translate_date($v['intime']);
                if (!empty($v['head_img'])) {
                    $list[$k]['head_img'] = $this->url . $v['head_img'];
                }
                if (!empty($v['img'])) {
                    $img = unserialize($v['img']);
                    foreach ($img as $key => $val) {
                        $img[$key]['img'] = $this->url . $val['img'];
                    }
                    $list[$k]['img'] = $img;
                } else {
                    $list[$k]['img'] = [];
                };

                if (!empty($v['thumb'])) {
                    $thumb = unserialize($v['thumb']);
                    foreach ($thumb as $key => $val) {
                        $thumb[$key]['img'] = $this->url . $val['img'];
                    }
                    $list[$k]['thumb'] = $thumb;
                } else {
                    $list[$k]['thumb'] = [];
                }

                //判断是否关注主播
                if (M('Follow')->where(['user_id2' => $v['user_id'], 'user_id' => $member['user_id']])->find()) {
                    $list[$k]['is_follow'] = '1';
                } else {
                        if ($member['user_id'] != $v['user_id']) {
                            $list[$k]['is_follow'] = '2';
                        } else {
                            $list[$k]['is_follow'] = '3';
                        }
                    }
                }
            $check = M('ZanPosts')->where(['type'=>'1','user_id'=>$member['user_id'],'post_id'=>$v['post_id']])->find();
            if($check){
                $list[$k]['is_zan'] = '1';
            }else{
                $list[$k]['is_zan'] = '2';
            }

        }
        $list = array_values($list);
        success(['banner'=>$banner,'module'=>$module,'list'=>$list,'page'=>$page]);
    }

    /**
     *@帖子分类
     */
    public function module_posts(){
        if(IS_POST){
            $member = checklogin();
            $p = I('p');
            empty($p)   && $p = 1;
            $num = I('pagesize');
            $num   ?  $num   :  $num = 10;
            $map['a.is_del'] = '1';
            !empty($_POST['module_id'])      &&  $map['a.module_id'] = I('module_id');
            $this->url = C('IMG_PREFIX');
            $count = M('TopicalPosts')->alias('a')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->where($map)->count();
            $page = ceil($count/$num);
            $list = M('TopicalPosts')->alias('a')
                ->field('b.user_id,b.username,b.img as head_img,b.sex,b.hx_username,b.alias,b.hx_password,
                a.title,a.post_id,a.thumb,a.img,a.zan,a.ping,a.content,a.img,a.intime')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->limit(($p-1)*$num,$num)->where(['a.is_del'=>'1'])
                ->where($map)->order("a.intime desc")->select();
            foreach($list as $k=>$v) {
                $check = M('Shield')->where(['user_id'=>$member['user_id'],'user_id2'=>$v['user_id']])->find();
                if($check){
                    unset($list[$k]);
                }else{
                if (!empty($v['head_img'])) {
                    $list[$k]['head_img'] = $this->url . $v['head_img'];
                }
                if (!empty($v['img'])) {
                    $img = unserialize($v['img']);
                    foreach ($img as $key => $val) {
                        $img[$key]['img'] = $this->url . $val['img'];
                    }
                    $list[$k]['img'] = $img;
                } else {
                    $list[$k]['img'] = [];
                };
                if (!empty($v['thumb'])) {
                    $thumb = unserialize($v['thumb']);
                    foreach ($thumb as $key => $val) {
                        $thumb[$key]['img'] = $this->url . $val['img'];
                    }
                    $list[$k]['thumb'] = $thumb;
                } else {
                    $list[$k]['thumb'] = [];
                }

                //判断是否关注主播
                if (M('Follow')->where(['user_id2' => $v['user_id'], 'user_id' => $member['user_id']])->find()) {
                    $list[$k]['is_follow'] = '1';
                } else {
                    if ($member['user_id'] != $v['user_id']) {
                        $list[$k]['is_follow'] = '2';
                    } else {
                        $list[$k]['is_follow'] = '3';
                    }
                }
                $check = M('ZanPosts')->where(['type'=>'1','user_id'=>$member['user_id'],'post_id'=>$v['post_id']])->find();
                if($check){
                    $list[$k]['is_zan'] = '1';
                }else{
                    $list[$k]['is_zan'] = '2';
                }

                $list[$k]['date_value'] = translate_date($v['intime']);
            }
            }
            $list = array_values($list);
            success(['list'=>$list,'page'=>$page]);

            }
    }

    /**
     *@帖子点赞和取消点赞
     */
    public function zan_posts(){
        if(IS_POST){
            $member = checklogin();
            $post_id = I('post_id');
            if(empty($post_id))         error("参数错误");
            $check = M('ZanPosts')->where(['user_id'=>$member['user_id'],'type'=>1,'post_id'=>$post_id])->find();
            if($check){
                $result = M('ZanPosts')->where(['id'=>$check['id']])->delete();
                $action = '2';
                M('TopicalPosts')->where(['post_id'=>$post_id])->setDec("zan");
            }else{
                $data['user_id'] = $member['user_id'];
                $data['post_id'] = $post_id;
                $data['type'] = '1';
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('ZanPosts')->add($data);
                $action = '1';
                M('TopicalPosts')->where(['post_id'=>$post_id])->setInc("zan");
            }
            if($result){
                success($action);
            }else{
                error("操作失败");
            }
        }
    }

    /**
     * @帖子评论
     * @param post_id
     * @param response_id 评论id
     */
    public function comment_posts(){
        if(IS_POST){
            $member = checklogin();
            $data = $_POST;
            if(empty($data['post_id']))         error("参数错误");
            $data['user_id']    =   $member['user_id'];
            if(empty($data['content']))         error("评论内容不能为空");
            if(!empty($data['response_id'])){
                $check = M('CommentPosts')->where(['comment_id'=>$data['response_id']])->find();
                if(!$check)                     error("回复的帖子没有找到");
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('CommentPosts')->add($data);
            if($result){
                M('TopicalPosts')->where(['post_id'=>$data['post_id']])->setInc("ping");
                success("评论成功");
            }else{
                error("评论失败");
            }
        }
    }

    /**
     *@帖子评论点赞
     */
    public function zan_comment(){
        if(IS_POST){
            $member = checklogin();
            $comment_id = I('comment_id');
            if(empty($comment_id))         error("参数错误");
            $check = M('ZanPost')->where(['user_id'=>$member['user_id'],'comment_id'=>$comment_id])->find();
            if($check){
                $result = M('ZanPost')->where(['id'=>$check['id']])->delete();
                $action = 1;
                M('CommentPost')->where(['comment_id'=>$comment_id])->setDec("zan");
            }else{
                $data['user_id'] = $member['user_id'];
                $data['comment_id'] = $comment_id;
                $data['intime'] = date("Y-m-d H:i:s",time());
                $data['type'] = 2;
                $result = M('ZanPost')->add($data);
                $action = 2;
                M('CommentPost')->where(['comment_id'=>$comment_id])->setInc("zan");
            }
            if($result){
                success($action);
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@ 发帖详情
     */
    public function posts_view()
    {
        if (IS_POST) {
            $member = checklogin();
            $post_id = I('post_id');
            $this->url = C('IMG_PREFIX');
            $re = M('TopicalPosts')->alias('a')
                ->field('b.user_id,b.username,b.img as head_img,b.sex,b.hx_username,b.alias,b.hx_password,
                a.title,a.post_id,a.thumb,a.img,a.zan,a.ping,a.content,a.img,a.intime')
                ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
                ->where(['a.post_id' => $post_id])
                ->find();
            if (!empty($re)) {
                if (!empty($re['head_img'])) {
                    $re['head_img'] = $this->url . $re['head_img'];
                };
                if(!empty($re['img'])){
                    $img = unserialize($re['img']);
                    foreach($img as $key=>$val){
                        $img[$key]['img'] = $this->url.$val['img'];
                    }
                    $re['img'] = $img;
                }else{
                    $re['img'] = [];
                };
                if(!empty($re['thumb'])){
                    $thumb = unserialize($re['thumb']);
                    foreach($thumb as $key=>$val){
                        $thumb[$key]['img'] = $this->url.$val['img'];
                    }
                    $re['thumb'] = $thumb;

                }else{
                    $re['thumb'] = [];
                }

                //判断是否关注主播
                if (M('Follow')->where(['user_id2' => $re['user_id'], 'user_id' => $member['user_id']])->find()) {
                    $re['is_follow'] = '1';
                } else {
                    if ($member['user_id'] != $re['user_id']) {
                        $re['is_follow'] = '2';
                    } else {
                        $re['is_follow'] = '3';
                    }
                }
                $check = M('ZanPosts')->where(['type'=>'1','user_id'=>$member['user_id'],'post_id'=>$re['post_id']])->find();
                if($check){
                    $re['is_zan'] = '1';
                }else{
                    $re['is_zan'] = '2';
                }
                $re['date_value'] = translate_date($re['intime']);
                $re['share_url'] = $this->url."/api.php?m=Api&c=Topical&a=share_url&post_id=".base64_encode($re['post_id']);

                //帖子评论
                $list = M('CommentPosts')->alias('a')
                    ->field('a.content,a.comment_id,a.user_id,a.intime,a.zan,b.username,b.sex,b.img')
                    ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                    ->where(['a.post_id' => $post_id, 'a.response_id' => '0'])
                    ->order("a.intime desc")->select();
                foreach ($list as $k => $v) {
                    $response = M('CommentPosts')->alias('a')
                        ->field('a.content,a.comment_id,a.user_id,a.intime,a.zan,b.username,b.img,b.sex')
                        ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                        ->where(['a.response_id' => $v['comment_id']])
                        ->order("a.intime asc")->select();
                    foreach ($response as $key => $val) {
                        $response[$key]['img'] = $this->url . $val['img'];
                        $response[$key]['date_value'] = translate_date($val['intime']);

                    }
                    $list[$k]['img'] = $this->url . $v['img'];
                    $list[$k]['response'] = $response;;

                    //判断时间
                    $list[$k]['date_value'] = translate_date($v['intime']);

                }
                $re['comment'] = $list;
            }else{
               $re = '';
            }
            success($re);
        }

    }


    /**
     * @分享页面
     */
    public function share_url(){
        $post_id = I('post_id');
        $re = M('TopicalPosts')->alias('a')
            ->field('b.user_id,b.username,b.img as head_img,b.sex,b.hx_username,b.alias,b.hx_password,
                a.title,a.post_id,a.thumb,a.img,a.zan,a.ping,a.content,a.img,a.intime')
            ->join("LEFT JOIN __USER__ b on a.user_id = b.user_id")
            ->where(['a.post_id' => $post_id])
            ->find();
        $this->url = C('IMG_PREFIX');
        if (!empty($re)) {
            if (!empty($re['head_img'])) {
                $re['head_img'] = $this->url . $re['head_img'];
            };
            if(!empty($re['img'])){
                $img = unserialize($re['img']);
                foreach($img as $key=>$val){
                    $img[$key]['img'] = $this->url.$val['img'];
                }
                $re['img'] = $img;
            }else{
                $re['img'] = [];
            };
            if(!empty($re['thumb'])){
                $thumb = unserialize($re['thumb']);
                foreach($thumb as $key=>$val){
                    $thumb[$key]['img'] = $this->url.$val['img'];
                }
                $re['thumb'] = $thumb;

            }else{
                $re['thumb'] = [];
            }

            //判断是否关注主播
            if (M('Follow')->where(['user_id2' => $re['user_id'], 'user_id' => $member['user_id']])->find()) {
                $re['is_follow'] = 1;
            } else {
                if ($member['user_id'] != $re['user_id']) {
                    $re['is_follow'] = 2;
                } else {
                    $re['is_follow'] = 3;
                }
            }
            $re['date_value'] = translate_date($re['intime']);
            $re['share_url'] = $this->url."api.php?m=Api&c=Topical&a=share_url&post_id=".base64_encode($re['post_id']);

            //帖子评论
            $list = M('CommentPosts')->alias('a')
                ->field('a.content,a.comment_id,a.user_id,a.intime,a.zan,b.username,b.sex,b.img')
                ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                ->where(['a.post_id' => $post_id, 'a.response_id' => '0'])
                ->order("a.intime desc")->select();
            foreach ($list as $k => $v) {
                $response = M('CommentPosts')->alias('a')
                    ->field('a.content,a.comment_id,a.user_id,a.intime,a.zan,b.username,b.img,b.sex')
                    ->join("INNER JOIN __USER__ b on a.user_id = b.user_id")
                    ->where(['a.response_id' => $v['comment_id']])
                    ->order("a.intime asc")->select();
                foreach ($response as $key => $val) {
                    $response[$key]['img'] = $this->url . $val['img'];
                    $response[$key]['date_value'] = translate_date($val['intime']);

                }
                $list[$k]['img'] = $this->url . $v['img'];
                $list[$k]['response'] = $response;;

                //判断时间
                $list[$k]['date_value'] = translate_date($v['intime']);

            }
            $re['comment'] = $list;
        }else{
            $re = '';
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')||strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){
            $url = 'https://itunes.apple.com/cn/app/幽秘/id1187582976?mt=8';
        }else if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android')){
            $url = 'http://sj.qq.com/myapp/detail.htm?apkName=tts.project.livek';
        }
        $this->assign('url',$url);
        $this->assign(['re'=>$re]);
        $this->display();
    }

    /**
     *@举报帖子
     */
    public function set_topical_report(){
        if(IS_POST){
            $member                 = checklogin();
            $post_id                = I('post_id');
            if(empty($post_id)){
                error("举报的帖子不能为空");
            }
            $data['content']        = I('content');
            $data['user_id']        = $member['user_id'];
            $data['response_id']    = $post_id;
            $data['intime']         = time();
            $data['state']          = 1;
            $result = M('Report')->add($data);
            if($result){
                success("举报成功");
            }else{
                error("举报失败");
            }
        }
    }

    /**
     *@举报评论
     */
    public function set_comment_report(){
        if(IS_POST){
            $member                 = checklogin();
            $comment_id                = I('comment_id');
            if(empty($comment_id)){
                error("举报的评论不能为空");
            }
            $data['content']        = I('content');
            $data['user_id']        = $member['user_id'];
            $data['response_id']    = $comment_id;
            $data['intime']         = time();
            $data['state']          = 2;
            $result = M('Report')->add($data);
            if($result){
                success("举报成功");
            }else{
                error("举报失败");
            }
        }
    }


}
