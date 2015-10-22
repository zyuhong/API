<?php
require_once 'lib/WriteLog.lib.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'public/public.php';
require_once 'tasks/ring/Ring.class.php';
require_once 'tasks/protocol/RingProtocol.php';

class RingDb extends DBManager{
	private $_ring;
	private $_memcached;
	function __construct(){
		
		$this->_memcached = new MemDb();
		$this->_ring = new Ring();
		$this->connectMySqlCommit();
	}	

	function setRingParam($id, $type, $name, $note, $fname, $url, $size, $md5, $insert_user){
		$this->_ring->setRing($id, $type, $name, $note, $fname, $url, $size, $md5, $insert_user);
	}
	
	function getRingType($type){
		$sql = $this->_ring->getSelectRingTypeSql($type);
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("RingDb::getRingType():executeQuery() sql: ".$sql." failed", "log");
			return false;
		}
		
		$typename = "";
		foreach ($rows as $row){
			$typename = $row['name'];
		}
		return $typename;
	}
	
	function insertRing2DB(){
		$sql = $this->_ring->getInsertRingSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("RingDb::insertRing2DB():executeSql() sql: ".$sql." failed", "log");
			return false;
		}
		return true;
	}
	
	private function _getRingList($sql, $nChannel = 0){
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("RingDb::getRingType():executeQuery() sql: ".$sql." failed", "log");
			return false;
		}
		$arr_ring = array();
		foreach ($rows as $row){
			$ring_protocol = new RingProtocol();
			$ring_protocol->setRingByDB($row, $nChannel);
			array_push($arr_ring, $ring_protocol);
		}
		return $arr_ring;
	}
	
	private function _getRingCount($type, $subtype, $vercode = 0){
		$sql = Ring::getCountRingSql($type, $subtype, $vercode);
		$count = $this->executeScan($sql);
		if($count === false){
			Log::write("RingDb::_getRingCount():executeScan() sql: ".$sql." failed", "log");
			return false;
		}
		
		return $count;
	}
	
	private function _getRing($sql, $channel = 0)
	{
		if(empty($sql)){
			Log::write('RingDb::_getRing() sql is empty', 'log');
			return false;
		}
		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write('RingDb::_getRing():executeQuery() SQL:'.$sql.' failed', 'log');
			return false;
		}
		$arrProtocol = array();
		foreach ($rows as $row){
			$protocol = $this->_getProtocol($row, $channel);
			array_push($arrProtocol, $protocol);
		}
		
		return $arrProtocol;
	}

	
	private function _getRingBanner($sql, $channel = 0)
	{
		if(empty($sql)){
			Log::write('RingDb::_getRingBanner() sql is empty', 'log');
			return false;
		}
	
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write('RingDb::_getRingBanner():executeQuery() SQL:'.$sql.' failed', 'log');
			return false;
		}
		
		$arrBanner = array();
		$strBannerId = '';
		foreach($rows as $row){
			if(!array_key_exists($row['bannerid'], $arrBanner)){
				$strBannerId = $row['bannerid'];
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
			
			$ring = new RingProtocol();
			$ring->setRingByDB($row, $channel);
			
			$arrBanner[$strBannerId]->setBannerRes($row['id'], $ring);
		}
		
		$arrProtocol = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol('ring');
			array_push($arrProtocol, $temBanner);
		}
		
		return $arrProtocol;
	}
	
	private function _getProtocol($row, $channel = 0)
	{
		$ring_protocol = new RingProtocol();
		$ring_protocol->setRingByDB($row, $channel);
		return $ring_protocol;
	}
	
	public function getBanner($channel = 0)
	{
		try{
			$sql = Ring::getSelectBannerSql();
// 			$result = $this->_memcached->getSearchResult($sql);
// 			if($result){
// 				return $result;
// 			}
				
			$protocol = $this->_getRingBanner($sql, $channel);
			if(!$protocol){
				Log::write("RingDb::getBanner():_getRingBanner() failed", "log");
				return false;
			}
			
// 			$result = $this->_memcached->setSearchResult($sql, $protocol);
// 			if(!$result){
// 				Log::write("RingDb::getBanner():setSearchResult() failed", "log");
// 			}
		}catch(Exception $e){
			Log::write("RingDb::getBanner() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return $protocol;
	}
	
	public function getRsc($id, $channel = 0){
		try{
			$sql = Ring::getSelectSrcWithIDSql($id);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
			
			$protocol = $this->_getRing($sql, $channel);
			if(!$protocol){
				Log::write("RingDb::getSrc():_getProtocol() failed", "log");
				return false;
			}
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("RingDb::getSrc():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("RingDb::getSrc() Exception: ".$e->getMessage(), "log");
			return false;
		}
	
		return $protocol;
	}
	
	public function getAlbum($channel = 0)
	{
		try{
			$sql = Ring::getSelectAlbumsSql();
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
		
			$protocol = $this->_getRing($sql, $channel);
			if(!$protocol){
				Log::write("RingDb::getAlbum():_getProtocol() failed", "log");
				return false;
			}
			
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("RingDb::getAlbum():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("RingDb::getAlbum() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return $protocol;
	}
	
	public function searchRing($type, $start, $limit, $vercode = 0, $subtype = 0){
		try{
			$sql = Ring::getSelectRingByLimitSql($type, $start, $limit, $vercode, $subtype);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return json_encode($result);
			}			
			
			$arr_ring = $this->_getRingList($sql);
			if($arr_ring === false){
				Log::write("RingDb::searchRing():_getRingList() failed", "log");
				return false;
			}
			
			$rsp_num = $this->getQueryCount();
				
			$count = (int)$this->_getRingCount($type, $subtype, $vercode);
			if($count === false){
				Log::write("RingDb::searchRing():_getRingCount() failed", "log");
				return false;
			}
			
			$json_rsp =  array('total_number'=>$count,
								'ret_number'=>$rsp_num,
								"ring"=>$arr_ring);
				
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 3*60*60);
			if(!$result){
				Log::write("RingDb::searchRing():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("RingDb::searchRing() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return json_encode($json_rsp);		
	}	
	
	public function searchRingForWeb($type, $subtype,  $sorttype, $channel, $start, $limit)
	{
		try{
			if($sorttype == COOLXIU_SEARCH_LAST){
				$sql  = Ring::getRingLastListSql($type, $subtype, $start, $limit);
			}else
				if($sorttype == COOLXIU_SEARCH_HOT){
				$sql  = Ring::getRingHotListSql($type, $subtype, $start, $limit);
			}else{
				$sql  = Ring::getRingListForWebSql($type, $subtype, $start, $limit);
			}
			if(!$sql){
				Log::write("CoolXiuDb::searchRingForWeb() failed Sql is empty", "log");
				return false;
			}
			
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return json_encode($result);
			}
				
			$arr_ring = $this->_getRingList($sql, $channel);
			if($arr_ring === false){
				Log::write("RingDb::searchRingForWeb():_getRingList() failed", "log");
				return false;
			}
				
			$rsp_num = $this->getQueryCount();
			
			$count = (int)$this->_getRingCount($type, $subtype);
			if($count === false){
				Log::write("RingDb::searchRing():_getRingCount() failed", "log");
				return false;
			}
				
			$json_rsp =  array('total_number' => $count,
							   'ret_number' => $rsp_num,
							   'ring' => $arr_ring);
			
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 24*60*60);
			if(!$result){
				Log::write("RingDb::searchRingForWeb():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("RingDb::searchRingForWeb() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return json_encode($json_rsp);
	}
	
	function getRingByID($id){
		$sql = $this->_ring->getSelectRingByIDSql($id);		
		$result = $this->_memcached->getSearchResult($sql);
		if($result){
			return $result;
		}
		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("RingDb::getRingByID():executeQuery() sql: ".$e->getMessage(), "log");
			return false;
		}
		global $g_arr_host;
		foreach ($rows as $row){
			$url = $g_arr_host['cdnhost'].$row['url'];
			//$this->_ring->setRingFromDB($row);
		}
		
		$result = $this->_memcached->setSearchResult($sql, $url);
		if(!$result){
			Log::write("RingDb::getRingByID():setSearchResult() failed", "log");
		}
		return $url;
// 		$file_path = $this->_ring->getRingFile(); 
// 		$file_name = $this->_ring->getRingFileName();
// 		return true;
	}
}