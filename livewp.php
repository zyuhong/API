<?php
require_once 'configs/config.php';
require_once 'lib/Verify.php';
require_once 'lib/Type.php';

header('Content-Type: application/json; charset=utf-8');

$engineCode = Verify::check($_GET, "engineCode", Type::INT);
$engineCodeType = Verify::check($_GET, "engineCodeType", Type::INT);
$channel = Verify::check($_GET, "channel", Type::INT);

if ($engineCodeType == 0 && $engineCode == 0 && $channel == 0) {
    $date = '20151203';
    $versionName = '5.133.2';
    $version = '461';
    $silent = 0;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk';
} elseif ($engineCodeType == TAG_ENGINE_TYPE_TT && $channel == TAG_ENGINE_CHANNEL_QIKU) {
    $date = '20151215';
    $versionName = '1.000.0';
    $version = '1';
    $silent = 0;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/t_lockscreen.apk';
} elseif ($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_QIKU) {
    $date = '20151203';
    $versionName = '5.133.2';
    $version = '461';
    $silent = 0;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk';
} elseif (($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_COOLPAD) or
            ($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_IVVI) ) {
    $date = '20151201';
    $versionName = '5.133.1';
    $version = '460';
    $silent = 0;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_coolpad.apk';
} else {
    exit();
}

$url_arr = array(
    "date" => $date,
    "version_name" => $versionName,
    "version" => $version,
    "silent" => $silent,
    "wholenet" => $wholenet,
    "url" => $url,
    "desc" => "升级描述",
    "md5" => ""
);
echo json_encode($url_arr);
