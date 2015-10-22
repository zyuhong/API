<?php
/**
 * 上传壁纸接口
 */
session_start();

header('content-type:text/html;charset=utf-8');
header ("Cache-Control: no-cache, must-revalidate");
require_once ('public/public.php');
require_once 'tasks/CoolXiu/Wallpaper.class.php';
require_once ('tasks/CoolXiu/CoolXiuDb.class.php');
require_once ('tasks/CoolXiu/CoolXiuFile.class.php');
require_once ('lib/WriteLog.lib.php');

try {	
	if (!isset($_SESSION['valid_user']) 
		&& !isset($_POST['userName'])){
		echo get_rsp_result(UPLOAD_FILE_ERR_LOGIN);
		exit;
	}
	
	if(!isset($_POST['wallname'])){
		echo get_rsp_result(UPLOAD_FILE_ERR_NAME);
		exit;
	}
	
/*	$wp_name 	= $_POST['wallname'];
	$wp_note 	= $_POST['note'];
	$wp_type 	= $_POST['reqType'];
 	$wp_width 	= $_POST['Width'];
	$wp_height 	= $_POST['Height'];
*/
	$wp_name   = isset($_POST['wallname'])?$_POST['wallname']:"";
	$s_author 	= (isset($_SESSION['valid_user']))?$_SESSION['valid_user']:$_POST['userName'];
	
	$wp_note   = isset($_POST['note'])?$_POST['note']:"";
	$wp_type   = (int)isset($_POST['reqType'])?$_POST['reqType']:7;
	$wp_ratio   = (int)isset($_POST['WH'])?$_POST['WH']:0;
	
//	$wp_width  = isset($_POST['Width'])?$_POST['Width']:"";
//	$wp_height = isset($_POST['Height'])?$_POST['Height']:"";
	
	$input_wp 	= "files";
	$error 		= $_FILES[$input_wp]["error"];
	$f_name 	= "";
	$f_size 	= 0;
	$f_tmp_file = "";
	$type		= 0;
	
	$result = get_file_info($input_wp, -1, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("wallpaper:get_file_info() failed ERRO NO: ".$result, "log");
		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_INFO);
		exit;
	}
	
	$wallpaper = new Wallpaper();
	$cool_xiu_file = new CoolXiuFile();
	$coolxiu_db = new CoolXiuDb();

	$result = $coolxiu_db->connectMySqlCommit();
	if(!$result){
		Log::write("wallpaper::connectMySqlCommit() InitMySql failed!", "log");
		echo get_rsp_result(UPLOAD_FILE_ERR_DB_CONN);
		exit;//return false;//返回错误代码 206：数据库链接失败
	}
	$identity = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
	$cpid = sprintf("%u", crc32($f_name));
	
	$wallpaper->setCoolXiuParam($identity, $cpid, $s_author, $wp_name, $f_size, $wp_note, $wp_type, $wp_ratio);		
	$result = $wallpaper->upload($cool_xiu_file, $error, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("wallpaper->upload() failed ERRO NO: ".$result, "log");
		echo get_rsp_result(UPLOAD_FILE_ERR_FILE_UPLOAD);
		exit;//记录失败并通知客户端
	}		
	
	//录入数据库
	$result = $coolxiu_db->setCoolXiu2DB(COOLXIU_TYPE_WALLPAPER, $wallpaper, null);
	if(!$result){
		Log::write("wallpaper::setCoolXiu2DB() failed", "log");
		echo get_rsp_result(UPLOAD_FILE_ERR_DB);
		exit;//记录失败并通知客户端
	}
	echo get_rsp_result(1);
}catch (Exception $e){
		echo get_rsp_result(UPLOAD_FILE_ERR_EXCPTION);
		exit;
	}

	