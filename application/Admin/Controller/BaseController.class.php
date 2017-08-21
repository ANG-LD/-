<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Auth;
class BaseController extends CommonController{
	public $user = array();
	public function _initialize(){
		
		$nums = array("1"=>"10","2"=>"20","3"=>"30","4"=>"50","5"=>"100","6"=>"150","7"=>"200","8"=>"300","9"=>"500");
	    $this->assign('nums',$nums);
	    
		header("Content-type: text/html; charset=utf-8");
		header("Access-Control-Allow-Origin: *");
		$this->user = session('user');
		if (empty($this->user)){
			$this->redirect('Public/login');
		}
        //权限控制
        if($this->user['id'] != 1){
            $rules_name = session('rules_'.$this->user['id']);
            if(!$rules_name){
                $rule_str = M('AuthGroupAccess')->alias('aga')
                    ->join('left join __AUTH_GROUP__ ag on aga.group_id=ag.id')
                    ->where(['aga.uid'=>$this->user['id']])
                    ->getField('rules');
                $rules_name = M('AuthRule')->where(['id'=>array('IN', $rule_str)])->getField('id,name');
                session('rules_'.$this->user['id'], $rules_name);
            }
            //如果是微信或配置模块,只要有能访问首页的权限则拥有整个模块操作权限
//            if(
//                (CONTROLLER_NAME == 'Weixin' && !in_array(CONTROLLER_NAME.'/index', $rules_name)) ||
//                (CONTROLLER_NAME == 'Config' && !in_array(CONTROLLER_NAME.'/index', $rules_name)) ||
//                !in_array(CONTROLLER_NAME.'/'.ACTION_NAME, $rules_name)
//            ){
//                $this->error('权限不足!');
//            }
        }
		$m_name = M('AuthRule')->where(['name'=>CONTROLLER_NAME.'/index', 'pid'=>0])->getField('title');
		$system = M("system")->where(['id'=>1])->find();
		$url = session('url');
		$this->assign(['system'=>$system,'m_name'=>$m_name,'user'=>$this->user,'url'=>$url]);
	}
	
	protected function page($name, $options=[]){
		$model = M($name); // 实例化User对象
		$map = isset($options['map']) ? $options['map'] : [];
		
		// 设置搜索
		$select = I("select");
		if(!empty($select)){
			$field = explode(':', $select);
			$text = I('text');
			if ($field[1] == 'eq'){
				$map[$field[0]] = $text;
			} elseif ($field[1] == 'like') {
				$map[$field[0]] = ['like', "%{$text}%"];
			} elseif ($field[1] == 'egt') {
				$map[$field[0]] = ['egt', $text];
			}
		}
		
		$order = isset($options['order']) ? $options['order'] : '';
		$display = isset($options['display']) ? $options['display'] : 10;
		 
		if (empty($order)){
			$list = $model->where($map)->page($_GET['p'], $display)->select();
		} else {
			$list = $model->where($map)->order($order)->page($_GET['p'], $display)->select();
		}
		
		$this->assign('list',$list); // 赋值数据集
		$count	= $model->where($map)->count(); // 查询满足要求的总记录数
		$Page	= new \Think\Page($count, $display); // 实例化分页类 传入总记录数和每页显示的记录数
		if (isset($options['header'])){
			$Page->setConfig('header', $options['header']);
		} else {
			$Page->setConfig('header','<li><a class="num">共%TOTAL_ROW%条记录</a></li>');	
		}
		$show	= $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
	}
	
	// 带返回值的分页
	protected function page2($name, $options=[]){
		$model = M($name); // 实例化User对象
		$map = isset($options['map']) ? $options['map'] : 1;
		$order = isset($options['order']) ? $options['order'] : '';
		$display = isset($options['display']) ? $options['display'] : 10;
		$p	= isset($_GET['p']) ? $_GET['p'] : 0;
		$list = $model->where($map)->order($order)->page($p, $display)->select();
		$count	= $model->where($map)->count(); // 查询满足要求的总记录数
		$Page	= new \Think\Page($count, $display); // 实例化分页类 传入总记录数和每页显示的记录数
		$Page->setConfig('header', '<li><a class="num">共%TOTAL_ROW%条记录</a></li>');
		
		$show	= $Page->show(); // 分页显示输出
		$this->assign('page',$show); // 赋值分页输出
		return $list;
	}

	public function  upimg($names,$dir){
		if ($_FILES[$names]["size"] < 20000000) {
			if ($_FILES[$names]["error"] > 0) {
				$data["error"] = $_FILES[$names]["error"];
			} else {
				$name = $dir . date("ymd", time()) . rand(100000, 999999) .$_FILES[$names]["name"];
				if (file_exists($name)) {
					$data["error"] = " already exists. ";
				} else {
					move_uploaded_file($_FILES[$names]["tmp_name"], $name);
				}
			}
			if ($_FILES[$names]["name"] != null) {
				return $name;
			}else{
				return " ";
			}
		}else{
			return " ";
		}
	}

	public  function  page3($count,$display){
		$Page	= new \Think\Page($count, $display); // 实例化分页类 传入总记录数和每页显示的记录数
		if (isset($options['header'])){
			$Page->setConfig('header', $options['header']);
		} else {
			$Page->setConfig('header','<li><a class="num">共%TOTAL_ROW%条记录</a></li>');
		}
		$show= $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
	}

	public  function  page4($count,$display){
		$Page	= new \Think\Page($count, $display); // 实例化分页类 传入总记录数和每页显示的记录数
		if (isset($options['header'])){
			$Page->setConfig('header', $options['header']);
		} else {
			$Page->setConfig('header','<li><a class="num">共%TOTAL_ROW%条记录</a></li>');
		}
		$show= $Page->show();// 分页显示输出
		$this->assign('page2',$show);// 赋值分页输出
	}

	protected function getpage($count, $pagesize) {
		//$p = new Think\Page($count, $pagesize);
		$p=new \Think\Page($count,$pagesize);
		$p->setConfig('header', '<li><a class="num">共%TOTAL_ROW%条记录</a></li>');
		$p->setConfig('prev', '上一页');
		$p->setConfig('next', '下一页');
		$p->setConfig('last', '末页');
		$p->setConfig('first', '首页');
		$p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
		$p->lastSuffix = false;//最后一页不显示为总页数
		return $p;
	}

	/**
	 *定义空方法
	 */
	public function _empty(){
		$this->redirect('Public/error');
	}

	protected function gaussian_blur($srcImg,$savepath=null,$savename=null,$blurFactor=3){
		$gdImageResource=$this->image_create_from_ext($srcImg);
		$srcImgObj=$this->blur($gdImageResource,$blurFactor);
		$temp = pathinfo($srcImg);
		$name = $temp['basename'];
		$path = $temp['dirname'];
		$exte = $temp['extension'];
		$savename = $savename ? $savename : $name;
		$savepath = $savepath ? $savepath : $path;
		$savefile = $savepath .'/'. $savename;
		$srcinfo = @getimagesize($srcImg);
		switch ($srcinfo[2]) {
			case 1: imagegif($srcImgObj, $savefile); break;
			case 2: imagejpeg($srcImgObj, $savefile); break;
			case 3: imagepng($srcImgObj, $savefile); break;
			default: return '保存失败'; //保存失败
		}

		return $savefile;
		imagedestroy($srcImgObj);
	}

	/**
	 * Strong Blur
	 *
	 * @param  $gdImageResource  图片资源
	 * @param  $blurFactor          可选择的模糊程度
	 *  可选择的模糊程度  0使用   3默认   超过5时 极其模糊
	 * @return GD image 图片资源类型
	 * @author Martijn Frazer, idea based on http://stackoverflow.com/a/20264482
	 */
	protected function blur($gdImageResource, $blurFactor = 3)
	{
		// blurFactor has to be an integer
		$blurFactor = round($blurFactor);

		$originalWidth = imagesx($gdImageResource);
		$originalHeight = imagesy($gdImageResource);

		$smallestWidth = ceil($originalWidth * pow(0.5, $blurFactor));
		$smallestHeight = ceil($originalHeight * pow(0.5, $blurFactor));

		// for the first run, the previous image is the original input
		$prevImage = $gdImageResource;
		$prevWidth = $originalWidth;
		$prevHeight = $originalHeight;

		// scale way down and gradually scale back up, blurring all the way
		for($i = 0; $i < $blurFactor; $i += 1)
		{
			// determine dimensions of next image
			$nextWidth = $smallestWidth * pow(2, $i);
			$nextHeight = $smallestHeight * pow(2, $i);

			// resize previous image to next size
			$nextImage = imagecreatetruecolor($nextWidth, $nextHeight);
			imagecopyresized($nextImage, $prevImage, 0, 0, 0, 0,
				$nextWidth, $nextHeight, $prevWidth, $prevHeight);

			// apply blur filter
			imagefilter($nextImage, IMG_FILTER_GAUSSIAN_BLUR);

			// now the new image becomes the previous image for the next step
			$prevImage = $nextImage;
			$prevWidth = $nextWidth;
			$prevHeight = $nextHeight;
		}

		// scale back to original size and blur one more time
		imagecopyresized($gdImageResource, $nextImage,
			0, 0, 0, 0, $originalWidth, $originalHeight, $nextWidth, $nextHeight);
		imagefilter($gdImageResource, IMG_FILTER_GAUSSIAN_BLUR);

		// clean up
		imagedestroy($prevImage);

		// return result
		return $gdImageResource;
	}

	protected function image_create_from_ext($imgfile)
	{
		$info = getimagesize($imgfile);
		$im = null;
		switch ($info[2]) {
			case 1: $im=imagecreatefromgif($imgfile); break;
			case 2: $im=imagecreatefromjpeg($imgfile); break;
			case 3: $im=imagecreatefrompng($imgfile); break;
		}
		return $im;
	}

	/**
	 * @发送短信
	 * @type 1:注册  2:找回密码
	 * Enter description here ...
	 */
	protected function send_SMS($mobile,$content)
	{
		function random($length = 6, $numeric = 0)
		{
			PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
			if ($numeric) {
				$hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
			} else {
				$hash = '';
				$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
				$max = strlen($chars) - 1;
				for ($i = 0; $i < $length; $i++) {
					$hash .= $chars[mt_rand(0, $max)];
				}
			}
			return $hash;
		}


		function xml_to_array($xml)
		{
			$reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
			if (preg_match_all($reg, $xml, $matches)) {
				$count = count($matches[0]);
				for ($i = 0; $i < $count; $i++) {
					$subxml = $matches[2][$i];
					$key = $matches[1][$i];
					if (preg_match($reg, $subxml)) {
						$arr[$key] = xml_to_array($subxml);
					} else {
						$arr[$key] = $subxml;
					}
				}
			}
			return $arr;
		}

		//发送验证码
		function Post($curlPost, $url)
		{
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_NOBODY, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
			$return_str = curl_exec($curl);
			curl_close($curl);
			return $return_str;
		}



		if (empty($mobile) || !preg_match('/^1[3|4|5|7|8]\d{9}$/', $mobile)) {
			echo json_encode(array('status'=>'error','info'=>'手机号错误'));
			die;
		}

		//用户密码 $password
		$account = 'tulaoda168';
		$password = 'Txb123456';
		$gateway = 'https://sapi.253.com/msg/HttpBatchSendSM?account=' . $account . '&pswd=' . $password . '&mobile=' . $mobile . '&msg=' . $content . '&needstatus=true';
		$result = curl_get($gateway);
		$result =preg_split("/[,\r\n]/",$result);
		if(isset($result[1])){
			return true;
		} else {
			echo json_encode(array('status'=>'error','info'=>'短信发送失败'));
			die;
		}
	}

	/**
 *判断机票订单锁定人
 */
	protected function check_airport_locker($order_id){
		$order = M('AirportOrder')->where(['order_id'=>$order_id])->find();
		$user = session('user');
		if($order['is_lock'] == '1' && $user['uname'] !=='admin'){
			echo json_encode(array('status'=>'cannot','info'=>'你没有权限,无法进行操作!'));
			exit;
		}else{
			if($order['locker_id'] == $user['id'] || $user['uname'] == 'admin'){
				echo json_encode(array('status'=>'ok'));
				exit;
			}else{
				echo json_encode(array('status'=>'cannot','info'=>'你没有权限,无法进行操作!'));
				exit;
			}
		}
	}

	/**
	 *判断酒店订单锁定人
	 */
	protected function check_hotel_locker($order_id){
		$order = M('MallOrder')->where(['id'=>$order_id])->find();
		$user = session('user');
		if($order['is_lock'] == '1' && $user['uname'] !=='admin'){
//			echo json_encode(array('status'=>'cannot','info'=>'你没有权限,无法进行操作!'));
			echo json_encode(array('status'=>'ok'));
			exit;
		}else{
//			if($order['locker_id'] == $user['id'] || $user['uname'] == 'admin'){
//				echo json_encode(array('status'=>'ok'));
//				exit;
//			}else{
//				echo json_encode(array('status'=>'cannot','info'=>'你没有权限,无法进行操作!'));
//				exit;
//			}
			echo json_encode(array('status'=>'ok'));
			die;
		}
	}

}