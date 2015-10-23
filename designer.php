<?php
/**
 * 关注/取消关注
 */
require_once 'lib/WriteLog.lib.php';

try{
	require_once 'tasks/Collect/CollectTask.class.php';
	$nType 	= (int)(isset($_GET['type'])?$_GET['type']:0);
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