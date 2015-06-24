<?php
require_once 'public/public.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/AlarmSql.sql.php';
require_once 'tasks/protocol/AlarmProtocol.php';
require_once 'tasks/protocol/BannerProtocol.php';

class Alarm extends CoolShow
{

	public function __construct()
	{
		parent::__construct();
		$this->strType = 'alarm';
	}
	
	public function setPayRatio()
	{
		
	}
	
	public function getCoolShowListSql($nStart = 0, $nLimit = 0)
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_ALARM, $this->_nSubType, $strCondition, $nStart, $nLimit);
		return $sql;
	}
	
	public function getCoolShowCountSql()
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_COUNT_ALARM, $this->_nSubType, $strCondition);
		return $sql;
	}
	
	private function _getCondition()
	{
		$strCondition = '';
		return $strCondition;
	}	
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100){
		$strId = sql_check_str($strId, 64);
		$sql = sprintf(SQL_SELECT_ALARM_ALBUMS, $strId, $nStart, $nNum);
		return $sql;
	}
	
	public function getSelectRscSql($id){
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_ALARM_WITH_ID, $id);
		return $sql;
	}
	
	public function getSelectBannerSql(){
		$sql = sprintf(SQL_SELECT_ALARM_BANNER);
		return $sql;
	}
	
	public function getSelectInfoByIdSql($id, $nChannel = 0)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_ALARM_BY_ID, $id);
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
		$sql = sprintf(SQL_SELECT_ALARM_WEB,  $this->_nSubType, $strCondition, $strOrder, $nStart, $nLimit);
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
			$alarm = new AlarmProtocol();
			$alarm->setProtocol($row, $this->_nChannel);
			array_push($arrProtocol, $alarm);
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
				
			$alarm = new AlarmProtocol();
			$alarm->setProtocol($row, $this->_nChannel);
				
			$arrBanner[$strBannerId]->setBannerRes($row['identity'], $alarm);
		}
	
		$arrProtocol = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol('alarm');
			array_push($arrProtocol, $temBanner);
		}
	
		return $arrProtocol;
	}

	public function getWebProtocol($rows)
	{
		return $this->getProtocol($rows);
	}
}