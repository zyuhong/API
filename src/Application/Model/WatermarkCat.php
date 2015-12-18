<?php
namespace Model;

class WatermarkCat extends Base
{
    protected $db = 'test';
    protected $table = 'watermark_cat';
    protected $p_key = 'id';

    public function __construct()
    {
        $config = \AppConf::getCfg('/db/mdb/zhuti_api_db');
        $config['db'] = $this->db;
        parent::__construct($this->table, $this->p_key, $config);
    }
}