<?php
/**
 * 设计师资源
 */
require_once 'lib/WriteLog.lib.php';

try{
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");

	$nCoolType 	= isset($_GET['type'])?$_GET['type']:0;
	$strCyid	= isset($_GET['cyid'])?$_GET['cyid']:10;
	$nPage 	   	= isset($_GET['page'])?$_GET['page']:0;
	$nNum  		= isset($_GET['num'])?$_GET['num']:10;
	$nStart 	  	= $nPage * $nNum;
	
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getDesignerCoolShow($nCoolType, $strCyid, $nStart, $nNum);
	
	echo $json_result; 

}catch(Exception $e){
	Log::write('collect exception:'.$e->getMessage(), 'log');
	echo get_rsp_result(false, 'collect exception');
}