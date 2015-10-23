<?php
require_once 'public/public.php';
require_once 'tasks/lucene/luceneTask.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

$nCoolType   = isset($_GET['type'])?$_GET['type']:-1;
$keyWord  	 = isset($_GET['keyword'])?$_GET['keyword']:'';
$nPage     	 = (int)(isset($_GET['page'])?$_GET['page']:0);
$nLimit      = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:100);

if (empty($keyWord)){
	echo get_rsp_result(false, 'keyword is null');
	exit;
}
require_once 'configs/config.php';
require_once("tasks/CoolShow/CoolShowSearch.class.php");

$coolshow = new CoolShowSearch();
$result = $json_result = $coolshow->searchWebLucene($keyWord);

echo $result;
