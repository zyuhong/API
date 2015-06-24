<?php 
defined("SQL_SELECT_LIVE_WALLPAPER")
	or define("SQL_SELECT_LIVE_WALLPAPER", "SELECT pay.*, lwp.*, dl.download_times "
								  ." FROM tb_yl_live_wallpaper lwp"
								  ." LEFT JOIN tb_yl_live_wallpaper_download dl ON dl.cpid = lwp.cpid "
								  ." LEFT JOIN tb_yl_pay pay ON pay.waresid = lwp.waresid AND pay.appid = lwp.appid " 	
								  ." WHERE 1=1 %s "
								  ." ORDER BY lwp.id DESC  "
								  ." LIMIT %d, %d");

defined("SQL_COUNT_LIVE_WALLPAPER")
	or define("SQL_COUNT_LIVE_WALLPAPER", "SELECT COUNT(id) "
								  ." FROM tb_yl_live_wallpaper lwp"
								  ." WHERE 1=1 %s ");

	