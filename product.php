<?php

require_once 'tasks/Product/ProductDb.class.php';

$nAll = (int)(isset($_GET['all'])?$_GET['all']:0);

$productDb = new ProductDb();
$json_result = $productDb->getProductList($nAll);
echo $json_result;
 