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
require_once 'public/public.php';
try{
	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$page 	= 0;//$_GET['page'];
		$limit  = 100;//$_GET['reqNum'];
		$start 	  = $page + $limit * $page;
	}else{
		$page 		= (int)(isset($_POST['start'])?$_POST['start']:0);
		$limit 		= (int)(isset($_POST['limit'])?$_POST['limit']:100);
		$start   	= $page;
	}
	$language = isset($_GET['language'])?$_GET['language']:'ch';
	
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
	
	require_once 'tasks/Font/FontDb.class.php';
	$font_db = new FontDb();
	$json_result = $font_db->searchAllFont($start, $limit);
	
	echo $json_result;
	
	
	
	
}catch(Exception $e){
	Log::write("ring::Exception Error:".$e->getMessage(), "log");
	echo(getFaultResult(-1));
	exit;
}
?>