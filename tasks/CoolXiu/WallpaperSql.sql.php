<?php
/**
 *数据库操作语句
 *
 *预定义数据库操作相关语句
 */
//wallpapers
define("SQL_COUNT_WALLPAPER", "SELECT COUNT(*) FROM tb_yl_wallpaper_info");
define("SQL_COUNT_WALLPAPER_BY_RATIO", "SELECT COUNT(*) FROM tb_yl_wallpaper_info WHERE width = %d AND height = %d ORDER BY id ASC");
define("SQL_COUNT_WALLPAPER_BY_TYPE", "SELECT COUNT(*) FROM tb_yl_wallpaper_info WHERE type = %d ORDER BY id ASC");
define("SQL_COUNT_WALLPAPER_BY_TYPE_RATIO", "SELECT COUNT(*) FROM tb_yl_wallpaper_info WHERE type = %d AND width = %d AND height = %d ORDER BY id ASC");
define("SQL_COUNT_WALLPAPER_BY_NEW", "SELECT COUNT(*) FROM tb_yl_wallpaper_info ORDER BY insert_time DESC");
define("SQL_COUNT_WALLPAPER_BY_NEW_RATIO", "SELECT COUNT(*) FROM tb_yl_wallpaper_info WHERE width = %d AND height = %d ORDER BY insert_time DESC");

define("SQL_INSERT_WALLPAPER_INFO","INSERT INTO tb_yl_wallpaper_info (cpid, name, folder, url, mid_url, small_url, size, note, insert_time, insert_user, md5, mid_md5, small_md5, author, type, width, height) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d, %d)");

define("SQL_SELECT_WALLPAPER_INFO_BY_LIMIT", "SELECT * FROM tb_yl_wallpaper_info ORDER BY id ASC  LIMIT %d, %d ");
define("SQL_SELECT_WALLPAPER_INFO_BY_RATIO_LIMIT", "SELECT * FROM tb_yl_wallpaper_info WHERE width=%d AND height=%d ORDER BY id ASC  LIMIT %d, %d ");
define("SQL_SELECT_WALLPAPER_INFO_BY_TYPE_LIMIT", "SELECT * FROM tb_yl_wallpaper_info WHERE type=%d ORDER BY id ASC  LIMIT %d, %d ");
define("SQL_SELECT_WALLPAPER_INFO_BY_TYPE_RATIO_LIMIT", "SELECT * FROM tb_yl_wallpaper_info WHERE type=%d AND width=%d AND height=%d ORDER BY id ASC  LIMIT %d, %d ");

defined("SQL_SELECT_WALLPAPER_ALBUMS")
	or define("SQL_SELECT_WALLPAPER_ALBUMS",  "SELECT * "
										." FROM tb_yl_albums a "
										." LEFT JOIN tb_yl_albums_res ares ON ares.identity = a.identity"
										." LEFT JOIN tb_yl_wallpaper_info wp ON wp.cpid = ares.resid"
										." WHERE ares.type = 2 AND a.valid =1 AND wp.valid = 1 AND width= %d AND height= %d ");

define("SQL_SELECT_WALLPAPER_INFO_BY_NEW_LIMIT", "SELECT * FROM tb_yl_wallpaper_info ORDER BY insert_time DESC  LIMIT %d, %d ");
define("SQL_SELECT_WALLPAPER_INFO_BY_NEW_RATIO_LIMIT", "SELECT * FROM tb_yl_wallpaper_info WHERE width=%d AND height=%d ORDER BY insert_time DESC  LIMIT %d, %d ");

//根据id获取URL
defined("SQL_SELECT_WLLPAPER_MID_URL")
	or define("SQL_SELECT_WLLPAPER_MID_URL", "SELECT mid_url AS url FROM tb_yl_wallpaper_info WHERE id='%s'");

defined("SQL_SELECT_WLLPAPER_LARGE_URL")
	or define("SQL_SELECT_WLLPAPER_LARGE_URL", "SELECT url FROM tb_yl_wallpaper_info WHERE id='%s'");

defined("SQL_SELECT_WALLPAPER_BANNER")
	or define("SQL_SELECT_WALLPAPER_BANNER", "SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, wp.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_wallpaper wp ON wp.cpid = bl.cpid "	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 3 AND b.valid = 1 AND wp.valid = 1  AND width= %d AND height= %d ");
?>