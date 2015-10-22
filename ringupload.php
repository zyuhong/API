<?php
/**
 * 上传壁纸接口
 */
session_start();

header('content-type:text/html;charset=utf-8');
header ("Cache-Control: no-cache, must-revalidate");
require_once ('public/public.php');
require_once ('lib/WriteLog.lib.php');
require_once ('tasks/ring/RingDb.class.php');
require_once ('tasks/ring/RingFile.class.php');

try {	
	$name   = isset($_POST['ringName'])?$_POST['ringName']:"";
	$author = isset($_POST['author'])?$_POST['author']:"";	
	$note   = isset($_POST['note'])?$_POST['note']:"";
	$type   = (int)(isset($_POST['type'])?$_POST['type']:0);
	
	$input 	= "ringfile";
	$error 		= $_FILES[$input]["error"];
	
	$f_name 	= "";
	$f_size 	= 0;
	$f_tmp_file = "";
	$result = get_file_info($input, -1, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("ringupload::get_file_info() failed ERRO NO: ".$result, "log");
		$_msg = "上传失败";		
		echo "<script>window.parent.Finish('".$_msg."');</script>";	
		exit;
	}
	
	$ring = new RingDb();
	$ring_file = new RingFile();
	
	$url = "";
	$md5 = "";
	$id = "";
	$result = $ring_file->ringUpload($error, 
									$f_name, $f_size, $f_tmp_file, 
									$type, 
									$url, $md5, $id);
	if($result != UPLOAD_ERR_OK){
		Log::write("ringupload::ring_file->ringUpload() failed ERRO NO: ".$result, "log");
		$_msg = "上传失败";		
		echo "<script>window.parent.Finish('".$_msg."');</script>";	
		exit;
	}
	
	$ring->setRingParam($id, $type, $name, $note, $f_name, $url, $f_size, $md5, $author);		
	$result = $ring->insertRing2DB();
	if(!$result){
		Log::write("ringupload::ring->insertRing2DB() failed ", "log");
		echo get_rsp_result(UPLOAD_FILE_ERR_DB);
		exit;//记录失败并通知客户端
	}			
	$_msg = "上传成功";		
	echo "<script>window.parent.Finish('".$_msg."');</script>";	
	
}catch (Exception $e){
	echo get_rsp_result(UPLOAD_FILE_ERR_EXCPTION);
	exit;
}

	