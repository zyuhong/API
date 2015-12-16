<?php

namespace Controller;

use Data\Verify as D;
use Data\Type as DT;

class Watermark extends Base
{
    public function listAction()
    {
        $cat = $this->get('cat');
        $cats = [
            [
                'id' => 1,
                'name' => '热点',
                'cat' => 'hot',
                'watermarks_count' => 8,
                'watermarks' => [
                    $this->getDetail(1),
                    $this->getDetail(2),
                    $this->getDetail(3),
                    $this->getDetail(4),
                    $this->getDetail(5),
                    $this->getDetail(6),
                    $this->getDetail(7),
                    $this->getDetail(8),
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
        ];
        if (is_numeric($cat)) {
            $catsHash = D::hashMap($cats, 'id');
        } else {
            $catsHash = D::hashMap($cats, 'cat');
        }
        if (!empty($cat)) {
            if (!D::get($catsHash, $cat)) {
                $this->jsonOutput(['result' => false]);
            }
            $demo = [
                'result' => true,
                'total_number' => 1,
                'cats' => [
                    [
                        'id' => $catsHash[$cat]['id'],
                        'name' => $catsHash[$cat]['name'],
                        'cat' => $cat,
                        'watermarks_count' => 8,
                        'watermarks' => $this->getWaters($cat, $catsHash[$cat]['id'])
                    ]
                ]
            ];
        } else {
            $demo = [
                'result' => true,
                'total_number' => 5,
                'cats' => [
                    [
                        'id' => 1,
                        'name' => '热点',
                        'cat' => 'hot',
                        'watermarks_count' => 8,
                        'watermarks' => $this->getWaters('hot', 1)
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
        if (empty($id)) {
            $this->jsonOutput(['result' => false]);
        }

        $demo = [
            'result' => true,
            'total_number' => 1,
            'watermarks_count' => 1,
            'watermarks' => [
                'id' => $id,
                'name' => '魂之挽歌',
                'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
                'resource' => 'http://watermark.test.os.qkcorp.net/101.zip',
                'hash' => '8f44d5f0cc77e40ddd6f2be13a332bc4',
                'sort' => 1000 - $id, // desc
            ]
        ];

        $this->jsonOutput($demo);
    }

    public function getWaters($cat, $index)
    {
        $size = 8;
        $watermarks = [];
        for ($i = 0; $i < $size; $i++) {
            $watermarks[] = $this->getDetail(($index - 1) * $size + $i + 1);
        }
        return $watermarks;
    }

    public function getDetail($id)
    {
        return [
            'id' => $id,
            'name' => '魂之挽歌',
            'cover' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
            'preview' => 'http://watermark.test.os.qkcorp.net/cover1.jpg',
            'sort' => 1000 - $id, // desc
        ];
    }
}
