<?php

function dump()
{
    $args = func_get_args();

    echo '<pre>';

    foreach ($args as $a) {
        var_dump($a);
    }

    echo '</pre>';
}

function dd()
{
    header('Content-type: text/html; charset=utf-8');
    call_user_func_array('dump', func_get_args());
    die();
}

/**
 * 获得请求ip
 */
function realIp()
{
    static $realip = NULL;

    if ($realip !== NULL) {
        return $realip;
    }

    $proxyIp = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $proxyIp) {
        // `X_FORWARDED_FOR`存在伪造可能, 不能作为IP判断依据, 这里只对公司代理
        // 的IP段进行处理, `224.3.6.0`为特殊标记, 出现该标记时以前一段数据作为用
        // 户IP. 参考*SO-193*
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $len = sizeof($arr);

        if ($len >= 2 && $arr[$len - 1] == $proxyIp) {
            $realip = trim($arr[$len - 2]);
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        $realip = $_SERVER['REMOTE_ADDR'];
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

    return $realip;
}

// 十进制转换三十六进制
function dec36($int, $format = 8)
{
    $dic = [
        0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
        'a', 'b', 'c', 'd', 'e', 'f', 'g',
        'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z'
    ];

    $dicLen = count($dic);
    $arr = [];

    while ($int) {
        $arr[] = $dic[bcmod($int, $dicLen)];
        $int = floor(bcdiv($int, $dicLen));
    }

    $arr = array_pad($arr, $format, $dic[0]);
    $return = strtolower(implode('', array_reverse($arr)));

    return $return;
}