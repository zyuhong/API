<?php
class ProductProtocol 
{
	public $product;		//产品名称
	public $width;			//宽度
	public $height;			//高度
	public $tag;			//机型的标签7系列、5系列、9系列
	public $kernel;		//主题内核版本
	
	function __construct(){
		$this->product	= '';
		$this->width	= '';
		$this->height	= '';
		$this->tag		= '';
		$this->kernel	= 3;
	}
	
	function setProductByDB($row){
		$this->product	= isset($row['product'])?$row['product']:'';
		$this->width	= (int)(isset($row['width'])?$row['width']:0);
		$this->height	= (int)(isset($row['height'])?$row['height']:0);
		$this->tag		= (int)(isset($row['tag'])?$row['tag']:0);	
		$this->kernel	= (int)(isset($row['kernelCode'])?$row['kernelCode']:0);	
	}
}