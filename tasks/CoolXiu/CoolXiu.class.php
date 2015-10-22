<?php
abstract  class CoolXiu{
	var $id;				//主题ID
	var $cpid;				//同一张主题多个分辨率的统一ID
	var $s_author;			//主题作者
	var $s_name;			//主题名
	var $s_folder;			//主题文件夹
	var $size;				//主题文件大小
	var $s_note;			//主题描述
	var $s_url;				//主题URI,相对路径
	var $insert_time;		//主题创建时间
	var $md5;				//主题文件MD5
	var $type;				//主题类型，预览图类型：缩略图？浏览图
	var $ratio;				//分辨率编号
	var $width;				//分辨率宽
	var $height;			//分辨率高
	var $download_times;	//下载次数
	
	protected  $_vercode;		//应用版本
	
	protected  $_search_type;
	protected  $_kernel;
	protected  $_width;
	protected  $_height;
	protected  $_start;
	protected  $_limit;
	
	function __construct(){
		$this->id			= '';	
		$this->cpid			= '';			
		$this->s_author		= "";
		$this->s_name 		= "";
		$this->s_folder 	= "";
		$this->size			= 0;
		$this->s_note		= "";
		$this->s_url		= "";
		$this->insert_time	= date("Y-m-d H:i:s");
		$this->md5			= "";	
		$this->type			= 0;
		$this->width		= 0;
		$this->height		= 0;	
		$this->download_times	= 0;	
		
		$this->_vercode		= 0;

		$this->_search_type 	= 0;
		$this->_kernel		= 1;
		$this->_width		= 0;
		$this->_height		= 0;
		$this->_start		= 0;
		$this->_limit		= 0;
	}
	
	function setSearchParam($searchtype, $kernel, $width, $height, $start = 0, $limit = 0, $vercode = 0)
	{
		$this->_search_type 	= $searchtype;
		$this->_kernel		= $kernel;
		$this->_width		= $width;
		$this->_height		= $height;
		$this->_start		= $start;
		$this->_limit		= $limit;
		$this->_vercode     = $vercode;
	}
	
	function setCoolXiuParam($id, $cpid, $s_author, $s_name, $f_size, $s_note, $type = 0, $ratio = 0){
		$this->id			= $id;
		$this->cpid			= $cpid;
		$this->s_author 	= $s_author;
		$this->s_name 		= $s_name;
		$this->size 		= $f_size;
		$this->s_note 		= $s_note;
		$this->type 		= $type;
		$this->ratio		= $ratio;
		$this->setWHByRatio($ratio);
//		$this->width		= $width;
//		$this->height		= $height;
	}
	
	function setWHByRatio($ratio){
		switch ($ratio){
			case 0:break;
			case 1:{
				$this->width		= 960;
				$this->height		= 800;
			}break;
			case 2:{
				$this->width		= 960;
				$this->height		= 960;
			}break;
			case 3:{
				$this->width		= 1440;
				$this->height		= 1280;
			}break;	
			case 4:{
				$this->width		= 1080;
				$this->height		= 960;
			}break;
			case 5:{
				$this->width		= 2160;
				$this->height		= 1920;
			}break;
			case 6:{
				$this->width		= 2400;
				$this->height		= 1920;
			}break;
			default:{
				$this->width		= 960;
				$this->height		= 800;
			}break;						
		}
	}
	/**
	 * 根据分辨率生产文件夹名称
	 */
	function getRatioFolder(){		
		switch ($this->ratio){
			case 0:break;
			case 1:{
				$folder = '960x800';
			}break;
			case 2:{
				$folder = '960x960';
			}break;
			case 3:{
				$folder = '1440x1280';
			}break;
			case 4:{
				$folder = '1080x960';
			}break;
			case 5:{
				$folder = '1080x1920';
			}break;
			default:{
				$folder = '960x800';
			}break;
		}
		return $folder;
	}
	/**
	 * 获取分辨率与类型组合的文件路径
	 * @param unknown_type $r_folder
	 * @param unknown_type $t_folder
	 */
	function getRTFolder($r_folder, $t_folder){
		return $r_folder.'/'.$t_folder;
	}
	/**
	 * 从数据库读取的行中设置CoolXiu
	 */
	abstract function setCoolXiu($row);	
	abstract function getCoolXiuListSql();
	abstract function getSearchCoolXiuCountSql();
	abstract function getInsertSql();
	abstract function getSelectLimitSql($start, $count);
	abstract function getSelectRatioLimitSql($width, $height, $start, $count);
	abstract function getSelectTypeLimitSql($type, $start, $count);
	abstract function getSelectTypeRatioLimitSql();
	abstract function getSelectNewLimitSql($start, $count);
	abstract function getSelectNewRatioLimitSql($width, $height, $start, $count);
	abstract function getSelectAlbumsSql();
	abstract function getCountSql();
	abstract function getRatioCountSql($width, $height);
	abstract function getTypeRatioCountSql();
	abstract function getTypeCountSql($type);
	abstract function getNewCountSql();
	abstract function getNewRatioCountSql($width, $height);
	abstract function getSelectUrlByIdSql($id, $type);
	abstract function upload($cool_xiu_file, $error, $f_name, $f_size, $f_tmp_file);
}