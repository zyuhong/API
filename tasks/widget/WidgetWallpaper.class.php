<?php
class WidgetWallpaper extends Widget
{
	public function __construct()
	{
		$this->type = parent::YL_COOLSHOW_WALLPAPER;
		$this->name = 'wallpapers';
	}
	
	public function getSelectWidgetTagSql($size_res, $size_mid, $size_small)
	{
		$sql = '';
		$sql = sprintf(SQL_SELECT_WIDGET_TAG_WALLPAPER, $size_res, $size_mid, $size_small,
					   $this->width, $this->height);
		return $sql;
	}
	
	public function getSelectWidgetSql($size_res = '', $size_mid  = '', $size_small = '', $start = 0, $num = 1, $vercode = 0)
	{
		$sql = '';
		$sql = sprintf(SQL_SELECT_WIDGET_WALLPAPER, $size_res, $size_mid, $size_small,
				 					$start, $num);
		return $sql;
	}
	
	public function getSelectWidgetSizeTagSql()
	{
		$size = 'id_'.$this->width.'_'.$this->height;
		$sql = sprintf(SQL_SELECT_WIDGE_SIZE_TAG, $size);
		return $sql; 
	}
}