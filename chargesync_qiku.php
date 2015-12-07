<?php
/**
 * 付费平台记录同步
 */
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/charge/ChargeDb.class.php';
require 'public/charge_sign.php';

$jsonCharge = isset($_POST['transdata'])?$_POST['transdata']:'';
$jsonCharge = str_replace('\\', '', $jsonCharge);
$sign = isset($_POST['sign'])?$_POST['sign']:'';
Log::write("charge=".$jsonCharge.", sign=".$sign, "debug");
if (empty($jsonCharge)) {
    Log::write('chargesync:: charge is empty', 'debug');
    echo 'FAILURE';
    exit();
}

if (! empty($sign)) {
    $result = validsign($jsonCharge, $sign);
    if ($result != 0) {
        Log::write("chargesync:: charge sign fail", "debug");
        echo 'FAILURE';
        exit();
    }
}

//验签名成功，添加处理业务逻辑的代码;
$result = 'SUCCESS';

$chargeDb = new  ChargeDb();

$jsonCharge 	= stripslashes($jsonCharge);
$arrChargeRecord = json_decode($jsonCharge, true);

$chargeDb->setQikuChargeRecord($arrChargeRecord);
$bResult = $chargeDb->recordNCharge();
if (! $bResult) {
	Log::write('chargesync:: recordNCharge failed', 'error');
	$result = 'FAILURE';	
}

require_once 'tasks/Exorder/ExorderRecordDb.class.php';
$exorder = isset($arrChargeRecord['exorderno'])?$arrChargeRecord['exorderno']:'';
$resultCode = (int)(isset($arrChargeRecord['result'])?$arrChargeRecord['result']:1);
if (! empty($exorder) && $resultCode == 0) {
    $erDb = new ExorderRecordDb();
    $bResult = $erDb->updateMobileExorder($exorder, 0);
    if (! $bResult) {
        Log::write('chargesync_qiku updateMobileExorder() failed,exorder is '.$exorder, 'debug');
    }
}

echo $result;