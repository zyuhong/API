<?php
/**
 * 根据Form表单的Input值获取文件信息
 * 
 * @param unknown_type $input 
 * @param unknown_type $key		
 * @param unknown_type $f_name
 * @param unknown_type $f_size
 * @param unknown_type $f_tmp_file
 * @return string|boolean
 */

require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';

function  get_file_info($input, $key, &$f_name, &$f_size, &$f_tmp_file){
	if ($key < 0){
		$f_name 	= $_FILES[$input]['name'];
		$f_size 	= $_FILES[$input]['size'];
		$f_tmp_file = $_FILES[$input]['tmp_name'];
	}else{
		$f_name 	= $_FILES[$input]['name'][$key];
		$f_size 	= $_FILES[$input]['size'][$key];
		$f_tmp_file = $_FILES[$input]['tmp_name'][$key];
	}
		
	$result = UPLOAD_ERR_OK;
	if($f_name == null){
		Log::write("f_name is empty", "log");
		$result = UPLOAD_ERR_FILE_NAME;
		return $result;		//返回错误代码 107:主题文件名为空
	}
		
	if($f_size == null){
		Log::write("f_size is empty", "log");
		$result = UPLOAD_ERR_FILE_SIZE;
		return $result;	//返回错误代码 108:文件大小为空
	}
		
	if($f_tmp_file == null){
		Log::write("f_tmp_file is empty", "log");
		$result = UPLOAD_ERR_FILE_TMP;
		return $result;		//返回错误代码 109:文件上传缓存失败
	}
	return $result;
}

/**
 * 压缩图像尺寸函数 imageresize
 * @perem $src_name 原始图像名称（包含路径名）
 * @perem $percent  压缩比例（如0.5为新图像宽高为原图一半）
 * @return $new_name 返回压缩后图像名称（包含路径名）
 * @caution：调用函数前请做好类型检查，尽限于gif、jpeg、jpg和png格式图像
 */
function img_resize( $resize_percent, $src_file, $des_file){
	try{

		list($src_width, $src_height) = getimagesize($src_file);
		$new_width = $src_width * $resize_percent;
		$new_height = $src_height * $resize_percent;
			
		$new_image = imagecreatetruecolor($new_width, $new_height);
		$suff = strrchr($src_file, '.');
		switch ($suff){
			case ".jpg":
			case ".jpeg":
				{
					$src_image = imagecreatefromjpeg($src_file);
					imagecopyresized($new_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height);
					imagejpeg($new_image, $des_file, 75);
				}break;
			case ".png":
				{
					$src_image = imagecreatefrompng($src_file);
					imagecopyresized($new_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height);
					imagepng($new_image,$des_file);
				}break;
			case ".gif":
				{
					$src_image = imagecreatefromgif($src_file);
					imagecopyresized($new_image, $src_image, 0, 0, 0, 0, $new_width, $new_height, $src_width, $src_height);
					imagegif($new_image,$src_file);
				}break;
			default:
				{
					return false;
				}break;
		}
	}catch (Exception $e){
		Log::write("ImgResize::resize() exception: ".$e->getMessage(), "log");
		return false;
	}
	return true;
}
/**
 * 根据URL跳转下载，要求path为文件的完整URL
 * @param unknown_type $path
 */
function url_skip_download($path){
	$pos = strrpos($path, '/');
	$name = substr($path, $pos + 1,  strlen($path) - $pos -1);
	$name = urlencode($name);
	
	$name = str_replace("+", "%20", $name);
	$path_tmp = substr($path, 0, $pos + 1);
	
	header('content-type: application/file');
	header('content-disposition: attachment; filename='.$name);
	header('location: '.$path_tmp.$name);
}
/**
 * 根据URL跳转下载，并实现伪referer，要求path为文件的完整URL
 * @param unknown_type $path
 */
function url_skip_referer($path){
	
	$arrUrl = parse_url($path);
	$errstr = '';
	$errno	= 0;
	$domain = $arrUrl['host'];
	$getpath = $arrUrl['path'];
	Log::write('url_skip_referer domain:'.$domain, "log");	
	Log::write('url_skip_referer getpath:'.$getpath, "log");
	
	$content = fsockopen($domain, 80, $errno, $errstr, 30);
	if (!$content){
		Log::write('url_skip_referer failed:', "log");
		return false;
	}
	$header = 'GET '.$getpath.' HTTP/1.1\r\n';
	$header .= 'Accept: */*\r\n';
	$header .= 'Accept-Language: zh-cn\r\n';
	$header .= 'Accept-Encoding: gzip, deflate\r\n';
	$header .= 'Host: '.$domain.' \r\n';
	$header .= 'Referer: '.$domain.' \r\n';
	/*以下头信息域可以省略
	$header .= "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; InfoPath.2; .NET CLR 2.0.50727; .NET4.0C; .NET4.0E; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729) \r\n";
	$header .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5 \r\n";
	$header .= "Accept-Language: en-us,en;q=0.5 ";
	$header .= "Accept-Encoding: gzip,deflate\r\n";
	*/
	$header .= 'Connection: Keep-Alive\r\n';
	
	fwrite($content, $header);
// 	fputs($content, $header);
	$tp = null;
	while(!feof($content)) {
		$tp .= fgets($content, 128);
		Log::write('url_skip_referer tp:'.$tp, "log");
// 		if (strstr($tp, '200 OK')){ 
// 			header("Location:$url");
// 		}
	}
	echo $tp;
	fclose($content);
}

/**
 * 对下载文件跳转处理
 * @param unknown_type $name
 * @param unknown_type $path
 */
function file_download($name, $path){
 	$path = parse_url_file_name($path);

	header('content-type: application/file');
	header('content-disposition: attachment; filename='.$name);
	header('location: '.$path);
}

//针对域名参数含有中文的URL编码处理
function parse_url_file_name($url){
	$pos = strrpos($url, '/');
	$name = substr($url, $pos + 1,  strlen($url) - $pos -1);
	$name = urlencode($name);

	$path_tmp = substr($url, 0, $pos + 1);
	$url = $path_tmp.$name;
	return $url;
}

function getFaultResult($error_no){
	$count = $error_no;
	$rsp_num = 0;
	$json_rsp =  array(
			'total_number'=>$count,
			'rst_number'=>$rsp_num);

	return json_encode($json_rsp);
}

function get_rsp_result($value, $error = ''){
	$result = array('result'=>$value, 'error'=>$error);
	return json_encode($result);
}

//对象转数组
function object_to_array($obj){
	
	$_arr = is_object($obj) ? get_object_vars($obj) : $obj;
	foreach ($_arr as $key => $val)
	{
		$val = (is_array($val) || is_object($val)) ? object_to_array($val) : $val;
		$arr[$key] = $val;
	}
	return $arr;
}

function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

//zip解析并获取文件内容
function get_zip_entry_content($zip_file, $entry){
	try{
		$zip = zip_open($zip_file);
		$b_find = false;
		if ($zip) {
			while ($zip_entry = zip_read($zip)) {
				$file_name = zip_entry_name($zip_entry);
				if(substr($file_name, 0, strlen($entry))!= $entry){
					continue;
				}

				$b_find = true;
				if (!zip_entry_open($zip, $zip_entry, "r")){
					continue;	
				}
				$content = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				zip_entry_close($zip_entry);
				break;
			}
			zip_close($zip);
			
//  			$start = strpos($content, "Cmd line:");
//  			$end   = strpos($content, "\n", $start);
//  			$class = trim(substr($content, $start + 9, $end - $start - 9));;
		}
	}catch(Exception $e){
		Log::write("Zip::getZipEntryContent() exception error:".$e->getMessage(), "log");
		return false;
	}
	if($b_find){
		return $content;
	}
	return false;
}

//CURL模拟HTTP请求
function get_respond_by_url($url, $date, $methord = 0){
	try{
		$ch = curl_init();

		if(!empty($date)){
			$url = $url.'?'.$date;
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		if($methord != 0){
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$curl_result = curl_exec($ch);
		if(!$curl_result){
			Log::write('public:get_respond_by_url():curl_exec() failed:'.curl_error($ch), 'log');
		}
		curl_close($ch);
	}catch(Exception $e){
		Log::write("public:get_respond_by_url() exception error:".$e->getMessage(), "log");
		return false;
	}
	return $curl_result;
}

function sql_check_str($str, $len = 0)
{
	if($len > 0){
		if (strlen($str) > $len) $str = substr($str, 0, $len);
	}
	$strKeyword = "and|or|select|update|from|where|order|by|*|delete|'|insert|into|values|create|table|database|truncate|grant";
	$arrKeyword = explode('|', $strKeyword);
	foreach ($arrKeyword as $keyword){
		$str = str_replace($keyword, '', $str);
		$keyword = strtoupper($keyword);
		$str = str_replace($keyword, '', $str);
	}
	return $str;
}

/*-----------------------------公共校验逻辑------------------------------------*/

if(checkVersion($_GET)){
    $bSign = checkSign($_GET);
    if(!$bSign){
        echo get_rsp_result(false, 'sign fail');
        exit();
    }
}

function checkVersion($arrParam){
    global $g_arr_host_config;
    $nVersionCode = (int)(isset($arrParam['versionCode'])?$arrParam['versionCode']:'');
    if($nVersionCode < $g_arr_host_config["sign_version"]){
        Log::write("version code lower, no sign", "log");
        return false;
    }

    if($nVersionCode >= 80){
        Log::write("version code higher, no sign", "log");
        return false;
    }

    return true;
}

function checkSign($signParam){
    global $g_arr_host_config;
    $key = $g_arr_host_config["sign_key"];
    if(empty($signParam) || !isset($signParam['sign'])) {
        Log::write(" no sign param", "log");
        return false;
    }

    $sign = trim($signParam['sign']);
    unset($signParam['sign']);
    ksort($signParam);

    $signStr = http_build_query($signParam);

    $calSign = hash_hmac("sha1", $signStr, $key);
    Log::write("sev_sign:".$calSign, "log");
    if ($calSign == $sign) {
        Log::write("sign pass", "log");
        return true;
    }
    Log::write("sign fail", "log");
    return false;
}

/* 
require_once 'Zend/Mail.php';
require_once 'Zend/Mail/Transport/Smtp.php';

function send_email($subject, $msg_body, $to_mail){
	try{
		$transport = new Zend_Mail_Transport_Smtp('MAIL1.yulong.com');
		$mail = new Zend_Mail();
		$mail->addTo($to_mail, 	"Some Recipient");
		$mail->setFrom('lijie1@yulong.com', "Some Sender");
		$mail->setSubject($subject);
		$mail->setBodyText($msg_body);
		$mail->send($transport);
	}catch (Zend_Exception $e){
		Log::write("send_email() zend_exception, error: ".$e->getMessage(), "log");
		return false;
	}
	return true;
}
*/