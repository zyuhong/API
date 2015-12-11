<?php
$demo = [
    'result' => true,
    'total_number' => 100,
    'watermarks' => [
        'id' => 1,
        'name' => '魂之挽歌',
        'cover' => 'png',
        'resource' => '',
        'hash' => '11',
        'sort' => 1000, // desc
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($demo);
