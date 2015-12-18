<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/18
 * Time: 20:56
 */

defined("SQL_SELECT_PRICE_LIST")
    or define("SQL_SELECT_PRICE_LIST", " SELECT cpid, cooltype, price FROM `tb_qiku_res_price` WHERE valid = 1 ");