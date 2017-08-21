<?php
namespace Wap\Controller;
use Think\Controller;
use Org\WeixinPay\JsApiPay;
use Org\WeixinPay\CLogFileHandler;
class WxpayController extends Controller{
	public function h5pay(){
		//初始化日志
		$logHandler= new CLogFileHandler("/logs/".date('Y-m-d').'.log');
		$log = Log::Init($logHandler, 15);
		// ①、获取用户openid
		$tools = new JsApiPay();
		$openId = $tools->GetOpenid();
		// $this->display();
	}
} 