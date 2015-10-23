<?php
require_once 'public/public.php';

$nCoolType   = isset($_GET['type'])?$_GET['type']:-1;
$keyWord  	 = isset($_GET['keyword'])?$_GET['keyword']:'';
$bColor    	 = isset($_GET['iscolor'])?$_GET['iscolor']:0;
$nPage     	 = (int)(isset($_GET['page'])?$_GET['page']:0);
$nLimit      = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:1000);

if (empty($keyWord)){
	echo get_rsp_result(false, 'keyword is null');
	exit;
}
require_once 'configs/config.php';
require_once("tasks/CoolShow/CoolShowSearch.class.php");

$coolshow = new CoolShowSearch();
$result = $json_result = $coolshow->searchLucene($nCoolType, $keyWord, $bColor, $nPage, $nLimit);

echo $result;


require_once 'tasks/Records/RecordTask.class.php';
$rt = new RecordTask();
$rt->saveLucene($nCoolType);
