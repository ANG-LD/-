<?php
/**
 * 本模块儿主要解决外部与系统的交互问题
 * 1、系统后台登陆
 * 2、微信公众平台响应
 * 3、每个模块儿接口的名字将以接口名称命名
 */
namespace Admin\Controller;
use Com\Wechat;
use Com\WechatAuth;
use Think\Controller;
class PublicController extends Controller{
	// Dit登陆系统登陆 
	public function login(){
		$system = M("system")->where(['id'=>"1"])->getField('title');
		if (IS_POST){
			$data = $_POST;
			if (empty($data['uname']) || empty($data['password'])){
				echo json_encode(['status'=>'error','info'=>'用户名或者密码不能为空','class'=>'']);
				return;
			}
			if(empty($data['verify_code'])){
				echo json_encode(['status'=>'error','info'=>'验证码不能为空','class'=>'']);
				return;
			}

			if(!check_verify($data['verify_code'])){
				echo json_encode(['status'=>'error','info'=>'验证码错误啦，请再输入吧','class'=>'']);
				return;
			}
			
			$data['password']	= encrypt($data['password']);
			$user = M('Member')->where(['username'=>$data['uname'],'password'=>$data['password']])->find();
			if (!empty($user)){
				$data['last_login_date'] = date("Y-m-d H:i:s");
				$data['last_login_ip'] = getIP();
				$user['login_times'] ++;
				$data['login_times'] = $user['login_times'];
				M('Member')->where(['id'=>$user['id']])->save($data);
				$group = M('AuthGroupAccess')->alias('a')
					->field("b.title")
					->join("LEFT JOIN __AUTH_GROUP__ b on a.group_id = b.id")
					->where(['a.uid'=>$user['id']])
					->find();
				unset($user['password']);
				$user['title'] = $group['title'];
				session('user',$user);
				echo json_encode(['status'=>'ok','info'=>$system.'管理系统登陆成功','url'=>U('Index/index')]);
				die;
			} else {
				echo json_encode(['status'=>'error','info'=>'用户名或者密码不正确','class'=>'']);
				die;
			}
			return;
		} else {
			layout(false);
			$user = session('user');
			if(!empty($user)){
				$this->redirect('Index/index');
			}
			$this->assign("system", $system);
			$this->display('Public/login-1');
		} 
	}
	
	public function logout(){
		//session(null);
		//unset($_SESSION['user']);
		session('user',null);
		$this->redirect('Public/login');
	}

	/**
	 *错误返回页面
	 */
	public function error(){
		layout(false);
		$this->display('Public/empty');
	}

	/**
	 *定义空方法
	 */
	public function _empty(){
		$this->redirect('Public/error');
	}

	/**
	 *验证码
	 */
	public function verify_code(){
//		import('Vendor.XSVerification');
//		$XSVerification = new \XSVerification();  // 加载类XSVerification.class.php
//		$data = $XSVerification->getOkPng();
//		$temp = array_chunk($data['data'],20);
//		$this->assign('left_pic',$temp[0]);
//		$this->assign('right_pic',$temp[1]);
//		$this->assign('pg_bg',$data['bg_pic']);
//		$this->assign('ico_pic',$data['ico_pic']);
//		$this->assign('y_point',$data['y_point']);
		$Verify = new \Think\Verify();
		$Verify->fontSize = 20;
		$Verify->length   = 4;
		$Verify->fontttf = '6.ttf';
		$Verify->codeSet = '0123456789';
		$Verify->entry();
	}

}