<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2016/3/30
 * Time: 10:01
 */

namespace Admin\Model;


use Think\Model;

class WeixinMenuModel extends Model
{
    protected $_validate = [
        ['title','require','菜单名称不能为空!'],
    ];

    protected $_auto = [
        ['create_time','time',3,'function']
    ];
}