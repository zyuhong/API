<?php
/**
 *评论评分的接口
 * $type : 资源类型
 * $id   : 资源ID
 */

require_once 'public/public.php';
require_once 'tasks/Records/ScoreRecord.class.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

$scoreRecord = new ScoreRecord();
$bResult = $scoreRecord->saveScore();

echo get_rsp_result($bResult);