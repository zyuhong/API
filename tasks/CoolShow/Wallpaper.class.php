<?php
require_once 'public/public.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/WallpaperSql.sql.php';
require_once 'tasks/protocol/WallpaperProtocol.php';
require_once 'tasks/protocol/BannerProtocol.php';

class Wallpaper extends CoolShow
{

	public function __construct()
	{
		parent::__construct();
		$this->strType = 'wallpapers';
	}
	
	public function setPayRatio()
	{
	}
	
	public function getCoolShowListSql($nStart = 0, $nLimit = 0)
	{
		$this->_resetRatio();
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO, $this->_nType, 
								$this->_nWidth, $this->_nHeight, 
								$nStart, $nLimit);
		return $sql;
	}
	
	protected function _resetRatio()
	{
		parent::_resetRatio();

		if(($this->_nWidth == 960 && $this->_nHeight == 800) 
			|| ($this->_nWidth == 960 && $this->_nHeight == 854)){

			$this->_nWidth   = 1080;
			$this->_nHeight   = 960;
		}
		

		if(($this->_nWidth == 1440 && $this->_nHeight == 1280)
			|| ($this->_nWidth == 2400 && $this->_nHeight == 1920)){
		
			$this->_nWidth   = 2160;
			$this->_nHeight  = 1920;
		}
	}
	
	public function getCoolShowCountSql()
	{
		$this->_resetRatio();
		$sql = sprintf(SQL_COUNT_WALLPAPER_INFO, $this->_nType, 
								$this->_nWidth, $this->_nHeight);
		return $sql;
	}

	public function getCoolShowWithIdSql($id)
	{
		
	}
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100)
	{
		$strId = sql_check_str($strId, 64);
		$this->_resetRatio();
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_ALBUMS, $strId, $this->_nWidth, $this->_nHeight);
		return $sql;
	}

	public function getSelectRscSql($id)
	{
		
	}

	public function getSelectTopBannerSql($id)
	{
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_TOP_BANNER);
		return $sql;
	}
	
	public function getSelectBannerTopSql()
	{
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_BANNER_TOP);
		return $sql;
	}
	
	public function getSelectBannerTopListSql($strId)
	{
		$strId = sql_check_str($strId, 64);
		$this->_resetRatio();
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_BANNER_TOP_LIST, $this->_nWidth, $this->_nHeight, $strId);
		return $sql;
	}
	
	public function getSelectBannerSql()
	{
		$this->_resetRatio();
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_BANNER, $this->_nWidth, $this->_nHeight);
		return $sql;
	}
	
	public function getSelectInfoByIdSql($id, $nChannel = 0)
	{
		$id = sql_check_str($id, 64);
		if($nChannel == REQUEST_CHANNEL_BANNER){
			$sql = 	sprintf(SQL_SELECT_WLLPAPER_BANNER_LARGE_URL, $id);
			return $sql;
		}
		
		$sql = 	sprintf(SQL_SELECT_WLLPAPER_LARGE_URL, $id);
		return $sql;
	}
	
	public function getChoiceWallpaperSql($nStart, $nLimit, $bChoice = 0, $nType = 0)
	{
		$this->_resetRatio();
		$strCondition = sprintf(' AND type = %d ', $nType);
		if($bChoice){
			$strCondition = '';// AND choice = 1 ';
		}
		$sql = sprintf(SQL_SELECT_CHOICE_WALLPAPER_INFO, $this->_nWidth, $this->_nHeight, $strCondition, $nStart, $nLimit);
		return $sql;
	}

    public function getAmazeWallpaperSql($nStart, $nLimit, $nType = 0)
    {
        $this->_resetRatio();
        $sql = sprintf(SQL_SELECT_AMAZE_WALLPAPER_INFO, $this->_nWidth, $this->_nHeight, $nStart, $nLimit);
        return $sql;
    }
	
	public function getCountChoiceWallpaperSql($bChoice = 0, $nType = 0)
	{
		$this->_resetRatio();
		$strCondition = sprintf(' AND type = %d ', $nType);
		if($bChoice){
			$strCondition = '';// AND choice = 1 ';
		}
		$sql = sprintf(SQL_COUNT_CHOICE_WALLPAPER_INFO, $this->_nWidth, $this->_nHeight, $strCondition);
		return $sql;
	}
	
	public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10)
	{
		
	}
	public function getCoolShowWebCountSql()
	{
		
	}
	public function getLucene($rows)
	{
		$arrProtocol = array();
		foreach($rows as $row){
			$wallpaper = new WallpaperProtocol();
			$wallpaper->setVercode($this->_nVercode);
			$wallpaper->setProduct($this->_product);
			$wallpaper->setWallpaper($row, $this->_nChannel);
			
			$strUrl = $row['id_'.$this->_nWidth.'_'.$this->_nHeight];
			$strMidUrl = $row['id_'.$this->_nWidth.'_'.$this->_nHeight];
			$strSmallUrl = $row['id_'.$this->_nWidth.'_'.$this->_nHeight];
			$wallpaper->setWallpaperUrl($strUrl, $strMidUrl, $strSmallUrl);
			
			array_push($arrProtocol, $wallpaper);
		}
		return $arrProtocol;
	}
	
	public function getProtocol($rows, $nType = 0)
	{
		$arrProtocol = array();
		foreach($rows as $row){
			$wallpaper = new WallpaperProtocol();
			$wallpaper->setVercode($this->_nVercode);
			$wallpaper->setProduct($this->_product);
			$wallpaper->setWallpaper($row, $this->_nChannel);
			array_push($arrProtocol, $wallpaper);
		}		
		return $arrProtocol;
	}
	
	public function getBannerProtocol($rows, $nType = 0)
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
				$wp->setVercode($this->_nVercode);
				$wp->setWallpaper($row, $this->_nChannel);
				$arrBanner[$strBannerId]->setBannerRes($strWpId, $wp);
			}
		}
		
		$arr_coolxius = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol($this->strType);
			array_push($arr_coolxius, $temBanner);
		}
		
		return $arr_coolxius;
	}
	
	public function getWebProtocol($rows)
	{
		
	}
}