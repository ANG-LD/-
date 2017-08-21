<?php

namespace Home\Widget;

use Think\Controller;

class CommonWidget extends Controller {
	/**
	 * 
	 * @param string $pid 省id
	 * @param string $cid 市id
	 * @param string $aid 区id
	 * @param string $pv 省值
	 * @param string $cv 市值
	 * @param string $av 区值
	 * {:W('Common/address',array('province','cityid','aid',$pv,$cv,$av))}
 	 * 参数可以为空，但必须写
	 */
	public function address($pid,$cid,$aid,$pv,$cv,$av){
		$P = D("Province");
		$C = D("City");
		$A = D("Area");
		$this->assign("pid",$pid);
		$this->assign("cid",$cid);
		$this->assign("aid",$aid);
		$this->assign("pv",$pv);
		$this->assign("cv",$cv);
		$this->assign("av",$av);
		
		$where["staus"]=1;
	
		$province = $P->where($where)->select();
		$this->assign("province",$province);
		if(empty($pv)){
			$where["pid"]=$province[0]["id"];
		}else{
			$where["pid"]=$pv;
		}
		
		$city = $C ->where($where)->select();
		
		$this->assign("city",$city);
		if(empty($cv)){
			$where["pid"]=$city[0]["id"];
		}else{
			$where["pid"]=$cv;
		}
		
		$area = $A ->where($where)->select();
		$this->assign("area",$area);
		
		$this->display("Widget:address");
	}
	public function jietu($data){
		if(empty($data["id"])){
			echo "使用方法 {:W(\"Jietu\",array(\"id\"=>\"touxiang\",\"width\"=>100,\"height\"=>100,\"max_width\"=>1000,\"max_height\"=>800)}";
			return;
		}
		return $this->renderFile('Jietu',$data);
		$this->display("Widget:jietu");
	}
	/**
	 * {:W("Common/uploadBigFile",array('id','bigfiles','a977706b86875117b8f23be465b65c60.png,你好.png;a977706b86875117b8f23be465b65c60.png,你好.png'))}
	* @param string $id  唯一id
	 * @param string $dir   bigfiles  文件上传目录  都在/Public/upload下
	 * @param string $data a977706b86875117b8f23be465b65c60.png,你好.png;a977706b86875117b8f23be465b65c60.png,你好.png
	 */
	public function uploadBigFile($id,$dir,$data){
		$this->assign("uuid",$id);
		if($data){
			$urls = explode(";", $data);
			$urls_new=array();
			for ($i=0;$i<countArray($urls);$i++){
				$tmpurls = explode(",",$urls[$i]);
				$file["url"]=$tmpurls[0];
				if(countArray($tmpurls)>1){
					$file["name"]=$tmpurls[1];
				}else{
					$file["name"]=$tmpurls[0];
				}
				array_push($urls_new, $file);
			}
			$this->assign("urls",$urls_new);
		}
		if($dir){
			$this->assign("dir",$dir);
		}else{
			$this->assign("dir","bigfiles");
		}
		$this->display("Widget:uploadBigFile");
	}
	/**
	 * {:W("Common/uploadImg",array(id','img','a977706b86875117b8f23be465b65c60.png;a977706b86875117b8f23be465b65c60.png'))}
	 * @param string $id  唯一id
	 * @param string $dir  img  文件上传目录  都在/Public/upload下
	 * @param string $data  a977706b86875117b8f23be465b65c60.png;a977706b86875117b8f23be465b65c60.png
	 */
	public function uploadImg($id,$dir,$data){
		$this->assign("uuid",$id);
		if($data){
			$urls = explode(";", $data);
			$this->assign("urls",$urls);
		}
		if($dir){
			$this->assign("dir",$dir);
		}else{
			$this->assign("dir","bigfiles");
		}
		$this->display("Widget:uploadImg");
	}
	public function uploadFile($data){
		$this->display("Widget:uploadFile");
	}
}