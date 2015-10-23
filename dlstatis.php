<?php
	require_once 'tasks/statis/DownloadStatis.class.php';
	require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}
	
//	if (!isset($_GET['id'])){
// 		exit;
// 	}
	$id 	= isset($_GET['id'])?$_GET['id'] : "";					//下载id
	$product= isset($_GET['product'])?$_GET['product'] : "";		//产品
	$cooltype = isset($_GET['cooltype'])?$_GET['cooltype'] : "";	//下载类型1、主题、2、壁纸 3、铃声
	$type 	= isset($_GET['type'])?$_GET['type'] : "";				//下载类型  ：艺术、抽象、美女......
	$imei 	= isset($_GET['imei'])?$_GET['imei'] : "";				//手机imei号
	$height = isset($_GET['height'])?$_GET['height'] : "";			//分辨率的高
	$width 	= isset($_GET['width'])?$_GET['width'] : "";			//分辨率的宽
	
	$download_statis = new DownloadStatis();
	$download_statis->setStatisParam($id, $imei, $product, $cooltype, $height, $width);
	$result = $download_statis->recordDownload();
	if(!$result){
		Log::write("wallpaterDownloadStatis::updateDownloadCount failed", "log");
	}
?>
