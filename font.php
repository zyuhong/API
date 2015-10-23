<?php
/*
 * 查询字体列表：
* 	http: get协议
* page=0&reqNum=10
* reqNum为我这边一次请求的数量，page为请求页，reqTpye为请求类型，目前暂做两类,
* 一类为来电铃声reqType=0，一类为通知铃声reqType=1；
* 返回结果
* JSON示例
* {
* "total_number": 300
* "ret_number": 20
* "fonts":
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

require_once 'configs/config.php';
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}

try{
	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$page 	= $_GET['page'];
		$limit  = $_GET['reqNum'];
		$start 	  = $limit * $page;
	}else{
		$page 		= (int)(isset($_POST['start'])?$_POST['start']:0);
		$limit 		= (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start   	= $page;
	}

	if($limit == null || $page === null){
		echo(getFaultResult(-1));
		exit; 
	}
	
	$result = is_numeric($limit);
	if(!$result){
		Log::write("font::limit is not numeral", "log");
		echo(getFaultResult(-1));
		exit; 
	}
	
	$result = is_numeric($page);
	if(!$result){
		Log::write("font::page is not numeral", "log");
		echo(getFaultResult(-1));
		exit; 
	}
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow(COOLXIU_TYPE_FONT, $start, $limit);
	
	echo $json_result;
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
// 	$reqStatis = new ReqStatis();
// 	$width 	= isset($_GET['width'])?$_GET['width']:480;
// 	$height = isset($_GET['height'])?$_GET['height']:800;
// 	$channel   = (int)(isset($_GET['chanel'])?$_GET['chanel']:0);
// 	$vercode   = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
// 	$type = 0;
// 	$kernel = 1;	
// 	$id = '';
// 	$cpid = '';
// 	$url = '';
// 	$reqStatis->recordRequest($type, COOLXIU_TYPE_FONT, $height, $width, 
// 							  $kernel, $id, $cpid, $url, 
// 							  $channel, $vercode);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveRequest(COOLXIU_TYPE_FONT);
	
}catch(Exception $e){
	Log::write("ring::Exception Error:".$e->getMessage(), "log");
	echo(getFaultResult(-1));
	exit;
}
?>