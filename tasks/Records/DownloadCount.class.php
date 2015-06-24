<?php
require_once 'public/public.php';
require_once 'tasks/Records/Download.class.php';

class DownloadCount
{
	public $id;				//资源ID
	public $cpid;			//资源的公共ID
	public $author;			//作者
	public $count;			//下载数量
	public $channel;		//下载渠道
	public $update_time;	//更新时间
	
	public function __construct()
	{	
		$this->id			= '';
		$this->cpid			= '';
		$this->author		= '';
		$this->count	 	= 1;
		$this->channel		= 0;		
		$this->update_time	= date("Y-m-d H:i:s");
	}

	public function setRecord()
	{
		$this->id 		= isset($_GET['id'])?$_GET['id']:'';
		$this->cpid 	= isset($_GET['cpid'])?$_GET['cpid']:$this->id;
		$this->author	= isset($_GET['author'])?$_GET['author']:0;
		$this->channel	= (int)(isset($_GET['channel'])?$_GET['channel']:0);
		
		$this->checkParam();
	}
	public function checkParam()
	{
		$this->id 		= sql_check_str($this->id, 64);
		$this->cpid 	= sql_check_str($this->cpid, 64);
		$this->author 	= sql_check_str($this->author, 50);
	}
}