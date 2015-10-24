<?php
/**
 * 付费后上报已付费的订单号及终端信息
 * 
 */

require_once 'public/public.php';
require_once 'public/check.php';
require_once 'lib/WriteLog.lib.php';

if(checkVersion($_GET)){
    $bRet = checkTKT($_POST);
    if(!$bRet){
        echo get_rsp_result(false, 'check token fail');
        exit();
    }
}

try{
	$strExorder = isset($_GET['exorder'])?$_GET['exorder']:''; //订单号
	if(empty($strExorder)){
		echo get_rsp_result(false, 'exorder is empty');
		exit;
	}

	$isScore   = (int)(isset($_GET['isscore'])?$_GET['isscore']:0);
	require_once 'tasks/Exorder/ExorderRecordDb.class.php';
	$erDb = new ExorderRecordDb();
	$bResult = $erDb->updateMobileExorder($strExorder, $isScore);
	if(!$bResult){
		Log::write('mcharge_qiku updateMobileExorder() failed', 'log');
		echo get_rsp_result(false, 'save mobile charge failed');
		exit();
	}
	
	echo get_rsp_result(true);
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	
	$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:0);
	$strCyid   = isset($_GET['cyid'])?$_GET['cyid']:'';
	if (!empty($strCyid)){
		$strId 	 = isset($_GET['id'])?$_GET['id']:'';
		$strCpid = isset($_GET['cpid'])?$_GET['cpid']:'';
		$erDb->saveChargeRecord($strExorder, $strCyid, $nCoolType, $strId, $strCpid);
	}
	
	$rt->updateOrder($nCoolType, $strExorder, $isScore);
	
}catch(Exception $e){
	Log::write('mcharge_qiku exception', 'log');
	echo get_rsp_result(false, 'mcharge_qiku exception');
}