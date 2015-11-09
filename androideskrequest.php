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
	session_start();
    require_once 'public/public.php';

	defined("YL_ADROIDESK_WP_COVER")
		or define("YL_ADROIDESK_WP_COVER", 1);
	defined("YL_ADROIDESK_WP_COVER_LIST")			//获取推荐列表
		or define("YL_ADROIDESK_WP_COVER_LIST", 2);
	defined("YL_ADROIDESK_WP")
		or define("YL_ADROIDESK_WP", 0);			//根据请求类型获取壁纸
	defined("YL_ADROIDESK_WP_RANDOM")
		or define("YL_ADROIDESK_WP_RANDOM", 3);			//获取随机两张壁纸(百变壁纸)
	defined("TOTAL_PAGE")
		or define("TOTAL_PAGE", 50);			//1900张最新壁纸随机
		
	//$type: 0=>壁纸请求  1=>壁纸封面列表请求  2=>封面内容（广告）请求 3=>百变壁纸请求
	$type = (int)(isset($_GET['type'])?$_GET['type']:0);
	$num = (int)(isset($_GET['retNum'])?$_GET['retNum']:2);
	if(isset($_GET['page']) && isset($_GET['reqNum'])){
		$req_page = (int)(isset($_GET['page'])?$_GET['page']:0);
		$req_num  = (int)(isset($_GET['reqNum'])?$_GET['reqNum']:10);
	
		$start 	  = $req_num * $req_page;
	}else{
		$req_page 	= (int)(isset($_POST['start'])?$_POST['start']:0);
		$req_num 	= (int)(isset($_POST['limit'])?$_POST['limit']:10);
		$start   	= $req_page;
	}

 	$width 	  = (int)isset($_GET['width'])?$_GET['width']:720;
	$height   = (int)isset($_GET['height'])?$_GET['height']:1280;
	$req_type = (int)isset($_GET['reqType'])?$_GET['reqType']:0;		//壁纸类型	
	$req_type = (int)isset($_GET['code'])?$_GET['code']:$req_type;	//兼容旧版本未做动态分类的代码，新版本实际类型为code字段值
	$channel   = (int)(isset($_GET['channel'])?$_GET['channel']:0);
	$sorttype  = (int)isset($_GET['sort'])?$_GET['sort']:0;
	
	$product  = trim(isset($_GET['product'])?$_GET['product']:"");
	$vercode  = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
	
	require_once("tasks/androidWallpaper/AndroidWallpaperDb.class.php");
	require_once("configs/config.php");
	require_once 'lib/WriteLog.lib.php';
	require_once 'public/public.php';

	if($req_num === null || $req_page === null){
		echo(getFaultResult(-1));
		exit; //错误请求
	}
//	$mts = microtime_float();
//	Log::write("Adrequset::write recorde start:".microtime(), "log");
	$result = is_numeric($req_num);
	if(!$result){
		echo(getFaultResult(-1));
		exit; //错误请求
	}
	
	$result = is_numeric($req_page);
	if(!$result){
		echo(getFaultResult(-1));
		exit; //错误请求
	}
	
	require_once 'tasks/CoolShow/CoolShowSearch.class.php';
	require_once 'tasks/statis/ReqStatis.class.php';
	require_once 'tasks/Records/RecordTask.class.php';
	$reqStatis = new ReqStatis();
	$rt = new RecordTask();
	
	$wp_list = new AndroidWallpaperDb();	
	$wp_list->setProduct($product);	
	
	switch($type){
		case YL_ADROIDESK_WP:{
			if ($req_type == 20){
				$req_type = 0;
				$coolshow = new CoolShowSearch();
				$result = $coolshow->getChoiceWallpaer($req_type, $start, $req_num);
			}else{
				$result = getAndroidesk($wp_list, $width, $height, $start, $req_num, $req_type, $sorttype, $channel);
			}
// 			$kernel = 1; $id = ''; $cpid = ''; $url = '';
// 			$reqStatis->recordRequest($req_type, COOLXIU_TYPE_ANDROIDESK_WALLPAPER, $height, $width,  
// 								  $kernel, $id, $cpid, $url, 
// 								  $channel, $vercode);
			
			$rt->saveRequest(COOLXIU_TYPE_ANDROIDESK_WALLPAPER);
			
		}break;
		case YL_ADROIDESK_WP_COVER:{
			$result = getAdroideskCover($wp_list);

// 			$reqStatis->recordCoverRequest();
			$rt->saveBanner(COOLXIU_TYPE_ANDROIDESK_WALLPAPER);
				
		}break;
		case YL_ADROIDESK_WP_COVER_LIST:{
			$result = getAdroidestCoverList($wp_list, $width, $height, $start, $req_num, $req_type);
			$adid = isset($_GET['adid'])?$_GET['adid']:"";
			$reqStatis->recordCoverListRequest($adid);
		}break;
		case YL_ADROIDESK_WP_RANDOM:{
            $req_num = 12;    //修正客户端请求 req_num = 8的旧版本，新版本统一 req_num = 12
			$filterid = isset($_GET['filterid'])?$_GET['filterid']:'';
			$filter_arr = explode(",", $filterid);
			$arr_cpid = array();
			$arr_return = array();
			while(count($arr_cpid) < $num) {
				$start 	  = $req_num * rand(0, TOTAL_PAGE);
				$tmp_result = getAndroidesk($wp_list, $width, $height, $start, $req_num, $req_type, $sorttype, $channel);
				$arr_result = json_decode($tmp_result, true);
				$i = rand(1, $req_num);
				$tmp_cpid = $arr_result['wallpapers'][$i]['cpid'];
				//随机壁纸既不再filterid中，也不在已有数组中
				if(empty($tmp_cpid)) {
					continue;
				}
				if(!in_array($tmp_cpid, $filter_arr) && !in_array($tmp_cpid, $arr_cpid)) {
					array_push($arr_cpid, $tmp_cpid);
					array_push($arr_return, $arr_result['wallpapers'][$i]);
				}				
			}
			 $result = json_encode(array('wallpapers'=>$arr_return));
			
		}break;
		default:
			break;
	}
//	$mte = microtime_float();
//	Log::write("Adrequset::write recorde end:".microtime(), "log");
//	Log::write("Adrequset::write recorde total:".($mte-$mts), "log");
	echo $result;
/////////////////////////////////////////////////////////////////////////////////////	
//函数解体	
/**
 * 更加请求类型获取壁纸
 * @param unknown_type $wp_list
 * @param unknown_type $width
 * @param unknown_type $height
 * @param unknown_type $start
 * @param unknown_type $req_num
 * @param unknown_type $req_type
 * @return string|unknown
 */

	function getAndroidesk($wp_list, $width, $height, $start, $req_num, $req_type, $sorttype, $channel){		
		$wp_list->setSearchCondition($width, $height, $start, $req_num, $req_type);
		if($channel == 0){
			$json_result = $wp_list->searchWpList($sorttype);
		}else{
			$json_result = $wp_list->searchWpListForWeb($sorttype);
		}
		if(!$json_result){
			return getFaultResult(-1);
		}
		return $json_result;
	}
/**
 * 获取广告、推荐封面
 * @param unknown_type $wp_list
 * @return string|unknown
 */	
	function getAdroideskCover($wp_list)
	{
		$coolshow = new CoolShowSearch();
		$arrCpBanner = $coolshow->getWPBannerTop();
		
		$arrAdBanner = $wp_list->searchAdCover();

		$arrBanner = mergeBanner($arrCpBanner, $arrAdBanner);
		if($arrBanner === false){
			$result =  get_rsp_result(false, 'search adver list failed');
			return $result;
		}
		
		$rsp =  array('number' => count($arrBanner),
		  			  'adconver' => $arrBanner);
		
		$json_result = json_encode($rsp);
		
		return $json_result;
	}
	
	function mergeBanner($arrCpBanner, $arrAdBanner)
	{
		if (is_array($arrCpBanner) && is_array($arrAdBanner)){
			$arrBanner =  array_merge($arrCpBanner, $arrAdBanner);	
			return $arrBanner;
		}
		if (is_array($arrCpBanner) && !$arrCpBanner)return $arrCpBanner;
		if (is_array($arrAdBanner) && !$arrAdBanner)return $arrAdBanner;
		
		return false;
	}
	/**
	 * 根据封面获取广告、封面列表
	 * @param unknown_type $wp_list
	 * @param unknown_type $width
	 * @param unknown_type $height
	 * @param unknown_type $start
	 * @param unknown_type $req_num
	 * @param unknown_type $req_type
	 * @return string|unknown
	 */
	function getAdroidestCoverList($wp_list, $width, $height, $start, $req_num, $req_type){		
		$adid = isset($_GET['adid'])?$_GET['adid']:"";
		$adid = urlencode($adid);
		
		if(empty($adid)){
			return get_rsp_result(false, 'search adver adid is empty');
		}
		
		if(strlen($adid) < 16){
			$coolshow = new CoolShowSearch();
			$json_result = $coolshow->getWPBannerTopList($adid);
			return $json_result;
		}
		
		$wp_list->setSearchCondition($width, $height, $start, $req_num, $req_type);
		$json_result = $wp_list->searchAdver($adid);
		if(!$json_result){
			return get_rsp_result(false, 'search adver failed');
		}
		return  $json_result;
	}
	
?>