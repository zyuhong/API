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
$verCode = Verify::check($_GET, 'versioncode', Type::INT);
$language = Verify::check($_GET, 'language');

if (empty($meid) && empty($cyid)) {
    Log::write("user info all null", "log");
    $bRet = array('result' => false);
    out_json($bRet);
}

$id = empty($cyid) ? $meid : $cyid;

$task = new ActivityTask();
$arrResult = $task->getUserActivity($id);

out_json($arrResult);
