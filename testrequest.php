<?php
	session_start();
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
		$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
		
		$start 	  = $req_num * $req_page;
	}else{
		$req_page = (int)(isset($_POST['start'])?$_POST['start']:0);
		$req_num  = (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start    = $req_page;		
	}
	
	if($req_num === null 
			|| $req_page === null 
			|| !is_numeric($req_num) 
			|| !is_numeric($req_page)){
		$result = get_rsp_result(false, 'request skip or limit failed');
		exit; 
	}

	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow(COOLXIU_TYPE_THEMES, $req_page, $req_num);
	echo $json_result;
?>