<?php
require_once 'public/public.php';
require_once 'tasks/statis/StatisSql.sql.php';
require_once 'tasks/statis/StatisFactory.class.php';

class Statis{
	private $_id;		//统计对象ID
	private $_cpid;		//同一张壁纸、主题、字体等，不同分辨率的相同ID
	private $_url;		//下载的URL
	private $_type;		//类型：1、美女、建筑、景物,应用：待机、壁纸、封面
	private $_ip;		//终端IP
	private $_cooltype;	//1、主题2、壁纸3、铃声
	private $_height;	//分辨率高
	private $_width;	//分辨率宽
	private $_session;	//sesiong；
	private $_imeid;	//imeid
	private $_imei;		//imei
	private $_imsi;		//imsi
	private $_net;		//网络类型2G/3G/WIFI
	private $_kernel;	//应用内核版本
	private $_channel;	//资源下载渠道
	private $_product;	//产品名称
	private $_vercode;	//产品名称

	function __construct(){
		$this->_id		= '';
		$this->_cpid	= '';
		$this->_type	= 0;
		$this->_ip		= '';
		$this->_cooltype = 0;
		$this->_height	 = 0;
		$this->_width	 = 0;
		$this->_session  = '';
		$this->_imeid	 = '';
		$this->_imei	 = '';
		$this->_imsi	 = '';
		$this->_net		 = '';
		$this->_kernel	 = '';
		$this->_channel	 = 0;
		$this->_vercode	 = 0;
	}

	public function setStatisParam($ip,$session,
							$product, $imeid, $imei, $imsi,
							$type, $cooltype,
							$height, $width){
		$this->_type	 = (int)($type);
		$this->_ip		 = $ip;
		$this->_cooltype = (int)($cooltype);
		$this->_height	 = (int)($height);
		$this->_width	 = (int)($width);
		$this->_session  = $session;
		$this->_imeid	 = $imeid;
		$this->_imei	 = $imei;
		$this->_imsi	 = $imsi;
		$this->_product  = $product;
	}
	/**
	 * 设置浏览参数
	 * @param unknown_type $height
	 * @param unknown_type $width
	 * @param unknown_type $type
	 * @param unknown_type $cooltype
	 * @param unknown_type $id
	 */
	public function setRequsetParam($height, $width, $type, $cooltype, 
									$kernel = 1, $id = '', $cpid = '', $url = '', $channel = 0, 
									$vercode = 0){
		$this->_id	 	 = $id;
		$this->_cpid 	 = $cpid;
		$this->_url	 	 = '';//$url;
		$this->_type	 = $type;
		$this->_cooltype = $cooltype;
		$this->_height	 = (int)($height);
		$this->_width	 = (int)($width);
		$this->_kernel	 = (int)($kernel);
		$this->_channel	 = (int)($channel);
		$this->_vercode	 = (int)($vercode);
	}
	/**
	 * 设置访问终端的信息
	 * @param unknown_type $ip
	 * @param unknown_type $session
	 * @param unknown_type $product
	 * @param unknown_type $imeid
	 * @param unknown_type $imei
	 * @param unknown_type $imsi
	 * @param unknown_type $height
	 * @param unknown_type $width
	 */
	public function setTerminalParam($ip, $session,
					  				 $product, $imeid, $imei, $imsi, 
									 $height, $width, $net){
		$this->_ip 		 = $ip;
		$this->_session  = $session;
		$this->_product  = $product;
		$this->_imeid	 = $imeid;
		$this->_imei	 = $imei;
		$this->_imsi	 = $imsi;
		$this->_height	 = (int)($height);
		$this->_width	 = (int)($width);
		$this->_net		 = $net;
	}

	public function checkParam()
	{
		$this->_product = sql_check_str($this->_product, 30);
		$this->_imeid 	= sql_check_str($this->_imeid, 50);
		$this->_imei 	= sql_check_str($this->_imei, 50);
		$this->_imsi 	= sql_check_str($this->_imsi, 50);
		$this->_net 	= sql_check_str($this->_net, 20);
		$this->_id 		= sql_check_str($this->_id, 64);
		$this->_cpid 	= sql_check_str($this->_cpid, 64);
	}
	
	public function getInsertReqStatisSql(){
		$sql = "";
		$table = StatisFactory::getReqRecodTable($this->_cooltype);
		$sql = sprintf(SQL_INSERT_REQ_RECORD, $table,
							$this->_ip, $this->_session,
							$this->_product, $this->_cooltype, $this->_kernel, $this->_type,
							$this->_height, $this->_width,
							$this->_imei, $this->_imsi,$this->_imeid,	
							$this->_net, $this->_channel,		
							$this->_vercode,			 
							date("Y-m-d H:i:s"));
		return $sql;
	}
	
	public function getInsertReqStatisMongo(){
		$table = StatisFactory::getReqRecodTable($this->_cooltype);
		return array('table'=> $table,
				 	'record'=>array('ip'=>$this->_ip,
									 'session'  => $this->_session,
									 'product'  => $this->_product, 
									 'cooltype' => (int)$this->_cooltype,
									 'kernel' 	=> (int)$this->_kernel, 
									 'type' 	=> (int)$this->_type,
									 'height'	=> (int)$this->_height, 
									 'width'	=> (int)$this->_width,
									 'imei'		=> $this->_imei, 
									 'imsi' 	=> $this->_imsi,
									 'meid'		=> $this->_imeid,
									 'net'		=> $this->_net,
				 					 'channel'	=> $this->_channel,
				 					 'vercode'	=> $this->_vercode,
									 'insert_time' => date("Y-m-d H:i:s")));
	}

	public function getInsertDownloadStatisSql(){
		$sql = "";
		$table = StatisFactory::getDlRecodTable($this->_cooltype);
		$sql = sprintf(SQL_INSERT_DL_RECORD, $table, 
							$this->_ip, $this->_session, $this->_id, $this->_cpid, $this->_product, 
							$this->_imeid, $this->_imei, $this->_imsi, $this->_type, $this->_cooltype, $this->_channel,
							$this->_height, $this->_width, $this->_url,
							$this->_net,
							date('Y-m-d H:i:s'));
		return $sql;
	}
	
	public function getInsertDownloadStatisMongo(){
		$table = StatisFactory::getDlRecodTable($this->_cooltype);
		return array('table'=> $table,
					 'record'=>array('ip'=>$this->_ip,
									'session'  => $this->_session,
									'id' 	   => $this->_id, 
									'cpid' 	   => $this->_cpid,
									'product'  => $this->_product,
									'cooltype' => (int)$this->_cooltype,
									'type' 	   => (int)$this->_type,
									'channel'  => (int)$this->_channel,
									'height'   => (int)$this->_height,
									'width'	   => (int)$this->_width,
									'imei'	   => $this->_imei,
									'imsi' 		=> $this->_imsi,
									'meid'		=> $this->_imeid,
									'net'		=> $this->_net,
									'insert_time' => date("Y-m-d H:i:s")));
	}
	
	public function getInsertBrowseStatisSql(){
		$sql = "";
		$table = StatisFactory::getBrRecodTable($this->_cooltype);
		$sql = sprintf(SQL_INSERT_BROWSE_RECORD, $table, 
							$this->_ip, $this->_session, $this->_id, $this->_cpid, $this->_product, 
							$this->_imeid, $this->_imei, $this->_imsi, $this->_type, $this->_cooltype, $this->_channel,
							$this->_height, $this->_width, $this->_url,	
							$this->_net,	
							date('Y-m-d H:i:s'));
		return $sql;
	}
	
	public function getInsertBrowseStatisMongo(){
		$table = StatisFactory::getBrRecodTable($this->_cooltype);
		return array('table'=> $table,
					 'record'=>array('ip'=>$this->_ip,
									'session'  => $this->_session,
									'id' 	   => $this->_id,
									'cpid' 	   => $this->_cpid,
									'product'  => $this->_product,
									'cooltype' => (int)$this->_cooltype,
									'type' 	   => (int)$this->_type,
									'channel' 	   => (int)$this->_channel,
									'height'   => (int)$this->_height,
									'width'	   => (int)$this->_width,
									'imei'	   => $this->_imei,
									'imsi' 		=> $this->_imsi,
									'meid'		=> $this->_imeid,
									'net'		=> $this->_net,
									'insert_time' => date("Y-m-d H:i:s")));
	}
	
	public function getInsertApplyStatisSql(){
		$sql = "";
		$table = StatisFactory::getApplyRecodTable($this->_cooltype);
		$sql = sprintf(SQL_INSERT_APPLY_RECORD, $table,
												$this->_ip, $this->_session, 
												$this->_id, $this->_cpid, 
												$this->_product, $this->_imeid, $this->_imei, $this->_imsi, 
												$this->_type, $this->_cooltype,	
												$this->_height, $this->_width, 	
												$this->_net,	
												date("Y-m-d H:i:s"));
		return $sql;
	}
	
	public function getInsertApplyStatisMongo(){
		$table = StatisFactory::getApplyRecodTable($this->_cooltype);
		return array('table'=> $table,
					 'record'=>array('ip'=>$this->_ip,
								'session'  => $this->_session,
								'id' 	   => $this->_id,
								'cpid' 	   => $this->_cpid,
								'product'  => $this->_product,
								'cooltype' => (int)$this->_cooltype,
								'type' 	   => (int)$this->_type,
								'height'   => (int)$this->_height,
								'width'	   => (int)$this->_width,
								'imei'	   => $this->_imei,
								'imsi' 		=> $this->_imsi,
								'meid'		=> $this->_imeid,
								'net'		=> $this->_net,
								'insert_time' => date("Y-m-d H:i:s")));
	}
	
	public function getInsertCoverStatisSql(){
		$sql = "";
		$sql = sprintf(SQL_INSERT_COVER_RECORD, $this->_ip, $this->_session,
							$this->_product, $this->_imeid, $this->_imei, $this->_imsi,
							$this->_height, $this->_width,
							$this->_net,
							date("Y-m-d H:i:s"));
		return $sql;
	}
	
	public function getInsertCoverStatisMongo(){
		$table = 'tb_yl_adwp_cover_req_record';
		return array('table'=> $table,
					'record'=>array('ip'=>$this->_ip,
								'session'  => $this->_session,
								'product'  => $this->_product,
								'height'   => (int)$this->_height,
								'width'	   => (int)$this->_width,
								'imei'	   => $this->_imei,
								'imsi' 		=> $this->_imsi,
								'meid'		=> $this->_imeid,
								'net'		=> $this->_net,
								'insert_time' => date("Y-m-d H:i:s")));
	}
	
	public function getInsertCoverListStatisSql($adid){
		$sql = "";
		$sql = sprintf(SQL_INSERT_COVER_LIST_RECORD, $adid, 
							$this->_ip, $this->_session,
							$this->_product, $this->_imeid, $this->_imei, $this->_imsi,
							$this->_height, $this->_width,
							$this->_net,
							date("Y-m-d H:i:s"));
		return $sql;
	}
	
	public function getInsertCoverListStatisMongo(){
		$table = 'tb_yl_adwp_cover_list_req_record';
		return array('table'=> $table,
					'record'=>array('ip'=>$this->_ip,
							'session'  => $this->_session,
							'product'  => $this->_product,
							'height'   => (int)$this->_height,
							'width'	   => (int)$this->_width,
							'imei'	   => $this->_imei,
							'imsi' 		=> $this->_imsi,
							'meid'		=> $this->_imeid,
							'net'		=> $this->_net,
							'insert_time' => date("Y-m-d H:i:s")));
	}
}