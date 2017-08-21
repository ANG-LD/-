<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/11
 * Time: 15:54
 */

namespace Admin\Controller;


class ChatController extends BaseController
{
    /**
     *@客服列表
     */
    public function index(){
        $list = M('User')->where(['type'=>3])->select();
        $count = M('User')->where(['type'=>3])->count();
        $this->assign(['count'=>$count,'list'=>$list]);
        $url = $_SERVER['REQUEST_URI'];
        session('url',$url);
        $this->display();
    }

    /**
     *@编辑客服信息
     */
    public function edit_kefu(){
        $id = I('uid');
        if(IS_POST){
            $data = $_POST;
            if(empty($data['username'])){
                echo json_encode(array('status'=>'error','info'=>'客服昵称不能为空','class'=>''));
                die;
            }
            if(empty($data['sex'])){
                echo json_encode(array('status'=>'error','info'=>'性别不能为空','class'=>''));
                die;
            }
            if(empty($data['img'])){
                echo json_encode(array('status'=>'error','info'=>'客服头像不能为空','class'=>''));
                die;
            }
            $result = M('User')->where(['user_id'=>$data['uid']])->save($data);
            if($result){
                echo json_encode(array('status'=>'ok','info'=>'编辑客服记录成功','url'=>session('url')));
                die;
            }else{
                echo json_encode(array('status'=>'error','info'=>'编辑客服记录失败'));
                die;
            }
        }else{
            $re = M('User')->where(['user_id'=>$id])->find();
            $this->assign(['re'=>$re]);
            $this->display();
        }
    }

    public function kefu(){
        check_auth();
        if(IS_POST){

        }else{
            $kefu = M('User')->where(['type'=>3])->find();
            $this->assign(['kefu'=>$kefu]);
            $this->display();
        }

    }

    public function youke(){
        check_auth();
        if(IS_POST){

        }else{
            $kefu = M('User')->where(['user_id'=>442])->find();
            $this->assign(['kefu'=>$kefu]);
            $this->display();
        }

    }

    /**
     * @根据环信用户名，返回用户信息
     */
    public function get_user_info(){
        $hx_username = I('hx_username');
        if (empty($hx_username)) {
            error("不存在");
        } else {
            $user = M('User')->field('user_id,username,company,duty,img,sex,hx_username,ID,grade,intime,autograph')->where(['hx_username' => $hx_username])->find();
            if(!$user){
                error("不存在");
            }
            $user['img'] = C('IMG_PREFIX') . $user['img'];
            $get_gradeinfo = get_gradeinfo($user['grade']);
            $user['grade_img'] = $get_gradeinfo['img'];
            $user['name'] = $get_gradeinfo['name'];
            $user['intime'] = date("Y-m-d H:i:s",$user['intime']);
            success($user);
        }
    }
}