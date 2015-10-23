<?php
/**
 * 获取当前资源专辑
 *
 *兼容了安卓壁纸的资源
 */
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:0);  //cooltype:主题、壁纸、铃声、专题等分类
$bColor    = isset($_GET['iscolor'])?$_GET['iscolor']:false;  //iscolor:获取延时关键词
require_once 'tasks/CoolShow/CoolShowSearch.class.php';

$rsc = new CoolShowSearch();
$result = $rsc->getHotWord($nCoolType, $bColor);	

echo $result;