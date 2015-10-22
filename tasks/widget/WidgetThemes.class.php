<?php
require_once 'tasks/widget/Widget.class.php';
require_once 'tasks/widget/WidgetSql.sql.php';

class WidgetThemes extends Widget
{
	private $_id;
	private $_cpid;
	private $_fname;
	private $_md5;
	private $_size;
	private $_note;
	private $_insert_time;
	private $_insert_user;
	
	public function __construct(){
		$this->type 	= parent::YL_COOLSHOW_THEME;
		$this->name 	= 'themes';
		$this->_id		= '';
		$this->_cpid	= '';
		$this->_fname	= '';
		$this->_md5		= '';
		$this->_size	= '';
		$this->_note	= '';
		$this->_insert_time = date("Y-m-d H:i:s");
		$this->_insert_user = 'admin'; 
	}
	
	public function setWidgetTheme($id, $cpid, $fname, $width, $height, $url, $md5, $size, $note)
	{
		$this->_id		= $id;
		$this->_cpid	= $cpid;
		$this->_fname	= $fname;
		$this->width	= $width;
		$this->height	= $height;
		$this->widgeturl= $url;
		$this->_md5		= $md5;
		$this->_size	= $size;
		$this->_note	= $note;
	}
	
	public function getSelectWidgetSql($vercode = 0)
	{
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND t.ischarge = 0 '; 
		}
		$sql = sprintf(SQL_SELECT_WIDGET_THEME, $this->kernel, $this->width, $this->height, $strIsCharge);
		return $sql;
	}
	
	public function getInsertWidgetSql()
	{
		$sql = sprintf(SQL_INSERT_WIDGET_THEME, $this->_id, $this->_cpid, 
									$this->_fname, $this->width, $this->height, $this->widgeturl, 
									$this->_note, $this->_size, $this->_md5, 
									$this->_insert_time, $this->_insert_user);
		return $sql;
	}
}