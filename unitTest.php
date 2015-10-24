<?php

require_once 'configs/config.php';
require_once 'public/public.php';

function unit_memcached_test(){
	global $g_arr_memcache;
	$oMucache = new mucache( $g_arr_memcache['memcache'], false);
	$oMucache->set('mail', 'zj@boyaa.com');
	echo $oMucache->get('mail');
}

function unit_memcache_test(){

	$memcache = new Memcache;
	$memcache->connect('127.0.0.1', 11211) or die ("Could not connect");
	$version = $memcache->getVersion();
 	echo "Server's version: ".$version."\n";
 	$tmp_object = new stdClass;
 	$tmp_object->str_attr = 'test';
 	$tmp_object->int_attr = 123;
 	$memcache->set('key', $tmp_object, false) or die ("Failed to save data at the server");
 	echo "Store data in the cache (data will expire in 1000 seconds)\n";
	$get_result = $memcache->get('key');
	echo "Data from the cache:\n";
	var_dump($get_result);	
}

//unit_memcache_test();

function unit_memcache_addserver_test(){

	$memcache = new Memcache;
	$memcache->addServer('127.0.0.1', 11211) or die ("Could not connect");
	$version = $memcache->getVersion();
	echo "Server's version: ".$version."\n";
// 	$tmp_object = new stdClass;
// 	$tmp_object->str_attr = 'test';
// 	$tmp_object->int_attr = 123;
// 	$memcache->set('key', $tmp_object, false) or die ("Failed to save data at the server");
// 	echo "Store data in the cache (data will expire in 1000 seconds)\n";
	$get_result = $memcache->get('key');
	echo "Data from the cache:\n";
	var_dump($get_result);
}

//unit_memcache_addserver_test();

function unit_cmd_test(){
	$to_ping = "www.baidu.com";
	$count = 2;
	$psize = 66;
	echo " Please be patient, this can take a few moments…\n<br><br>";
	flush();
	$ii = 1;
	while ($ii < 2) {
		echo "<pre>";
		echo $ii;
		exec("ping -n $count -l $psize $to_ping", $list);
		for ($i=0;$i < count($list);$i++) {
			print $list[$i]."\n";
		}
		echo "</pre>";
		flush();
		sleep(3);
		$ii++;
	}
	exec("ipconfig /all", $list);
	for ($i=0;$i < count($list);$i++) {
		print $list[$i]."\n"."<br>";
		flush();
	}	
}
//unit_cmd_test();


function unit_cmd_cq_pl_test(){
	$mail_to = "lijie1@yulong.com";
	$body = "cq pl test";
	$pl = "d:/mail.pl";
	echo " Please be patient, this can send a mail by pl…\n<br><br>";
	flush();
	$list= "";
	echo "<pre>";
	exec("cqperl ".$pl." ".$mail_to." ".$body, $list);
	for ($i = 0; $i < count($list); $i++) {
		print $list[$i]."\n";
	}
	echo "</pre>";
	flush();
	sleep(3);	
}
unit_cmd_cq_pl_test();

?>