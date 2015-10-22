<?php

require_once 'tasks/CoolXiu/CoolXiu.class.php';
require_once 'tasks/CoolXiu/themes.class.php';
require_once 'tasks/CoolXiu/Wallpaper.class.php';

class CoolXiuFactory{
	
	function __construct(){
		
	}
	
	static function getCoolXiu($xiu_type, &$type_key){
		switch($xiu_type){
			case COOLXIU_TYPE_THEMES:{
					$coolxiu = new Themes();
					$type_key = "themes";
				}break;
			case COOLXIU_TYPE_PREV:{
					$coolxiu = new Preview();
					$type_key = "themes";
				}break;
			case COOLXIU_TYPE_WALLPAPER:{
					$coolxiu = new Wallpaper();
					$type_key = "wallpapers";
				}break;
			default:{
					$coolxiu = new Themes();
					$type_key = "themes";
				}break;
		}		
		return $coolxiu;
	}
	
	static function getCoolShowStatisTb($xiu_type){
		$table = "";
		switch($xiu_type){
			case COOLXIU_TYPE_THEMES:{				
				$table = "tb_yl_tdl_statis";
				}break;
			case COOLXIU_TYPE_WALLPAPER:{
					$table = "tb_yl_wpdl_statis";
				}break;
			default:{
					$table = "tb_yl_tdl_statis";
				}break;
		}
		return $table;
	}
}