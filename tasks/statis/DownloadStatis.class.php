<?php
require_once 'lib/WriteLog.lib.php';
require_once 'lib/mySql.lib.php';
require_once 'lib/DBManager.lib.php';
require_once 'configs/config.php';
require_once 'tasks/CoolXiu/CoolXiuFactory.class.php';

class DownloadStatis extends DBManager{
	private $_id;						//下载ID
	private $_imeid;						//手机IMEID
	private $_imei;						//手机IMEI
	private $_imsi;						//手机IMSI
	private $_product;					//终端产品型号
	private $_cooltype;					//0:主题 1：壁纸 2：铃声
	private $_applytype;				//壁纸主题应用类型0:主题 1：壁纸 2：铃声
	private $_height;					//分辨率高
	private $_width;					//分辨率宽
	function __construct(){
		$this->_id			= "";
		$this->_imeid		= "";
		$this->_imei		= "";
		$this->_imsi		= "";
		$this->_product		= "";
		$this->_cooltype	= "";
		$this->_applytype   = "";
		$this->_height		= "";
		$this->_width		= "";
		
		$this->connectMySqlCommit();		
	}	
	function setStatisParam($id,  $cooltype, $height, $width){
		$this->setCommonParam();
		$this->_id 			=   $id;
		$this->_cooltype	=	$cooltype;
		$this->_height		=	$height;
		$this->_width		=	$width;
	}
	
	function setApplyParam($id, $cooltype, $applytype){
		$this->setCommonParam();
		$this->_id			= 	$id;
		$this->_cooltype	=	$cooltype;
		$this->_applytype	=	$applytype;
	}
	
	function setCommonParam(){
		$product = (isset($_GET['product']))?$_GET['product']:"";
		$imeid = (isset($_GET['meid']))?$_GET['meid']:"";
		$imei = (isset($_GET['imei']))?$_GET['imei']:"";
		$imsi = (isset($_GET['imsi']))?$_GET['imsi']:"";
		
		$this->_imeid		= 	$imeid;
		$this->_imei		=	$imei;
		$this->_imsi		=	$imsi;
		$this->_product		=	$product;
	}
	
	function recordApply(){
		$statis = new StatisFactory();
		
		$this->setCommonParam();
		$sql = StatisFactory::getInsertApplyRecordSql($this->_id, $this->_product, 
									$this->_imeid, $this->_imei, $this->_imsi, 
									$this->_applytype);
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("DownloadStatis::_InsertApplyRocord():executeSql() sql:".$sql." error", "log");
			return false;
		}
		return true;
	}
	
	private function _InsertApplyRocord(){
		
	}
	
	function recordCommonDownload($object, $id){
		$statis = new StatisFactory();

		$this->setCommonParam();
		$sql = StatisFactory::getInsertRecordSql($object, $id, 
										$this->_product, 
										$this->_imeid, $this->_imei, $this->_imsi);
		$result = $this->executeSql($sql);
		if(!$result){
			Log::write("DownloadStatis::recordCommonDownload():executeSql() sql:".$sql." error", "log");
			return false;
		}
		return true;
	}
}
?>