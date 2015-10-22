<?php
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';

require_once 'configs/config.php';
require_once 'tasks/widget/WidgetWallpaper.class.php';

class WidgetWpDb extends DBManager
{
	private $_size_res;
	private $_size_mid;
	private $_size_small;
	public function __construct()
	{
		$this->_size_res 	= '';
		$this->_size_mid	= '';
		$this->_size_small	= '';
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['androidesk']);
	}
	
	public function getSizeRes()
	{
		return $this->_size_res;
	}
	
	private function _getWpSizeTag(WidgetWallpaper $widget){
		
		$sql = $widget->getSelectWidgetSizeTagSql();
		$rows = $this->executeQuery($sql);
// 		Log::write('WidgetWpDb::_getAndroidWpSizeTag():executeQuery() sql:'.$sql, 'log');
		if($rows === false){
			Log::write('WidgetWpDb::_getAndroidWpSizeTag():executeQuery() sql'.$sql.' failed', 'log');
			return false;
		}
		if(count($rows) <=0 ){
			Log::write('WidgetWpDb::_getAndroidWpSizeTag():count(rows) == 0 ', 'log');
			return false;
		}
		return $rows;
	}
	
	public function getWidgetWp(WidgetWallpaper $widget, $start, $num)
	{
		if(!$widget){
			Log::write('WidgetWpDb::getWidgetWp():widget is null', 'log');
			return false;
		}
		
		$sql = $widget->getSelectWidgetSql($this->_size_res, $this->_size_mid, $this->_size_small, $start, $num);
		if(!$sql){
			Log::write("WidgetWpDb::getWidgetWp():getSelectWidgetSql() Sql is empty", "log");
			return false;
		}
		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("WidgetWpDb::getWidgetWp():executeQuery() Sql:'.$sql.' failed", "log");
			return false;
		}
		return $rows;
	}
	
	
	public function setWpSizeTag(Widget $widget)
	{
		if(!$widget){
			Log::write('WidgetWpDb::setWpSizeTag():widget is null', 'log');
			return false;
		}
		
		$arrSize = $this->_getWpSizeTag($widget);
		if(!$arrSize){
			Log::write('WidgetWpDb::setWpSizeTag():_getWpSizeTag() failed', 'log');
			return false;
		}
		
		$this->_size_res 	= $arrSize[0]['size_res'];
		$this->_size_mid	= $arrSize[0]['size_mid'];
		$this->_size_small	= $arrSize[0]['size_small'];
		return true;
	}
	
	public function getWidgetWpTag(WidgetWallpaper $widget)
	{
		if(!$widget){
			Log::write('WidgetWpDb::getWidgetWpTag():widget is null', 'log');
			return false;
		}
		
		$sql = $widget->getSelectWidgetTagSql($this->_size_res, $this->_size_mid, $this->_size_small);
		if(!$sql){
			Log::write("WidgetWpDb::getWidgetWpTag():getSelectWidgetTagSql() Sql is empty", "log");
			return false;
		}
		
		$rows = $this->executeQuery($sql);
// 		Log::write('WidgetWpDb::getWidgetWpTag():executeQuery() Sql:'.$sql, 'log');
		if($rows === false){
			Log::write("WidgetWpDb::getWidgetWpTag():executeQuery() Sql:'.$sql.' failed", "log");
			return false;
		}	
		return $rows;
	}
}