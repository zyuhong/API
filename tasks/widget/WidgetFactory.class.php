<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'tasks/widget/Widget.class.php';
require_once 'tasks/widget/WidgetThemes.class.php';
require_once 'tasks/widget/WidgetWallpaper.class.php';

require_once 'tasks/widget/WidgetDb.class.php';
require_once 'tasks/widget/WidgetWpDb.class.php';
require_once 'tasks/protocol/Protocol.php';
require_once 'tasks/protocol/ThemesProtocol.php';
require_once 'tasks/protocol/WallpaperProtocol.php';
class WidgetFactory
{
	private $_widget;
	public function __construct()
	{
		$this->_widget = null;
	}
	
	public function setWidget($type, $width, $height, $kernel)
	{
		switch($type){
			case Widget::YL_COOLSHOW_THEME:{
				$this->_widget = new WidgetThemes();
			}break;
			case Widget::YL_COOLSHOW_WALLPAPER:{
				$this->_widget = new WidgetWallpaper(); 
			}break;
			default:{
				Log::write('WidgetFactory()::getWidget() type is not exist', 'log');
				return false;
			}
		}
		$this->_widget->setWidget($width, $height, $kernel);
		return $this->_widget;
	}
	
	public function getThemesProtocol($rows)
	{
		$arrThems = array();
		foreach($rows as $row){
			if(!array_key_exists($row['identity'], $arrThems)){
				$theme = new ThemesProtocol();
				$theme->setProtocol($row, REQUEST_CHANNEL_WIDGET);
				if(!isset($row['widgetid']) || !isset($row['cpid'])){
					return false;
				}
				global $g_arr_host_config;
				$url = $g_arr_host_config['cdnhost'].$row['turl'];
 				$widgeturl = $this->_widget->getWidgetUrl($row['widgetid'], $row['cpid'], $url);
				$theme->setWidgetUrl($widgeturl);
				
				$arrThems = $arrThems + array($row['identity'] => $theme);
			}
			$prev = new PrevProtocol();
		
			if((int)$row['prev_type'] == 1){
				$arrThems[$row['identity']]->setMainPrev($row);
			}
		
			$prev->setPrev($row);
			$arrThems[$row['identity']]->pushPrevImg($prev);
		}
		return $arrThems;
	}
	
	public function getWallpaperProtocol($rows)
	{
		$arrAndroidWp = array();
		foreach ($rows as $row){
			$wp_protocol = new WallpaperProtocol() ;
			$wp_protocol->setWallpaperType(0);
			$wp_protocol->setWallpaperRatio($this->_widget->width, $this->_widget->height);
			$wp_protocol->setAndroideskWallpaper($row, Protocol::YL_DOWNLOAD_CHANNEL_WIDGET);
			
			$widgeturl = $this->_widget->getWidgetUrl($row['id'], $row['cpid'], $row['small_url']);
			$wp_protocol->setWidgetUrl($widgeturl);
			
			array_push($arrAndroidWp, $wp_protocol);
		}
		return $arrAndroidWp;
	}
	
	public function getWallpaperTagProtocol($rows)
	{
		$arrAndroidWp = array();
		$tagUrl = '';
		$tagName = '';
		if (count($rows) > 0){
			$tagName = $rows[0]['name'];
			$url = $this->_widget->getWidgetUrl($rows[0]['id'], $rows[0]['cpid'], $rows[0]['small_url']);
			global $g_arr_host_config;
			$tagUrl = $g_arr_host_config['host'].$url;
		}
		$arrAndroidWp = $this->getWallpaperProtocol($rows);
		$arrWpTag = array(
						'tag'=>true,
						'tagName'=>$tagName,
						'tagUrl'=>$tagUrl,
						'count'=>count($arrAndroidWp),
						'wallpapers'=>$arrAndroidWp,
				);
		$arrWpTagProtocol = array();
		array_push($arrWpTagProtocol, $arrWpTag);
		return $arrWpTagProtocol;	
	}
	
	public function getWidgeMemSql($vercode = 0)
	{
		if(!$this->_widget){
			Log::write('WidgetFactory()::getWidgeMemSql() _widget is null', 'log');
			return false;
		}
		$sql = '';
		switch($this->_widget->type){
			case Widget::YL_COOLSHOW_THEME:{
				$sql = $this->_widget->getSelectWidgetSql($vercode);
			}break;
			case Widget::YL_COOLSHOW_WALLPAPER:{
				$sql = $this->_widget->getSelectWidgetSizeTagSql();
			}break;
			default:{
				Log::write('WidgetFactory()::getWidgeMemSql() type is not exist', 'log');
				return false;
			}
		}
		return  $sql;
	}
	
	public function getWidgetProtocol()
	{
		if(!$this->_widget){
			Log::write('WidgetFactory()::getWidgetProtocol() _widget is null', 'log');
			return false;
		}	
		$arrWidget = array();
		
		switch($this->_widget->type){
			case Widget::YL_COOLSHOW_THEME:{
				$widgetDb = new WidgetDb();
				$arrThemes = $widgetDb->getThemes($this->_widget);
				if(!$arrThemes){
					Log::write('WidgetFactory()::getWidgetProtocol():getThemes() failed', 'log');
					return false;
				}
				
				$arrThemeProtocol = $this->getThemesProtocol($arrThemes);
				if(!$arrThemeProtocol){
					Log::write('WidgetFactory()::getWidgetProtocol():getThemesProtocol() failed', 'log');
					return false;
				}
				foreach ($arrThemeProtocol as $key=>$theme){
					array_push($arrWidget, $theme);
				}
				$result =  array(
						'result'=>true,
						'count' => count($arrWidget),
						$this->_widget->name=>$arrWidget
				);
			}break;
			case Widget::YL_COOLSHOW_WALLPAPER:{
				$wpdb = new WidgetWpDb();
				$bResult = $wpdb->setWpSizeTag($this->_widget);
				if(!$bResult ){
					Log::write('WidgetFactory()::getWidgetProtocol():setWpSizeTag() failed', 'log');
					return false;
				}
				$resolution = explode('_', $wpdb->getSizeRes());
				$this->_widget->width	= $resolution[1];
				$this->_widget->height	= $resolution[2];
				
				$arrTag = $wpdb->getWidgetWpTag($this->_widget);
				$num = 1;
				if(count($arrTag)<=0){
					$num = 2;
				}
				$arrTagProtocol = $this->getWallpaperTagProtocol($arrTag);
				
				$arrWp = $wpdb->getWidgetWp($this->_widget, 0, $num);
				$arrWpProtocol = $this->getWallpaperProtocol($arrWp);
				
				$result =  array('result'=>true, 
								 'count' => count($arrWpProtocol),
								 'tag'=>$arrTagProtocol,
								 'wallpapers' => $arrWpProtocol);
			}break;
			default:{
				Log::write('WidgetFactory()::getWidgetProtocol() type is not exist', 'log');
				return false;
			}
		}
		return $result;
	}
}