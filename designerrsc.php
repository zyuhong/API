<?php
/**
 * 设计师资源
 */
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}

try{
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");

	$nCoolType 	= (int)(isset($_GET['type'])?$_GET['type']:0);
	$strCyid	= isset($_GET['cyid'])?$_GET['cyid']:10;
	$nPage 	   	= (int)(isset($_GET['page'])?$_GET['page']:0);
	$nNum  		= (int)(isset($_GET['num'])?$_GET['num']:10);
	$nStart 	  	= $nPage * $nNum;
	
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getDesignerCoolShow($nCoolType, $strCyid, $nStart, $nNum);
	
	echo $json_result; 

}catch(Exception $e){
	Log::write('collect exception:'.$e->getMessage(), 'log');
	echo get_rsp_result(false, 'collect exception');
}