<?php
require_once 'lib/CollecterFile.lib.php';
require_once 'lib/WriteLog.lib.php';

class WidgetFile {
	private $_folder;
	private $collecter_file;
	function __construct(){
		$this->_folder = "";
		$this->collecter_file 	= new CollecterFile();
	}	

	/**
	 * 推荐图片文件上传
	 * @param unknown_type $error
	 * @param unknown_type $cpid
	 * @param unknown_type $ratio
	 * @param unknown_type $f_name
	 * @param unknown_type $f_size
	 * @param unknown_type $f_tmp_file
	 * @param unknown_type $f_url
	 * @param unknown_type $f_md5
	 * @param unknown_type $crc
	 * @return Ambigous <boolean, unknown, error, string>|string
	 */
	function widgetUpload($error, $cpid, $ratio,
						&$f_name, $f_size, $f_tmp_file,
						&$f_url, &$f_md5, &$crc){

		$crc = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
		$f_md5 = md5_file($f_tmp_file);

		$f_type = substr(strrchr($f_name,"."), 1);
		$f_name = $crc.'.'.$f_type;
		
		$this->_folder = $this->getDestFolder($cpid, $ratio);
		
		global $g_arr_widget_th_file;
		$this->collecter_file->setFileParam($g_arr_widget_th_file, $f_tmp_file, $f_name, $this->_folder);
		
		$result = $this->collecter_file->fileUpload($error, $f_name, $f_size, $f_tmp_file);
		if($result != UPLOAD_ERR_OK){
			Log::write("WidgetFile::widgetUpload():fileUpload() failed ERRO NO: ".$result, "log");
			return $result;
		}
		
		$f_folder = "";
		$this->collecter_file->getFile($f_folder, $f_url, $f_md5);
		return UPLOAD_ERR_OK;
	}
	
	function getDestFolder($cpid, $ratio){		
		$str = 'abcdefghijklmnopgrstuvwxyz0123456789';
		$rand = '';
		for ($x=0; $x < 6; $x++){
			$rand .= substr($str, mt_rand(0,strlen($str)-1),1);
		}
	
		$month = date('Ym');
		$t = date('ymdHis');
		global $g_arr_widget_th_file;
		return $g_arr_widget_th_file['dir'].$cpid.'/'.$ratio.'/';
	}
	
	public function setFolder($folder){
		$this->_folder = $folder;
	}
}