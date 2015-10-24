<?php
//获取用户变量
require_once 'tasks/User/UserManager.class.php';
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';

$user_manager = new UserManager();
try {
	if(isset($_POST[name]) && isset($_POST['email'])){

		$user_name	= $_POST[name];
		$user_email = $_POST['email'];
		
		$result = $user_manager->resetPasswd($user_name, $user_email);
		echo $result;
	}else
	{
		$result = $user_manager->getFaildRst("yl_rsp_retrieve_passwd", 0);
		echo $result;
	}
}
catch (Exception $e) {
	Log::write("forgotPasswd exception: ".$e->getMessage(), "log");
	$result = $user_manager->getFaildRst("yl_rsp_retrieve_passwd", 0);
	echo $result;
}
?>
