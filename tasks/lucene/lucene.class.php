<?php
		
class Lucene
{
	public $name;
	public $content;
	
	public function __construct()
	{
		$this->name	= '';
		$this->content = '';
	}
	
	public function setParam($row)
	{
		$strContent		= json_decode($row->content);
		$this->content	= isset($strContent)?$strContent:'';
		$this->name		= isset($row->name)?$row->name:'';
	}
}
