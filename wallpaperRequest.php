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
	 * "wallpaper":
	 * [
	 * 		{
	 *            "id": 11488058246,
	 *            “author” : “little”,
	 *            “name”: “圆圆”
	 *            “description”:”这是一个团团圆圆的主题”,
	 *            “url”: ”../1.wp”,
	 *             "created_at": "Tue May 31 17:46:55 +0800 2011",
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
require_once 'public/public.php';
	
	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
		$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
	
		$start 	  = $req_num * $req_page;
	}else{
		$req_page 	= (int)(isset($_POST['start'])?$_POST['start']:0);
		$req_num 	= (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start   	= $req_page;
	}
	
	if($req_num === null || $req_page === null){
		echo($wp_list->getFaultResult(-1));
		exit; 
	}
	
	$result = is_numeric($req_num);
	if(!$result){
		echo($wp_list->getFaultResult(-1));
		exit; 
	}
	
	$result = is_numeric($req_page);
	if(!$result){
		echo($wp_list->getFaultResult(-1));
		exit; 
	}

	
	require_once 'configs/config.php';
	require_once("tasks/CoolShow/CoolShowSearch.class.php");
	
	$coolshow = new CoolShowSearch();
	$json_result = $coolshow->getCoolShow(COOLXIU_TYPE_WALLPAPER, $req_page, $req_num);
	
	echo $json_result;
	
// 	require_once 'tasks/statis/ReqStatis.class.php';
	
// 	$width 	  = (isset($_GET['width']))?$_GET['width']:480;
// 	$height   = (isset($_GET['height']))?$_GET['height']:800;
// 	$req_type = (isset($_GET['reqType']))?$_GET['reqType']:0;
	
// 	$reqStatis = new ReqStatis();
// 	$reqStatis->recordRequest($req_type, COOLXIU_TYPE_WALLPAPER, $height, $width);

	require_once 'tasks/Records/RecordTask.class.php';
	$rt = new RecordTask();
	$rt->saveRequest(COOLXIU_TYPE_WALLPAPER);
?>