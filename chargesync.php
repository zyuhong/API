<?php
/**
 * 付费平台记录同步
 */
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/charge/ChargeDb.class.php';
require_once 'public/public.php';

$jsonCharge = isset($_POST['transdata'])?$_POST['transdata']:'';
if (empty($jsonCharge)){
	$jsonCharge = file_get_contents("php://input");//isset($_POST['charge'])?$_POST['charge']:'';
}
// $jsonCharge = file_get_contents("php://input");//isset($_POST['charge'])?$_POST['charge']:'';
// $jsonCharge = '{"amt":"1.0","merid":"1001","mername":"测试","appid":"1001","appname":"应用场景","chargepoint":"","chargepointname":"","operators":"","orderdate":"2012-11-01 23:32:50","orderid":"121101233244538","ordersatus":"202","paytype":"100000000016","paytypename":"短信","phone":"","province":"","reqOrderId":"123456789","sign":"…………….."}';

Log::write('chargesync:: jsonCharge:'.$jsonCharge, 'debug');
if(empty($jsonCharge)){
	Log::write('chargesync:: charge is empty', 'error');
	
	$result = array('resultCode'=>300);
	echo json_encode($result);
	exit();
}

$chargeDb = new  ChargeDb();

$jsonCharge 	= stripslashes($jsonCharge);
$arrChargeRecord = json_decode($jsonCharge, true);

$chargeDb->setNChargeRecord($arrChargeRecord);
$bResult = $chargeDb->recordNCharge();
if(!$bResult){
	Log::write('chargesync:: recordNCharge failed', 'error');
	
	$result = array('resultCode'=>300);	
	echo json_encode($result);
	exit();
}

$result = array('resultCode'=>200);
echo json_encode($result);