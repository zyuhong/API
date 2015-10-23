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

require_once ('tasks/Font/FontFile.class.php');
require_once ('tasks/Font/FontDb.class.php');
require_once ('tasks/Font/FontPreview.class.php');
require_once ('lib/WriteLog.lib.php');
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

try {
	$id = isset($_POST['id'])?$_POST['id']:'';
	if(!isset($_POST['id'])){
		$_msg =  "ID不能为空";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	
	$folder 	=	isset($_POST['folder'])?$_POST['folder']:''; 
	$width   	=	isset($_POST['width'])?$_POST['width']:'';
	$height 	= 	isset($_POST['height'])?$_POST['height']:'';
	//font文件上传
	$preview = previewsUpload($id, $folder, $width, $height);
	
	//录入数据库
	$font_db = new FontDb();
	$font_db->setPreview($preview);
	$result = $font_db->addFontPreview();
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

function previewsUpload($id, $folder, $width, $height)
{
	$input 		= "preview";
	$error = $_FILES[$input]['error'];
	$f_preview_name = '';
	$f_preview_size 		= 0;
	$f_preview_tmp_file 	= "";
	$preview_url 	= '';
	$preview_md5 	= '';

	$result = get_file_info($input, -1, $f_preview_name, $f_preview_size, $f_preview_tmp_file);
	if(!$f_preview_name){
		$_msg =  "预览文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	if($result != UPLOAD_ERR_OK){
		Log::write("fontupload::get_file_info(preview) failed ERRO NO: ".$result, "log");
		//			echo get_rsp_result(UPLOAD_FILE_ERR_FILE_INFO);
		$_msg =  "预览文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	
	$font_file = new FontFile();
	$font_file->setFolder('..'.$folder);
	$f_name = $width.'x'.$height.substr(strrchr($f_preview_name,"."), 0);
	
	$result = $font_file->uploadPreview($error, $f_preview_size, $f_preview_tmp_file,
			$f_name, $preview_url, $preview_md5);
	if($result != UPLOAD_ERR_OK){
		Log::write("fontupload::uploadPreview(Preview) failed ERRO NO: ".$result, "log");
		//			echo get_rsp_result(UPLOAD_FILE_ERR_FILE_UPLOAD);
		$_msg =  "预览文件上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}

	$preview = new FontPreview();
	$preview->setPreview($id, $width, $height, $f_name, $preview_url, $f_preview_size, $preview_md5);
	
	return $preview;
}

?>