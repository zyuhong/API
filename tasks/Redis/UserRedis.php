<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/10/23
 * Time: 21:05
 */
require_once 'configs/config.php';
require_once 'lib/CRedis.lib.php';
require_once 'lib/WriteLog.lib.php';

class UserRedis
{
    private $_redis;
    public function __construct()
    {
        global $g_arr_queue_config;
        $this->_redis = new CRedis($g_arr_queue_config['server']);
    }

    public function getUserToken($key){
        try {
            $bResult = $this->_redis->get($key);
        }catch (\Exception $e){
            Log::write("redis ex. error on ".$e->getMessage(), "log");
            return false;
        }
        return $bResult;
    }

    public function createUserToken($key, $value){
        $expire = 1 * 24 * 60 * 60;
        $bResult = $this->_redis->setex($key, $value, $expire);
        return $bResult;
    }
}