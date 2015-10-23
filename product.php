<?php

require_once 'tasks/Product/ProductDb.class.php';
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

$nAll = (int)(isset($_GET['all'])?$_GET['all']:0);

$productDb = new ProductDb();
$json_result = $productDb->getProductList($nAll);
echo $json_result;
 