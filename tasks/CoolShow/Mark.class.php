<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/2
 * Time: 20:47
 */
require_once 'tasks/CoolShow/MarkSql.class.php';
class Mark
{
    public $identity;   //角标ID
    public $url;        //角标资源地址
    public $position;   //角标位置

    public function __construct()
    {
        $this->identity = '';
        $this->url = '';
        $this->position = 0;
    }

    public function getSelectMarkSql(){
        $sql = sprintf(SQL_SELECT_MARK_LIST);
        return $sql;
    }
}