<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/6
 * Time: 14:09
 */

namespace Admin\Model;


use Think\Model;

class TutorLiveClassModel extends Model
{
  public function auth(){
      $data['name'] = I('name');
      if(empty($data['name'])){
          return array('status'=>'error','info'=>'班级名不能为空','class'=>'title');
      }
      $data['tutor_id'] = I('user_id');
      if(empty($data['tutor_id'])){
          return array('status'=>'error','info'=>'请选择导师','class'=>'username');
      }
      $data['start_time'] = I('start_time');
      $data['end_time'] = I('end_time');
      if(empty($data['start_time'])){
          return array('status'=>'error','info'=>'开始时间不能为空','class'=>'start_time');
      }
      $data['end_time'] = strtotime($data['start_time']);
      $data['end_time'] = date("Y-m-d",strtotime('+1 year',$data['end_time']));
//      if(empty($data['end_time'])){
//          return array('status'=>'error','info'=>'结束时间不能为空','class'=>'end_time');
//      }
      $id = I('id');
      if(empty($id)) {
          $check = M('TutorLiveClass')->where(['tutor_id'=>$data['tutor_id']])->order("intime desc")->limit(1)->find();
          if(time()<strtotime($check['end_time']) && $check){
              return array('status'=>'error','info'=>'该导师还在授课中,不能重新开班');
              die;
          }
          $data['intime'] = date("Y-m-d");
          $result = M('TutorLiveClass')->add($data);
          $action = '添加';
      }else{
          $data['uptime'] = date("Y-m-d");
          $result = M('TutorLiveClass')->where(['id'=>$id])->save($data);
          $action = ' 编辑';
      }
      if($result){
          return array('status'=>'ok','info'=>$action.'线上班级成功','url'=>session('url'));
          die;
      }else{
          return array('status'=>'error','info'=>$action.'线上班级失败');
          die;
      }
  }
}