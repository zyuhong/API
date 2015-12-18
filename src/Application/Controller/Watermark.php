<?php

namespace Controller;

use Data\Verify as D;
use Data\Type as DT;

class Watermark extends Base
{
    public function listAction()
    {
        $catModel = new \Model\WatermarkCat();

        $id = $this->get('id', DT::INT);
        $cat = $this->get('cat');
        $offset = $this->get('offset', DT::INT, 'default=0,max=640');
        $page = $this->get('page', DT::INT, 'default=1,max=64');
        $num = $this->get('num', DT::INT, 'default=10,max=100');
        $vcode = $this->get('vcode');

        if (!isset($_GET['offset'])) {
            $offset = ($page - 1) * $num;
        }

        $where = '';
        $value = [];

        if ($id) {
            $where = 'id=?';
            $value[] = $id;
        }

        $cats = $catModel->getAll(['cols' => 'id, name, cat', 'where' => $where, 'value' => $value]);

        if (empty($cats)) {
            $this->jsonOutput(['result' => false]);
        }

        if ($cats) {
            foreach ($cats as &$c) {
                $c['id'] = intval($c['id']);
            }
        }

        if (!empty($id)) {
            $watermarks = $this->getCatResources(D::get($cats, '0.id'), $offset, $num);
            $cats[0]['watermarks_count'] = $watermarks['count'];
            $cats[0]['watermarks'] = $watermarks['data'];
            $result = [
                'result' => true,
                'total_number' => 1,
                'cats' => [
                    $cats[0]
                ]
            ];
        } else {
            $watermarks = $this->getCatResources(D::get($cats, '0.id'), $offset, $num);
            $cats[0]['watermarks_count'] = $watermarks['count'];
            $cats[0]['watermarks'] = $watermarks['data'];

            $result = [
                'result' => true,
                'total_number' => 5,
                'cats' => $cats
            ];
        }

        $this->jsonOutput($result);
    }

    public function detailAction()
    {
        $id = $this->get('id', DT::INT);

        if (empty($id)) {
            $this->jsonOutput(['result' => false]);
        }

        $resource = $this->getResource($id);
        if (empty($resource)) {
            $this->jsonOutput(['result' => false]);
        }

        $result = [
            'result' => true,
            'total_number' => 1,
            'watermarks_count' => 1,
            'watermarks' => $resource
        ];

        $this->jsonOutput($result);
    }

    /**
     * 获取具体资源
     */
    public function getResource($id)
    {
        $detailTable = new \Model\WatermarkDetail();
        $resource = $detailTable->getByPk($id, 'id,name,cover,preview,resource,hash,sort');

        if (empty($resource)) {
            return false;
        }

        $cdn = \AppConf::getCfg('/app/cdn');
        $resource['id'] = intval($resource['id']);
        $resource['sort'] = intval($resource['sort']);
        $resource['cover'] = $resource['cover'] ? $cdn . $resource['cover'] : '';
        $resource['preview'] = $resource['preview'] ? $cdn . $resource['preview'] : '';
        $resource['resource'] = $resource['resource'] ? $cdn . $resource['resource'] : '';

        return $resource;
    }

    /**
     * 获取分类下的资源
     * @param $cid 分类id
     * @param $offset 偏移量
     * @param $size 获取记录数
     */
    public function getCatResources($cid, $offset = 0, $size = 10)
    {
        $cat = new \Model\WatermarkCatDetail();
        $details = $cat->getAll([
                'cols' => 'd.id, d.name, d.cover, d.preview,d.sort',
                'where' => 'cid=?',
                'value' => [$cid],
                'left_join' => 'watermark_detail as d on watermark_cat_detail.wid=d.id ',
                'limit' => "$offset,$size",
                'order' => 'd.sort desc'
            ]
        );

        if ($details) {
            $cdn = \AppConf::getCfg('/app/cdn');
            foreach ($details as &$detail) {
                $detail['id'] = intval($detail['id']);
                $detail['sort'] = intval($detail['sort']);
                $detail['preview'] = $detail['preview'] ? $cdn . $detail['preview'] : '';
                $detail['cover'] = $detail['cover'] ? $cdn . $detail['cover'] : '';
            }
        }

        $count = $cat->count('cid=?', [$cid]);

        return [
            'data' => $details,
            'count' => $count
        ];
    }

}
