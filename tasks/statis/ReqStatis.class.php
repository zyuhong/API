<?php
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/statis/Statis.class.php';
require_once 'configs/config.php';
require_once 'lib/MongoPHP.lib.php';

class ReqStatis extends DBManager{
	private $_statis;
	private $_mongo;
	function __construct(){
		$this->_statis = new Statis();
		
// 		global $g_arr_recod_mongo_db_conn;
// 		$this->_mongo = new MongoPHP($g_arr_recod_mongo_db_conn);
// 		$this->_mongo->selectDB('db_yl_coolshow_records');
		
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['coolshow_record']);
	}
	
	private function _setStatisParam(){
		try{
			$product = '';
			$imei 	 = '';
			$meid 	 = '';
			$imsi 	 = '';
			$net     = '';
			
			if(isset($_POST['statis'])){
				$json_param = isset($_POST['statis'])?$_POST['statis']:'';

				$json_param = stripslashes($json_param);
				$arr_param = json_decode($json_param, true);

				$product = isset($arr_param['product'])?$arr_param['product']:'';
				$imsi = isset($arr_param['imsi'])?$arr_param['imsi']:'';
				$imei = isset($arr_param['imei'])?$arr_param['imei']:'';
				$meid = isset($arr_param['meid'])?$arr_param['meid']:'';
				$version = isset($arr_param['version'])?$arr_param['version']:'';
				$width = (int)(isset($arr_param['width'])?$arr_param['width']:0);
				$height = (int)(isset($arr_param['height'])?$arr_param['height']:0);
				$net 	= isset($arr_param['network'])?$arr_param['network']:'';
			}else{
				$product = (isset($_GET['product']))?$_GET['product']:'';
				$imei 	 = (isset($_GET['imei']))?$_GET['imei']:'';
				$meid 	 = (isset($_GET['meid']))?$_GET['meid']:'';
				$imsi 	 = (isset($_GET['imsi']))?$_GET['imsi']:'';
				$width 	 = (int)(isset($_GET['width'])?$_GET['width']:0);
				$height  = (int)(isset($_GET['height'])?$_GET['height']:0);
			}

			$ip = isset($_SERVER['HTTP_X_REAL_IP'])?$_SERVER['HTTP_X_REAL_IP']:$_SERVER['REMOTE_ADDR'];
			$session = '';//session_id();
			
			$this->_statis->setTerminalParam($ip, $session, $product, $meid, $imei, $imsi, $height, $width, $net);
			$this->_statis->checkParam();
				
		}catch(Exception $e){
			Log::write('StatisDB::_setStatisParam() error: '.$e->getMessage(), 'log');
			return false;
		}
		return true;
	}
	
	/**
	 * 用户请求的流水记录
	 * @param unknown_type $type
	 * @param unknown_type $cooltype
	 * @param unknown_type $height
	 * @param unknown_type $width
	 * @return boolean
	 */
	public function recordRequest($type, $cooltype, $height, $width, $kernel = 1, 
								  $id = '', $cpid = '', $url = '', $channel = 0, $vercode = 0){
/*		$this->_statis->setRequsetParam($height, $width, $type, $cooltype, $kernel, 
										$id, $cpid, $url, $channel, $vercode);
		$this->_setStatisParam();
			
		$sql = $this->_statis->getInsertReqStatisSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("ReqStatis::recordRequest():executeSql() sql: ".$sql." failed", "log");
// 			return false;
		}
// 		$arrRecord = $this->_statis->getInsertReqStatisMongo();
// 		$this->_mongo->insert($arrRecord['table'], $arrRecord['record']);
 */
		return true;
	}
/**
 * 浏览记录
 * @param unknown_type $id
 * @param unknown_type $type
 * @param unknown_type $cooltype
 * @param unknown_type $height
 * @param unknown_type $width
 * @return boolean
 */
	public function recordBrowseRequest($id, $type, $cooltype, $height, $width, 
										$cpid = '', $url = '', $channel = 0){
		$this->_statis->setRequsetParam($height, $width, $type, $cooltype, 1, $id, $cpid, $url, $channel);
		$this->_setStatisParam();
		
		$sql = $this->_statis->getInsertBrowseStatisSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("ReqStatis::recordBrowseRequest():executeSql() sql: ".$sql." failed", "log");
// 			return false;
		}
// 		$arrRecord = $this->_statis->getInsertBrowseStatisMongo();
// 		$this->_mongo->insert($arrRecord['table'], $arrRecord['record']);
		return true;
	}
/**
 * 下载记录
 * @param unknown_type $id
 * @param unknown_type $cooltype
 * @param unknown_type $height
 * @param unknown_type $width
 * @return boolean
 */
	public function recordDownloadRequest($id, $cooltype, $height, $width,
										  $cpid = '', $url = '', $type = 0, $channel= 0){
		$this->_statis->setRequsetParam($height, $width, $type, $cooltype, 1, $id, $cpid, $url, $channel);
		$this->_setStatisParam();
	
		$sql = $this->_statis->getInsertDownloadStatisSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("ReqStatis::recordDownloadRequest():executeSql() sql: ".$sql." failed", "log");
// 			return false;
		}
//  		$arrRecord = $this->_statis->getInsertDownloadStatisMongo();
//  		$this->_mongo->insert($arrRecord['table'], $arrRecord['record']);
		return true;
	}
	
/**
 * 广告位统计
 * @return boolean
 */	
	public function recordCoverRequest(){
		$this->_setStatisParam();
		$sql = $this->_statis->getInsertCoverStatisSql();
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("ReqStatis::recordCoverRequest():executeSql() sql: ".$sql." failed", "log");
// 			return false;
		}
// 		$arrRecord = $this->_statis->getInsertCoverStatisMongo();
// 		$this->_mongo->insert($arrRecord['table'], $arrRecord['record']);
		return true;
	}	
/**
 * 广告位列表统计
 * @param unknown_type $adid
 * @return boolean
 */
	public function recordCoverListRequest($adid){
		$this->_setStatisParam();
		$sql = $this->_statis->getInsertCoverListStatisSql($adid);
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("ReqStatis::recordCoverListRequest():executeSql() sql: ".$sql." failed", "log");
		//	return false;
		}
// 		$arrRecord = $this->_statis->getInsertCoverListStatisMongo();
// 		$this->_mongo->insert($arrRecord['table'], $arrRecord['record']);
		return true;
	}

/**
 * 壁纸、主题、铃声的应用统计
 * @return boolean
 */	
	public function recordApply($height, $width, $type, $cooltype, $id, $cpid = ''){
		$this->_statis->setRequsetParam($height, $width, $type, $cooltype, 1, $id, $cpid);
		$this->_setStatisParam();
		
		$sql = $this->_statis->getInsertApplyStatisSql();
		
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("DownloadStatis::recordApply():executeSql() sql:".$sql." error", "log");
		//	return false;
		}
// 		$arrRecord = $this->_statis->getInsertApplyStatisMongo();
// 		$this->_mongo->insert($arrRecord['table'], $arrRecord['record']);
		return true;
	} 
}