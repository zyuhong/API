<?php
require_once 'public/public.php';

$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
$start 	  = $req_num * $req_page;

$nCoolType   = (int)(isset($_GET['type']))?$_GET['type']:0;

require_once 'configs/config.php';
require_once("tasks/CoolShow/CoolShowSearch.class.php");

$coolshow = new CoolShowSearch(); 
$json_result = $coolshow->getCoolShow($nCoolType);

echo $json_result;
