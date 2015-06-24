<?php
require_once 'configs/config.php';

defined("SQL_SELECT_EXORDER_BY_TYPE")
	or define("SQL_SELECT_EXORDER_BY_TYPE", "SELECT * FROM tb_yl_exorder WHERE type = %d ");

defined("SQL_INSERT_EXORDER")
	or define("SQL_INSERT_EXORDER", "INSERT INTO tb_yl_exorder(date, type, exorder) VALUES('%s', %d, %d) ");

defined("SQL_UPDATE_EXORDER")
	or define("SQL_UPDATE_EXORDER", "UPDATE tb_yl_exorder SET date='%s', exorder = %d WHERE type= %d ");

class Exorder
{
	public $strName;
	public $nExorder;
	public $nType;
	public $strDate;
	
	public function __construct()
	{
		$this->strName  = '';
		$this->nExorder	= 0;
		$this->nType	= 0;
		$this->strDate  = '';
	}
	
	public function setName($nType)
	{
		$strName = '';
		switch ($nType){
			case COOLXIU_TYPE_THEMES_CONTACT:
			case COOLXIU_TYPE_THEMES_ICON:
			case COOLXIU_TYPE_THEMES_MMS:
			case COOLXIU_TYPE_THEMES:
				$strName = 'TH';break;
			case COOLXIU_TYPE_RING:
				$strName = 'RG';break;
			case COOLXIU_TYPE_FONT:
				$strName = 'FT';break;
			case COOLXIU_TYPE_SCENE:
				$strName = 'SC';break;
		}

		$this->nType   = $nType;
		$this->strName = $strName;
	}
	public function setExorder($nExorder)
	{
		$this->nExorder	= $nExorder;
	}
	
	public function setExorderByDb($row)
	{
// 		$this->nType	= (int)isset($row['type'])?$row['type']:0;
		$this->nExorder	= (int)isset($row['exorder'])?$row['exorder']:0;
		$this->strDate  = isset($row['date'])?$row['date']:'';
		
// 		$this->setName($this->nType);
	}
	
	public function getNewExorder(){
		$this->nExorder	 = $this->nExorder + 1;
		$num 			 = sprintf("%06d", $this->nExorder);
		$strDate		 = date('Ymd');
		$strExorder		 = 'CS'.$this->strName.$strDate.$num;
		return $strExorder;
	}
	
	static public function getSelectExorderSql($nType)
	{
		$sql = sprintf(SQL_SELECT_EXORDER_BY_TYPE, $nType);
		return $sql;
	}
	
	static public function getInsertExorderSql($strDate, $nType, $nExorder)
	{
		$sql = sprintf(SQL_INSERT_EXORDER, $strDate, $nType, $nExorder);
		return $sql;
	}
	
	static public function getUpdateExorderSql($strDate, $nType, $nExorder)
	{
		$sql = sprintf(SQL_UPDATE_EXORDER, $strDate, $nExorder, $nType);
		return $sql;
	}

}