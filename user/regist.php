<?php
//获取用户变量
try{
	$user_name  = $_POST['username'];
	$passwd    = $_POST['password'];
	$user_email = $_POST['useremail'];
	$user_role  = $_POST['userrole'];
	$user_phone  = $_POST['userphone'];
	if($user_role == '1'){
		$user_id  = $_POST['userid'];
	}
	if($user_role == '0'){
		$user_id  = $_POST[''];
	}
	session_start();	
	
	$new_user = new User($user_id,
	        			$passwd,     
						$user_name,
						$user_email,
						$user_role,
						$user_phone,
						date("Ymdhis"));

	$userManager = new UserManager();
	$msg_regist = $userManager->registerUser($new_user);
	if(!$msg_regist){
		echo "注册失败";
	}
	if($msg_regist == "INFO_USER_SUCCESS_REGIST"){
		echo '注册成功';
	}
	if($msg_regist == "INFO_USER_ID_EXIST"){
		echo '用户ID已存在';
	}
	if($msg_regist == "INFO_USER_PHONE_EXIST"){
		echo '电话已存在';
	}
	if($msg_regist == "INFO_USER_EMAIL_EXIST"){
		echo 'EMAIL已存在';
	}
	if($msg_regist == "INFO_USER_NAME_EXIST"){
		echo '用户名已存在 ';
	}

}
catch (Exception $e) {
	$_msg .= $e->getMessage();
}