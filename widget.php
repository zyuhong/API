<?php
/**
 * 酷派秀Widget推荐接口
 * author：lijie1
 * 2013
 */
session_start();
require_once 'public/public.php';

$width 	  = (int)(isset($_GET['width']))?$_GET['width']:480;
$height   = (int)(isset($_GET['height']))?$_GET['height']:800;
$kernel   = (int)(isset($_GET['kernelCode']))?$_GET['kernelCode']:3;
$type 	  = (int)(isset($_GET['type']))?$_GET['type']:1;
$vercode  = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
$channel  = 4;

if(($width == 480 && $height == 854)||($width == 540 && $height == 888)){
	$width 	  = 540;
	$height   = 960;
}

if($vercode == 4){
	$vercode = 3;
}

$width = $width * 2;

$json_param = isset($_POST['statis'])?$_POST['statis']:'';
if(!empty($json_param)){
	$json_param = stripslashes($json_param);
	$arr_param = json_decode($json_param, true);

	$product = isset($arr_param['product'])?$arr_param['product']:'';
	if(strcmp('Coolpad8750', $product)==0){
		$kernel = 2;
	}
}

require_once 'tasks/widget/WidgetSearch.class.php';
require_once 'public/public.php';

$widgetSearch = new WidgetSearch();
$bResult = $widgetSearch->setWidgetParam($type, $width, $height, $kernel);
if(!$bResult){
	echo getFaultResult(0);
}

$result = $widgetSearch->searchWidget($vercode);
echo $result;

// require_once 'tasks/statis/ReqStatis.class.php';
// $reqStatis = new ReqStatis();
// $id = ''; $cpid = ''; $url = '';
// $reqStatis->recordRequest($type, COOLXIU_TYPE_WIDGET, $height, $width,
// 						 $kernel, $id, $cpid, $url, $channel, $vercode);

// require_once 'tasks/Records/RecordTask.class.php';
// $rt = new RecordTask();
// $nCoolType = 0;
// if($type == 0)$nCoolType = COOLXIU_TYPE_THEMES;
// if($type == 1)$nCoolType = COOLXIU_TYPE_ANDROIDESK_WALLPAPER;
// $rt->saveWidget($nCoolType);