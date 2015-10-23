<?php
/**
 * 统计接口 versionCode >= 39
 * 旧static、wpdlStatic接口全部作废
 *
 * 预览、下载、应用主动上报接口
 * 统一各个模块的上报信息
 * service/mstatis.php?id=xxx&cpid=xxx&moduletype=1&type=1&optype=1&applytype=0
 *
 * 说明：
 * 方法：POST
 *
 *id:资源唯一ID
 *
 *cpid:资源ID（同分辨率的相同资源）
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
 * type:模块下的分类
 * 如壁纸的：美女、动物、植物、视觉等
 * 
 * optype:操作类型
 * 0：请求        //一般不用，当全部通过第三方的服务器时，做自己的统计用
 * 1：预览
 * 2：下载
 * 3：应用
 *
 * applytype:操作子类型，当操作类型为应用(3)时作为应用的类型
 * 1： 壁纸->待机
 * 2： 壁纸->桌面
 * 3： 壁纸->主菜单
 *
 * POST字段 ：为手机基本信息
 * 			product:产品名称
 * 			cyid： 酷云账号（暂无）
 * 			imsi： 终端串号
 *			meid   终端串号
 * 			versionCode：应用版本
 * 			width：  分辨率宽
 * 			height： 分辨率高
 * 			network: 网络模式2g/3g/wifi
 */

require_once 'lib/WriteLog.lib.php';
require_once 'public/public.php';
require_once 'tasks/Records/RecordTask.class.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, '');
    exit();
}

if (!isset($_GET['id'])){
	Log::write('statis:: id is not set', 'log');
	exit(get_rsp_result(false, 'id is empty'));
}

//mongodb记录
$rt = new RecordTask();
$result = $rt->saveAdStaticRecord();

echo get_rsp_result(true);

