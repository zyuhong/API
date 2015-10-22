<?php
require_once 'tasks/Font/FontSql.sql.php';
		
class Font
{
	private $_id;
	private $_name;
	private $_language;
	private $_width;
	private $_height;
	private $_note;
	private $_author;
	private $_designer;
	private $_version;
	private $_uiversion;
	private $_fname;
	private $_url;
	private $_preview_url;
	private $_size;
	private $_md5; 
	private $_insert_time;
	private $_insert_user;
	
	public $arrPreview;
	
	public function __construct()
	{
		$this->_id 		= '';
		$this->_language= '';
		$this->_name	= '';
		$this->_width	= '';
		$this->_height	= '';
		$this->_note	= '';
		$this->_author   = '';
		$this->_designer = '';
		$this->_version	 = '';
		$this->_uiversion= '';
		$this->_fname	= '';
		$this->_url		= '';
		$this->_size	= '';
		$this->_md5		= '';
		$this->_insert_time = date("Y-m-d H:i:s");
		$this->_insert_user = ''; 
		$this->arrPreview = array();
	}
	
	public function setPreviews($arrPreview)
	{
		$this->arrPreview = $arrPreview;
	}

	public function setFont($id, $language, $name, 
					 $note,  $author, $designer, $version, $uiversion,
					 $fname, $url, $size, $md5, $insert_user)
	{
		$this->_id		= $id;
		$this->_language= $language;
		$this->_name	= $name;
		$this->_note	= $note;
		$this->_author   = $author;
		$this->_designer = $designer;
		$this->_version	 = $version;
		$this->_uiversion= $uiversion;
		$this->_fname	= $fname;
		$this->_url		= $url;
		$this->_size	= $size;
		$this->_md5		= $md5;
		$this->_insert_user	= $insert_user;
	}
	
	public function setFontFromDB($row){
		$this->_id		= isset($row['identity'])?$row['identity']:'';
		$this->_name	= isset($row['name'])?$row['name']:'';
		$this->_language= isset($row['language'])?$row['language']:'';
		$this->_width	= isset($row['width'])?$row['width']:0;
		$this->_height	= isset($row['height'])?$row['height']:0;

		$this->_author   = isset($row['author'])?$row['author']:0;
		$this->_designer = isset($row['designer'])?$row['designer']:0;
		$this->_version	 = isset($row['version'])?$row['version']:0;
		$this->_uiversion= isset($row['uiversion'])?$row['uiversion']:0;
		$this->_note	= isset($row['note'])?$row['note']:'';
		$this->_fname	= isset($row['fname'])?$row['fname']:'';
		$this->_url		= isset($row['url'])?$row['url']:'';
		$this->_preview_url	= isset($row['preview_url'])?$row['preview_url']:'';
		$this->_size	= isset($row['size'])?$row['size']:0;
		$this->_md5		= isset($row['md5'])?$row['md5']:'';
	}
	public function getFontFile()
	{
		global $g_arr_root_dir;
		return $g_arr_root_dir['font'].$this->_url;
	}
	
	public function getFontFileName()
	{
		return $this->_fname;
	}
	
	public function getSelectFontRatioSql()
	{
		$sql = SQL_SELECT_FONT_RATIO;
		return $sql;
	}
	
	public function getInsertFontSql()
	{
		$sql = sprintf(SQL_INSERT_FONT, $this->_id, $this->_language,
				$this->_name, $this->_fname, $this->_url,
				$this->_author, $this->_designer, $this->_version, $this->_uiversion,
				$this->_note, $this->_size, $this->_md5, $this->_insert_time, $this->_insert_user);
		return $sql;
	}
	
	static public function getSelectFontByLimitSql($start, $limit, $vercode = 0)
	{
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND font.ischarge = 0 ';
		}
		
		$sql = sprintf(SQL_SELECT_FONT_LIMIT, $strIsCharge, $start, $limit);
		return $sql;
	}
	
	static public function getFontListForWebSql($start, $limit)
	{
		$sql = sprintf(SQL_SELECT_FONT_FOR_WEB, $start, $limit);
		return $sql;
	}
	
	static public function getFontLastListSql($start, $limit)
	{
		$sql = sprintf(SQL_SELECT_FONT_LAST, $start, $limit);
		return $sql;
	}
	
	static public function getFontHotListSql($start, $limit)
	{
		$sql = sprintf(SQL_SELECT_FONT_HOT, $start, $limit);
		return $sql;
	}
	
	public function getSelectFontPreviewByIdSql($id){
		$sql = sprintf(SQL_SELECT_FONT_PREVIEW_BY_ID, $id);
		return $sql;
	}
	public function getSelectAllFontByLimitSql($start, $limit)
	{
		$sql = sprintf(SQL_SELECT_ALL_FONT_LIMIT, $start, $limit);
		return $sql;
	}
	
	static public function getCountFontSql($vercode = 0)
	{
		$strIsCharge = '';
		if($vercode < 18){
			$strIsCharge = ' AND font.ischarge = 0 ';
		}
		
		$sql = sprintf(SQL_COUNT_FONT, $strIsCharge);
		return $sql;
	}
	
	public function getSelectFontWithIdSql($id)
	{
		$sql = sprintf(SQL_SELECT_FONT_WITH_ID, $id);
		return $sql;
	}
	
	static public function getSelectFontByIDSql($id)
	{
		$sql = sprintf(SQL_SELECT_FONT_BY_ID, $id);
		return $sql;
	}
	
	static public function getSelectBannerSql()
	{
		$sql = SQL_SELECT_FONT_BANNER;
		return $sql;
	}
}