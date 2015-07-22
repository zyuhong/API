<?php

require_once 'lib/MongoPHP.lib.php';
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'tasks/Records/Record.class.php';

abstract class MongoRecord
{
	protected $_collection;
	protected $_mongo;
	protected $_db;

	public function __construct()
	{
		$this->_db = '';
		$this->_collection = '';
	}
	
	public function close()
	{
		if(!$this->_mongo){
			return false;
		}
		
		$this->_mongo->close();
	}
	
	public function connect()
	{
		try {
			if(empty($this->_db)){
				return false;
			}
			
			if($this->_mongo){
				return true;
			}
			
			global $g_arr_mongo_db_config;
			$arrMongoServer = $g_arr_mongo_db_config['coolshow_record'];
			$arrMongoServer['db'] = $this->_db;
			$this->_mongo	   = new MongoPHP($arrMongoServer);
			
		}catch (Exception $e){
			Logs::write('MongoRecord::connect() exception, err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
			return false;
		}
		return true;
	}
	
	protected  function _setDatabase($type)
	{
		$strDatabase = '';
		switch($type){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$strDatabase = 'db_yl_wp_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$strDatabase = 'db_yl_theme_contact_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$strDatabase = 'db_yl_theme_mms_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$strDatabase = 'db_yl_theme_icon_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES:{
				$strDatabase = 'db_yl_theme_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_FONT:{
				$strDatabase = 'db_yl_font_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_RING:{
				$strDatabase = 'db_yl_ring_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$strDatabase = 'db_yl_widget_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_SCENE:{
				$strDatabase = 'db_yl_scene_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_X_RING:{
				$strDatabase = 'db_yl_xring_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$strDatabase = 'db_yl_livewp_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_ALARM:{
				$strDatabase = 'db_yl_alarm_record_'.date('Ym');
			}break;
				
			default:return false; 
		}
		$this->_db = $strDatabase;
		return true;
	}
	
	protected  function _setScoreDatabase($type)
	{
		$strDatabase = 'db_yl_score_record';
// 		switch($type){
// 			case COOLXIU_TYPE_WALLPAPER:
// 			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
// 				$strDatabase = 'db_yl_score_wp_record';
// 			}break;
// 			case COOLXIU_TYPE_THEMES:{
// 				$strDatabase = 'db_yl_score_theme_record';
// 			}break;
// 			case COOLXIU_TYPE_FONT:{
// 				$strDatabase = 'db_yl_score_font_record';
// 			}break;
// 			case COOLXIU_TYPE_RING:{
// 				$strDatabase = 'db_yl_score_ring_record';
// 			}break;
// 			case COOLXIU_TYPE_WIDGET:{
// 				$strDatabase = 'db_yl_score_widget_record';
// 			}break;
// 			case COOLXIU_TYPE_SCENE:{
// 				$strDatabase = 'db_yl_score_scene_record';
// 			}break;
// 			default:return false;
// 		}
		$this->_db = $strDatabase;
		return true;
	}
	
	protected  function _setDlDatabase($type)
	{
		$strDatabase = '';
		switch($type){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$strDatabase = 'db_yl_dl_wp_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$strDatabase = 'db_yl_dl_theme_contact_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$strDatabase = 'db_yl_dl_theme_mms_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$strDatabase = 'db_yl_dl_theme_icon_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_THEMES:{
				$strDatabase = 'db_yl_dl_theme_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_FONT:{
				$strDatabase = 'db_yl_dl_font_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_RING:{
				$strDatabase = 'db_yl_dl_ring_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$strDatabase = 'db_yl_dl_widget_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_SCENE:{
				$strDatabase = 'db_yl_dl_scene_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$strDatabase = 'db_yl_dl_livewp_record_'.date('Ym');
			}break;
			case COOLXIU_TYPE_ALARM:{
				$strDatabase = 'db_yl_dl_alarm_record_'.date('Ym');
			}break;
			default:return false;
		}
		$this->_db = $strDatabase;
		return true;
	}
	
	protected  function _setOrderDatabase($type)
	{
		$strDatabase = '';
		switch($type){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$strDatabase = 'db_yl_order_wp_record';
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$strDatabase = 'db_yl_order_theme_contact_record';
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$strDatabase = 'db_yl_order_theme_mms_record';
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$strDatabase = 'db_yl_order_theme_icon_record';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$strDatabase = 'db_yl_order_theme_record';
			}break;
			case COOLXIU_TYPE_FONT:{
				$strDatabase = 'db_yl_order_font_record';
			}break;
			case COOLXIU_TYPE_RING:{
				$strDatabase = 'db_yl_order_ring_record';
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$strDatabase = 'db_yl_order_widget_record';
			}break;
			case COOLXIU_TYPE_SCENE:{
				$strDatabase = 'db_yl_order_scene_record';
			}break;
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$strDatabase = 'db_yl_order_livewp_record';
			}break;
			case COOLXIU_TYPE_ALARM:{
				$strDatabase = 'db_yl_order_alarm_record';
			}break;
			default:return false;
		}
		$this->_db = $strDatabase;
		return true;
	}
	public function addIndex($keys)
	{
		try{
			$result = $this->_mongo->getIndexInfo($this->_collection);
			if($result){
				return true;
			}
			foreach ($keys as $key => $value){
				if(is_array($value)){
					$index = $value;
				}else {
					$index = array($key=>$value);
				}
				$bResult = $this->_mongo->ensureIndex($this->_collection, $index);
				if(!$bResult){
					Logs::write('MongoRecord::addIndex():ensureIndex() failed, collection:'.$this->_collection, 'log');
					return false;
				}
			}
			return true;
		}catch (Exception $e){
			Logs::write('MongoRecord::addIndex() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
	
	abstract public function saveRecord($nCoolType, Record $record);
}