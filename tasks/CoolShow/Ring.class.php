<?php
require_once 'public/public.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/RingSql.sql.php';
require_once 'tasks/protocol/RingProtocol.php';
require_once 'tasks/protocol/BannerProtocol.php';

class Ring extends CoolShow
{

	public function __construct()
	{
		parent::__construct();
		$this->strType = 'ring';
	}
	
	public function setPayRatio()
	{
		
	}
	
	public function getCoolShowListSql($nStart = 0, $nLimit = 0)
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_RING, $this->_nType, $strCondition, $nStart, $nLimit);
		return $sql;
	}
	
	public function getCoolShowCountSql()
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_COUNT_RING, $this->_nType, $strCondition);
		return $sql;
	}
	
	private function _getCondition()
	{
		$strCondition = '';
		if($this->_nVercode < 18 || $this->_nVercode == 83){
			$strIsCharge = ' AND ischarge = 0 ';
		}
		
		if($this->_nSubType != 0 && $this->_nType == 0 ){
			if($this->_nSubType == 1){//推荐单独处理
				$strCondition .=  ' AND choice =1  ';
			}else{
				$strCondition .=  sprintf(' AND subtype =  %d ', $this->_nSubType);
			}
		}
		
		return $strCondition;
	}	
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100){
		$strId = sql_check_str($strId, 64);
		$sql = sprintf(SQL_SELECT_RING_ALBUMS, $strId, $nStart, $nNum);
		return $sql;
	}
	
	public function getSelectRscSql($id){
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_RING_WITH_ID, $id);
		return $sql;
	}
	
	public function getSelectBannerSql(){
		$strCondition = '';
		if ($this->_bWidgetBanner){
			$strCondition = ' AND b.istop = 1 ';
		}
		$sql = sprintf(SQL_SELECT_RING_BANNER, $strCondition);
		return $sql;
	}
	
	public function getSelectInfoByIdSql($id, $nChannel = 0)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_RING_BY_ID, $id);
		return $sql;
	}
	
	
	private function _getOrderBy($nSortType)
	{
		switch ($nSortType){
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY ring.id DESC ';
				break;
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY dl.download_times DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY ring.id DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY ring.id DESC ';
				break;
			default:
				$strOrderBy   = ' ORDER BY asort ASC ';
				break;
		}
	
		return $strOrderBy;
	}
	
	public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10)
	{
		$strOrder = $this->_getOrderBy($nSortType);
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_RING_WEB,  $this->_nType, $strCondition, $strOrder, $nStart, $nLimit);
		return $sql;
	}
	
	public function getCoolShowWebCountSql()
	{
		$sql = $this->getCoolShowCountSql();
		return $sql;
	}

	public function getLucene($rows)
	{
		return $this->getProtocol($rows);
	}
	
	public function getProtocol($rows, $nType = 0)
	{
		$arrProtocol = array();
		foreach ($rows as $row){
			$ring = new RingProtocol();
			$ring->setVercode($this->_nVercode);
			$ring->setProtocol($row, $this->_nChannel);
			array_push($arrProtocol, $ring);
		}
		return $arrProtocol;
	}
	
	public function getBannerProtocol($rows, $nType = 0)
	{
		$arrBanner = array();
		$strBannerId = '';
		foreach($rows as $row){
			$strBannerId = $row['bannerid'];
			if(!array_key_exists($row['bannerid'], $arrBanner)){
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
				
			$ring = new RingProtocol();
			$ring->setVercode($this->_nVercode);
			$ring->setProtocol($row, $this->_nChannel);
				
			$arrBanner[$strBannerId]->setBannerRes($row['identity'], $ring);
		}
	
		$arrProtocol = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol('ring');
			array_push($arrProtocol, $temBanner);
		}
	
		return $arrProtocol;
	}

	public function getWebProtocol($rows)
	{
		return $this->getProtocol($rows);
	}
}