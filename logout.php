<?php
	require_once 'lib/WriteLog.lib.php';
    require_once 'public/public.php';

	session_start();
	if(isset($_SESSION['valid_user'])){
		$old_user = $_SESSION['valid_user'];
		unset($_SESSION['valid_user']);
	
		if(!(session_destroy())){
			Log::write("UserManager::logout():session_destroy() failed!", "log");
			
			$result = array("yl_rsp_logout" =>array("result" => 0));
			echo json_encode($result);
		}
		
		$result = array("yl_rst_logout" => array("result" => 1));
		echo json_encode($result);
	}else{
		$result = array("yl_rst_logout" => array("result" => 0));
		echo json_encode($result);
	}
?>