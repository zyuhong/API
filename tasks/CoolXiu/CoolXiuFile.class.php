<?php
	require_once 'CoolXiu.class.php';
	require_once 'configs/config.php';
	require_once 'lib/fileUpload.lib.php';
	
	defined("UPLOAD_FILE_ERR_LOGIN") 
		OR define("UPLOAD_FILE_ERR_LOGIN", 			1001);
	defined("UPLOAD_FILE_ERR_NAME") 
		OR  define("UPLOAD_FILE_ERR_NAME", 			1002);
	defined("UPLOAD_FILE_ERR_FILE_INFO")
		OR define("UPLOAD_FILE_ERR_FILE_INFO", 		1003);
	defined("UPLOAD_FILE_ERR_FILE_UPLOAD")
		OR define("UPLOAD_FILE_ERR_FILE_UPLOAD", 	1004);
	defined("UPLOAD_FILE_ERR_DB") 			
		OR define("UPLOAD_FILE_ERR_DB", 			1005);
	defined("UPLOAD_FILE_ERR_DB_CONN")
		OR define("UPLOAD_FILE_ERR_DB_CONN", 		1006);
	defined("UPLOAD_FILE_ERR_LOGIN")
		OR define("UPLOAD_FILE_ERR_LOGIN", 			1007);
	defined("UPLOAD_FILE_ERR_EXCPTION")
		OR define("UPLOAD_FILE_ERR_EXCPTION", 		1008);
	
	defined('UPLOAD_ERR_Ext')
		OR define('UPLOAD_ERR_Ext', 				101);
	defined('UPLOAD_ERR_MOVE')
		OR define('UPLOAD_ERR_MOVE',				102);
	defined('UPLOAD_ERR_EXIST')
		OR define('UPLOAD_ERR_EXIST', 				103);
	defined('UPLOAD_ERR_SIZE')
		OR define('UPLOAD_ERR_SIZE', 				104);
	defined('UPLOAD_ERR_IMG_RESIZE')
		OR define('UPLOAD_ERR_IMG_RESIZE', 			105);
	defined('UPLOAD_ERR_IMG_EXCP')
		OR define('UPLOAD_ERR_IMG_EXCP', 			106);
	
	defined('UPLOAD_ERR_FILE_NAME')
		OR define('UPLOAD_ERR_FILE_NAME', 			107);
	defined('UPLOAD_ERR_FILE_SIZE')
		OR define('UPLOAD_ERR_FILE_SIZE', 			108);
	defined('UPLOAD_ERR_FILE_TMP')
		OR define('UPLOAD_ERR_FILE_TMP', 			109);
	class CoolXiuFile{
		var $f_md5;					//上传文件MD5
		var $f_des_folder;			//目标文件夹名称
		var $f_des_dir;				//目标路径
		var $f_des_file;			//目标文件URI
		var $f_url;					//文件相对URL
		var $cool_xiu_dir;			//coolpadxiu根目录
		var $f_size_max;			//上传文件大小最大值 
		var $f_type;				//上传文件类型
		var $overwrite;
		var $file_upload;
			
		function __construct(){
			$this->f_md5 = "";
			$this->f_des_folder = "";
			$this->f_des_dir  = "";
			$this->f_des_file = "";
			$this->f_url = "";
			$this->cool_xiu_dir = "";
			$this->f_size_max = 0;
			$this->f_type = 0;
			$this->overwrite = false;
			
			$this->file_upload = new FileUpload();
		}
		
		function getCoolxiu($coolxiu){			
			$coolxiu->s_folder 	= $this->f_des_folder;
			$coolxiu->s_url		= $this->f_url;
			$coolxiu->md5		= $this->f_md5;	
		}
			
		/**
		 * @param unknown_type $xiu_type 	0: 主题 1：主题缩略图 2：壁纸
		 * @param unknown_type $rt_folder	分辨率与类型的组合文件夹路径
		 * @param unknown_type $error		文件上传错误信息
		 * @param unknown_type $f_name		文件名
		 * @param unknown_type $f_size		文件大小
		 * @param unknown_type $f_tmp_file	文件缓存	
		 * @return boolean|Ambigous <error, string>	返回错误类型
		 */
		function coolXiuUpload($xiu_type, 
								$rt_folder,
								$error, 
								$f_name, 
								$f_size, 
								$f_tmp_file) {

			if(!$this->file_upload){
				Log::write("CoolXiuFile::oolXiuUpload(f_theme_max_size) failed", "log");
				return false;
			}
			
			$this->setCoolXiuParam($xiu_type, $rt_folder, $f_tmp_file, $f_name);
			
			$this->file_upload->initData($this->f_size_max, 
										$this->f_type, 
										$this->overwrite, 
										$this->f_des_dir);
			
			$result = $this->uploadFile($error, $f_name, $f_tmp_file, $f_size, $this->f_des_file);
			if($result != UPLOAD_ERR_OK){
				Log::write("CoolXiuFile::coolXiuUpload():uploadFile() failed ERRO NO: ".$result, "log");
				return $result;
			}			
			return $result;
		}
		
		/**
		 * 文件上传
		 * @param unknown_type $error 	上传错误信息
		 * @param unknown_type $f_name	文件名
		 * @param unknown_type $f_tmp	缓存文件
		 * @param unknown_type $f_size	文件大小
		 * @param unknown_type $f_des_file	目标文件
		 * @return error no					返回错误代码
		 */
		function uploadFile($error, $f_name, $f_tmp, $f_size, $f_des_file) {
			$err_no = UPLOAD_ERR_OK;
			$up_error="no";
		
			if ($error == UPLOAD_ERR_OK){
				$f_type = substr(strrchr($f_name,"."),1);//获取文件扩展名
				$f_type = strtolower($f_type);
		
				if(!$this->file_upload->checkExt($f_name)){
					$err_no = UPLOAD_ERR_Ext;
					$up_error="yes";
				}
		
				if (!$this->file_upload->checkSize($f_size)) {
					$err_no = UPLOAD_ERR_SIZE;
					$up_error="yes";
				}
		
				$uploadfile = strtolower(basename($f_name));
				if (!$this->file_upload->checkExist($uploadfile) && !$this->overwrite){
					$err_no = UPLOAD_ERR_EXIST;
					$up_error="yes";
				}
		
				if(!$this->file_upload->makeDesDir()){
					$err_no = UPLOAD_ERR_MOVE;
					$up_error="yes";
				}
		
				if($up_error!="yes"){
					if(is_uploaded_file($f_tmp)){
						$file_dest = iconv('utf-8', 'gb2312', $f_des_file);
						if(!$this->file_upload->moveFile($f_tmp, $file_dest)){
							$err_no = UPLOAD_ERR_MOVE;
						}
					}
				}
			}
			return $err_no;
		}
		/**
		 * 设置文件基本属性：MD5\URL\文件夹
		 * @param unknown_type $xiu_type
		 * @param unknown_type $f_tmp_file
		 * @param unknown_type $f_name
		 */
		function setCoolXiuParam($xiu_type, $folder, $f_tmp_file, $f_name){
			$this->getCoolxiuConfig($xiu_type, $this->cool_xiu_dir, $this->f_size_max, $this->f_type, $this->overwrite);				
							
			$this->f_md5 			= md5_file($f_tmp_file);
			if ($xiu_type != COOLXIU_TYPE_PREV){
				$crc = sprintf("%u", crc32(file_get_contents($f_tmp_file)));
				$this->f_des_folder = $this->getfolder($crc);
				$this->f_des_dir 		= $this->getDesDir($this->cool_xiu_dir, $folder, $this->f_des_folder);				
			}
			$this->f_des_file 		= $this->getDesFile($this->f_des_dir, $f_name);
			$this->f_url 			= $this->getUrl($this->f_des_file);			
		}
		
		/**
		 * 根据上传文件类型获取相关配置信息
		 * 
		 * @param unknown_type $xiu_type 		文件类型 ： 0 ：主题 1：主题预览 2：壁纸
		 * @param unknown_type $cool_xiu_dir	主题秀的根目录
		 * @param unknown_type $f_size_max		约定的文件大小最大值
		 * @param unknown_type $f_type  		约定的文件类型
		 * @param unknown_type $overwrite		是否可以覆盖
		 */
		function getCoolxiuConfig($xiu_type, &$cool_xiu_dir, &$f_size_max, &$f_type, &$overwrite){
			switch($xiu_type){
				case 0:
					{
						global $g_arr_themes_file;
						$cool_xiu_dir 	= $g_arr_themes_file['dir'];
						$f_size_max 	= $g_arr_themes_file['max_size'];
						$f_type 		= $g_arr_themes_file['file_type'];
						$overwrite 		= true;
					}break;
				case 1:
					{
						global $g_arr_prev_file;
						$cool_xiu_dir 	= $g_arr_prev_file['dir'];
						$f_size_max 	= $g_arr_prev_file['max_size'];
						$f_type 		= $g_arr_prev_file['file_type'];
						$overwrite 		= true;
					}break;
				case 2:
					{
						global $g_arr_wallpaper_file;
						$cool_xiu_dir 	= $g_arr_wallpaper_file['dir'];
						$f_size_max 	= $g_arr_wallpaper_file['max_size'];
						$f_type 		= $g_arr_wallpaper_file['file_type'];
						$overwrite 		= true;
					}break;
			}			
		}
		
		/**
		 * 获取存放主题文件的文件夹名称，作为与预览图关联的键值
		 * 获取字段生成的ID有点麻烦，文件夹是用CRC+时间+随机数生成的
		 */
		function getFolder($md5){
			$str = 'abcdefghijklmnopgrstuvwxyz0123456789';
			$rand = '';
			for ($x=0; $x < 6; $x++){
				$rand .= substr($str, mt_rand(0,strlen($str)-1),1);
			}
				
			$t = date("ymdHis");
			return $t."_".$md5."_".$rand;
		}
		/**
		 * 获取目标目录,根据主题名称_时间_随机字符生成
		 * $theme_name：主题名称
		 */
		function getDesDir($cool_xiu_dir, $folder, $f_folder){
			$f_des_dir = $cool_xiu_dir.$folder.'/'.$f_folder."/";
			//Log::write("des dir name:".$this->f_des_dir, "log");
			return $f_des_dir;
		}
		/**
		 * 获取目标文件URI
		 * $file_name:上传文件名
		 */
		function getDesFile($f_des_dir, $f_name){
			$f_des_file = $f_des_dir.$f_name;
			//Log::write("file name:".$this->f_des_file, "log");
			return  $f_des_file;
		}		
		
		/**
		 * 获取目标文件URL的相对路径
		 * $f_des_file:上传文件目标路径
		 */
		function getUrl($f_des_file){
			$f_url = substr($f_des_file, 2, strlen($f_des_file));
			//Log::write("file url is:".$f_url, "log");
			return $f_url;
		}
	}	
?>