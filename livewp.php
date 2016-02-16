<?php
require_once 'configs/config.php';
require_once 'lib/Verify.php';
require_once 'lib/Type.php';

header('Content-Type: application/json; charset=utf-8');

$engineCode = Verify::check($_GET, "engineCode", Type::INT);
$engineCodeType = Verify::check($_GET, "engineCodeType", Type::INT);
$channel = Verify::check($_GET, "channel", Type::INT);

if ($engineCodeType == 0 && $engineCode == 0 && $channel == 0) {
    $date = '20160125';
    $versionName = '5.133.3';
    $version = '462';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife-release-462-20160125-142006.apk';
} elseif ($engineCodeType == TAG_ENGINE_TYPE_TT) {
    $date = '20151225';
    $versionName = '1.1';
    $version = '1';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/tiantianlocker_V1.1_2_20151225_1429.apk';
} elseif ($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_QIKU) {
    $date = '20160125';
    $versionName = '5.133.3';
    $version = '462';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife-release-462-20160125-142006.apk';
} elseif (($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_COOLPAD) or
            ($engineCodeType == TAG_ENGINE_TYPE_VL && $channel == TAG_ENGINE_CHANNEL_IVVI) ) {
    $date = '20160201';
    $versionName = '5.133.3';
    $version = '463';
    $silent = 1;
    $wholenet = 1;
    $url = 'http://d.res.zhuti.qiku.com/coolshow/vlife-release-463-20160201-094114.apk';
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
