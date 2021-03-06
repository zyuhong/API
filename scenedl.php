<?php
/*
 * 根据ＩＤ下载字体：
 */
header('content-type:text/html;charset=utf-8');
header ("Cache-Control: no-cache, must-revalidate");
require_once 'public/public.php';

try {
	$id 	=  (isset($_GET['id']))?$_GET['id']:'';
	$url 	=  (isset($_GET['url']))?$_GET['url']:'';
	$cpid = $id;
	if(empty($id)){
		Log::write('scenedl id is empty', 'log');
		exit;
	}
	if(empty($url)){
		Log::write('scenedl url is empty', 'log');
		exit;
	}
	
	require_once 'lib/WriteLog.lib.php';
	require_once 'configs/config.php';
 	require_once 'public/public.php';
// 	require_once("tasks/CoolShow/CoolShowSearch.class.php");	

// 	$coolshow = new CoolShowSearch();
// 	$url = $coolshow->getUrl(COOLXIU_TYPE_SCENE, $id);
// 	if($url === false){
// 		Log::write('CoolShowSearch::getUrl(COOLXIU_TYPE_SCENE) id:'.$id, 'log');
// 		exit;
// 	}
	
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
	$bIsCharge = $coolshow->checkIscharge(COOLXIU_TYPE_SCENE, $id);
	if($bIsCharge){
		require_once 'tasks/Exorder/ExorderRecordDb.class.php';
		$erDb = new ExorderRecordDb();
		$bResult = $erDb->checkMobileCharged($strProduct, COOLXIU_TYPE_SCENE, $id, $cpid, $strMeid, $strImsi, $strCyid);
		if(!$bResult){
			$result = get_rsp_result(false, 'the scene resource is not paid');
			exit($result);
		}
	}
	
	url_skip_download($url);
	
	if($protocolCode >= 1){#增加协议版本控制
		Log::write('scenedl protocolCode = '.$protocolCode, 'debug');
		exit;
	}
	
//	require_once 'tasks/statis/ReqStatis.class.php';
//	$reqStatis = new ReqStatis();
//	$type = 0;
//	$channel = isset($_GET['channel'])?$_GET['channel']:0;
//	$reqStatis->recordDownloadRequest($id, COOLXIU_TYPE_SCENE, 0, 0, $cpid, $url, $type, $channel);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveDownload(COOLXIU_TYPE_SCENE);
	
}catch(Exception $e){
	Log::write("ringdl exception error:".$e->getMessage(), "log");
	exit;
}
	
