<?php
require_once 'lib/WriteLog.lib.php';

try{
	$id = isset($_GET['id'])?$_GET['id']:'';
	if(empty($id)){
		Log::write('thbrowse:ID is empty', 'log');
		exit;
	}
	
// 	$vercode = (int)(isset($_GET['vercode'])?$_GET['vercode']:0);
// 	if($vercode >= 39){
// 		Log::write('thbrowser vercode = '.$vercode, 'log');
// 		exit;
// 	}
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
// 	$reqStatis = new ReqStatis();
// 	$reqStatis->recordBrowseRequest($id, 0, COOLXIU_TYPE_THEMES, 0, 0);

	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveBrowse(COOLXIU_TYPE_THEMES);
	
}catch(Exception $e){
	Log::write('thbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}