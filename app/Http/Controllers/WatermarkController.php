<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use App\WatermarkCat;
use App\WatermarkCatDetail;
use App\WatermarkDetail;

use Cache;

class WatermarkController extends BaseController
{
    const CACHE_TIME = 300;
    const SUBSCRIPT_NEW = 2592000;

    public function catList(Request $request)
    {
        $id = $request->get('id');
        $cat = $request->get('cat');
        $offset = min(intval($request->get('offset', 0)), 640);
        $page = min(max(intval($request->get('page', 1)), 1), 64);
        $num = min(max(intval($request->get('num', 10)), 1), 100);

        # first version is 40002
        $vcode = intval($request->get('vcode', 40002));

        $key = "list:$id:" . substr(md5($cat.$offset.$page.$num.$vcode), 8, 16);

        $cacheEnable = env('CACHE_ENABLE', true);
        if ($cacheEnable) {
            $cache = Cache::get($key);
            if ($cache && $cacheEnable) {
                return response()->json($cache);
            }
        }

        if (!isset($_GET['offset'])) {
            $offset = ($page - 1) * $num;
        }

        if ($id) {
            $cats = [WatermarkCat::find($id)];
        } else {
            $cats = WatermarkCat::all()->sortByDesc('sort');
        }

        if (empty($cats)) {
            return $this->error();
        }

        # has cat id
        $result = [
            'result' => true,
            'total_number' => 0,
            'cats' => []
        ];
        $first = null;
        foreach ($cats as $i => $cat) {
            $watermarksCount = $cat->getValidResourcesCount($vcode);

            if ($watermarksCount || $id) {
                # mark first
                if (is_null($first)) {
                    $first = $cat;
                }
                $tmp = [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'cat' => $cat->cat,
                    'watermarks_count' => $watermarksCount
                ];
                $result['cats'][] = $tmp;
            }
        }

        # if has first cat, fix cat info
        if (!is_null($first)) {
            $result['cats'][0]['watermarks'] = $this->getCatResources($first, $vcode, $offset, $num);
        }

        $result['total_number'] = count($result['cats']);

        if ($cacheEnable) {
            Cache::put($key, $result, self::CACHE_TIME);
        }

        return response()->json($result);
    }

    public function detail(Request $request)
    {
        $id = $request->input('id');
        $key = "detail:$id";

        $cacheEnable = env('CACHE_ENABLE', true);
        if ($cacheEnable) {
            $cache = Cache::get($key);
            if ($cache) {
                return response()->json($cache);
            }
        }

        $detail = WatermarkDetail::find($id, ['id', 'name', 'cover', 'preview', 'resource', 'hash', 'sort', 'created_at']);

        if (empty($detail)) {
            $result = ['result' => false];
            return $this->error();
        }

        $result = [
            'result' => true,
            'total_number' => 1,
            'watermarks_count' => 1,
            'watermarks' => $this->fixDetailArray($detail, 'detail')
        ];

        if ($cacheEnable) {
            Cache::put($key, $result, self::CACHE_TIME);
        }

        return response()->json($result);
    }

    /**
     * 获取分类下的资源
     */
    private function getCatResources($cat, $vcode, $offset = 0, $num = 10)
    {
        $details = $cat->getValidResources($vcode, $offset, $num);

        $detailsArray = [];
        foreach ($details as $detail) {
            $detailsArray[] = $this->fixDetailArray($detail);
        }

        return $detailsArray;
    }

    /**
     * 整理详情
     * model: {simple, detail}
     */
    private function fixDetailArray($detail, $mode = 'simple')
    {
        $cdn = env('APP_CDN');

        $result = [
            'id' => $detail->id,
            'name' => $detail->name,
            'cover' => $cdn . $detail->cover,
            'preview' => $cdn . $detail->preview,
            'sort' => $detail->sort
        ];

        # new subscript logic
        if ($detail->created_at > date('Y-m-d H:i:s', time() - self::SUBSCRIPT_NEW)) {
            $result['subscripts'] = [
                'new' => ['position' => 'rt', 'res_url' => '']
            ];
        }

        if ($mode == 'detail') {
            $result['resource'] = $cdn . $detail->resource;
            $result['hash'] = $detail->hash;
        }

        return $result;
    }

    /**
     * 错误处理
     */
    public function error($result = [])
    {
        if (empty($result)) {
            $result = ['result' => false];
        }

        return response()->json($result);
    }
}