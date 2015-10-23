<?php
/**
 * 根据分辨率及主题内核获取主题列表
 * 主要获取参数：identity, cpid, name
 * 
 */

session_start();
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

$nWidth    = (int)(isset($_GET['width'])?$_GET['width']:1080);
$nHeight   = (int)(isset($_GET['height'])?$_GET['height']:960);
$nKernel   = (int)(isset($_GET['kernelcode'])?$_GET['kernelcode']:3);
$start     = (int)(isset($_GET['start'])?$_GET['start']:0);
$limit	   = (int)(isset($_GET['limit'])?$_GET['limit']:20);

require_once 'tasks/CoolShow/CoolShowDb.class.php';
$coolShowDb = new CoolShowDb();
$result = $coolShowDb->getThemeList($nWidth, $nHeight, $nKernel, $start, $limit);

echo $result;