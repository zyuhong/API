<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'tasks/Records/MongoRecord.class.php';
require_once 'tasks/Records/Download.class.php';
require_once 'tasks/Records/DownloadOrder.class.php';

class DownloadRecord extends MongoRecord
{
	public function __construct()
	{
		parent::__construct();
		$this->_collection = 'cl_yl_dl_record';
	}
		
	public function saveRecord($nCoolType, Record $record)
	{
		try {
			$this->_setDlDatabase($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('DownloadRecord::saveRecord():connect() failed', 'log');
				return false;
			}
			
			$this->_collection = 'cl_yl_dl_record';				
			$this->addIndex(array('insert_time'=>-1, 
								  'product'=>1, 'channel'=>1, 
								  'cpid'=>1, 'cyid'=>1, 'author'=>1, 
								  'cpcy'=>array('cpid'=>1, 'cyid'=>1)));
			
			$result = $this->_mongo->insert($this->_collection, object_to_array($record));
			if($result === false){
				Log::write('DownloadRecord::saveRecord():insert() failed', 'log');
				return false;
			}
			
			return true;				
		}catch (Exception $e){
			Log::write('DownloadRecord::saveRecord() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}

	public function saveOrderRecord($nCoolType, Record $record)
	{
		try {
			$this->_setOrderDatabase($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('DownloadRecord::saveOrderRecord():connect() failed', 'log');
				return false;
			}
				
			$this->_collection = 'cl_yl_order_record';
			$this->addIndex(array('insert_time'=>-1,
								  'order'=>1,	
								  'product'=>1, 'channel'=>1,
								  'id'=>1, 'cpid'=>1, 'cyid'=>1, 'author'=>1,
								  'cpcy'=>array('cpid'=>1, 'cyid'=>1)));
				
			$result = $this->_mongo->insert($this->_collection, object_to_array($record));
			if($result === false){
				Log::write('DownloadRecord::saveOrderRecord():insert() failed', 'log');
				return false;
			}
				
			return true;
		}catch (Exception $e){
			Log::write('DownloadRecord::saveOrderRecord() exception, mongErr:'.$this->_mongo->getError()
			.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
	
	public function updateOrderRecord($nCoolType, Record $record)
	{
		try {
			$this->_setOrderDatabase($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('DownloadRecord::updateOrderRecord():connect() failed', 'log');
				return false;
			}

			$this->_collection = 'cl_yl_order_record';				
			$result = $this->_mongo->update($this->_collection,
											array('status' => 1, 'isscore' => $record->isscore),
											array('order'=>$record->order),
											'set');
	
			if($result === false){
				Log::write('DownloadRecord::updateOrderRecord():update() failed', 'log');
				return false;
			}
			return true;
		}catch (Exception $e){
			Log::write('DownloadRecord::updateOrderRecord() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
	
	public function saveCountRecord($nCoolType, DownloadCount $dlCount)
	{
		try {		
			$this->_setOrderDatabase($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('DownloadRecord::saveCountRecord():connect() failed', 'log');
				return false;
			}
			
			$this->_collection = 'cl_yl_dlcount_record';
			$result = $this->_mongo->select($this->_collection,
												array('cpid'   =>$dlCount->cpid,
													  'channel'=>$dlCount->channel),
												array('count'));
			if($result <= 0 || !$result){
				$this->addIndex(array('update_time'=>-1, 'cpid'=>1, 'id'=>1, 'count'=>-1, 'author'=>1));
				$result = $this->_mongo->insert($this->_collection, object_to_array($dlCount));
				
			}else{
				$result = $this->_mongo->update($this->_collection,
												array('count'=>1),
												array('cpid'=>$dlCount->cpid, 'channel'=>$dlCount->channel),
												'inc');
			}
				
			if($result === false){
				Log::write('DownloadRecord::saverCountRecord():insert() failed', 'log');
				return false;
			}
			return true;

		}catch (Exception $e){
			Log::write('DownloadRecord::saverCountRecord() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
	
}