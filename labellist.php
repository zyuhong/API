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
 * msubtype 
 *类型的子类型 
 * 
 * width:分辨率宽
 * height:分辨率高
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

session_start();
require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'configs/config.php';
require_once 'tasks/label/LabelDb.class.php';
try{
	
	$moduletype	 = (int)(isset($_GET['moduletype'])?$_GET['moduletype']:3);
	$msubtype	 = (int)(isset($_GET['msubtype'])?$_GET['msubtype']:0);
	$width	 	 = (int)(isset($_GET['width'])?$_GET['width']:0);
	$height 	 = (int)(isset($_GET['height'])?$_GET['height']:0);
	$req_page 	 = (int)(isset($_GET['page'])?$_GET['page']:0);
	$req_num 	 = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:50);
	
	$start 	  	 = $req_num * $req_page;
	$limit 		 = $req_num;
	
	$labelDb = new LabelDb();
	$labelDb->setLabelParam($moduletype, $msubtype, $width, $height);
	$json_ret = $labelDb->searchLabel($start, $limit);
	
	echo $json_ret;
	
}catch(Exception $e){
	Log::write('labellist::exception error:'.$e->getMessage(), 'log');
	echo get_rsp_result(false);
}
?>