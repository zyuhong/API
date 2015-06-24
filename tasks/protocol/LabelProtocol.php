<?php
require_once 'configs/config.php';

class LabelProtocol{
	public $code;		//索引
	public $index;		//索引
	public $chname;	//中文名字
	public $enname;	//英文名字
	
	public function __construct(){
		$this->code = 0;
		$this->index = 0;	
		$this->chname = '';	
		$this->enname = '';	
		$this->imgurl = '';	
	}
	
	public function setIndex($nIndex)
	{
		$this->index  = $nIndex;
	}
	
	public function setProtocol($row){
		$this->code   = isset($row['code'])?$row['code']:0;
		$this->index  = isset($row['sort'])?$row['sort']:0;
		$this->chname = isset($row['chname'])?$row['chname']:'';
		$this->enname = isset($row['enname'])?$row['enname']:'';
		
		$protocol = (int)(isset($_GET['protocolCode'])?$_GET['protocolCode']:0);
		global $g_arr_host_config;		
		if ($protocol >=3 ){
			$this->imgurl = $g_arr_host_config['cdnhost'].$row['newurl'];
		}else{
			$this->imgurl = $g_arr_host_config['cdnhost'].$row['imgurl'];
		}
	}
}