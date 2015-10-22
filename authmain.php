<?php
//require_once ('../lib/mySql.lib.php');

session_start();

try {
	// 连接数据库
	if(!($usersql = new mysqli('localhost', 'webauth','webauth','auth'))){
		throw new Exception('无法连接服务器数据，请稍后再试！');
	}
	// 获取浏览器数据
	$userid = $_POST['userid'];
	$password = $_POST['password'];	
	$password_sha1 = sha1($password);
	
	if(!$userid || !$password){
		$_msg = "工号或密码错误，请重新输入！";
		echo $_msg;
		exit;
	}
	
	$query_user = "select * from authorized_users where job_id = '".$userid."' and password = '$password_sha1'";
	if(!($result = $usersql->query($query_user))){
		throw new Exception('工号或密码错误，请重新登陆！');
	}
	if($result->num_rows){
		// 注册会话变量
		$row = $result->fetch_assoc();
		$_SESSION['valid_user_id']=$row[0]; // job_id
		$_SESSION['valid_user_name']=$row[1]; // name
		$usersql->close();
		$_msg = "登陆成功！";
		echo $_msg;
	}	
}catch (Exception $e) {
	$_msg .= $e->getMessage();
	echo $_msg;
}

//echo "<script>window.parent.Finish('".$_msg."');</script>";
?>