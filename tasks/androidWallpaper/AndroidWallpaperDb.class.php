<?php
/**
 * MODIFY BY liangweiwei@yulong.com AT 2012-08-03
 */
require_once 'lib/WriteLog.lib.php';
require_once 'lib/mySql.lib.php';
require_once 'lib/DBManager.lib.php';
require_once 'configs/config.php';
require_once 'lib/MemDb.lib.php';
require_once 'tasks/statis/Product.class.php';
require_once 'tasks/protocol/WallpaperProtocol.php';
require_once 'tasks/androidWallpaper/AndroidWallpaper.class.php';

class AndroidWallpaperDb extends DBManager{
	private $_adWp;	
	private $_width;
	private $_height;	
	private $_start;
	private $_num;	
	private $_type;
	private $_product;
	private $_memcached;
	function __construct(){		
		$this->_width	= 1440;
		$this->_height	= 1280;
		$this->_type	= 0;
		$this->_adWp = new AndroidWallpaper(); 
		$this->_product = new Product();
		
		global $g_arr_memcache_config;
		$this->_memcached = new MemDb();
		$this->_memcached->connectMemcached($g_arr_memcache_config);
		
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['androidesk']);		
	}

	public function setProduct($product){
		$this->_product->setProduct($product);
	}
	
	function setSearchCondition($width, $height, $start, $req_num, $req_type){
		$this->_adWp->setAndroidWp($width, $height, $start, $req_num, $req_type);
		$this->_width  = $width * 2;
		$this->_height = $height;

		$this->_start  = $start;
		$this->_num	   = $req_num;
		
		$this->_type   = $req_type;
	}
	private function _setType($type)
	{
		$this->_type = $type;
	}
	
	private function _getAdRsc($strId)
	{
		try{
			$sql = AndroidWallpaper::getSelectAdWithIdSql($strId);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
			
			$rows = $this->_getAndroidWp($sql);
			if($rows === false){
				Log::write("AndroidWallpaperDb::getAdRsc():_getAndroidWp() failed", "log");
				return false;
			}
			
			$protocol = $rows;
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("AndroidWallpaperDb::getAdRsc() failed", "log");
			}
		}catch(Exception $e){
			Log::write("AndroidWallpaperDb::getAdRsc()exception error:".$e->getMessage(), "log");
			return false;
		}
		return $protocol;
	}	
	
	private function _getWpRsc($id, $nWidth, $nHeight, $type)
	{
		try{
			$this->_setType($type);
			$this->_adWp->setRatio($nWidth, $nHeight);
			$arr_size_tag = $this->_getAndroidWpSizeTag();
			if($arr_size_tag === false){
				Log::write("AndroidWallpaperDb::getSrc():_getAndroidWpSizeTag() failed", "log");
				return false;
			}
				
			$sql = AndroidWallpaper::getSelectWpWithIdSql($arr_size_tag[0], $type, $id);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
				
			$this->_explodeRatio($arr_size_tag);
				
			$rows = $this->_getAndroidWp($sql);
			if($rows === false){
				Log::write("AndroidWallpaperDb::getSrc():_getAndroidWp() failed", "log");
				return false;
			}
				
			$protocol = null;
			$protocol = $this->_getPrototcol($rows);
			if(!$protocol){
				Log::write("AndroidWallpaperDb::getSrc():_getPrototcol() failed", "log");
				return false;
			}
			$result = $this->_memcached->setSearchResult($sql, $protocol);
			if(!$result){
				Log::write("AndroidWallpaperDb::getSrc() failed", "log");
			}
		}catch(Exception $e){
			Log::write("AndroidWallpaperDb::getSrc()exception error:".$e->getMessage(), "log");
			return false;
		}
		return $protocol;
	}
	
	public function getRsc($id, $nWidth, $nHeight, $type, $nAdType)
	{
		if($nAdType){
			return $this->_getAdRsc($id);
		}else
		{
			return $this->_getWpRsc($id, $nWidth, $nHeight, $type);
		}
		return false;	
	}
	
	private function _getAndroidWp($sql)
	{
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("AndroidWallpaperDb::_getAndroidWp():executeQuery() sql".$sql." failed", "log");
			return false;
		}
		return $rows;
	}
	
	private function _getPrototcol($rows)
	{
		$arrProtocol = array();
		foreach ($rows as $row){
			$wp_protocol = new WallpaperProtocol() ;
			$wp_protocol->setWallpaperType($this->_type);
			if($this->_type == AndroidWallpaper::hdorigin){
				$wp_protocol->setWallpaperRatio($row['origin_w'], $row['origin_h']);
			}else {
				$wp_protocol->setWallpaperRatio($this->_width, $this->_height);
			}
			$cpid = isset($row['id'])?$row['id']:'';
			$wp_protocol->setAndroideskWallpaper($row, 0, $cpid);
			array_push($arrProtocol, $wp_protocol);
		}
		return  $arrProtocol;
	}
	
	private function _getAndroidWpList($sql){		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("AndroidWallpaperDb::getAndroidWpList():executeQuery() sql".$sql." failed", "log");
			return false;
		}	
		$arr_android_wp = array();	
		foreach ($rows as $row){
			$wp_protocol = new WallpaperProtocol() ;
			$wp_protocol->setProduct($this->_product);
			$wp_protocol->setWallpaperType($this->_type);
			if($this->_type == AndroidWallpaper::hdorigin){
				$wp_protocol->setWallpaperRatio($row['origin_w'], $row['origin_h']);
			}else {
				$wp_protocol->setWallpaperRatio($this->_width, $this->_height);
			}
			$cpid = isset($row['id'])?$row['id']:'';
			$wp_protocol->setAndroideskWallpaper($row, 0, $cpid);
			array_push($arr_android_wp, $wp_protocol);
		}
		return $arr_android_wp;
	}
	
	private function _getAndroidWpCount(){
		$sql = $this->_adWp->getCountAndroidWpSql();
		$count = $this->executeScan($sql);
		if($count === false){
			Log::write("AndroidWallpaperDb::getAndroidWpCount():executeScan() sql".$sql." failed", "log");
			return false;
		}
		return $count;
	}
	
	private function _getAndroidWpSizeTag(){
		$sql = $this->_adWp->getSelectAndroidWpSizeTagSql();
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write("AndroidWallpaperDb::_getAndroidWpSizeTag():executeQuery() sql".$sql." failed", "log");
			return false;
		}
		return $rows;
	}
	
	private function _explodeRatio($arr_size_tag){
		$resolution = explode('_',$arr_size_tag[0]['size_res']);
		$this->_width	= $resolution[1]; 
		$this->_height	= $resolution[2]; 
	} 
	
	
	private function _getWpList($sorttype = 0) 
	{
		try{
			$arr_size_tag = $this->_getAndroidWpSizeTag();
			if($arr_size_tag === false){
				Log::write("AndroidWallpaperDb::_getWpList():_getAndroidWpSizeTag() failed", "log");
				return false;
			}
				
			$sql = $this->_adWp->getSelectAndroidWpByLimitSql($arr_size_tag[0], $sorttype);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return $result;
			}
				
			$this->_explodeRatio($arr_size_tag);
				
			$arr_android_wp = $this->_getAndroidWpList($sql);
			if($arr_android_wp === false){
				Log::write("AndroidWallpaperDb::_getWpList():getAndroidWpList() failed, product".$this->_product->name.", width:".$this->_width.", height:".$this->_height, "log");
				return false;
			}
			// 查询当前请求返回的壁纸量
			$rsp_num = $this->getQueryCount();
				
			$count = $this->_getAndroidWpCount();
			if($count === false){
				Log::write("AndroidWallpaperDb::_getWpList():_getAndroidWpCount() failed", "log");
				return false;
			}
		
			$json_rsp =  array('total_number'=>$count,
					'ret_number'=>$rsp_num,
					"wallpapers"=>$arr_android_wp);
				
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 3*60*60);
			if(!$result){
				Log::write("AndroidWallpaperDb::_getWpList:setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("AndroidWallpaperDb::_getWpList()exception error:".$e->getMessage(), "log");
			return false;
		}
		return $json_rsp;
	}
	
	public function searchWpList($sorttype = 0)
	{
		/**
		 * 2015.08.06
		 * 自运营壁纸叠加到安卓壁纸智商
		 * 需要兼容旧版本
		 * 
		 */

		$num   = $this->_num;
		$start = $this->_start;
		$nCpTotal = 0;
		$nRetNum  = 0;
		$arrRetWallpaper = array();
		$bChoice = false;
 		if ($this->_type == 0){
 			$bChoice = true;
		}
		$coolshow = new CoolShowSearch();
		$result = $coolshow->getWallpaper($bChoice, $this->_type, $this->_start, $this->_num);
		if(!$result){
			return  false;
		}
		return json_encode($result);
		
		if($result){
			$nCpTotal = $result['total_number'];
			$nRetNum = $result['ret_number'];
			$arrRetWallpaper = $result['wallpapers'];
			
// 			if ($nCpTotal >= ($this->_start + $this->_num)){
// 				return json_encode($result);
// 			}
			
			if($nRetNum == 0){
				$num   = $this->_num;
				$start = $this->_start - $nCpTotal;
			}else{
				$num   = $this->_num - $nRetNum;
				$start = 0;
			}
		}
// 		}

		$this->_adWp->setReqNum($start, $num);
		$result = $this->_getWpList($sorttype);
		if (!$result){
			return false;
		}

		$json_rsp =  array('total_number'=>(int)($nCpTotal) + (int)($result['total_number']),
						   'ret_number'=>(int)($nRetNum) + (int)($result['ret_number']),
							'wallpapers'=>array_merge($arrRetWallpaper, $result['wallpapers']));		
		return json_encode($json_rsp);
	}	
	
	
	function searchWpListForWeb($sorttype){
		try{
			$arr_size_tag = $this->_getAndroidWpSizeTag();
			if($arr_size_tag === false){
				Log::write("AndroidWallpaperDb::searchWpListForWeb():_getAndroidWpSizeTag() failed", "log");
				return false;
			}
			if($sorttype == COOLXIU_SEARCH_LAST){
				$sql  = $this->_adWp->getSelectAndroidWpLastSql($arr_size_tag[0]);
			}else
			if($sorttype == COOLXIU_SEARCH_HOT){
				$sql  = $this->_adWp->getSelectAndroidWpHotSql($arr_size_tag[0]);
			}else
			if($sorttype == COOLXIU_SEARCH_CHOICE){
				$sql  = $this->_adWp->getSelectAndroidWpChoiceSql($arr_size_tag[0]);
			}else{
				$sql  = $this->_adWp->getSelectAndroidWpByLimitSql($arr_size_tag[0]);
			}	
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return json_encode($result);
			}
				
			$this->_explodeRatio($arr_size_tag);
				
			$arr_android_wp = $this->_getAndroidWpList($sql);
			if($arr_android_wp === false){
				Log::write("AndroidWallpaperDb::searchWpList():getAndroidWpList() failed", "log");
				return false;
			}
			// 查询当前请求返回的壁纸量
			$rsp_num = $this->getQueryCount();
				
			$count = $this->_getAndroidWpCount();
			if($count === false){
				Log::write("AndroidWallpaperDb::searchWpList():_getAndroidWpCount() failed", "log");
				return false;
			}
	
			$json_rsp =  array('total_number'=>$count,
					'ret_number'=>$rsp_num,
					"wallpapers"=>$arr_android_wp);
				
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 3600);
			if(!$result){
				Log::write("AndroidWallpaperDb::setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("AndroidWallpaperDb::searchWpList()exception error:".$e->getMessage(), "log");
			return false;
		}
		return json_encode($json_rsp);
	}
	
	/**
	 * 获取广告类型列表
	 */
	private function _getAdCoverList(){
		$sql = $this->_adWp->getSelectAdTypeListSql();
		$result = $this->_memcached->getSearchResult($sql);
		if($result){
			return $result;
		}
		
		$rows = $this->executeQuery($sql);
		if($rows === false){
			log::write("AndroidWallpaperDb::_getAdTypeList():executeQuery() sql: ".$sql."faled", "log");
			return false;
		}
		
		$result = $this->_memcached->setSearchResult($sql, $rows, 60*60);
		return $rows;
	}
	
	function searchAdCover(){
		$rows = $this->_getAdCoverList();
		if($rows === false){
			log::write("searchAdCover::_getAdListByType():_getAdTypeList faled", "log");
			return false;
		}
		
 		return $rows;
// 		$count = $this->getQueryCount();
// 		$json_rsp =  array('number'=>$count,
// 							"adconver"=>$rows);
// 		return json_encode($json_rsp);
	}

	/**
	 * 根据类型获取广告列表
	 * @param unknown_type $type
	 */
	private function _getAdverList($sql,  $channel = 0, $adid = ''){
		$rows = $this->executeQuery($sql);
		if($rows === false){
			log::write("AndroidWallpaperDb::_getAdListByType():executeQuery() sql: ".$sql."faled", "log");
			return false;
		}
		$arr_android_wp_ad = array();	
		foreach ($rows as $row){
			$wp_protocol = new WallpaperProtocol() ;
			$wp_protocol->setProduct($this->_product);
			$wp_protocol->setAndroideskWallpaper($row, $channel, $adid);
			$wp_protocol->setWallpaperRatio($this->_width, $this->_height);
			array_push($arr_android_wp_ad, $wp_protocol);
		}
		return $arr_android_wp_ad;
	}
	
	private function _getAdverCount($type){
		$sql = $this->_adWp->getCountAdListSql($type);
		$count = $this->executeScan($sql);
		if($count === false){
			log::write("AndroidWallpaperDb::_getAdverCount():executeScan() sql: ".$sql."faled", "log");
			return false;
		}
		return $count;
	}
	
	function searchAdver($adid, $channel = 0){
		try{
			$arr_size_tag = $this->_getAndroidWpSizeTag();
			if($arr_size_tag === false){
				Log::write("AndroidWallpaperDb::searchAdver():_getAndroidWpSizeTag() failed", "log");
				return false;
			}
			$sql = $this->_adWp->getSelectAdListByTypeSql($arr_size_tag[0], $adid);
			$result = $this->_memcached->getSearchResult($sql);
			if($result){
				return json_encode($result);
			}			
				
			$this->_explodeRatio($arr_size_tag);
			
			$arr_android_wp = $this->_getAdverList($sql, $channel, $adid);
			if($arr_android_wp === false){
				Log::write("AndroidWallpaperDb::searchAdver():_getAdverList() failed", "log");
				return false;
			}
			// 查询当前请求返回的壁纸量
			$rsp_num = $this->getQueryCount();
				
			$count = $this->_getAdverCount($adid);
			if(false === $count){
				Log::write("AndroidWallpaperDb::searchAdver():_getAdverCount() failed", "log");
				return false;
			}
			$count = ((int)$count > 50)? 50: $count; 						
			$json_rsp =  array('total_number'=>$count,
								'ret_number'=>$rsp_num,
								"wallpapers"=>$arr_android_wp);
				
			$result = $this->_memcached->setSearchResult($sql, $json_rsp, 3600);
			if(!$result){
				Log::write("AndroidWallpaperDb::searchAdver():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("AndroidWallpaperDb::searchAdver()exception error:".$e->getMessage(), "log");
			return false;
		}
		return json_encode($json_rsp);
	}
	
	function getAlbums($adid, $channel = 0){
		try{
			$arr_size_tag = $this->_getAndroidWpSizeTag();
			if($arr_size_tag === false){
				Log::write("AndroidWallpaperDb::getAlbums():_getAndroidWpSizeTag() failed", "log");
				$result = get_rsp_result(false, 'get android wallpaper size tag failed');
				return $result;
			}
			$sql = $this->_adWp->getSelectAdListByTypeSql($arr_size_tag[0], $adid);
			$result = $this->_memcached->getSearchResult($sql.'album');
			if($result){
// 				Log::write('AndroidWallpaperDb::getAlbums(): SQL'.$sql.' \n result'.json_encode($result), 'error');
				return $result;
			}
	
			$this->_explodeRatio($arr_size_tag);
			$arr_android_wp = $this->_getAdverList($sql, $channel, $adid);
			if($arr_android_wp === false){
				Log::write("AndroidWallpaperDb::getAlbums():_getAdverList() failed", "log");
				$result = get_rsp_result(false, 'get android wallpaper failed');
				return $result;
			}
			$json_rsp =  array('result'=>true,
							   'albums'=>$arr_android_wp);

			$json_result = json_encode($json_rsp);
			$this->_memcached->setSearchResult($sql.'album', $json_result, 3600);
			
			return $json_result;
		}catch(Exception $e){
			Log::write("AndroidWallpaperDb::getAlbums()exception error:".$e->getMessage(), "log");
			$result = get_rsp_result(false, 'get android wallpaper exception');
			return $result;
		}
	}
}
?>