<?php
/**
 * 按客户端请求响应的协议格式定义的类型，方便对象生成JSON格式的响应流
 *
 * @author lijie1@yulong.com
 */
require_once 'tasks/protocol/Protocol.php';
require_once 'configs/config.php';

class ThemesWebProtocol extends Protocol
{
	public $id;					//主题ID
	public $name;				//主题名
	public $main_prev_url;		//主缩略图URI
	public $download_times;		//下载次数
   
	function __construct(){
		parent::__construct();
		$this->id					=	0;
		$this->name					= 	'';
		$this->main_prev_url		= 	'';
		$this->download_times		=   0;
	}
	
	public function setProtocol($theme_row, $channel = 0)
	{
		$this->id 				= isset($theme_row['identity'])?$theme_row['identity']:'';
		$this->_cpid 			= isset($theme_row['cpid'])?$theme_row['cpid']:0;
		$this->name 			= isset($theme_row['name'])?$theme_row['name']:'';
		$this->main_prev_url 	= isset($theme_row['prev_url'])?$theme_row['prev_url']:0;
		$this->download_times 	= isset($theme_row['download_times'])?$theme_row['download_times']:0;
	}
}
?>