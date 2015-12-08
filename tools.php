<?php
	require_once 'lib/MemDb.lib.php';	
	require_once ('public/public.php');

	$bClear = isset($_GET['clear'])? $_GET['clear']:false;
	$strSet = isset($_GET['set'])? $_GET['set']:'';
	$strGet = isset($_GET['get'])? $_GET['get']:'';
	$bGetIp = isset($_GET['getip'])? $_GET['getip']:0;
	$strGetIp = isset($_GET['getips'])? $_GET['getips']:'';
    $strUpdateMark = (int)(isset($_GET['mark'])?$_GET['mark']:0);
	
	$memcached = new MemDb();
	if(!$memcached){
		echo "New MemDb failed";
		exit();
	}

	global $g_arr_memcache_config;
	$memcached->connectMemcached($g_arr_memcache_config);
	
	if($bClear){
		$result = $memcached->clearMemcache();
		if(!$result){
			echo get_rsp_result(0);
			exit();
		}
		echo get_rsp_result(1).'clear mem';
	}

	if(!empty($strSet)){
		$bResult = $memcached->setSearchResult('4a03c8f22641741b6e1d35b87d71a571', $strSet);
		if(!$bResult){
			echo "MemDb:setSearchResult() failed";
			exit();
		}
		
		$bResult = $memcached->getSearchResult('4a03c8f22641741b6e1d35b87d71a571');
		if(!$bResult){
			echo "getSearchResult() failed";
			exit();
		}
		echo get_rsp_result(1).'set mem'.$bResult;
	}
	
	if(!empty($strGet)){
		$bResult = $memcached->getSearchResult($strGet);
		if(!$bResult){
			echo "getSearchResult() failed";
			exit();
		}
		echo get_rsp_result(1).'get mem:'.$bResult;
	}
	
	if($bGetIp){
		$arr_memcache1['memcache'] = array(
 				array('host'=>'127.0.0.1', 'port'=>'11211', 'persistent'=>true,'weight'=>2),
		);
		
		$arr_memcache2['memcache'] = array(
				array('host'=>'127.0.0.1', 'port'=>'11211', 'persistent'=>true,'weight'=>2),
		);
		
		if($bGetIp == 1){
			$arr_memcache = $arr_memcache1;
		}
		
		if($bGetIp == 2){
			$arr_memcache = $arr_memcache2;
		}
		
		$memcached = new MemDb();
		$memcached->setConnect($arr_memcache);
		$bResult = $memcached->getSearchResult($strGetIp);
		if(!$bResult){
			echo "getSearchResult() failed";
			exit();
		}
		echo get_rsp_result(1).'get mem:'.$bResult;
	}

    if ($strUpdateMark) {
        require_once 'tasks/CoolShow/MarkDb.class.php';

        $mark = new MarkDb();
        $bResult = $mark->updateMarkList();
        if (! $bResult) {
            Log::write("update fail", "log");
            exit("Fail");
        }

        exit("SUCCESS");
    }
	
	
?>
	
	
	