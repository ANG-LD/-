<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
define('BUILD_DIR_SECURE', false);
// 定义应用目录
define('BIND_MODULE', 'Admin');
define('BUILD_CONTROLLER_LIST','System,Member,Auth');
define('APP_PATH','./application/');
define('SITE_PATH',__DIR__);
define("WEB_ROOT", dirname(__FILE__) . "/application/");
define('BASE_PATH',str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/");

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单