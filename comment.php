<?php
/**
 *评论评分的终端获取接口
 * $type : 资源类型
 * $id   : 资源ID
 */

require_once 'public/public.php';

try{
	$id = isset($_GET['id'])?$_GET['id']:'';
	$cpid = isset($_GET['cpid'])?$_GET['cpid']:'';

	if(empty($id) && empty($cpid)){
		Log::write('comment is empty', 'log');
		exit;
	}
	
	$nType = (int)(isset($_GET['type'])?$_GET['type']:0);
	$page  = (int)(isset($_GET['page'])?$_GET['page']:0);
	$limit = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
		
	$skip = $page * $limit;
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");

	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShowComment($nType, $id, $cpid, $limit, $skip);

	echo $json_result;
	
}catch(Exception $e){
	Log::write('comment:: exception error:'.$e->getMessage(), 'log');
	exit;
}