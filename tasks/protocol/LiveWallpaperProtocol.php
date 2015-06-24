<?php
require_once 'tasks/protocol/Protocol.php';
require_once 'configs/config.php';

defined('LIVE_WALLPAPER_DOWNLOD_PHP')
	or define('LIVE_WALLPAPER_DOWNLOD_PHP', '/service/livewpdl.php?id=%s&cpid=%s&channel=%d&url=%s');

class LiveWallpaperProtocol extends Protocol
{
	public	$name;      		//String   英文名
	//public	$ename;      		//String   中文名
	public	$iconUrl;      		//String   预览图下载地址
	public	$url;      			//String   资源下载地址
	public	$intro;				//string   场景描述
	public	$size;      		//long     场景大小
	public	$md5;				//md5 	        资源md5
			
	function __construct(){
		parent::__construct();
		$this->name			= 0;		
		$this->iconUrl		= '';
		$this->url			= '';
		$this->size			= '';
		$this->intro		= '';	
	}
	
	private function getUrl($id, $surl = '', $channel = 0)
	{
		global $g_arr_host_config;
		$download = sprintf(LIVE_WALLPAPER_DOWNLOD_PHP, $id, $id, $channel, $surl);
		
		$url = $g_arr_host_config['host'].$download;
		return $url;
	} 
	
	public function setProtocol($row, $nChannel = 0, $newver = false)
	{
		$this->id				= isset($row['cpid'])?$row['cpid']:0;
		$this->cpid 			= isset($theme_row['cpid'])?$theme_row['cpid']:0;
		$this->name 			= isset($theme_row['name'])?$theme_row['name']:'';
		$this->size 			= (int)isset($theme_row['size'])?$theme_row['size']:0;
		$this->md5 				= isset($theme_row['md5'])?$theme_row['md5']:'';
		$this->intro			= isset($row['intro'])?$row['intro']:'';
		$iconUrl 				= isset($row['icon'])?$row['icon']:'';
		$surl 					= isset($row['url'])?$row['url']:'';
		global $g_arr_host_config;
		$surl					= $g_arr_host_config['cdnhost'].$surl;
// 		$strUrl = $this->getUrl($this->id, $surl, $nChannel);
		$this->url				= $surl;
		$this->iconUrl			= $g_arr_host_config['cdnhost'].$iconUrl;
		
		$this->setCommonParam($row, $nChannel);
	}	
}