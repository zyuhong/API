<?php

try{
	require_once 'lib/WriteLog.lib.php';
	require_once 'public/public.php';
	require_once 'configs/config.php';
	
	$id = isset($_GET['id'])?$_GET['id']:'';
	if(empty($id)){
		Log::write('wpbrowse:ID is empty', 'log');
		exit;
	}
	$nChannel = (int)(isset($_GET['channel'])?$_GET['channel']:0);
	
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$url = $coolshow->getBrowseUrl(COOLXIU_TYPE_WALLPAPER, $id, $nChannel);
	if($url === false){
		Log::write('CoolShowSearch::getBrowseUrl(COOLXIU_TYPE_WALLPAPER) id:'.$id, 'log');
		exit;
	}
	
	url_skip_download($url);
	
// 	$vercode = (int)(isset($_GET['vercode'])?$_GET['vercode']:0);
// 	if($vercode >= 39){
// 		Log::write('wpbrowse vercode = '.$vercode, 'log');
// 		exit;
// 	}
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
// 	$reqStatis = new ReqStatis();
// 	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';
// 	$type = isset($_GET['type'])?$_GET['type']:0;
// 	$channel = isset($_GET['channel'])?$_GET['channel']:0;
// 	$reqStatis->recordBrowseRequest($id, 0, COOLXIU_TYPE_WALLPAPER, 0, 0, $cpid, $url, $type, $channel);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveBrowse(COOLXIU_TYPE_WALLPAPER);
	
}catch(Exception $e){
	Log::write('wpbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}