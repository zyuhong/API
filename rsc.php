<?php
/**
 * 根据资源类型和ID获取单个资源的协议
 * 
 * $type : 资源类型
 * $id   : 资源ID
 */
session_start();
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}
	
$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:0);  //cooltype:主题、壁纸、铃声、字体等分类
$strId     = isset($_GET['id'])?$_GET['id']:0;
$nType     = isset($_GET['reqType'])?$_GET['reqType']:0;//请求的类型主要是壁纸用来区分高清图片
$nAdType     = isset($_GET['adtype'])?$_GET['adtype']:0;//请求的类型为获取专题1:专题 
$nWidth    = (int)(isset($_GET['width'])?$_GET['width']:0);
$nHeight   = (int)(isset($_GET['height'])?$_GET['height']:0);
$nSceneCode  = isset($_GET['scenecode'])?$_GET['scenecode']:0;
$nKernelCode = isset($_GET['kernelcode'])?$_GET['kernelcode']:0;
$nIsOrder   = isset($_GET['isorder'])?$_GET['isorder']:0; //是否命令
$nIsShow   = isset($_GET['isshow'])?$_GET['isshow']:0; //是否命令
$nDisplayType  = isset($_GET['displaytype'])?$_GET['displaytype']:0; //是否分离托盘和图标打点0全部1托盘2图标
$nIsLarge   = isset($_GET['islarge'])?$_GET['islarge']:0; //是否图片
$nIsGoing   = isset($_GET['isgoing'])?$_GET['isgoing']:0; //是否锁定
$strTitle   = isset($_GET['title'])?$_GET['title']:0; //推送标题
$strContent = isset($_GET['content'])?$_GET['content']:0; //推送内容描述
$strUrl 	= isset($_GET['url'])?$_GET['url']:0; //推送内容描述

require_once 'configs/config.php';
require_once("tasks/CoolShow/CoolShowSearch.class.php");

$result = array('coolshow'=>'order');
if (!$nIsOrder){
	$coolshow = new CoolShowSearch();
	$result = $coolshow->getRsc($nCoolType, $strId);	
}

$arrResult = array('isdirect'=>true,
					'entity'=>array('pushtype'=>(int)$nCoolType,
									'isorder' =>$nIsOrder?true:false,
									'pushDisplayType' => $nDisplayType,
									'isshow'  => $nIsShow,
									'title'   => $strTitle,
									'content' => $strContent,
									'isgoing' => $nIsGoing?true:false,
									'islarge' => $nIsLarge?true:false,
									'url' 	  => $strUrl,
									'id' 	  => $strId,
									$result['key']=>$result['result'])
			       );

echo json_encode($arrResult); 
