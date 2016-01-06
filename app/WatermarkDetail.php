<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatermarkDetail extends Model
{
    protected $table = 'watermark_detail';

    protected $casts = [
        'id' => 'integer',
        'sort' => 'integer',
    ];

    public function cats()
    {
        return $this->belongsToMany('App\WatermarkCat', 'watermark_cat_detail', 'wid', 'cid');
    }
}
