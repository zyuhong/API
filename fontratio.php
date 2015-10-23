<?php
/*
 * 查询字体分辨率列表
*/

require_once 'tasks/Font/FontDb.class.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

$font_db = new FontDb();
$arrRatio = $font_db->getFontRatio();
$result = array('ratio'=>$arrRatio);
$jsonResult = json_encode($result);
echo $jsonResult;