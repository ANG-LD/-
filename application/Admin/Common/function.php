 <?php
function getUser(){
	$user = session('user');
	return $user['id'];
}

function format($arr){
	empty($arr) && $arr = [];
	$result = [
		'status'    => '1',
		'info'      => 'success',
		'data'      => [
			'code'      => '1',
			'info'      => 'success',
			'msgData'   => $arr
		]
	];

	return;
}

function curl_post($url, $fields){   
	 $ch = curl_init();
	 curl_setopt($ch,CURLOPT_URL,$url);
	 curl_setopt($ch, CURLOPT_HEADER, 0);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	 $data = curl_exec($ch);
	 curl_close($ch);
	 return $data;
}
/** excel导入函数
 * @param $file
 * @return array
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function importExecl($file, $type='xls'){
	if(!file_exists($file)){
		return array("error"=>0,'message'=>'file not found!');
	}
	Vendor("PHPExcel.PHPExcel.IOFactory");
	$excel_type = in_array($type,['xls','xlt']) ? 'Excel5' : 'Excel2007';
	$objReader = PHPExcel_IOFactory::createReader($excel_type);
	try{
		$PHPReader = $objReader->load($file);
	}catch(Exception $e){}
	if(!isset($PHPReader)) return array("error"=>0,'message'=>'read error!');
	$allWorksheets = $PHPReader->getAllSheets();
	$i = 0;
	foreach($allWorksheets as $objWorksheet){
		$sheetname=$objWorksheet->getTitle();
		$allRow = $objWorksheet->getHighestRow();//how many rows
		$highestColumn = $objWorksheet->getHighestColumn();//how many columns
		$allColumn = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$array[$i]["Title"] = $sheetname;
		$array[$i]["Cols"] = $allColumn;
		$array[$i]["Rows"] = $allRow;
		$arr = array();
		$isMergeCell = array();
		foreach ($objWorksheet->getMergeCells() as $cells) {//merge cells
			foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
				$isMergeCell[$cellReference] = true;
			}
		}
		for($currentRow = 1 ;$currentRow<=$allRow;$currentRow++){
			$row = array();
			for($currentColumn=0;$currentColumn<$allColumn;$currentColumn++){
				$cell =$objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
				$afCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn+1);
				$bfCol = PHPExcel_Cell::stringFromColumnIndex($currentColumn-1);
				$col = PHPExcel_Cell::stringFromColumnIndex($currentColumn);
				$address = $col.$currentRow;
				$value = $objWorksheet->getCell($address)->getValue();
				if(substr($value,0,1)=='='){
					return array("error"=>0,'message'=>'can not use the formula!');
					exit;
				}
				if($cell->getDataType()==PHPExcel_Cell_DataType::TYPE_NUMERIC){
					$cellstyleformat=$cell->getParent()->getCacheData( $cell->getCoordinate() )->getFormattedValue();
					if (preg_match('/^([0-9A-F]*-[0-9A-F]*-[0-9A-F]*)/i', $cellstyleformat)) {
						$value=gmdate("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($value));
					}
					/*else{
						$value=PHPExcel_Style_NumberFormat::toFormattedString($value,$cellstyleformat);
						}*/
				}
				$temp = '';
				if($isMergeCell[$col.$currentRow]&&$isMergeCell[$afCol.$currentRow]&&!empty($value)){
					$temp = $value;
				}elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$col.($currentRow-1)]&&empty($value)){
					$value=$arr[$currentRow-1][$currentColumn];
				}elseif($isMergeCell[$col.$currentRow]&&$isMergeCell[$bfCol.$currentRow]&&empty($value)){
					$value=$temp;
				}
				$row[$currentColumn] = $value;
			}
			$arr[$currentRow] = $row;
		}
		$array[$i]["Content"] = $arr;
		$i++;
	}
	//	spl_autoload_register(array('Think','autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
	unset($objWorksheet);
	unset($PHPReader);
	unset($PHPExcel);
	unlink($file);
	return array("error"=>1,"data"=>$array);
}

function xls($arr1,$arr2,$arr3){
	header("Content-type:application/vnd.ms-excel");
	header("Content-Disposition:filename=data.xls");
	for($i=0;$i<count($arr3);$i++) {
		echo $arr3[$i]."\t";
	}
	echo "\n";
	for($i=0;$i<count($arr1);$i++) {
		for($j=0;$j<count($arr2);$j++) {
			if(strstr("$arr2[$j]","time"))
				if($arr1[$i][$arr2[$j]]>0)
				echo date("Y-m-d h:i:s",$arr1[$i][$arr2[$j]])."\t";
					else
						echo " \t";
			else
			echo $arr1[$i][$arr2[$j]]."\t";
		}
		echo "\n";
	}
}

/** excel导出函数
 * @param $expName
 * @param $expTitle
 * @param $expCellName
 * @param $expTableData
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function exportExcel($expName,$expTitle,$expCellName,$expTableData){
	//	$xlsName = iconv('utf-8', 'gb2312', $expName);//文件名称
	$fileName = $expName.date('_YmdHis');//or $xlsName 文件名称可根据自己情况设定
	$cellNum = count($expCellName);
	$dataNum = count($expTableData);
	vendor("PHPExcel.PHPExcel");
	$objPHPExcel = new PHPExcel();
	$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

	$objPHPExcel->getActiveSheet()->setTitle($expTitle);
	$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
	for($i=0;$i<$cellNum;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
	}
	// Miscellaneous glyphs, UTF-8
	for($i=0;$i<$dataNum;$i++){
		for($j=0;$j<$cellNum;$j++){
			if($expCellName[$j][0] == 'status') {
				if($expTableData[$i][$expCellName[$j][0]] == 3){
					$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), "待预付");
				} elseif ($expTableData[$i][$expCellName[$j][0]] == 5) {
					$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), "待全付");
				} else {
					$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), date("Y-m-d H:i",$expTableData[$i][$expCellName[$j][0]]));
				}
			}
			if (stripos($expCellName[$j][0], "time") > 0) {
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), date("Y-m-d H:i",$expTableData[$i][$expCellName[$j][0]]));
			} else {
				$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
			}
		}
	}

	header('pragma:public');
	header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$expName.'.xls"');
	header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}
/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param string $path 缓存路径
 * @return mixed
 */
function set_config($name, $value='', $path=DATA_PATH) {
    static $_cache  = array();
    $filename       = $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 删除缓存
            return false !== strpos($name,'*')?array_map("unlink", glob($filename)):unlink($filename);
        } else {
            // 缓存数据
            $dir            =   dirname($filename);
            // 目录不存在则创建
            if (!is_dir($dir))
                mkdir($dir,0755,true);
            $_cache[$name]  =   $value;
            return file_put_contents($filename, strip_whitespace("<?php\treturn " . var_export($value, true) . ";?>"));
        }
    }
    if (isset($_cache[$name]))
        return $_cache[$name];
    // 获取缓存数据
    if (is_file($filename)) {
        $value          =   include $filename;
        $_cache[$name]  =   $value;
    } else {
        $value          =   false;
    }
    return $value;
}


/**
  +-----------------------------------------------------------------------------------------
 * 删除目录及目录下所有文件或删除指定文件
  +-----------------------------------------------------------------------------------------
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
  +-----------------------------------------------------------------------------------------
 * @return bool 返回删除状态
  +-----------------------------------------------------------------------------------------
 */
function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}

/**
 *验证视图权限
 */
function check_auth(){
	$check = I('check');
	if(!empty($check)){
		$Auth = new \Think\Auth();
		$name = CONTROLLER_NAME . '/' . ACTION_NAME;
		//当前用户id
		$user = session('user');
		//分类
		$type = CONTROLLER_NAME;
		//执行check的模式
		$mode = 'url';
		//'or' 表示满足任一条规则即通过验证;
		//'and'则表示需满足所有规则才能通过验证
		$relation = 'or';
		if($user['username'] !=='admin') {
				if (!$Auth->check($name, $user['id'], $type, $mode, $relation)) {
					echo json_encode(array('status'=>'cannot','info'=>'你没有权限,无法进行操作!'));
					exit;
				}else{
					echo json_encode(array('status'=>'ok'));
					exit;
				}
		}else{
			echo json_encode(array('status'=>'ok'));
			exit;
		}
	}

}

/**
 *操作记录时新增日志
 */
function work_log($table,$record_id,$type,$work){
	$data['table'] = $table;
	$user = session('user');
	$data['user_id'] = $user['id'];
	$data['type'] = $type;   //判断是修改那类型。
	$data['record_id'] = $record_id;
	$data['title'] = $work;
	$data['intime'] = date("Y-m-d H:i:s",time());
	M('WorkLog')->add($data);
}



	