<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/18
 * Time: 15:41
 */

namespace Api\Model;
use Think\Model;
class PassengerModel extends Model
{
    public function auth(){
        $data = $_POST;
        if(empty($data['type']))            error("请选择证件号类型");
        switch($data['type']){
            case "身份证" :
                if(empty($data['name']))    error("请填写乘机人姓名");
                if(empty($data['card']))    error("请填写乘机人身份证号");
                if(!preg_match('/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/',$data['card'])){
                    error("您填写的乘机人身份证号错误");
                }
                if(!validateIDCard($data['card']))      error("您填写的乘机人身份证号错误");
                break;
            case "护照" :
                if(empty($data['lastname']))  error("请填写乘机人姓");
                if(empty($data['firstname'])) error("请填写乘机人名");
                if(empty($data['sex']))       error("请选择乘机人性别");
                if(empty($data['borth']))     error("请填写乘机人出生日期");
                if(empty($data['country']))   error("请填写乘机人国籍");
                if(empty($data['card']))      error("请填写乘机人证件号");
                if(empty($data['term']))      error("请填写乘机人有效期");
                if(empty($data['issued']))    error("请填写证件签发地");
                break;
            case "其他" :
                if(empty($data['name']))      error("请填写乘机人姓名");
                if(empty($data['card']))      error("请填写乘机证件号");
                if(empty($data['sex']))       error("请选择乘机人性别");
                if(empty($data['borth']))     error("请填写乘机人出生日期");
                break;
        }
        if(empty($data['id'])){
            if(M('Passenger')->where(['card'=>$data['card'],'uid'=>$data['uid']])->count()>0){
                error("您填写的乘机人证件号有重复，请重新填写");
            }
        }else{
            $card = M('Passenger')->where(['id'=>$data['id'],'uid'=>$data['uid']])->getField('card');
            if($card != $data['card']){
                if(M('Passenger')->where(['card'=>$data['card']])->count()>0){
                    error("您填写的乘机人证件号有重复，请重新填写");
                }
            }
        }

    }
}