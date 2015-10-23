<?php
	/*
	 * 动态壁纸接口
	 */
//以下部分测试通过
	
	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
		$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
	
		$start 	  = $req_num * $req_page;
	}else{
		$req_page 	= (int)(isset($_POST['start'])?$_POST['start']:0);
		$req_num 	= (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start   	= $req_page;
	}
	
	require_once 'public/public.php';
	if($req_num === null || $req_page === null 
		|| !is_numeric($req_num) || !is_numeric($req_page)){
		$result = get_rsp_result(false, 'request page is wrong');
		die($result);
	}
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow(COOLXIU_TYPE_LIVE_WALLPAPER, $req_page, $req_num);
	
	echo $json_result;
// 	require_once 'tasks/statis/ReqStatis.class.php';

// 	$width 	  = (isset($_GET['width']))?$_GET['width']:480;
// 	$height   = (isset($_GET['height']))?$_GET['height']:800;
// 	$req_type = (isset($_GET['reqType']))?$_GET['reqType']:0;

// 	$reqStatis = new ReqStatis();
// 	$reqStatis->recordRequest($req_type, COOLXIU_TYPE_SCENE, $height, $width);
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveRequest(COOLXIU_TYPE_SCENE);#动态壁纸归类为锁屏，在channel里区分来源
?>