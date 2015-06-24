<?php
/**
 * 按客户端请求响应的协议格式定义的类型，方便对象生成JSON格式的响应流
 *
 * @author lijie1@yulong.com
 */
require_once 'tasks/protocol/Protocol.php';
require_once ('./configs/config.php');

class ThemesDetailsProtocol extends Protocol
{
	const YL_TH_DOWNLOAD_URL = '/service/thdownload.php?id=%s&cpid=%s&type=%d&channel=%d';
	public $id;					//主题ID
	public $author;				//主题作者
	public $name;				//主题名
	public $size;				//主题文件大小
	public $description;		//主题描述
	public $them_file_url;		//主题URI,绝对路径
	public $main_prev_url;		//主缩略图URI
	public $created_at;			//主题创建时间
	public $prev_img_num;		//缩略图数量
	public $them_widget_url;	//主题URI,绝对路径
	public $effect;	 			//主题特效
    public $font_style; 		//主题字体样式
    public $keyguard_style; 	//主题解锁样式
    public $prev_imgs;			//主题缩略图数组
    
    function __construct(){
		parent::__construct();
		$this->id					=	0;
		$this->author				= 	'';
		$this->name					= 	'';
		$this->size					= 	0;
		$this->description			= 	'';
		$this->them_file_url		= 	'';
		$this->main_prev_url		= 	'';
		$this->them_widget_url		=   '';
		$this->created_at			= 	'';
		$this->prev_img_num			= 	0;
		$this->type					= 	0;
		$this->download_times		=   0;
		$this->effect				=   '';	 			
		$this->font_style			=   ''; 	
		$this->keyguard_style		=   ''; 	
		$this->prev_imgs 			= array();
	}
	
	public function setMainPrev($prev){
		global  $g_arr_host;
		$this->main_prev_url 	= $g_arr_host['cdnhost'].$prev['prev_url'];
	}

	public function pushPrevImg($prev){
		array_push($this->prev_imgs, $prev);
	}
	
	private function _getDownloadUrl($channel){
		global $g_arr_host;
		$download = sprintf(self::YL_TH_DOWNLOAD_URL, $this->id, $this->_cpid, $this->type, $channel);
	
		$url = $g_arr_host['host'].$download;
		return $url;
	}
	
	
	function setPrevImgs($img_num, $main_url, $prev_imgs){
		$this->main_prev_url 	= $main_url;
		$this->prev_img_num 	= (int)$img_num;
		$this->prev_imgs 		= $prev_imgs;
	}
	
	public function setProtocol($theme_row, $channel = 0)
	{
		global $g_arr_host;
		$this->id 				= isset($theme_row['identity'])?$theme_row['identity']:'';
		$this->_cpid 			= isset($theme_row['cpid'])?$theme_row['cpid']:0;
		$this->type 			= (int)isset($theme_row['type'])?$theme_row['type']:0;
		$this->author 			= isset($theme_row['author'])?$theme_row['author']:'';
		$this->name 			= isset($theme_row['name'])?$theme_row['name']:'';
		$this->size 			= (int)isset($theme_row['size'])?$theme_row['size']:0;
		$this->effect 			= isset($theme_row['effect'])?$theme_row['effect']:'';
		$this->font_style 		= isset($theme_row['font_style'])?$theme_row['font_style']:'';
		$this->keyguard_style 	= isset($theme_row['keyguard_style'])?$theme_row['keyguard_style']:'';
		$this->description 		= isset($theme_row['note'])?$theme_row['note']:'';
		$this->them_file_url 	= $this->_getDownloadUrl($channel);
		$this->created_at 		= isset($theme_row['insert_time'])?$theme_row['insert_time']:'';
		//		$this->md5 				= $theme_row['md5'];
		$this->prev_img_num 	= (int)isset($theme_row['img_num'])?$theme_row['img_num']:0;

		$this->intro			=  isset($theme_row['kernel'])?$theme_row['kernel']:'';
		$this->version			=  isset($theme_row['version'])?$theme_row['version']:'';
		$this->star_level		=  isset($theme_row['star_level'])?$theme_row['star_level']:'';
		$this->setCommonParam($theme_row, $channel);
	}
}
?>