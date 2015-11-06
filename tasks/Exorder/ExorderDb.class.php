<?php
require_once 'tasks/Exorder/Exorder.class.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/WriteLog.lib.php';

class ExorderDb extends DBManager
{
	const YL_EXORDER_TABLE = 'tb_yl_exorder';
	
	public function __construct()
	{
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['coolshow']);
	}
	
	public function createExorder($nType)
	{
		try{
			$this->lockTable(self::YL_EXORDER_TABLE);
			
			$exorder = $this->getExorder($nType);
			if($exorder === false){
				Log::write('ExorderDb::createExorder():getExorder() failed', 'log');
				$this->unlockTables();
				return false;
			}
				
			$strNow =  date("Ymd");
			$nExorder = 1;
			if(!$exorder){
				$exorder = new Exorder();
				$exorder->setName($nType);
				$bResult = $this->saveExorder($strNow, $nType, $nExorder);
				if(!$bResult){
					Log::write('ExorderDb::createExorder():saveExorder() failed', 'log');
					$this->unlockTables();
					return false;
				}
			}else{
// 				Log::write('ExorderDb::createExorder():saveExorder()'.$exorder->strDate, 'log');
// 				if ($exorder->strDate == $strNow){
// 					$nExorder = $exorder->nExorder + 1;
// 				}
// 				Log::write('ExorderDb::createExorder():updateExorder()'.$nExorder, 'log');
				
				if ($exorder->nExorder < 999999){//一天不可超过999999个订单，不然会出错，如果一天订单量到了这个级别，需要 调整数据，确认订单号位数是否够 
					$nExorder = $exorder->nExorder + 1;
				}
					
				$bResult = $this->updateExorder($strNow, $nType, $nExorder);
				if(!$bResult){
					Log::write('ExorderDb::createExorder():updateExorder() failed', 'log');
					$this->unlockTables();
					return false;
				}
			}
			$this->unlockTables();
			
			$strExorder = $exorder->getNewExorder();
		}catch(Exception $e){
			Logs::write('ExorderDb::createExorder() error:'.$e->getMessage(), 'log');
			return false;
		}
		return $strExorder;
	}	
	
	public function getExorder($nType){
		try {
			$sql = Exorder::getSelectExorderSql($nType);
			$rows = $this->executeQuery($sql);
			if($rows === false){
				Log::write('ExorderDb::getExorder():executeQuery() failed, SQL:'.$sql, 'log');
				return false;
			}
			
			$exorder = null;
			foreach ($rows as $row){
				$exorder = new Exorder();
				$exorder->setExorderByDb($row);
				$exorder->setName($nType);
			}
			
			return $exorder;
		}catch(Exception $e){
			Log::write('ExorderDb::getExorder() error:'.$e->getMessage(), 'log');
			return false;
		}
		return $rows;
	}
	
	public function updateExorder($strDate, $nType, $nExorder){
		try{
			$sql = Exorder::getUpdateExorderSql($strDate, $nType, $nExorder);
			$bResult = $this->executeSql($sql);
			if(!$bResult){
				Log::write('ExorderDb::updateExorder():executeSql() failed', 'log');
				return false;
			}
			return true;	
		}catch(Exception $e){
			Log::write('ExorderDb::updateExorder() exception, err:'.$e->getMessage(), 'log');
			return false;
		}
		return true;
	}
	
	public function saveExorder($strDate, $nType, $nExorder){
		try{
			$sql = Exorder::getInsertExorderSql($strDate, $nType, $nExorder);
			$bResult = $this->executeSql($sql);
			if(!$bResult){
				Log::write('ExorderDb::saveExorder():executeSql() failed', 'log');
				return false;
			}
			return true;
		}catch(Exception $e){
			Log::write('ExorderDb::saveExorder() exception, err:'.$e->getMessage(), 'log');
			return false;
		}
		return true;
	}
}