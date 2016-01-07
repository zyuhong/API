<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatermarkCat extends Model
{
    protected $table = 'watermark_cat';

    protected $casts = [
        'id' => 'integer',
        'sort' => 'integer',
    ];

    /**
     * the resources that belong to the cat
     */
    public function resources()
    {
        return $this->belongsToMany('App\WatermarkDetail', 'watermark_cat_detail', 'cid', 'wid');
    }

    public function getValidResources($vcode, $offset = 0, $num = 10)
    {
        $details = $this->resources()
            ->where('is_online', 1)
            ->where('vcode', '>=', $vcode)
            ->orderBy('sort', 'desc')
            ->skip($offset)
            ->take($num)
            ->get();

        return $details;
    }

    public function getValidResourcesCount($vcode)
    {
        $count = $this->resources()
            ->where('is_online', 1)
            ->where('vcode', '>=', $vcode)
            ->count();

        return $count;
    }
}
