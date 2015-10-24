<?php
try{
	require_once 'lib/WriteLog.lib.php';
    require_once 'public/public.php';
	
	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
		$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
		$start 	  = $req_num * $req_page;
	}else{
		$req_page 	= (int)(isset($_POST['start'])?$_POST['start']:0);
		$req_num 	= (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start   	= $req_page;
	}
	
	if($req_num === null || $req_page === null){
		echo(getFaultResult(-1));
		exit; //错误请求
	}
	
	$result = is_numeric($req_num);
	if(!$result){
		echo(getFaultResult(-1));
		exit; //错误请求
	}
	
	$result = is_numeric($req_page);
	if(!$result){
		echo(getFaultResult(-1));
		exit; //错误请求
	}
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow(COOLXIU_TYPE_SCENE, $start, $req_num);
	
	echo $json_result;
	
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
// 	$reqStatis = new ReqStatis();
// 	$width 	  = isset($_GET['width'])?$_GET['width']:720;
// 	$height   = isset($_GET['height'])?$_GET['height']:1280;
// 	$kernel	  = isset($_GET['kernelcode'])?$_GET['kernelcode']:2;
// 	$vercode = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
	
// 	$req_type = 0;
// 	$kernel   = 2;
// 	$id 	= '';
// 	$cpid 	= '';
// 	$url 	= '';
// 	$channel = 0;
// 	$reqStatis->recordRequest($req_type, COOLXIU_TYPE_SCENE, $height, $width, $kernel,
// 								  $id, $cpid, $url, $channel, $vercode);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveRequest(COOLXIU_TYPE_SCENE);
	
}catch(Exceptin $e){
	
}
