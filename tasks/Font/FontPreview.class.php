<?php
require_once 'tasks/Font/FontSql.sql.php';
		
class FontPreview
{
	private $_id;
	private $_width;
	private $_height;
	private $_fname;
	private $_preview_url;
	private $_size;
	private $_md5; 
	
	public function __construct()
	{
		$this->_id 		= '';
		$this->_width	= '';
		$this->_height	= '';
		$this->_fname	= '';
		$this->_preview_url		= '';
		$this->_size	= '';
		$this->_md5		= '';
	}
	
	public function setPreview($id, $width, $height, $fname, $preview_url, $size, $md5)
	{
		$this->_id		= $id;
		$this->_width	= $width;
		$this->_height	= $height;
		$this->_fname	= $fname;
		$this->_preview_url	= $preview_url;
		$this->_size	= $size;
		$this->_md5		= $md5;
	}
	
	public function setPreviewFromDB($row){
		$this->_id		= isset($row['identity'])?$row['identity']:'';
		$this->_width	= isset($row['width'])?$row['width']:0;
		$this->_height	= isset($row['height'])?$row['height']:0;
		$this->_fname	= isset($row['fname'])?$row['fname']:'';
		$this->_preview_url	= isset($row['preview_url'])?$row['preview_url']:'';
		$this->_size	= isset($row['size'])?$row['size']:0;
		$this->_md5		= isset($row['md5'])?$row['md5']:'';
	}
	public function getPreviewFile()
	{
		global $g_arr_root_dir;
		return $g_arr_root_dir['font'].$this->_preview_url;
	}
	
	public function getPreviewFileName()
	{
		return $this->_fname;
	}
	
	public function getInsertPreviewSql()
	{
		$sql = sprintf(SQL_INSERT_FONT_PREVIEW, $this->_id,
				$this->_width, $this->_height, $this->_fname, $this->_preview_url,
				$this->_size, $this->_md5);
		return $sql;
	}
}