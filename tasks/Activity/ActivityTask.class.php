<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/16
 * Time: 20:23
 */
require_once 'tasks/Redis/UserRedis.php';
class ActivityTask
{
    const ACTIVITY_COUNT = 10;
    const ACTIVTTY_COVER = '';
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
        $arrActivity = array('cover' => ActivityTask::ACTIVTTY_COVER,
                            'event_url' => ActivityTask::ACTIVTTY_URL,
                            'title' => ActivityTask::ACTIVTTY_TITLE);
        return $arrActivity;
    }
}