<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function del_img(){
		$url = I('url');
		return @unlink(rtrim(BASE_PATH, '/').$url);
	}

}