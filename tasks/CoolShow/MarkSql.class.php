<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/12/2
 * Time: 20:56
 */

defined("SQL_SELECT_MARK_LIST")
    or define("SQL_SELECT_MARK_LIST", " SELECT res.`res_id`, res.`cooltype`, res.`mark_id`, res.`position`,mark.`url` "
                                        ." FROM `tb_qiku_mark_resource` res "
                                        ." LEFT JOIN `tb_qiku_mark_list` mark ON res.`mark_id` = mark.`identity`"
                                        ." WHERE res.valid=1 and mark.valid=1 ");