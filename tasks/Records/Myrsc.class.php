<?php
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';
require_once 'configs/config.php';

class Myrsc extends Record
{
	public function __construct()
	{	
		parent::__construct();
	}
	
	public function setRecord()
	{
		
		$nCoolType 	 = isset($_GET['type'])?$_GET['type']:'';
		$this->setCoolType($nCoolType);
		
		parent::setParam();
		$this->checkParam();
	}

	public function checkParam()
	{
		parent::checkParam();
	}
}