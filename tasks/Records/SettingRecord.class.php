<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'tasks/Records/Record.class.php';
require_once 'tasks/Records/MongoRecord.class.php';

class SettingRecord extends MongoRecord
{
	public function __construct()
	{
		parent::__construct();
		$this->_collection = 'cl_yl_setting_record';//'cl_yl_setting_record_'.date('Ymd');		
	}
		
	private  function __selectDatabase()
	{
		$this->_db = 'db_yl_cs_setting_record';
		return true;
	}
	
	public function saveRecord($nCoolType, Record $record)
	{
		try {
			$this->__selectDatabase($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('SettingRecord::saveRecord():connect() failed', 'log');
				return false;
			}
			$this->addIndex(array('insert_time'=>-1, 'id'=>1, 'cpid'=>1, 'type'=>1, 'product'=>1, 'cyid'=>1, 'meid'=>1, 'net'=>1));
						
			$result = $this->_mongo->insert($this->_collection, object_to_array($record));
			if($result === false){
				Log::write('SettingRecord::saveRecord():insert() failed', 'log');
				return false;
			}
			
			return true;				
		}catch (Exception $e){
			Log::write('SettingRecord::saveRecord() exception, mongErr:'.$this->_mongo->getError()
						.' err:'
						.' file:'.$e->getFile()
						.' line:'.$e->getLine()
						.' message:'.$e->getMessage()
						.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
}