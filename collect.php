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
	
	$collect = new CollectTask();
	$nCollect = (int)(isset($_GET['collect'])?$_GET['collect']:0);  #0取消关注/1添加关注/2获取关注状态
	if($nCollect == 2){
		$result = $collect->getCollect($nCollect);
	}else{
//		echo get_rsp_result(false, 'collect exception');
//        exit();
		$result = $collect->setCollect($nCollect);
	}
	
	echo $result; 

}catch(Exception $e){
	Log::write('collect exception:'.$e->getMessage(), 'log');
	echo get_rsp_result(false, 'collect exception');
}