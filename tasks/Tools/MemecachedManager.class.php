<?php
require_once 'lib/memcached.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
class MemecachedManage{
	private $_memcached;
	function __construct(){
		$this->memcached = new Memecached();
		global $g_arr_memcache;
		$this->connectMemcached($g_arr_memcache['memcache']);
	}

	function connectMemcached($servers){
		$result = $this->memcached->addServers($servers);
		if(!$result){
			log::write("MemecachedManage::connectMemcached():addServers() failed", "log");
			return false;
		}
		return true;
	}
	
	public function clearMemcache(){
		$result = $this->memcached->flush();
		if(!$result){
			log::write("MemecachedManage::clearMemcach() failed", "log");
			return false;
		}
		return true;
	}	
}
?>