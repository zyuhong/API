<?php
class MyResProtocol 
{
	public $id;	
	public $date;	
	function __construct(){
		$this->id = '';	
		$this->date = date('Y-m-d');
	}
	
	public function setProtocol($row)
	{
		$this->id	= isset($row['cpid'])?$row['cpid']:'';
		$strDate		= isset($row['insert_time'])?$row['insert_time']:'';
		$this->date = strftime('%Y-%m-%d', strtotime($strDate));
	}
}