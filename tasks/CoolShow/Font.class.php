<?php
require_once 'public/public.php';
require_once 'configs/config.php';
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/FontSql.sql.php';
require_once 'tasks/protocol/FontProtocol.php';
require_once 'tasks/protocol/BannerProtocol.php';

class Font extends CoolShow
{
	
	public function __construct()
	{
		parent::__construct();
		$this->strType = 'fonts';
		$this->bHavePaid = true;
	}
	
	public function setPayRatio()
	{
		if ($this->_nVercode > 28) {
			$this->nPay			= 1;
			$this->nFree		= 1;
		}else{
			$this->nPay			= 1;
			$this->nFree		= 1;
		}
	}
	public function getCoolShowListSql($nStart = 0, $nLimit = 0)
	{
		$strCondition = $this->_getCondition();
		$strOrder = $this->_getSort();
		$sql = sprintf(SQL_SELECT_FONT, $strCondition, $strOrder, $nStart, $nLimit);
// 		Log::write('Font::getCoolShowListSql() SQL'.$sql, 'debug');
		return $sql;
	}

	private function _getSort()
	{
		$strOrderBy = ' ORDER BY font.asort DESC ';
		switch ($this->_nSort){
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY mdl DESC ';
				break;
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY font.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY font.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY font.insert_time DESC ';
				break;
			default:
				//$strOrderBy = ' ORDER BY font.asort DESC ';
				break;
		}
	
		return $strOrderBy;
	}
	
	private function _getCondition()
	{
		$strCondition = '';
		$strIsCharge = ' AND font.valid = 1 ';
		if($this->_nProtocolCode < 1 || $this->_nVercode == 83/*$this->_nVercode < 18 || $this->_nVercode == 83*/){
			$strIsCharge .= ' AND font.ischarge = 0 ';
		}
		
		$strCharge = $this->getCharge();
		
		$strCondition .= $strIsCharge.$this->strPayCondition.$strCharge;
		
		return $strCondition;
	}
	
	function getCoolShowCountSql()
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_COUNT_FONT, $strCondition);
		return $sql;
	}
	
	public function getCoolShowDetailSql($strId)
	{
		$sql = 	sprintf(SQL_SELECT_FONT_BY_ID, $strId);
		return $sql;
	}	
	
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100)
	{
		$strId = sql_check_str($strId, 64);
		$sql = sprintf(SQL_SELECT_FONT_ALBUM, $strId, $nStart, $nNum);
		return $sql;
	}
	
	public function getSelectBannerSql(){
		$strCondition = '';
		if ($this->_bWidgetBanner){
			$strCondition = ' AND b.istop = 1 ';
		}
		$sql = sprintf(SQL_SELECT_FONT_BANNER, $strCondition);
		return $sql;
	}
	
	public function getSelectRscSql($id)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_FONT_BY_ID, $id);
		return $sql;
	}

	public function getSelectInfoByIdSql($id, $nChannel = 0 )
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_FONT_DL_URL, $id);
		return $sql;
	}
	
	private function _getOrderBy($nSortType)
	{
		$strOrderBy = '';
		switch ($nSortType){
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY font.id DESC ';
				break;
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY dl.download_times DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY font.id DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY font.id DESC ';
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
		$sql = sprintf(SQL_SELECT_FONT_WEB, $strCondition, $strOrder, $nStart, $nLimit);
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
		$arr_font = array();
		foreach ($rows as $row){
			$font_protocol = new FontProtocol();
			$font_protocol->setVercode($this->_nVercode);
			$font_protocol->setKernelCode($this->_nKernel);
			$font_protocol->setProtocol($row, $this->_nChannel);
			array_push($arr_font, $font_protocol);
		}
		return $arr_font;
	}
	
	public function getBannerProtocol($rows, $nType = 0)
	{
		$arrBanner = array();
		foreach($rows as $row){
			$strBannerId = $row['bannerid'];
			if(!array_key_exists($strBannerId, $arrBanner)){
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
	
			$font = new FontProtocol();
			$font->setVercode($this->_nVercode);
			$font->setKernelCode($this->_nKernel);
			$font->setProtocol($row, $this->_nChannel);
	
			$arrBanner[$strBannerId]->setBannerRes($row['id'], $font);
		}
	
		$arrProtocol = array();
		foreach ($arrBanner as $key => $temBanner){
			$temBanner = $temBanner->getProtocol($this->strType);
			array_push($arrProtocol, $temBanner);
		}
	
		return $arrProtocol;
	}
	

	public function getWebProtocol($rows)
	{
		return $this->getProtocol($rows);
	}
}