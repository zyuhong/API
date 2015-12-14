<?php

namespace Controller;

use Data\Verify as D;
use Data\Type as DT;

class Watermark extends Base
{
    public function listAction()
    {
        $cat = $this->get('cat');
        if (!empty($cat)) {
            $demo = [
                'result' => true,
                'total_number' => 100,
                'cats' => [
                    [
                        'id' => 1,
                        'name' => 'test',
                        'cat' => $cat,
                        'watermarks_count' => 1,
                        'watermarks' => [
                            [
                                'id' => 1,
                                'name' => '魂之挽歌',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 1000, // desc
                            ],
                        ]
                    ]
                ]
            ];
        } else {
            $demo = [
                'result' => true,
                'total_number' => 100,
                'cats' => [
                    [
                        'id' => 1,
                        'name' => '热点',
                        'cat' => 'hot',
                        'watermarks_count' => 8,
                        'watermarks' => [
                            [
                                'id' => 1,
                                'name' => '魂之挽歌',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 1000, // desc
                            ],
                            [
                                'id' => 2,
                                'name' => '青春',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 990, // desc
                            ],
                            [
                                'id' => 3,
                                'name' => 'test3',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 970, // desc
                            ],
                            [
                                'id' => 4,
                                'name' => 'test4',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 960, // desc
                            ],
                            [
                                'id' => 5,
                                'name' => 'test5',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 950, // desc
                            ],
                            [
                                'id' => 6,
                                'name' => 'test3',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 940, // desc
                            ],
                            [
                                'id' => 7,
                                'name' => 'test3',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 930, // desc
                            ],
                            [
                                'id' => 8,
                                'name' => 'test3',
                                'cover' => 'http://watermark.test.os.qkcorp.net/cover2.jpg',
                                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                                'sort' => 920, // desc
                            ],
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
        }

        $this->jsonOutput($demo);
    }

    public function detailAction()
    {
        $id = $this->get('id', DT::INT);

        $demo = [
            'result' => true,
            'total_number' => 100,
            'watermarks_count' => 1,
            'watermarks' => [
                'id' => $id,
                'name' => '魂之挽歌',
                'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                'resource' => 'http://watermark.test.os.qkcorp.net/101.zip',
                'hash' => '8f44d5f0cc77e40ddd6f2be13a332bc4',
                'sort' => 1000, // desc
            ]
        ];

        $this->jsonOutput($demo);
    }
}
