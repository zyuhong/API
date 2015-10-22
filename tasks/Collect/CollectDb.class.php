<?php
require_once 'configs/config.php';
require_once 'lib/MemDb.lib.php';
require_once 'lib/DBManager.lib.php';

class CollectDb extends DBManager{
	
	public function __construct($dbConfig=array())
	{
		if(!$dbConfig){
			global $g_arr_db_config;
			$dbConfig = $g_arr_db_config['coolshow_charge_record'];
		}
		$this->connectMySqlPara($dbConfig);
	}
	
	public function getRecords($sql)
	{
		return $this->executeQuery($sql);
	}
	
	public function getRecordsCount($sql)
	{
		return $this->executeScan($sql);
	}
	
	public function setRecords($sql)
	{
		return $this->executeSql($sql);
	}
}