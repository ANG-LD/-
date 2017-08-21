<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/25
 * Time: 9:40
 */

namespace Admin\Model;
use Think\Model;
class SpecialTicketModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['go_city']))             error("出发城市不能为空");
        if(empty($data['arrive_city']))         error("目的城市不能为空");
        if(empty($data['go_hangban']))              error("去程航司不能为空");
        if(empty($data['price']))              error("最低价不能为空");
        if(empty($data['go_date1']))             error("出行日期不正确");
        if(empty($data['go_date2']))             error("出行日期不正确");
        if(empty($data['cangwei']))             error("舱位选择不能为空");
        if($data['type'] == '2'){
            if(empty($data['arrive_hangban']))             error("返程航司不能为空");
//            if(empty($data['arrive_date1']))             error("返程日期不正确");
//            if(empty($data['arrive_date2']))             error("返程日期不正确");
        }
        if(empty($data['id'])){
            $data['intime'] = date("Y-m-d H:i;s",time());
            $data['go_date'] = date("Y-m-d",strtotime($data['go_date']));
            $result = M('SpecialTicket')->add($data);
            $action = '新增';
        }else{
            $data['uptime'] = date("Y-m-d H:i:s",time());
            $data['go_date'] = date("Y-m-d",strtotime($data['go_date']));
            $result = M('SpecialTicket')->where(['id'=>$data['id']])->save($data);
            $action = '编辑';
        }
        if($result){
            return array('status'=>'ok','info'=>$action.'记录成功','url'=>session('url'));
        }else{
            return array('status'=>'error','info'=>$action.'记录失败');
        }
    }
}