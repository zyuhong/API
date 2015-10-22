<?php
require_once 'lib/WriteLog.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'configs/config.php';
require_once 'public/public.php';

require_once 'tasks/widget/WidgetFactory.class.php';

class WidgetSearch
{
	private $_widgetFac;
	public function __construct()
	{
		$this->_memcached = new MemDb();
		global $g_arr_memcache_config;
		$this->_memcached->connectMemcached($g_arr_memcache_config);
		
		$this->_widgetFac = new WidgetFactory();
	}
	
	public function setWidgetParam($type, $width, $height, $kernel)
	{
		$bResult = $this->_widgetFac->setWidget($type, $width, $height, $kernel);
		return $bResult;
	}
	
	public function searchWidget($vercode = 0)
	{
		if(!$this->_widgetFac){
			Log::write("WidgetSearch::searchWidget():_widgetFac is null", "log");
			return false;
		}
		
		$sql = $this->_widgetFac->getWidgeMemSql($vercode);
		if(!$sql || empty($sql)){
			Log::write('WidgetSearch::searchWidget():getWidgeMemSql() sql is empty', 'log');
			$result = get_rsp_result(false, 'get widget mem sql failed');
			return $result;
		}
		
		$result = $this->_memcached->getSearchResult($sql);
		if($result){
			return $result;
		}		

		$result = $this->_widgetFac->getWidgetProtocol();
		if(!$result){
			Log::write('WidgetSearch::searchWidget():getWidgetProtocol() failed', 'log');
			$result = get_rsp_result(false, 'get widget protocol failed');
			return $result;
		}
		
		$result =  json_encode($result);
		
		$bResult = $this->_memcached->setSearchResult($sql, $result);
		if(!$bResult){
			Log::write('WidgetSearch::setSearchResult() failed', 'log');
		}
		
		return $result;
	}
}