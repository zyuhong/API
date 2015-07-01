<?php
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';
require_once 'configs/config.php';

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
		
		$nCoolType 	 = isset($_GET['moduletype'])?$_GET['moduletype']:'';
		$this->setCoolType($nCoolType);
		
		if($nCoolType == COOLXIU_TYPE_THEMES_CONTACT){
			$this->applytype 	= REQUEST_CHANNEL_CONTACT;
		}	
		if($nCoolType == COOLXIU_TYPE_THEMES_ICON){
			$this->applytype 	= REQUEST_CHANNEL_ICON;
		}
		if($nCoolType == COOLXIU_TYPE_LIVE_WALLPAPER){
			$this->applytype 	= REQUEST_CHANNEL_LIVEWP;
		}
		
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