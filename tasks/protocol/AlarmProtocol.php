<?php
require_once 'configs/config.php';
require_once 'tasks/protocol/Protocol.php';
class AlarmProtocol extends Protocol
{
	public $id;				//ID下载及下载统计用
	public $name;			//名字
	public $fname;			//文件名
	public $url;			//URL
	public $size;			//
	public $imgurl;			//专辑的图片，每个铃声都带有相同的图片，冗余了点，为了banner的结构不受影响，只能冗余些
	
	function __construct(){
		parent::__construct();
		$this->id		= '';
		$this->name		= '';
		$this->fname	= '';
		$this->url		= '';
		$this->size		= 0;
		$this->imgurl	= '';
	}
	
	public function setProtocol($row, $channel = 0)
	{
		$this->id		= isset($row['identity'])?$row['identity']:'';
		$this->name		= isset($row['name'])?$row['name']:'';
		$this->fname	= isset($row['fname'])?$row['fname']:'';
		$this->size		= (int)isset($row['size'])?$row['size']:0;
		$this->type		= (int)isset($row['type'])?$row['type']:0;
		$strUrl 		= isset($row['url'])?$row['url']:'';
		$strImgurl 		= isset($row['imgurl'])?$row['imgurl']:'';
		global  $g_arr_host_config;
		$this->url		= $g_arr_host_config['cdnhost'].$strUrl;
		$this->imgurl	= $g_arr_host_config['cdnhost'].$strImgurl;
		
		$this->setCommonParam($row, $channel);
	}
}