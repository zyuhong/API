<?php
require_once 'tasks/Exorder/ExorderRecord.class.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';

class ExorderRecordDb extends DBManager
{
	
	public function __construct()
	{
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['coolshow_charge_record']);
	}
	
	public function checkMobileCharged($strProduct, $nCoolType, $strId, $strMeid, $strImsi, $strCyid)
	{
		try{

			$sql = ExorderRecord::getCheckMobileChargedSql($strProduct, $nCoolType, $strId, $strMeid, $strImsi, $strCyid);
			if(!$sql){
				Log::write('ExorderDb::checkMobileExorder() SQL is empty', 'log');
				return false;
			}
			$nCount = $this->executeScan($sql);
			if($nCount === false){
				Log::write('ExorderDb::checkMobileExorder():executeScan() failed, SQL:'.$sql, 'log');
				return false;
			}
			if($nCount > 0)	return true;
			
			return false;
		}catch(Exception $e){
			Log::write('ExorderDb::checkMobileExorder():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
	
	public function updateMobileExorder($strExorder, $isScore)
	{
		try{
			$sql = ExorderRecord::getUpdateMobileExorderSql($strExorder, $isScore);
			$bResult = $this->executeSql($sql);
			if(!$bResult){
				Log::write('ExorderDb::updateMobileExorder():executeSql() failed, SQL:'.$sql, 'log');
				return false;
			}
			return true;
		}catch(Exception $e){
			Log::write('ExorderDb::updateMobileExorder():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
	
	public function saveMobileExorder($nCoolType, $strExorder, $ruleid, $score,
									$strId, $cpid, $name, $userid, $author, $type,
									$appid, $waresid, $money,
									$strProduct, $strMeid, $strCyid, $strImsi, $strNet,
									$strVercode, $kernel)
	{
		try{	
			$sql = ExorderRecord::getInsertMobileExorderSql($nCoolType, $strExorder, $ruleid, $score,
									$strId, $cpid, $name, $userid, $author, $type,
									$appid, $waresid, $money,
									$strProduct, $strMeid, $strCyid, $strImsi, $strNet,
									$strVercode, $kernel);
			$bResult = $this->executeSql($sql);
			if(!$bResult){
				Log::write('ExorderDb::saveMobileExorder():executeSql() failed, SQL:'.$sql, 'log');
				return false;
			}	
			return true;
		}catch(Exception $e){
			Log::write('ExorderDb::saveMobileExorder():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
	
	public function saveMobileExorderCharge($strExorder, $strProduct, $strMeid, $strImei, $strImsi = '')
	{
		try{
			$sql = ExorderRecord::getInsertMobileExorderChargeSql($strExorder, $strProduct, $strMeid, $strImei, $strImsi);
//			Log::write('ExorderDb::saveMobileExorderCharge():executeSql() , SQL:'.$sql, 'log');
				
			$bResult = $this->executeSql($sql);
			if(!$bResult){
				Log::write('ExorderDb::saveMobileExorderCharge():executeSql() failed, SQL:'.$sql, 'log');
				return false;
			}
			return true;
		}catch(Exception $e){
			Log::write('ExorderDb::saveMobileExorderCharge():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
	
	public function getExorderById($strExorder)
	{
		try{
			$sql = ExorderRecord::getSelectExorderByIdSql($strExorder);
			$rows = $this->executeQuery($sql);
			if(!$rows){
				Log::write('ExorderDb::getExorderById():executeSql() failed, SQL:'.$sql, 'log');
				return false;
			}
			foreach ($rows as $row){
				return $row;
			}
			return false;
		}catch(Exception $e){
			Log::write('ExorderDb::getExorderById():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
	
	public function saveChargeRecord($strExorder, $strCyid, $nCoolType, $strId, $strCpid)
	{
		try{
			$sql = ExorderRecord::getInsertChargeSql($strExorder, $strCyid, $nCoolType, $strId, $strCpid);
			$bResult = $this->executeSql($sql);
			if(!$bResult){
				Log::write('ExorderDb::saveChargeRecord():executeSql() failed, SQL:'.$sql, 'log');
				return false;
			}
			return true;
		}catch(Exception $e){
			Log::write('ExorderDb::saveChargeRecord():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
	
	public function getChargeRecord($strCyid, $nCoolType, $start, $limit)
	{
		try{
			$sql = ExorderRecord::getSelectChargeByCyidSql($strCyid, $nCoolType, $start, $limit);
			$rows = $this->executeQuery($sql);
			if($rows === false){
				Log::write('ExorderDb::getChargeRecord():executeSql() failed, SQL:'.$sql, 'log');
				return false;
			}

			return $rows;
		}catch(Exception $e){
			Log::write('ExorderDb::getChargeRecord():exception, error:'.$e->getMessage(), 'log');
			return false;
		}
	}
}