<?php
$req_page = isset($_GET['page'])?$_GET['page']:0;
$req_num  = isset($_GET['reqNum'])?$_GET['reqNum']:10;
$start 	  = $req_num * $req_page;

$nCoolType   = (int)(isset($_GET['type']))?$_GET['type']:0;

require_once 'configs/config.php';
require_once("tasks/CoolShow/CoolShowSearch.class.php");

$coolshow = new CoolShowSearch(); 
$protocol = $coolshow->getBanner($nCoolType);

if(!$protocol || count($protocol) <= 0){
	return get_rsp_result(false, 'get banner error');
}
$result = array('result'=>true,
				'banners'=>$protocol);
echo $json_result;
