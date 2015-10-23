<?php
/**
 * 付费平台记录同步
 */
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/charge/ChargeDb.class.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}

$jsonCharge = isset($_POST['transdata'])?$_POST['transdata']:'';
$strSgin   = isset($_POST['sign'])?$_POST['sign']:'';
if (empty($jsonCharge)){
	$jsonCharge = file_get_contents("php://input");//isset($_POST['charge'])?$_POST['charge']:'';
}
// $jsonCharge = file_get_contents("php://input");//isset($_POST['charge'])?$_POST['charge']:'';
/*$jsonCharge = '{"exorderno":"MN20198211142707956","transid":"02112080114270900125",'
			.' "waresid":"10001400123001100014","chargepoint":1,"feetype":0,"money":100,"result":0,"transtype":0,'
			.' "transtime":"2012-08-01 14:30:36","count":1,"sign":"aaf137a1134a5553bd207b90469c95c2"}';
*/
Log::write('chargerecord:: jsonCharge:'.$jsonCharge, 'debug');
if(empty($jsonCharge)){
	Log::write('chargerecord:: charge is empty', 'error');
	echo 'FAILURE';
	exit();
}
$chargeDb = new  ChargeDb();

$jsonCharge 	= stripslashes($jsonCharge);
$arrChargeRecord = json_decode($jsonCharge, true);

$chargeDb->setChargeRecord($arrChargeRecord, $strSgin);
$bResult = $chargeDb->recordCharge();
if(!$bResult){
	Log::write('chargerecord:: recordCharge failed', 'error');
	echo 'FAILURE';
	exit();
}


// 	require_once 'tasks/Records/RecordFactory.class.php';
// 	require_once 'tasks/Exorder/ExorderDb.class.php';
// 	$rf = new RecordFactory();
// 	$exorderDb = new ExorderDb();
// 	$exorder = $exorderDb->getExorderById($strExorder);
// 	if(!$exorder){
// 		exit(get_rsp_result(false, 'get exorder failed'));
// 	}
// 	$nCoolType = isset($exorder['cooltype'])?$exorder['cooltype']:0;

// 	$strExorderNo	=  isset($arrChargeRecord['exorderno'])?$arrChargeRecord['exorderno']:'';
// 	$strTransid		=  isset($arrChargeRecord['transid'])?$arrChargeRecord['transid']:'';
// 	$strWaresid		=  isset($arrChargeRecord['waresid'])?$arrChargeRecord['waresid']:'';
// 	$strChargePoint	=  isset($arrChargeRecord['changepoint'])?$arrChargeRecord['changepoint']:'';
// 	$nFeeType		=  isset($arrChargeRecord['feetype'])?$arrChargeRecord['feetype']:0;
// 	$nMoney			=  isset($arrChargeRecord['money'])?$arrChargeRecord['money']:0;
// 	$nCount			=  isset($arrChargeRecord['count'])?$arrChargeRecord['count']:0;
// 	$nResult		=  isset($arrChargeRecord['result'])?$arrChargeRecord['result']:1;
// 	$nTransType		=  isset($arrChargeRecord['transtype'])?$arrChargeRecord['transtype']:0;
// 	$strTransTime	=  isset($arrChargeRecord['transtime'])?$arrChargeRecord['transtime']:'';

//  $rf->setTransaction($strExorderNo, $nCount, $strTransid, $strWaresid, $nFeeType, $nMoney, $nResult, $nTransType, $strTransTime);
//  $rf->updatePayDownload($nCoolType);

echo 'SUCCESS';