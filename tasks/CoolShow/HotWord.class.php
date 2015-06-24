<?php

class HotWord
{
	const YL_SQL_HOTWORD = 'SELECT hotword FROM tb_yl_hotword WHERE cooltype = %d ';
	const YL_SQL_RGB_HOTWORD = 'SELECT rgb, hsv FROM tb_yl_hotword_color WHERE cooltype = %d ';
	public function __construct()
	{
	}
	
	public function getSelectHotWordSql($nCoolType, $bColor = false)
	{
		if (!$bColor){
			$sql = sprintf(self::YL_SQL_HOTWORD, $nCoolType);
		}else {
			$sql = sprintf(self::YL_SQL_RGB_HOTWORD, $nCoolType);
		}
		return $sql;
	}
}