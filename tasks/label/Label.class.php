<?php
require_once 'configs/config.php';
require_once 'tasks/label/LabelSql.sql.php';
require_once 'tasks/CoolShow/AlarmSql.sql.php';
require_once 'tasks/protocol/LabelProtocol.php';
require_once 'tasks/protocol/AlarmLabelProtocol.php';

class Label
{	
	private $_nType;
	private $_nSubType;
	private $_nWidth;
	private $_nHeight;
	
	public function __construct()
	{
		$this->_nType 		= 0;
		$this->_nSubType	= 0;
		$this->_nWidth 		= '';
		$this->_nHeight 	= '';
	}
	
	public function setLabelParam($nType, $nSubType, $nWidth, $nHeight)
	{
		$this->_nType 		= $nType;
		$this->_nSubType	= $nSubType;
		$this->_nWidth 		= $nWidth;
		$this->_nHeight 	= $nHeight;
	}
	
	private function _getTable()
	{
		$table = '';
		switch ($this->_nType){
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$table = 'tb_yl_adwp_label';
			}break;
			case COOLXIU_TYPE_RING:{
				$table = 'tb_yl_ring_label';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$table = 'tb_yl_theme_label';
			}break;
			case COOLXIU_TYPE_ALARM:{
				$table = 'tb_yl_alarm_label';
			}break;
		}
		return $table;
	}
	
	private function _getCondition()
	{
		$condition = '';
		switch ($this->_nType){
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$condition = ' AND special = 0 ';
				if($this->_nWidth >= 1080 || $this->_nWidth >= 1920){
					$condition = ''; // AND special = 1 
				}
			}break;
			case COOLXIU_TYPE_RING:{
				$condition = sprintf(' AND msubtype = %d', $this->_nSubType);
			}break;
		}
		return $condition;
	}
	
	public function getSelectLabelSql($start = 0, $limit = 100)
	{
		if($this->_nType == COOLXIU_TYPE_ALARM){
			$sql = sprintf(SQL_SELECT_ALARM_LABEL, $start, $limit);
			return $sql;
		}
		$table = $this->_getTable();
		$condition = $this->_getCondition();
		$sql = sprintf(SQL_SELECT_LABEL, $table, $condition);
		return $sql;
	}
	
	public function getProtocol()
	{
		$label = new LabelProtocol();
		if($this->_nType == COOLXIU_TYPE_ALARM){
			$label =  new AlarmLabelProtocol();
		}
		return $label;
	}
}