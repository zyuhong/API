<?php
require_once 'lib/CollecterFile.lib.php';
require_once 'lib/DBManager.lib.php';
require_once 'tasks/ring/RingDb.class.php';

class RingFile extends DBManager{
	private $_folder;
	private $collecter_file;
	function __construct(){
		$this->_folder = "";
		$this->collecter_file 	= new CollecterFile();
		$this->connectMySqlCommit();
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
	function ringUpload($error, $f_name, $f_size, $f_tmp_file, $type,
						&$f_url, &$f_md5, &$crc){

		$crc = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
		$f_md5 = md5_file($f_tmp_file);;
		
		$this->_folder = $this->getDestFolder($crc, $type);
		$f_type = substr(strrchr($f_name,"."), 1);
		$f_name = $f_md5.".".$f_type;
		global $g_arr_ring_file;
		$this->collecter_file->setFileParam($g_arr_ring_file, $f_tmp_file, $f_name, $this->_folder);
		
		$result = $this->collecter_file->fileUpload($error, $f_name, $f_size, $f_tmp_file);
		if($result != UPLOAD_ERR_OK){
			Log::write("RingFile::ringUpload():fileUpload() failed ERRO NO: ".$result, "log");
			return $result;
		}
		
		$f_folder = "";
		$this->collecter_file->getFile($f_folder, $f_url, $f_md5);
		return UPLOAD_ERR_OK;
	}
	
	
	function getDestFolder($crc, $type){		
		$ringdb = new RingDb();
		$typename = $ringdb->getRingType($type);
		if(!$typename){
			Log::write("RingFile::getDestFolder():getRingType() failed ", "log");
			return $result;
		}
		
		$str = 'abcdefghijklmnopgrstuvwxyz0123456789';
		$rand = '';
		for ($x=0; $x < 6; $x++){
			$rand .= substr($str, mt_rand(0,strlen($str)-1),1);
		}
	
		$month = date("Ym");
		$t = date("ymdHis")."_";
		global  $g_arr_ring_file;
		return $g_arr_ring_file['dir'].$typename."/".$month.'/'.$t.$crc."_".$rand."/";
	}
	
	function getFileName($file, $f_name){
		$f_type = substr(strrchr($f_name, "."), 1);//获取文件扩展名
		$f_type = strtolower($f_type);
		$md5 = md5_file($file);
		return $md5.'.'.$f_type;
	}
}