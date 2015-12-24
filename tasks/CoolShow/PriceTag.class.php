<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/18
 * Time: 20:47
 */
require_once 'tasks/CoolShow/PriceTagSql.class.php';
class PriceTag
{
    public $identity;   //价格标签ID
    public $price_tag;  //价格标签

    public function __construct()
    {
        $this->identity = '';
        $this->price_tag = '';
    }

    public function getSelectPriceTagSql(){
        $sql = sprintf(SQL_SELECT_PRICE_LIST);
        return $sql;
    }
}