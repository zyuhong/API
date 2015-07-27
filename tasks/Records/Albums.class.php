<?php
require_once 'tasks/Records/Record.class.php';
class Albums extends Record
{
	public $height;
	public $width;
	public $cpid;
	public function __construct()
	{	
		parent::__construct();
		
		$this->width  = 0;
		$this->height = 0;
		$this->cpid   = ''; 
	}
	
	public function setRecord()
	{
		$this->product 	= trim(isset($_GET['product'])?$_GET['product']:'');
		$this->width 	= (int)(isset($_GET['width'])?$_GET['width']:720);
		$this->height 	= (int)(isset($_GET['height'])?$_GET['height']:1280);
		$this->channel 	= (int)(isset($_GET['channel'])?$_GET['channel']:5);
		$this->cpid     = isset($_GET['id'])?$_GET['id']:'';

		$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:0);
		$this->setCoolType($nCoolType);
		
		parent::setParam();
		
		$this->checkParam();
	}
}