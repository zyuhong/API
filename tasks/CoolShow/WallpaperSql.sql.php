<?php 
/**
 * 第一版酷派壁纸信息
 */
defined("SQL_SELECT_WALLPAPER_INFO")
	or define("SQL_SELECT_WALLPAPER_INFO", "SELECT * FROM tb_yl_wallpaper_info "
							." WHERE type=%d AND width=%d AND height=%d "
							." ORDER BY folder DESC  "
							." LIMIT %d, %d ");

defined("SQL_COUNT_WALLPAPER_INFO")
	or define("SQL_COUNT_WALLPAPER_INFO", "SELECT COUNT(*) FROM tb_yl_wallpaper_info "
							." WHERE type = %d AND width = %d AND height = %d "
							." ORDER BY id ASC");
/**
 * 获取壁纸专辑信息
 */
defined("SQL_SELECT_WALLPAPER_ALBUMS")
	or define("SQL_SELECT_WALLPAPER_ALBUMS",  "SELECT wp.* "
							." FROM tb_yl_albums_res ares "
							." LEFT JOIN tb_yl_wallpaper wp ON wp.cpid = ares.cpid"
							." WHERE ares.albumid = '%s' AND ares.valid =1 AND ares.cooltype = 3 AND wp.valid = 1 AND wp.width= %d AND wp.height= %d "
							." ORDER BY wp.asort DESC "		);

/**
 * 获取混合安桌壁纸bnner区信息
 */
defined("SQL_SELECT_WALLPAPER_BANNER_TOP")
	or define("SQL_SELECT_WALLPAPER_BANNER_TOP", "SELECT DISTINCT b.identity AS adid, b.url AS url "
							."		FROM tb_yl_banner_list  bl "
							." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid "
							."		WHERE bl.valid = 1 AND bl.cooltype = 2 AND b.valid = 1 AND b.istop = 1 ");

	
/**
 * 获取壁纸bnner区信息
 */
defined("SQL_SELECT_WALLPAPER_BANNER_TOP_LIST")
	or define("SQL_SELECT_WALLPAPER_BANNER_TOP_LIST", "SELECT wp.* "
							."		FROM tb_yl_banner_list  bl "
							." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid "
							." 		LEFT JOIN tb_yl_wallpaper wp ON wp.cpid = bl.cpid "
							."		WHERE bl.valid = 1 AND bl.cooltype = 2 AND b.valid = 1 AND b.istop = 1 "
							."      AND wp.valid = 1  AND wp.width= %d AND wp.height= %d AND b.identity = '%s' ");
	
/**
 * 获取壁纸bnner区信息
 */							
defined("SQL_SELECT_WALLPAPER_BANNER")
	or define("SQL_SELECT_WALLPAPER_BANNER", "SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, wp.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_wallpaper wp ON wp.cpid = bl.cpid "	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 2 AND b.valid = 1 AND b.istop = 0 AND wp.valid = 1  AND width= %d AND height= %d " 
									."		ORDER BY wp.folder DESC ");
	
/**
 * 获取壁纸下载地址
 */	
defined("SQL_SELECT_WLLPAPER_LARGE_URL")
	or define("SQL_SELECT_WLLPAPER_LARGE_URL", "SELECT mid_url, url FROM tb_yl_wallpaper_info WHERE id='%s'");

/**
 * 获取banner壁纸下载地址
 */
defined("SQL_SELECT_WLLPAPER_BANNER_LARGE_URL")
	or define("SQL_SELECT_WLLPAPER_BANNER_LARGE_URL", "SELECT mid_url, url FROM tb_yl_wallpaper WHERE id='%s'");

/**
 * 获取酷派精品壁纸列表
 */	
defined("SQL_SELECT_CHOICE_WALLPAPER_INFO")
	or define("SQL_SELECT_CHOICE_WALLPAPER_INFO", "SELECT wp.* FROM tb_yl_wallpaper wp "
                                                ." LEFT JOIN tb_yl_wallpaper_download wpdl on wp.id = wpdl.cpid "
												." WHERE wp.valid = 1 AND width=%d AND height=%d %s "
												." %s  "
												." LIMIT %d, %d ");
//defined("SQL_SELECT_CHOICE_WALLPAPER_INFO")
//    or define("SQL_SELECT_CHOICE_WALLPAPER_INFO", "SELECT * FROM tb_yl_wallpaper "
//                                        ." WHERE valid = 1 AND width=%d AND height=%d %s "
//                                        ."  ORDER BY asort DESC, insert_time DESC  "
//                                        ." LIMIT %d, %d ");
	
defined("SQL_COUNT_CHOICE_WALLPAPER_INFO")
	or define("SQL_COUNT_CHOICE_WALLPAPER_INFO", "SELECT COUNT(*) FROM tb_yl_wallpaper "
												." WHERE 1=1 AND valid = 1 AND width = %d AND height = %d  %s ");
/**
 * 获取百变壁纸列表
 */
defined("SQL_SELECT_AMAZE_WALLPAPER_INFO")
    or define("SQL_SELECT_AMAZE_WALLPAPER_INFO", "SELECT * FROM tb_qiku_vary_wp "
                                                ." WHERE valid = 1 AND height=%d ORDER BY insert_time DESC "
                                                ." LIMIT %d, %d ");

defined("SQL_COUNT_AMAZE_WALLPAPER_INFO")
    or define("SQL_COUNT_AMAZE_WALLPAPER_INFO", "SELECT count(1) FROM tb_qiku_vary_wp "
                                                ." WHERE valid = 1 AND height=%d ");