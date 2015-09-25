<?php
require_once 'configs/config.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'tasks/charge/ChargeRecord.class.php';

class ChargeDb extends DBManager
{
	private $_chargeRecord; 
	public function __construct()
	{
		$this->_chargeRecord = new ChargeRecord();
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['coolshow_charge_record']);
	}
	
	public function setChargeRecord($arrChargeRecord, $strSign)
	{
		$this->_chargeRecord->setChargeRecord($arrChargeRecord);
		$this->_chargeRecord->setSign($strSign);
	}
	
	public function setNChargeRecord($arrChargeRecord)
	{
		$this->_chargeRecord->setNChargeRecord($arrChargeRecord);
	}
	
	public function setQikuChargeRecord($arrChargeRecord)
	{
		$this->_chargeRecord->setQikuChargeRecord($arrChargeRecord);
	}
	
	public function recordCharge()
	{
		$bResult = $this->_chargeRecord->checkChargeRecord();
		if(!$bResult){
			Log::write('CharegeDb::recordChare():checkChargeRecord() failed', 'log');
			return false;
		}
		
		$sql = $this->_chargeRecord->getInsertSql();
		$bResult = $this->executeSql($sql);
		if(!$bResult){
			Log::write('CharegeDb::recordChare():executeSql() failed', 'log');
			return false;
		}
		
		return true;
	}
	
	public function recordNCharge()
	{
		$bResult = $this->_chargeRecord->checkChargeRecord();
		if(!$bResult){
			Log::write('CharegeDb::recordNCharge():checkChargeRecord() failed', 'log');
			return false;
		}
	
		$sql = $this->_chargeRecord->getNInsertSql();
		$bResult = $this->executeSql($sql);
		if(!$bResult){
			Log::write('CharegeDb::recordNCharge():executeSql() failed', 'log');
			return false;
		}
	
		return true;
	}
	
	public function recordQikuCharge()
	{
		$bResult = $this->_chargeRecord->checkChargeRecord();
		if(!$bResult){
			Log::write('CharegeDb::recordNCharge():checkChargeRecord() failed', 'log');
			return false;
		}
	
		$sql = $this->_chargeRecord->getNInsertSql();
		$bResult = $this->executeSql($sql);
		if(!$bResult){
			Log::write('CharegeDb::recordNCharge():executeSql() failed', 'log');
			return false;
		}
	
		return true;
	}
	
	public function getChargeRecode($strCpid, $strExorderNo, $strSign)
	{
		$strChargeRecordHost = $g_arr_host['chargerecod_host'];
		$strData = 'cpid='.$strCpid.'&exorderno='.$strExorderNo.'&sign='.$strSign;
		$result = get_respond_by_url($strChargeRecordHost, $strData);
		
		$jsonCharge 	= stripslashes($result);
		$arrChargeRecord = json_decode($result, true);
		
		if(!isset($arrChargeRecord['retcode'])){
			Log::write('CharegeDb::getChargeRecode() failed result:'.$result, 'log');
			return false;
		}
		
		$nRetcode = $arrChargeRecord['retcode'];
		if($nRetcode == 1){
			Log::write('CharegeDb::getChargeRecode() there is no record', 'log');
			return false;
		}
		
		$nRetcode = $arrChargeRecord['retcode'];
		if($nRetcode == 2){
			Log::write('CharegeDb::getChargeRecode() other error', 'log');
			return false;
		}
		
		$this->_chargeRecord->setChargeRecord($arrChargeRecord);
		$bResult = $this->recordCharge();
		if($bResult){
			Log::write('CharegeDb::getChargeRecode():recordCharge()', 'log');
			return false;
		}
		
		return true;
	}
	
}