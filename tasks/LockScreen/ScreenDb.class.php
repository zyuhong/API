<?php
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'lib/MemDb.lib.php';
require_once 'tasks/LockScreen/Scene.class.php';
require_once 'tasks/protocol/ScreenProtocol.php';

class ScreenDb extends DBManager
{
	private $_screen;
	private $_memcached;
	public function __construct($db)
	{
		$this->_memcached = new MemDb();
		global $g_arr_memcache_config;
		$this->_memcached->connectMemcached($g_arr_memcache_config);
		$this->_screen = new Scene();
// 		global $g_arr_lockscreen_db_conn;
		$this->connectMySqlPara($db);
	}
	
	public function searchScreen($kernelcode, $nStart, $nNum, $vercode = 0, $newver = false)
	{
		try {
			$sql = $this->_screen->getSelectScreenSql($kernelcode, $nStart, $nNum, $vercode, $newver);
			$result = $this->_memcached->getSearchResult($sql);
			if ($result) {
				return json_encode($result);
			}
			
			$rows = $this->executeQuery($sql);
			if ($rows === false) {
				Log::write('ScreenDb::searchScreen() SQL:'.$sql.' failed', 'log');
				return false;
			}
			// 查询当前请求返回的壁纸量
			$nRspNum = $this->getQueryCount();
			
			$arrSceen = array();
			foreach ($rows as $row) {
				$screenProtocol = new ScreenProtocol();
				$screenProtocol->setProtocol($row, 0, $newver);
				array_push($arrSceen, $screenProtocol);
			}
			
			$nCount = $this->_getScreenCount($kernelcode, $vercode, $newver);
			if (false === $nCount) {
				Log::write("ScreenDb::searchScreen():_getScreenCount() failed", "log");
				return false;
			}
				
			$strJsonRsp =  array('total_number'=>$nCount,
								'ret_number'=>$nRspNum,
								'lockscreens'=>$arrSceen);
			
			$result = $this->_memcached->setSearchResult($sql, $strJsonRsp, 12*60*60);
			if (! $result) {
				Log::write("ScreenDb::searchScreen():setSearchResult() failed", "log");
			}
		} catch (Exception $e) {
			Log::write("ScreenDb::searchScreen() Exception, error:".$e->getMessage(), "log");
			return true;
		}
		
		return json_encode($strJsonRsp);
	}

	private function _getScene($sql)
	{
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write('ScreenDb::_getScene() SQL:'.$sql.' failed', 'log');
			return false;
		}
		
		return $rows;
	}
	
	private function _getProtocol($sql)
	{
		$rows = $this->_getScene($sql);
		if($rows === false){
			Log::write('ScreenDb::_getProtocol():_getScene() SQL:'.$sql.' failed', 'log');
			return false;
		}
		$arrProtocol = array();
		foreach ($rows as $row){
			$screenProtocol = new ScreenProtocol();
			$screenProtocol->setProtocol($row);
			array_push($arrProtocol, $screenProtocol);
		}
	
		return $arrProtocol;
	}
	
	public function getRsc($sceneCode, $kernelCode)
	{
		try{	
			$sql = Scene::getSelectScreenWithIdSql($sceneCode, $kernelCode);
				
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
				
			$protocol = $this->_getProtocol($sql);
			if($protocol === false){
				Log::write("ScreenDb::getSrc():_getProtocol() failed", "log");
				return false;
			}
				
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("ScreenDb::getSrc():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("ScreenDb::getSrc() Exception, error:".$e->getMessage(), "log");
			return true;
		}
	
		return $protocol;
	}
	
	private function _getScreenCount($kernelcode, $vercode = 0, $newver = false){
		$sql = $this->_screen->getCountScreenSql($kernelcode, $vercode, $newver);
		$count = $this->executeScan($sql);
		if($count === false){
			log::write("ScreenDb::_getScreenCount():executeScan() SQL: ".$sql."faled", "log");
			return false;
		}
		return $count;
	}
	
	public function getSceneById($strId)
    {
		$sql = $this->_screen->getSelectSceneByIDSql($strId);
		$result = $this->_memcached->getSearchResult($sql);
		if ($result) {
			return $result;
		}
		
		$rows = $this->executeQuery($sql);
		if ($rows === false) {
			Log::write("ScreenDb::getScreenById():executeQuery() sql: ".$sql, "log");
			return false;
		}
		
		global $g_arr_host;
		foreach ($rows as $row) {
			$url = $g_arr_host['cdnhost'].$row['url'];
		}
		$result = $this->_memcached->setSearchResult($sql, $url);
		if (! $result) {
			Log::write("ScreenDb::getScreenById():setSearchResult() failed", "log");
		}
		return $url;
	}
}