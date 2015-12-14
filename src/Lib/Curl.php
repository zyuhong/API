<?php

namespace Lib;

use Data\Verify as D;
use Data\Type as DT;

class Lib_Curl
{
    function __construct()
    {
    }

    /**
     * curl静态方法
     * @param {string} $url curl的url
     * @param {int} $timeout curl下载时间
     * @param {int} $connectTimeout curl连接时间
     * @param {Array} $options
     * 支持 refer, header, proxy, fn闭包自定义吧
     */
    public static function curl($url, $timeout = 1000, $connectTimeout = 1000, $options = [])
    {
        global $idc;

        // 如果是安全扫描的UA，则不curl到第三方了
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], '360webscan') !== false) {
            return false;
        }

        $b = microtime(true);
        $process = curl_init();
        curl_setopt($process, CURLOPT_URL, $url);
        curl_setopt($process, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.)");
        curl_setopt($process, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($process, CURLOPT_CONNECTTIMEOUT_MS, $connectTimeout);

        $header = D::verify($options, 'header', 'array');
        if ($header && is_array($header)) {
            curl_setopt($process, CURLOPT_HTTPHEADER, $header);
        }

        $refer = D::verify($options, 'refer', 'string');
        if ($refer) {
            curl_setopt($process, CURLOPT_REFERER, $refer);
        }

        $proxy = D::verify($options, 'proxy', 'string');
        if (!empty($proxy)) {
            curl_setopt($process, CURLOPT_PROXY, $proxy);
        }

        $fn = D::verify($options, 'fn', 'function');
        if ($fn instanceof \Closure) {
            $fn($process);
        }

        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($process);

        $timeSpend = microtime(true) - $b;
        $msg = implode("\t", [$idc, $timeSpend, $url]);
        // Log::qlog("CloudSearch.QSSWEB.MAP_SO.API_CURL", $msg);

        $http_code = curl_getinfo($process, CURLINFO_HTTP_CODE);
        $len = strlen($result);

        if (curl_errno($process) || $http_code != 200 || empty($len)) {
            $qLogParas = [
                $idc,
                curl_errno($process),
                curl_error($process),
                $timeSpend,
                $url
            ];
            // Log::qlog('CloudSearch.QSSWEB.MAP_SO.API_CURL_ERROR', implode("\t", $qLogParas));

            $logParas = [
                date('Y-M-d H:i:s'),
                $idc,
                "no:" . curl_errno($process),
                "error:" . curl_error($process),
                "code:" . $http_code,
                "len:$len",
                "time:" . $timeSpend,
                "api:". $url
            ];
            // Alarm::alarm(implode("\t", $logParas));

            return false;
        }

        $errno = curl_errno($process);
        curl_close($process);

        return $result;
    }

    /**
     * 重试
     */
    public static function reCurl($url, $time = 3, $timeout = 1000, $connectTimeout = 1000, $options = [])
    {
        $data = self::curl($url, $timeout, $connectTimeout, $options);

        if ($time < 2) {
            return $data;
        }

        if (empty($data)) {
            return self::reCurl($url, --$time, $timeout + 500, $connectTimeout, $options);
        }

        return $data;
    }

    public static function post($url, $paras, $time = 1, $timeout = 2000, $connectTimeout = 1000, $options = [])
    {
        global $idc;

        // 如果是安全扫描的UA，则不curl到第三方了
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], '360webscan') !== false) {
            return false;
        }

        $b = microtime(true);
        $process = curl_init($url);
        curl_setopt($process, CURLOPT_POST, 1);
        if ($paras) {
            curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($paras));
        }
        //curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_TIMEOUT_MS, $timeout);
        curl_setopt($process, CURLOPT_CONNECTTIMEOUT_MS, $connectTimeout);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

        $fn = D::verify($options, 'fn', 'function');
        if ($fn instanceof \Closure) {
            $fn($process);
        }

        $r = curl_exec($process);
        $http_code = curl_getinfo($process, CURLINFO_HTTP_CODE);

        $e = microtime(true);
        $len = strlen($r);

        if (curl_errno($process) || $http_code != 200 || (empty($r) && (!D::get($options, 'empty')))) {
            /**Log::runLog(implode("\t", [
                        date('Y-M-d H:i:s'),
                        $idc,
                        "no:" . curl_errno($process),
                        "error:" . curl_error($process),
                        "code:" . $http_code,
                        "len:$len",
                        "time:" . ($e-$b),
                        "url:". $url
                    ]), 'api_error');
            */

            /**Alarm::alarm(implode("\t", [
                        date('Y-M-d H:i:s'),
                        $idc,
                        "no:" . curl_errno($process),
                        "error:" . curl_error($process),
                        "code:" . $http_code,
                        "len:$len",
                        "time:" . ($e-$b),
                        "api:". $url,
                        "params:" . json_encode($paras)
                    ]));
            */

            if ($time > 0) {
                return self::post($url, $paras, --$time, $timeout, $connectTimeout, $options);
            } else {
                return false;
            }
        } else {
            return $r;
        }
    }

    public static function multi($urlArray, $timeOut = 2000, $connectTimeout = 1000)
    {
        // 如果是安全扫描的UA，则不curl到第三方了
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], '360webscan') !== false) {
            return false;
        }

        $curlArray = array();
        $curlMulti = curl_multi_init();
        foreach ($urlArray as $key => $url) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeOut);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, $connectTimeout);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //返回字符串
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //允许重定向

            $curlArray[$key] = $curl;
            curl_multi_add_handle($curlMulti, $curl);
        }

        $running = NULL;
        do {
            curl_multi_exec($curlMulti,$running);
        } while($running > 0);

        $contentArray = array();
        foreach ($curlArray as $key => $curl) {
            $url = $urlArray[$key];
            $contentArray[$key] = curl_multi_getcontent($curl);
        }

        foreach ($curlArray as $key => $curl) {
            curl_multi_remove_handle($curlMulti, $curl);
        }

        curl_multi_close($curlMulti);

        return $contentArray;
    }

    // 添加recurl机制
    public static function reMulti($urlArray, $timeOut = 2, $timeout = 1000, $connectTimeout = 1000)
    {
        $datas = self::multi($urlArray, $timeout, $connectTimeout);

        foreach ($datas as $k => $d) {
            if (empty($d)) {
                $d = self::reCurl($urlArray[$key], $timeOut, $timeout, $connectTimeout);
            }
            if ($d) {
                $datas[$k] = $d;
            }
        }

        return $datas;
    }
}
