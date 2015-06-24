<?php
require_once 'configs/config.php';
require_once 'tasks/protocol/LabelProtocol.php';

class AlarmLabelProtocol extends LabelProtocol{
	public $number;				//数量
	public $size;				//大小
	public $download_times;		//下载次数
	
	public function __construct(){
		parent::__construct();	
		$this->number		= 0	;		//数量
		$this->size			= 0;		//文件大小KB
		$this->download_times = 0;		//下载次数
	}
	
	public function setProtocol($row){
		$this->number	= (int)(isset($row['num'])?$row['num']:0);
		$nSize			= (int)(isset($row['size'])?$row['size']:0);
		$this->size 	= (int)($nSize / 1024);	
		$this->download_times = (int)(isset($row['download_times'])?$row['download_times']:0);
		parent::setProtocol($row);
	}
}