<?php
namespace Api\Controller;
use Think\Controller;
use Org\Util\Rbac;
use Pingpp\Pingpp;
use Pingpp\Charge;
use Pingpp\Error\Base;
use Think\Log;
class CommonController extends Controller {
	
    public $loginMarked;
    public $url='';
    public $system='';
    /**
      +----------------------------------------------------------
     * 初始化
     * 如果 继承本类的类自身也需要初始化那么需要在使用本继承类的类里使用parent::_initialize();
      +----------------------------------------------------------
     */
    public function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
        header('Content-Type:application/json; charset=utf-8');
		header("Access-Control-Allow-Origin: *");
        //$systemConfig = include'./application/Common/Conf/systemConfig.php';
		$this->url = C('IMG_PREFIX');
		$this->system = M('System')->where(['id'=>1])->find();
		$text="\n\n".date("y-m-d H:i:s",time())."\n".$_SERVER["QUERY_STRING"]."\rpost:\r".var_export($_POST,true)."\rget:\r".var_export($_GET,true)."\rfile:\r".var_export($_FILES,true);
		file_put_contents("logo.txt", $text, FILE_APPEND);
        //$this->assign("site", $systemConfig);
    }
    
	public  function  page3($count,$display)
	{
		$Page	= new \Think\Page($count, $display); // 实例化分页类 传入总记录数和每页显示的记录数
		if (isset($options['header'])){
			$Page->setConfig('header', $options['header']);
		} else {
			$Page->setConfig('header','<li><a class="num">共%TOTAL_ROW%条记录</a></li>');
		}
		$show= $Page->show();// 分页显示输出
		return $show;
	}
 

    public function checkLogin() 
	{
		$map['user_token']=I('user_token');
		$map['uid']=I('uid');
		if(empty($map['user_token'])&& empty($map['uid']))
		{
			$msg=array('hmsg'=>array("0","请重新登录!"));
	        $this->MSG($msg);
		}
		$user=M('Member');
		$user_r=$user->where($map)->field('uid,user_token')->find();
		if(!$user_r['uid'])
		{
			$msg=array('hmsg'=>array("0","用户未被授权!"));
	        $this->MSG($msg);
		}
		return $user_r;
    }

    /**
      +----------------------------------------------------------
     * 验证token信息
      +----------------------------------------------------------
     */
    protected function checkToken() {
        if (IS_POST) {
            if (!M("Admin")->autoCheckToken($_POST)) {
                die(json_encode(array('status' => 0, 'info' => '令牌验证失败')));
            }
            unset($_POST[C("TOKEN_NAME")]);
        }
    }
 /**
  *获取数据产品列表 
  *@param tbname 数据库名称(必选) map(where条件(可选) field字段(选) order 排序 （可选） rows 条数（可选） offset 要输出数据的offset（可选）)
  *
  */
  public function getProductList($tbname,$map=array(),$order='id desc')
  {
	  $field=$map['field'];
	  if(empty($tbname))
	  {
		  $this->MSG(array('amsg'=>array('0','请选择数据库！')));
	  }
	  if(empty($field))
	  {
		  $field='*';
	  }
	
	  $m=M(ucfirst($tbname));
	  if($limit==1)
	  {
		  $list=$m->where($map)->order($order)->field($field)->limit(1)->find();
	  }else
	  {
		  if($map['rows']>0)
		  {
			  $list=$m->where($map)->order($order)->field($field)->limit($map['offset'],$map['rows'])->select();
			  
		  }else
		  {
			  $list=$m->where($map)->order($order)->field($field)->select();
		  }
	   	  
	  }
	  return $this->parse($list,'images');
  
  }
  
  // 解析数组
  public function parse($data,$field)
  {
	
	  $i=0;
	  foreach($data as $k=>$v)
	  {
		 $imageArray=array_filter(explode("|",$v[$field]));
	     $data[$i++][$field]=array_map(array($this,"MapImg"),$imageArray);
	  }
	  return $data;
   }
   
   // 解析字符串
  public function parseStr($data,$field)
  {
	
	  $i=0;
	  foreach($data as $k=>$v)
	  {
		 $strArray=array_filter(explode(PHP_EOL,$v[$field]));
	     $data[$i++][$field]=$strArray;
	  }
	  return $data;
   }
   
   // array_map回调方法
   public function MapImg($v,$k)
   {
	   $url=$this->url.__ROOT__;
	   return $url.$v;
   }
   
   
  /**
  *获取数据列表 
  *@param tbname 数据库名称(必选) map(where条件(可选) field字段(选) rows 条数（可选） offset 要输出数据的offset（可选）)
  *
  */
  public function getList($tbname,$map=array(),$order='id desc')
  {
	  $field=$map['field'];
	  if(empty($tbname))
	  {
		  $this->MSG(array('amsg'=>array('0','请选择数据库！')));
	  }
	  if(empty($field))
	  {
		  $field='*';
	  }
	
	  $m=M(ucfirst($tbname));
	  if(isset($map['rows'])&&$map['rows']==1)
	  {
		  $list=$m->where($map['where'])->order($order)->field($field)->limit(1)->find();
	  }else
	  {
		  if(isset($map['rows'])&&$map['rows']>1)
		  {
			  $list=$m->where($map['where'])->order($order)->field($field)->limit($map['offset'],$map['rows'])->select();
			  
		  }else
		  {
			  $list=$m->where($map['where'])->order($order)->field($field)->select();
		  }
	   	  
	  }
	  return $list;
  
  }
	public function arr_foreach ($arr) {
		 static $data;
		if (!is_array ($arr)) {
			if($arr==null)
				return "";
			else
				return $arr;
		}
		foreach ($arr as $key => $val ) {
			if (is_array ($val)) {
				$val=self::arr_foreach ($val);
			} else {
				if ($val == null)
					$val = "";
			}
				$data[]=$val;
		}
		return $data;
	}
 /**
  * 信息提示
  *@param amsg 后台提示(可选) hmsg 用户提示（可选） data 要输出数据的（可选）
  */
  public function MSG($config,$data="")
  {
	  if(empty($config['amsg']))
	  {
		  $config['amsg']=array('1','Success');
	  }
	  $config['hmsg'][0]=isset($config['hmsg'][0])?$config['hmsg'][0]:"0";
	  $config['hmsg'][1]=isset($config['hmsg'][1])?$config['hmsg'][1]:"";
	  if($data=="")
		  $msg=array
		  (
			  'status'=>$config['amsg'][0],
			  'info'=>$config['amsg'][1],
			  'data'=>array
			  (
				  'status'=>$config['hmsg']['0'],
				  'info'=>$config['hmsg'][1],
			  )
		  );
	  else
		  $msg=array
		  (
			  'status'=>$config['amsg'][0],
			  'info'=>$config['amsg'][1],
			  'data'=>array
			  (
				  'status'=>$config['hmsg']['0'],
				  'info'=>$config['hmsg'][1],
				  'msgData'=>$data,
			  )
		  );


	  echo json_encode($msg);
	  exit;
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

	public  function  urls($str)
	{
		if ($str == null || $str == "") {
			return "";
		} else {
			$att = strpos($str, "http");
			if ($att > -1) {
				return $str;
			} else {
				return "http://".$_SERVER['SERVER_NAME']."/" . $str;
			}
		}
	}

	//百度地图获取距离
	protected function getDistance($lat1, $lng1, $lat2, $lng2)
	{
		$earthRadius = 6367000; //approximate radius of earth in meters
		/*
        Convert these degrees to radians
        to work with the formula
        */

		$lat1 = ($lat1 * pi() ) / 180;
		$lng1 = ($lng1 * pi() ) / 180;

		$lat2 = ($lat2 * pi() ) / 180;
		$lng2 = ($lng2 * pi() ) / 180;

		/*
        Using the
        Haversine formula

        http://en.wikipedia.org/wiki/Haversine_formula

        calculate the distance
        */

		$calcLongitude = $lng2 - $lng1;
		$calcLatitude = $lat2 - $lat1;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
		$stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		$calculatedDistance = $earthRadius * $stepTwo;

		return round($calculatedDistance);
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
		$result = file_get_contents($gateway);
		$result =preg_split("/[,\r\n]/",$result);
		if(isset($result[1])){
			return true;
		} else {
			echo json_encode(array('status'=>'error','info'=>'短信发送失败'));
			die;
		}
	}

	public function _empty(){
		header('location:system.php?m=Admin&c=Public&a=error');
	}

}

