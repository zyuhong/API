<?php
$cats = [
];
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
                    'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                    'sort' => 1000, // desc
                ],
                [
                    'id' => 2,
                    'name' => '青春',
                    'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                    'sort' => 990, // desc
                ]
            ]
        ],
        [
            'id' => 2,
            'name' => '心情',
            'cat' => 'xinqing',
        ],
        [
            'id' => 3,
            'name' => '人像',
            'cat' => 'renxiang',
        ],
        [
            'id' => 4,
            'name' => '地点',
            'cat' => 'didian',
        ],
        [
            'id' => 5,
            'name' => '时间',
            'cat' => 'shijian',
        ],
    ]
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($demo);
