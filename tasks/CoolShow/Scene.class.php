<?php
require_once 'public/public.php';
require_once 'configs/config.php';
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/SceneSql.sql.php';
require_once 'tasks/protocol/ScreenProtocol.php';

class Scene extends CoolShow
{

	public function __construct()
	{
		parent::__construct();
		$this->strType = 'lockscreens';
		$this->bHavePaid = true;
	}
	
	public function setType($strType)
	{
		$this->strType = $strType;
	}

	public function resetKernel()
	{
		$this->_nKernel		= '';
		$this->_nWidth		= 0;
		$this->_nHeight		= 0;
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
		$strCondition = $this->_getCondition();
		$strOrder = $this->_getSort();
		if(!$this->_bNewVer){
			$sql = sprintf(SQL_SELECT_SCENE, $this->_nKernel, $strCondition, $nStart, $nLimit);
		}else{
			$sql = sprintf(SQL_SELECT_SCENE_APK, $this->_nWidth, $this->_nHeight, $strCondition, $strOrder, $nStart, $nLimit);
		}	
		
		return $sql;		
	}
	
	private function _getSort()
	{
		$strOrderBy = ' ORDER BY scene.asort DESC ';
		switch ($this->_nSort){
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY download_times DESC ';
				break;
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY scene.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY scene.insert_time DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY scene.insert_time DESC ';
				break;
			default:
				//$strOrderBy   = ' ORDER BY t.asort DESC ';
				break;
		}
	
		return $strOrderBy;
	}

	private function _getCondition()
	{
		$strIsCharge = '';
		if($this->_nProtocolCode < 1 /*$this->_nVercode < 18 || $this->_nVercode == 83*/ ){
			$strIsCharge = ' AND scene.ischarge = 0 ';
		}
		
		#最初版本默认为1，新增字段从2开始,动态锁屏资源为3，主题共享锁屏资源为2
		$strKernel = sprintf(' AND scene.kernel = %d ', $this->_nKernel);
		if($this->_nKernel >= 2){//如果内核为2则取所有邮箱的锁屏资源
			$strKernel = sprintf('AND scene.kernel <= %d ', $this->_nKernel);
		}
		$strPackage = '';
		if(strcmp($this->strType, 'livewallpapers') == 0 ){
			$strPackage = " AND scene.package = 'com.vlife.coolpad.wallpaper' " ;
		}
		
		$this->_resetRatio();
		
		$this->_bNewVer = true;
		
		$strCharge = $this->getCharge();
		
		return $strIsCharge.$this->strPayCondition.$strKernel.$strPackage.$strCharge;
	}

    private function _getMd5Condition(){
        if(strcmp($this->strType, 'livewallpapers') == 0 ){
            $strPackage = " AND package = 'com.vlife.coolpad.wallpaper' " ;
        }
    }
	
	private function _getAlbumCondition()
	{
		#最初版本默认为1，新增字段从2开始
		$strKernel = sprintf(' AND scene.kernel = %d ', $this->_nKernel);
		if($this->_nKernel >= 2 ){//如果内核为2则取所有邮箱的锁屏资源
			$strKernel = '';
		}
		
		$this->_resetRatio();
		
		return $strKernel;
	}
	
	function getCoolShowCountSql()
	{
		$strCondition = $this->_getCondition();		
		if(!$this->_bNewVer){
			$sql = sprintf(SQL_COUNT_SCENE, $this->_nKernel, $strCondition);
		}else{
			$sql = sprintf(SQL_COUNT_SCENE_APK, $strCondition);
		}
		return $sql;
	}
	
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100)
	{
		$strId = sql_check_str($strId, 64);
		$strCondition = $this->_getAlbumCondition();
		$sql = sprintf(SQL_SELECT_SCENE_ALBUM, $strId, $this->_nWidth, $this->_nHeight, $strCondition, $nStart, $nNum);
		return $sql;
	}

    public function getSelectMd5Sql($strId)
    {
        $strId = sql_check_str($strId, 64);

        if ($this->_nKernel == 1 || $this->_nKernel == 10) {
            $sql = 	sprintf(SQL_SELECT_SCENE_DL_MD5, $strId, $this->_nKernel);
        } else {
            $sql = 	sprintf(SQL_SELECT_SCENE_THEME_DL_MD5, $strId, $this->_nKernel, $this->_nWidth, $this->_nHeight);
        }

        return $sql;
    }

	public function getSelectBannerSql()
	{
		
	}
	
	public function getSelectRscSql($id)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_SCENE_WITH_ID, $id);
		return $sql;
	}
	
	public function getSelectInfoByIdSql($id, $nChannel = 0)
	{
		$id = sql_check_str($id, 64);
		$sql = sprintf(SQL_SELECT_SCENE_BY_ID, $id);
		return $sql;
	}

	private function _getOrderBy($nSortType)
	{
		switch ($nSortType){
			case  COOLXIU_SEARCH_LAST:
				$strOrderBy   = ' ORDER BY scene.id DESC ';
				break;
			case  COOLXIU_SEARCH_HOT:
				$strOrderBy   = ' ORDER BY dl.download_times DESC ';
				break;
			case  COOLXIU_SEARCH_CHOICE:
				$strOrderBy   = ' ORDER BY scene.id DESC ';
				break;
			case  COOLXIU_SEARCH_HOLIDAY:
				$strOrderBy   = ' ORDER BY scene.id DESC ';
				break;
			default:
				$strOrderBy   = ' ORDER BY scene.id ASC ';
				break;
		}
	
		return $strOrderBy;
	}
	
	public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10)
	{
		$strOrder = $this->_getOrderBy($nSortType);
		$strCondition = $this->_getCondition();
		if(!$this->_bNewVer){
			$sql = sprintf(SQL_SELECT_SCENE_WEB, $this->_nKernel, $strCondition, $strOrder, $nStart, $nLimit);
		}else{
			$sql = sprintf(SQL_SELECT_SCENE_APK_WEB, $strOrder, $nStart, $nLimit);
		}	
		return $sql;
	}
	
	public function getCoolShowWebCountSql()
	{
		$sql = $this->getCoolShowCountSql();
		return $sql;
	}
	
	public function getCoolShowDetailSql($strId)
	{
		$strId = sql_check_str($strId, 64);
		$sql = 	sprintf(SQL_SELECT_SCENE_DETAILS, $strId);
		return $sql;
	}
	
	public function getLucene($rows)
	{
		return $this->getProtocol($rows);
	}
	
	public function getProtocol($rows, $nType = 0)
	{
		$arrSceen = array();
		foreach ($rows as $row){
			$screenProtocol = new ScreenProtocol();
			$screenProtocol->setVercode($this->_nVercode);
			$screenProtocol->setProtocol($row, $this->_nChannel, true);
			array_push($arrSceen, $screenProtocol);
		}
		return $arrSceen;
	}
	
	public function getBannerProtocol($rows, $nType = 0)
	{
		
	}

	public function getWebProtocol($rows)
	{
		return $this->getProtocol($rows);
	}
	
	public function getDetailProtocol($rows)
	{
		try{
			$sceen = null;
			foreach ($rows as $row){
				$screenProtocol = new ScreenProtocol();
				$screenProtocol->setVercode($this->_nVercode);
				$screenProtocol->setProtocol($row, $this->_nChannel, true);
				$sceen = $screenProtocol;
			}
			return $sceen;
			
		}catch (Exception $e){
			Log::write("Scene::getDetailProtocol() exception ".$e->getMessage(), "log");
			return false;
		}
		return false;
	}
}
