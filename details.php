<?php

require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';

try{
	$nType = isset($_GET['type'])?$_GET['type']:0;
	$id = isset($_GET['id'])?$_GET['id']:'';
	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';
	if(empty($id) && empty($cpid)){
		Log::write('thbrowse:ID is empty', 'log');
		exit;
	}
	$product  = isset($_GET['product'])?$_GET['product']:'';
	$chanel   = isset($_GET['channel'])?$_GET['channel']:0;
	$width = isset($_GET['width'])?$_GET['width']:0;
	$heith = isset($_GET['height'])?$_GET['height']:0;
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");

	$coolshow = new CoolShowSearch();
	if ($chanel == REQUEST_CHANNEL_WEB){
		$json_result = $coolshow->getCoolShowDetail($nType, $cpid);
	}else{
		$json_result = $coolshow->getCoolShowRelevant($nType, $id, $cpid);
	}
	
	echo $json_result;
	
}catch(Exception $e){
	Log::write('thbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}