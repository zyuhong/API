<?php
$demo = [
    'result' => true,
    'total_number' => 100,
    'watermarks' => [
        'id' => 1,
        'name' => '魂之挽歌',
        'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
        'resource' => 'http://watermark.test.os.qkcorp.net/101.zip',
        'hash' => '8f44d5f0cc77e40ddd6f2be13a332bc4',
        'sort' => 1000, // desc
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($demo);
