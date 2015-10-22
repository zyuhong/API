<?php
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'tasks/CoolXiu/Wallpaper.class.php';
require_once 'tasks/CoolXiu/themes.class.php';

class CoolShowDb extends DBManager{
	private $_memcached;
	
	public function __construct(){
		$this->_memcached = new MemDb();
		$this->connectMySqlCommit();
	}
	
	public function getWPBrowseUrlById($id){
		$coolxiu = new Wallpaper();
		$url = $this->_getUrlById($coolxiu, $id, Wallpaper::YL_WP_MID_URL);
		if($url === false){
			Log::write('CoolShowDb::getWPBrowseUrlById():_getWPUrlById() type:'.Wallpaper::YL_WP_MID_URL, 'log');
			return false;
		}
		return $url;
	}

	public function getThDownloadUrlById($id){
		$coolxiu = new Themes();
		$url = $this->_getUrlById($coolxiu, $id);
		if($url === false){
			Log::write('CoolShowDb::getThDownloadUrlById():_getUrlById() id:'.$id, 'log');
			return false;
		}
		return $url;
	}

	public function getWPDownloadUrlById($id){
		$coolxiu = new Wallpaper();
		$url = $this->_getUrlById($coolxiu, $id, Wallpaper::YL_WP_LARGE_URL);
		if($url === false){
			Log::write('CoolShowDb::getWPBrowseUrlById():_getUrlById() id:'.$id.' type:'.Wallpaper::YL_WP_LARGE_URL, 'log');
			return false;
		}
		return $url;
	}
	
	private function _getUrlById($coolxiu, $id, $type = 0){
		$sql = $coolxiu->getSelectUrlByIdSql($id, $type);
		
		$result = $this->_memcached->getSearchResult($sql);
		if($result){
			return $result;
		}
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write('CoolShowDb::_getWPUrlById():executeQuery() sql:'.$sql.' failed', 'log');
			return false;
		}
		global $g_arr_host;
		foreach ($rows as $row){
			$url = $g_arr_host['cdnhost'].$row['url'];
		}
		$result = $this->_memcached->setSearchResult($sql, $url);
		return $url;
	}
}