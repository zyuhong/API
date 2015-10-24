<?php
/**
 * 关注/取消关注
 */
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'public/check.php';

if(checkVersion($_GET)){
    $bRet = checkTKT($_POST);
    if(!$bRet){
        echo get_rsp_result(false, 'check token fail');
        exit();
    }
}

try{
	require_once 'tasks/Collect/CollectTask.class.php';
	$nType 	= (int)(isset($_GET['type'])?$_GET['type']:0);
	
	//将Get和Post方法简单校验
	$strMyCyid = isset($_GET['cyid'])?$_GET['cyid']:'';
	$strChCyid = ''; 
	$json_param = isset($_POST['statis'])?$_POST['statis']:'';
	if(!empty($json_param)){
		$json_param = stripslashes($json_param);
		$arr_param = json_decode($json_param, true);
		$strChCyid = isset($arr_param['cyid'])?$arr_param['cyid']:'';		
	}
	if($strMyCyid != $strChCyid) {
		echo get_rsp_result(false, 'designer exception');
		exit();
	}	
	
	$collect = new CollectTask();
	if($nType == 0){
		$result = $collect->getMyDesigner();
	}
	
	if($nType == 1){
		$result = $collect->getDesigner();
	}
	
	echo $result; 

}catch(Exception $e){
	Log::write('designer exception:'.$e->getMessage(), 'log');
	echo get_rsp_result(false, 'designer exception');
}