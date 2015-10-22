<?php
require_once 'tasks/ring/RingSql.sql.php';
require_once 'configs/config.php';
class Ring{
	private $_id;
	private $_type;
	private $_name;
	private $_note;
	private $_fname;
	private $_url;
	private $_size;
	private $_md5;
	private $_insert_time;
	private $_insert_user;
	
	function __construct(){
		$this->_id		= "";
		$this->_type	= "";
		$this->_name	= "";
		$this->_note	= "";
		$this->_fname	= "";
		$this->_url		= "";
		$this->_size	= "";
		$this->_md5		= "";
		$this->_insert_time	= date("Y-m-d H:i:s");
		$this->_insert_user	= "admin";		
	}

	function setRing($id, $type, $name, $note, $fname, $url, $size, $md5, $insert_user){
		$this->_id		= $id;
		$this->_type	= $type;
		$this->_name	= $name;
		$this->_note	= $note;
		$this->_fname	= $fname;
		$this->_url		= $url;
		$this->_size	= $size;
		$this->_md5		= $md5;
		$this->_insert_user	= $insert_user;
	}
	
	function setRingFromDB($row){
		$this->_id		= $row['identity'];
		$this->_type	= $row['type'];
		$this->_url		= $row['url'];
		$this->_fname	= $row['fname'];
	}
	
	function getRingFile(){
		global $g_arr_root_dir;
		return $g_arr_root_dir['ring'].$this->_url;				
	}
	function getRingFileName(){
		return $this->_fname;
	}
	
	function getSelectRingTypeSql($type){
		$sql = sprintf(SQL_SELECT_RING_TYPE_NAME, $type);
		return $sql;
	}
	
	function getInsertRingSql(){
		$sql = sprintf(SQL_INSERT_RING, $this->_id, $this->_type,
										$this->_name, $this->_note, 
										$this->_fname, $this->_url,
										$this->_size, $this->_md5,
										$this->_insert_time,
										$this->_insert_user);
		return $sql;
	}
	
	static function getSelectRingByLimitSql($type, $start, $limit, $vercode = 0, $subtype = 0){
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND ischarge = 0 ';
		}
		$strSubType = '';
		if($subtype != 0 && $type == 0 ){
			$strSubType =  ' AND subtype =  '.$subtype;
		}
		
		$sql = sprintf(SQL_SELECT_RING_BY_LIMIT, $type, $strIsCharge, $strSubType, $start, $limit);
		return $sql;
	}
	
	static public function getRingLastListSql($type, $subtype, $start, $limit)
	{
		$strSubType = '';
		if($subtype != 0 && $type == 0){
			$strSubType =  ' AND subtype =  '.$subtype;
		}
		$sql = sprintf(SQL_SELECT_RING_LAST, $type, $strSubType, $start, $limit);
		return $sql;
	}
	
	static public function getRingListForWebSql($type, $subtype, $start, $limit)
	{
		$strSubType = '';
		if($subtype != 0 && $type == 0){
			$strSubType =  ' AND subtype =  '.$subtype;
		}
		$sql = sprintf(SQL_SELECT_RING_FOR_WEB, $type, $strSubType, $start, $limit);
		return $sql;
	}
	
	static public function getRingHotListSql($type, $subtype, $start, $limit)
	{
		$strSubType = '';
		if($subtype != 0){
			$strSubType =  ' AND subtype =  '.$subtype;
		}
		$sql = sprintf(SQL_SELECT_RING_HOST, $type, $strSubType, $start, $limit);
		return $sql;
	}
	
	static function getCountRingSql($type, $subtype, $vercode){
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND ischarge = 0 ';
		}
		$strSubType = '';
		if($subtype != 0 && $type == 0){
			$strSubType =  ' AND subtype =  '.$subtype;
		}
		$sql = sprintf(SQL_COUNT_RING, $type, $strSubType, $strIsCharge);
		return $sql;
	}
	function getSelectRingByIDSql($id){
		$sql = sprintf(SQL_SELECT_RING_BY_ID, $id);
		return $sql;
	}
	
	static public function getSelectSrcWithIDSql($id){
		$sql = sprintf(SQL_SELECT_RING_WITH_ID, $id);
		return $sql;
	}
	
	static public function getSelectAlbumsSql(){
		$sql = SQL_SELECT_RING_ALBUMS;
		return $sql;
	}
	
	static public function getSelectBannerSql(){
		$sql = SQL_SELECT_RING_BANNER;
		return $sql;
	}
}