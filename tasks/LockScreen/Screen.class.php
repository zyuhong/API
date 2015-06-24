<?php

abstract class Screen
{
	public $createTime;				//创建日期
	public $updateTime;				//更形日期
	protected  $_table;
	
	public function __construct()
	{
		$this->createTime	=  date("Y-m-d H:i:s");
		$this->updateTime	=  date("Y-m-d H:i:s");
	}
	
	abstract public function setScreen($row);
	abstract public function getCountScreenSql($kernelcode);
	abstract public function getSelectScreenSql($kernelcode, $nStart, $nNum);
}