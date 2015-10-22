<?php

require_once ("configs/config.php");
require_once ('tasks/User/User.class.php');
require_once ('tasks/User/User.sql.php');
require_once ("lib/mySql.lib.php");
require_once 'public/public.php';
/**
 * 
 * @author liangweiwei
 *
 */
class UserManager
{
	public $user;			// Object of CLASS User
	private $user_conn;		// Array to initialize the member variables of CLASS mysql
	
	public function __construct()
	{
		$this->user = new User();
		global $g_arr_db_conn;
		$this->user_conn = new mysql($g_arr_db_conn['host'], 
									 $g_arr_db_conn['user'], 
									 $g_arr_db_conn['pwd'], 
									 $g_arr_db_conn['db'], 
									 "",
									 $g_arr_db_conn['coding']);	
	}
	
	/**
	 * @param User[CLASS] $a_user
	 */
	public function UserManager($a_user)
	{
		$this->user = $a_user;
		global $g_arr_db_conn;
		$this->user_conn = new mysql($g_arr_db_conn['host'], 
									 $g_arr_db_conn['user'], 
									 $g_arr_db_conn['pwd'], 
									 $g_arr_db_conn['db'], 
									 "conn",
									 $g_arr_db_conn['coding']);	
	}
		
	public function setUser($a_user) 
	{
		$this->user = $a_user;
	}
	
	public function getUser() 
	{
		return $this->user;
	}
	
	function getUserInfo($rsp_key, $user_name){
		
		$sql = sprintf(SQL_SELECT_USER_BY_NAME, $user_name);
		$result = $this->user_conn->query($sql);
		if(!$result){
			Log::write("UserManager::getUserInfo():query() failed!", "log");
			return $this->getFaildRst($rsp_key, self::INFO_EXECUTE_SQL_ERROR);
		}
		
		$row =  $this->user_conn->fetch_assoc();
		if(!$row){
			Log::write("UserManager::getUserInfo():fetch_assoc() failed!", "log");
			return $this->getFaildRst($rsp_key, self::INFO_EXECUTE_SQL_ERROR);
		}
		
		$this->user->setUserByDb($row);		
		return $this->_getUserJson($rsp_key);
	}
	/**
	 * 正常情况下的JSON结果
	 * @return string
	 */
	private function _getUserJson($rsp_key){
		$result = array($rsp_key=>array("name"=>$this->user->getName(),
										"power" => $this->user->getPower(),
										"email" => $this->user->getEmail(),
										"phone" => $this->user->getPhone(),
										"date"  => $this->user->getDate(),
										"result"=> 1),
								"success"=>true);
		return json_encode($result);
	}
	/**
	 * 异常情况下返回JSON结果
	 * @param unknown_type $rst_key
	 * @param unknown_type $rst_value
	 */
	function getFaildRst($rst_key, $rst_value){
		$result = array($rst_key=>array("result"=>$rst_value),
						"success"=>true);
		return json_encode($result);
	}
	
	/**
	 * 用户注册，要验证用户名/Email/Phone
	 * @param unknown_type $new_user
	 * @return string|boolean
	 */
	public function registerUser($new_user) 
	{
		try{
			$result = $this->_checkUserName($new_user->getName());
			if($result != self::INFO_USER_NAME_NOT_EXIST){
				return $this->getFaildRst("yl_rsp_register", $result);
			}
			
			$result = $this->_checkUserEmail($new_user->getEmail());
			if($result != self::INFO_USER_EMAIL_NOT_EXIST){
				return $this->getFaildRst("yl_rsp_register", $result);
			}
			$result = $this->_checkUserPhone($new_user->getPhone());
			if($result != self::INFO_USER_PHONE_NOT_EXIST){
				return $this->getFaildRst("yl_rsp_register", $result);
			}
			
			//啥意思？
			if(($new_user->getPower() == 1) && ($this->_checkUserId($new_user->getID()) == self::INFO_USER_ID_EXIST)){
				return self::INFO_USER_ID_EXIST;
			}
			
			$sql = sprintf(SQL_INSERT_USER_INFO, $new_user->getPasswd(),
					$new_user->getName(),
					$new_user->getEmail(),
					$new_user->getPower(),
					$new_user->getPhone(),
					date("Y-m-d h:i:s"),
					$new_user->getName());
			$result = $this->user_conn->query($sql);
			if(!$result){
				Log::write("UserManager::registerUser():commit_query() failed!", "log");
				return $this->getFaildRst("yl_rsp_register", self::INFO_EXECUTE_SQL_ERROR);
			}
		}catch (Exception $e){
			Log::write("UserManager::registerUser():commit_query() exception, error".$e->getMessage(), "log");
			return $this->getFaildRst("yl_rsp_register", self::INFO_EXECUTE_EXCEPTION);
		}		
		$this->user->setUser($new_user);
		return $this->_getUserJson("yl_rsp_register");
	}
	
	/**
	 * MUST BE SURE $this->user HAS login BEFORE CITING deleteUser()
	 * @param $delete_id --IS THE id OF THE USER TO BE DELETE
	 */
	public function deleteUser($delete_name) 
	{
		//done
		if($this->user->role != '1'){
			return self::INFO_USER_ROLE_NOT_ADMIN ;
		}
		
		$sql = sprintf(SQL_DELETE_USER_BY_NAME, $delete_name);
		$result = $this->user_conn->query($sql);
		if(!$result){
			Log::write("UserManager::deleteUser():commit_query() failed!--p3", "log");
			return false;
		}
		return self::INFO_USER_SUCCESS_DELETE;
	}

	/**
	 * decide the user or admin in this step
	 * IF login, THEN cteate $_SESSION[...] FOLLOW THE FUNCTION
	 * @param char $user_name --from $_POST[...]
	 * @param char $passwd    --from $_POST[...] without encripted
	 */
	public function login($user_name, $user_passwd) 
	{
		$result = $this->_checkUserName($user_name);
 		if($result != self::INFO_USER_NAME_EXIST) {
			return $this->getFaildRst("yl_rsp_login", $result);
		}	

		$result = $this->_checkUserPasswd($user_name, $user_passwd);
		if($result != self::INFO_USER_PASSWORD_RIGHT){
			return $this->getFaildRst("yl_rsp_login", $result);
		}
		
		$sql = sprintf(SQL_SELECT_USER_BY_NAME_PSW, $user_name, $user_passwd);
		$result = $this->user_conn->query($sql);
		if(!$result){
			Log::write("UserManager::login():query() failed!", "log");
			return $this->getFaildRst("yl_rsp_login", self::INFO_EXECUTE_SQL_ERROR);
		}
		
		$row =  $this->user_conn->fetch_assoc();
		if(!$row){
			Log::write("UserManager::login():fetch_assoc() failed!", "log");
			return $this->getFaildRst("yl_rsp_login", self::INFO_EXECUTE_SQL_ERROR);
		}
		
		$this->user->setUserByDb($row);		
		
		return $this->_getUserJson("yl_rsp_login");		
	}
	
	/**
	 * @param $old_user_id --MUST BE THE $_SESSION[...] VAR WHICH created AFTER login()
	 * PLEASE unset($_SESSION[...]) BEFORE CITING logout () 
	 */
	public function logout($old_user_name) 
	{
		if(empty($old_user_name)){      // check up if the user has login 
			return self::INFO_USER_NOT_LOGIN;
		}
		if(!(session_destroy())){
			Log::write("UserManager::logout():session_destroy() failed!", "log");
			return false;
		}
		unset($_SESSION['valid_user']);
		$this->user = new User();	// reset user;
		return self::INFO_USER_SUCCESS_LOGOUT;
	}
	
	/**
	 * @param $user_name 	--IS GOT BY $_SESSION(...)
	 * @param $old_passwd AND $new_passwd 	--ARE GOT BY $_POST(...)
	 */
	public function changePasswd($user_name,$old_passwd,$new_passwd)
	{
		if(!$this->login($user_name,$old_passwd)){
			return self::INFO_USER_PASSWORD_WRONG;
		}
		$sql = sprintf(SQL_UPDATE_USER_BY_NAME, $new_passwd, $user_name);
		$result = $this->user_conn->commit_query($sql);
		if(!$result){
			Log::write("UserManager::changePasswd():commit_query() failed!", "log");
			return false;
		}
		$this->user->passwd = $new_passwd;
		return self::INFO_USER_SUCCESS_CHANGE_PSW;
	}
	
	public function updatePasswd($user_name, $old_passwd, $new_passwd){
		
		$result = $this->_checkUserPasswd($user_name, $old_passwd);
		if($result != self::INFO_USER_PASSWORD_RIGHT){
			return $this->getFaildRst("yl_rsp_update_passwd", $result);
		}
		
		$sql = sprintf(SQL_UPDATE_USER_PASSWD_BY_NAME_PASSWD, $new_passwd, $user_name, $old_passwd);
		$result = $this->user_conn->query($sql);
		if(!$result){
			Log::write("UserManager::updatePasswd():query() failed!", "log");
			return $this->getFaildRst("yl_rsp_update_passwd", self::INFO_EXECUTE_SQL_ERROR);
		}
		
		$result = array("yl_rsp_update_passwd"=>array("result"=>1),
						"success"=>true);
		return json_encode($result);
	}
	
	/**
	 * 密码重置，在忘记密码的情况下获取自动生成密码
	 * @param unknown_type $user_name
	 * @param unknown_type $user_email
	 */
	public function resetPasswd($user_name, $user_email){
		//用户名和密码应该联合检验吧？
		
		$result = $this->_checkUserName($user_name);
		if($result != self::INFO_USER_NAME_EXIST){
			return $this->getFaildRst("yl_rsp_retrieve_passwd", $result);
		}
		
		$result = $this->_checkUserEmail($new_user->email);
		if($result == self::INFO_USER_EMAIL_EXIST){
			return $this->getFaildRst("yl_rsp_retrieve_passwd", $result);
		}
		
		// create new 8 bit of password 
		$string = 'abcdefghijklmnopgrstuvwxyz0123456789';
		$rand = '';
		for ($x=0; $x < 8; $x++){
			$rand .= substr($string, mt_rand(0,strlen($string)-1),1);
		}
		
		$new_passwd = md5($rand);
		$sql = sprintf(SQL_UPDATE_USER_PASSWD_BY_EMAIL, $new_passwd, $user_name, $user_email);
		$result = $this->user_conn->query($sql);
		if(!$result){
			Log::write("UserManager::resetPasswd():query() failed!", "log");
			return $this->getFaildRst("yl_rsp_retrieve_passwd", self::INFO_EXECUTE_SQL_ERROR);
		}
		
		$result = send_email("change password succeed", "your new password is: ".$rand, $user_email);
		if(!$result){
			Log::write("UserManager::resetPasswd():send_email() failed!", "log");
			return $this->getFaildRst("yl_rsp_retrieve_passwd", self::INFO_EXECUTE_SENMAIL_ERROR);
		}
		
		$result = array("yl_rsp_retrieve_passwd"=>array("result"=>1,
					  									"email"=>$user_email),
						"success"=>true);
		return json_encode($result);
	}
	
	private function _checkUserId($user_id) 
	{
		try{
			$sql = sprintf(SQL_CHECK_USER_BY_ID, $user_id);
			$result = $this->user_conn->query($sql);
			if(!$result){
				Log::write("UserManager::checkUserId():commit_query() failed!", "log");
				return false;
			}
			$count = $this->user_conn->db_num_rows();
			if($count == 0){
				return self::INFO_USER_ID_NOT_EXIST;
			}
			if($count == 1){
				return self::INFO_USER_ID_EXIST;
			}
			if($count >=2){
				return self::INFO_USER_ID_EXCEPTION;
			}
		}catch (Exception $e){
			Log::write("UserManager::_checkUserId():exception, error ".$e->getMessage(), "log");
			return false;
		}	
		return  true;		
	}
	
	private  function _checkUserName($user_name) 
	{
		try{
			$sql = sprintf(SQL_CHECK_USER_NAME, $user_name);
			$result = $this->user_conn->query($sql);
			if(!$result){
				Log::write("UserManager::checkUserName():commit_query() failed!", "log");
				return self::INFO_EXECUTE_SQL_ERROR;
			}
			
			$row = $this->user_conn->fetch_row();
			$count = $row[0];
			
			if($count <= 0){
				return self::INFO_USER_NAME_NOT_EXIST;
			}
			if($count == 1){
				return self::INFO_USER_NAME_EXIST;
			}
			if($count >=2){
				return self::INFO_USER_NAME_EXCEPTION;
			}
		}catch (Exception $e){
			Log::write("UserManager::_checkUserName():exception, error ".$e->getMessage(), "log");
			return false;
		}	
		return  true;
	}
	
	private function _checkUserPasswd($user_name, $user_passwd){		
		try {
			$sql = sprintf(SQL_CHECK_USER_PASSWD, $user_name, $user_passwd);
			$result = $this->user_conn->query($sql);
			if(!$result){
				Log::write("UserManager::login():query() failed!", "log");
				return self::INFO_EXECUTE_SQL_ERROR;
			}
			
			$row = $this->user_conn->fetch_row();
			$count = $row[0];
			if($count <= 0){
				return self::INFO_USER_PASSWORD_WRONG;  // user isn't exist return INFO VAR
			}
			if($count == 1){
				return self::INFO_USER_PASSWORD_RIGHT;
			}
			if($count >=2){
				return self::INFO_USER_EXCEPTION;       // more than one users return INFO VAR
			}
		}catch (Exception $e){
			Log::write("UserManager::_checkUserPasswd():exception, error ".$e->getMessage(), "log");
			return false;
		}	
		return  true;
	}
	
	private function _checkUserEmail($user_email) 
	{
		try {
			$sql = sprintf(SQL_CHECK_USER_EMAIL, $user_email);
			$result = $this->user_conn->query($sql);
			if(!$result){
				Log::write("UserManager::checkUserEmail():commit_query() failed!", "log");
				return false;
			}
			$row = $this->user_conn->fetch_row();
			$count = $row[0];
			if($count == 0){
				return self::INFO_USER_EMAIL_NOT_EXIST;
			}
			if($count == 1){
				return self::INFO_USER_EMAIL_EXIST;
			}
			if($count >=2){
				return self::INFO_USER_EMAIL_EXCEPTION;
			}
		}catch (Exception $e){
			Log::write("UserManager::_checkUserEmail():exception, error ".$e->getMessage(), "log");
			return false;
		}	
		return true;
	}
	
	private function _checkUserPhone($user_phone)
	{
		try {
			$sql = sprintf(SQL_CHECK_USER_PHONE, $user_phone);
			$result = $this->user_conn->query($sql);
			if(!$result){
				Log::write("UserManager::checkUserPhone():commit_query() failed!", "log");
				return false;
			}
			$row = $this->user_conn->fetch_row();
			$count = $row[0];
			if($count == 0){
				return self::INFO_USER_PHONE_NOT_EXIST;
			}
			if($count == 1){
				return self::INFO_USER_PHONE_EXIST;
			}
			if($count >=2){
				return self::INFO_USER_PHONE_EXCEPTION;
			}
		}catch (Exception $e){
			Log::write("UserManager::_checkUserPhone():exception, error ".$e->getMessage(), "log");
			return false;
		}	
		return  true;
	}
	
	const INFO_USER_NOT_EXIST 		    = 1000; //
	const INFO_USER_ID_NOT_EXIST 		= 1001; //
	const INFO_USER_NAME_NOT_EXIST 		= 1002; //
	const INFO_USER_EMAIL_NOT_EXIST		= 1003; //
	const INFO_USER_PHONE_NOT_EXIST		= 1004; //
	
	const INFO_USER_EXIST 				= 2000; //
	const INFO_USER_ID_EXIST 			= 2001; //
	const INFO_USER_NAME_EXIST 			= 2002; //
	const INFO_USER_EMAIL_EXIST			= 2003; //
	const INFO_USER_PHONE_EXIST			= 2004; //
	
	const INFO_USER_EXCEPTION 			= 3000; //
	const INFO_USER_ID_EXCEPTION 		= 3001; //
	const INFO_USER_NAME_EXCEPTION 		= 3002; //
	const INFO_USER_EMAIL_EXCEPTION		= 3003; //
	const INFO_USER_PHONE_EXCEPTION		= 3004; //
	
	const INFO_USER_PASSWORD_RIGHT 		= 4000; //
	const INFO_USER_PASSWORD_WRONG 		= 4001; //
	const INFO_USER_NOT_LOGIN           = 4002; //
	const INFO_USER_ROLE_IS_ADMIN		= 4003; //
	const INFO_USER_ROLE_NOT_ADMIN      = 4004; //
	
	const INFO_USER_SUCCESS_REGIST      = 6001; //
	const INFO_USER_SUCCESS_DELETE      = 6002; //
	const INFO_USER_SUCCESS_LOGIN       = 6003; //
	const INFO_ADMIN_SUCCESS_LOGIN      = 6004; //
	const INFO_USER_SUCCESS_CHANGE_PSW	= 6005; //
	
	const INFO_EXECUTE_SQL_ERROR		= 7000;	
	const INFO_EXECUTE_SENMAIL_ERROR	= 7001;
	const INFO_EXECUTE_EXCEPTION		= 7002;
	
}
