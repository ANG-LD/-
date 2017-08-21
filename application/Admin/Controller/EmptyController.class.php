<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/8/29
 * Time: 17:32
 */

namespace Admin\Controller;
use Think\Controller;
class EmptyController extends Controller
{
    public function _empty(){
        $this->redirect('Public/error');
    }

}