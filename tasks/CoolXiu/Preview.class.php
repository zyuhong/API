<?php
require_once 'CoolXiu.class.php';
require_once 'PreviewSql.sql.php';

/**
 * 主题缩略图类，主要是为了方便数据库操作。
 *
 * @author lijie1@yulong.com
 *
 */
class Preview extends CoolXiu{
	function Preview(){				
			parent::__construct();
	}

	function setCoolXiu($row){
		parent::$id 		= $row['id'];
		parent::$s_folder 	= $row['theme_folder'];
		parent::$s_note 	= $row['note'];
		parent::$s_url 		= $row['prev_url'];
		parent::$md5 		= $row['prev_file_md5'];
		parent::$size 		= $row['size'];
		parent::$type		= $row['prev_type'];
	}
	
	function getInsertSql(){
		$sql = sprintf(SQL_INSERT_THEME_PREV_INFO,
				$this->id,
				$this->s_folder,
				$this->s_url,
				$this->s_name,
				$this->size,
				$this->s_note,
				$this->md5,
				$this->type);
		return $sql;
	}
	function getCoolXiuListSql(){		
	}
	function getSearchCoolXiuCountSql(){
	}
	function getSelectLimitSql($start, $count){		
	}
	function getSelectRatioLimitSql($width, $height, $start, $count){		
	}	
	function getSelectTypeLimitSql($type, $start, $count){
	}
	function getSelectTypeRatioLimitSql(){
	}
	function getSelectNewLimitSql($start, $count){
	}
	function getSelectNewRatioLimitSql($width, $height, $start, $count){
	}
	function getCountSql(){
	}
	function getRatioCountSql($width, $height){
	}
	function getTypeRatioCountSql(){
	}
	function getTypeCountSql($type){
	}
	function getNewCountSql(){
	}
	function getNewRatioCountSql($width, $height){
	}
	function getSelectUrlByIdSql($id, $type){
		
	}
	function getSelectAlbumsSql(){
		
	}
	function upload($cool_xiu_file, $error, $f_name, $f_size, $f_tmp_file){
		if($cool_xiu_file == null){
			Log::write("Preview::upload() cool_xiu_file is not init", "log");
			return false;
		}
		
		$result = $cool_xiu_file->coolXiuUpload(COOLXIU_TYPE_PREV, null, $error, $f_name, $f_size, $f_tmp_file);
		if($result != UPLOAD_ERR_OK){
			Log::write("Preview::upload() failed ERRO NO: ".$result, "log");
			return $result;
		}
		$cool_xiu_file->getCoolxiu($this);
		return $result;
	}
}