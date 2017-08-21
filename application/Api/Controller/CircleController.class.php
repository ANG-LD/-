<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/13
 * Time: 17:08
 */

namespace Api\Controller;

use Think\Upload;

class CircleController extends CommonController
{
    /**
     * @发表帖子
     */
    public function set_topical_post(){
        if(IS_POST){
            $member = checklogin();
            $data['mid'] = $member['member_id'];
            $data['title'] = I('title');
//            if(empty($data))        error("标题不能为空");
//            if(strlen($data['title'])<4)        error("最少4个字节");
            $state = I('state');
            $data['content'] = I('content');
            $data['img'] = I('img');
            $data['thumb'] = I('thumb');
//            if($state == 1) {
//                $config = [
//                    'maxSize' => 300 * 3145728,
//                    'rootPath' => './Uploads/image/topical/',
//                    'savePath' => '',
//                    'saveName' => ['uniqid', ''],
//                    'exts' => ['png', 'jpg', 'jpeg', 'git', 'gif'],
//                    'autoSub' => true,
//                    'subName' => ['date', 'Ymd'],
//                ];
//                $uploader = new Upload($config);
//                $info = $uploader->upload();
//                $imgs = array();
//                $fix_img = C('IMG_PREFIX');
//                if ($info) {
//                    foreach ($info as $k => $v) {
//                        $img = '/Uploads/image/topical/' . date("Ymd", time()) . '/' . $v["savename"];
//                        $array = getimagesize('.' . $img);
//                        if ($array[0] > 200) {
//                            $image = new \Think\Image();
//                            $image->open('.' . $img);
//                            $image->thumb(200, 200, \Think\Image::IMAGE_THUMB_SCALE)
//                                ->save('./Uploads/image/topical/thumb/' . time() . '_' . $v["savename"]);
//                            $thumb = '/Uploads/image/topical/thumb/' . time() . '_' . $v["savename"];
//                            $arr2 = getimagesize('.' . $thumb);
//
//                        } else {
//                            $thumb = $img;
//                            $arr2 = $array;
//                        }
//                        $image['mid'] = $member['member_id'];
//                        $image['img'] = $img;
//                        $image['thumb'] = $thumb;
//                        $image['intime'] = date("Y-m-d H:i:s",time());
//                        M('Images')->add($image);
//                        $imgs[$k]['img'] =  $img;
//                        $imgs[$k]['width'] = (string)($array[0]);
//                        $imgs[$k]['height'] = (string)($array[1]);
//                        $thumbs[$k]['img'] = $thumb;
//                        $thumbs[$k]['width'] = (string)($arr2[0]);
//                        $thumbs[$k]['height'] = (string)($arr2[1]);
//                    }
//                } else {
//                    error($uploader->getError());
//                }
//                $data['img'] = serialize($imgs);
//                $data['thumb'] = serialize($thumbs);
//            }
            if(empty($data['content']) && empty($data['img'])){
                error("参数错误");
            }
            if(!empty($data['img'])){
                $img = explode(',',$data['img']);
                $thumb = explode(',',$data['thumb']);
                foreach($img as $k=>$v){
                    $image['mid'] = $member['member_id'];
                    $image['img'] = $v;
                    $image['thumb'] = $thumb[$k];
                    $image['intime'] = date("Y-m-d H:i:s",time());
                    M('Images')->add($image);
                        $array = getimagesize('.' . $v);
                        $imgs[$k]['img'] =  $v;
                        $imgs[$k]['width'] = (string)($array[0]);
                        $imgs[$k]['height'] = (string)($array[1]);

                        $thumbs[$k]['img'] = $thumb[$k];
                        $arr2 = getimagesize('.' . $thumb[$k]);
                        $thumbs[$k]['width'] = (string)($arr2[0]);
                        $thumbs[$k]['height'] = (string)($arr2[1]);
                }
                $data['img'] = serialize($imgs);
                $data['thumb'] = serialize($thumbs);
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
     * @帖子评论
     */
    public function comment_post(){
        if(IS_POST){
            $member = checklogin();
            $data = $_POST;
            if(empty($data['post_id']))         error("参数错误");
            $data['mid']    =   $member['member_id'];
            if(empty($data['content']))         error("评论内容不能为空");
            if(!empty($data['response_id'])){
                $check = M('CommentPost')->where(['comment_id'=>$data['response_id']])->find();
                if(!$check)                     error("回复的帖子没有找到");
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['type'] = 1;
            $result = M('CommentPost')->add($data);
            if($result){
                M('TopicalPosts')->where(['post_id'=>$data['post_id']])->setInc("ping");
                success("评论成功");
            }else{
                error("评论失败");
            }
        }
    }

    /**
     *@关注和取消关注
     */
    public function follow_user(){
        if(IS_POST){
            $member = checklogin();
            $data['user_id'] = $member['member_id'];
            $data['user_id2'] = I('user_id2');
            if(empty($data['user_id2']))        error("被关注者不能为空");
            if($data['user_id'] == $data['user_id2'])   error("参数错误");
            $check = M('Follow')->where($data)->find();
            if($check){
                $result = M('Follow')->where(['follow_id'=>$check['follow_id']])->delete();
                $code = 2;
            }else{
                $data['intime'] = date("Y-m-d H:i:s",time());
                $result = M('Follow')->add($data);
                $code = 1;
            }
            if($result){
                success($code);
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@上传视频
     */
    public function add_video(){
        if(IS_POST){
            $member = checklogin();
            $data = $_POST;
            if(empty($data['title']))       error("视频标题不能为空");
//            $config = [
//                'maxSize'	=> 3145728,
//                'rootPath'	=> './',
//                'savePath'	=> '/Uploads/image/comment/'.$_REQUEST[''],
//                'saveName'	=> ['uniqid',''],
//                'exts'		=> ['jpg', 'gif', 'png', 'jpeg'],
//                'autoSub'	=> true,
//                'subName'	=> ['date','Ymd'],
//            ];
//            $uploader = new Upload($config);
//            $info = $uploader->upload();
//            if ($info){
//                foreach($info as $k=>$v){
//                    $url[] = $v['savepath'].$v['savename'];
//                }
//            }else{
//                error($uploader->getError());
//            }
//            $data['img'] = implode(',',$url);
            if(empty($data['img']))         error("视频封面不能为空");
            if(empty($data['video']))       error("视频链接不能为空");
            $data['mid'] = $member['member_id'];
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('TopicalVideo')->add($data);
            if($result){
                success("添加成功");
            }else{
                error("添加失败");
            }
        }
    }

    /**
     *@视频列表
     *@param sate 0广场；1关注；2自己
     */
    public function square_video(){
        if(IS_POST){
            $member = checklogin();
            $map['a.is_del'] = 1;
            $p = I('p');
            empty($p)       && $p = 1;
            $num = 10;
            $state = I('state');
            if($state =='2'){
                $map['a.mid'] = $member['member_id'];
            }
            if($state == '1'){
                $follow = M('Follow')->where(['user_id'=>$member['member_id']])->select();
                if(!empty($follow)){
                    foreach($follow as $k=>$v){
                        $where[] = $v['user_id2'];
                    }
                }else{
                    success(['page'=>0,'list'=>[]]);
                }
                $map['a.mid'] = ['in',$where];
            }
            $count = M('TopicalVideo')->alias('a')
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->count();
            $page = ceil($count/$num);
            $list = M('TopicalVideo')->alias('a')
                ->field("a.id,a.video,a.img,a.title,a.zan,a.browse,a.intime,b.nickname,b.img as head_img,b.member_id")
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->limit(($p-1)*$num,$num)->order("a.intime desc")->select();
            if(!empty($list)){
                foreach($list as $k=>$v){
                    $list[$k]['data_value'] = translate_date($v['intime']);
                }
            }
            success(['page'=>$page,'list'=>$list]);
        }
    }

    /**
     *@视频详情
     */
    public function video_view(){
        if(IS_POST){
            $member = checklogin();
            $id = I('id');
            M('TopicalVideo')->where(['id'=>$id])->setInc('browse');
            $re = M('TopicalVideo')->alias('a')
                ->field("a.id,a.video,a.img,a.title,a.zan,a.browse,a.intime,b.nickname,b.img as head_img,b.member_id")
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where(['a.id'=>$id])->find();
            if($member['member_id'] == $re['member_id']){
                $re['is_follow'] = 3;
            }else{
                //查询是否关注发帖人
                $check = M('Follow')->where(['user_id'=>$member['member_id'],'user_id2'=>$re['member_id']])->find();
                if($check){
                    $re['is_follow'] = 1;
                }else{
                    $re['is_follow'] = 2;
                }
            }
            //查询是否点过赞
            $check = M('ZanPost')->where(['type'=>'2','user_id'=>$member['member_id'],'post_id'=>$re['id']])->find();
            if($check){
                $re['is_zan'] = 1;
            }else{
                $re['is_zan'] = 2;
            }

            $re['date_value'] = translate_date($re['intime']);

            $comment = M('CommentPost')->alias('a')
                ->field('a.comment_id,a.content,a.intime,b.nickname,b.img as head_img,b.member_id')
                ->join("LEFT JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where(['a.type'=>'2','a.post_id'=>$re['id']])
                ->order("a.intime desc")->select();
            if(!empty($comment)){
                foreach($comment as $k=>$v){
                    $comment[$k]['date_value'] = translate_date($v['intime']);
                }
            }

            $re['comment'] = $comment;
            success($re);
        }
    }

    /**
     *@视频评论
     */
    public function comment_video(){
        if(IS_POST){
            $member = checklogin();
            $data = $_POST;
            if(empty($data['id']))         error("参数错误");
            $data['mid']    =   $member['member_id'];
            if(empty($data['content']))         error("评论内容不能为空");
            if(!empty($data['response_id'])){
                $check = M('CommentPost')->where(['comment_id'=>$data['response_id']])->find();
                if(!$check)                     error("回复的帖子没有找到");
            }
            $data['intime'] = date("Y-m-d H:i:s",time());
            $data['type'] = 2;
            $data['post_id'] = $data['id'];
            $result = M('CommentPost')->add($data);
            if($result){
                M('TopicalVideo')->where(['id'=>$data['id']])->setInc("ping");
                success("评论成功");
            }else{
                error("评论失败");
            }
        }
   }

    /**
     *@视频点赞与取消点赞
     */
    public function zan_post(){
        if(IS_POST){
            $member = checklogin();
            $data   =   $_POST;
            if(empty($data['type']))    error("参数错误");
            if(empty($data['post_id'])) error("参数错误");
            $map['user_id'] = $member['member_id'];
            $map['post_id'] = $data['post_id'];
            $map['type'] = $data['type'];
            $check = M('ZanPost')->where($map)->find();
            if($check){
                $result = M('ZanPost')->where(['id'=>$check['id']])->delete();
                $code = 2;
            }else{
                $map['intime'] = date("Y-m-d H:i:s",time());
                $result = M('ZanPost')->add($map);
                $code = 1;
            }
            $arr = ['member_id'=>$member['member_Id'],'nickname'=>$member['nickname']];
            if($result){
                switch($map['type']){
                    case 1 :
                        if($code == 1){
                            M('TopicalPosts')->where(['post_id'=>$map['post_id']])->setInc('zan');
                        }else{
                            M('TopicalPosts')->where(['post_id'=>$map['post_id']])->setDec('zan');
                        }
                        $number = M('TopicalPosts')->where(['post_id'=>$map['post_id']])->getField('zan');
                    break;
                    case 2 :
                        if($code == 1){
                            M('TopicalVideo')->where(['id'=>$map['post_id']])->setInc('zan');
                        }else{
                            M('TopicalVideo')->where(['id'=>$map['post_id']])->setDec('zan');
                        }
                        $number = M('TopicalVideo')->where(['id'=>$map['post_id']])->getField('zan');
                    break;
                    case 3 :
                        if($code == 1){
                            M('Comment')->where(['comment_id'=>$map['post_id']])->setInc('zan_number');
                        }else{
                            M('Comment')->where(['comment_id'=>$map['post_id']])->setDec('zan_number');
                        }
                        $number = M('Comment')->where(['comment_id'=>$map['post_id']])->getField('zan_number');
                    break;

                }
                if($code == 1){
                    $list = M('ZanPost')->alias('a')
                        ->field('b.member_id,b.nickname')
                        ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                        ->where(['a.type'=>$map['type'],'a.post_id'=>$data['post_id']])
                        ->order("a.intime desc")->limit(3)->select();
                    foreach($list as $k=>$v){
                        $m[] = $v['member_id'];
                    }
                    if(!in_array($member['member_id'],$m)){
                        unset($list[3]);
                        array_unshift($list,$arr);
                    }
                }else{
                    $list = M('ZanPost')->alias('a')
                        ->field('b.member_id,b.nickname')
                        ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                        ->where(['a.type'=>$map['type'],'a.post_id'=>$data['post_id']])
                        ->order("a.intime desc")->limit(3)->select();
                }
                success(['code'=>$code,'number'=>$number,'list'=>$list]);
            }else{
                error("操作失败");
            }
        }
    }

    /**
     *@点赞的所有用户
     */
    public function zan_user(){
        if(IS_POST){
            $map['a.post_id'] = I('post_id');
            $map['a.type'] = I('type');
            $p = I('p');
            empty($p)   && $p = 1;
            $num = 20;
            if(empty($map['a.post_id']) || empty($map['a.type']))     error("参数错误");
            $count = M('ZanPost')->alias('a')
                ->join("INNER JOIN __MEMBER__ b on a.user_id = b.member_id")
                ->where($map)->count();
            $page =ceil($count/$num);
            $list = M('ZanPost')->alias('a')
                ->field("b.nickname,b.img,b.member_id,a.intime")
                ->join("INNER JOIN __MEMBER__ b on a.user_id = b.member_id")
                ->order("a.intime desc")->where($map)
                ->limit(($p-1)*$num,$num)->select();
            foreach($list as $k=>$v){
                $list[$k]['intime'] = date("Y-m-d H:i",strtotime($v['intime']));
            }
            success(['page'=>$page,'list'=>$list]);
        }
    }

    /**
     *@社区广场
     */
    public function square_posts(){
        if(IS_POST){
            $member = checklogin();
            $p = I('p');
            $map['a.auth'] = 1;
            $map['a.is_del'] = 1;
            $type = I('type');
            if($type == 1){
//                $map['a.auth'] = 2;
                $follow = M('Follow')->where(['user_id'=>$member['member_id']])->select();
                if(empty($follow)){
                    success(['page'=>0,'list'=>[]]);
                }
                foreach($follow as $k=>$v){
                    $arr[] = $v['user_id2'];
                }
                $map['a.mid'] = ['in',$arr];
            }
            empty($p)   &&  $p = 1;
            $count = M('TopicalPosts')->alias('a')
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->count();
            $num = 10;
            $page = ceil($count / $num);
            $list = M('TopicalPosts')->alias('a')
                ->field('a.post_id,a.content,a.img,a.zan,a.ping,a.intime,a.thumb,b.member_id,b.nickname,b.img as head_img')
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->limit(($p-1)*$num,$num)->order("a.intime desc")->select();
            foreach($list as $k=>$v){
                if($member['member_id'] == $v['member_id']){
                    $list[$k]['is_follow'] = 3;
                }else{
                    //查询是否关注发帖人
                    $check = M('Follow')->where(['user_id'=>$member['member_id'],'user_id2'=>$v['member_id']])->find();
                    if($check){
                        $list[$k]['is_follow'] = 1;
                    }else{
                        $list[$k]['is_follow'] = 2;
                    }
                }
                //查询是否点过赞
                $check = M('ZanPost')->where(['type'=>'1','user_id'=>$member['member_id'],'post_id'=>$v['post_id']])->find();
                if($check){
                    $list[$k]['is_zan'] = 1;
                }else{
                    $list[$k]['is_zan'] = 2;
                }

                if (!empty($v['img'])) {
                    $img = unserialize($v['img']);
                    $list[$k]['img'] = $img;
                } else {
                    $list[$k]['img'] = [];
                };
                if (!empty($v['thumb'])) {
                    $thumb = unserialize($v['thumb']);
//                    foreach ($thumb as $key => $val) {
//                        $thumb[$key]['img'] = $fix_img . $val['img'];
//                    }
                    $list[$k]['thumb'] = $thumb;
                } else {
                    $list[$k]['thumb'] = [];
                }

                $list[$k]['date_value'] = translate_date($v['intime']);
                //列出举例的三个赞
                $list[$k]['zan_detail'] = M('ZanPost')->alias('a')
                        ->field('b.member_id,b.nickname')
                        ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                        ->where(['a.type'=>1,'a.post_id'=>$v['post_id']])
                        ->limit(3)->order("a.intime desc")->select();
                //列出评论详情
                $list[$k]['ping_detail'] = M('CommentPost')->alias('a')
                        ->field('a.content,a.intime,b.member_id,b.nickname')
                        ->join("LEFT JOIN __MEMBER__ b on a.mid = b.member_id")
                        ->where(['a.type'=>1,'a.post_id'=>$v['post_id']])
                        ->limit(3)->order("a.intime desc")->select();
            }
            success(['page'=>$page,'list'=>$list]);
        }
    }

    /**
     *@帖子详情
     */
    public function posts_view(){
        if(IS_POST){
            $member = checklogin();
            $post_id = I('post_id');
            if(empty($post_id))     error("参数错误");
            $map['a.post_id']     =   $post_id;
            $re = M('TopicalPosts')->alias('a')
                ->field('a.post_id,a.content,a.img,a.zan,a.ping,a.intime,a.thumb,b.member_id,b.nickname,b.img as head_img')
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->find();
            if(!empty($re['img'])){
                $img = unserialize($re['img']);
                $re['img'] = $img;
            }else{
                $re['img'] = [];
            };
            if(!empty($re['thumb'])){
                $thumb = unserialize($re['thumb']);
                $re['thumb'] = $thumb;

            }else{
                $re['thumb'] = [];
            }
            if($member['member_id'] == $re['member_id']){
                $re['is_follow'] = 3;
            }else{
                //查询是否关注发帖人
                $check = M('Follow')->where(['user_id'=>$member['member_id'],'user_id2'=>$re['member_id']])->find();
                if($check){
                    $re['is_follow'] = 1;
                }else{
                    $re['is_follow'] = 2;
                }
            }
            //查询是否点过赞
            $check = M('ZanPost')->where(['type'=>'1','user_id'=>$member['member_id'],'post_id'=>$re['post_id']])->find();
            if($check){
                $re['is_zan'] = 1;
            }else{
                $re['is_zan'] = 2;
            }

            $re['date_value'] = translate_date($re['intime']);
            //列出举例的三个赞
            $re['zan_detail'] = M('ZanPost')->alias('a')
                ->field('b.member_id,b.nickname')
                ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                ->where(['a.type'=>1,'a.post_id'=>$re['post_id']])
                ->limit(3)->order("a.intime desc")->select();
            //列出评论详情
            $re['ping_detail'] = M('CommentPost')->alias('a')
                ->field('a.content,a.intime,b.member_id,b.nickname')
                ->join("LEFT JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where(['a.type'=>1,'a.post_id'=>$re['post_id']])
                ->order("a.intime desc")->select();
            success($re);
        }
    }

    /**
     *@他人主页
     */
    public function user_home(){
        if(IS_POST){
            $member = checklogin();
            $p = I('p');
            empty($p)   && $p = 1;
            $num = 10;
            $user_id = I('user_id');
            $map['a.is_del'] = 1;
            $user = M('Member')->field(['member_id,nickname,img'])
                ->where(['member_id'=>$user_id])->find();
            $user['follow'] = M('Follow')->where(['user_id'=>$user['member_id']])->count();
            $user['fans'] = M('Follow')->where(['user_id2'=>$user['member_id']])->count();
            $check = M('Follow')
                ->where(['user_id'=>$member['member_id'],'user_id2'=>$user['member_id']])->find();
            if($check){
                $user['is_follow'] = 1;
            }else{
                $user['is_follow'] = 2;
                $map['a.auth'] = 1;
            }
            $map['a.mid'] = $user['member_id'];
            $count = M('TopicalPosts')->alias('a')
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->count();
            $page = ceil($count / $num);
            $list = M('TopicalPosts')->alias('a')
                ->field('a.post_id,a.content,a.img,a.zan,a.ping,a.intime,a.thumb,b.member_id,b.nickname,b.img as head_img')
                ->join("INNER JOIN __MEMBER__ b on a.mid = b.member_id")
                ->where($map)->limit(($p-1)*$num,$num)->order("a.intime desc")->select();
            foreach($list as $k=>$v){
                if($member['member_id'] == $v['member_id']){
                    $list[$k]['is_follow'] = 3;
                }else{
                    //查询是否关注发帖人
                    $check = M('Follow')->where(['user_id'=>$member['member_id'],'user_id2'=>$v['member_id']])->find();
                    if($check){
                        $list[$k]['is_follow'] = 1;
                    }else{
                        $list[$k]['is_follow'] = 2;
                    }
                }
                if (!empty($v['img'])) {
                    $img = unserialize($v['img']);
//                    foreach ($img as $key => $val) {
//                        $img[$key]['img'] = $fix_img . $val['img'];
//                    }
                    $list[$k]['img'] = $img;
                } else {
                    $list[$k]['img'] = [];
                };
                if (!empty($v['thumb'])) {
                    $thumb = unserialize($v['thumb']);
                    $list[$k]['thumb'] = $thumb;
                } else {
                    $list[$k]['thumb'] = [];
                }
                //查询是否点过赞
                $check = M('ZanPost')->where(['type'=>'1','user_id'=>$member['member_id'],'post_id'=>$v['post_id']])->find();
                if($check){
                    $list[$k]['is_zan'] = 1;
                }else{
                    $list[$k]['is_zan'] = 2;
                }

                $list[$k]['date_value'] = translate_date($v['intime']);
                //列出举例的三个赞
                $list[$k]['zan_detail'] = M('ZanPost')->alias('a')
                    ->field('b.member_id,b.nickname')
                    ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                    ->where(['a.type'=>1,'a.post_id'=>$v['post_id']])
                    ->limit(3)->order("a.intime desc")->select();
                //列出评论详情
                $list[$k]['ping_detail'] = M('CommentPost')->alias('a')
                    ->field('a.content,a.intime,b.member_id,b.nickname')
                    ->join("LEFT JOIN __MEMBER__ b on a.mid = b.member_id")
                    ->where(['a.type'=>1,'a.post_id'=>$v['post_id']])
                    ->limit(3)->order("a.intime desc")->select();
            }
            success(['user'=>$user,'page'=>$page,'list'=>$list]);
        }
    }

    /**
     *@查看关注状态
     */
    public function check_follow(){
        if(IS_POST){
            $member = checklogin();
            $user_id = I('user_id');
            if(empty($user_id))     error("参数错误");
            if($member['member_id'] == $user_id){
                success(3);
            }
            $check = M('Follow')->where(['user_id'=>$member['member_id'],'user_id2'=>$user_id])->find();
            if($check){
                success(1);
            }else{
                success(2);
            }
        }
    }

    /**
     *@举报用户
     */
    public function set_user_report(){
        if(IS_POST){
            $member                 = checklogin();
            $user_id                = I('user_id');
            if(empty($user_id))     error("被举报者不能为空");
            $data['content']        = I('content');
            $data['user_id']        = $member['member_id'];
            $data['response_id']    = $user_id;
            $data['intime']         = time();
            $data['state']          = 1;
            $check = M('Report')
                ->where(['user_id'=>$member['member_id'],'response_id'=>$user_id])
                ->limit(1)->order("intime desc")->find();
            if($check){
                if(time()- $check['intime']<3600){
                    error("举报时间间隔太短");
                }
            }
            $result = M('Report')->add($data);
            if($result){
                success("举报成功");
            }else{
                error("举报失败");
            }
        }
    }

    /**
     *@投票列表
     */
    public function pull_list(){
        if(IS_POST){
//            $member = checklogin();
            $p = I('p');
            empty($p)   && $p = 1;
            $num = 10;
            $map['status'] = '2';
            $count = M('TopicalPull')->where($map)->count();
            $page = ceil($count/$num);
            $list = M('TopicalPull')->field('id,title,number,img,intime')
                ->where($map)->limit(($p-1)*$num,$num)
                ->order("intime desc")->select();
            foreach($list as $k=>$v){
                $list[$k]['date_value'] = translate_date($v['intime']);
            }
            success(['page'=>$page,'list'=>$list]);
        }
    }

    /**
     *@投票详情
     */
    public function pull_view(){
        if(IS_POST){
//            $member = checklogin();
            $id = I('id');
            if(empty($id))          error("参数错误");
            $re = M('TopicalPull')->field('id,title,number,img,intime,content')
                ->where(['id'=>$id])->find();
            if(!$re)                error("参数错误");
            $re['detail'] = M('TopicalPullValue')->field('id,name,number')
                ->where(['pull_id'=>$id])->order("number desc")->select();
            success($re);
        }
    }

    /**
     *@投票
     */
    public function post_pull(){
        if(IS_POST){
            $member = checklogin();
            $pull_id = I('pull_id');
            $id = I('id');
            if(empty($pull_id) || empty($id))       error("参数错误");
            $check = M('TopicalPullNumber')->where(['pull_id'=>$pull_id,'user_id'=>$member['member_id']])->find();
            if($check)                              error("你已经投过票了");
            $data['pull_id'] = $pull_id;
            $data['pull_value_id'] = $id;
            $data['user_id'] = $member['member_id'];
            $data['intime'] = date("Y-m-d H:i:s",time());
            $result = M('TopicalPullNumber')->add($data);
            if($result){
                M('TopicalPull')->where(['id'=>$pull_id])->setInc("number");
                M('TopicalPullValue')->where(['id'=>$id])->setInc("number");
                success("投票成功");
            }else{
                error("投票失败");
            }
        }
    }

    /**
     *@好物分享
     */
    public function goods_share(){
        $uid = I('uid');
        $member = M('Member')->where(['member_id'=>$uid])->find();
        $p = I('p');
        empty($p)  && $p = 1;
        $num = 10;
        $map['a.type'] = ['in',['1','2']];
        $count = M('Comment')->alias('a')
            ->join("INNER JOIN __MEMBER__ b on a.member_id = b.member_id")
            ->where($map)->count();
        $page = ceil($count/$num);
        $list = M('Comment')->alias('a')
            ->field('a.comment_id,a.content,a.zan_number,a.member_id,a.intime,a.object_id,a.thumb,a.img,b.nickname,b.img as head_img,a.type')
            ->join("INNER JOIN __MEMBER__ b on a.member_id = b.member_id")
            ->where($map)->order("a.intime desc")
            ->limit(($p-1)*$num,$num)->select();
        if(!empty($list)){
            foreach($list as $k=>$v) {
                $list[$k]['img'] = explode(',', $v['img']);
                $list[$k]['thumb'] = explode(',', $v['thumb']);
                if ($v['type'] == 1) {
                    $list[$k]['goods'] = M('Goods')->field('goods_id,name,sale_price,thumb')
                        ->where(['goods_id' => $v['object_id'], 'type' => '1'])->find();
                } else {
                    $list[$k]['goods'] = M('GoodsTailor')->alias('a')
                        ->field('a.tailor_id as goods_id,b.name,a.presale_price as sale_price,b.thumb')
                        ->join("INNER JOIN __GOODS__ b on a.goods_id = b.goods_id")
                        ->order("a.intime desc")->limit(1)
                        ->where(['a.goods_id' => $v['object_id'], 'b.type' => '2'])->find();
                }
                $list[$k]['data_value'] = translate_date($v['intime']);
                $list[$k]['zan_detail'] = M('ZanPost')->alias('a')
                    ->field('b.member_id,b.nickname')
                    ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                    ->where(['a.type' => 3, 'a.post_id' => $v['comment_id']])
                    ->limit(3)->order("a.intime desc")->select();
                //查询是否点过赞
                if ($member) {
                    $check = M('ZanPost')->where(['type' => '3', 'user_id' => $member['member_id'], 'post_id' => $v['comment_id']])->find();
                    if ($check) {
                        $list[$k]['is_zan'] = 1;
                    } else {
                        $list[$k]['is_zan'] = 2;
                    }
                    if ($member['member_id'] == $v['member_id']) {
                        $list[$k]['is_follow'] = 3;
                    } else {
                        //查询是否关注发帖人
                        $check = M('Follow')->where(['user_id' => $member['member_id'], 'user_id2' => $v['member_id']])->find();
                        if ($check) {
                            $list[$k]['is_follow'] = 1;
                        } else {
                            $list[$k]['is_follow'] = 2;
                        }
                    }
                }
            }
        }
        success(['page'=>$page,'list'=>$list]);
    }


    /**
     *@我的关注
     */
    public function my_follow(){
        $member = checklogin();
        $p = I('p');
        empty($p)   &&  $p = 1;
        $num = 10;
        $count = M('Follow')->alias('a')
            ->join("LEFT JOIN __MEMBER__ b on a.user_id2 = b.member_id")
            ->where(['a.user_id'=>$member['member_id']])->count();
        $page = ceil($count/$num);
        $list = M('Follow')->alias('a')
            ->field("b.member_id,b.img,b.nickname")
            ->join("LEFT JOIN __MEMBER__ b on a.user_id2 = b.member_id")
            ->limit(($p-1)*$num,$num)
            ->where(['a.user_id'=>$member['member_id']])->select();
        foreach($list as $k=>$v){
            $check = M('Follow')->where(['user_id'=>$v['member_id'],'user_id2'=>$member['member_id']])->find();
            if($check){
                $list[$k]['is_follow'] = 1;
            }else{
                $list[$k]['is_follow'] = 2;
            }
            $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
        }
        success(['page'=>$page,'list'=>$list]);
    }
    /**
     * @我的粉丝
     */
    public function my_fans(){
        if(IS_POST){
            $member = checklogin();
            $p = I('p');
            empty($p)   &&  $p = 1;
            $num = 10;
            $count = M('Follow')->alias('a')
                ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                ->where(['a.user_id2'=>$member['member_id']])->count();
            $page = ceil($count/$num);
            $list = M('Follow')->alias('a')
                ->field("b.member_id,b.img,b.nickname")
                ->join("LEFT JOIN __MEMBER__ b on a.user_id = b.member_id")
                ->limit(($p-1)*$num,$num)
                ->where(['a.user_id2'=>$member['member_id']])->select();
            foreach($list as $k=>$v){
                $check = M('Follow')->where(['user_id'=>$member['member_id'],'user_id2'=>$v['member_id']])->find();
                if($check){
                    $list[$k]['is_follow'] = 1;
                }else{
                    $list[$k]['is_follow'] = 2;
                }
                $list[$k]['img'] = C('IMG_PREFIX').$v['img'];
            }
            success(['page'=>$page,'list'=>$list]);
        }
    }

    /**
     *删帖
     */
    public function del_topical_posts(){
        if(IS_POST){
            $member = checklogin();
            $id = I('post_id');
            if(empty($id))              error("参数错误");
            $result = M('TopicalPosts')->where(['post_id'=>$id])->save(['is_del'=>'2']);
            if($result){
                success("删除成功");
            }else{
                error("删除失败");
            }
        }
    }


}