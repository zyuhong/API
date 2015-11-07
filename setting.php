<?php
/**
 * Created by PhpStorm.
 * User: wangweilin
 * Date: 2015/11/7
 * Time: 8:56
 */

/**
 * 主动上报统计接口
 * service/setting.php
 *
 * 说明：
 * 方法：POST
 *
 * POST字段 ：statis
 * setting:ttwindow瀑布流
 * POST值：product:产品名称
 *                      imsi： 终端串号
 *                      imei   终端串号
 *                      meid   终端串号
 *                      version：应用版本
 *                      width：  分辨率宽
 *                      height： 分辨率高
 */

require_once 'lib/WriteLog.lib.php';
require_once 'tasks/Records/RecordTask.class.php';
require_once 'public/public.php';

$jsonSetting = isset($_POST['statis'])?$_POST['statis']:'';
Log::write("setting :".$jsonSetting, 'debug');

$rt = new RecordTask();
$rt->saveSetting();
