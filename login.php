<?php

session_start();

require_once ('tasks/User/UserManager.class.php');
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

try{
	$userManager = new UserManager();
	
	if (isset($_POST['name']) && isset($_POST['password'])){
		$user_name 		= $_POST['name'];
		$user_passwd 	= $_POST['password'];
		
		$result = $userManager->login($user_name, $user_passwd);
		$name = $userManager->user->getName();
		if(!empty($name)){
			$_SESSION['valid_user'] = $name;
		} 		
		echo $result;
		exit;	
	}
	
	if(isset($_SESSION['valid_user'])){
		$user_name = $_SESSION['valid_user'];
		$result = $userManager->getUserInfo("yl_rsp_login", $user_name);
		echo $result;
		exit;
	}	
	
	$result = $userManager->getFaildRst("yl_rsp_login", UserManager::INFO_USER_NOT_LOGIN);
	echo $result;
	
}catch (Exception $e) {
	$userManager = new UserManager();
	$result = $userManager->getFaildRst("yl_rsp_login", 0);
	echo $result;
}