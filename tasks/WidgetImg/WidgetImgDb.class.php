<?php

require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'task/WidgetImg/WidgetImg.class.php';

class WidgetImgDb extends DBManager
{
	private $_memcached;
	
	public function __construct()
	{
		$this->_memcached = new MemDb();
		$this->connectMySqlCommit();
	}

	
	private function _getThemeWidgetList($sql)
	{
		$rows = $this->executeQuery($sql);
		if($rows === false){
			Log::write('WidgetImgDb::_getThemeWidgetList():executeQuery() sql:'.$sql.' failed', 'log');
			return false;
		}
		
		$arrWidgetTemp = array();
		foreach ($rows as $row){
			
			$strRegion = isset($row['region'])?$row['region']:'';
			$strUrl	   = isset($row['url'])?$row['url']:'';
			$strCpid   = isset($row['cpid'])?$row['cpid']:'';
			$strName   = isset($row['name'])?$row['name']:'';
			
			if(!array_key_exists($row['identity'], $arrWidgetTemp)){
				$widget = new WidgetImg();
				
				$widget->setRegion($strRegion, $strUrl);
				$widget->setWidgetParam($strCpid,$strName);
				
				$arrWidgetTemp = $arrWidgetTemp + array($row['identity'] => $widget);
				continue;
			}
			$arrWidgetTemp[$row['identity']]->setWidgetParam($strCpid,$strName);
		}
		
		$arrWidget = array();
		foreach ($arrWidgetTemp as $key => $value){
			array_push($arrWidget, $value);
		}
		return $arrWidget;
	}
	
	public function getThemeWidget()
	{
		$sql = WidgetImg::getSelectWidgetThemes();
		$result = $this->_memcached->getSearchResult($sql);
		if($result){
			return json_decode($result);
		}
		
		$arrWidget = $this->_getThemeWidgetList($sql);
		if($arrWidget === false){
			Log::write('WidgetImgDb::getThemeWidget():_getThemeWidgetList() failed', 'log');
			return false;
		}
		
		$result = $this->_memcached->setSearchResult($sql, $arrWidget);
		return json_encode($arrWidget);
	}
}