<?php
	require_once 'tasks/CoolXiu/DownloadStatis.class.php';
	require_once 'lib/WriteLog.lib.php';
	
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