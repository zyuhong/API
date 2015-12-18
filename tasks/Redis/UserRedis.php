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
    public $markKey;
    public $priceKey;
    public function __construct()
    {
        global $g_arr_redis_config;
        $this->_redis = new CRedis($g_arr_redis_config['server']);

        $this->markKey = 'zhuti_mark_list';
        $this->priceKey = 'zhuti_price_list';
    }

    public function selectDB($index)
    {
        return $this->_redis->selectDB($index);
    }

    public function getUserToken($key)
    {
        try {
            $bResult = $this->_redis->get($key);
        } catch (\Exception $e) {
            Log::write("redis ex. error on ".$e->getMessage(), "log");
            return false;
        }
        return $bResult;
    }

    public function createUserToken($key, $value)
    {
        $expire = 1 * 24 * 60 * 60;
        $bResult = $this->_redis->setex($key, $value, $expire);
        return $bResult;
    }

    public function getKey($key)
    {
        $bResult = $this->_redis->getValue($key);
        return $bResult;
    }

    public function setKey($key, $value)
    {
        $bResult = $this->_redis->setValue($key, $value);
        return $bResult;
    }
}