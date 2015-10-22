<?php

defined("SQL_SELECT_WIDGET_THEMES")
	or define("SQL_SELECT_WIDGET_THEMES", "SELECT widget.identity, img.cpid, img.url, img.region "
										." FROM tb_yl_widget widget "
										." LEFT JOIN tb_yl_widget_images img ON img.widgetid = widget.identity "
										." WHERE widget.valid  = 1 AND widget.appname = 'coolshow' AND (img.region = 'widget_theme' OR img.region = 'widget_anim') " 
										." ORDER BY widget.id DESC "
										." LIMIT 0, 3");


class WidgetImg
{
	public $id;
	public $name;
	public $theme;
	public $anim;
	public $details;
	public $download_times;
	
	public function __construct()
	{
		$this->id	 =	'';
		$this->name	 = 	'';
		$this->theme = '';
		$this->anim	 = '';
		$this->download_times =   0;
	}
	
	public function setRegion($strRegion, $strUrl)
	{
		if(strcmp($strRegion, 'widget_theme') == 0){
			$this->theme = $strUrl;
		}
		
		if(strcmp($strRegion, 'widget_anim') == 0){
			$this->anim = $strUrl;
		}
	}
	
	public function setAnim($strAnim)
	{
		$this->anim = $strAnim;
		
	}
	
	public function setWidgetParam($strId, $strName)
	{
		$this->id	 =	$id;
		$this->name	 = 	isset($row['name'])?$row['name']:'';
		$this->theme = isset($row['theme'])?$row['theme']:'';
		$this->anim	 = isset($row['anim'])?$row['anim']:'';
		$this->download_times =   isset($row['download_times'])?$row['download_times']:0;
	}
	
	static public function getSelectWidgetThemes()
	{
		$sql = SQL_SELECT_WIDGET_THEMES;
		return SQL_SELECT_WIDGET_THEMES;
	}
}