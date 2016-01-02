<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/16
 * Time: 19:35
 */
require_once 'public/public.php';
require_once 'lib/Verify.php';
require_once 'lib/Type.php';
require_once 'tasks/Activity/ActivityTask.class.php';

$meid = Verify::check($_GET, 'meid');
$cyid = Verify::check($_GET, 'cyid');
$width = Verify::check($_GET, 'width');
$height = Verify::check($_GET, 'height');
$verCode = Verify::check($_GET, 'versionCode', Type::INT);
$language = Verify::check($_GET, 'language');
$entry = Verify::check($_GET, 'entry', Type::INT);

$id = empty($cyid) ? $meid : $cyid;

$task = new ActivityTask();
$arrResult = $task->getUserActivity($id, $verCode);

$logArr = $_REQUEST;
if (isset($_POST['statis'])) {
    unset($logArr['statis']);
    $statis = json_decode($_POST['statis'], 1);

    if ($statis) {
        $logArr = array_merge($statis, $logArr);
    }
}

$logArr['success'] = Verify::check($arrResult, 'result', Type::BOOLEAN);
Log::appendJson($logArr, 'activity', '_time');

out_json($arrResult);
