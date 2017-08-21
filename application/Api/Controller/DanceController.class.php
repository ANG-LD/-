<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/5/3
 * Time: 11:14
 */

namespace Api\Controller;


class DanceController extends CommonController
{
    /**
     *@街舞首页
     */
    public function index(){
        $member = checklogin();
        $count = M('DanceEnroll')->where(['is_del'=>'1','state'=>'2'])->count();
        $content = M('Notice')->where(['id'=>'21'])->getField('content');
        $map['uid'] = $member['member_id'];
        $map['state'] = '2';
        $map['is_del'] = '1';
        $check = M('DanceEnroll')->where($map)->find();
        if($check){
            $is_check = 1;
        }else{
            $is_check = 2;
        }
        success(['count'=>$count,'content'=>$content,'is_check'=>$is_check]);
    }

    /**
     *@街舞报名页
     *@post
     */
    public function dance_sign(){
        if(IS_POST){

        }
    }
}