<?php

try{
	require_once 'lib/WriteLog.lib.php';
	require_once 'public/public.php';
	require_once 'configs/config.php';
		
	$id = isset($_GET['id'])?$_GET['id']:'';
	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';
	if(empty($id)){
		Log::write('thdownload:ID is empty', 'log');
		exit;
	}
	$strProduct = '';
	$strImsi    = '';
	$strMeid    = '';
	$protocolCode = 0;
	if(isset($_POST['statis'])){
		$json_param = isset($_POST['statis'])?$_POST['statis']:'';
		
		$json_param = stripslashes($json_param);
		$arr_param = json_decode($json_param, true);
		
		$strProduct = isset($arr_param['product'])?$arr_param['product']:'';
		$strImsi = isset($arr_param['imsi'])?$arr_param['imsi']:'';
		$strMeid = isset($arr_param['meid'])?$arr_param['meid']:'';
		$strCyid = isset($arr_param['cyid'])?$arr_param['cyid']:'';
		$protocolCode = (int)(isset($arr_param['protocolCode'])?$arr_param['protocolCode']:0);
	}
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	//下面两个数据库操作可以合并优化
	$coolshow = new CoolShowSearch();
	$bIsCharge = $coolshow->checkIscharge(COOLXIU_TYPE_THEMES, $id);
	if($bIsCharge){
		require_once 'tasks/Exorder/ExorderRecordDb.class.php';	
		$erDb = new ExorderRecordDb();
		$bResult = $erDb->checkMobileCharged($strProduct, COOLXIU_TYPE_THEMES, $id, $cpid, $strMeid, $strImsi, $strCyid);
		if(!$bResult){
			$result = get_rsp_result(false, 'the resource is not paid');
			exit($result);
		}
	}
		
	$url = $coolshow->getUrl(COOLXIU_TYPE_THEMES, $id);
	if($url === false){
		Log::write('CoolShowSearch::getUrl(COOLXIU_TYPE_THEMES) id:'.$id, 'log');
		exit;
	}
	
	url_skip_download($url);
	
	if($protocolCode >= 1){
 		Log::write('thdownload protocolcode = '.$protocolCode, 'debug');
 		exit;
 	}
	
	require_once 'tasks/statis/ReqStatis.class.php';
	$reqStatis = new ReqStatis();
	$type = (int)(isset($_GET['type'])?$_GET['type']:0);
	$channel = (int)(isset($_GET['channel'])?$_GET['channel']:0);
	
	$reqStatis->recordDownloadRequest($id, COOLXIU_TYPE_THEMES, 0, 0, $cpid, $url, $type, $channel);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveDownload(COOLXIU_TYPE_THEMES);
	
}catch(Exception $e){
	Log::write('thdownload:: exception error:'.$e->getMessage(), 'log');
	exit;
}