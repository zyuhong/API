<?php
/**
 * 按客户端请求响应的协议格式定义的类型，方便对象生成JSON格式的响应流
 * 
 * @author lijie1@yulong.com
 */
require_once ('configs/config.php');
global $g_arr_host;
defined("YL_HOST_NAME") 
	OR define("YL_HOST_NAME", $g_arr_host['host']);

class Prev_protocol{
	var $img_url;		//缩略图URI
	function Preview(
	){
		$this->img_url = "";
 	}

	function setPrev($prev){
		$this->img_url = YL_HOST_NAME.$prev['prev_url'];
	}
}

class Themes_protocol{
	var $id;				//主题ID
	var $author;			//主题作者
	var $name;				//主题名
	var $size;				//主题文件大小
	var $description;		//主题描述
	var $them_file_url;		//主题URI,绝对路径
	var $main_prev_url;		//主缩略图URI
	var $created_at;		//主题创建时间
	var $prev_img_num;		//缩略图数量
	var $theme_file_md5;	//主题文件MD5
	var $type;				//主题类型
	var $download_times;		//下载次数
	var $prev_imgs;			//主题缩略图数组
	function __construct(){		
		$this->id					=	0;
		$this->author				= 	"";
		$this->name					= 	"";
		$this->size					= 	0;
		$this->description			= 	"";
		$this->them_file_url		= 	"";
		$this->main_prev_url		= 	"";
		$this->created_at			= 	"";
		$this->prev_img_num			= 	0;
		$this->theme_file_md5		= 	"";
		$this->type					= 	0;
		$this->download_times		= 	0;
		$this->prev_imgs = array();
	}

	function setPrevImgs($img_num, $main_url, $prev_imgs){
		$this->main_prev_url 	= $main_url;
		$this->prev_img_num 	= $img_num;
		$this->prev_imgs 		= $prev_imgs;
	}

	//根据数据库设置主题参数
	function setTheme($theme_row){
		$this->id 				= (int)$theme_row['id'];
		$this->author 			= $theme_row['author'];
		$this->name 			= $theme_row['name'];
		$this->size 			= (int)$theme_row['size'];
		$this->description 		= $theme_row['note'];
		$this->them_file_url 	= YL_HOST_NAME.$theme_row['url'];
		$this->created_at 		= $theme_row['insert_time'];
		$this->theme_file_md5 	= $theme_row['theme_file_md5'];
		$this->type 			= (int)$theme_row['type'];
		$this->download_times	= (int)$theme_row['download_times'];
	}
}
?>