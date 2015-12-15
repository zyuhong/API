<?php
require_once 'tasks/Collect/CollectSql.sql.php';
class Collect
{
	public function __construct()
	{
	}	
	static public function getCheckSql($strCyid, $strAuthorId)
	{
		return sprintf(SQL_CHECK_COLLECT,$strCyid, $strAuthorId);
	}
	
	static public function getInsertSql($strCyid, $strAuthorId, $strAuthorName)
	{
		return sprintf(SQL_INSERT_COLLECT,$strCyid, $strAuthorId, $strAuthorName, date('Y-m-d H:i:s'));
	}

	static public function getUpdateSql($nCollect, $strCyid, $strAuthorId)
	{
		return sprintf(SQL_UPDATE_COLLECT, $nCollect, $strCyid, $strAuthorId);
	}
	
	static public function getSelectSql($strCyid)
	{
		return sprintf(SQL_SELECT_COLLECT, $strCyid);
	}
	
	static public function getSelectDesignerSql($strCyid)
	{
		return sprintf(SQL_SELECT_DESIGNER, $strCyid);
	}

    static public function getSelectCollectStatusSql($strCyid, $strAuthorId)
    {
        return sprintf(SQL_SELECT_COLLECT_STATUS, addslashes($strCyid), addslashes($strAuthorId));
    }
}
