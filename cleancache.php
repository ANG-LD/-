<?php
if(isset($_GET['portal'])){
	$dirs = array('./data/portal');
}
if(isset($_GET['all'])){
	$dirs	=	array('./_runtime');
}else{
//缓存目录
	$dirs	=	array('./Application/Runtime');
}
//清理缓存
foreach($dirs as $value)
{
	rmdirr($value);

	echo "<div style='border:2px solid green; background:#f1f1f1; padding:20px;margin:20px;width:800px;font-weight:bold;color:green;text-align:center;'><!--\"".$value."\"--> 清除成功! </div> <br /><br />";

	@mkdir($value,0777,true);

}

function rmdirr($dirname) {

	if (!file_exists($dirname)) {
		return false;
	}

	if (is_file($dirname) || is_link($dirname)) {
		return unlink($dirname);
	}

	$dir = dir($dirname);

	while (false !== $entry = $dir->read()) {

		if ($entry == '.' || $entry == '..') {
			continue;
		}

		rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
	}

	$dir->close();

	return rmdir($dirname);
}
?>