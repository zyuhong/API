<?php
try {
	require_once 'lib/WriteLog.lib.php';
    require_once 'public/public.php';

	$id 	=  (isset($_GET['id']))?$_GET['id']:"";
	if(empty($id)){
		Log::write('ringdl id is empty', 'log');
		exit;
	}
	require_once 'configs/config.php';
	require_once 'public/public.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
		
	$coolshow = new CoolShowSearch();
	$url = $coolshow->getUrl(COOLXIU_TYPE_RING, $id);
	if($url === false || empty($url)){
		Log::write('CoolShowSearch::getUrl(COOLXIU_TYPE_RING) failed id:'.$id, 'log');
		exit;
	}
	
	url_skip_download($url);
	$protocolCode = 0;
	if(isset($_POST['statis'])){
		$json_param = isset($_POST['statis'])?$_POST['statis']:'';
	
		$json_param = stripslashes($json_param);
		$arr_param = json_decode($json_param, true);
		$protocolCode = (int)(isset($arr_param['protocolCode'])?$arr_param['protocolCode']:0);
	}
	if($protocolCode >= 1){
		Log::write('ringdl protocolCode = '.$protocolCode, 'debug');
		exit;
	}
	
	if(!isset($_POST['statis'])){
		require_once 'tasks/Records/RecordTask.class.php';
		$rt = new RecordTask();
		$rt->saveBrowse(COOLXIU_TYPE_RING);
		exit();
	}
	
//	require_once 'tasks/statis/ReqStatis.class.php';
//	$reqStatis = new ReqStatis();
//	$type = isset($_GET['type'])?$_GET['type']:0;
//	$reqStatis->recordDownloadRequest($id, COOLXIU_TYPE_RING, 0, 0, '', $url, $type);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveDownload(COOLXIU_TYPE_RING);
	
}catch(Exception $e){
	Log::write("ringdl exception error:".$e->getMessage(), "log");
	exit;
}
	
