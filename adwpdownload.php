<?php
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

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

	$url = stripslashes($url) ;
	$url = stripslashes($url) ;
	header('Location: '.$url);
	
// 	$vercode = (int)(isset($_GET['vercode'])?$_GET['vercode']:0);
// 	if($vercode >= 39){
// 		Log::write('adwpdownload vercode = '.$vercode, 'log');
// 		exit;
// 	}
	
//	require_once 'tasks/statis/ReqStatis.class.php';
//	$reqStatis = new ReqStatis();
//	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';
//	$type = (int)(isset($_GET['type'])?$_GET['type']:0);
//	$channel = (int)(isset($_GET['channel'])?$_GET['channel']:0);
//	$height = 0;
//	$width  = 0;
// 	$reqStatis->recordDownloadRequest($id, COOLXIU_TYPE_ANDROIDESK_WALLPAPER, $height, $width, $cpid, $url, $type, $channel);
 	
 	require_once 'tasks/Records/RecordTask.class.php';
 	$rt = new RecordTask();
 	$rt->saveDownload(COOLXIU_TYPE_ANDROIDESK_WALLPAPER);
 	
}catch(Exception $e){
	Log::write('wpbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}