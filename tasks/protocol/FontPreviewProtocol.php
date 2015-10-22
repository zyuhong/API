<?php

defined("FONT_DOWNLOD_PHP")
	or define("FONT_DOWNLOD_PHP", "/service/fontdl.php?id=%s");
	
class FontPreviewProtocol{
	public $largepreurl; 	//larg url
	public $url;			//URL
	public $width;			//width
	public $height;			//width
	function __construct(){
		$this->largepreurl  = '';
		$this->url			= '';
		$this->width		= '';
		$this->height		= '';
	}
	
	function setFontByDB($row){
		$this->width		= isset($row['width'])?$row['width']:'';
		$this->height		= isset($row['height'])?$row['height']:'';
		global $g_arr_host;
		$url_tmp = isset($row['url'])?$row['url']:'';
		$largepreurl_tmp = isset($row['largepreurl'])?$row['largepreurl']:'';
		$this->url		= $g_arr_host['cdnhost'].$url_tmp;
		$this->largepreurl  = $g_arr_host['cdnhost'].$largepreurl_tmp;
	}
}