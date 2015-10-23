<?php
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'configs/config.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

if (!isset($_GET['id'])){
	exit;
}
$id = $_GET['id'];
$height  = (int)(isset($_GET['height'])?$_GET['height']:0);
$width   = (int)(isset($_GET['width'])?$_GET['width']:0);
$statis_type =  (int)(isset($_GET['statistype'])?$_GET['statistype']:2);
try{
	$cooltype = COOLXIU_TYPE_WALLPAPER;
	switch($statis_type){
		case 1:{//壁纸下载统计
			// 				$result = $download_statis->recordCommonDownload(StatisFactory::STATIS_OBJECT_WALLPAPER, $id);
			// 				if(!$result){
			// 					Log::write("wpdlStatis::recordCommonDownload failed", "log");
			// 					echo get_rsp_result(0);
			// 				}
		}break;
		case 2:{//壁纸、主题应用

			$apply_type = (int)(isset($_GET['applytype'])?$_GET['applytype']:0);
			if($apply_type == 4){
				$cooltype = COOLXIU_TYPE_THEMES;
			}
			require_once 'tasks/statis/ReqStatis.class.php';
			$reqStatis = new ReqStatis();
			$wpid = $id;
			$result = $reqStatis->recordApply($height, $width, $apply_type, $cooltype, $id);
			if(!$result){
				Log::write("wpdlStatis::recordApply failed", "log");
				$result = get_rsp_result(false, 'the record apply failed');
				exit($result);
			}
			
			require_once 'tasks/Records/RecordTask.class.php';
			$rt = new RecordTask();
			$rt->saveApply($cooltype);
			$result = get_rsp_result(true);
			exit($result);
		}break;
	}
}catch(Exception $e){
	Log::write("wpdlStatis::exception error:".$e->getMessage(), "log");
	echo get_rsp_result(0);
}
?>