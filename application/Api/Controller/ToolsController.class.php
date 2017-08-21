<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/20
 * Time: 13:38
 */

namespace Api\Controller;

use Think\Controller;
use Think\Image;
use Think\Upload;

class ToolsController extends Controller
{

    /**
     * 图集列表
     */
//    public function images()
//    {
//        $user_id = $this->userMsg['id'];
//        $map['user_id'] = $user_id;
//        $count=M('Images')->where($map)->count();
//        $p=$this->getpage($count,9);
//        $list = M('Images')->where($map)->order("id desc")->limit($p->firstRow,$p->listRows)->select();
//        $this ->assign(['list'=>$list,'page'=>$p->show()]);
//        C("LAYOUT_NAME","Layout/win");
//        $this->display();
//    }
    public function _initialize(){
        $this->url = C('IMG_PREFIX');
    }

    /**
     * 图片统一上传入口
     */
    public function upload_waiter()
    {
        $dn=trim(@$_GET['dirname']);
        $config = [
            'maxSize'	=> 3145728,
            'rootPath'	=> './',
            'savePath'	=> '/Uploads/image/'.$dn.'/'.$_REQUEST[''],
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['jpg', 'gif', 'png', 'jpeg'],
            'autoSub'	=> true,
            'subName'	=> ['date','Ymd'],
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
//        $user_id = $this->userMsg['id'];
        if ($info){
//            $data['url']		    = $info['Filedata']['savepath'].$info['Filedata']['savename'];
//            $data['create_time']	= time();
//            $data['user_id']        = $user_id;
//            M('images')->add($data);
            $image = new \Think\Image();
            $image->open('.'.$info['imgFile']['savepath'].$info['imgFile']['savename'])
                ->water('./Uploads/shuiyin.png',\Think\Image::IMAGE_WATER_SOUTHEAST,50)
                ->save('.'.$info['imgFile']['savepath'].$info['imgFile']['savename']);
            $utl = $info['imgFile']['savepath'].$info['imgFile']['savename'];
            echo json_encode(array('error' => 0, 'url' => $utl));
        } else {
            echo json_encode($uploader->getError());
        }
    }


    /**
     * 头像图片统一上传入口
     */
    public function upload()
    {
        $dn=trim(@$_GET['dirname']);
        $config = [
            'maxSize'	=> 3145728,
            'rootPath'	=> './',
            'savePath'	=> '/Uploads/image/'.$dn.'/'.$_REQUEST[''],
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['jpg', 'gif', 'png', 'jpeg'],
            'autoSub'	=> true,
            'subName'	=> ['date','Ymd'],
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
//        $user_id = $this->userMsg['id'];
        if ($info){
//            $data['url']		    = $info['Filedata']['savepath'].$info['Filedata']['savename'];
//            $data['create_time']	= time();
//            $data['user_id']        = $user_id;
//            M('images')->add($data);
            $utl = $info['imgFile']['savepath'].$info['imgFile']['savename'];
            echo json_encode(array('error' => 0, 'url' => $utl));
        } else {
            echo json_encode($uploader->getError());
        }
    }

    /**
     * 删除图片
     */
    public function del_img()
    {
        $url = I('url');
        if($url == '/public/admin/touxiang.png'){
            echo json_encode(array('status' => 'ok'));
            return true;
        }
        $result = unlink(rtrim(BASE_PATH, '/').$url);
        if($result){
            echo json_encode(array('status' => 'ok'));
        }else{
            echo json_encode(array('status' => 'error'));
        }
    }

    /**
     *@图片上传并生成缩略图
     */
    public function upload_photo(){
        $dn=trim(@$_GET['dirname']);
        $config = [
            'maxSize'	=> 3145728,
            'rootPath'	=> './',
            'savePath'	=> '/Uploads/image/'.$dn.'/'.$_REQUEST[''],
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['jpg', 'gif', 'png', 'jpeg'],
            'autoSub'	=> true,
            'subName'	=> ['date','Ymd'],
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info){
            $image = new Image();
            $url = '.'.$info['imgFile']['savepath'].$info['imgFile']['savename'];
            $result = $image->open($url)
                ->thumb(500, 500,\Think\Image::IMAGE_THUMB_FIXED)
                ->save('.'.$info['imgFile']['savepath'].'thumb'.$info['imgFile']['savename']);
            $url2 = $info['imgFile']['savepath'].'thumb'.$info['imgFile']['savename'];
            $utl = $info['imgFile']['savepath'].$info['imgFile']['savename'];
//            if($result){
//                echo json_encode(array("ok"));
//                die;
//            }else{
//                echo json_encode(array("error"));
//                die;
//            }
            echo json_encode(array('status' =>'ok', 'url' => $utl,'url2'=>$url2));
        } else {
            echo json_encode($uploader->getError());
        }
    }

    /**
     * 删除多张图片
     */
    public function del_imgs()
    {
        $url = I('url');
        $url = implode(',',$url);
        foreach($url as $k){
            $result = unlink(rtrim(BASE_PATH, '/').$k);
        }
        if(!$result){
            echo json_encode(array('status' => 'error'));
        }
    }

    /**
     * 定制图片统一上传入口
     */
    public function upload_picture()
    {
        $config = [
            'maxSize'	=> 3145728,
            'rootPath'	=> './',
            'savePath'	=> '/Uploads/image/comment/'.$_REQUEST[''],
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['jpg', 'gif', 'png', 'jpeg'],
            'autoSub'	=> true,
            'subName'	=> ['date','Ymd'],
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info){
            foreach($info as $k=>$v){
                $url[] = $v['savepath'].$v['savename'];
            }
            echo json_encode(array('status' => 'ok', 'data' => $url));
        } else {
            echo json_encode(array('status' => 'error', 'data' => $uploader->getError(),'error'=>$uploader->getError()));
        }
    }

    /**
     * 定制图片统一上传入口
     */
    public function upload_comment_picture()
    {
        $config = [
            'maxSize'	=> 3145728,
            'rootPath'	=> './',
            'savePath'	=> '/Uploads/image/comment/'.$_REQUEST[''],
            'saveName'	=> ['uniqid',''],
            'exts'		=> ['jpg', 'gif', 'png', 'jpeg'],
            'autoSub'	=> true,
            'subName'	=> ['date','Ymd'],
        ];
        $uploader = new Upload($config);
        $info = $uploader->upload();
        if ($info){
            foreach($info as $k=>$v){
                $url[] = $v['savepath'].$v['savename'];
                $array = getimagesize('.' . $v['savepath'].$v['savename']);
                if ($array[0] > 200) {
                    $image = new \Think\Image();
                    $image->open('.' . $v['savepath'].$v['savename']);
                    $image->thumb(200, 200, \Think\Image::IMAGE_THUMB_SCALE)
                        ->save('./Uploads/image/comment/thumb/' . time() . '_' . $v["savename"]);
                    $thumb = '/Uploads/image/comment/thumb/' . time() . '_' . $v["savename"];
                } else {
                    $thumb = $v['savepath'].$v['savename'];
                }
                $arr[] = $thumb;
            }
            $img['img'] = $url;
            $img['thumb'] = $arr;
            echo json_encode(array('status' => 'ok', 'data' => $img));
        } else {
            echo json_encode(array('status' => 'error', 'error' => $uploader->getError(),'data'=>$uploader->getError()));
        }
    }

    /**
     *定义空方法
     */
    public function _empty(){
        $this->redirect('Public/error');
    }
}