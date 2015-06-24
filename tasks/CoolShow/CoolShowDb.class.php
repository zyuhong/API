<?php
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';

class CoolShowDb extends DBManager{
	
	public function __construct($dbConfig=array())
	{
		if(!$dbConfig){
			global $g_arr_db_config;
			$dbConfig = $g_arr_db_config['coolshow'];
		}
		$this->connectMySqlPara($dbConfig);
	}
	
	public function getCoolShow($sql)
	{
		return $this->executeQuery($sql);
	}
	
	public function getCoolShowCount($sql)
	{
		return $this->executeScan($sql);
	}
}