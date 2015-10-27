<?php
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'configs/config.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/ThemeSql.sql.php';
require_once 'tasks/protocol/ThemesProtocol.php';
require_once 'tasks/protocol/ThemesWebProtocol.php';
require_once 'tasks/protocol/ThemesDetailsProtocol.php';

class Theme extends CoolShow
{

	private $_nModule;
	public function __construct($nModule = COOLXIU_TYPE_THEMES)
	{
		parent::__construct();
		$this->strType = 'themes';
		$this->bHavePaid = true;
		$this->_nModule = $nModule;
	}
	
	public function setPayRatio()
	{
		if ($this->_nVercode > 28) {
			$this->nPay			= 1;
			$this->nFree		= 2;
		}else{
			$this->nPay		    = 1;
			$this->nFree		= 1;
		}
	}
	
	public function getCoolShowListSql($nStart = 0, $nLimit = 0)
	{
// 		if($this->_nType == 2){
// 			$this->_nKernel = 1;
// 		}
		
		$strCondition = $this->_getCondition();
		$strOrder = $this->_getSort();
		$sql = sprintf(SQL_SELECT_THEME_INFO, $this->_nType, 
											  $this->_nWidth, $this->_nHeight,
											  $strCondition,
											  $strOrder,
											  $nStart, $nLimit,
											  $strOrder);
		return $sql;
	}
	
	public function getCoolShowCountSql()
	{
// 		if($this->_nType == 2){
// 			$this->_nKernel = 1;
// 		}
	
		$strCondition = $this->_getCondition();	
		$sql = sprintf(SQL_COUNT_THEME_INFO, $this->_nType,
											 $this->_nWidth, $this->_nHeight,
											 $strCondition);
		return $sql;
	}

	private function _getCondition()
	{
		$strIsCharge = '';
		if($this->_nProtocolCode < 1 /*$this->_nVercode >= 86 */){
			$strIsCharge = ' AND t.ischarge = 0 ';
		}
				
		$strKernel = sprintf(' AND tinfo.kernel = %d ', $this->_nKernel);
		if($this->_nKernel >= 4){
			$strKernel = sprintf(' AND tinfo.kernel >= 3 ', $this->_nKernel);
		}
		
		if ($this->_nModule == COOLXIU_TYPE_THEMES_CONTACT #新增短信和联系人模块
			|| $this->_nModule == COOLXIU_TYPE_THEMES_MMS
			|| $this->_nModule == COOLXIU_TYPE_THEMES_ICON){
			$strKernel = sprintf(' AND tinfo.kernel >= 5 ', $this->_nKernel);
		}
		$strChoice = '';
		if ($this->_nSort == COOLXIU_SEARCH_CHOICE){
			$strChoice = ' AND t.choice = 1 ';
		}
		
		$this->_resetRatio();
		
		if(strcmp('Coolpad8750', $this->_strProduct) == 0){
			$this->_nKernel = 2;
		}		
		
		$strType = '';
		if($this->_nSubType != 0 && $this->_nType == 0 ){
// 			if($this->_nSubType == 1){//推荐单独处理
// 				$strType .=  ' AND choice =1  ';
// 			}else{
				$strType .=  sprintf(' AND subtype =  %d ', $this->_nSubType);
// 			}
		}
		
		$strCharge = $this->getCharge();
		
		$strCondition = $strType.$strIsCharge.$strKernel.$this->strPayCondition.$strCharge.$strChoice;
		
		//按机型过滤
		$tmparray1 = explode('8681', $this->_strProduct);
		$tmparray2 = explode('8692', $this->_strProduct);
	    if(count($tmparray1)>1 || count($tmparray2)>1){
	    	$strCondition .= sprintf(' AND t.cpid not in ("510261832", "510261827", "510261820") ');
	    } 
		
		return $strCondition;
	}
	
	public function _getAlbumConditon()
	{
		$strKernel = sprintf(' AND tinfo.kernel = %d ', $this->_nKernel);
		if($this->_nKernel >= 4){
			$strKernel = sprintf(' AND tinfo.kernel >= 3 ');// AND tinfo.kernel <= %d, $this->_nKernel);
		}
		
		$this->_resetRatio();
		
		$strCondition = $strKernel;
		return $strCondition;
	}
	
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100)
	{
		$strId = sql_check_str($strId, 64);
		$strCondition = $this->_getAlbumConditon();
		$sql = 	sprintf(SQL_SELECT_THEME_ALBUMS, $strId, $this->_nWidth, $this->_nHeight, $strCondition, $nStart, $nNum);
		return $sql;
	}
	
	public function getSelectRscSql($id)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_THEME_INFO_WITH_ID, $id);
		return $sql;
	}

	public function getSelectThemeByCpidSql($strCpid)
	{
		$strCpid = sql_check_str($strCpid, 64);
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_THEME_INFO_WITH_CPID, $this->_nWidth, $this->_nHeight, $strCpid, $strCondition);
		return $sql;
	}
		
	public function getSelectBannerSql()
	{
// 		if ($this->_nProtocolCode < 1){
// 			return false;
// 		}
		
		$strCondtion = $this->_getCondition();
		
		if($this->_bWidgetBanner ){
			$strCondtion .= 'AND b.istop = 1';
		}
		
		$sql = 	sprintf(SQL_SELECT_THEMES_BANNER, $this->_nWidth, $this->_nHeight, $strCondtion);
		return $sql;
	}
	
	public function getSelectWidgetSql()
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_THEME_WIDGET,  $this->_nWidth, $this->_nHeight, $strCondition);
		return $sql;
	}
	
	public function getSelectInfoByIdSql($id, $nChannel = 0)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_THEMES_DL_URL, $id);
		return $sql;
	}

	private function _getSort()
	{
		$strOrderBy = ' ORDER BY t.asort DESC, t.id DESC';
		if($this->_nProtocolCode >= 3 )$strOrderBy = ' ORDER BY t.tdate DESC ';
		switch ($this->_nSort){
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY mdl DESC ';
				break;
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY t.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY t.asort DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY t.insert_time DESC ';
				break;
			default:
				//$strOrderBy   = ' ORDER BY t.asort DESC ';
				break;
		}
		
		return $strOrderBy;
	}
	
	private function _getOrderBy($nSortType)
	{
		switch ($nSortType){
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY t.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY dl.download_times DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY t.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY t.insert_time DESC ';
				break;
			default:
				$strOrderBy   = ' ORDER BY t.insert_time DESC ';
				break;
		}
	
		return $strOrderBy;
	}
	
	public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10)
	{
		$strOrder = $this->_getOrderBy($nSortType);
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_THEME_WEB_INFO, $this->_nType, $strCondition,
												  $this->_nWidth, $this->_nHeight,
												  $strOrder,
												  $nStart, $nLimit);
		return $sql;
	}
	
	public function getCoolShowWebCountSql()
	{
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_COUNT_THEME_WEB_INFO, $this->_nType, $strCondition,
												$this->_nWidth, $this->_nHeight);
		return $sql;
	}
	
	public function getCoolShowDetailSql($strId)
	{
		$strId = sql_check_str($strId, 64);
		if($this->_nWidth == 0 && $this->_nHeight == 0){
			$sql = 	sprintf(SQL_SELECT_THEMES_DETAILS, $strId);
		}else{
			$strCondition = $this->_getCondition();
			$sql = 	sprintf(SQL_SELECT_THEMES_DETAILS_RATIO, $strId, $this->_nWidth, $this->_nHeight, $strCondition);
		}
		return $sql;
	}
	
	public function getDesignerCoolShowSql($strCyid, $nStart, $nNum)
	{
		$strId = sql_check_str($strCyid, 64);
		$sql = 	sprintf(SQL_SELECT_DESIGNER_THEME_INFO, $this->_nWidth, $this->_nHeight, $strCyid, $nStart, $nNum);
		return $sql;
	}
	
	public function getCountDesignerCoolShowSql($strCyid)
	{
		$strId = sql_check_str($strCyid, 64);
		$sql = 	sprintf(SQL_COUNT_DESIGNER_THEME_INFO, $this->_nWidth, $this->_nHeight, $strCyid);
		return $sql;
	}
	
	public function getRecommendCpidSql($strCpid)
	{
		$sql = sprintf(SQL_SELECT_RECIOMMEND_CPID, $strCpid);
		return $sql;
	}
	
	public function getRecommendSql()
	{
		if($this->_nType == 2){
			$this->_nKernel = 1;
		}
		
		$strCondition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_RAND_THEME_INFO, $this->_nType,
												$this->_nWidth, $this->_nHeight,
												$strCondition);
		return $sql;
	}
	
	public function getLucene($rows)
	{
		return $this->getProtocol($rows);
	}
	
	public function getProtocol($rows, $nType = 0)
	{
		$arrThemes = array();
		foreach($rows as $row){
			if(!array_key_exists($row['identity'], $arrThemes)){
				$theme = new ThemesProtocol();
				$theme->setVercode($this->_nVercode);
				$theme->setKernelCode($this->_nKernel);
				$theme->setProtocol($row, $this->_nChannel);
				
				if($this->_nSort == COOLXIU_SEARCH_CHOICE){
					$theme->setTag(COOLXIU_TAG_CHOICE);
				}
				$arrThemes[$row['identity']] = $theme;
			}
	
			$prev = new PrevProtocol();
	
			if((int)$row['prev_type'] == 1){
				$arrThemes[$row['identity']]->setMainPrev($row);
			}
	
			$prev->setPrev($row);
			$arrThemes[$row['identity']]->pushPrevImg($prev, $row['prev_type']);
		}
	
		$arrProtocol = array();
		foreach ($arrThemes as $key => $theme){
			array_push($arrProtocol, $theme);
		}
	
		return $arrProtocol;
	}
		
	public function getBannerProtocol($rows, $nType = 0)
	{
		$arrBanner = array();
		$coolxius = array();
		$strBannerId = '';
		foreach($rows as $row){
			$strBannerId = $row['bannerid'];
			if(!array_key_exists($row['bannerid'], $arrBanner)){
				$banner = new BannerProtocol();
				$banner->setBanner($row['bannerurl'], $row['bannername']);
				$arrBanner = $arrBanner + array($strBannerId => $banner);
			}
			$strThemeId = $row['identity'];
			if(!array_key_exists($row['identity'], $arrBanner[$strBannerId]->bannerRes)){
				$theme = new ThemesProtocol();
				$theme->setVercode($this->_nVercode);
				$theme->setKernelCode($this->_nKernel);
				$theme->setProtocol($row, $this->_nChannel);
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
			$temBanner = $temBanner->getProtocol($this->strType);
			array_push($arr_coolxius, $temBanner);
		}
		
		return $arr_coolxius;
	}
	
	public function getWebProtocol($rows)
	{
		$arrThemes = array();
		foreach($rows as $row){
			if(!array_key_exists($row['identity'], $arrThemes)){
				$theme = new ThemesWebProtocol();
				$theme->setKernelCode($this->_nKernel);
				$theme->setProtocol($row, $this->_nChannel);
// 				array_push($arr_coolxius, $theme);
				$arrThemes[$row['identity']] = $theme;
			}
		}
		$arrProtocol = array();
		foreach ($arrThemes as $key => $theme){
			array_push($arrProtocol, $theme);
		}
		return $arrProtocol;
	}
	
	public function getDetailProtocol($rows)
	{
		try{
			$theme = null;
			$arrPrev = array();
			foreach($rows as $row){
				if(!$theme){
					$theme = new ThemesDetailsProtocol();
					$theme->setVercode($this->_nVercode);
					$theme->setKernelCode($this->_nKernel);
					$theme->setProtocol($row, $this->_nChannel);
				}
	
				$prev = new PrevProtocol();
				$prev->setPrev($row);
	
				if((int)$row['prev_type'] == 1){
					$strMainUrl = $prev->getMainPrev($row);
				}
				array_push($arrPrev, $prev);
			}
			
			$nImgNum = count($arrPrev);
			if($theme){
				$theme->setPrevImgs($nImgNum, $strMainUrl, $arrPrev);
			}
			
			return $theme;
		}catch (Exception $e){
			Log::write("Theme::getThemeDetail() exception ".$e->getMessage(), "log");
			return false;
		}
		return false;
	}
}
