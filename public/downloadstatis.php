<?php
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/statis/DownloadStatis.class.php';

function downloadRecordStatis($object, $id, $imei,$imsi, $product){
	$download_statis = new DownloadStatis();
	$download_statis->setCommonParam($id, $imei, $imsi, $product);
	$result = $download_statis->recordCommonDownload($object);
	if(!$result){
		Log::write("downloadStatis::recordDownload failed", "log");
		return false;
	}
	return  true;
}