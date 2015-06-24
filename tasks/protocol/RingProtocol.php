<?php
require_once 'tasks/protocol/Protocol.php';
defined('RING_DOWNLOD_PHP')
	or define('RING_DOWNLOD_PHP', '/service/ringdl.php?id=%s&type=%d&channel=%d');
class RingProtocol extends Protocol
{
	public $id;			//ID下载及下载统计用
	public $name;			//名字
	public $fname;			//文件名
	public $url;			//URL
	public $size;			//
	
	function __construct(){
		parent::__construct();
		$this->id		= '';
		$this->name		= '';
		$this->fname	= '';
		$this->url		= '';
		$this->size		= 0;
		$this->type		= 0;
		$this->download_times = 0;
	}
	
	public function  setRingType($type)
	{
		$this->type = $type;	
	}
	
	public function setProtocol($row, $channel = 0)
	{
		$this->id		= isset($row['identity'])?$row['identity']:'';
		$this->name		= isset($row['name'])?$row['name']:'';
		$this->fname	= isset($row['fname'])?$row['fname']:'';
		$this->size		= (int)isset($row['size'])?$row['size']:0;
		$this->type		= (int)isset($row['type'])?$row['type']:0;
		global $g_arr_host_config;
		//		$this->url		= $g_arr_host['host'].$row['url'];
		$url_get_param  = sprintf(RING_DOWNLOD_PHP, $this->id, $this->type, $channel);
		$this->url		= $g_arr_host_config['host'].$url_get_param;
		
		$this->setCommonParam($row, $channel);
	}
}