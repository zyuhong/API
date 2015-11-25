<?php
require_once 'public/public.php';

defined("SQL_CHECK_MOBILE_EXORDER")
	or define("SQL_CHECK_MOBILE_EXORDER", "SELECT COUNT(*) FROM tb_yl_exorder_record " 
										." WHERE status = 1 AND %s "); //AND cooltype = %d AND product = '%s' identity = '%s' AND
	
defined("SQL_INSERT_MOBILE_EXORDER")
	or define("SQL_INSERT_MOBILE_EXORDER", "INSERT INTO tb_yl_exorder_record "
										." (exorder, status, channel, count, ruleid, score, appid, waresid, money, "
										." cooltype, identity, cpid, name, userid, author, type, " 
										." product, meid, cyid, imsi, net, vercode, kernel, insert_time)"
										." VALUES ('%s', 0, '%s', 1, '%s', %d, '%s',  '%s', %d, "
										." %d,  '%s', '%s', '%s', '%s', '%s',  %d, "
										." '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s')");
	
defined("SQL_UPDATE_MOBILE_EXORDER")
	or define("SQL_UPDATE_MOBILE_EXORDER", "UPDATE tb_yl_exorder_record SET status = 1, isscore = %d, update_time= '%s' WHERE exorder = '%s' ");
	
defined("SQL_INSERT_MOBILE_EXORDER_CHARGE")
	or define("SQL_INSERT_MOBILE_EXORDER_CHARGE", "INSERT INTO tb_yl_charge_record (exorder, product, meid, imei, imsi, insert_time)"
										." VALUES ('%s', '%s', '%s', '%s', '%s', '%s')");

defined("SQL_SELECT_EXORDER_BY_ID")
	or define("SQL_SELECT_EXORDER_BY_ID", "SELECT * FROM tb_yl_exorder_record WHERE exorder = '%s' ");	
	
defined("SQL_INSERT_CHARGE_RECORD")
or define("SQL_INSERT_CHARGE_RECORD", "INSERT INTO tb_yl_charge_record "
    ." (exorder, cooltype, identity, cpid, cyid, insert_time)"
    ." VALUES ('%s', %d, '%s',  '%s', '%s', '%s')");

defined("SQL_SELECT_CHARGE_BY_CYID")
	or define("SQL_SELECT_CHARGE_BY_CYID", "SELECT cpid, insert_time FROM tb_yl_charge_record WHERE cyid = '%s' AND cooltype = %d AND cpid != 0 GROUP BY cpid limit %d, %d ");

defined("SQL_SELECT_FREE_RECORD")
    or define("SQL_SELECT_FREE_RECORD", "SELECT count(1) FROM tb_yl_charge_record WHERE cyid = '%s' AND cooltype = %d AND id='%s' AND cpid='%d' ");
	
class ExorderRecord
{	
	public function __construct()
	{
	}
	
	static public function getCheckMobileChargedSql($strProduct, $nCoolType, $strId, $strCpid, $strMeid, $strImsi, $strCyid = '')
	{
		$strFiledCondition = ''; 
		if(!empty($strMeid)){
			$strMeid = sql_check_str($strMeid, 50);
			$strFiledCondition = " ( meid = '".$strMeid."'";
		}else if(!empty($strImsi)){
			$strImsi = sql_check_str($strImsi, 50);
			$strFiledCondition = " ( imsi = '".$strImsi."'";
		}
		
		if(empty($strFiledCondition)){
			return false;
		}
		
		if(!empty($strCyid)){
			$strCyid = sql_check_str($strCyid, 50);
			$strFiledCondition .= " OR cyid = '".$strCyid."' ) ";
		}else{
			$strFiledCondition .= " ) ";
		}
		
		#兼容旧版本只上报了ID，带有已购功能的要用CPID 20150804
		$strTemp = sprintf("AND identity = '%s' ", $strId);
		if(empty($strCpid)){
			$strTemp = sprintf(" AND identity = '%s' ", $strId);
		}else{
			$strTemp = sprintf(" AND cpid = '%s' ", $strCpid);
		}
		
		$strFiledCondition .= $strTemp;
		
		$strProduct = sql_check_str($strProduct, 30);			
		$sql = sprintf(SQL_CHECK_MOBILE_EXORDER, $strFiledCondition);//$nCoolType, $strProduct,
		return $sql;
	}
	
	static public function getInsertMobileExorderSql($nCoolType, $strExorder, $ruleid, $score,
									$strId, $cpid, $name, $userid, $author, $type,
									$appid, $waresid, $money,
									$strProduct, $strMeid, $strCyid, $strImsi, $strNet,
									$strVercode, $kernel, $channel = 'yx')
	{	
		$strProduct = sql_check_str($strProduct, 30);
		$strMeid = sql_check_str($strMeid, 50);
		$strImsi = sql_check_str($strImsi, 50);
		$strExorder = sql_check_str($strExorder, 30);
		$sql = sprintf(SQL_INSERT_MOBILE_EXORDER, $strExorder, $channel, $ruleid, $score, $appid, $waresid, $money,
												  $nCoolType, $strId, $cpid, $name, $userid,  $author, $type,
												  $strProduct, $strMeid, $strCyid, $strImsi, $strNet, $strVercode, $kernel,
												  date('Y-m-d H:i:s'));
		return $sql;
	}	
	
	static public function getUpdateMobileExorderSql($strExorder, $isScore = 0)
	{
		$strExorder = sql_check_str($strExorder, 30);
		$sql = sprintf(SQL_UPDATE_MOBILE_EXORDER, $isScore, date('Y-m-d H:i:s'), $strExorder);
		return $sql;
	}
	
	static public function getInsertMobileExorderChargeSql($strExorder, $strProduct, $strMeid, $strImei, $strImsi = '')
	{
		$strProduct = sql_check_str($strProduct, 30);
		$strMeid = sql_check_str($strMeid, 50);
		$strImsi = sql_check_str($strImsi, 50);
		$strExorder = sql_check_str($strExorder, 30);
		$sql = sprintf(SQL_INSERT_MOBILE_EXORDER_CHARGE, $strExorder, $strProduct, $strMeid, $strImei, $strImsi, date('Y-m-d H:i:s'));
		return $sql;
	}
	
	static public function getSelectExorderByIdSql($strExorder)
	{
		$strExorder = sql_check_str($strExorder, 30);
		$sql = sprintf(SQL_SELECT_EXORDER_BY_ID, $strExorder);
		return $sql;
	}

	static public function getInsertChargeSql($strExorder, $strCyid, $nCoolType, $strId, $strCpid)
	{
		$strExorder = sql_check_str($strExorder, 30);
// 		$strCyid = sql_check_str($strCyid, 50);
		$sql = sprintf(SQL_INSERT_CHARGE_RECORD, $strExorder, $nCoolType, $strId, $strCpid, $strCyid, date('Y-m-d H:i:s'));
		return $sql;
	}

    static public function getCheckFreeRecordSql($strId, $strCpid, $nCoolType, $strCyid)
    {
        $sql = sprintf(SQL_SELECT_FREE_RECORD, $strCyid, $nCoolType, $strId, $strCpid);
        return $sql;
    }

	static public function getSelectChargeByCyidSql($strCyid, $nCoolType, $start, $limit)
	{
		$sql = sprintf(SQL_SELECT_CHARGE_BY_CYID, $strCyid, $nCoolType, $start, $limit);
		return $sql;
	}
	
}