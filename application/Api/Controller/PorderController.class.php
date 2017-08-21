<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/10/28
 * Time: 9:40
 */

namespace Api\Controller;


use Com\WechatAuth;

class PorderController extends CommonController
{
    private $system=array();
    function _initialize(){
        header("Content-type: text/html; charset=utf-8");
        $this->system = M("system")->where(['id'=>1])->find();
    }

    public function teach(){

        $this->display();
    }

}