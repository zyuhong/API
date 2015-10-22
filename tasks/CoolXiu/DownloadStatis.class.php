<?php
require_once 'lib/writelog.lib.php';
require_once 'lib/mysql.lib.php';
require_once 'configs/config.php';
require_once 'CoolXiuFactory.class.php';

class DownloadStatis{
	private $sql_conn;
	private $count;
	
	function __construct(){
		$sql_conn = null;
	}	
	
	function connectMySql() {
		try {
			global $g_arr_db_conn;
			$this->sql_conn = new mysql($g_arr_db_conn['host'],
					$g_arr_db_conn['user'],
					$g_arr_db_conn['pwd'],
					$g_arr_db_conn['db'],
					"",
					$g_arr_db_conn['coding']);
		} catch (Exception $e) {
			Log::write("DownloadStatis::connectMySql(".$g_arr_db_conn['db'].") error","log");
			return false;
		}
		return true;
	}
	
	function updateDownloadCount($xiu_type, $id){
		$type_key = "themes";
		$coolxiu = CoolXiuFactory::getCoolXiu($xiu_type, $type_key);
		
		$result = $this->connectMySql();
		if(!$result){
			Log::write("DownloadStatis::updateDownloadCount():connectMySql() failed", "log");
			return false;
		}
		
		$result = $this->_getDownloadCount($coolxiu, $id);
		if(!$result){
			Log::write("DownloadStatis::updateDownloadCount():_getDownloadCount() failed", "log");
			return false;
		}
		
		$result = $this->_updateDownloadCount($coolxiu, $id, $this->count);
		if(!$result){
			Log::write("DownloadStatis::updateDownloadCount():_updateDownloadCount() failed", "log");
			return false;
		}
		return true;
	}
	
	private  function  _getDownloadCount($coolxiu, $id){
		if($this->sql_conn == null){
			return false;
		}

		$sql = $coolxiu->getDownloadCountSql($id);
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("DownloadStatis::_getDownloadCount():query()".$sql." error", "log");
			return false;
		}
		
		$rows = $this->sql_conn->fetch_assoc_rows();
		if (count($rows) == 0){
			return false;
		}		
		$this->count = (int)$rows['download_times'];
		return true;
	}
	
	private function _updateDownloadCount($coolxiu, $id, $count){
		$count += 1;
		$sql = $coolxiu->getUpdateCountSql($id, $count);
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("DownloadStatis::_getDownloadCount():query()".$sql." error", "log");
			return false;
		}
		return true;
	}
}
?>