<?php
/**
 * 根据分辨率及主题内核获取主题列表
 * 主要获取参数：identity, cpid, name
 * 
 */

session_start();

$nWidth    = isset($_GET['width'])?$_GET['width']:1080;
$nHeight   = isset($_GET['height'])?$_GET['height']:960;
$nKernel   = isset($_GET['kernelcode'])?$_GET['kernelcode']:3;
$start     = isset($_GET['start'])?$_GET['start']:0;
$limit	   = isset($_GET['limit'])?$_GET['limit']:20;

require_once 'tasks/CoolShow/CoolShowDb.class.php';
$coolShowDb = new CoolShowDb();
$result = $coolShowDb->getThemeList($nWidth, $nHeight, $nKernel, $start, $limit);

echo $result;