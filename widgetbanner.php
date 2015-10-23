<?php
/**
 * banner区资源列表获取接口
 * 
 * $type : 资源类型
 * 
 */
session_start();
	
require_once 'tasks/CoolShow/CoolShowSearch.class.php';
require_once 'configs/config.php';

$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:4);  

$coolshow = new CoolShowSearch();
$json_result = $coolshow->getWidgetBanner($nCoolType);

echo $json_result;
 
require_once 'tasks/Records/RecordTask.class.php';
 
$rt = new RecordTask();
$rt->saveBanner($nCoolType);
