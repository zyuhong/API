<?php
session_start();

require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'tasks/Records/RecordTask.class.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}

$moduletype	 = isset($_GET['moduletype'])?$_GET['moduletype']:'';

$rt = new RecordTask();
$result = $rt->saveBrowse($moduletype);

Log::write('browse::save browse module type:'.$moduletype, 'debug');

$result = get_rsp_result($result);

exit($result);
