<?php
header('Content-Type: application/json; charset=utf-8');
$url_arr = array(
    "date" => "20151203",
    "version_name" => "5.7.8",
    "version" => "455",
    "silent" => 0,
    "wholenet" => 1,
    "url" => "http://d.res.zhuti.qiku.com/coolshow/vlife_20151203_qiku.apk",
    "desc" => "升级描述",
    "md5" => ""
);
echo json_encode($url_arr);
