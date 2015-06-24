<?php
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/LiveWallpaperSql.sql.php';
require_once 'tasks/protocol/LiveWallpaperProtocol.php';

class LiveWallpaper extends CoolShow
{

	public function __construct()
	{
		parent::__construct();
		$this->strType = 'livewallpapers';
	}
	
	public function setPayRatio()
	{
		$this->nPay			= 2;
		$this->nFree		= 1;
	}
	
	private function _getCondition()
	{
		$strCondtion = '';
		return $strCondtion.$this->strPayCondition;
	}
	public function getCoolShowListSql($nStart = 0, $nLimit = 0)
	{
		$strCondtion = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_LIVE_WALLPAPER, $strCondtion, $nStart, $nLimit);
		return $sql;
	}
	
	public function getCoolShowCountSql()
	{
		$strCondtion = $this->_getCondition();
		$sql = sprintf(SQL_COUNT_LIVE_WALLPAPER, $strCondtion);
		return $sql;
	}

	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100)
	{
	
	}
	
	public function getSelectRscSql($id)
	{
		
	}
	
	public function getSelectBannerSql()
	{
	
	}
	
	public function getSelectInfoByIdSql($id, $nChannel = 0)
	{

	}
	
	
	public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10)
	{
		
	}
	public function getCoolShowWebCountSql()
	{
		
	}

	public function getLucene($rows)
	{
	
	}
	
	public function getProtocol($rows, $nType = 0)
	{
		$arrProtocol = array();
		foreach($rows as $row){
			$wallpaper = new LiveWallpaperProtocol();
			$wallpaper->setProtocol($row, $this->_nChannel);
			array_push($arrProtocol, $wallpaper);
		}		
		return $arrProtocol;
	}
	
	public function getBannerProtocol($rows, $nType = 0)
	{
		
	}
	
	public function getWebProtocol($rows)
	{
		
	}
}