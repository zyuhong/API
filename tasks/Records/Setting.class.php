<?php
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';
class Setting extends Record
{
	public $item;		//设置项
	public $state;		//设置状态
	public $cid;		//推送的终端ID
		
	public function __construct()
	{	
		parent::__construct();
		$this->item 	= '';	
		$this->state	= '';	
		$this->cid		= '';
	}
	
	public function setRecord()
	{
		if(isset($_POST['statis'])){
			$json_param = isset($_POST['statis'])?$_POST['statis']:'';
		
			$json_param = stripslashes($json_param);
			$arr_param = json_decode($json_param, true);
		
			$this->item 	= isset($arr_param['settingItem'])?$arr_param['settingItem']:'';
			$this->state	= isset($arr_param['state'])?$arr_param['state']:0;
			$this->cid		= isset($arr_param['cid'])?$arr_param['cid']:0;
		}
		
		parent::setParam();
		
		$this->checkParam();
	}
	
	public function checkParam()
	{
		$this->item 		= sql_check_str($this->item, 64);
		$this->cid 			= sql_check_str($this->cid, 64);
		$this->state 		= sql_check_str($this->state, 64);
		parent::checkParam();
	}
}