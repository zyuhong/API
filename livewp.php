<?php

require_once 'lib/Verify.php';
require_once 'lib/Type.php';

header('Content-Type: application/json; charset=utf-8');

$engineCode = Verify::check($_GET, "engineCode", Type::INT);
$engineCodeType = Verify::check($_GET, "engineCodeType", Type::INT);
$channel = Verify::check($_GET, "channel", Type::INT);

if ($engineCodeType == 0 && $engineCode == 0 && $channel == 0) {
    $version = '455';
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk';
} elseif ($engineCodeType == 1 && $channel == 1) {
    $version = '455';
    $url = 'http://d.res.zhuti.qiku.com/coolshow/t_lockscreen.apk.apk';
} elseif ($engineCodeType == 2 && $channel == 1) {
    $version = '455';
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk';
} elseif ($engineCodeType == 2 && $channel == 2) {
    $version = '455';
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_coolpad.apk';
} else {
    exit();
}

$url_arr = array(
    "date" => "20151203",
    "version_name" => "5.7.8",
    "version" => $version,
    "silent" => 0,
    "wholenet" => 1,
    "url" => $url,
    "desc" => "升级描述",
    "md5" => ""
);
echo json_encode($url_arr);
