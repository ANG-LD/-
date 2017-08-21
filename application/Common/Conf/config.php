<?php

/*
 * 通用配置文件
 * Author：leo.li（281978297@qq.com）
 * Date:2013-02-01
 */
return array(
    'SHOW_PAGE_TRACE' => FALSE,
    'TOKEN_ON' => false, // 是否开启令牌验证
    'TOKEN_NAME' => '__hash__', // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE' => 'md5', //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET' => FALSE, //令牌验证出错后是否重置令牌 默认为true
    'AUTH_CODE' => 'zhengan88', //密码加密字段
    'SESSION_PREFIX' => 'zhengan88_',

    'LOAD_EXT_CONFIG' => 'systemConfig',

//    'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
//    'MODULE_ALLOW_LIST'     =>  array('Home','Conist','Api','Wap'), // 配置你原来的分组列表
//    'DEFAULT_MODULE'        =>  'Admin', // 配置你原来的默认分组
//    'MODULE_DENY_LIST'      =>  array('Common','Runtime','Ucenter'),
//    'URL_MODULE_MAP'    =>    array('conist'=>'admin'),	//模块映射

    //数据库'配置项'=>'配置值'
    'DB_TYPE'		=> 'mysql', // 数据库类型
    'DB_HOST' => '139.196.178.64', // 服务器地址
    'DB_NAME'		=> 'bsxy', // 数据库名
    'DB_USER'		=> 'root', // 用户名
    'DB_PWD' => 'Zha54321', // 密码
    'DB_PORT'		=> 3306, // 端口
    'DB_PREFIX'		=> 'tk_', // 数据库表前缀
    'DB_CHARSET'	=> 'utf8', // 字符集

    'URL_MODEL'     =>  1,// URL访问模式
    //'URL_HTML_SUFFIX'=>'html',
    //'PATH_MODEL'=>  2,
    //'URL_PATHINFO_DEPR'=>'-',
    'IMG_PREFIX'	=> 'http://bs.tstmobile.com',

//    'AUTH_CONFIG' => array(
//        'AUTH_ON' => true, //认证开关
//        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
//        'AUTH_GROUP' => 'tk_auth_group', //用户组数据表名
//        'AUTH_GROUP_ACCESS' => 'tk_auth_group_access', //用户组明细表
//        'AUTH_RULE' => 'tk_auth_rule', //权限规则表
//        'AUTH_USER' => 'tk_user'//用户信息表
//    ),

    	'MAIL_HOST' =>'smtp.163.com',//smtp服务器的名称
		'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
		'MAIL_USERNAME' =>'yuexiaooffice@163.com',//你的邮箱名
		'MAIL_FROM' =>'yuexiaooffice@163.com',//发件人地址
		'MAIL_FROMNAME'=>'管理员',//发件人姓名
		'MAIL_PASSWORD' =>'123qweasd',//邮箱密码
		'MAIL_CHARSET' =>'utf-8',//设置邮件编码
		'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
		'MAIL_SEND_NAME'=>"管理员",//发送者名称
		'MAIL_SEND_TITLE_active'=>"【途老大】邮箱激活",//发送激活邮件时候的标题
		'MAIL_SEND_TITLE_sendcode'=>"【途老大】邮件验证码",//发送邮件验证码时候的标题
		'MAIL_SEND_JIANGE'=>"20",//邮件发送间隔，单位秒
		'MAIL_SEND_SHIXIAO'=>"1800",//邮件uuid生效时间
);
/*$config2 = WEB_ROOT . "Common/Conf/systemConfig.php";
$config2 = file_exists($config2) ? include "$config2" : array();

return array_merge($config1, $config2);*/