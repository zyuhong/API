<?php

require_once ('configs/config.php');
require_once 'tasks/statis/Product.class.php';

class WallpaperProtocol{
	
	const YL_WP_BROWSE_URL = '/service/wpbrowse.php?id=%s&cpid=%s&type=%d&channel=%d&product=%s&author=%s';
	const YL_WP_DOWNLOAD_URL = '/service/wpdownload.php?id=%s&cpid=%s&type=%d&channel=%d&product=%s&author=%s';
	const YL_ADWP_BROWSE_URL = '/service/adwpbrowse.php?id=%s&cpid=%s&type=%d&channel=%d&product=%s&url=%s&author=%s';
	const YL_ADWP_DOWNLOAD_URL = '/service/adwpdownload.php?id=%s&cpid=%s&type=%d&channel=%d&product=%s&url=%s&author=%s';
	
	private $_product;
	private $_vercode;

	public $cpid;
	public $id;					//ID
	public $size;				//文件大小
	public $wp_widget_url;		//壁纸Widget URI
	public $wp_url;				//壁纸URI
	public $wp_mid_url;			//壁纸中视图URI
	public $wp_small_url;		//壁纸小视图URI
	public $width;
	public $height;
	public $download_times;		//下载次数
	public $type;				//具体类型
	public $author;				//作者
	public $channel;			//下载渠道
	
	function __construct(){
		$this->_product = new Product();
		$this->_vercode				= 0;
		$this->id					=	0;
		$this->cpid					=   '';
		$this->size					= 	0;
		$this->wp_widget_url		= 	'';
		$this->wp_url				= 	'';
		$this->wp_mid_url			= 	'';
		$this->wp_small_url			= 	'';
		$this->width				=   0;
		$this->height				= 	0;
		$this->download_times		= 	0;
		$this->author				=   '';	
		$this->channel			    = 0;
	}
	
	public function setVercode($vercode)
	{
		$this->_vercode	= $vercode;
	}
	
	public  function setProduct($product){
		$this->_product = $product;
	}
	
	private function _getBrowseUrl($channel = 0){
		global $g_arr_host_config;
		$brose = sprintf(self::YL_WP_BROWSE_URL, $this->id, $this->cpid, $this->type, $channel, urlencode($this->_product->name), $this->author);

		$url = $g_arr_host_config['host'].$brose;
		return $url;
	}

	private function _getDownloadUrl($channel = 0){
		global $g_arr_host_config;
		$download = sprintf(self::YL_WP_DOWNLOAD_URL, $this->id, $this->cpid, $this->type,	$channel, urlencode($this->_product->name), $this->author);

		$url = $g_arr_host_config['host'].$download;
		return $url;
	}

	private function _getAdBrowseUrl($url, $channel = 0){
		global $g_arr_host_config;
		$brose = sprintf(self::YL_ADWP_BROWSE_URL, $this->id, $this->cpid, $this->type,  $channel, urlencode($this->_product->name), $url, $this->author);
	
		$url = $g_arr_host_config['host'].$brose;
		return $url;
	}
	
	private function _getAdDownloadUrl($url, $channel = 0){
		global $g_arr_host_config;
		$download = sprintf(self::YL_ADWP_DOWNLOAD_URL, $this->id, $this->cpid, $this->type, $channel, urlencode($this->_product->name), $url, $this->author);
	
		$url = $g_arr_host_config['host'].$download;
		return $url;
	}
	
	function setWallpaperUrl($url, $midUrl, $smallUrl)
	{
		$this->wp_url 			= $url;
		$this->wp_mid_url 		= $midUrl;
		$this->wp_small_url 	= $smallUrl;
	}	
	/**
	 * 根据数据库设置主题参数
	 * @param unknown_type $row
	 */
	function setWallpaper($row, $channel = 0){
		global $g_arr_host_config;
		$this->id 				= $row['id'];
		$this->cpid 			= isset($row['cpid'])?$row['cpid']:'';
		$this->size 			= (int)$row['size'];
		
		$this->wp_url 			= $this->_getDownloadUrl($channel);//$g_arr_host['host'].$row['url'];
		$this->wp_mid_url 		= $this->_getBrowseUrl($channel);//$g_arr_host['host'].$row['mid_url'];
		$this->wp_small_url 	= $g_arr_host_config['cdnhost'].$row['small_url'];
		
		$this->width			=   $row['width'];
		$this->height			= 	$row['height'];
		$this->download_times	= (int)isset($row['download_times'])?$row['download_times']:1001;
		$this->download_times	+= rand(1000, 10000);
		$this->author			=   isset($row['author'])?$row['author']:'';
		
		$this->channel			= $channel;			//下载渠道
	}
	
	public function setProtocol($row, $channel = 0)
	{
		global $g_arr_host_config;
		$this->id 				= isset($row['id'])?$row['id']:'';
		$this->cpid 			= isset($row['id'])?$row['id']:'';		//isset($row['cpid'])?$row['cpid']:'';
		$this->wp_url 			= $this->_getAdDownloadUrl($row['url'], $channel);
		$this->wp_mid_url 		= $this->_getAdBrowseUrl($row['mid_url'], $channel);
		$this->wp_small_url 	= $g_arr_host_config['androidwp_host'].$row['small_url'];
		$this->download_times	= (int)isset($row['download_times'])?$row['download_times']:1001;
		$this->download_times	+= rand(1000, 10000);
		$this->author			=   isset($row['author'])?$row['author']:'';
		
		$this->channel			= $channel;			//下载渠道
	}
	
	function setAndroideskWallpaper($row, $channel = 0, $cpid = ''){
		global $g_arr_host_config;
		$this->id 				= isset($row['id'])?$row['id']:'';
		$this->cpid 			= $cpid;//isset($row['id'])?$row['id']:'';		//isset($row['cpid'])?$row['cpid']:'';
		$this->wp_url 			= $this->_getAdDownloadUrl($row['url'], $channel);
		$this->wp_mid_url 		= $this->_getAdBrowseUrl($row['mid_url'], $channel);
		$this->wp_small_url 	= $g_arr_host_config['androidwp_host'].$row['small_url'];
		$this->download_times	= (int)isset($row['download_times'])?$row['download_times']:1001;
		$this->download_times	+= rand(1000, 10000);
		$this->author			=  isset($row['author'])?$row['author']:'';
		
		$this->channel			= $channel;			//下载渠道
	}

	public function setCpid($strCpid)
	{
		$this->cpid = $strCpid;
	}
	
	public function setWidgetUrl($url)
	{
		global $g_arr_host;
		$this->wp_widget_url = $url;//$g_arr_host['host'].$url;
	}
	
	function setWallpaperRatio($width, $height){
		$this->width			=   $width;
		$this->height			= 	$height;
	}

	function setWallpaperType($type){
		$this->type		= $type;
	}
}