<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/18
 * Time: 20:51
 */
require_once 'tasks/CoolShow/PriceTag.class.php';
require_once 'tasks/Redis/UserRedis.php';
require_once 'lib/DBManager.lib.php';
class PriceTagDb extends DBManager
{
    public function __construct($dbConfig=array())
    {
        if (! $dbConfig) {
            global $g_arr_db_config;
            $dbConfig = $g_arr_db_config['coolshow'];
        }
        $this->connectMySqlPara($dbConfig);
    }

    public function updatePriceTagList()
    {
        $price = new PriceTag();
        $strSql = $price->getSelectPriceTagSql();
        $result = $this->executeQuery($strSql);
        if (! $result) {
            Log::write("updatePriceTagList fail", "log");
            return false;
        }

        //存入redis
        $redis = new UserRedis();
        if (count($result) < 1) {
            Log::write("no mark list", "log");
            return false;
        }
        $arrList = array();
        foreach ($result as $arrPrice) {
            $key = $arrPrice['cpid'] . '_' . $arrPrice['cooltype'];
            $arrList[$key] = $arrPrice;
        }
        $key = $redis->priceKey;
        $redis->setKey($key, json_encode($arrList));

        return true;
    }
}