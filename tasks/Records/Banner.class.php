<?php
require_once 'tasks/Records/Record.class.php';
class Banner extends Record
{
	
	public function __construct()
	{	
		parent::__construct();
	}
	
	public function setRecord()
	{
		$this->product 	= trim(isset($_GET['product'])?$_GET['product']:'');
		$this->width 	= (int)(isset($_GET['width'])?$_GET['width']:720);
		$this->height 	= (int)(isset($_GET['height'])?$_GET['height']:1280);
		$this->type 	= (int)(isset($_GET['reqType'])?$_GET['reqType']:0);		
		$this->type		= (int)(isset($_GET['code'])?$_GET['code']:$this->type);	//兼容旧版本未做动态分类的代码，新版本实际类型为code字段值
		$this->channel 	= (int)(isset($_GET['chanel'])?$_GET['chanel']:0);
		$this->vercode 	= (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);

		parent::setParam();
		
		$this->checkParam();
	}
}