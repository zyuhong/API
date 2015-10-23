<?php
require_once ('tasks/User/UserManager.class.php');
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}
try{
	$userManager = new UserManager();
	
	if(!isset($_SESSION['valid_user']) 
		|| $_SESSION['valid_user'] !== $_POST['name']){
		echo $userManager->getFaildRst("yl_rsp_update_passwd", UserManager::INFO_USER_NOT_LOGIN);
		exit;
	}
	
	if (isset($_POST['name']) 
			&& isset($_POST['password']) 
			&& isset($_POST['password'])){
		
		$user_name 		= $_POST['name'];
		$old_passwd 	= $_POST['oldpasswd'];
		$new_passwd		= $_POST['newpasswd'];

		$result = $userManager->updatePasswd($user_name, $old_passwd, $new_passwd);
		echo $result;
		exit;	
	}
	
	$result = $userManager->getFaildRst("yl_rsp_update_passwd", 0);
	echo $result;	
}catch (Exception $e) {
	$userManager = new UserManager();
	$result = $userManager->getFaildRst("yl_rsp_update_passwd", 0);
	echo $result;
}