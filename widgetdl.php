<?php
session_start();
require_once 'lib/WriteLog.lib.php';

try{
	$id = isset($_GET['id'])?$_GET['id']:'';
	if(empty($id)){
		Log::write('adwpdownload:ID is empty', 'log');
		exit;
	}

	$url = isset($_GET['url'])?$_GET['url']:'';
	if(empty($url)){
		Log::write('adwpdownload:url is empty', 'log');
		exit;
	}
	require_once 'public/public.php';
	require_once 'configs/config.php';
	
	global  $g_arr_host;

	$url = stripslashes($url) ;
	$url = stripslashes($url) ;
// 	$url = $g_arr_host['androidwp_host'].$id;
	
	header('Location: '.$url);
	
//	require_once 'tasks/statis/ReqStatis.class.php';
//	$reqStatis = new ReqStatis();
	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';
	$type = isset($_GET['type'])?$_GET['type']:0;
//	$reqStatis->recordDownloadRequest($id, COOLXIU_TYPE_WIDGET, $height, $width, $cpid, $url, $type);

}catch(Exception $e){
	Log::write('wpbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}
