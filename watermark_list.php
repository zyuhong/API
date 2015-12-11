<?php
$demo = [
    'result' => true,
    'total_number' => 100,
    'cats' => [
        [
            'id' => 1,
            'name' => '热点',
            'cat' => 'hot',
            'watermarks' => [
                [
                    'id' => 1,
                    'name' => '魂之挽歌',
                    'cover' => 'png'
                    'sort' => 1000, // desc
                ]
            ]
        ],
        [
            'id' => 2,
            'name' => '心情',
            'cat' => 'xinqing',
        ]
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($demo);
