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

}
