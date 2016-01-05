<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WatermarkDetail extends Model
{
    protected $table = 'watermark_detail';

    public function cats()
    {
        return $this->belongsToMany('App\WatermarkCat', 'watermark_cat_detail', 'wid', 'cid');
    }
}
