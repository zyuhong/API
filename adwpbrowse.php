<?php
session_start();
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}

try{
	$id = isset($_GET['id'])?$_GET['id']:'';
	if(empty($id)){
		Log::write('wpbrowse:ID is empty', 'log');
		exit;
	}
	$url = isset($_GET['url'])?$_GET['url']:'';
	if(empty($url)){
		Log::write('wpbrowse:url is empty', 'log');
		exit;
	}
	
	require_once 'public/public.php';
	require_once 'configs/config.php';

	global  $g_arr_host;
	
// 	$url = $g_arr_host['androidwp_host'].$id;
	$url = stripslashes($url) ;
	$url = stripslashes($url) ;
	header('location: '.$url);
	
// 	$vercode = (int)(isset($_GET['vercode'])?$_GET['vercode']:0);
// 	if($vercode >= 39){
// 		Log::write('adwpbrowse vercode = '.$vercode, 'log');
// 		exit;
// 	}
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
// 	$reqStatis = new ReqStatis();	
// 	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';
// 	$type = isset($_GET['type'])?$_GET['type']:0;
// 	$channel = isset($_GET['channel'])?$_GET['channel']:0;
// 	$reqStatis->recordBrowseRequest($id, $type, COOLXIU_TYPE_ANDROIDESK_WALLPAPER, 0, 0, $cpid, $url, $channel);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveBrowse(COOLXIU_TYPE_ANDROIDESK_WALLPAPER);
	
}catch(Exception $e){
	Log::write('wpbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}