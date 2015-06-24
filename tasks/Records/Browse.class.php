<?php
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';
class Browse extends Record
{
	public $id;			//资源ID
	public $cpid;		//资源的公共ID
	public $url;		//资源的公共ID
		
	public function __construct()
	{	
		parent::__construct();
		$this->id 	= '';	
		$this->cpid	= '';	
		$this->url	= '';		
	}
	
	public function setRecord()
	{
		$this->id 		= isset($_GET['id'])?$_GET['id']:'';
		$this->cpid		= isset($_GET['cpid'])?$_GET['cpid']:'';
		if(empty($this->cpid))$this->cpid = $this->id;
		$this->url		= '';//isset($_GET['url'])?$_GET['url']:'';
		$this->type 	= (int)(isset($_GET['type'])?$_GET['type']:0);
		$this->channel 	= (int)(isset($_GET['channel'])?$_GET['channel']:0);
		
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