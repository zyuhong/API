<?php

require_once 'tasks/protocol/ThemesProtocol.php';
require_once 'tasks/protocol/ThemesWebProtocol.php';
require_once 'tasks/protocol/WallpaperProtocol.php';
require_once 'tasks/protocol/ThemesDetailsProtocol.php';
require_once 'tasks/protocol/BannerProtocol.php';
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'lib/mySql.lib.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'themes.class.php';
require_once 'ThemesSql.sql.php';
require_once 'Wallpaper.class.php';
require_once 'tasks/statis/Product.class.php';

class CoolXiuDb extends DBManager{
	public $width;
	public $height;
	public $kernel;
	public $vercode;
	public $start;
	public $req_num;
	private $_product;
	function __construct(){
		$this->width	= 0;
		$this->height	= 0;
		$this->kernel	= 1;
		$this->vercode  = 0;
		$this->start	= 0;
		$this->req_num	= 0;
		$this->_product = new Product();
		$this->connectMySqlCommit();
	}
	/**
	 * 上传文件信息录入 数据库
	 * 
	 * @param unknown_type $xiu_type	Coolpadxiu类型
	 * @param unknown_type $coolxiu		Coolxiu对象
	 * @param unknown_type $prevs		主题缩略图数组
	 */	
	function setCoolXiu2DB($xiu_type, $coolxiu, $prevs){
		try {
			if(!$coolxiu){
				Log::write("CoolXiuDb::coolxiu is null", "log");
				return false;
			}
			$this->beginTransaction();
			$result = $this->insertCoolxiu($coolxiu);
			if(!$result){
				Log::write("CoolXiuDb::insertCoolxiu() failed", "log");
				return false;
			}
			
			if ($xiu_type == COOLXIU_TYPE_THEMES){
				$result = $this->setPrev2DB($prevs);
				if(!$result){
					Log::write("CoolXiuDb::setCoolXiu2DB():setPrev2DB() failed", "log");
					$this->roolback();
					return false;
				}
			}
		}catch (Exception $e){
				Log::write("CoolXiuDb::setCoolXiu2DB() Exception, error:".$e->getMessage(), "log");
				$this->roolback();
				return false;
		}
	
		$this->endTransaction();
		return true;
	}
	
	function setPrev2DB($prevs){
		foreach ($prevs as $prev){
			$result = $this->insertCoolxiu($prev);
			if(!$result){
				Log::write("CoolXiuDb::setPrev2DB(".$prevs->s_name.") failed", "log");
				return false;
			}
		}
		return true;
	}
	
	function insertCoolxiu($coolxiu){
		if(!$coolxiu){
			Log::write("CoolXiuDb::insertCoolxiu() coolxiu is null", "log");
			return false;
		}
		
		$sql = $coolxiu->getInsertSql();		
		if($sql == ""){
			Log::write("CoolXiuDb::insertCoolxiu()sql is empty error", "log");
			return false;
		}
	
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("CoolXiuDb::insertCoolxiu():executeSql() sql:".$sql." failed", "log");
			return  false;
		}
		return true;
	}
	
	function getFaultResult($error_no){
		$count = $error_no;
		$rsp_num = 0;
		$json_rsp =  array(
				'total_number'=>$count,
				'rst_number'=>$rsp_num);
	
		return json_encode($json_rsp);
	}
	/**
	 * 根据类型获取coolxiu对象,可以想办法些个工厂类
	 * 
	 * @param unknown_type $xiu_type
	 * @return Wallpaper
	 */
	function getCoolXiu($xiu_type, &$type_key){
		switch($xiu_type){
			case COOLXIU_TYPE_THEMES:
				{
					$coolxiu = new Themes();
					$type_key = "themes";
				}break;
			case COOLXIU_TYPE_PREV:
				{
					$coolxiu = new Preview();
					$type_key = "themes";
				}break;
			case COOLXIU_TYPE_WALLPAPER:
				{
					$coolxiu = new Wallpaper();
					$type_key = "wallpapers";
				}break;
		}		
		return $coolxiu;
	}
	
	/**
	 * 返回主题总数量
	*/
	private function getCoolXiuCount($coolxiu){
		$sql = $coolxiu->getCountSql();
		$count = $this->executeScan($sql);
		if($count === false ){
			Log::write("CoolXiuDb::getCoolXiuCount():executeScan()".$sql." error", "log");
			return false;
		}
		
		return $count;
	}
	/**
	 * 作废
	 * 根据请求的数量和开始项获取CoolXiu
	 * 返回Coolxiu列表
	 * @param unknown_type $xiu_type
	 * @param unknown_type $page
	 * @param unknown_type $req_num
	 */
	function getReqCoolXiuList($xiu_type, $page, $req_num){
		try {
			$type_key = "themes";
			$coolxiu = $this->getCoolXiu($xiu_type, $type_key);
				
			$start = $page * $req_num;
			$arr_coolxius = $this->getCoolXiuByLimit($xiu_type, $coolxiu, $start, $req_num);
			if(!$arr_coolxius){
				Log::write("CoolXiuDb::getReqCoolXiuList():getCoolXiuByLimit() failed", "log");
				$count = -3;
				return $this->getFaultResult($count);
			}
			
			$result = $this->getCoolXiuCount($coolxiu);
			if(!$result){
				Log::write("CoolXiuDb::getReqCoolXiuList():getCoolXiuCount() failed", "log");
				$count = -4;
				return $this->getFaultResult($count);
			}
			
			$count = (int)$result;
			$rsp_num = $this->getReqCoolXiuCount();
			$json_rsp =  array('total_number'=>$count,
					'ret_number'=>$rsp_num,
					$type_key=>$arr_coolxius);
		}catch(Exception $e){
			Log::write("CoolXiuDb::getReqCoolXiuList(): excepton error:".$e->getMessage(), "log");
			$count = -1;
			return $this->getFaultResult($count);
		}
		return json_encode($json_rsp);
	}

	function getCoolXiuByLimit($xiu_type, $coolxiu, $start, $req_num){
		try{
			
			$sql = $coolxiu->getSelectLimitSql($start, $req_num);
			
			$coolxius = $this->_getProtocol($sql, $xiu_type);
				
		}catch(Exception $e){
			Log::write("CoolXiuDb::getCoolXiuByLimit()exception, error:".$e->getMessage(), "log");
			return false;
		}
		return $coolxius;
	}
	/**
	 * 设置搜索条件
	 * @param unknown_type $width   分辨率宽度
	 * @param unknown_type $height  分辨率高度
	 * @param unknown_type $start	起始位置
	 * @param unknown_type $req_num 请求量
	 */
	function setSearchCondition($width, $height, $kernel = 1, $start = 0, $req_num = 0, $vercode = 0){
		$this->width	= $width * 2;
		$this->height	= $height;
		$this->kernel	= $kernel;
		$this->start	= $start;
		$this->req_num	= $req_num;
		$this->vercode  = $vercode;
	}
	
	public function setProduct($product, $imei, $meid, $imsi){
		$this->_product->setProductParam($product, $imei, $meid, $imsi);
	}
	
	function  transRatio($height, $width){
		$this->width	= $width;
		$this->height	= $height;
	}
	/**
	 * 获取按条件搜索的结果数量
	 * @param unknown_type $xiu_type
	 * @param unknown_type $search_type
	 * @param unknown_type $coolxiu
	 * @return boolean
	 */
	private function getSeachCoolXiuCount($coolxiu){
		$sql = $coolxiu->getSearchCoolXiuCountSql();
		if(!$sql){
			Log::write("CoolXiuDb::getSeachCoolXiuCount():getSearchCoolXiuCountSql() failed, sql is empty", "log");
			return false;
		}
		$count = $this->executeScan($sql);
		if($count === false){
			Log::write("CoolXiuDb::getSeachCoolXiuCount():executeScan() SQL:".$sql." error", "log");
			return false;
		}
		return $count;
	}
	
	private function _getSeachCoolXiuCountForWeb($coolxiu, $sorttype){
		if($sorttype == COOLXIU_SEARCH_HOLIDAY){
			$sql = $coolxiu->getSearchCoolXiuHolidayCountSql();
		}else{
			$sql = $coolxiu->getSearchCoolXiuForWebCountSql();
		}
		if(!$sql){
			Log::write("CoolXiuDb::getSeachCoolXiuForWebCount():getSearchCoolXiuForWebCountSql() failed, sql is empty", "log");
			return false;
		}
		$count = $this->executeScan($sql);
		if($count === false){
			Log::write("CoolXiuDb::getSeachCoolXiuForWebCount():executeScan() SQL:".$sql." error", "log");
			return false;
		}
		return $count;
	}
	
	/**
	 * 搜索Coolshow主题、壁纸
	 * @param unknown_type $xiu_type
	 * @param unknown_type $search_type
	 */
	function searchCoolXiuList($xiu_type, $search_type){
		try {			
			$type_key 	= "themes";			
			$coolxiu 	= $this->getCoolXiu($xiu_type, $type_key);
			$coolxiu->setSearchParam($search_type,
									 $this->kernel,
									 $this->width, $this->height, 
									 $this->start, $this->req_num,
									 $this->vercode);
			$sql 		= $coolxiu->getCoolXiuListSql();
			if(!$sql){
				Log::write("CoolXiuDb::searchCoolXiuList():getCoolXiuListSql() failed Sql is empty", "log");
				$result = get_rsp_result(false, 'get protocol sql empty');
				return $result;
			}
			
			$memcached = new MemDb();
			$result = $memcached->getSearchResult($sql);
			if($result){
// 				Log::write("CoolXiuDb::searchCoolXiuList():getSearchResult()".$sql, "log");
				return json_encode($result);
			}

			$arr_coolxius = $this->_getProtocol($sql, $xiu_type);
			if(!$arr_coolxius){
				Log::write("CoolXiuDb::searchCoolXiuList():_getProtocol() failed", "log");
				$result = get_rsp_result(false, 'get protocol failed');
				return  $result;
			}
			
			$rsp_num = count($arr_coolxius);			
			$result = $this->getSeachCoolXiuCount($coolxiu);
			if($result === false){
				Log::write("CoolXiuDb::searchCoolXiuList():getSeachCoolXiuCount() failed", "log");
				$count = -4; //获取总数错误
				return $this->getFaultResult($count);
			}
				
			$count = (int)$result;
			$json_rsp =  array(
					'total_number'=>$count,
					'ret_number'=>$rsp_num,
					$type_key=>$arr_coolxius
					);
			
			$result = $memcached->setSearchResult($sql, $json_rsp, 12*60*60);
			if(!$result){
				Log::write("CoolXiuDb::setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("CoolXiuDb::getReqCoolXiuList(): excepton error:".$e->getMessage(), "log");
			$count = -1; //搜索过程异常
			return $this->getFaultResult($count);
		}
		
		return json_encode($json_rsp);
	}
	
	public function searchCoolXiuListForWeb($xiu_type, $search_type, $sorttype, $channel = 0)
	{
		try {
			$type_key 	= "themes";
			$themes = new Themes();
			$themes->setSearchParam($search_type,
									$this->kernel,
									$this->width, $this->height,
									$this->start, $this->req_num);
			
			if($sorttype == COOLXIU_SEARCH_LAST){
				$sql  = $themes->getCoolXiuLastListSql();
			}else
			if($sorttype == COOLXIU_SEARCH_HOT){
				$sql  = $themes->getCoolXiuHotListSql();
			}else
			if($sorttype == COOLXIU_SEARCH_CHOICE){
				$sql  = $themes->getCoolXiuChoiceListSql();
			}else
			if($sorttype == COOLXIU_SEARCH_HOLIDAY){
				$sql  = $themes->getCoolXiuHolidayListSql();
			}else{
				$sql  = $themes->getCoolXiuListForWebSql();
			}
			if(!$sql){
				Log::write("CoolXiuDb::searchCoolXiuListForWeb():getCoolXiuListForWebSql() failed Sql is empty", "log");
				return false;
			}
				
			$memcached = new MemDb();
			$result = $memcached->getSearchResult($sql);
			if($result){
// 				Log::write("CoolXiuDb::searchCoolXiuListForWeb():getSearchResult()".$sql, "log");
				return json_encode($result);
			}
		
			$arr_coolxius = $this->_getCoolXiusListForWeb($sql);
			if(!$arr_coolxius){
				Log::write("CoolXiuDb::getCoolXiusListForWeb() failed", "log");
				$count = -3; //搜索结果为错误
				return $this->getFaultResult($count);
			}
				
			$rsp_num = count($arr_coolxius);
				
			$result = $this->_getSeachCoolXiuCountForWeb($themes, $sorttype);
			if($result === false){
				Log::write("CoolXiuDb::searchCoolXiuListForWeb():_getSeachCoolXiuCountForWeb() failed", "log");
				$count = -4; //获取总数错误
				return $this->getFaultResult($count);
			}
		
			$count = (int)$result;
			$json_rsp =  array(
					'total_number'=>$count,
					'ret_number'=>$rsp_num,
					$type_key=>$arr_coolxius
			);
				
			$result = $memcached->setSearchResult($sql, $json_rsp);
			if(!$result){
				Log::write("CoolXiuDb::searchCoolXiuListForWeb() failed", "log");
			}
		}catch(Exception $e){
			Log::write("CoolXiuDb::searchCoolXiuListForWeb(): excepton error:".$e->getMessage(), "log");
			$count = -1; //搜索过程异常
			return $this->getFaultResult($count);
		}
		
		return json_encode($json_rsp);
	}
	
	private function _getThemeBanner($rows, $channel)
	{
		$arrBanner = array();
		$coolxius = array();
		$strBannerId = '';
		foreach($rows as $row){
			if(!array_key_exists($row['bannerid'], $arrBanner)){
				$strBannerId = $row['bannerid'];
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
			if(!array_key_exists($row['identity'], $arrBanner[$strBannerId]->bannerRes)){
				$strThemeId = $row['identity'];
				$theme = new ThemesProtocol();
				$theme->setProtocol($row, $channel);
				$arrBanner[$strBannerId]->setBannerRes($strThemeId, $theme);
			}
		
			$prev = new PrevProtocol();
			if((int)$row['prev_type'] == 1){
				$arrBanner[$strBannerId]->bannerRes[$strThemeId]->setMainPrev($row);
			}
		
			$prev->setPrev($row);
			$arrBanner[$strBannerId]->bannerRes[$strThemeId]->pushPrevImg($prev);
		}
		$arr_coolxius = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol('themes');
			array_push($arr_coolxius, $temBanner);
		}
		
		return $arr_coolxius;
	}
	
	private function _getWallpaperBanner($rows, $channel)
	{
		$arrBanner = array();
		$coolxius = array();
		foreach($rows as $row){
			$strBannerId = $row['bannerid'];
			if(!array_key_exists($strBannerId, $arrBanner)){
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
			
			if(!array_key_exists($row['cpid'], $arrBanner[$strBannerId]->bannerRes)){
				$strWpId = $row['cpid'];
				$wp = new WallpaperProtocol();
				$wp->setWallpaper($row, $channel);
				$arrBanner[$strBannerId]->setBannerRes($strWpId, $wp);
			}
		}
		
		$arr_coolxius = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol('wallpapers');
			array_push($arr_coolxius, $temBanner);
		}
	
		return $arr_coolxius;
	}
	
	private function _getBannerProtocol($sql, $nCoolType, $channel = 0)
	{
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("CoolXiuDb::_getBannerProtocol():executeQuery() SQL:'.$sql.' failed", "log");
			return false;
		}
		if($nCoolType == COOLXIU_TYPE_THEMES)
		{
			$arr_coolxius = $this->_getThemeBanner($rows, $channel);
		}
		if($nCoolType == COOLXIU_TYPE_ANDROIDESK_WALLPAPER)
		{
			$arr_coolxius = $this->_getWallpaperBanner($rows, $channel);
		}
		
		return $arr_coolxius;
	}
	
	private function _getProtocol($sql, $xiu_type = 0, $channel = 0)
	{
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("CoolXiuDb::_getProtocol():executeQuery() SQL:'.$sql.' failed", "log");
			return false;
		}
		
		$arr_coolxius = array();
		switch ($xiu_type){
			case COOLXIU_TYPE_THEMES:
				{
					$coolxius = array();
					foreach($rows as $row){
						if(!array_key_exists($row['identity'], $coolxius)){
							$theme = new ThemesProtocol();
							$theme->setProtocol($row, $channel);
							$coolxius = $coolxius + array($row['identity'] => $theme);
						}
		
						$prev = new PrevProtocol();
		
						if((int)$row['prev_type'] == 1){
							$coolxius[$row['identity']]->setMainPrev($row);
						}
		
						$prev->setPrev($row);
						$coolxius[$row['identity']]->pushPrevImg($prev);
					}
					foreach ($coolxius as $key => $tem_coolxiu){
						array_push($arr_coolxius, $tem_coolxiu);
					}
				}
				break;
			case COOLXIU_TYPE_WALLPAPER:
				{
					foreach($rows as $row){
						$wallpaper = new WallpaperProtocol();
						$wallpaper->setProduct($this->_product);
						$wallpaper->setWallpaper($row);
						array_push($arr_coolxius, $wallpaper);
					}
				}
				break;
		}
		
		return $arr_coolxius;
	}
	/**
	 * 根据ID获取资源的协议
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, unknown>|multitype:
	 */

	public function getRsc($id, $channel = 0)
	{
		try {
			$sql  = Themes::getCoolXiuWithIdSql($id);
			if(!$sql){
				Log::write("CoolXiuDb::getSrc():getCoolXiuWithIdSql() failed Sql is empty", "log");
				return false;
			}
	
			$memcached = new MemDb();
			$result = $memcached->getSearchResult($sql);
			if($result){
				Log::write("CoolXiuDb::getSrc():getSearchResult()".$sql, "log");
				return $result;
			}
	
			$protocol = $this->_getProtocol($sql, COOLXIU_TYPE_THEMES, $channel);
			if(!$protocol){
				Log::write("CoolXiuDb::getSrc():_getProtocol() failed", "log");
				return  false;
			}
			$result = $memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("CoolXiuDb::getSrc() failed", "log");
			}
		}catch(Exception $e){
			Log::write("CoolXiuDb::getSrc(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	
		return $protocol;
	}
	
	public function getBanner($nCoolType, $nWidth, $nHeight, 
							  $nKernelCode = 3, $nVersionCode = 0, $channel = 0)
	{
		try {
			if($nCoolType == COOLXIU_TYPE_THEMES)
			{
				$sql  = Themes::getSelectBannerSql($nWidth * 2, $nHeight, $nKernelCode);
			}
			if($nCoolType == COOLXIU_TYPE_ANDROIDESK_WALLPAPER)
			{
				Wallpaper::getSelectBannerSql($nWidth * 2, $nHeight);
			}
			if(!$sql){
				Log::write("CoolXiuDb::getBanner():getSelectBannerSql() failed Sql is empty", "log");
				return false;
			}
			Log::write("CoolXiuDb::getBanner():getSearchResult()".$sql, "log");
// 			$memcached = new MemDb();
// 			$result = $memcached->getSearchResult($sql);
// 			if($result){
// 				Log::write("CoolXiuDb::getBanner():getSearchResult()".$sql, "log");
// 				return $result;
// 			}
	
			$protocol = $this->_getBannerProtocol($sql, $nCoolType, $channel);
			if($protocol === false){
				Log::write("CoolXiuDb::getBanner():_getBannerProtocol() failed", "log");
				return  false;
			}
// 			$result = $memcached->setSearchResult($sql, $protocol);
// 			if(!$result){
// 				Log::write("CoolXiuDb::getBanner() failed", "log");
// 			}
		}catch(Exception $e){
			Log::write("CoolXiuDb::getBanner(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	
		return $protocol;
	}
	
	/**
	 * 酷秀专辑
	 * @param unknown_type $id
	 * @return boolean|Ambigous <boolean, unknown>|Ambigous <NULL, ThemesProtocol>
	 */
	public function getAlbum($nXiuType = 0, $channel = 0)
	{
		try {
			$strTypeKey 	= "themes";
			$coolxiu 	= $this->getCoolXiu($nXiuType, $strTypeKey);
			$coolxiu->setSearchParam(0,
									 $this->kernel,
									 $this->width, $this->height);
			$sql  = $coolxiu->getSelectAlbumsSql();
			if(!$sql){
				Log::write("CoolXiuDb::getAlbum():getSelectAlbumsSql() failed Sql is empty", "log");
				return false;
			}
// 			Log::write('CoolXiuDb::getAlbum():getSelectAlbumsSql() Sql :'.$sql, "log");
			$memcached = new MemDb();
			$result = $memcached->getSearchResult($sql);
			if($result){
				Log::write("CoolXiuDb::getAlbum():getSearchResult()".$sql, "log");
				return $result;
			}
	
			$protocol = $this->_getProtocol($sql, $nXiuType, $channel);
			if(!$protocol){
				Log::write("CoolXiuDb::getAlbum():_getProtocol() failed", "log");
				return  false;
			}
			
			$result = $memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("CoolXiuDb::getAlbum() failed", "log");
			}
		}catch(Exception $e){
			Log::write("CoolXiuDb::getAlbum(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	
		return $protocol;
	}
	
	private function _getCoolXiusDetail($sql, $channel = 1)
	{
		try{
			$rows = $this->executeQuery($sql);
			if($rows === false){
				Log::write("CoolXiuDb::_getCoolXiusDetail():executeQuery()".$sql." error", "log");
				return false;
			}
			
			$count = $this->getQueryCount();
			if ($count == 0){
				return true;
			}
			
			$theme = null;
			$arrPrev = array();
			foreach($rows as $row){
				if(!$theme){
					$theme = new ThemesDetailsProtocol();
					$theme->setProtocol($row, $channel);
				}
						
				$prev = new PrevProtocol();
				$prev->setPrev($row);
				
				if((int)$row['prev_type'] == 1){
					$strMainUrl = $prev->getMainPrev($row); 
				}
				array_push($arrPrev, $prev);
			}
			$nImgNum = count($arrPrev);
			$theme->setPrevImgs($nImgNum, $strMainUrl, $arrPrev);
			return $theme;
		}catch (Exception $e){
			Log::write("CoolXiuDb::_getCoolXiusDetail() exception ".$e->getMessage(), "log");
			return false;
		}
		
		return false;
	}
	
	private function _getCoolXiusListForWeb($sql){
	
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("CoolXiuDb::getCoolXiusListForWeb():executeQuery()".$sql." error", "log");
			return false;
		}
	
		$count = $this->getQueryCount();
		if ($count == 0){
			return true;
		}
		$arr_coolxius = array();
		foreach($rows as $row){
			$theme = new ThemesWebProtocol();
			$theme->setProtocol($row);
			array_push($arr_coolxius, $theme);
		}	
		return $arr_coolxius;
	}
	
	/**
	 *
	 * 根据主题文件夹获取缩略图
	 * @param unknown_type $theme_folder 主题文件夹
	 * @param unknown_type $img_num		 缩略图数量 引用
	 * @param unknown_type $main_img	主缩略图 引用
	 * @param unknown_type $prev_imgs	缩略图数组 引用
	 * @return boolean
	 */
	function getPrevsByTheme($theme_folder, &$img_num, &$main_img, &$prev_imgs){
	
		$sql = sprintf(SQL_SELECT_THEME_PREV_INFO, $theme_folder);
	
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("CoolXiuDb::getPrevsByTheme():executeQuery() ".$sql." error", "log");
			return false;
		}
			
		if(count($rows) <= 0){
			return true;
		}
		
		foreach($rows as $row){
			$prev = new PrevProtocol();
			$prev->setPrev($row);
			++$img_num;
			if($main_img == ""){
				$main_img = $prev->img_url;
			}
			array_push($prev_imgs, $prev);
		}
		return true;
	}
	
	public function getCoolXiuDetails($strId, $strProduct, $nWidth = 0, $nHeight = 0, $channel = 1)
	{
		try {
			$type_key 	= "themes";
			$coolxiu 	= new Themes();
			$sql 		= $coolxiu->getCoolXiuDetailsSql($strId, $nWidth, $nHeight);
			if(!$sql){
				Log::write("CoolXiuDb::getCoolXiuDetails():getCoolXiuDetailsSql() failed Sql is empty", "log");
				return false;
			}
				
			$memcached = new MemDb();
			$result = $memcached->getSearchResult($sql);
			if($result){
//				Log::write("CoolXiuDb::getCoolXiuDetails():getSearchResult()".$sql, "log");
				return json_encode($result);
			}
		
			$details = $this->_getCoolXiusDetail($sql, $channel);
			if(!$details){
				Log::write("CoolXiuDb::getCoolXiuDetails():_getCoolXiusDetail() failed", "log");
				$count = -3; //搜索结果为错误
				return $this->getFaultResult($count);
			}
			
			$json_rsp =  array(
					'result'  => true,
					'product' => $strProduct,
					'details' =>$details,
					);
				
			$result = $memcached->setSearchResult($sql, $json_rsp);
			if(!$result){
				Log::write("CoolXiuDb::getCoolXiuDetails():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("CoolXiuDb::getCoolXiuDetails(): excepton error:".$e->getMessage(), "log");
			$count = -1; //搜索过程异常
			return $this->getFaultResult($count);
		}
		
		return json_encode($json_rsp);
	}
}