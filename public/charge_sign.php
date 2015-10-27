<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/10/27
 * Time: 11:49
 */
require_once 'lib/RSA.php';
require_once 'configs/config.php';

function validsign($trans_data, $sign, $key=""){
    if(empty($key)){
        global $g_arr_host_config;
        $key = $g_arr_host_config['app_key'];
    }
    $rsa = new RSA();

    //解析key 需要从商户自服务提供的key中解析出我们的真正的key. 商户自服务提供的key = mybase64(private_key+mod_key);
    $key1 =  base64_decode($key);
    $key2 = substr($key1,40,strlen($key1)-40);
    $key3 = base64_decode($key2);
    //php 5.3环境用下面这个
    list($private_key, $mod_key) = explode("+", $key3);
    //list($private_key, $mod_key) = split("\\+", $key3);
    //使用解析出来的key，解密包体中传过来的sign签名值
    $sign_md5 = $rsa->decrypt($sign, $private_key, $mod_key);
    $msg_md5 = md5($trans_data);
    //echo "key3 : {$key3} <br/>\n";
    //echo "private_key : {$private_key} <br/>\n";
    //echo "mod_key : {$mod_key} <br/>\n";
    //echo "sign_md5 : {$sign_md5} <br/>\n";
    //echo "msg_md5 : {$msg_md5} <br/>\n";

    return strcmp($msg_md5, $sign_md5);
}

function gensign($trans_data,$key){
    $rsa = new RSA();

    //解析key 需要从商户自服务提供的key中解析出我们的真正的key. 商户自服务提供的key = mybase64(private_key+mod_key);
    $key1 =  base64_decode($key);
    $key2 = substr($key1,40,strlen($key1)-40);
    $key3 = base64_decode($key2);
    //list($private_key, $mod_key) = split("\\+", $key3);
    if(phpversion () > "5.3"){
        list ( $private_key, $mod_key ) = explode ( "+", $key3 );
    }else{
        list($private_key, $mod_key) = split("\\+", $key3);
    }

    //使用解析出来的key，解密包体中传过来的sign签名值
    $msg_md5 = md5($trans_data);
    return $rsa->encrypt($msg_md5, $private_key, $mod_key);
}