<?php
/**
 * 主题上传接口
 * 
 * @author lijie1@yulong.com
 *  
 */

session_start();

header('content-type:text/html;charset=utf-8');
header ("Cache-Control: no-cache, must-revalidate");

require_once 'tasks/CoolXiu/Preview.class.php';
require_once ('tasks/CoolXiu/CoolXiuDb.class.php');
require_once ('tasks/CoolXiu/CoolXiuFile.class.php');
require_once ('lib/WriteLog.lib.php');
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

try {
// 	if (!isset($_SESSION['valid_user'])){
// 		$_msg = "soory, 您还没有登录！";	
// 		echo "<script>window.parent.Finish('".$_msg."');</script>";		
// 		exit;
// 	}
	
	if(!isset($_POST['themeName'])){
		$_msg =  "主题名字不能为空";
		echo "<script>window.parent.Finish('".$_msg."');</script>";		
 		exit;
	}
	
	$s_author 	  = $_SESSION['valid_user'];
	
	//上传主题	
	$themes = new Themes();
	$cool_xiu_file = new CoolXiuFile();
	upload_theme($themes, $cool_xiu_file, $s_author);
	
	$arr_prevs = array();
	
	//上传浏览图
	$input_prev_br 	= "brFile";
	$theme_prev_url = "";
	$img_num 		= 0;
	$error 			= $_FILES[$input_prev_br]["error"];
	$arr_prevs = array();
	
	//上传缩略图
	$preve = upload_preview($cool_xiu_file, $themes->id, $s_author, COOLXIU_TYPE_PREV_BROWSER, $input_prev_br, -1, $error);
	$theme_prev_url = $preve->url;
	array_push($arr_prevs, $preve);
	++$img_num;

	//上传浏览图
	$input_prev 	= "Files";
	foreach($_FILES[$input_prev]["error"] as $key => $error){
		$f_name 	= $_FILES[$input_prev]['name'][$key];
		if(!$f_name){
			continue;
		}
		$preve = upload_preview($cool_xiu_file, $themes->id, $s_author, COOLXIU_TYPE_PREV_COMMON, $input_prev, $key, $error);
		array_push($arr_prevs, $preve);
		++$img_num;
	}
	
	$themes->setPrevImgs($img_num, $theme_prev_url, $arr_prevs);
	
	//录入数据库
	$coolxiu_db = new CoolXiuDb();
	$result = $coolxiu_db->setCoolXiu2DB(COOLXIU_TYPE_THEMES, $themes, $arr_prevs);
	if(!$result){
		Log::write('themes upload failed ', 'log');
		$_msg = "上传失败";	
	}else{
		$_msg = "上传成功";	
	}
	
	echo "<script>window.parent.Finish('".$_msg."');</script>";	
}catch (Exception $e){	
	$_msg = "发生异常，上传不成功";	
	echo "<script>window.parent.Finish('".$_msg."');</script>";
	exit;
}

function upload_theme($themes, $cool_xiu_file, $s_author)
{
	$theme_name   	= $_POST['themeName'];
	$theme_note   	= (isset($_POST['themeNote']))?$_POST['themeNote']:0;
	$theme_ratio  	= (int)isset($_POST['WH'])?$_POST['WH']:0;
	$type 		 	= (isset($_POST['reqType']))?$_POST['reqType']:0;

	$input_theme 	= "themeFile";
	$error 			= $_FILES[$input_theme]["error"];
	$f_name 		= "";
	$f_size 		= 0;
	$f_tmp_file 	= "";
	
	$result = get_file_info($input_theme, -1, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("themes:get_file_info() failed ERRO NO: ".$result, "log");
		$_msg =  "主题文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	
	$identity = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
	$cpid = sprintf("%u", crc32($f_name));
	$themes->setCoolXiuParam($identity, $cpid, $s_author, $theme_name, $f_size, $theme_note, $type, $theme_ratio);
	$result = $themes->upload($cool_xiu_file, $error, $f_name, $f_size, $f_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("themes->upload() failed ERRO NO: ".$result, "log");
		$_msg =  "主题文件上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	
	return true;
}

function upload_preview($cool_xiu_file, $identity, $s_author, $type, $input_prev, $key, $error)
{
	$f_prev_name 		= "";//获取上传源文件名
	$f_prev_size 		= 0;
	$f_prev_tmp_file 	= "";
	$s_prev_name 		= "";
	$s_prev_note 		= "";

	$result = get_file_info($input_prev, $key, $f_prev_name, $f_prev_size, $f_prev_tmp_file);
	if(!$f_prev_name){
		return;
	}
	if($result != UPLOAD_ERR_OK){
		Log::write("themes:get_file_info() failed ERRO NO: ".$result, "log");
		$_msg =  "预览文件信息错误，上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}

	$preve = new Preview();
	$cpid = '';
	$preve->setCoolXiuParam($identity, $cpid, $s_author, $s_prev_name, $f_prev_size, $s_prev_note, $type, 0);

	$result = $preve->upload($cool_xiu_file, $error, $f_prev_name, $f_prev_size, $f_prev_tmp_file);
	if($result != UPLOAD_ERR_OK){
		Log::write("preve->upload() failed ERRO NO: ".$result, "log");
		$_msg =  "预览文件上传失败";
		echo "<script>window.parent.Finish('".$_msg."');</script>";
		exit;
	}
	return $preve;
}