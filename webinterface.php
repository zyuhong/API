<?php


$req_page = isset($_GET['page'])?$_GET['page']:0;
$req_num  = isset($_GET['reqNum'])?$_GET['reqNum']:10;
$start 	  = $req_num * $req_page;

require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

if($req_num === null || $req_page === null){
	$result = get_rsp_result(false, 'req_num or req_paga is null');
	exit($result); //错误请求
}

if(!is_numeric($req_num) || !is_numeric($req_page)){
	$result = get_rsp_result(false, 'req_num or req_paga is not num');
	exit($result); //错误请求
}

$nCoolType   = (int)(isset($_GET['cooltype']))?$_GET['cooltype']:0;
$nSortType   = (int)(isset($_GET['sort']))?$_GET['sort']:0;

require_once 'configs/config.php';
require_once("tasks/CoolShow/CoolShowSearch.class.php");

$coolshow = new CoolShowSearch();
$json_result = $coolshow->searchWeb($nCoolType, $nSortType, $start, $req_num);

echo $json_result;

require_once 'tasks/Records/RecordTask.class.php';
$rt = new RecordTask();
$rt->saveWebRequest($nCoolType);