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
    const ACTIVITY_COUNT = 1;
    const ACTIVITY_PAGE_SHOW_TIME = 8;
    const ACTIVTTY_COVER = '/activity/activity_02.png';
    const ACTIVTTY_URL = 'https://web.os.qiku.com/zhuti/surprise.html';
    const ACTIVTTY_TITLE = '主题商店活动';
    const ACTIVITY_START_TIME = '2016-01-01 10:00:00';
    const ACTIVITY_END_TIME = '2016-01-03 10:00:00';
    const ACTIVITY_VERSION = 45;
    const ACTIVITY_SWITCH = true;
    public function getUserActivity($id, $verCode)
    {
        if (empty($id)) {
            Log::write("user id is null", "log");
            return array('result' => false);
        }
        if (! ActivityTask::ACTIVITY_SWITCH) {
            return array('result' => false);
        }
        if ($verCode != ActivityTask::ACTIVITY_VERSION) {
            return array('result' => false);
        }

//        global $arr_activity_white_list;
//        if (! in_array($id, $arr_activity_white_list)) {
//            return array('result' => false);
//        }

        $tNow = date('Y-m-d H:i:s', time());
        if ($tNow < ActivityTask::ACTIVITY_START_TIME || $tNow > ActivityTask::ACTIVITY_END_TIME) {
            Log::write("activity already over or have not start", "log");
            return array('result' => false);
        }

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

        if ($userCount >= ActivityTask::ACTIVITY_COUNT) {
            Log::write("this user " . $id . " is more times", "log");
            return array('result' => false);
        } else {
            return $this->updateKey($redis, $key, $userCount + 1);
        }


//        if (strcmp($curDate, $userDate) == 0) {
//            if ($userCount >= ActivityTask::ACTIVITY_COUNT) {
//                Log::write("this user " . $id . " is more times", "log");
//                return array('result' => false);
//            }
//
//            return $this->updateKey($redis, $key, $userCount + 1);
//        } else {
//            return $this->updateKey($redis, $key, 1);
//        }
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