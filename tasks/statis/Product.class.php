<?php
class Product{
	public $name;
	public $imei;
	public $meid;
	public $imsi;
	
	public function __construct(){
		$this->name  =	'';
		$this->imei  =	'';
		$this->meid	 =	'';
		$this->imsi	 = 	'';
	}
	
	public function setProductParam($name, $imei, $meid, $imsi){
		$this->name  =	$name;
		$this->imei  =	$imei;
		$this->meid	 =	$meid;
		$this->imsi	 = 	$imsi;		
	}
	public function setProduct($name){
		$this->name = $name;
	}
}