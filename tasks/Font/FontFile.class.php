<?php
require_once 'lib/CollecterFile.lib.php';

class FontFile {
	private $_folder;
	private $collecter_file;
	function __construct(){
		$this->_folder = "";
		$this->collecter_file 	= new CollecterFile();
	}	
	/**
	 * Ring文件上传
	 * @param unknown_type $error
	 * @param unknown_type $f_name
	 * @param unknown_type $f_size
	 * @param unknown_type $f_tmp_file
	 * @param unknown_type $f_folder
	 * @param unknown_type $f_url
	 * @param unknown_type $f_md5
	 * @return boolean
	 */
	function fontUpload($error, $language,
						$f_name, $f_size, $f_tmp_file,
						&$f_url, &$f_md5, &$crc){

		$crc = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
		$f_md5 = md5_file($f_tmp_file);;
		
		$this->_folder = $this->getDestFolder($language, $crc);
		
// 		$f_type = substr(strrchr($f_name,"."), 1);
// 		$f_name = $f_md5.".".$f_type;
		
		global $g_arr_font_file;
		$this->collecter_file->setFileParam($g_arr_font_file, $f_tmp_file, $f_name, $this->_folder);
		
		$result = $this->collecter_file->fileUpload($error, $f_name, $f_size, $f_tmp_file);
		if($result != UPLOAD_ERR_OK){
			Log::write("FontFile::fontUpload():fileUpload() failed ERRO NO: ".$result, "log");
			return $result;
		}
		
		$f_folder = "";
		$this->collecter_file->getFile($f_folder, $f_url, $f_md5);
		return UPLOAD_ERR_OK;
	}
	
	function getDestFolder($languag, $crc){		
		$str = 'abcdefghijklmnopgrstuvwxyz0123456789';
		$rand = '';
		for ($x=0; $x < 6; $x++){
			$rand .= substr($str, mt_rand(0,strlen($str)-1),1);
		}
	
		$month = date('Ym');
		$t = date('ymdHis');
		global $g_arr_font_file;
		return $g_arr_font_file['dir'].$languag.'/'.$month.'/'.$t."_".$crc."_".$rand."/";
	}
	
	function uploadPreview($error, $f_size, $f_tmp_file,
						   &$f_name, &$f_url, &$f_md5){
		
		$crc = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
		$f_type = substr(strrchr($f_name,"."), 1);
		$f_name = $crc.'.'.$f_type;
		
		global $g_arr_font_file;
		$this->collecter_file->setFileParam($g_arr_font_file, $f_tmp_file, $f_name, $this->_folder);
		
		$result = $this->collecter_file->fileUpload($error, $f_name, $f_size, $f_tmp_file);
		if($result != UPLOAD_ERR_OK){
			Log::write("FontFile::uploadPreview():fileUpload() failed ERRO NO: ".$result, "log");
			return $result;
		}
		$f_folder= '';
		$this->collecter_file->getFile($f_folder, $f_url, $f_md5);
		return UPLOAD_ERR_OK;	
	}
	
	public function setFolder($folder){
		$this->_folder = $folder;
	}
}