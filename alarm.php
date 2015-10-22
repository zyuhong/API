<?php
/*
 * 查询铃声列表：
* 	http: get协议
* ?reqType=0&page=0&reqNum=10
* reqNum为我这边一次请求的数量，page为请求页，reqTpye为请求类型，目前暂做两类,
* 一类为来电铃声reqType=0，一类为通知铃声reqType=1；
* 返回结果
* JSON示例
* {
* "total_number": 300
* "ret_number": 20
* "rings":
* [
* 		{
*            "id": 11488058246,
*            “name” : “little”,
*            “url”: ”../1.wp”,
*             "size": 520,
*      },
*      ...
*],
*}
*/
session_start();

require_once 'public/public.php';
try{
	$page 	= isset($_GET['page'])?$_GET['page']:0;
	$limit  = isset($_GET['reqNum'])?$_GET['reqNum']:10;
	$start 	  = $limit * $page;
	
	if($limit == null || $page === null){
		echo(getFaultResult(-1));
		exit; 
	}
	
	$result = is_numeric($limit);
	if(!$result){
		Log::write("ring::limit is not numeral", "log");
		echo(getFaultResult(-1));
		exit; 
	}
	
	$result = is_numeric($page);
	if(!$result){
		Log::write("ring::page is not numeral", "log");
		echo(getFaultResult(-1));
		exit; 
	}
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow(COOLXIU_TYPE_ALARM, $start, $limit);
	
	echo $json_result;
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveRequest(COOLXIU_TYPE_ALARM);
	
}catch(Exception $e){
	Log::write("ring::Exception Error:".$e->getMessage(), "log");
	echo(getFaultResult(-1));
	exit;
}
?>