<?php
require_once 'configs/config.php';
require_once 'lib/MemDb.lib.php';
require_once 'public/public.php';
require_once 'tasks/CoolShow/Wallpaper.class.php';
require_once 'tasks/CoolShow/CoolShowDb.class.php';
require_once 'tasks/CoolShow/CoolShowFactory.class.php';
require_once 'tasks/CoolShow/Album.class.php';
require_once 'tasks/statis/Product.class.php';
require_once 'tasks/Records/ScoreRecord.class.php';
require_once 'tasks/protocol/ScoreProtocol.php';
require_once 'tasks/CoolShow/HotWord.class.php';

class CoolShowSearch
{
	private $_memcached;
	private $_db;
	private $_rdb;
	private $_channel;
	private $_dbconfig;

	public function __construct($dbConfig=array())
	{
		$this->_memcached = new MemDb();
		global $g_arr_memcache_config;
		$this->_memcached->connectMemcached($g_arr_memcache_config);
		$this->_db 		  = null;//new CoolShowDb($dbConfig);
		$this->_rdb		  = null;
		$this->_dbconfig  = $dbConfig;
	}
	
	private function _getDb()
	{
		if(!$this->_db){
			$this->_db 		  = new CoolShowDb($this->_dbconfig);
		}
		return $this->_db;
	}
	
	private function _getRecommendDb()
	{
		if(!$this->_rdb){
			global $g_arr_db_config;
			$this->_rdb 	= new CoolShowDb($g_arr_db_config['recommend']);
		}
		return $this->_rdb;
	}
	
	private function _setCoolShowParam(CoolShow $coolshow)
	{
		$strProduct= (int)(isset($_GET['product']))?$_GET['product']:'';
		$nKernel   = (int)(isset($_GET['kernelCode']))?$_GET['kernelCode']:1;#默认值不可变，防止最初版本（未增加此字段的版本）异常
		$nWidth    = (int)(isset($_GET['width']))?$_GET['width']:540;
		$nHeight   = (int)(isset($_GET['height']))?$_GET['height']:960;
		$nType 	   = (int)(isset($_GET['reqType'])?$_GET['reqType']:0);
		$nType 	   = (int)(isset($_GET['type'])?$_GET['type']:$nType);
		$nSubType  = (int)(isset($_GET['subtype'])?$_GET['subtype']:0);
		$nChannel  = (int)(isset($_GET['chanel'])?$_GET['chanel']:0);
		$nSort     = (int)(isset($_GET['sort'])?$_GET['sort']:0);			#运营模式（排序模式）2015.4.28
		$nVercode  = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);#默认值不可变，防止最初版本（未增加此字段的版本）异常
		$nCharge   = (int)(isset($_GET['charge'])?$_GET['charge']:2); #0:免费 1：付费  2：全部  20150504

		$strProduct= '';
		$nProtocolCode = 0;
		$json_param = isset($_POST['statis'])?$_POST['statis']:'';
		if(!empty($json_param)){
			$json_param = stripslashes($json_param);
			$arr_param = json_decode($json_param, true);
	
			$strProduct = isset($arr_param['product'])?$arr_param['product']:'';
			$nProtocolCode = (int)(isset($arr_param['protocolCode'])?$arr_param['protocolCode']:0);#新增的版本判断依据
		}
		$nProtocolCode  = (int)(isset($_GET['protocolCode'])?$_GET['protocolCode']:$nProtocolCode);#新版本在主GET参数中追加20150522
		
		$product = new Product();
		$product->setProduct($strProduct);
		$coolshow->setProduct($product);

		$coolshow->setParam($strProduct, $nType, $nSubType,
							$nKernel, $nVercode,
							$nWidth * 2, $nHeight, $nChannel, false, 
						    $nSort,
							$nProtocolCode,
							$nCharge);
	}
	
	
	private function _getProtocol(CoolShow $coolshow, $strSql)
	{
		$rows = $this->_getDb()->getCoolShow($strSql);
		if($rows === false){
			Log::write('CoolShowSearch::_getProtocol():getCoolShow() failed, SQL:'.$strSql, 'log');
			return false;
		}
			
		$arrProtocol = $coolshow->getProtocol($rows);
		if($arrProtocol === false){
			Log::write('CoolShowSearch::_getProtocol():getProtocol() failed', 'log');
			return false;
		}
		
		return $arrProtocol;
	}
	
	private  function _getProtocolMix(CoolShow $coolshow, $start, $limit)
	{
		if(!$coolshow->bHavePaid || $limit == 0){
			return false;
		}
		$coolshow->setPayRatio();
		$coolshow->setPay(true);
		$nPayStart = $coolshow->getStart(true, $start);
		$nPayLimit = $coolshow->getLimit(true, $limit);
		$strSql = $coolshow->getCoolShowListSql( $nPayStart, $nPayLimit);
		$arrPay = $this->_getProtocol($coolshow, $strSql);
		$nPayFix = $nPayLimit- count($arrPay);
		$nPayCount = $this->_getCoolShowCount($coolshow);
		
		$coolshow->setPay(false);
		$nFreeStart = $coolshow->getStart(false, $start);
		$nFreeLimit = $coolshow->getLimit(false, $limit);
		$strSql = $coolshow->getCoolShowListSql($nFreeStart, $nFreeLimit);
		$arrFree = $this->_getProtocol($coolshow, $strSql);
		$nFreeFix = $nFreeLimit- count($arrFree);
		$nFreeCount = $this->_getCoolShowCount($coolshow);
		
		if($nPayFix > 0){
			$coolshow->setPay(false);
			if($nPayFix == $nPayLimit){
				$nFreeStart = $start - $nPayCount;
			}
			$nFreeLimit += $nPayFix;
			$strSql = $coolshow->getCoolShowListSql($nFreeStart, $nFreeLimit);
			$arrFree = $this->_getProtocol($coolshow, $strSql);
		}
		
		if($nFreeFix > 0){
			$coolshow->setPay(true);
			$nPayLimit += $nFreeFix;
			if($nFreeFix == $nFreeLimit){
				$nPayStart = $start - $nFreeCount;
			}
			$strSql = $coolshow->getCoolShowListSql( $nPayStart, $nPayLimit);
			$arrPay = $this->_getProtocol($coolshow, $strSql);
		}
		
		$arrProtocol = $this->_mixProtocol($coolshow, $arrPay, $arrFree);
		
		return $arrProtocol;
	}

	private function _mixProtocol(CoolShow $coolshow, $arrPay, $arrFree)
	{
		if ($arrPay === false || count($arrPay) <= 0) return $arrFree;
		if ($arrFree === false || count($arrFree) <= 0) return $arrPay;
		$nPay = count($arrPay);
		$nFree = count($arrFree);
		$nCount = $nPay + $nFree;
		
		$arrMix = array();
		$p = 0; $f = 0;$s = 1;$i = 1;
		if($coolshow->nFree == 1){
			$i = 0;
			$s = 0;
		}
		for (; $i < ($nCount + 1);){
			if($p >= $nPay && $f >= $nFree){
				break;
			}
			
			if($p >= $nPay){
				array_push($arrMix, $arrFree[$f]);
				++$f;
				++$i;
				continue;
			}
			if($f >= $nFree){
				array_push($arrMix, $arrPay[$p]);
				++$p;
				++$i;
				continue;
			}
			
			if($s == 0){
				if ($i % ($coolshow->nPay + $coolshow->nFree) == 0){
					array_push($arrMix, $arrFree[$f]);
					++$f;
				}else{
					array_push($arrMix, $arrPay[$p]);
					++$p;
				}
			}else{			
				if ($i % ($coolshow->nPay + $coolshow->nFree) == 0){
					array_push($arrMix, $arrPay[$p]);
					++$p;
				}else{
					array_push($arrMix, $arrFree[$f]);
					++$f;
				}
			}
			++$i;
			
		}		
		return $arrMix;
	}
	
	private function _getCoolShowCount(CoolShow $coolshow)
	{
		$strSql = $coolshow->getCoolShowCountSql();
		$result = $this->_memcached->getSearchResult($strSql);
		if($result){
			return $result;
		}

		$count = $this->_getDb()->getCoolShowCount($strSql);
		if($count === false){
			Log::write('CoolShowSearch::_getCoolShowCount():getCoolShowCount() failed, SQL:'.$strSql, 'log');
			return false;
		}
		$this->_memcached->setSearchResult($strSql, $count, 12*60*60);
		return $count;
	}
	
	private function _setChannel($coolshow, $nCoolType)
	{
		if ($coolshow->_nSort == COOLXIU_SEARCH_CHOICE){
			$coolshow->setChannel(REQUEST_CHANNEL_CHOICE);
		}
		if ($coolshow->nCharge == 0){
			$coolshow->setChannel(REQUEST_CHANNEL_CHARGE_NO);
		}
		if ($coolshow->nCharge == 1){
			$coolshow->setChannel(REQUEST_CHANNEL_CHARGE_YES);
		}
		
		if($nCoolType == COOLXIU_TYPE_THEMES_CONTACT){
			$coolshow->setChannel(REQUEST_CHANNEL_CONTACT);
		}
		if($nCoolType == COOLXIU_TYPE_THEMES_ICON){
			$coolshow->setChannel(REQUEST_CHANNEL_ICON);
		}
		if($nCoolType == COOLXIU_TYPE_LIVE_WALLPAPER){
			$coolshow->setChannel(REQUEST_CHANNEL_LIVEWP);
		}
	}	
	
	public function getCoolShow($nCoolType, $start = 0, $limit = 10)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			if(!$coolshow){
				Log::write('CoolShowSearch::getCoolShow() coolshow  is null', 'log');
				$result = get_rsp_result(false, 'get coolshow is null');
				return $result;
			}
			
			$this->_setCoolShowParam($coolshow);
			
			$this->_setChannel($coolshow, $nCoolType);
			
			$strSql = $coolshow->getCoolShowListSql($start, $limit);
			if(!$strSql){
				Log::write('CoolShowSearch::getCoolShow():getCoolShowListSql() failed, Sql is empty', 'log');
				$result = get_rsp_result(false, 'get protocol sql empty');
				return $result;
			}
			
//  	  	Log::write('CoolShowSearch::getCoolShow():getCoolShowListSql(), SQL:'.$strSql, 'debug');
			$result = $this->_memcached->getSearchResult($strSql.$coolshow->nCharge);
			if($result){
// 				Log::write('CoolXiuDb::searchCoolXiuList():getSearchResult()'.$strSql, 'log');
				return json_encode($result);
			}
			
			$count = $this->_getCoolShowCount($coolshow);
			
			if(!$coolshow->bHavePaid || 
				($nCoolType == COOLXIU_TYPE_THEMES 
				    && $coolshow->_nProtocolCode >= 3 
					&& ($coolshow->_nSort == COOLXIU_SEARCH_COMMEN 
						|| $coolshow->_nSort == COOLXIU_SEARCH_CHOICE )) || 
				$coolshow->nCharge != 2){
				$arrProtocol = $this->_getProtocol($coolshow, $strSql);
			}else{
				$arrProtocol = $this->_getProtocolMix($coolshow, $start, $limit);
			}
			if(!$arrProtocol){
 				Log::write('CoolShowSearch::searchCoolXiuList():_getProtocol() failed', 'log');
				$result = get_rsp_result(false, 'get protocol failed');
				return $result;
			}
			
			
			$result =  array(
					'total_number'=> $count,
					'ret_number'  => count($arrProtocol),
					$coolshow->strType     => $arrProtocol
			);
			
 			$this->_memcached->setSearchResult($strSql.$coolshow->nCharge, $result, 60*60);
			
		}catch(Exception $e){
			Log::write('CoolShowSearch::getCoolShow() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get coolshow exception');
			return $result;
		}
		
		return json_encode($result);
	}
	
	public function getBanner($nCoolType)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel(REQUEST_CHANNEL_BANNER);
			
			$strSql = $coolshow->getSelectBannerSql();
			if(!$strSql){
				Log::write("CoolShowSearch::getBanner():getSelectBannerSql() failed Sql is empty", "log");
				return false;
			}
			
//  		Log::write('CoolShowSearch::getBanner():getSearchResult() SQL:'.$strSql, 'debug');
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolShowSearch::getBanner():getSearchResult()'.$strSql, 'log');
				return $result;
			}
			
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getBanner():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
				
			$protocol = $coolshow->getBannerProtocol($rows);
			if($protocol === false){
				Log::write("CoolShowSearch::getBanner():getBannerProtocol() failed", "log");
				return  false;
			}
			
			$result = array('result'=>true,
							'banners'=>$protocol);
			$json_result = json_encode($result);
			$this->_memcached->setSearchResult($strSql, $json_result, 24*60*60);
			
		}catch(Exception $e){
			Log::write("CoolShowSearch::getBanner(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	
		return $json_result;
	}
	/**
	 * 
	 * COOLUI6.0完全改变了banner的获取方式，先获取banner图，点击时通过albums获取具体资源列表，等同于专辑
	 * @param unknown_type $nCoolType
	 * @return string|Ambigous <boolean, unknown>|multitype:
	 */
	public function getBannerList($nCoolType, $bAlbum = 0)
	{
		try {
			$coolshow = new Album();			
			$bSceneWallpaper = false;
			$strAlbum = 'albums'.$nCoolType;
			if($nCoolType == COOLXIU_TYPE_SCENE_WALLPAPER){
				$nCoolType = COOLXIU_TYPE_ANDROIDESK_WALLPAPER;
				$bSceneWallpaper = true;
				$coolshow->setSceneWallpaper(true);
			}
			
			$strSql = $coolshow->getSelectBannerListSql($nCoolType, $bAlbum);
			if(!$strSql){
				Log::write("CoolShowSearch::getBannerList():getSelectBannerListSql() failed Sql is empty", "log");
				$result = get_rsp_result(false, 'get bannerlist sql failed');
				return $result;
			}

			$result = $this->_memcached->getSearchResult($strSql.$strAlbum);
			if($result){
// 				Log::write('CoolShowSearch::getBanner():getSearchResult()'.$strSql, 'log');
				return $result;
			}
			
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getBannerList():getCoolShow() failed, SQL:'.$strSql, 'log');
				$result = get_rsp_result(false, 'get bannerlist failed');
				return $result;
			}
			if($nCoolType == COOLXIU_TYPE_ANDROIDESK_WALLPAPER){
				$nCoolType = COOLXIU_TYPE_WALLPAPER;
			}	
			$protocol = $coolshow->getProtocol($rows, $nCoolType);
			if($protocol === false){
				Log::write("CoolShowSearch::getBannerList():getBannerProtocol() failed", "log");
				$result = get_rsp_result(false, 'get bannerlist protocol failed');
				return $result;
			}
			
			if(($nCoolType == COOLXIU_TYPE_ANDROIDESK_WALLPAPER 
				|| $nCoolType == COOLXIU_TYPE_WALLPAPER)
				&& $bAlbum == 0){
				//合并安卓壁纸资源
				$arrAndroidBanner = $this->_getAndroideskBanner($bSceneWallpaper);
				$arrTemp = array_merge($protocol['top'], $arrAndroidBanner);
				$protocol['top'] = $arrTemp;
			}			
			
			$result = array('result'=>true,
							'banners'=>$protocol);
			$json_result = json_encode($result);
			$this->_memcached->setSearchResult($strSql.$strAlbum, $json_result, 24*60*60);
			
		}catch(Exception $e){
			Log::write("CoolShowSearch::getBannerList(): excepton error:".$e->getMessage(), "log");
			$result = get_rsp_result(false, 'get bannerlist exception');
			return $result;
		}
	
		return $json_result;
	}
	
	private function _getAndroideskBanner($bSceneWallpaper = false)
	{
		try {
			$coolshow = new Album();
			$coolshow->setSceneWallpaper($bSceneWallpaper);
			$strSql = $coolshow->getSelectAndroideskBannerListSql();
			if(!$strSql){
				Log::write("CoolShowSearch::getAndroideskBanner():getSelectAndroideskBannerListSql() failed Sql is empty", "log");
				$result = get_rsp_result(false, 'get bannerlist sql failed');
				return $result;
			}
			
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolShowSearch::getAndroideskBanner():getSearchResult()'.$strSql, 'log');
				return $result;
			}
			
			global $g_arr_db_config;
			$db 		  = new CoolShowDb($g_arr_db_config['androidesk']);
			
			$rows = $db->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getAndroideskBanner():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
			
			$arrProtocol = $coolshow->getBannerProtocol($rows, COOLXIU_TYPE_ANDROIDESK_WALLPAPER);
			if($arrProtocol === false){
				Log::write("CoolShowSearch::getAndroideskBanner():getBannerProtocol() failed", "log");
				return false;
			}
			
			return $arrProtocol;
		}catch(Exception $e){
			Log::write("CoolShowSearch::getAndroideskBanner(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	}
	
	public function getWidgetBanner($nCoolType)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			$this->_setCoolShowParam($coolshow);
			$coolshow->setWidgetBanner(true);
			$coolshow->setChannel(REQUEST_CHANNEL_WIDGET_BANNER);
				
			$strSql = $coolshow->getSelectBannerSql();
			if(!$strSql){
				Log::write("CoolShowSearch::getWidgetBanner():getSelectBannerSql() failed Sql is empty", "log");
				return false;
			}
				
//  		Log::write('CoolShowSearch::getBanner():getSearchResult() SQL:'.$strSql, 'debug');
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolShowSearch::getBanner():getSearchResult()'.$strSql, 'log');
				return $result;
			}
		
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getWidgetBanner():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
		
			$protocol = $coolshow->getProtocol($rows);
			if($protocol === false){
				Log::write("CoolShowSearch::getWidgetBanner():getProtocol() failed", "log");
				return  false;
			}
				
			$result = array('result'=>true,
							$coolshow->strType=>$protocol);
			$json_result = json_encode($result);
			$this->_memcached->setSearchResult($strSql, $json_result, 24*60*60);
				
		}catch(Exception $e){
			Log::write("CoolShowSearch::getWidgetBanner(): excepton error:".$e->getMessage(), "log");
			return false;
		}
		
		return $json_result;
	}
	
	
	public function getAlbums($nCoolType, $strId, $nChannel = 5, $nStart = 0, $nNum = 100)
	{
		try {
			$memKey = 'banner'.$nCoolType.$strId.$nStart.$nNum;
			
			$result = $this->_memcached->getSearchResult($memKey);
			if($result){
// 				Log::write("CoolShowSearch::getAlbum():getSearchResult()".$strSql, "debug");
				return $result;
			}
			
			$arrProtocol = $this->_getAlbums($nCoolType, $strId, $nChannel, $nStart, $nNum);
			if($arrProtocol === false){
				Log::write('CoolShowSearch::getAlbums():getProtocol() failed', 'log');
				$result = get_rsp_result(false, 'get albums protocol failed');
				return $result;
			}
			
			$result = array('result'=>true,
							'albums'=>$arrProtocol);
			$json_result = json_encode($result);
			
			$this->_memcached->setSearchResult($memKey, $json_result, 24*60*60);
			
		}catch(Exception $e){
			Log::write("CoolShowSearch::getAlbum(): excepton error:".$e->getMessage(), "log");
			$result = get_rsp_result(false, 'get albums exception');
			return $result;
		}
	
		return $json_result;
	}
	
	private function _getAlbums($nCoolType, $strId, $nChannel = 5, $nStart = 0, $nNum = 100){
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel($nChannel);
			
			$strSql  = $coolshow->getSelectAlbumsSql($strId, $nStart, $nNum);
			if(!$strSql){
				Log::write("CoolShowSearch::_getAlbums():getSelectAlbumsSql() failed Sql is empty", "log");
				return false;
			}
			
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::_getAlbums():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
			
			$arrProtocol = array();
			$arrProtocol = $coolshow->getProtocol($rows);
			if($arrProtocol === false){
				Log::write('CoolShowSearch::_getAlbums():getProtocol() failed', 'log');
				return false;
			}
			
			return $arrProtocol;
		}catch(Exception $e){
			Log::write("CoolShowSearch::_getAlbums(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	}
	
	public function getNewBanner($nCoolType, $strId, $nChannel = 5)
	{
		try {
			$memKey = 'newbanner'.$strId;
			$result = $this->_memcached->getSearchResult($memKey);
			if($result){
// 				Log::write('CoolShowSearch::getNewBanner():getSearchResult()'.$memKey, 'debug');
				return $result;
			}
			
			$arrTheme = $this->_getAlbums(COOLXIU_TYPE_THEMES, $strId, $nChannel);
			if ($arrTheme === false){
				Log::write('CoolShowSearch::getNewBanner() get arrTheme false', 'log');
				$arrTheme = array();
			}
			
			$arrWallpaper = $this->_getAlbums(COOLXIU_TYPE_ANDROIDESK_WALLPAPER, $strId, $nChannel);
			if ($arrWallpaper === false){
				Log::write('CoolShowSearch::getNewBanner() get arrWallpaper false', 'log');
				$arrWallpaper = array();
			}
			
			$arrFont = $this->_getAlbums(COOLXIU_TYPE_FONT, $strId, $nChannel);
			if ($arrFont === false){
				Log::write('CoolShowSearch::getNewBanner() get arrFont false', 'log');
				$arrFont = array();
			}
			
			$arrScene = $this->_getAlbums(COOLXIU_TYPE_SCENE, $strId, $nChannel);
			if ($arrScene === false){
				Log::write('CoolShowSearch::getNewBanner() get arrScene false', 'log');
				$arrScene = array();
			}
			
			$arrRing = $this->_getAlbums(COOLXIU_TYPE_RING, $strId, $nChannel);
			if ($arrRing === false){
				Log::write('CoolShowSearch::getNewBanner() get arrRing false', 'log');
				$arrRing = array();
			}
			
			$arrLwp = array();//$this->_getAlbums(COOLXIU_TYPE_LIVE_WALLPAPER, $strId, $nChannel);
			if ($arrLwp === false){
				Log::write('CoolShowSearch::getNewBanner() get arrLwp false', 'log');
				$arrLwp = array();
			}
			
			$result = array('result'=>true,
							'theme'=>$arrTheme,
							'wallpaper'=>$arrTheme,
							'font'=>$arrFont,
							'scene'=>$arrScene,
							'ring'=>$arrRing,
							'lwallpaper'=>$arrLwp,);
			
			$json_result = json_encode($result);
				
			$this->_memcached->setSearchResult($memKey, $json_result, 24*60*60);
				
		}catch(Exception $e){
			Log::write("CoolShowSearch::getNewBanner(): excepton error:".$e->getMessage(), "log");
			$result = get_rsp_result(false, 'get banners exception');
			return $result;
		}
	
		return $json_result;
	}
	
	public function getRsc($nCoolType, $id)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel(REQUEST_CHANNEL_RSC);
			
			$strSql  = $coolshow->getSelectRscSql($id);
			if(!$strSql){
				Log::write("CoolShowSearch::getRsc():getSelectRscSql() failed Sql is empty", "log");
				return false;
			}
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write("CoolShowSearch::getRsc():getSearchResult()".$strSql, "log");
				return $result;
			}
		
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getRsc():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
		
			$arrProtocol = $coolshow->getProtocol($rows);
			if($arrProtocol === false){
				Log::write('CoolShowSearch::getRsc():getProtocol() failed', 'log');
				return false;
			}

			$result = array('key'=>$coolshow->strType,
						'result'=>$arrProtocol);
 			$bResult = $this->_memcached->setSearchResult($strSql, $result);
			if(!$bResult){
				Log::write("CoolShowSearch::getRsc() failed", "log");
			}
		}catch(Exception $e){
			Log::write("CoolShowSearch::getRsc(): excepton error:".$e->getMessage(), "log");
			return false;
		}
		return $result;
	}
	
	public function getUrl($nCoolType, $id, $nChannel = 0)
	{
		$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			
		$strUrl = '';
		global $g_arr_host_config;
		
		$strSql  = $coolshow->getSelectInfoByIdSql($id, $nChannel);
		$result = $this->_memcached->getSearchResult($strSql);
		if($result){
			foreach ($result as $row){
				$strUrl = $g_arr_host_config['cdnhost'].$row['url'];
			}
			return $strUrl;
		}
		
		$rows = $this->_getDb()->getCoolShow($strSql);
		if($rows === false){
			Log::write("CoolShowSearch::getUrl():executeQuery() failed, sql: ".$strSql, "log");
			return false;
		}
		
		foreach ($rows as $row){
			$strUrl = $g_arr_host_config['cdnhost'].$row['url'];
		}
		
		$result = $this->_memcached->setSearchResult($strSql, $rows);
		if(!$result){
			Log::write("CoolShowSearch::getUrl():setSearchResult() failed", "log");
		}
		return $strUrl;
	}
	
	public function getBrowseUrl($nCoolType, $id, $nChannel = 0)
	{
		$coolshow = CoolShowFactory::getCoolShow($nCoolType);
		
		$strUrl = '';
		global $g_arr_host_config;
		$strSql  = $coolshow->getSelectInfoByIdSql($id, $nChannel);
		
		$result = $this->_memcached->getSearchResult($strSql);
		if($result){
			foreach ($result as $row){
				$strUrl = $g_arr_host_config['cdnhost'].$row['mid_url'];
			}
			return $strUrl;
		}
	
		$rows = $this->_getDb()->getCoolShow($strSql);
		if($rows === false){
			Log::write("CoolShowSearch::getBrowseUrl():executeQuery() failed, sql: ".$strSql, "log");
			return false;
		}
	
		foreach ($rows as $row){
			$strUrl = $g_arr_host_config['cdnhost'].$row['mid_url'];
		}
	
		$result = $this->_memcached->setSearchResult($strSql, $rows);
		if(!$result){
			Log::write("CoolShowSearch::getBrowseUrl():setSearchResult() failed", "log");
		}
		return $strUrl;
	}
	
	public function checkIscharge($nCoolType, $id)
	{
		$coolshow = CoolShowFactory::getCoolShow($nCoolType);
		$bCharge = false;
		$strSql  = $coolshow->getSelectInfoByIdSql($id);
		$result = $this->_memcached->getSearchResult($strSql);
		if($result){
			foreach ($result as $row){
				$bCharge = isset($row['ischarge'])?$row['ischarge']:false;
			}
			return $bCharge;
		}
		
		$rows = $this->_getDb()->getCoolShow($strSql);
		if($rows === false){
			Log::write("CoolShowSearch::getBrowseUrl():executeQuery() failed, sql: ".$strSql, "log");
			return false;
		}
		
		foreach ($result as $row){
			$bCharge = isset($row['ischarge'])?$row['ischarge']:false;
		}
		
		$result = $this->_memcached->setSearchResult($strSql, $rows);
		if(!$result){
			Log::write("CoolShowSearch::getBrowseUrl():setSearchResult() failed", "log");
		}
		return $bCharge;
	}
	
	private function _searchLucene($coolshow, $data)
	{
		try{
			$jsonResult = get_respond_by_url(YL_SEARCH_LUCENE_URL, $data);
// 			Log::write('CoolXiuDb::searchCoolXiuList():getSearchResult() data:'.$data, 'debug');
// 			Log::write('CoolXiuDb::searchCoolXiuList():getSearchResult() jsonResult:'.$jsonResult, 'debug');
			$arrResult = json_decode($jsonResult, true);
			$arrCoolshow = array();
			if (!is_array($arrResult)){
				Log::write('CoolShowSearch::_searchLucene():get_respond_by_url() failed', 'log');
				return false;
			}
				
			foreach ($arrResult as $content){
				array_push($arrCoolshow, json_decode($content['content'], true));
			}
			
			$arrProtocol = $coolshow->getLucene($arrCoolshow);
			if($arrProtocol === false){
				Log::write('CoolShowSearch::_searchLucene():getProtocol() failed', 'log');
				return false;
			}
			
			return $arrProtocol;
			
		}catch(Exception $e){
			Log::write("CoolShowSearch::_searchLucene(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	}
	
	public function searchLucene($nCoolType, $strKeyWord, $bColor, $nPage, $nLimit)
	{
		try{	
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			$this->_setCoolShowParam($coolshow);
			$data = $coolshow->getLuceneParam($nCoolType, $strKeyWord, $bColor, $nPage, $nLimit);
			$coolshow->setChannel(REQUEST_CHANNEL_LUCENE);
	
			$result = $this->_memcached->getSearchResult($data.$nCoolType);
			if($result){
// 				Log::write('CoolXiuDb::searchLucene():getSearchResult()'.$sql, 'log');
				return json_encode($result);
			}
			
			$result = $this->_searchLucene($coolshow, $data);
			$arrProtocol = ($result === false)?array():$result;
			 
			if ($nCoolType == COOLXIU_TYPE_SCENE){
				$coolshow->resetKernel();
				$data = $coolshow->getLuceneParam($nCoolType, $strKeyWord);
// 				Log::write('CoolXiuDb::searchLucene():getLuceneParam() data'.$data, 'log');
				$result = $this->_searchLucene($coolshow, $data);
				$arrTempProtocol = ($result === false)?array():$result;
				$arrProtocol = array_merge($arrTempProtocol, $arrProtocol);
			}
			
			$result =  array('total_number'=> count($arrProtocol),
							 'ret_number'  => count($arrProtocol),
							 $coolshow->strType     => $arrProtocol
						);
			$this->_memcached->setSearchResult($data, $result, 12*60*60);
		}catch(Exception $e){
			Log::write("CoolShowSearch::searchLucene(): excepton error:".$e->getMessage(), "log");
			$result = get_rsp_result(false, 'get lucene exception');
			return $result;
		}
		return json_encode($result);
	}
	
	public function searchWebLucene($strKeyWord)
	{
		$theme = $this->_luceneCoolshow(COOLXIU_TYPE_THEMES, $strKeyWord);
		$ring  = $this->_luceneCoolshow(COOLXIU_TYPE_RING, $strKeyWord);
		$font  = $this->_luceneCoolshow(COOLXIU_TYPE_FONT, $strKeyWord);
		$scene = $this->_luceneCoolshow(COOLXIU_TYPE_SCENE, $strKeyWord);
	
		$arrCoolShow = array();
		if(!$theme){
			$theme = array();
		}
		if(!$ring){
			$ring  = array();
		}
		if(!$font){
			$font  = array();
		}
		if(!$scene){
			$scene = array();
		}
	
		$arrCoolShow['themes'] = $theme;
		$arrCoolShow['ring'] = $ring;
		$arrCoolShow['fonts'] = $font;
		$arrCoolShow['lockscreens'] = $scene;
	
		$arrSearch = array("result"=>true, "coolshow"=>$arrCoolShow );
		return json_encode($arrSearch);
	}
	
	private function _luceneCoolshow($nCoolType, $strKeyWord)
	{
		try{
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			$this->_setCoolShowParam($coolshow);
			$data = $coolshow->getLuceneParam($nCoolType, $strKeyWord);
				
			$result = $this->_memcached->getSearchResult($data.'forweb');
			if($result){
				// 				Log::write('CoolShowSearch::_luceneCoolshow():getSearchResult()'.$sql, 'log');
				return $result;
			}
	
			$jsonResult = get_respond_by_url(YL_SEARCH_LUCENE_URL, $data);
			// 			Log::write('CoolShowSearch::_luceneCoolshow() data:'.$data, 'debug');
			// 			Log::write('CoolShowSearch::_luceneCoolshow() jsonResult:'.$jsonResult, 'debug');
			$arrResult = json_decode($jsonResult, true);
			$arrCoolshow = array();
			if (!is_array($arrResult)){
				Log::write('CoolShowSearch::_luceneCoolshow():get_respond_by_url() failed', 'log');
				return false;
			}
	
			foreach ($arrResult as $content){
				array_push($arrCoolshow, json_decode($content['content'], true));
			}
				
			$arrProtocol = $coolshow->getWebProtocol($arrCoolshow);
			if($arrProtocol === false){
				Log::write('CoolShowSearch::_luceneCoolshow():getWebProtocol() failed', 'log');
				return false;
			}
				
			$this->_memcached->setSearchResult($data.'forweb', $arrProtocol, 12*60*60);
				
			return $arrProtocol;
				
		}catch(Exception $e){
			Log::write("CoolShowSearch::_luceneCoolshow(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	}
	
	private  function _getWebProtocol(CoolShow $coolshow, $strSql, $strCountSql)
	{
		$rows = $this->_getDb()->getCoolShow($strSql);
		if($rows === false){
			Log::write('CoolShowSearch::_getWebProtocol():getCoolShow() failed, SQL:'.$strSql, 'log');
			return false;
		}
	
		$arrProtocol = $coolshow->getWebProtocol($rows);
		if($arrProtocol === false){
			Log::write('CoolShowSearch::_getWebProtocol():getWebProtocol() failed', 'log');
			return false;
		}
	
		$count = $this->_getDb()->getCoolShowCount($strCountSql);
		if($count === false){
			Log::write('CoolShowSearch::_getWebProtocol():getCoolShowCount() failed, SQL:'.$strCountSql, 'log');
			return false;
		}
	
		$result =  array(
				'total_number'=> $count,
				'ret_number'  => count($arrProtocol),
				$coolshow->strType  => $arrProtocol
		);
	
		return $result;
	}
	
	public function searchWeb($nCoolType, $nSortType, $start, $limit)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			if(!$coolshow){
				Log::write('CoolShowSearch::getCoolShow() coolshow  is null', 'log');
				$result = get_rsp_result(false, 'get coolshow is null');
				return $result;
			}
				
			$coolshow->setChannel(REQUEST_CHANNEL_WEB);
			$this->_setCoolShowParam($coolshow);
			
			$strSql = $coolshow->getCoolShowWebSql($nSortType, $start, $limit);
			if(!$strSql){
				Log::write('CoolShowSearch::searchWeb():getCoolShowWebSql() failed Sql is empty', 'log');
				$result = get_rsp_result(false, 'get web sql is empty');
				return $result;
			}
	
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write("CoolXiuDb::searchCoolXiuListForWeb():getSearchResult()".$sql, 'log');
				return json_encode($result);
			}
	
			$strCountSql = $coolshow->getCoolShowWebCountSql();
			if($strCountSql === false){
				Log::write('CoolShowSearch::searchWeb():getCoolShowWebCountSql() failed', 'log');
				$result = get_rsp_result(false, 'get web count sql is empty');
				return $result;
			}
			
			$result = $this->_getWebProtocol($coolshow, $strSql, $strCountSql);
			if(!$result){
				Log::write('CoolShowSearch::searchWeb():_getWebProtocol() failed', 'log');
				$result = get_rsp_result(false, 'get web protocol failed');
				return $result;
			}
			
			$this->_memcached->setSearchResult($strSql, $result);
			
		}catch(Exception $e){
			Log::write('CoolShowSearch::searchWeb(): excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'search web exception');
			return $result;
		}
	
		return json_encode($result);
	}
	
	public function getCoolShowDetail($nCoolType, $strId, $channel = 0)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			if(!$coolshow){
				Log::write('CoolShowSearch::getCoolShowDetail() coolshow  is null', 'log');
				$result = get_rsp_result(false, 'get coolshow is null');
				return $result;
			}
				
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel($channel);
				
			$strSql = $coolshow->getCoolShowDetailSql($strId);
			if(!$strSql){
				Log::write('CoolShowSearch::getCoolShowDetail():getCoolShowDetailSql() failed Sql is empty', 'log');
				$result = get_rsp_result(false, 'get coolshow detail sql is empty');
				return $result;
			}

			$result = $this->_memcached->getSearchResult($strSql.$channel);
			if($result){
//				Log::write("CoolXiuDb::getCoolXiuDetails():getSearchResult()".$sql, "log");
				return json_encode($result);
			}

			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getCoolShowDetail():getCoolShow() failed, sql: '.$strSql, 'log');
				$result = get_rsp_result(false, 'get coolshow detail error');
				return $result;
			}
			$theme = null;
			if ($channel == REQUEST_CHANNEL_WEB){
				$theme = $coolshow->getDetailProtocol($rows);
				if(!$theme){
					Log::write("CoolShowSearch::getCoolShowDetail():getDetailProtocol() failed", "log");
					$result = get_rsp_result(false, 'get coolshow web protocol error');
					return $result;
				}
			}else {
				$arrTheme = $coolshow->getProtocol($rows);
				if(count($arrTheme) <= 0){
					Log::write("CoolShowSearch::getCoolShowDetail():getProtocol() failed", "log");
					$result = get_rsp_result(false, 'get coolshow protocol error');
					return $result;
				}
				$theme = $arrTheme[0];
			}	
			
			$result =  array('result'  => $theme?true:false,
							 'details' =>$theme,
							);
			
			$this->_memcached->setSearchResult($strSql.$channel, $result);
			
		}catch(Exception $e){
			Log::write('CoolShowSearch::getCoolShowDetail(): excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get coolshow detail excption');
			return $result;
		}
	
		return json_encode($result);
	}
	
	private function _getScore($nCoolType, $strCpid)
	{
		$scoreRecord = new ScoreRecord();
		$arrScore = $scoreRecord->searchCpidScore($nCoolType, $strCpid);
		if($arrScore === false){
			Log::write('CoolShowSearch::getScore():searchCpidScore() failed', 'log');
			return false;
		}
		
		$nOverall = 0;
		$nStar1 = 0;
		$nStar2 = 0;
		$nStar3 = 0;
		$nStar4 = 0;
		$nStar5 = 0;
		$nTotal = 0;
		$nTotalScore = 0;
		foreach ($arrScore as $s){
			$nScore = isset($s['score'])?$s['score']:0;
			$nCount = isset($s['count'])?$s['count']:0;
			
			if( 9 <= $nScore){
				$nStar5 += $nCount;
			}
			if(7 <= $nScore && $nScore <= 8){
				$nStar4 += $nCount;
			}
			if(5 <= $nScore && $nScore <= 6){
				$nStar3 += $nCount;
			}
			if(3 <= $nScore && $nScore <= 4){
				$nStar2 += $nCount;
			}
			if($nScore <= 2){
				$nStar1 += $nCount;
			}

			$nTotalScore += $nScore * $nCount;
			$nTotal += $nCount;
		}
		if ($nTotal != 0){
			$nOverall = round($nTotalScore/$nTotal, 1);
			$nStar1 = (int)($nStar1/$nTotal*100);
			$nStar2 = (int)($nStar2/$nTotal*100);
			$nStar3 = (int)($nStar3/$nTotal*100);
			$nStar4 = (int)($nStar4/$nTotal*100);
			$nStar5 = (int)($nStar5/$nTotal*100);
		}

		$score = new ScoreProtocol();
		$score->setScore($nOverall, $nTotal, $nStar1, $nStar2, $nStar3, $nStar4, $nStar5);
		return $score;
	}
	
	
	private function _getRecommend($coolshow, $strCpid)
	{
		
		$strSql = $coolshow->getRecommendCpidSql($strCpid);
		$rows = $this->_getRecommendDb()->getCoolShow($strSql);
		if($rows === false || count($rows) < 3 ){
			Log::write('CoolShowSearch::_getRecommend():getCoolShow() failed, SQL:'.$strSql, 'log');
			return false;
		}
		
		$arrProtocol = array();
		
		foreach ($rows as $row){
			$strSql = $coolshow->getSelectThemeByCpidSql($row['recommend']);
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false || count($rows) <= 0){
				Log::write('CoolShowSearch::_getRecommend():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
			
			$arrTemp = $coolshow->getProtocol($rows);
			$arrProtocol = array_merge($arrProtocol, $arrTemp);
		}
			
		return $arrProtocol;
	}
	
	/**
	 * 获取主题的评分汇总和相关推荐
	 * @param unknown_type $nCoolType
	 * @param unknown_type $strId
	 * @param unknown_type $strCpid
	 */
	public function getCoolShowRelevant($nCoolType, $strId, $strCpid)
	{
		try {
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			if(!$coolshow){
				Log::write('CoolShowSearch::getCoolShowRelevant() coolshow  is null', 'log');
				$result = get_rsp_result(false, 'get coolshow is null');
				return $result;
			}
		
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel(REQUEST_CHANNEL_RECOMMENED);

			$strSql = $coolshow->getRecommendSql();
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolXiuDb::searchCoolXiuList():getSearchResult()'.$strSql, 'log');
				return json_encode($result);
			}
			
			$score = $this->_getScore($nCoolType, $strCpid);
			if(!$score){
				Log::write('CoolShowSearch::getCoolShowRelevant() get score failed', 'log');
				$result = get_rsp_result(false, 'get score is null');
				return $result;
			}
				
			$arrProtocol = array();
			$arrProtocol = $this->_getRecommend($coolshow, $strCpid);
			if(!$arrProtocol){
				$arrProtocol = $this->_getProtocol($coolshow, $strSql);
			}
			
			$result = array('result'=>true,
							'score'=>$score,
							'recommend' => $arrProtocol);
			
			$this->_memcached->setSearchResult($strSql, $result, 60*60);
			return json_encode($result);
			
		}catch (Exception $e){
			Log::write('CoolShowSearch::getCoolShowDetail(): excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get coolshow detail excption');
			return $result;
		}
	}
	
	public function getCoolShowComment($nCoolType, $strId, $strCpid, $limit, $skip)
	{
		try {
			$scoreRecord = new ScoreRecord();
			$result = $scoreRecord->searchCpidRecord($nCoolType, $strCpid, $limit, $skip);
			if(!$result){
				$result = get_rsp_result(false, 'search record failed');
				return $result;
			}
			
			return $result;
			
		}catch (Exception $e){
			Log::write('CoolShowSearch::getCoolShowComment(): excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get coolshow comment excption');
			return $result;
		}
	}
	/**
	 * 一下函数为COOLUI5.5壁纸的banner区自运营资源，6.0改成了专辑，逻辑一样，为了统一主题/锁屏等此处未动，6.0不走此处
	 * 以protocolCode >=2为分界线，单是最终还是需要合并，后期看提供的资源的兼容性考虑代码合一 
	 * @return boolean|unknown|multitype:
	 */
	
	public function getWPBannerTop()
	{
		try {
			$coolshow = new Wallpaper();
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel(REQUEST_CHANNEL_BANNER);
				
			$strSql = $coolshow->getSelectBannerTopSql();
			if(!$strSql){
				Log::write('CoolShowSearch::getWPBannerTop():getSelectBannerTopSql() failed Sql is empty', 'log');
				return false;
			}
				
// 	  		Log::write('CoolShowSearch::getWPBannerTop():getSearchResult() SQL:'.$strSql, 'debug');
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
//  				Log::write('CoolShowSearch::getWPBannerTop():getSearchResult()'.$strSql, 'log');
				return $result;
			}
						
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getWPBannerTop():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
			
			$arrBanner = array();
			global $g_arr_host_config;
			foreach ($rows as $row){
				$row['url'] = $g_arr_host_config['cdnhost'].$row['url'];
				$row['type'] = 'tag';
				array_push($arrBanner, 	$row);
			}
			
			$this->_memcached->setSearchResult($strSql, $arrBanner, 60*60);
			
			return $arrBanner;
			
		}catch(Exception $e){
			Log::write("CoolShowSearch::getWPBannerTop(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	}
	
	public function getWPBannerTopList($strId)
	{
		try {
			$coolshow = new Wallpaper();
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel(REQUEST_CHANNEL_BANNER);
		
			$strSql = $coolshow->getSelectBannerTopListSql($strId);
			if(!$strSql){
				Log::write('CoolShowSearch::getWPBannerTopList():getSelectBannerTopListSql() failed Sql is empty', 'log');
				return false;
			}
		
// 			Log::write('CoolShowSearch::getWPBannerTopList():getSearchResult() SQL:'.$strSql, 'debug');
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolShowSearch::getWPBannerTopList():getSearchResult()'.$strSql, 'log');
				return $result;
			}
		
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getWPBannerTopList():getCoolShow() failed, SQL:'.$strSql, 'log');
				return false;
			}
				
			$arrProtocol = $coolshow->getProtocol($rows);
			$count = count($arrProtocol);
			$count = ((int)$count > 50)? 50: $count;
			$arrRsp =  array('total_number'=>$count,
								'ret_number'=>$count,
								'wallpapers'=>$arrProtocol);
			
			$json_rsp = json_encode($arrRsp);	
			$this->_memcached->setSearchResult($strSql, $json_rsp, 24 * 60*60);
				
			return $json_rsp;
				
		}catch(Exception $e){
			Log::write("CoolShowSearch::getWPBannerTopList(): excepton error:".$e->getMessage(), "log");
			return false;
		}
	}
	
	public function getChoiceWallpaer($start = 0, $limit = 10)
	{
		try {
			$coolshow = new Wallpaper();
			$this->_setCoolShowParam($coolshow);
			$coolshow->setChannel(REQUEST_CHANNEL_BANNER);
			
			$strSql = $coolshow->getChoiceWallpaperSql($start, $limit);
			if(!$strSql){
				Log::write('CoolShowSearch::getChoiceWallpaer():getChoiceWallpaperSql() failed, Sql is empty', 'log');
				$result = get_rsp_result(false, 'get protocol sql empty');
				return $result;
			}
				
//  		Log::write('CoolShowSearch::getChoiceWallpaer():getCoolShowListSql(), SQL:'.$strSql, 'debug');
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolShowSearch::getChoiceWallpaer():getSearchResult()'.$strSql, 'log');
				return json_encode($result);
			}
			
			$arrProtocol = $this->_getProtocol($coolshow, $strSql);
			if(!$arrProtocol){
				Log::write('CoolShowSearch::getChoiceWallpaer():_getProtocol() failed', 'log');
				$result = get_rsp_result(false, 'get protocol failed');
				return $result;
			}

			$strSql = $coolshow->getCountChoiceWallpaperSql();
			$count = $this->_getDb()->getCoolShowCount($strSql);
			if($count === false){
				Log::write('CoolShowSearch::getChoiceWallpaer():getCoolShowCount() failed, SQL:'.$strSql, 'log');
				$result = get_rsp_result(false, 'get choice wallpaper count failed');
				return $result;
			}
				
			
			$result =  array(
					'total_number'=> $count,
					'ret_number'  => count($arrProtocol),
					$coolshow->strType     => $arrProtocol
			);
				
			$this->_memcached->setSearchResult($strSql, $result, 12*60*60);
				
		}catch(Exception $e){
			Log::write('CoolShowSearch::getChoiceWallpaer() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get coolshow exception');
			return $result;
		}
	
		return json_encode($result);
	}
	
	public function getHotWord($nCoolType, $bColor = false)
	{
		try {
			$hw = new HotWord();
				
			$strSql = $hw->getSelectHotWordSql($nCoolType, $bColor);
			if(!$strSql){
				Log::write('CoolShowSearch::getHotWord():getSelectHotWordSql() failed, Sql is empty', 'log');
				$result = get_rsp_result(false, 'get hotword sql empty');
				return $result;
			}
		
			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolShowSearch::getChoiceWallpaer():getSearchResult()'.$strSql, 'log');
				return $result;
			}
			
			
			$rows = $this->_getDb()->getCoolShow($strSql);
			if($rows === false){
				Log::write('CoolShowSearch::getHotWord():getCoolShow() failed, SQL:'.$strSql, 'log');
				$result = get_rsp_result(false, 'get hotword falied');
				return $result;
			}
		
			$result =  array('result'=> true,
							'hotwords'  => $rows);
		
			$result = json_encode($result);
			$this->_memcached->setSearchResult($strSql, $result, 12*60*60);
			
			return $result;
		
		}catch(Exception $e){
			Log::write('CoolShowSearch::getHotWord() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get hot word exception');
			return $result;
		}
	}
	
	public function getDesignerCoolShow($nCoolType = 0, $strCyid, $nStart, $nLimit)
	{
		try {
			if (empty($strCyid)){
				Log::write('CoolShowSearch::getDesignerCoolShow() cyid is empy', 'log');
				$result = get_rsp_result(false, 'cyid is empy');
				return $result;
			}
			
			$coolshow = CoolShowFactory::getCoolShow($nCoolType);
			if(!$coolshow){
				Log::write('CoolShowSearch::getDesignerCoolShow() coolshow  is null', 'log');
				$result = get_rsp_result(false, 'get coolshow is null');
				return $result;
			}
			
			$this->_setCoolShowParam($coolshow);
			
			$strSql = $coolshow->getDesignerCoolShowSql($strCyid, $nStart, $nLimit);
			if(!$strSql){
				Log::write('CoolShowSearch::getDesignerCoolShow():getDesignerCoolShowSql() failed, Sql is empty', 'log');
				$result = get_rsp_result(false, 'get protocol sql empty');
				return $result;
			}

			$result = $this->_memcached->getSearchResult($strSql);
			if($result){
// 				Log::write('CoolXiuDb::getDesignerCoolShow():getSearchResult()'.$strSql, 'log');
				return json_encode($result);
			}
			
			$arrProtocol = $this->_getProtocol($coolshow, $strSql);
			
			$sql = $coolshow->getCountDesignerCoolShowSql($strCyid);
			$count = $this->_getDb()->getCoolShowCount($sql);
			if($count === false){
				Log::write('CoolShowSearch::getDesignerCoolShow():getCoolShowCount() failed, SQL:'.$strSql, 'log');
				return false;
			}
			
			$result =  array('result'=>true,
							 'total_number'=> $count,
							 'ret_number'  => count($arrProtocol),
							 $coolshow->strType     => $arrProtocol
			);
			
 			$this->_memcached->setSearchResult($strSql, $result, 60*60);
		
 			return json_encode($result);
		}catch(Exception $e){
			Log::write('CoolShowSearch::getDesignerCoolShow() excepton error:'.$e->getMessage(), 'log');
			$result = get_rsp_result(false, 'get designer res exception');
			return $result;
		}
	}
}