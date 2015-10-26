<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/10/23
 * Time: 21:16
 */
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'lib/Des.lib.php';
require_once 'tasks/Redis/UserRedis.php';

function checkTKT($arrPost){
    $arrParams = isset($arrPost['statis'])?$arrPost['statis']:'';
    if(empty($arrParams)){
        Log::write("no post params", "log");
        return false;
    }
    $arrParams = stripslashes($arrParams);
    $arrParams = json_decode($arrParams, true);
    $userid = isset($arrParams['uid'])?$arrParams['uid']:'';
    $appid = isset($arrParams['appid'])?$arrParams['appid']:'';
    $tkt = isset($arrParams['tkt'])?$arrParams['tkt']:'';
    $tkt = base64_decode($tkt);

    global $g_arr_des_key;
    foreach($g_arr_des_key as $key){
        $tkt = Des::decrypt($tkt, $key);
    }
    Log::write("tkt is :".$tkt, "log");

    return checkUserToken($userid, $appid, $tkt);
}

function checkUserToken($strUserId, $strCyAppId, $strToken){
    $key = $strCyAppId.'_'.$strUserId;
    $redis = new UserRedis();
    try {
        $result = $redis->getUserToken($key);
    }catch (Exception $e){
        Log::write("redis exception".$e->getMessage(), "log");
        return false;
    }

    if($result){
        if (strcmp($result, $strToken) == 0){
            return true;
        }
    }

    global $g_arr_qk_yun_config;
    $strUrl = $g_arr_qk_yun_config['check_tkt'];
    $nMethod = 1;

    $arrData = array(	'tkt' 		=> $strToken,
                        'appid'		=> $strCyAppId,
                        'uid'		=> $strUserId);

    $urlData = 'uid='.$strUserId.'&tkt='.$strToken.'&appid='.$strCyAppId;

    $jsonResult = skip_curl($arrData, $strUrl, '', $nMethod);
    //$jsonResult = curl_data($strUrl, $urlData, '', $nMethod);
    if ($jsonResult === false){
        Log::write("curl fail", "log");
        return false;
    }

    $result = json_decode($jsonResult);
    $rtnCode = $result->rtncode;
    if ($rtnCode == '0'){
        Log::write("access_token validtion success. curl:".$jsonResult, "log");

        $result = $redis->createUserToken($key, $strToken);

        return true;
    }

    Log::write("access_token validtion fail.curl=".$jsonResult, "log");

    return false;
}

/**
 * @name Curl 数据
 */
function curl_data($url, $vars, $cookie='', $method = 0){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if($method == 0){
        if(!empty($vars)){
            $url = $url.'?'.$vars;
        }
    }elseif ($method == 1){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($cookie)){
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }

    if(strstr($url,'https://')){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    $curl_result = curl_exec($ch);
    curl_close($ch);

    return $curl_result;
}
/**
 * 跳转接口
 * @param unknown_type $arrData 参数数组
 * @param unknown_type $strUrl	请求URL
 * @param unknown_type $strCookie	cookie，默认为空
 * @param unknown_type $nMethod		请求方法，默认是0（GET） 1（POST）
 * @param unknown_type $bHeader     是否要求返回header，默认是false
 * @return unknown
 */

function skip_curl($arrData, $strUrl, $strCookie = '', $nMethod = 0, $bHeader = false){
    $jsonData = json_encode($arrData);
    $urlData = 'method='.$nMethod.'&url='.$strUrl.'&data='.urlencode($jsonData).'&cookie='.urlencode($strCookie);

    if ($bHeader){
        $urlData .= '&action=headinfo';
    }else{
        $urlData .= '&action=requestinfo';
    }

    global $g_arr_qk_yun_config;
    $jsonResult = curl_data($g_arr_qk_yun_config['skip_url'], $urlData);

    return $jsonResult;
}