<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'lib/MongoPHP.lib.php';


class ReqStatis extends DBManager{
	private $_statis;
	private $_mongo;
	function __construct(){
		$this->_statis = new Statis();

		global $g_arr_recod_mongo_db_conn;
		$this->_mongo = new MongoPHP($g_arr_recod_mongo_db_conn);

		global $g_arr_recod_db_conn;
		$this->connectMySqlPara($g_arr_recod_db_conn);
	}
}