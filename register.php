<?php
//获取用户变量
	session_start();

	require_once 'tasks/User/User.class.php';
	require_once 'tasks/User/UserManager.class.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}
	
try{	
	$user_name  = isset($_POST['name'])?$_POST['name']:"";
	$passwd 	= isset($_POST['password'])?$_POST['password']:"";
	$user_email = isset($_POST['email'])?$_POST['email']:"";
	$user_power = isset($_POST['power'])?$_POST['power']:0;
	$user_phone = isset($_POST['phone'])?$_POST['phone']:"";
	
	$userManager = new UserManager();
	
	if(empty($user_name) || empty($passwd) || empty($user_email)){
		$result = $userManager->getFaildRst("yl_rsp_register", 0);
		echo $result;
		exit;
	}
		
	if($user_power == 1){
		$user_id  = $_POST['id'];
	}
	
	if($user_power == 0){
		$user_id  = '0';
	}
	
	$new_user = new User();
	$new_user->setUserParam($user_id, $passwd, $user_name, $user_email, $user_power, $user_phone, date('Y-m-d h:i:s'));
	
	$result = $userManager->registerUser($new_user);
	
	$name = $userManager->user->getName();
	if(!empty($name)){
		$_SESSION['valid_user'] = $name;
	}
	echo $result;
}catch (Exception $e) {
	$userManager = new UserManager();
	$result = $userManager->getFaildRst("yl_rsp_register", 0);
	return $result;
}