<?php
class MyDesignerProtocol 
{
	public $cyid;	//设计师CYID
	public $name; 	//设计师昵称 	
	public $date; 	//关注时间
	function __construct(){
		$this->cyid = '';	
		$this->name = '';
		$this->date = date('Y-m-d');
	}
	
	public function setProtocol($row)
	{
		$this->cyid	= isset($row['authorid'])?$row['authorid']:'';
		$this->name = isset($row['authorname'])?$row['authorname']:'';
		$strDate	= isset($row['insert_time'])?$row['insert_time']:'';
		$this->date = strftime('%Y-%m-%d', strtotime($strDate));
	}
}