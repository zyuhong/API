<?php
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'lib/MemDb.lib.php';

require_once 'tasks/widget/WidgetFactory.class.php';
require_once 'tasks/widget/Widget.class.php';

class WidgetDb extends DBManager
{
	public function __construct()
	{
		global $g_arr_db_config;
		$dbConfig = $g_arr_db_config['coolshow'];
		$this->connectMySqlPara($dbConfig);
	}
	
	public function getThemes(WidgetThemes $widget)
	{
		if(!$widget){
			Log::write('WidgetDb::getThemes():widget is null', 'log');
			return false;
		}
		
		$sql = $widget->getSelectWidgetSql();
		if(!$sql){
			Log::write('WidgetDb::getThemes():getSelectWidgetSql() Sql is empty', 'log');
			return false;
		}
		
		$rows = $this->executeQuery($sql);
// 		Log::write('WidgetDb::getThemes():executeQuery() Sql:'.$sql.' failed', 'log');
		if($rows === false){
			Log::write('WidgetDb::getThemes():executeQuery() Sql:'.$sql.' failed', 'log');
			return false;
		}
		return $rows;
	}
	
	public function insertThemes(WidgetThemes $widget)
	{
		if(!$widget){
			Log::write('WidgetDb::insertThemes():widget is null', 'log');
			return false;
		}
		
		$sql = $widget->getInsertWidgetSql();
		if(!$sql){
			Log::write("WidgetDb::insertThemes():getInsertWidgetSql() Sql is empty", "log");
			return false;
		}
		
		$rows = $this->executeSql($sql);
		if($rows === false){
			Log::write("WidgetDb::insertThemes():executeSql() Sql:'.$sql.' failed", "log");
			return false;
		}
		return $rows;
	}
}