<?php
/**
 * 主动上报统计接口
 * service/statis.php?id=xxx&moduletype=1&msubtype=1&optype=1&opsubtype=0
 * 
 * 说明：
 * 方法：POST
 * 
 * moduletype: 应用类型标识
 * 0 : 主题
 * 1 : 主题预览图（不用）
 * 2：酷派壁纸  //不用
 * 3：安卓壁纸
 * 4：铃声
 * 5：字体
 * 6：精灵解锁场景
 * 7：酷派秀widge
 * 
 * msubtype:模块子类型(分类)
 * 1: 铃声->来电铃声   酷派秀Widget->主题
 * 2: 铃声->短信提示音 酷派秀Widget->壁纸

 * optype:操作类型
 * 0：请求        //一般不用，当全部通过第三方的服务器时，做自己的统计用
 * 1：预览        
 * 2：下载
 * 3：应用
 * 
 * opsubtype:操作子类型，当操作类型为应用(3)时作为应用的类型
 * 1： 壁纸->待机
 * 2： 壁纸->桌面
 * 3： 壁纸->主菜单
 * 
 * POST字段 ：statis
 * POST值：product:产品名称
 * 			imsi： 终端串号
 * 			imei   终端串号
 *			meid   终端串号
 * 			version：应用版本
 * 			width：  分辨率宽
 * 			height： 分辨率高
 */

require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'configs/config.php';
require_once 'tasks/statis/StatisInterface.class.php';

try{
	if (!isset($_GET['id'])){
		Log::write('statis:: id is  not set', 'log');
		echo get_rsp_result(false);
		exit;
	}
	
	$statis = new StatisInterface();
	$result = $statis->doStatis();
	
	echo get_rsp_result($result);
	
}catch(Exception $e){
	Log::write("statis::exception error:".$e->getMessage(), "log");
	echo get_rsp_result(false);
}
?>