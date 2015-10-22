<?php
/*��׼�ϴ�����
 * UPLOAD_ERR_INI_SIZE
* ��ֵΪ 1���ϴ����ļ������� php.ini �� upload_max_filesize ѡ�����Ƶ�ֵ��
* UPLOAD_ERR_FORM_SIZE
* ��ֵΪ 2���ϴ��ļ��Ĵ�С������ HTML ���� MAX_FILE_SIZE ѡ��ָ����ֵ��
*
* UPLOAD_ERR_PARTIAL
* ��ֵΪ 3���ļ�ֻ�в��ֱ��ϴ���
*
*UPLOAD_ERR_NO_FILE
*��ֵΪ 4��û���ļ����ϴ���
*
*UPLOAD_ERR_NO_TMP_DIR
*��ֵΪ 6���Ҳ�����ʱ�ļ��С�PHP 4.3.10 �� PHP 5.0.3 ������
*
*UPLOAD_ERR_CANT_WRITE
*��ֵΪ 7���ļ�д��ʧ�ܡ�PHP 5.1.0 ������
*
*����Ϊ�Զ������
*UPLOAD_ERR_Ext
*��ֵΪ101����չ������
*
*UPLOAD_ERR_MOVE
*��ֵΪ102���ϴ��ļ��ƶ�����
*
*UPLOAD_ERR_EXIST
*��ֵΪ103���ϴ��ļ��Ѵ��ڴ���
*
*UPLOAD_ERR_SIZE
*��ֵΪ104���ϴ��ļ�����
*/
//	require_once("../lib/writeLog.lib.php");
	require_once("lib/mysql.lib.php");
	require_once("lib/fileUpload.lib.php");
	require_once ("themes.php");
	require_once ("theme_sql.php");
	
class ThemesManager{
	
	const UPLOAD_ERR_Ext = 101;
	const UPLOAD_ERR_MOVE = 102;
	const UPLOAD_ERR_EXIST = 103;
	const UPLOAD_ERR_SIZE = 104;
	
	private $sql_conn;
	private $f_upload;
	private $db_theme = "db_yl_themes";
	private $rsp_count = 0;		//���󷵻ؽ������
	
	function __construct(){
		
	}
	
	function initUpload(){
		$f_upload = new FileUpload();
	}
	
	function initMySql($db) {
		try {
			$this->db_themes = $db;
			$this->sql_conn = new mysql("localhost", "root", "83141328@lj", $this->db_themes, "", "utf8");
		} catch (Exception $e) {
			Log::write("ThemesManager::InitMySql(".$this->db_themes.") error","log");
			return false;
		}
		return true;
	}
	
	function initMySqlCommit($db){
		try {
			$this->db_theme = $db;
			$this->sql_conn = new mysql("localhost", "root", "83141328@lj", $this->db_theme, "commit", "utf8");
		} catch (Exception $e) {
			Log::write("ThemesManager::InitMySql() error","log");
			return false;
		}
		return true;
	}
	
	function setTheme2DB($theme, $prevs) {
		$result = false;
		$this->sql_conn->commit_start();
		try {
			$sql = sprintf(SQL_INSERT_THEME_INFO,
					$theme->s_name,
					$theme->s_theme_url,
					$theme->size,
					$theme->s_note,
					$theme->s_themes_id,
					date("D M j G:i:s T Y"),
					$theme->s_author,
					$theme->theme_crc,
					$theme->s_author,
					$theme->type);
			if($sql == ""){
				Log::write("ThemesManager::InsertDatabase(themes)sql is empty error", "log");
				return $result;
			}
					
			$this->sql_conn->commit_query($sql);
			if($this->sql_conn->commit_errno()){  
				Log::write("ThemesManager::InsertDatabase(themes)". $sql." error", "log");
				$this->sql_conn->commit_rollback();
				$this->sql_conn->commit_end();
				return $result;
			}
			
			foreach ($prevs as $prev){
				$sql = sprintf(SQL_INSERT_PREV_INFO,
						$prev->s_theme_name,
						$prev->s_prev_url,
						$prev->s_prev_name,
						$prev->prev_size,
						$prev->s_prev_note,
						$prev->prev_crc);
				if($sql == ""){
					Log::write("ThemesManager::InsertDatabase(prev)sql is empty error", "log");
					return $result;
				}
					
				$this->sql_conn->commit_query($sql);
				if($this->sql_conn->commit_errno()){
					Log::write("ThemesManager::InsertDatabase(prev)". $sql." error", "log");
					$this->sql_conn->commit_rollback();
					$this->sql_conn->commit_end();
					return $result;
				}
			}
			
		}catch (Exception $e){
			Log::write("ThemesManager::InsertDatabase()". $sql." error", "log");
			$this->sql_conn->commit_rollback();
			$this->sql_conn->commit_end();
			return $result;
		}

		$this->sql_conn->commit();
		$this->sql_conn->commit_end();
		return $result;
	}
	//����ͨ��
	function testGetThemes($start, $count){
		
		$sql = sprintf(SQL_SELECT_THEME_INFO_BY_LIMIT, $start, $count);
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("ThemesManager::GetThemesByLimit()". $sql." error", "log");
			return false;
		}
		
		$result = $this->sql_conn->fetch_assoc_rows(); 
		if(!$result){
			Log::write("ThemesManager::testGetThemes:db_fetch_rows_assoc() error", "log");
			return false;
		}
		return $result;
	}
	
	function getPrevsByTheme($theme_name, &$prev_imgs){
		
		$sql = sprintf(SQL_SELECT_THEME_PREV_INFO, $theme_name);
		
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("ThemesManager::getPrevsByTheme()". $sql." error", "log");
			return false;
		}
		
		if($this->sql_conn->db_num_rows() > 0){
			$rows = $this->sql_conn->fetch_assoc_rows(); 
			foreach($rows as $row){
				$prev = new Preview();
				$prev->setPrev($row);
			
				array_push($prev_imgs, $prev);				
			}
		}
		return true;
	}
	
	function getThemesByLimit($start, $count){		
		try{
			$sql = sprintf(SQL_SELECT_THEME_INFO_BY_LIMIT, $start, $count);
			$result = $this->sql_conn->query($sql);
			if(!$result){
				Log::write("ThemesManager::GetThemesByLimit()". $sql." error", "log");
				return false;
			}
			$this->rsp_count = $this->sql_conn->db_num_rows();
			
			$themes = array();
			$rows = $this->sql_conn->fetch_assoc_rows();
			foreach($rows as $row){
					
				$theme = new Themes();
				$theme->setTheme($row);
					
				$prev_imgs = array();				
				$result = $this->getPrevsByTheme($row["name"], $prev_imgs);
				if(!$result){
					Log::write("ThemesManager::getThemesByLimit():getPrevsByTheme()false", "log");
					return false;
				}
					
				$theme->a_prev_imgs = $prev_imgs;
				array_push($themes, $theme);
			}	
		}catch(Exception $e){
			Log::write("ThemesManager::getThemesByLimit()exception".$e->getMessage(), "log");
			return false;
		}
		return $themes; 
	}
	
	function getReqThemesCount(){
		return $this->rsp_count;
	}
	
	function getThemesCount(){
		$sql = SQL_COUNT_THEMES;
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("ThemesManager::GetThemesCount()". $sql." error", "log");
			return false;
		}
		list($count) = $this->sql_conn->fetch_row();
		return $count;
	}	
	
	function initFileUpload($file_size_max, $f_type, $overwrite, $s_des_dir){
		if($this->f_upload == null){
			Log::write("ThemesManager::InitFileUpload f_upload is not inited", "log");
			return false;
		}
		$this->f_upload->initData($file_size_max, $f_type, $overwrite, $s_des_dir);
		return true;
	}
	
	function getFileCrc($f_tmp){
		if(!is_file($f_tmp)){
			Log::write("ThemesManager::GetFileCrc(".$f_tmp.") is not a file", "log");
			return 0;
		}
		return crc32(file_get_contents($f_tmp));
	}
	
	function uploadFile($error, $f_name, $f_tmp, $f_size) {		
		$err_no = UPLOAD_ERR_OK;
		$up_error="no";
		
		if ($error == UPLOAD_ERR_OK){
			$uploadfile = strtolower(basename($f_name));
			$f_type = substr(strrchr($f_name,"."),1);//��ȡ�ļ���չ��
			$f_type = strtolower($f_type);
		
			if(!$this->f_upload->checkExt($f_name)){
				$err_no = self::UPLOAD_ERR_Ext;
				$up_error="yes";
			}
		
			if (!$this->f_upload->checkSize($f_size)) {
				$err_no = UPLOAD_ERR_SIZE;
				$up_error="yes";
			}
		
			if (!$this->f_upload->checkExist($uploadfile) && !$this->overwrite){
				$err_no = self::UPLOAD_ERR_EXIST;
				$up_error="yes";
			}
		
			if(!$this->f_upload->makeDesDir()){
				$err_no = self::UPLOAD_ERR_MOVE;
				$up_error="yes";
			}
				
			if($up_error!="yes"){
				
				$f_des = $this->getUploadFile($f_type);
				$f_src = $f_tmp;		
				if(!$this->f_upload->moveFile($f_src, $f_des)){
					$err_no = self::UPLOAD_ERR_MOVE;
				}
			}
		}
		return $err_no;
	}
	
	function uploadTheme($error, $f_name, $f_tmp, $f_size){		
		return $this->uploadFile($error, $f_name, $f_tmp, $f_size);
	}
	
	function uploadPrev($error, $f_name, $f_tmp, $f_size) {
		return $this->uploadFile($error, $f_name, $f_tmp, $f_size);
	}
}
?>