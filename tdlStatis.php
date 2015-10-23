<?php
	require_once 'tasks/CoolXiu/DownloadStatis.class.php';
	require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}
	
	if (!isset($_GET['id'])){
		exit;
	}
	$id = $_GET['id'];
	
	$download_statis = new DownloadStatis();
	
	$result = $download_statis->updateDownloadCount(COOLXIU_TYPE_THEMES, $id);
	if(!$result){
		Log::write("themesDownloadStatis::updateDownloadCount failed", "log");
	}
?>