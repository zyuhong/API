<?php
namespace Model;

class WatermarkCatDetail extends Base
{
    protected $table = 'watermark_cat_detail';
    protected $p_key = 'id';

    public function __construct()
    {
        $config = \AppConf::getCfg('/db/mdb/zhuti_api_db');
        $this->db = \AppConf::getCfg('/db/name/watermark_db');
        $config['database'] = $this->db;
        parent::__construct($this->table, $this->p_key, $config);
    }
}