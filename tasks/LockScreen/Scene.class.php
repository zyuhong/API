<?php
require_once 'tasks/LockScreen/Screen.class.php';
require_once 'tasks/LockScreen/ScreenSql.sql.php';

class Scene extends Screen
{
	public $sceneCode;			//场景编码
	public $zhName;				//中文名称
	public $enName;				//英文名称
	public $icon;				//图标以http开头的绝对地址
	public $iconHd;				//高清预览图地址
	public $iconMicro;			//微型图地址
	public $intro;				//场景说明
	
	public function __construct()
	{
		$this->sceneCode	= 0;		
		$this->zhName		= '';			
		$this->enName		= '';			
		$this->icon			= '';				
		$this->iconHd		= '';			
		$this->iconMicro	= '';			
		$this->intro		= '';		
		$this->_table		= 'tb_yl_scene';
	}
	
	public function setScreen($arrScene)
	{
		$this->sceneCode	= isset($arrScene['sceneCode'])?$arrScene['sceneCode']:0;
		$this->zhName		= isset($arrScene['zhName'])?$arrScene['zhName']:'';
		$this->enName		= isset($arrScene['enName'])?$arrScene['enName']:'';
		$this->icon			= isset($arrScene['icon'])?$arrScene['icon']:'';
		$this->iconHd		= isset($arrScene['iconHd'])?$arrScene['iconHd']:'';
		$this->iconMicro	= isset($arrScene['iconMicro'])?$arrScene['iconMicro']:'';
		$this->intro		= isset($arrScene['intro'])?$arrScene['intro']:'';
		$this->createTime 	= isset($arrLable['createTime'])?$arrLable['createTime']:date("Y-m-d H:i:s");
		$this->updateTime	= isset($arrLable['updateTime'])?$arrLable['updateTime']:date('Y-m-d H:m:s');
	}
	
	public function checkScreen()
	{
		if ($this->sceneCode == 0);{
			return false;
		}
		if (empty($this->zhName)){
			return false;
		}
		if(empty($this->enName)){
			return false;
		}
		if(empty($this->icon)){
			return false;
		}
		if(empty($this->iconHd)){
			return false;
		}
		if(empty($this->iconMicro)){
			return false;
		}
		if(empty($this->intro)){
			return false;
		}
		return true;
	}

	public function getSelectScreenSql($kernelcode, $nStart, $nNum, $vercode = 0, $newver = false)
	{
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND scene.ischarge = 0 ';
		}
		if(!$newver){
			$sql = sprintf(SQL_SELECT_SCENE, $kernelcode, $strIsCharge, $nStart, $nNum);
		}else{
			$sql = sprintf(SQL_SELECT_SCENE_APK, $nStart, $nNum);
		}	
		
		return $sql;		
	}
	
	public function getCountScreenSql($kernelcode, $vercode = 0, $newver = false)
	{
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND scene.ischarge = 0 ';
		}
		
		if(!$newver){
			$sql = sprintf(SQL_COUNT_SCENE, $kernelcode, $strIsCharge);
		}else{
			$sql = SQL_COUNT_SCENE_APK;
		}
		return $sql;
	}

	static public function getSelectScreenWithIdSql($sceneCode, $kernelcode)
	{
		$sql = sprintf(SQL_SELECT_SCENE_WITH_ID, $sceneCode, $kernelcode);
		return $sql;
	}
	
	static public function getSelectSceneByIDSql($id)
	{
		$sql = sprintf(SQL_SELECT_SCENE_BY_ID, $id);
		return $sql;
	}
}