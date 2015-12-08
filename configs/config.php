<?php

$dir = dirname(__FILE__);
if (file_exists($dir.'/config_test.php')) {
    include $dir.'/config_test.php';
} else {
    include $dir."/config_release.php";
}