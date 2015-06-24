<?php
require_once 'tasks/statis/StatisSql.sql.php';
require_once 'configs/config.php';

class StatisFactory{
	const STATIS_OBJECT_RING 		= 0;
	const STATIS_OBJECT_WALLPAPER 	= 1;
	
	function __construct(){
		
	}

	static function getReqRecodTable($objec){
		$table = '';
		switch($objec){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$table = 'tb_yl_wpreq_record';
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$table = 'tb_yl_threq_contact_record';
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$table = 'tb_yl_threq_mms_record';
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$table = 'tb_yl_threq_icon_record';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$table = 'tb_yl_threq_record';
			}break;
			case COOLXIU_TYPE_FONT:{
				$table = 'tb_yl_fontreq_record';
			}break;
			case COOLXIU_TYPE_RING:{
				$table = 'tb_yl_rreq_record';
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$table = 'tb_yl_widgetreq_record';
			}break;
			case COOLXIU_TYPE_SCENE:{
				$table = 'tb_yl_lsreq_record';
			}break;
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$table = 'tb_yl_livewpreq_record';
			}break;
			case COOLXIU_TYPE_ALARM:{
				$table = 'tb_yl_alarmreq_record';
			}break;
		}
		return $table;
	}
	
	static function getDlRecodTable($objec){
		$table = '';
		switch($objec){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$table = 'tb_yl_wpdl_record';
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$table = 'tb_yl_thdl_contact_record';
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$table = 'tb_yl_thdl_mms_record';
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$table = 'tb_yl_thdl_icon_record';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$table = 'tb_yl_thdl_record';
			}break;
			case COOLXIU_TYPE_FONT:{
				$table = 'tb_yl_fontdl_record';
			}break;
			case COOLXIU_TYPE_RING:{
				$table = 'tb_yl_rdl_record';
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$table = 'tb_yl_widgetdl_record';
			}break;
			case COOLXIU_TYPE_SCENE:{
				$table = 'tb_yl_lsdl_record';
			}break;
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$table = 'tb_yl_livewpdl_record';
			}break;
			case COOLXIU_TYPE_ALARM:{
				$table = 'tb_yl_alarmdl_record';
			}break;
		}
		return $table;
	}
	
	static function getBrRecodTable($objec){
		$table = '';
		switch($objec){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$table = 'tb_yl_wpbrowse_record';
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$table = 'tb_yl_thbrowse_contact_record';
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$table = 'tb_yl_thbrowse_mms_record';
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$table = 'tb_yl_thbrowse_icon_record';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$table = 'tb_yl_thbrowse_record';
			}break;
			case COOLXIU_TYPE_RING:{
				$table = 'tb_yl_rbrowse_record';
			}break;
			case COOLXIU_TYPE_FONT:{
				$table = 'tb_yl_fontbrowse_record';
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$table = 'tb_yl_widgetbrowse_record';
			}break;		
			case COOLXIU_TYPE_SCENE:{
				$table = 'tb_yl_lsbrowse_record';
			}break;	
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$table = 'tb_yl_livewpbrowse_record';
			}break;
			case COOLXIU_TYPE_ALARM:{
				$table = 'tb_yl_alarmbrowse_record';
			}break;
		}
		return $table;
	}
	static function getApplyRecodTable($objec){
		$table = '';
		switch($objec){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$table = 'tb_yl_wpapply_record';
			}break;
			case COOLXIU_TYPE_THEMES_CONTACT:{
				$table = 'tb_yl_thapply_contact_record';
			}break;
			case COOLXIU_TYPE_THEMES_MMS:{
				$table = 'tb_yl_thapply_mms_record';
			}break;
			case COOLXIU_TYPE_THEMES_ICON:{
				$table = 'tb_yl_thapply_icon_record';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$table = 'tb_yl_thapply_record';
			}break;
			case COOLXIU_TYPE_FONT:{
				$table = 'tb_yl_fontapply_record';
			}break;
			case COOLXIU_TYPE_RING:{
				$table = 'tb_yl_rapply_record';
			}break;
			case COOLXIU_TYPE_SCENE:{
				$table = 'tb_yl_lsapply_record';
			}break;	
			case COOLXIU_TYPE_LIVE_WALLPAPER:{
				$table = 'tb_yl_livewpapply_record';
			}break;
			case COOLXIU_TYPE_ALARM:{
				$table = 'tb_yl_alarmapply_record';
			}break;
		}
		return $table;
	}
	
   	static function getInsertRecordSql($object, $id, $product, $imeid, $imei, $imsi){
		$table = "";
		switch($object){
			case self::STATIS_OBJECT_RING:{
				$table = 	'tb_yl_rdl_statis';
			}break;
			case self::STATIS_OBJECT_WALLPAPER:{
				$table = 	'tb_yl_wpdl_statis';
			}break;
		}
		$sql = sprintf(SQL_INSERT_DOWNLOAD_RECORD, $table, 
						$id, $product, $imeid, $imei, $imsi, date("Y-m-d H:i:s"));
		return $sql;
	}
	
	static function getInsertApplyRecordSql($id, $product, $imeid, $imei, $imsi, $applytype)
	{
		$sql = 	sprintf(SQL_INSERT_WLLPAPER_APPLY_RECORD, $id, $product, $imeid, $imei, $imsi, $applytype, date("Y-m-d H:i:s"));
		return $sql;
	}
}