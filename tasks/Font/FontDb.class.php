<?php
require_once 'lib/WriteLog.lib.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'public/public.php';
require_once 'tasks/Font/Font.class.php';
require_once 'tasks/Font/FontPreview.class.php';
require_once 'tasks/protocol/FontProtocol.php';
require_once 'tasks/protocol/FontPreviewProtocol.php';


class FontDb extends DBManager{
	private $_font;
	private $_preview;
	private $_memcached;
		
	function __construct(){
		$this->_memcached = new MemDb();
		$this->_font = new Font();
		$this->_preview = new FontPreview();
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['coolshow']);
	}	

	public function setFont(Font $font)
	{
		$this->_font = $font;
	}

	public function setPreview(FontPreview $preview)
	{
		$this->_preview = $preview;
	}
	
	public function getFontRatio()
	{
		$sql = $this->_font->getSelectFontRatioSql();
		$rows = $this->executeQuery($sql);
		return  $rows;
	} 
	
	public function addFontPreview()
	{
		$sql = $this->_preview->getInsertPreviewSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("FontDb::insertFontPreview():executeSql() sql: ".$sql." failed", "log");
			return false;
		}
		return true;
	}
	
	private function insertFont()
	{
		$sql = $this->_font->getInsertFontSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("FontDb::insertFont2DB():executeSql() sql: ".$sql." failed", "log");
			return false;
		}
		return true;
	}
	
	private function insertFontPreview()
	{
		foreach ($this->_font->arrPreview as $preview){
			$sql = $preview->getInsertPreviewSql();
			$result = $this->executeSql($sql);
			if(!$result){
				Log::write("FontDb::insertFontPreview():executeSql() sql: ".$sql." failed", "log");
				return false;
			}
		}
		return true; 
	}
	
	public function insertFont2DB()
	{
		try{
			$this->beginTransaction();
			
			$result = $this->insertFont();
			if(!$result){
				Log::write("FontDb::insertFont2DB():insertFont() failed", "log");
				$this->roolback();
				return false;
			}
			
			$result = $this->insertFontPreview();
			if(!$result){
				Log::write("FontDb::insertFont2DB():insertFontPreview() failed", "log");
				$this->roolback();
				return false;
			}
			
			$this->endTransaction();
			
		}catch(Exception $e){
			Log::write("FontDb::insertFont2DB() Exception, error:".$e->getMessage(), "log");
			$this->roolback();
			return false;
		}
		return true;
	}
	
	private function _getFontPreviewList($sql){
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("FontDb::_getFontPreviewList():executeQuery() sql: ".$sql." failed", "log");
			return false;
		}
		$arr_font = array();
		foreach ($rows as $row){
			$font_preview_protocol = new FontPreviewProtocol();
			$font_preview_protocol->setFontByDB($row);
			array_push($arr_font, $font_preview_protocol);
		}
		return $arr_font;
	}
	
	private function _getFontList($sql, $channel = 0){
		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("FontDb::_getFontList():executeQuery() sql: ".$sql." failed", "log");
			return false;
		}
		
		$arr_font = array();
		foreach ($rows as $row){
			$font_protocol = new FontProtocol();
			$font_protocol->setFontByDB($row, $channel);
			array_push($arr_font, $font_protocol);
		}
		return $arr_font;
	}
	
	private function _getFontCount($vercode = 0){
		$sql = Font::getCountFontSql($vercode);
		$count = $this->executeScan($sql);
		if($count === false){
			Log::write("FontDb::_getFontCount():executeScan() sql: ".$sql." failed", "log");
			return false;
		}
		return $count;
	}

	function searchAllFont($start, $limit){
		try{
			$sql = $this->_font->getSelectAllFontByLimitSql($start, $limit);
				
			$arr_font = $this->_getFontList($sql);
			if($arr_font === false){
				Log::write("FontDb::searchFont():_getFontList() failed", "log");
				return false;
			}
				
			$rsp_num = $this->getQueryCount();
	
			$count = (int)$this->_getFontCount();
			if($count === false){
				Log::write("FontDb::searchFont():_getFontCount() failed", "log");
				return false;
			}
				
			$json_rsp =  array('total_number'=>$count,
					'ret_number'=>$rsp_num,
					"fonts"		=>$arr_font);
	
		}catch(Exception $e){
			Log::write("FontDb::searchFont() Exception: ".$e->getMessage(), "log");
			return false;
		}
	
		return json_encode($json_rsp);
	}
	
	function searchFont($width, $height, $start, $limit, $vercode = 0){
		try{
			$sql = Font::getSelectFontByLimitSql($start, $limit, $vercode);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return json_encode($result);
			}			
			
			$arr_font = $this->_getFontList($sql);
			if($arr_font === false){
				Log::write("FontDb::searchFont():_getFontList() failed", "log");
				return false;
			}
			
			$rsp_num = $this->getQueryCount();
				
			$count = (int)$this->_getFontCount($vercode);
			if($count === false){
				Log::write("FontDb::searchFont():_getFontCount() failed", "log");
				return false;
			}
			
			$json_rsp =  array('total_number'=>$count,
								'ret_number'=>$rsp_num,
								"fonts"		=>$arr_font);
				
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 24*60*60);
			if(!$result){
				Log::write("FontDb::searchFont():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("FontDb::searchFont() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return json_encode($json_rsp);		
	}	
	
	public function searchFontForWeb($sorttype, $channel = 0, $start = 0, $limit = 0)
	{
		try{
			if($sorttype == COOLXIU_SEARCH_LAST){
				$sql  = Font::getFontLastListSql( $start, $limit);
			}else
				if($sorttype == COOLXIU_SEARCH_HOT){
				$sql  = Font::getFontHotListSql( $start, $limit);
			}else{
				$sql  = Font::getFontListForWebSql( $start, $limit);
			}
			if(!$sql){
				Log::write("FontDb::searchFontForWeb() failed Sql is empty", "log");
				$result = get_rsp_result(false, 'param is error');
				return $result;
			}
			
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return json_encode($result);
			}

			$arr_font = $this->_getFontList($sql, $channel);
			if($arr_font === false){
				Log::write("FontDb::searchFont():_getFontList() failed", "log");
				$result = get_rsp_result(false, 'get font list error');
				return $result;
			}
				
			$rsp_num = $this->getQueryCount();
			
			$count = (int)$this->_getFontCount();
			if($count === false){
				Log::write("FontDb::searchFont():_getFontCount() failed", "log");
				$result = get_rsp_result(false, 'get font count error');
				return $result;
			}
				
			$json_rsp =  array('total_number'=>$count,
							   'ret_number'=>$rsp_num,
							   'fonts'		=>$arr_font);
			
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 24*60*60);
			if(!$result){
				Log::write("FontDb::searchFont():setSearchResult() failed", "log");
			}
			
		}catch(Exception $e){
			Log::write("FontDb::searchFont() Exception: ".$e->getMessage(), "log");
			$result = get_rsp_result(false, 'get font exception');
			return $result;
		}
		
		return json_encode($json_rsp);
	}
	
	
	private function _getFont($sql, $channel = 0)
	{
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("FontDb::_getFontList():executeQuery() sql: ".$sql." failed", "log");
			return false;
		}
		
		$arrFont = array();
		foreach ($rows as $row){
			$fontPro = new FontProtocol();
			$fontPro->setFontByDB($row, $channel);
			array_push($arrFont, $fontPro);
		}
		return $arrFont;
	}
	
	public function getRsc($id, $channel = 0){
		try{
			$sql = Font::getSelectFontByIDSql($id);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
				
			$protocol = $this->_getFont($sql, $channel);
			if($protocol === false){
				Log::write("FontDb::getSrc():_getFont() failed", "log");
				return false;
			}
				
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("FontDb::searchFont():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("FontDb::searchFont() Exception: ".$e->getMessage(), "log");
			return false;
		}
	
		return $protocol;
	}
	
	public function searchFontPreview($id){
		try{
			$sql = $this->_font->getSelectFontPreviewByIdSql($id);
				
			$arr_font_preview = $this->_getFontPreviewList($sql);
			if($arr_font_preview === false){
				Log::write("FontDb::searchFontPreview():_getFontPreviewList() failed", "log");
				return false;
			}
				
			$rsp_num = $this->getQueryCount();
			$json_rsp =  array('total_number'=>$rsp_num,
					'ret_number'=>$rsp_num,
					"previews"		=>$arr_font_preview);
		
		}catch(Exception $e){
			Log::write("FontDb::searchFontPreview() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return json_encode($json_rsp);
	}
	
	private function _getFontBanner($sql, $channel = 0)
	{
		if(empty($sql)){
			Log::write('FontDb::_getFontBanner() sql is empty', 'log');
			return false;
		}
	
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write('FontDb::_getFontBanner():executeQuery() SQL:'.$sql.' failed', 'log');
			return false;
		}
	
		$arrBanner = array();
		foreach($rows as $row){
			$strBannerId = $row['bannerid'];
			if(!array_key_exists($strBannerId, $arrBanner)){
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
				
			$font = new FontProtocol();
			$font->setFontByDB($row, $channel);
				
			$arrBanner[$strBannerId]->setBannerRes($row['id'], $font);
		}
	
		$arrProtocol = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol('fonts');
			array_push($arrProtocol, $temBanner);
		}
	
		return $arrProtocol;
	}
	
	public function getBanner($channel = 0)
	{
		try{
			$sql = Font::getSelectBannerSql();
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
		
			$protocol = $this->_getFontBanner($sql, $channel);
			if(!$protocol){
				Log::write("FontDb::getBanner():_getFontBanner() failed", "log");
				return false;
			}
				
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("FontDb::getBanner():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("FontDb::getBanner() Exception: ".$e->getMessage(), "log");
			return false;
		}
		
		return $protocol;
	}
	
	function getFontByID($id){
		$sql = $this->_font->getSelectFontByIDSql($id);
		
		$result = $this->_memcached->getSearchResult($sql);
		if($result){
			return $result;
		}
		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("FontDb::getFontByID():executeQuery() sql: ".$sql, "log");
			return false;
		}
		
		global $g_arr_host_config;
		foreach ($rows as $row){
			$url = $g_arr_host_config['cdnhost'].$row['url'];
		}
		$result = $this->_memcached->setSearchResult($sql, $url);
		if(!$result){
			Log::write("FontDb::getFontByID():setSearchResult() failed", "log");
		}
		return $url;
	}
}