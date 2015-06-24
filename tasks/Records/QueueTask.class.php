<?php
require_once 'configs/config.php';
require_once 'lib/CRedis.lib.php';

class QueueTask
{
	private $_redis;
	public function __construct()
	{
		global $g_arr_queue_config;
		$this->_redis = new CRedis($g_arr_queue_config['server']);
	}

	public function getQueue($strKey, $nCoolType)
	{
		global $g_arr_queue_config;
		return $g_arr_queue_config[$strKey][$nCoolType];
	}
	
	public function push($strKey, $nCoolType, $record, $strIncr)
	{
		$key = $this->getQueue($strKey, $nCoolType);
		$this->_redis->lpush($key, $record);
		
		$this->_redis->incr($strIncr);
	}
}