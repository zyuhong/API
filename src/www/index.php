<?php

require(dirname(dirname(__DIR__)) . '/config/config.php');

$loader = include ROOT_PATH . '/vendor/autoload.php';
$loader->addPsr4('Controller\\', ROOT_PATH . '/src/Application/Controller');

@list($nameUri, $queryStr) = isset($_SERVER['REQUEST_URI']) ? explode("?" , $_SERVER['REQUEST_URI']) : [];

$_SERVER['SCRIPT_NAME'] = $nameUri;
$_SERVER['PHP_SELF'] = $nameUri;
if (!empty($queryStr)) {
    parse_str($queryStr, $params);
    $_GET = array_merge($params, $_GET);
}

$str = trim($_SERVER["SCRIPT_NAME"], "/ ");   //过滤空格、斜杠
$tmp = explode("/", $str);
$num = count($tmp);

if ($num == 2 || php_sapi_name() == 'cli') {
    require(ROOT_PATH . '/src/Lib/Misc.php');

    $controller = "\\Controller\\" . ucfirst($tmp[0]);  //ucfirst:字符串首字母大写

    if (class_exists($controller)) {
        $action = $tmp[1]."Action";
        $oo = new $controller();
        if (method_exists($oo, $action)) {
            $oo->$action();
        } else {
            // Log::append("action do not exist", "log");
            // echo $action." not exist";
            header404();
        }
    } else {
        // Log::append("controller do not exist", "log");
        header404();
        // echo $controller.' not exist';
    }
} else {
    header404();
}

function header404()
{
    header('HTTP/1.1 404 Not Found');
    exit();
}