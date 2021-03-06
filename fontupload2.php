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

try {
// 	if(!isset($_POST['title'])){
// 		$_msg =  "名字不能为空";
// 		echo "<script>window.parent.Finish('".$_msg."');</script>";
// 		exit;
// 	}

	$name   	=	isset($_POST['title'])?$_POST['title']:'';
	$language  	= 	isset($_POST['language'])?$_POST['language']:'';
	$author  	= 	isset($_POST['author'])?$_POST['author']:'';
	$designer  	= 	isset($_POST['designer'])?$_POST['designer']:'';
	$version  	= 	isset($_POST['version'])?$_POST['version']:'';
	$uiversion  = 	isset($_POST['uiversion'])?$_POST['uiversion']:'';
	$note		= 	isset($_POST['note'])?$_POST['note']:'';

	//font文件上传
	$input 		= 'fontFile';
	$f_name 	= "";
	$f_size 	= "";
	$f_tmp_file = "";
	
	$result = get_file_info($input, -1, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("fontUpload:get_file_info(font) failed ERRO NO: ".$result, "log");
		//		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_INFO);
		$_msg =  "字体文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}	

	$font_url	= '';
	$font_md5	= '';
	$font_size	= 0;
	$id			= '';
	
	$font_file = new FontFile();
	fontUpload($font_file, $language, $font_url, $font_size, $font_md5, $id);
	$arrPreview = previewsUpload($font_file, $id);
	
	$font = new Font();
	
// 	$f_type = substr(strrchr($f_name,"."), 1);
// 	$f_name = $name.".".$f_type;
	
	$font->setFont($id, $language, $name, $note, $author, $designer, $version, $uiversion,
					$f_name, $font_url, $font_size, $font_md5, 'admin');
	$font->setPreviews($arrPreview);
	
	//录入数据库
	$font_db = new FontDb();
	$font_db->setFont($font);
	
	$result = $font_db->insertFont2DB();
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

function previewsUpload($font_file, $id)
{
	$input = 'priview';
	$f_preview_name = '';
	$f_preview_size 		= 0;
	$f_preview_tmp_file 	= "";
	$preview_url 	= '';
	$preview_md5 	= '';
	$error 		= $_FILES[$input]["error"];
	
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

	$result = $font_file->uploadPreview($error, $f_preview_size, $f_preview_tmp_file,
										$f_preview_name, $preview_url, $preview_md5);
	if($result != UPLOAD_ERR_OK){
		Log::write("fontupload::uploadPreview(Preview) failed ERRO NO: ".$result, "log");
		//			echo get_rsp_result(UPLOAD_FILE_ERR_FILE_UPLOAD);
		$_msg =  "预览文件上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	$width = 540;
	$height = 960;
	$arrPreview = array();
	$preview = new FontPreview();
	$preview->setPreview($id, $width, $height, $f_preview_name, $preview_url, $f_preview_size, $preview_md5);
	array_push($arrPreview, $preview);

	return $arrPreview;
}

function fontUpload($font_file, $language, &$font_url, &$font_size, &$font_md5, &$id)
{
	//font上传
	$input		= "fontFile";
	$error 		= $_FILES[$input]["error"];
	$f_name 	= "";
	$f_tmp_file = "";
	
	$result = get_file_info($input, -1, $f_name, $font_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("fontUpload:get_file_info(font) failed ERRO NO: ".$result, "log");
		//		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_INFO);
		$_msg =  "字体文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}

	//上传字体
	$result = $font_file->fontUpload($error, $language, 
									 $f_name, $font_size, $f_tmp_file,
									 $font_url, $font_md5, $id);
	if($result != UPLOAD_ERR_OK){
		Log::write("fontUpload->fontUpload() failed ERRO NO: ".$result, "log");
		//		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_UPLOAD);
		$_msg =  "字体文件上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	return true;
}

?>