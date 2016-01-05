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
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk';
} elseif ($engineCodeType == TAG_ENGINE_TYPE_TT) {
    $date = '20151225';
    $versionName = '1.1';
    $version = '2';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/tiantianlocker_V1.1_2_20151225_1429.apk';
} elseif ($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_QIKU) {
    $date = '20151203';
    $versionName = '5.133.2';
    $version = '461';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk';
} elseif (($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_COOLPAD) or
            ($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_IVVI) ) {
    $date = '20151222';
    $versionName = '5.133.1';
    $version = '461';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife-release-461-20151222-134929.apk';
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
