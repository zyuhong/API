<?php
/**
 * 主题类
 *
 * @author lijie1@yulong.com
 *
 */
require_once 'CoolXiu.class.php';
require_once 'ThemesSql.sql.php';
require_once 'configs/config.php';

class Themes extends CoolXiu{
	
	var $prev_url;			//主缩略图URI
	var $img_num;			//缩略图数量
	var $a_prev_imgs;		//主题缩略图数组

	function Themes(){
		parent::__construct();
		$this->prev_url = "";
		$this->img_num = 0;
		$this->a_prev_imgs = array();
	}

	function setPrevImgs($img_num, $main_url, $prev_imgs){
		$this->prev_url = $main_url;
		$this->img_num = $img_num;
		$this->a_prev_imgs = $prev_imgs;
	}

	function setCoolXiu($row){
		parent::$id 			= isset($row['id'])?$row['id']:'';
		parent::$s_author 		= isset($row['author'])?$row['author']:'';
		parent::$s_name 		= isset($row['name'])?$row['name']:'';
		parent::$s_folder 		= isset($row['folder'])?$row['folder']:'';
		parent::$s_note 		= isset($row['note'])?$row['note']:'';
		parent::$s_url 			= isset($row['url'])?$row['url']:'';
		parent::$insert_time 	= isset($row['insert_time'])?$row['insert_time']:'';
		parent::$md5 			= isset($row['theme_file_md5'])?$row['theme_file_md5']:'';
		parent::$type 			= isset($row['type'])?$row['type']:0;
		parent::$size 			= isset($row['size'])?$row['size']:0;
		parent::$width 			= isset($row['width'])?$row['width']:0;
		parent::$height			= isset($row['height'])?$row['height']:0;
		parent::$download_times	= isset($row['download_times'])?$row['download_times']:0;
	}

	/**
	 * 根据类型获取搜索SQL
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

	static public function getCoolXiuWithIdSql($id)
	{
		$sql = sprintf(SQL_SELECT_THEME_INFO_WITH_ID, $id);
		return $sql;
	}
	
	public function getCoolXiuListForWebSql()
	{
		$sql = sprintf(SQL_SELECT_THEME_FOR_WEB, $this->_search_type, $this->_kernel,
												$this->_width, $this->_height,
												$this->_start, $this->_limit);
		return $sql;
	}
	
	public function getCoolXiuHotListSql()
	{
		$sql = sprintf(SQL_SELECT_THEME_HOT, $this->_search_type, $this->_kernel,
											 $this->_width, $this->_height,
											 $this->_start, $this->_limit);
		return $sql;
	}
	
	public function getCoolXiuLastListSql()
	{
		$sql = sprintf(SQL_SELECT_THEME_LAST, $this->_search_type, $this->_kernel,
											  $this->_width, $this->_height,
											  $this->_start, $this->_limit);
		return $sql;
	}
	
	public function getCoolXiuChoiceListSql()
	{
		$sql = sprintf(SQL_SELECT_THEME_CHOICE, $this->_search_type, $this->_kernel,
				$this->_width, $this->_height,
				$this->_start, $this->_limit);
		return $sql;
	}
	
	public function getCoolXiuHolidayListSql()
	{
		$sql = sprintf(SQL_SELECT_THEME_HOLIDAY, $this->_search_type, $this->_kernel,
				$this->_width, $this->_height,
				$this->_start, $this->_limit);
		return $sql;
	}

	public function getSearchCoolXiuHolidayCountSql()
	{
		$sql = sprintf(SQL_COUNT_THEME_HOLIDAY, $this->_search_type, $this->_kernel,
											     $this->_width, $this->_height);
		return $sql;
	}
	
	
	public function getSearchCoolXiuForWebCountSql()
	{
		$sql = sprintf(SQL_COUNT_THEME_FOR_WEB, $this->_search_type, $this->_kernel,
												$this->_width, $this->_height);
		return $sql;
	}
	/**
	 * 根据类型获取统计SQL
	 * @see CoolXiu::getSearchCoolXiuCountSql()
	 */
	function getSearchCoolXiuCountSql(){
		$sql = $this->getTypeRatioCountSql();
		return $sql;
	}

	function getInsertSql(){
		$sql = sprintf(SQL_INSERT_THEME_INFO,
				$this->id,
				$this->cpid,
				$this->s_name,
				$this->s_folder,
				$this->s_url,
				$this->size,
				$this->s_note,
				date("Y-m-d H:i:s"),
				$this->s_author,
				$this->md5,
				$this->s_author,
				$this->type,
				$this->img_num,
				$this->width,
				$this->height);
		return $sql;
	}

	function getSelectLimitSql($start, $count){
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_LIMIT, $start, $count);
		return $sql;
	}

	function getSelectRatioLimitSql($width, $height, $start, $count){
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_RATIO_LIMIT, $width, $height, $start, $count);
		return $sql;
	}

	function getSelectTypeLimitSql($type, $start, $count){
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_TYPE_LIMIT, $type, $start, $count);
		return $sql;
	}

	function getSelectTypeRatioLimitSql(){
		if($this->_search_type == 2){
			$this->_kernel = 1;
		}
		$strIsCharge = '';
		if($this->_vercode < 18){
			$strIsCharge = ' AND ischarge = 0 ';
		}
		
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_TYPE_RATIO_LIMIT, $this->_search_type, $this->_kernel, 
																	$this->_width, $this->_height, 
																	$strIsCharge,
																	$this->_start, $this->_limit);
		return $sql;
	}

	function getSelectNewLimitSql($start, $count){
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_NEW_LIMIT, $start, $count);
		return $sql;
	}

	function getSelectNewRatioLimitSql($width, $height, $start, $count){
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_NEW_RATIO_LIMIT, $width, $height,  $start, $count);
		return $sql;
	}

	function getCountSql(){
		return SQL_COUNT_THEMES;
	}

	function getRatioCountSql($width, $height){
		$sql = sprintf(SQL_COUNT_THEMES_BY_RATIO, $width, $height);
		return  $sql;
	}
	
	function getTypeCountSql($type){
		$sql = sprintf(SQL_COUNT_THEMES_BY_TYPE, $type);
		return  $sql;
	}
	
	function getTypeRatioCountSql(){
		if($this->_search_type == 2){
			$this->_kernel = 1;
		}
		
		$strIsCharge = '';
		if($this->_vercode < 18){
			$strIsCharge = ' AND ischarge = 0 ';
		}
		
		$sql = sprintf(SQL_COUNT_THEMES_BY_TYPE_RATIO, $this->_search_type, $this->_kernel, 
														$this->_width, $this->_height, 
														$strIsCharge);
		return $sql;
	}
	
	function getNewCountSql(){
		return SQL_COUNT_THEMES_BY_NEW;
	}
	
	function getNewRatioCountSql($width, $height){
		$sql = sprintf(SQL_COUNT_THEME_BY_NEW_RATIO, $width, $height);
		return  $sql;
	}
	
	function getSelectUrlByIdSql($id, $type){
		$sql = 	sprintf(SQL_SELECT_THEMES_DL_URL, $id);
		return $sql;
	}
	
	public function getSelectAlbumsSql()
	{
		$sql = 	sprintf(SQL_SELECT_THEME_ALBUMS, $this->_kernel, $this->_width, $this->_height);
		return $sql;
	}
	
	public function getCoolXiuDetailsSql($strId, $nWidth, $nHeight)
	{
		if($nWidth == 0 && $nHeight == 0){
			$sql = 	sprintf(SQL_SELECT_THEMES_DETAILS_URL, $strId);
		}else{
			$sql = 	sprintf(SQL_SELECT_THEMES_DETAILS_URL_RATIO, $strId, $nWidth, $nHeight);
		}
		return $sql;
	}

	static public function getCheckIsChargeSql($strId)
	{
		$sql = 	sprintf(SQL_CHECK_THEME_ISCHARGE, $strId);
		return $sql;
	}
	
	static public function getThemeListSql($width, $height, $kernel, $start = 0, $limit = 10)
	{
		$sql = 	sprintf(SQL_SELECT_THEME_LIST, $width, $height, $kernel, $start, $limit);
		return $sql;
	}
	
	static public function getCountThemeListSql($width, $height, $kernel)
	{
		$sql = 	sprintf(SQL_COUNT_THEME_LIST, $width, $height, $kernel);
		return $sql;
	}
	
	static public function getSelectBannerSql($width, $height, $kernel)
	{
		$sql = 	sprintf(SQL_SELECT_THEMES_BANNER, $width, $height, $kernel);
		return $sql;
	}
	
	private function _getTypeFolder(){
		$folder = "";
		switch($this->type){
			case 0:{
				$folder = 'large';
			}break;
			case 1:{
				$folder = 'simple';
			}break;
			default:break;
		}
		return $folder;
	}

	function upload($cool_xiu_file, $error, $f_name, $f_size, $f_tmp_file){
		if($cool_xiu_file == null){
			Log::write("Themes::upload() cool_xiu_file is not init", "log");
			return false;
		}
			
		$ratio_folder = $this->getRatioFolder();
		$type_folder = $this->_getTypeFolder();
		$rt_folder = $this->getRTFolder($ratio_folder, $type_folder);
			
		$result = $cool_xiu_file->coolXiuUpload(COOLXIU_TYPE_THEMES, $rt_folder,
				$error, $f_name, $f_size, $f_tmp_file);
		if($result != UPLOAD_ERR_OK){
			Log::write("Themes::upload() failed ERRO NO: ".$result, "log");
			return $result;
		}
		$cool_xiu_file->getCoolxiu($this);
		return $result;
	}	
}
?>