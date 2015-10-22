<?php
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'lib/MemDb.lib.php';
require_once 'tasks/Collect/CollectDb.class.php';
require_once 'tasks/Collect/Collect.class.php';
require_once 'tasks/protocol/DesignerProtocol.php';
require_once 'tasks/protocol/MyDesignerProtocol.php';

class CollectTask
{
	private $_mem;
	private $_db;

	public function __construct()
	{
		$this->_mem 	= null;
		$this->_db 		= null;
	}
	
	private function _getMem()
	{
		if(!$this->_mem){

			global $g_arr_memcache_config;
			$this->_mem = new MemDb();
			$this->_mem->connectMemcached($g_arr_memcache_config);
		}
		return $this->_mem;
	}
	
	private function _getDb($dbConfig = null)
	{
		if(!$this->_db){
			$this->_db = new CollectDb($dbConfig);
		}	
		return $this->_db;
	}
	
	public function setCollect($nCollect)
	{
		try {
// 			$bCollect = isset($_GET['collect'])?$_GET['collect']:0;
			$strCyid  = isset($_GET['cyid'])?$_GET['cyid']:'';
			$strAuthorId  = isset($_GET['author'])?$_GET['author']:'';
			$strAuthorName = isset($_GET['authorName'])?$_GET['authorName']:'';
			
			if(empty($strCyid) || empty($strAuthorId)){
				return get_rsp_result(false, 'cyid or author is empty');
			}
			
			$sql = Collect::getCheckSql($strCyid, $strAuthorId);
			$nCount = $this->_getDb()->getRecordsCount($sql);
			if($nCount === false){
				Log::write('CollectTask::setCollect():getRecordsCount() failed, SQL:'.$sql, 'log');
				return get_rsp_result(false, 'check collect error');
			}
			
			if($nCount <= 0){
				$sql = Collect::getInsertSql($strCyid, $strAuthorId, $strAuthorName);
			}else{
				$sql = Collect::getUpdateSql($nCollect, $strCyid, $strAuthorId);
			}
			
			$result = $this->_getDb()->setRecords($sql);
			if(!$result){
				Log::write('CollectTask::setCollect():setRecords() failed, SQL:'.$sql, 'log');
				return get_rsp_result(false, 'set collect error');
			}
			
			return get_rsp_result(true);
		}catch(Exception $e){
			Log::write('CollectTask::setCollect() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'set collect exception');
			return $result;
		}
	}

	/**
	 * 获取关注状态
	 * @param unknown_type $nCollect
	 * @return string
	 */
	public function getCollect($nCollect)
	{
		try{
			//$bCollect = isset($_GET['collect'])?$_GET['collect']:0;
			$strCyid  = isset($_GET['cyid'])?$_GET['cyid']:'';
			$strAuthorId  = isset($_GET['author'])?$_GET['author']:'';
			
			if(empty($strCyid) || empty($strAuthorId)){
				return get_rsp_result(false, 'cyid or author is empty');
			}
		
			$sql = Collect::getSelectCollectStatusSql($strCyid, $strAuthorId);
			$rows = $this->_getDb()->getRecords($sql);
			if($rows === false){
				Log::write('CollectTask::getCollect():getRecords() failed, SQL:'.$sql, 'log');
				return get_rsp_result(false, 'check collect error');
			}
		
			$bCollect = 0;
			foreach ($rows as $row){
				$bCollect = isset($row['collect'])?$row['collect']:0;
			}
		
			$result = array('result'=>true, 'collect'=>(int)$bCollect);
			return json_encode($result);
		}catch(Exception $e){
			Log::write('CollectTask::getCollect() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get collect exception');
			return $result;
		}
	}
	
	public function getMyDesigner()
	{
		try{
			$strCyid  = isset($_GET['cyid'])?$_GET['cyid']:'';
			if(empty($strCyid)){
				return get_rsp_result(false, 'cyid is empty');
			}
		
			$sql = Collect::getSelectSql($strCyid);
			$rows = $this->_getDb()->getRecords($sql);
			if($rows === false){
				Log::write('CollectTask::getMyDesigner():getRecords() failed, SQL:'.$sql, 'log');
				return get_rsp_result(false, 'get collect error');
			}
			
			$arrDesigner = array();
			foreach ($rows as $row){
				$designer = new MyDesignerProtocol();
				$designer->setProtocol($row);
				$arrDesigner[] = $designer;
			}
			
			return json_encode(array('result'=>true, 'list'=>$arrDesigner));

		}catch(Exception $e){
			Log::write('CollectTask::getMyDesigner() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get collect exception');
			return $result;
		}
	}
	
	public function getDesigner()
	{
		try{
			$strCyid  = isset($_GET['cyid'])?$_GET['cyid']:'';
			if(empty($strCyid)){
				return get_rsp_result(false, 'cyid is empty');
			}
		
			$sql = Collect::getSelectDesignerSql($strCyid);
			
			global $g_arr_db_config;
			$dbConfig = $g_arr_db_config['designer'];
			$rows = $this->_getDb($dbConfig)->getRecords($sql);
			if($rows === false){
				Log::write('CollectTask::getDesigner():getRecords() failed, SQL:'.$sql, 'log');
				return get_rsp_result(false, 'get designer error');
			}
		
			$arrDesigner = array();
			foreach ($rows as $row){
				$designer = new DesignerProtocol();
				$designer->setProtocol($row);
				$arrDesigner[] = $designer;
			}
		
			return json_encode(array('result'=>true, 'designer'=>$arrDesigner));
		}catch(Exception $e){
			Log::write('CollectTask::getDesigner() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get designer exception');
			return $result;
		}
	}
}
