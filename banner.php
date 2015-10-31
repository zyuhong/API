<?php
/**
 * banner区资源列表获取接口
 * 
 * $type : 资源类型
 * 
 */
	
require_once 'tasks/CoolShow/CoolShowSearch.class.php';
require_once 'configs/config.php';
require_once 'public/public.php';

$nCoolType = (int)(isset($_GET['type'])?$_GET['type']:4);  
$bAlbum    = (int)(isset($_GET['album'])?$_GET['album']:0);
$protocolCode = (int)(isset($_GET['protocolCode'])?$_GET['protocolCode']:1);
$nNum = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:20);
$nPage = (int)(isset($_GET['page'])?$_GET['page']:0);

$nStart = $nPage *  $nNum;

$coolshow = new CoolShowSearch();
if(isset($_POST['statis'])){
	$json_param = isset($_POST['statis'])?$_POST['statis']:'';

	$json_param = stripslashes($json_param);
	$arr_param = json_decode($json_param, true);
	$protocolCode = (int)(isset($arr_param['protocolCode'])?$arr_param['protocolCode']:0);
	$strProduct   = isset($arr_param['product'])?$arr_param['product']:'';
}
/**
 * 以下函数为COOLUI5.5的banner区自运营资源和COOLUI6.0改成了专辑做了区别，
 * 逻辑一样，为了统一主题/锁屏等此处未动，COOLUI6.0单独做了个函数
 * 以protocolCode >=2为分界线，但是方便运营最终还是需要合并，后期看提供的资源的兼容性考虑代码合一
 * 
 */

if($protocolCode >= 2){
	$json_result = $coolshow->getBannerList($nCoolType, $bAlbum, $nStart, $nNum, $protocolCode, $strProduct);
}else{
	$json_result = $coolshow->getBanner($nCoolType);	
}

echo $json_result;
 
require_once 'tasks/Records/RecordTask.class.php';
 
$rt = new RecordTask();
$rt->saveBanner($nCoolType);
