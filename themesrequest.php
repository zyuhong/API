<?php
/*
 * 查询主题列表：
* 	http: get协议
* ?type=theme&reqNum=20&page=0
* 返回结果
* JSON示例
* {
* "total_number": 300
* "ret_number": 20
* "themes":
* [
* 		{
*            "id": 11488058246,
*            “author” : “little”,
*            “name”: “圆圆”
*            “description”:”这是一个团团圆圆的主题”,
*            “them_file_url”: ”../1.theme”,
*             "created_at": "20110406174655",
*             “main_prev_url”: “http://www.coolshow.com/1.jpg“,
*             “prev_img_num”:3,
*             “prev_imgs”:
*             [
*             		{“img_url”: “http://www.coolshow.com/1.jpg”},
*             		{“img_url”:” http://www.coolshow.com/2.jpg”},
*             		{“img_url”:” http://www.coolshow.com/3.jpg”}
*             ]
*      },
*      ...
*],
*}
*/
//以下部分测试通过
//
require_once 'public/public.php';

$bSign = checkSign($_GET);
if(!$bSign){
    echo get_rsp_result(false, 'sign fail');
    exit();
}

	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
		$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
		
		$start 	  = $req_num * $req_page;
	}else{
		$req_page = (int)(isset($_POST['start'])?$_POST['start']:0);
		$req_num  = (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start    = $req_page;		
	}
	
	if($req_num === null || $req_page === null){
//		echo($themesList->getFaultResult(-1));
		exit; //错误请求
	}
	
	$result = is_numeric($req_num);
	if(!$result){
//		echo($themesList->getFaultResult(-1));
		exit; //错误请求
	}
	
	$result = is_numeric($req_page);
	if(!$result){
//		echo($themesList->getFaultResult(-1));
		exit; //错误请求
	}
	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$type = (int)(isset($_GET['moduletype'])?$_GET['moduletype']:COOLXIU_TYPE_THEMES);
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow($type, $start, $req_num);	

	echo $json_result;
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
// 	$reqStatis = new ReqStatis();
	
// 	$kernel   = (int)(isset($_GET['kernelCode']))?$_GET['kernelCode']:1;
// 	$width 	  = (int)(isset($_GET['width']))?$_GET['width']:480;
// 	$height   = (int)(isset($_GET['height']))?$_GET['height']:800;
// 	$req_type = (int)(isset($_GET['type'])?$_GET['type']:0);
// 	$channel  = (int)(isset($_GET['chanel'])?$_GET['chanel']:0);
// 	$vercode  = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
	
// 	$id = '';
// 	$cpid = '';
// 	$url = '';
//  	$reqStatis->recordRequest($req_type, COOLXIU_TYPE_THEMES, $height, $width, $kernel,
//  							  $id, $cpid, $url, $channel, $vercode);
	
	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveRequest(COOLXIU_TYPE_THEMES);
?>