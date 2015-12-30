<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/2
 * Time: 20:51
 */
require_once 'tasks/CoolShow/Mark.class.php';
require_once 'tasks/Redis/UserRedis.php';
require_once 'lib/DBManager.lib.php';
class MarkDb extends DBManager
{
    public function __construct($dbConfig=array())
    {
        if(!$dbConfig){
            global $g_arr_db_config;
            $dbConfig = $g_arr_db_config['coolshow'];
        }
        $this->connectMySqlPara($dbConfig);
    }

    public function updateMarkList()
    {
        $mark = new Mark();
        $strSql = $mark->getSelectMarkSql();
        $result = $this->executeQuery($strSql);
        if (! $result) {
            Log::write("updateMarkList fail", "log");
            return false;
        }

        //存入redis
        $redis = new UserRedis();
        if (count($result) < 1) {
            Log::write("no mark list", "log");
            return false;
        }
        $arrList = array();
        foreach($result as $arrMark) {
            $key = $arrMark['res_id'] . '_' . $arrMark['cooltype'] . '_' . $arrMark['ratio'];
            $arrList[$key] = $arrMark;
        }
        $key = $redis->markKey;
        $redis->setKey($key, json_encode($arrList));

        return true;
    }
}