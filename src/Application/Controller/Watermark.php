<?php

namespace Controller;

use Data\Verify as D;
use Data\Type as DT;

class Watermark extends Base
{
    public function listAction()
    {
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

        $this->jsonOutput($demo);
    }

    public function detailAction()
    {
        $id = $this->get('id', DT::INT);

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

        $this->jsonOutput($demo);
    }
}
