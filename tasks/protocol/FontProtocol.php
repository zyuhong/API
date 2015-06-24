<?php
require_once 'tasks/protocol/Protocol.php';

defined('FONT_DOWNLOD_PHP')
	or define('FONT_DOWNLOD_PHP', '/service/fontdl.php?id=%s&cpid=%s&type=%d&channel=%d');
	
class FontProtocol extends Protocol
{
	public $id;				//ID下载及下载统计用
	public $name;			//名字
	public $language;		//语言
	public $fname;			//文件名
	public $url;			//URL
	public $largepreurl;	//大预览图
	public $previewurl;		//PreviewURL
	public $purl;			//COOLUI6.0新的预览图20141128，要求兼容旧版，PreviewURL保留旧版使用
	public $size;
	public $md5;
	public $folder;
	
	function __construct(){
		parent::__construct();
		$this->_cpid	= '';
		$this->id		= '';
		$this->name		= '';
		$this->language = '';	
		$this->fname	= '';
		$this->url		= '';
		$this->largepreurl = '';
		$this->previewurl = '';
		$this->purl 	= '';
		$this->size		= 0;
		$this->md5		= '';
		$this->folder	= '';
		$this->type		= 0;
		$this->download_times = 0;
	}
	
	private function _getFolder($url)
	{
		$pos = strripos($url, '/');
		if($pos === false){
			return $url;
		}
		
		$folder = substr($url, 0, (int)$pos + 1);
		return $folder;
	}
	
	private function _getDownloadUrl($channel = 0){
		global $g_arr_host_config;
		$url_get_param  = sprintf(FONT_DOWNLOD_PHP, $this->id, $this->cpid, $this->type, $channel);
		$url = $g_arr_host_config['host'].$url_get_param;
		return $url;
	}
	public function setProtocol($row, $channel = 0)
	{
		$this->id		= isset($row['identity'])?$row['identity']:'';
		$this->cpid	= isset($row['identity'])?$row['identity']:'';
		$this->name		= isset($row['name'])?$row['name']:'';
		$this->language	= isset($row['language'])?$row['language']:'';
		$this->fname	= isset($row['fname'])?$row['fname']:'';
		$this->size		= isset($row['size'])?$row['size']:0;
		$this->md5		= isset($row['md5'])?$row['md5']:'';
		
		$this->url		= $this->_getDownloadUrl();
		$largepreurl = isset($row['largepreurl'])?$row['largepreurl']:'';
		$previewurl  = isset($row['preview_url'])?$row['preview_url']:'';
		$purl 		 = isset($row['purl'])?$row['purl']:'';
		global $g_arr_host_config;
		$this->largepreurl = $g_arr_host_config['cdnhost'].$largepreurl;
		$this->previewurl  = $g_arr_host_config['cdnhost'].$previewurl;
		$this->purl		   = $g_arr_host_config['cdnhost'].$purl;
		$url = isset($row['url'])?$row['url']:'';
		$this->folder = $this->_getFolder($url);
		
		$this->setCommonParam($row, $channel);
	}
}