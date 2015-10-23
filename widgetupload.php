<?php
/**
 * Widget上传接口
*
* @author lijie1@yulong.com
*
*/
session_start();

header('content-type:text/html;charset=utf-8');
header ("Cache-Control: no-cache, must-revalidate");

require_once ('tasks/widget/WidgetFile.class.php');
require_once ('tasks/widget/WidgetDb.class.php');
require_once ('lib/WriteLog.lib.php');
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

try {
	if(!isset($_POST['cpid'])){
		$_msg =  "主题ID不能为空";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}

	$cpid   	=	isset($_POST['cpid'])?$_POST['cpid']:'';
	$width   	=	isset($_POST['width'])?$_POST['width']:'';
	$height  	= 	isset($_POST['height'])?$_POST['height']:'';
	$note		= 	isset($_POST['note'])?$_POST['note']:'';
	
	$input 		= "widgetfile";
	$error 		= $_FILES[$input]["error"];
	$f_name 	= "";
	$f_size 	= "";
	$f_tmp_file = "";
	
	$result = get_file_info($input, -1, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("widgetupload:get_file_info(font) failed ERRO NO: ".$result, "log");
		//		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_INFO);
		$_msg =  "字体文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}	
	$widgetFile = new WidgetFile();
	//上传主题Widget推荐图
	$ratio = $width.'x'.$height;
	$f_url	= '';
	$f_md5	= '';
	$crc    = '';
	
	$result = $widgetFile->widgetUpload($error, $cpid, $ratio, 
									    $f_name, $f_size, $f_tmp_file, 
										 $f_url, $f_md5, $crc);
	if($result != UPLOAD_ERR_OK){
		Log::write("widgetupload:widgetUpload() failed ERRO NO: ".$result, "log");
		//		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_UPLOAD);
		$_msg =  "字体文件上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	
	$widgetDb = new WidgetDb();
	$widgetTh = new WidgetThemes();
	$widgetTh->setWidgetTheme($crc, $cpid, $f_name, $width, $height, $f_url, $f_md5, $f_size, $note);
	$result = $widgetDb->insertThemes($widgetTh);
	if(!$result){
		$_msg = "上传失败";	//207：数据库插入数据失败
	}else{
		$_msg = "上传成功";	//0：上传成功
	}
	
	echo "<script>window.parent.Finish('".$_msg."');</script>";
}catch (Exception $e){
	$_msg = "发生异常，上传不成功";
	echo "<script>window.parent.Finish('".$_msg."');</script>";
	exit;
}

?>