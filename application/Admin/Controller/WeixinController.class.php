<?php
/*
 * 1、本微信系统不针对多公众号
 * 2、本微信系统服务对象:a.认证后的订阅号;b.服务号
 */
namespace Admin\Controller;
use Com\Jssdk;
use Think\Controller;
use Com\WechatAuth;
use Com\Wechat;


class WeixinController extends Controller{

	private $system=array();
	function _initialize(){
		header("Content-type: text/html; charset=utf-8");
		$this->system = M("system")->where(['id'=>1])->find();
		$this->assign(['system'=>$this->system]);
	}

	/**
	 *获取openid和注册
	 */
	public function getOpenIdRs(){
		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->system['appid']."&secret=".$this->system['appsecret']."&code=".I("code")."&grant_type=authorization_code";
		$result= curl_get($url);
		$arr = json_decode($result,true);
		$url1 = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$this->system['appid']."&grant_type=refresh_token&refresh_token=".$arr['refresh_token'];
		$arr1 = curl_get($url1);
		$arr1 = json_decode($arr1,true);

		$url2 = "https://api.weixin.qq.com/sns/userinfo?access_token=".$arr1['access_token']."&openid=".$this->system['appid']."&lang=zh_CN";
		$arr2 = curl_get($url2);
		$openid = $arr['openid'];
		if(!empty($openid)){
			$check = M('Member')->field('member_id,token,openid')->where(['openid'=>$openid])->find();
			if(!$check){
				$arr2 = json_decode($arr2,true);
				$data['openid'] = $openid;
				$data['token'] = uniqid();
				$data['intime'] = date("Y-m-d H:i:s",time());
				$data['nickname'] = $arr2['nickname'];
				$data['img'] = $arr2['headimgurl'];
				$data['sex'] = $arr2['sex'];
				$data['province'] = $arr2['province'];
				$data['city'] = $arr2['city'];
				$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
				mt_srand(10000000 * (double)microtime());
				for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 12; $i++) {
					$str .= $chars[mt_rand(0, $lc)];
				}
				for ($i = 0, $str1 = '', $lc = strlen($chars) - 1; $i < 13; $i++) {
					$str1 .= $chars[mt_rand(0, $lc)];
				}
				$hx_password = "123456";
				$data['hx_password'] = $hx_password;
				$data['hx_username'] = $str;
				$data['score'] = 500;
				$data['alias'] = $str;
				huanxin_zhuce($str, $hx_password);
				$result = M('Member')->add($data);
				$check = M('Member')->where(['member_id'=>$result])->find();
				$check = json_encode(array('uid'=>$check['member_id'],'token'=>$check['token'],'openid'=>$check['openid'],'phone'=>$check['phone']));
			}else{
				$arr2 = json_decode($arr2,true);
				$data['img'] = $arr2['headimgurl'];
				$data['sex'] =$arr2['sex'];
				$data['nickname'] = $arr2['nickname'];
				$data['province'] = $arr2['province'];
				$data['city'] = $arr2['city'];
				M('Member')->where(['member_id'=>$check['member_id']])->save($data);
				$check = json_encode(array('uid'=>$check['member_id'],'token'=>$check['token'],'openid'=>$check['openid'],'phone'=>$check['phone']));
			}
			cookie('member',$check);
		}
//		cookie('openid',$arr['openid']);
		$code = cookie('url');
		if(!empty($code)){
			header('location:'.$code);
		}
	}

	public function getjssdk()
	{
		$jssdk = new Jssdk($this->system['appid'], $this->system['appsecret']);
		$signPackage = $jssdk->GetSignPackage(I("url"));
		success($signPackage);
	}

	/**
	 *获取openid
	 */
	public function getOpenId()
	{
		$code = cookie('url');
		$code = urldecode($code);
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->system['appid'] . "&secret=" . $this->system['appsecret'] . "&code=" . I("code") . "&grant_type=authorization_code";
		$result = curl_get($url);
		$arr = json_decode($result, true);
		$url1 = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=" . $this->system['appid'] . "&grant_type=refresh_token&refresh_token=" . $arr['refresh_token'];
		$arr1 = curl_get($url1);
		$arr1 = json_decode($arr1, true);

		$url2 = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $arr1['access_token'] . "&openid=" . $this->system['appid'] . "&lang=zh_CN";
		$arr2 = curl_get($url2);
		$openid = $arr['openid'];
		if (!empty($openid)) {
			$check = M('Member')->field('member_id,token,openid,hx_username,hx_password')->where(['openid' => $openid])->find();
			if (!$check) {
				$parse = explode('?', $code); //截取参数
				$count = count($parse);
				$parse = $parse[$count - 1];
				parse_str($parse, $e); //参数转化为数组
				$chars = "abcdefghijklmnopqrstuvwxyz0123456789";
				mt_srand(10000000 * (double)microtime());
				for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < 12; $i++) {
					$str .= $chars[mt_rand(0, $lc)];
				}
				for ($i = 0, $str1 = '', $lc = strlen($chars) - 1; $i < 13; $i++) {
					$str1 .= $chars[mt_rand(0, $lc)];
				}
				$hx_password = "123456";
				$data['hx_password'] = $hx_password;
				$data['hx_username'] = $str;
				$data['score'] = 500;
				$arr2 = json_decode($arr2, true);
				$data['openid'] = $openid;
				$data['token'] = uniqid();
				$data['intime'] = date("Y-m-d H:i:s", time());
				$data['nickname'] = $arr2['nickname'];
				$data['img'] = $arr2['headimgurl'];
				$data['sex'] = $arr2['sex'];
				$data['province'] = $arr2['province'];
				$data['city'] = $arr2['city'];
				$result = M('Member')->add($data);
				$url = "http://91dreambar.com/web/#/?uid=" . $result;
				$middle = "./Uploads/qrcode/" . md5($url) . '_middle.png';
				qrcode($url, $middle, 4, 5);
				$share_qrcode = "/Uploads/qrcode/" . md5($url) . '_middle.png';
				M('Member')->where(['member_id' => $result])->save(['share_qrcode' => $share_qrcode]);
				if (!empty($e['uid'])) {
					$today = strtotime(date("Y-m-d", time()));
					$count = M('Share')->where(['mid' => $e['uid'], 'intime' => ['gt', $today]])->count();
					if ($count < 6) {
						$share_arr['mid'] = $e['uid'];
						$share_arr['share_id'] = $result;
						$share_arr['intime'] = date("Y-m-d H:i:s");
						$share_arr['score'] = 200;
						$res = M('Share')->add($share_arr);
						if ($res) {
							M('Member')->where(['member_id' => $e['uid']])->setInc("score", 200);
						}
					}
				}
					$check = M('Member')->where(['member_id' => $result])->find();
					$check = json_encode(array('uid' => $check['member_id'], 'token' => $check['token'], 'openid' => $check['openid'],
						'phone' => $check['phone'], 'hx_username' => $check['hx_username'], 'hx_password' => $check['hx_password']));
				} else {
					$arr2 = json_decode($arr2, true);
					$data['img'] = $arr2['headimgurl'];
					$data['sex'] = $arr2['sex'];
					$data['nickname'] = $arr2['nickname'];
					$data['province'] = $arr2['province'];
					$data['city'] = $arr2['city'];
					M('Member')->where(['member_id' => $check['member_id']])->save($data);
					$check = json_encode(array('uid' => $check['member_id'], 'token' => $check['token'], 'openid' => $check['openid'],
						'phone' => $check['phone'], 'hx_username' => $check['hx_username'], 'hx_password' => $check['hx_password']));
				}
				cookie('member', $check);
			}
			if (!empty($code)) {
				header('location:' . $code);
			}
	}

	/**
	 *验证与微信消息回复
	 */
	public function test(){
		/*加载微信SDK*/ $token = 'tulaoda';//微信后台填写的TOKEN
		/*加载微信SDK*/ $weChat = new Wechat($token);
		if(IS_GET){
			file_put_contents("1.txt",$_GET['echostr'],FILE_APPEND);
			echo $_GET['echostr'];
			exit();
		}
		$data = $weChat->request();
		file_put_contents("data.txt",$data,FILE_APPEND);
		if($data && is_array($data)){
			file_put_contents("xml.txt",'4566',FILE_APPEND);
			//在这里你可以分析用户发送过来的数据来决定需要做出什么样的回复
			$content = "你好,欢迎关注途老大微信公众号";
			$type = 'text'; //回复消息的类型
			/*响应当前请求（自动回复）*/
			$weChat->replyText($content,$type);
		}
	}

	// 微信公众平台配置页面
	public function index(){
		if(IS_POST){
			$data=I("post.");
			$res=M("system")->where("id=1")->save($data);
			if ($res>0){
				$this->ajaxReturn(['code'=>'200','msg'=>'更新配置成功','redirect' => U('weixin/index')]);
			} else {
				$this->ajaxReturn(['code'=>'500','msg'=>'更新配置失败','data'=>$data]);
			}
		}else{
		$re = M("system")->where(['id'=>1])->find();
		$this->assign(['re'=>$re]);
		$this->display();
		}
	}

	/*
	 * 自动回复
	 * 1、关键字过滤回复,关键字关联预定义事件
	 * 2、关注回复
	 */
	public function autoReply(){
		$this->display();
	}

	/**
	 * 微信自定义菜单
	 * 1、表名称weixin_menu
	 * 2、主菜单最多为3项
	 * 3、二级菜单最多为5项
	 */
	public function menu(){
		$redirect_url = $_SERVER["REQUEST_URI"];
		cookie('redirect_url',$redirect_url);
		$list = M('WeixinMenu')->order('path')->select();
		$this->assign(['list'=>$list]);
		$this->display();
	}

	/**
	 * 编辑微信菜单
	 */
	public function editMenu(){
		$map['id'] = I('id');
		if(IS_POST){
			$data = $_POST;
			if(empty($data['title'])){
				$this->ajaxReturn(['status'=>'error','info'=>"菜单名称不能为空"],'json');
			}
//			if(empty($data['menu_value'])){
//				$this->ajaxReturn(['code'=>'400','msg'=>"菜单值不能为空"],'json');
//			}

			if(empty($data['id'])){
				$pid = I('pid');
				if($pid== '0'){
					$count = M('WeixinMenu')->where(['pid'=>'0'])->count();
					if($count>3){
						$this->ajaxReturn(['status'=>'error','info'=>'主菜单只能有3个'],'json');
					}
				}
				$data['create_time'] = time();
				$result = M('WeixinMenu')->add($data);
				if($data['pid'] != 0){
					$path = '0,'.$data['pid'].','.$data['id'];
				}else{
					$path = $data['pid'].','.$data['id'];
				}
				M('WeixinMenu')->where(['id'=>$result])->save(['path'=>$path]);
				$action = '添加';
			}else{
				if($data['pid'] != 0){
					$path = '0,'.$data['pid'].','.$data['id'];
				}else{
					$path = $data['pid'].','.$data['id'];
				}
				$data['path'] = $path;
				$result = M('WeixinMenu')->where(['id'=>$data['id']])->save($data);
				$action = '编辑';
			}
			if($result !==false){
				$this->ajaxReturn(['status'=>'ok','info'=>$action.'微信菜单成功!','url'=>cookie('redirect_url')],'json');
			}else{
				$this->ajaxReturn(['status'=>'error','info'=>$action.'微信菜单失败!'],'json');
			}
		}else{
			//获取一级菜单列表(共三个)
			$wmenu = M('WeixinMenu')->where(['pid'=>0])->select();
			$this->assign(['wmenu'=>$wmenu]);
			if(!empty($map['id'])){
				$re = M('WeixinMenu')->where($map)->find();
				$this->assign(['d'=>$re]);
			}
			$this->display();
		}
	}


	/**
	 * 删除菜单项
	 * 如果是一级菜单则将其子菜单也一起删除
	 */
	public function delMenuItem(){
		$map['id'] = I('id');
		$pid = (int)I('pid');
		if($pid == 0){
			M('WeixinMenu')->where(['pid'=>$map['id']])->delete();
		}
		$res = M('WeixinMenu')->where($map)->delete();
		if ($res){
			$this->ajaxReturn(['code'=>'200','msg'=>'删除微信菜单项成功']);
		} else {
			$this->ajaxReturn(['code'=>'500','msg'=>'删除微信菜单项失败']);
		}
	}

	/**
	 * 生成新菜单到微信公众号
	 */
	public function pushRemoteMenu(){
		$weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
		$top = M('WeixinMenu')->where(['pid'=>0])->order("sort asc")->limit(3)->select();
		$buttons = [];
		foreach ($top as $key=>$val){
			if ($val['menu_type'] == 'menu') {
				$items	= M('WeixinMenu')->where(['pid'=>$val['id']])->order('path')->limit(5)->select();
				$subs	= [];
				foreach ($items as $k=>$v){
					if ($v['menu_type'] == 'click'){
						$subs[] = [
							'type'	=> 'click',
							'name'	=> $v['title'],
							'key'	=> $v['menu_value']
						];
					} elseif ($v['menu_type'] == 'view') {
						$subs[] = [
							'type'	=> 'view',
							'name'	=> $v['title'],
							'url'	=> $v['menu_value']
						];
					}
				}

				$buttons[] = [
					'name'			=> $val['title'],
					'sub_button'	=> $subs
				];
			} elseif ($val['menu_type'] == 'click') {
				$buttons[] = [
					'type'	=> 'click',
					'name'	=> $val['title'],
					'key'	=> $val['menu_value']
				];
			} elseif ($val['menu_type'] == 'view') {
				$buttons[] = [
					'type'	=> 'view',
					'name'	=> $val['title'],
					'url'	=> $val['menu_value']
				];
			}
		}
		$res = $weixin->menuCreate($buttons);
		if ($res['errmsg'] == 'ok') {
			$this->ajaxReturn(['code'=>'200','button'=>$buttons,'msg'=>'创建微信菜单成功']);
		} else {
			$this->ajaxReturn(['code'=>'500','button'=>$buttons,'msg'=>$res['errmsg']]);
		}
	}

	/**
	 * 获取微信公众号当前菜单
	 */
	public function pullRemoteMenu(){
		$weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
		$menu = $weixin->menuGet();
		print_r($menu);
	}

	/**
	 * 自定义事件
	 */
	public function event(){
		$this->display();
	}

	/**
	 * 微信会员以及分组
	 */
	public function member(){
		$count = M('WeixinMember')->count();
		if (empty($num)){
			$num = 10;
		}
		$p = getpage($count,$num);
		$list = M('WeixinMember')->order("id desc")->limit($p->firstRow,$p->listRows)->select();
		$this->assign(['list'=>$list,'page'=>$p->show()]);
		$this->display();
	}

	// 获取微信会员
	public function getMember(){
		// 用户后台运行
		ignore_user_abort(true);
		set_time_limit(0);
		$weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
		$result = $weixin->userGet();
		foreach ($result['data']['openid'] as $key=>$val){
			$user = $weixin->getUserInfo($val);
			$m = M('WeixinMember')->where(['openid'=>$val])->find();
			if (empty($m)){
				M('WeixinMember')->add($user);
			} else {
				M('WeixinMember')->where(['openid'=>$val])->save($user);
			}
			sleep(2);
		}

		exit('1');
	}

	/**
	 * 素材管理
	 * 1、视频素材
	 * 2、音频素材
	 * 3、图片素材
	 * 4、图文素材
	 */
	public function material(){
		$this->display();
	}

	/**
	 * 生成永久二维码图片
	 */
	public function createQrcode(){
		$id		= I('id');
		$title	= I("title");
		$weixin = new WechatAuth($this->system['appid'], $this->system['appsecret']);
		$qrcode = $weixin->qrcodeCreate($id, 0);
		if(isset($qrcode['ticket'])){
			$data['ticket']		= $qrcode['ticket'];
			$data['qrcode_url']	= $qrcode['url'];
			// 获取并保存二维码
			$url 		= $weixin->showqrcode($data['ticket']);
			$codestr 	= file_get_contents($url);
			$filename	= md5($id.$title).'.png';
			$dir		= SITE_PATH.'/public/qrcode/';
			file_put_contents($dir.$filename, $codestr);
			$data['qrcode']	= '/public/qrcode/'.$filename;
			$result	= M("salesman")->where(['id'=>$id])->save($data);
			if($result){
				$this->ajaxReturn(['code'=>'200','msg'=>'生成二维码成功!','qrcode'=>$data['qrcode']]);
			} else {
				// 记录错误日志
				$this->ajaxReturn(['code'=>'500','msg'=>'二维码入库错误']);
			}
		} else {
			$this->ajaxReturn(['code'=>500,'msg'=>'生成永久二维码失败']);
		}
	}

	/**
	 *定义空方法
	 */
	public function _empty(){
		$this->redirect('Public/error');
	}

	public function test1(){
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxf2a5326e7765eab1&secret=b9f2b211368032825dea968f5f295c21&code=021mojs8005RMK1q2yt80aEns80mojs7&grant_type=authorization_code";
		ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; SV1; .NET CLR 1.1.4322)');
		$result = file_get_contents($url);
		$arr = json_decode($result,true);
		var_dump($arr);
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, "60");
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		var_dump($data);

	}
}