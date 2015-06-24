<?php
require_once 'configs/config.php';
class DesignerProtocol 
{
	public $cyid;	//设计师CYID
	public $name; 	//设计师昵称
	public $iconurl;//投向地址
	public $mood;	//签名
	public $date;	//加入时间
	function __construct(){
		$this->cyid = '';	
		$this->name = '';
		$this->iconurl = '';
		$this->mood = '';
		$this->date = date('Y-m-d');
	}
	
	public function setProtocol($row)
	{
		$this->cyid		= isset($row['username'])?$row['username']:'';
		$this->name 	= isset($row['devname'])?$row['devname']:'';
		$iconurl = isset($row['iconurl'])?$row['iconurl']:'';
		if (empty($iconurl)) {
			global  $g_arr_host_config;
			$iconurl = $g_arr_host_config['cdnhost'].'/themes/designer/default.png';
		}
		$this->iconurl 	= $iconurl;
		$this->mood    	= isset($row['mood'])?$row['mood']:'';
		$strDate		= isset($row['insert_time'])?$row['insert_time']:date('Y-m-d');
		$this->date 	= strftime('%Y-%m-%d', strtotime($strDate));
	}
}