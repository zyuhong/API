<?php
/**
 *查询资源评论评分的接口
 * $type : 资源类型
 * $id   : 资源ID
 */


$nCoolType 	= (int)(isset($_GET['type'])?$_GET['type']:0);  			//cooltype:主题、壁纸、铃声、字体等分类
//$nCommType= isset($_GET['commType'])?$_GET['commType']:0;		//评论类型：0：全部评论 1：系统bug
$strId     	= isset($_GET['id'])?$_GET['id']:'';
$strCpid   	= isset($_GET['cpid'])?$_GET['cpid']:'';
$skip    	= (int)(isset($_GET['page'])?$_GET['page']:0);
$limit 		= (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);

require_once 'public/public.php';
require_once 'tasks/Records/ScoreRecord.class.php';

$scoreRecord = new ScoreRecord();
$result = $scoreRecord->searchCpidRecord($nCoolType, $strCpid, $limit, $skip);
if(!$result){
	exit(get_rsp_result(false, 'search record failed'));
}

echo $result;