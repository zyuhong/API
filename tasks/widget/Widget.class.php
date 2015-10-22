<?php
	
class Widget
{
	const YL_COOLSHOW_THEME 	= 0;
	const YL_COOLSHOW_WALLPAPER = 1;	
	const WIDGET_DOWNLOD_PHP 	= '/service/widgetdl.php?id=%s&cpid=%s&type=%d&url=%s';//widget图片下载？？？？？
		
	public $type;
	public $name;
	public $width;
	public $height;
	public $kernel;
	public $widgeturl;
	
	public function __construct()
	{
		$this->type 	= 0;
		$this->name		= '';
		$this->width	= 0;
		$this->height	= 0;
		$this->kernel   = 1;
		$this->widgeturl = '';
	}
	public function setWidget($width, $height, $kernel)
	{
		$this->width	= $width;
		$this->height	= $height;
		$this->kernel   = $kernel;
	}
	
	public function getWidgetUrl($id, $cpid, $url)
	{
		$this->widgeturl = sprintf(self::WIDGET_DOWNLOD_PHP, $id, $cpid, $this->type, $url);
		return $this->widgeturl;
	}
}