<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/16
 * Time: 20:23
 */
require_once 'configs/config.php';
require_once 'tasks/Redis/UserRedis.php';
class ActivityTask
{
    const ACTIVITY_COUNT = 10;
    const ACTIVITY_PAGE_SHOW_TIME = 5;
    const ACTIVTTY_COVER = '/activity/activity_01.png';
    const ACTIVTTY_URL = 'http://www.baidu.com';
    const ACTIVTTY_TITLE = '主题商店活动测试';
    public function getUserActivity($id)
    {
        $redis = new UserRedis();
        $redis->selectDB(1);

        $key = 'activity_' . $id;
        $result = $redis->getKey($key);
        if (! $result) {
            return $this->updateKey($redis, $key, 1);
        }

        $userCount = $result['cnt'];
        $userDate = $result['date'];
        $curDate = date('Y-m-d');
        if (strcmp($curDate, $userDate) == 0) {
            if ($userCount >= ActivityTask::ACTIVITY_COUNT) {
                Log::write("this user " . $id . " is more times", "log");
                return array('result' => false);
            }

            return $this->updateKey($redis, $key, $userCount + 1);
        } else {
            return $this->updateKey($redis, $key, 1);
        }
    }

    private function updateKey($redis, $key, $cnt)
    {
        $arrD = array('cnt' => $cnt, 'date' => date('Y-m-d'));
        $bRet = $redis->setKey($key, $arrD);
        if (! $bRet) {
            Log::write("activity set key fail", "log");
        }
        return $this->getActivityInfo();
    }

    private function getActivityInfo()
    {
        global $g_arr_host_config;
        $arrActivity = array('result' => true,
                            'cover' => $g_arr_host_config['cdnhost'] . ActivityTask::ACTIVTTY_COVER,
                            'event_url' => ActivityTask::ACTIVTTY_URL,
                            'title' => ActivityTask::ACTIVTTY_TITLE,
                            'exsit_time' => ActivityTask::ACTIVITY_PAGE_SHOW_TIME);
        return $arrActivity;
    }
}