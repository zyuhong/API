<?php
/*
* 讯飞合作铃声跳转
* 方法: POST 协议
* 
*/

require_once 'public/public.php';
require_once 'lib/WriteLog.lib.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

try{
	require_once 'configs/config.php';
	
	$tag = isset($_GET['tag'])?$_GET['tag']:'xunfei';
	
	global $g_arr_xring_config;
	$url = $g_arr_xring_config[$tag];
	if($tag == 'ss'){
		$word = isset($_GET['word'])?$_GET['word']:'';
		$url = sprintf($url, $word);
	}
	header('location: '.$url);	
#采用主题上报，这里不需要统计	
// 	require_once 'tasks/Records/RecordTask.class.php';
// 	$rt = new RecordTask();
// 	$rt->saveXRequest(COOLXIU_TYPE_X_RING);
	
}catch(Exception $e){
	Log::write("ring::Exception Error:".$e->getMessage(), "log");
	echo(getFaultResult(-1));
	exit;
}
?>