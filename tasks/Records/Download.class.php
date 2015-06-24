<?php
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';

class Download extends Record 
{
	public $id;			//资源ID
	public $cpid;		//资源的公共ID
	public $author;		//作者
	public $url;		//地址
	
	public function __construct()
	{	
		parent::__construct();
		$this->id	 	 = '';
		$this->cpid		 = '';
		$this->url	 	 = '';
		$this->author	 = '';
	}

	public function setRecord()
	{
		$this->id 		= isset($_GET['id'])?$_GET['id']:'';				
		$this->cpid 	= isset($_GET['cpid'])?$_GET['cpid']:'';
 		$this->url		= '';//isset($_GET['url'])?$_GET['url']:0;
		$this->type 	= (int)(isset($_GET['type'])?$_GET['type']:0);
		$this->channel 	= (int)(isset($_GET['channel'])?$_GET['channel']:0);
		$this->author	= isset($_GET['author'])?$_GET['author']:0;
		
		parent::setParam();
		
		$this->checkParam();
	}
	
	public function checkParam()
	{
		$this->id 		= sql_check_str($this->id, 64);
		$this->cpid 	= sql_check_str($this->cpid, 64);
		$this->author 	= sql_check_str($this->author, 50);
		parent::checkParam();
	}
}