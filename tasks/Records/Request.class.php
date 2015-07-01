<?php
require_once 'tasks/Records/Record.class.php';
require_once 'configs/config.php';

class Request extends Record
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
		$this->type		= (int)(isset($_GET['type'])?$_GET['type']:$this->type);	
		$this->subtype	= (int)(isset($_GET['code'])?$_GET['code']:$this->type);	//兼容旧版本未做动态分类的代码，新版本实际类型为code字段值
		$this->subtype  = (int)(isset($_GET['subtype'])?$_GET['subtype']:$this->subtype);
		$this->channel 	= (int)(isset($_GET['chanel'])?$_GET['chanel']:0);
		$this->vercode 	= (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
		
		$nSort     = (int)(isset($_GET['sort'])?$_GET['sort']:0);
		$nCharge   = (int)(isset($_GET['charge'])?$_GET['charge']:2);
		if ($nSort == COOLXIU_SEARCH_CHOICE){
			$this->channel = REQUEST_CHANNEL_CHOICE;
		}
		if ($nCharge == 0){
			$this->channel 	= REQUEST_CHANNEL_CHARGE_NO;
		}
		if ($nCharge == 1){
			$this->channel 	= REQUEST_CHANNEL_CHARGE_YES;
		}
		
		$nCoolType = (int)(isset($_GET['moduletype'])?$_GET['moduletype']:'');
		if($nCoolType == COOLXIU_TYPE_THEMES_CONTACT){
			$this->channel 	= REQUEST_CHANNEL_CONTACT;
		}
		if($nCoolType == COOLXIU_TYPE_THEMES_ICON){
			$this->channel 	=  REQUEST_CHANNEL_ICON;
		}
		if($nCoolType == COOLXIU_TYPE_LIVE_WALLPAPER){
			$this->channel 	= REQUEST_CHANNEL_LIVEWP;
		}
		
		$this->setCoolType($nCoolType);
		
		parent::setParam();
		
		$this->checkParam();
	}
}