<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'tasks/Records/MongoRecord.class.php';
require_once 'tasks/Records/Record.class.php';

class BannerRecord extends MongoRecord
{
	public function __construct()
	{
		parent::__construct();
		$this->_collection = 'cl_yl_banner_record_'.date('Ymd');		
	}
		
	public function saveRecord($nCoolType, Record $record)
	{
		try {
			$this->_setDatabase($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('BannerRecord::saveRecord():connect() failed', 'log');
				return false;
			}
				
			$this->addIndex(array('insert_time'=>-1, 'type'=>1, 'product'=>1, 'cyid'=>1, 'meid'=>1, 'net'=>1));
			$result = $this->_mongo->insert($this->_collection, object_to_array($record));
			if($result === false){
				Log::write('BannerRecord::saveRecord():insert() failed', 'log');
				return false;
			}
			
			return true;				
		}catch (Exception $e){
			Log::write('BannerRecord::saveRecord() exception, mongErr:'.$this->_mongo->getError()
						.' err:'
						.' file:'.$e->getFile()
						.' line:'.$e->getLine()
						.' message:'.$e->getMessage()
						.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
}