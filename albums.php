<?php
/**
 * 获取当前资源专辑
 *
 *兼容了安卓壁纸的资源
 */

require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';

$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:0);  //cooltype:主题、壁纸、铃声、专题等分类
$protocolCode = (int)(isset($_GET['protocolCode'])?$_GET['protocolCode']:1); //20150506
$bAlbum    = (int)(isset($_GET['album'])?$_GET['album']:0);
$nPage     = (int)(isset($_GET['page'])?$_GET['page']:0);
$nNum      = (int)(isset($_GET['num'])?$_GET['num']:100);
$strId     = isset($_GET['id'])?$_GET['id']:'';
$nChannel  = (int)(isset($_GET['channel'])?$_GET['channel']:REQUEST_CHANNEL_BANNER);
if (empty($strId) || strlen($strId) > 32){
	Log::write('strId length is wrong', 'log');
	exit();
}

$nStart = $nPage * $nNum;

require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'tasks/CoolShow/CoolShowSearch.class.php';

$rsc = new CoolShowSearch();
if ($nCoolType == COOLXIU_TYPE_ANDROIDESK_WALLPAPER){
	require_once 'tasks/androidWallpaper/AndroidWallpaperDb.class.php';
	$adwp = new AndroidWallpaperDb();
	
	$nWidth    = (int)(isset($_GET['width']))?$_GET['width']:540;
	$nHeight   = (int)(isset($_GET['height']))?$_GET['height']:960;
	
	$strId = urlencode($strId);
	$adwp->setSearchCondition($nWidth, $nHeight, 0, 50, 0);
	$result = $adwp->getAlbums($strId, REQUEST_CHANNEL_BANNER);
}else{
	if ($protocolCode >= 3){
		$result = $rsc->getNewBanner($nCoolType, $strId, $nChannel);
	}else {
		$result = $rsc->getAlbums($nCoolType, $strId, REQUEST_CHANNEL_BANNER, $nStart, $nNum);
	}	
}


echo $result;

require_once 'tasks/Records/RecordTask.class.php';
$rt = new RecordTask();
$rt->saveAlbums($nCoolType);

