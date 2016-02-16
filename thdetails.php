<?php
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

try{
	$nCoolType 	= isset($_GET['type'])?$_GET['type']:'';
	$id 	   	= isset($_GET['id'])?$_GET['id']:'';
	if(empty($id)){
		Log::write('thbrowse:ID is empty', 'log');
		exit;
	}
	$product  = isset($_GET['product'])?$_GET['product']:'';
	$channel   = (int)(isset($_GET['channel'])?$_GET['channel']:0);
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");

    $coolshow = new CoolShowSearch();
    $json_result = $coolshow->getCoolShowDetail($nCoolType, $id, $channel);

    echo $json_result;
	
}catch(Exception $e){
	Log::write('thbrowse:: exception error:'.$e->getMessage(), 'log');
	exit;
}