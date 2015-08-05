<?php
require_once 'tasks/protocol/Protocol.php';
require_once 'configs/config.php';

defined('SENCE_DOWNLOD_PHP')
	or define('SENCE_DOWNLOD_PHP', '/service/scenedl.php?id=%s&cpid=%s&channel=%d&url=%s');

class ScreenProtocol extends Protocol
{
	public	$authorId;      		//long     作者id
	public	$authorName;      		//String   作者名
// 	public	$channel;      			//long     频道
	public	$sceneEName;      		//String   场景英文名
	public	$sceneZName;      		//String   场景中文名
	public	$iconHd;      			//String   高清图下载地址
	public	$iconDownURL;      		//String   普通图下载地址
	public	$newCount;      		//long     下载次数
	public	$severResVersion;      	//long     资源版本号
	public	$sceneId;      			//long     场景id
	public	$sceneTotalSize;      	//long     场景大小
	public	$intro;					//string   场景描述
	public	$downloadUrl;			//string   场景下载资源URL
	public	$packageName;			//string   场景APK包名
	public	$updateCount;      		//long     更新次数
	public	$updateTime;      		//long     更新时间
			
	function __construct(){
		parent::__construct();
		$this->authorId			= 0;		
		$this->authorName		= '';
// 		$this->channel			= 0;	
		$this->sceneEName		= '';
		$this->sceneZName		= '';
		$this->iconHd			= '';	      		
		$this->iconDownURL		= '';
		$this->newCount			= 0;
		$this->severResVersion	= 0;	
		$this->sceneId			= 0;	
		$this->sceneTotalSize	= 0;	
		$this->intro			= '';
		$this->downloadUrl		= '';
		$this->packageName		= '';
		$this->updateCount		= 0;	
		$this->updateTime		= 0;	
	}
	
	private function getUrl($id, $surl = '', $channel = 0)
	{
		global $g_arr_host_config;
		$download = sprintf(SENCE_DOWNLOD_PHP, $id, $id, $channel, $surl);
		
		$url = $g_arr_host_config['host'].$download;
		return $url;
	} 
	
	public function setProtocol($row, $nChannel = 0, $newver = false)
	{
		$this->id				= isset($row['sceneCode'])?$row['sceneCode']:0;
		$this->cpid				= isset($row['sceneCode'])?$row['sceneCode']:0;
		$this->sceneId			= isset($row['sceneCode'])?$row['sceneCode']:0;
		$this->sceneEName		= isset($row['enName'])?$row['enName']:'';
		$this->sceneZName		= isset($row['zhName'])?$row['zhName']:'';
		$this->iconHd			= isset($row['iconHd'])?$row['iconHd']:'';
		$this->iconDownURL		= isset($row['icon'])?$row['icon']:'';
		$this->authorId			= isset($row['authorId'])?$row['authorId']:0;
		$this->authorName		= isset($row['authorName'])?$row['authorName']:'CoolUI';
// 		$this->channel			= isset($row['channel'])?$row['channel']:0;
		$this->newCount			= isset($row['newCount'])?$row['newCount']:0;
		$this->severResVersion	= isset($row['severResVersion'])?$row['severResVersion']:0;
		$this->sceneTotalSize	= isset($row['totalSize'])?$row['totalSize']:0;
		$this->intro			= isset($row['intro'])?$row['intro']:'';
		$this->updateCount		= isset($row['updateCount'])?$row['updateCount']:0;
		$this->updateTime		= strtotime(isset($row['updateTime'])?$row['updateTime']:date('Y-m-d H:i:s'));

		$nKernel = isset($row['kernel'])?$row['kernel']:1;
		global  $g_arr_host_config;
		$surl= '';
		
		$url = isset($row['url'])?$row['url']:'';
		if($nKernel == 2){
			$url = isset($row['turl'])?$row['turl']:$url;
		}
		
		$surl = $g_arr_host_config['cdnhost'].$url;
		$strUrl = $this->getUrl($this->sceneId, $surl, $nChannel);
		$this->downloadUrl		= $strUrl;//isset($row['url'])?$row['url']:'';
		if($newver){
			$this->iconHd			= $g_arr_host_config['cdnhost'].$this->iconHd;
			$this->iconDownURL		= $g_arr_host_config['cdnhost'].$this->iconDownURL;
		}
		
		$this->packageName		= isset($row['package'])?$row['package']:'';
		
		$this->setCommonParam($row, $nChannel);
	}	
}