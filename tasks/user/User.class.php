<?php
/**
 *
 * @author liangweiwei
 * @version 
 */
class User
{
	private $id;		    // Employee number * primary key 
	private $passwd;		// Password * 40 chars which encripted by sha1
    private $name;			// user name	
	private $email;			// Email of user
	private $power;			// 0:user  1:admin
	private $phone;			// phone number of register
	private $date;   			// regist_time
	

    /**
     * Construct function which initial params by null
     */
	public function __construct() {
		$this->id		    = "";	
		$this->passwd		= "";
		$this->name 		= "";
		$this->email		= "";
		$this->power		= 0;
		$this->phone		= "";
		$this->date		  	= date("Y-m-d h:i:s");
	}
	
	/**
	 * Construct function which initial params by params
	 */
	public function User($user_id,
	        			$user_passwd,     
						$user_name,
						$user_email,
						$user_power,
						$user_phone,
						$regist_date)								 
	{
		$this->id		    = $user_id;	
		$this->passwd		= $user_passwd; 
		$this->name 		= $user_name;
		$this->email		= $user_email;
		$this->power		= $user_power;	
		$this->phone		= $user_phone;
		$this->date  		= $regist_date;	
	}
	
	/**
	 * Construct function which initial params by 
	 * an old user exist in the Database
	 * @param ARRAY $row
	 */
	public function setUserByDb($row){
		$this->id 			= $row['id'];
		$this->passwd 		= $row['passwd'];
		$this->name 		= $row['name'];
		$this->email 		= $row['email'];
		$this->power 		= $row['power'];
		$this->phone 		= $row['phone'];
		$this->date  		= $row['insert_time'];
	}
	
	public function setUser($user){
		$this->id 			= $user->id;
		$this->passwd 		= $user->passwd;
		$this->name 		= $user->name;
		$this->email 		= $user->email;
		$this->power 		= $user->power;
		$this->phone 		= $user->phone;
		$this->date  		= $user->date;
	}
	
	public function setUserParam($user_id,
	        			$user_passwd,     
						$user_name,
						$user_email,
						$user_power,
						$user_phone,
						$regist_date){
		$this->id		    = $user_id;	
		$this->passwd		= $user_passwd; 
		$this->name 		= $user_name;
		$this->email		= $user_email;
		$this->power		= $user_power;	
		$this->phone		= $user_phone;
		$this->date  		= $regist_date;	
	}
	
    public function setName($new_name) {
    	$this->name = $new_name;    	
    }
    
    public function getName() {
    	return $this->name;
    }

    /**
     * @param char $new_password --without encripted
     */
    public function setPasswd($new_password) {
    	$this->passwd = $new_password;
    	 
    }   
    
    public function getPasswd() {
    	return $this->passwd;
    }
    

    public function setPower($new_power) {
    	$this->power = $new_power;
    
    }
    
    public function getPower() {
    	return $this->power;
    }
    

    public function setID($new_ID) {
    	$this->id = $new_ID;
    
    }   
     
    public function getID() {
    	return $this->id;
    }
    

    public function setEmail($new_email) {
    	$this->email = $new_email;
    
    }
    
    public function getEmail() {
    	return $this->email;
    }
    
    public function setPhone($new_phone) {
    	$this->phone = $new_phone;
    }
    
    public function getPhone() {
    	return $this->phone;
    }
    
    public function setDate($regist_date) {
    	$this->date = $regist_date;    
    }
    
    public function getDate() {
    	return $this->date;
    }
    
}
