<?php
require_once 'lib/memcached.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
class CoolXiuMemcache{
	private $memcached;
	function __construct(){
		$this->memcached = new Memecached();	
		global $g_arr_memcache;
		$this->connectMemcached($g_arr_memcache['memcache']);
	}
	
	function connectMemcached($servers){
		$result = $this->memcached->addServers($servers);
		if(!$result){
			log::write("CoolXiuMemcache::connectMemcached():addServers() failed", "log");
			return false;
		}
		return true;
	}
	
	function setSearchResult($sql, $ret){
		if(empty($sql)){
			return false;
		}
		$key = md5($sql);
		
		$result = $this->memcached->get($key);
		if(!$result){
			$result = $this->memcached->set($key, $ret);
		}else{
			$result = $this->memcached->replace($key, $ret);
		}
		return $result;
	}
	
	function getSearchResult($sql){
		if(empty($sql)){
			return false;
		}
		$key = md5($sql);
		$result = $this->memcached->get($key);
		return $result;
	}
}

function  unit_coolxiu_memcached_test(){
	$memcached = new CoolXiuMemcache();
	global $g_arr_memcache;
	$memcached->connectMemcached($g_arr_memcache['memcache']);
	$memcached->setSearchResult("AAAA", "BBBB");
	$result = $memcached->getSearchResult("AAAA");
	echo $result;
}

//unit_coolxiu_memcached_test();
?>
