<?php
namespace Org\Net;
class Message{
	// 获取创蓝验证码
	static public function getFromCl($mobile){
		$code					= self::createIdentifyingCode();
		$data					= "亲爱的用户，您的手机验证码是{$code}";
		$post_data 				= array();
		$post_data['account']	= iconv('GB2312', 'GB2312', "vipyswl");
		$post_data['pswd']		= iconv('GB2312', 'GB2312', "MJmeihu123");
		$post_data['mobile']	= $mobile;
		$post_data['msg']		= mb_convert_encoding("$data",'UTF-8', 'auto');
		$url	= 'http://222.73.117.158/msg/HttpBatchSendSM?';
		$o		= "";
		foreach ($post_data as $k=>$v){
			$o.= "$k=".urlencode($v)."&";
		}
		$post_data	= substr($o, 0, -1);
		$result		= self::curlPost($url, $post_data);	
		$result		= explode(",", $result);
		return $result[1] == 0 ? $code : false;
	}
	
	// 创建验证码短信
	static public function postNoticeVerify(){
		$code					= self::createIdentifyingCode();
		$data					= "亲爱的用户，您的手机验证码是{$code}";
		$post_data 				= array();
		$post_data['account']	= iconv('GB2312', 'GB2312', "vipyswl");
		$post_data['pswd']		= iconv('GB2312', 'GB2312', "MJmeihu123");
		$post_data['mobile']	= $mobile;
		$post_data['msg']		= mb_convert_encoding("$data",'UTF-8', 'auto');
		$url	= 'http://222.73.117.158/msg/HttpBatchSendSM?';
		$o		= "";
		foreach ($post_data as $k=>$v){
			$o.= "$k=".urlencode($v)."&";
		}
		$post_data	= substr($o, 0, -1);
		$result		= self::curlPost($url, $post_data);
		$result		= explode(",", $result);
		return $result[1] == 0 ? true : false;
	}
	
	// 创建订单短信
	static public function postNoticeOrder($mobile, $name, $service, $orderno){
		$data					= "亲爱的{$name},您有一个{$service}的新订单{$code},请及时查看订单";
		$post_data 				= array();
		$post_data['account']	= iconv('GB2312', 'GB2312', "vipyswl");
		$post_data['pswd']		= iconv('GB2312', 'GB2312', "MJmeihu123");
		$post_data['mobile']	= $mobile;
		$post_data['msg']		= mb_convert_encoding("$data",'UTF-8', 'auto');
		$url	= 'http://222.73.117.158/msg/HttpBatchSendSM?';
		$o		= "";
		foreach ($post_data as $k=>$v){
			$o.= "$k=".urlencode($v)."&";
		}
		$post_data	= substr($o, 0, -1);
		$result		= self::curlPost($url, $post_data);
		$result		= explode(",", $result);
		return $result[1] == 0 ? true : false;
	}
	
	// curl post
	static private function curlPost($url, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	// 创建二维码
	static private function createIdentifyingCode(){
		$code = '';
		for ($i=0;$i<6;$i++){
			$code.= rand(0, 9);
		}
		return $code;
	}
}