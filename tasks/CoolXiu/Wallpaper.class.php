<?php
require_once 'configs/config.php';
require_once 'CoolXiu.class.php';
require_once 'CoolXiuFile.class.php';
require_once 'public/public.php';
require_once 'WallpaperSql.sql.php';

class Wallpaper extends CoolXiu{	
	const YL_WP_MID_URL 	= 0;
	const YL_WP_LARGE_URL 	= 1;
	
	var $mid_url;			//壁纸中视图URI
	var $small_url;			//壁纸小视图URI
	var $mid_md5;			//中视图文件MD5
	var $small_md5;			//小视图文件MD5
	var $width;				//分辨率宽
	var $height;			//分辨率高
	function __construct(){
		parent::__construct();
		$mid_url 	= "";
		$small_url  = "";
		$mid_md5 	= "";
		$small_md5 	= "";
		$width		= 0;
		$height		= 0;
	}
	
	function setWallpaper($mid_url, $small_url, $mid_md5, $small_md5){
		$this->mid_url 		= $mid_url;
		$this->small_url 	= $small_url;
		$this->mid_md5 		= $mid_md5;
		$this->small_md5 	= $small_md5;
	}
	
	function setCoolXiu($row){
		try {
			$this->id 				= $row['id'];
			$this->s_author 		= $row['author'];
			$this->s_name 			= $row['name'];
			$this->s_folder 		= $row['folder'];
			$this->s_note 			= $row['note'];
			$this->s_url 			= $row['url'];
			$this->mid_url 			= $row['mid_url'];
			$this->small_url 		= $row['small_url'];
			$this->insert_time 		= $row['insert_time'];
			$this->md5 				= $row['md5'];
			$this->mid_md5 			= $row['mid_md5'];
			$this->small_md5 		= $row['small_md5'];
			$this->type 			= $row['type'];
			$this->size 			= $row['size'];
			$this->width			= $row['width'];
			$this->height			= $row['height'];
		}catch(Exception $e){
			Log::write("Wallpaper::setCoolXiu() exception: ".$e->getMessage(), "log");
			return false;
		}		
	}
	
	function getInsertSql(){
		$sql = sprintf(SQL_INSERT_WALLPAPER_INFO,
				$this->cpid,
				$this->s_name,
				$this->s_folder,
				$this->s_url,
				$this->mid_url,
				$this->small_url,
				$this->size,
				$this->s_note,
				date("Y-m-d H:i:s"),
				$this->s_author,
				$this->md5,
				$this->mid_md5,
				$this->small_md5,
				$this->s_author,
				$this->type,
				$this->width,
				$this->height);
		return $sql;
	}
	/**
	 * 根据类型或去搜索SQL
	 * @param unknown_type $search_type
	 * @param unknown_type $width
	 * @param unknown_type $height
	 * @param unknown_type $start
	 * @param unknown_type $req_num
	 * @return unknown
	 */
	function getCoolXiuListSql(){
		$sql = $this->getSelectTypeRatioLimitSql();
		return $sql;
	}
	
	function getSearchCoolXiuCountSql(){
		$sql = $this->getTypeRatioCountSql();
		return $sql;
	}
	
	function getSelectLimitSql($start, $count){
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO_BY_LIMIT, $start, $count);
		return $sql;
	}
	function getSelectRatioLimitSql($width, $height, $start, $count){
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO_BY_RATIO_LIMIT, $width, $height, $start, $count);
		return $sql;
	}
	
	function getSelectTypeLimitSql($type, $start, $count){
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO_BY_TYPE_LIMIT, $type, $start, $count);
		return $sql;
	}
	
	function getSelectTypeRatioLimitSql(){
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO_BY_TYPE_RATIO_LIMIT, $this->_search_type, 
																	  $this->_width, $this->_height, 
																	  $this->_start, $this->_limit);
		return $sql;
	}
	
	function getSelectNewLimitSql($start, $count){
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO_BY_NEW_LIMIT, $start, $count);
		return $sql;
	}
	
	function getSelectNewRatioLimitSql($width, $height, $start, $count){
		$sql = sprintf(SQL_SELECT_WALLPAPER_INFO_BY_NEW_RATIO_LIMIT, $width, $height,  $start, $count);
		return $sql;
	}	
	
	function getCountSql(){
		$sql = SQL_COUNT_WALLPAPER;
		return $sql;
	}

	function getRatioCountSql($width, $height){
		$sql = sprintf(SQL_COUNT_WALLPAPER_BY_RATIO, $width, $height);
		return  $sql;
	}
	function getTypeCountSql($type){
		$sql = sprintf(SQL_COUNT_WALLPAPER_BY_TYPE, $type);
		return  $sql;
	}
	function getTypeRatioCountSql(){
		$sql = sprintf(SQL_COUNT_WALLPAPER_BY_TYPE_RATIO, $this->_search_type, $this->_width, $this->_height);
		return $sql;
	}
	function getNewCountSql(){
		
	}
	function getNewRatioCountSql($width, $height){
		$sql = sprintf(SQL_COUNT_WALLPAPER_BY_NEW_RATIO, $width, $height);
		return  $sql;
	}
	
	public function getSelectAlbumsSql()
	{
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_ALBUMS, $this->_width, $this->_height);
		return $sql;
	}
	
	static public function getSelectBannerSql($nWidth, $nHeight)
	{
		$sql = 	sprintf(SQL_SELECT_WALLPAPER_BANNER, $width, $height);
		return $sql;
	}
	
	/**
	 * 根据ID获取图片URL
	 * @param unknown_type $id 
	 * @param unknown_type $type 0:中图 1：大图
	 * @return string
	 */
	public function getSelectUrlByIdSql($id, $type){
		$sql = '';
		if($type == self::YL_WP_MID_URL){
			$sql = 	sprintf(SQL_SELECT_WLLPAPER_MID_URL, $id);
		}
		if($type == self::YL_WP_LARGE_URL){
			$sql = 	sprintf(SQL_SELECT_WLLPAPER_LARGE_URL, $id);
		}		
		return $sql;
	}
	
	private function _getTypeFolder(){
		$folder = "";
		switch($this->type){
			case COOLXIU_SEARCH_WALLPAPER_COMMEN:
				$folder = "commen";
				break;
			case COOLXIU_SEARCH_WALLPAPER_ABSTRACT:
				$folder = "abstract";
				break;
			case COOLXIU_SEARCH_WALLPAPER_PERSON:
				$folder = "woman";
				break;				
			case COOLXIU_SEARCH_WALLPAPER_LANDSCAPE:
				$folder = "landscape";
				break;
			case COOLXIU_SEARCH_WALLPAPER_PLANT:
				$folder = "plant";
				break;
			case COOLXIU_SEARCH_WALLPAPER_KATUN:
				$folder = "cartoon";
				break;
			case COOLXIU_SEARCH_WALLPAPER_ANIMAL:
				$folder = "animal";
				break;
			case COOLXIU_SEARCH_WALLPAPER_OTHER;
				$folder = "other";
				break;
			default:
				$folder = "commen";
				break;
		}
		return $folder;
	}
	
	function upload($cool_xiu_file, $error, $f_name, $f_size, $f_tmp_file){
		if($cool_xiu_file == null){
			Log::write("Wallpaper::upload() cool_xiu_file is not init", "log");				
			return false;
		}
		try{
			$ratio_folder = $this->getRatioFolder();
			$type_folder = $this->_getTypeFolder();
			$rt_folder = $this->getRTFolder($ratio_folder, $type_folder);
			
			$result = $cool_xiu_file->coolXiuUpload(COOLXIU_TYPE_WALLPAPER, $rt_folder, $error, $f_name, $f_size, $f_tmp_file);
			if($result != UPLOAD_ERR_OK){
				Log::write("Wallpaper::upload() failed ERRO NO: ".$result, "log");
				return $result;
			}
			$cool_xiu_file->getCoolxiu($this);
			
			$mid_wallpaper = $this->resizeFile($cool_xiu_file->f_des_dir, $cool_xiu_file->f_des_file, $f_name, 0.5, "mid");
			if($mid_wallpaper == false){
				Log::write("Wallpaper::resizeFile() failed", "log");
				$result = UPLOAD_ERR_IMG_RESIZE;
				return $result;
			}
			$mid_md5 = md5_file($mid_wallpaper);
			$mid_url = $cool_xiu_file->getUrl($mid_wallpaper);
			
			$small_wallpaper = $this->resizeFile($cool_xiu_file->f_des_dir, $cool_xiu_file->f_des_file, $f_name, 0.25, "small");
			if($mid_wallpaper == false){
				Log::write("Wallpaper::resizeFile() failed", "log");
				$result = UPLOAD_ERR_IMG_RESIZE;
				return $result;
			}
			$small_md5 = md5_file($small_wallpaper);
			$small_url = $cool_xiu_file->getUrl($small_wallpaper);
			$this->setWallpaper($mid_url, $small_url, $mid_md5, $small_md5);				
		}catch (Exception $e){
			Log::write("Wallpaper::upload() exception:".$e->getMessage(), "log");
			$result = UPLOAD_ERR_IMG_EXCP;
			return $result;
		}	
		return $result;
	}	
	/**
	 * 复制图片，并按一定的比例缩放
	 * @param unknown_type $f_des_dir		文件夹路径
	 * @param unknown_type $f_des_file		文件完整路径
	 * @param unknown_type $f_name			文件名
	 * @param unknown_type $percent			缩放倍数
	 * @param unknown_type $tmp_add			追加名字后缀
	 * @return boolean|string				返回新的文件路径
	 */
	private function resizeFile($f_des_dir, $f_des_file, $f_name, $percent, $tmp_add){
		$new_wallpaper = $this->getNewFile($f_des_dir, $f_name, $tmp_add);
		$result = img_resize($percent, $f_des_file, $new_wallpaper);
		if(!$result){
			Log::write("Wallpaper::resizeFile() failed", "log");
			return false;
		}
		return $new_wallpaper;
	} 
	
	private function getNewFile($f_dir, $f_name, $add){
		$suff = strrchr($f_name, '.');
		$new_name = substr($f_name, 0, strlen($f_name)- strlen($suff)).'_'.$add.$suff;
		return  $f_dir.$new_name;
	}
}

?>