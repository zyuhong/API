<?php
require_once 'tasks/Records/Record.class.php';

class Widgets extends Record
{
	
	public function __construct()
	{	
		parent::__construct();
	}
	
	public function setRecord()
	{
		$this->product 	= trim(isset($_GET['product'])?$_GET['product']:'');
// 		$this->width 	= isset($_GET['width'])?$_GET['width']:720;
// 		$this->height 	= isset($_GET['height'])?$_GET['height']:1280;	
		$this->vercode 	= (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
		parent::setParam();
		
		$this->checkParam();
	}
}