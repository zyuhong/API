<?php
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';
class Apply extends Record
{
	public $id;			//资源ID
	public $cpid;		//资源的公共ID
	public $applytype;	//应用类型
		
	public function __construct()
	{	
		parent::__construct();
		$this->id 			= '';	
		$this->cpid			= '';	
		$this->applytype	= 0;
	}
	
	public function setRecord()
	{
		$this->id 		= isset($_GET['id'])?$_GET['id']:'';
		$this->cpid		= isset($_GET['cpid'])?$_GET['cpid']:'';
		$this->applytype= (int)(isset($_GET['applytype'])?$_GET['applytype']:0);
		
		parent::setParam();
		$this->checkParam();
	}

	public function checkParam()
	{
		$this->id 		= sql_check_str($this->id, 64);
		$this->cpid 	= sql_check_str($this->cpid, 64);
		parent::checkParam();
	}
}