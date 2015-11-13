<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/11/6
 * Time: 15:02
 */

require_once 'public/public.php';
require_once 'lib/Verify.php';
require_once 'lib/Type.php';
require_once 'tasks/CoolShow/CoolShowSearch.class.php';

$nType = Verify::check($_GET, "type", Type::INT, "default=0,min=0,max=13");
$id = Verify::check($_GET, "id");
$cpid = Verify::check($_GET, "cpid");
$kernel = Verify::check($_GET, "kernelCode", Type::INT);
$moduletype = Verify::check($_GET, "moduletype", Type::INT);

if(empty($id) ){
    $bRet = array('result'=>false, 'error'=>'id is empty');
    out_json($bRet);
}

$res = new CoolShowSearch();
$bRet = $res->getSrcSign($nType, $id, $kernel, $moduletype);

out_json($bRet, false);