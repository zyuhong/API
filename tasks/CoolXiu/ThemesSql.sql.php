<?php
/**
 *数据库操作语句
 *
 *预定义数据库操作相关语句
 */
//themes
define("SQL_COUNT_THEMES", "SELECT COUNT(*) FROM tb_yl_theme_info");
define("SQL_COUNT_THEMES_BY_RATIO", "SELECT COUNT(*) FROM tb_yl_theme_info" 
								." WHERE width = %d AND height = %d" 
								." ORDER BY id ASC");
define("SQL_COUNT_THEMES_BY_TYPE", "SELECT COUNT(*) FROM tb_yl_theme_info" 
								." WHERE type = %d AND valid = 1");
define("SQL_COUNT_THEMES_BY_TYPE_RATIO", "SELECT COUNT(*) FROM tb_yl_theme_info" 
								." WHERE type = %d AND valid = 1 AND kernel = %d AND width = %d AND height = %d %s ");
define("SQL_COUNT_THEMES_BY_NEW", "SELECT COUNT(*) FROM tb_yl_theme_info" 
								." ORDER BY insert_time DESC");
define("SQL_COUNT_THEME_BY_NEW_RATIO", "SELECT COUNT(*) FROM tb_yl_theme_info "
								." WHERE width = %d AND height = %d");

define("SQL_SELECT_THEME_INFO_BY_LIMIT", "SELECT * FROM tb_yl_theme_info "
								." ORDER BY id DESC  LIMIT %d, %d ");

define("SQL_SELECT_THEME_INFO_BY_RATIO_LIMIT", "SELECT * FROM tb_yl_theme_info" 
								." WHERE width = %d AND height = %d "
								." ORDER BY id DESC"  
								." LIMIT %d, %d ");

define("SQL_SELECT_THEME_INFO_BY_TYPE_LIMIT", "SELECT pay.*, dl.download_times,"
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url," 
       							." theme.height, theme.width, theme.img_num," 
       							." theme.size,  theme.type, theme.note, theme.author, theme.md5,"
       							." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro, theme.star_level, " 
       							." theme.insert_user, theme.insert_time,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT *, DATE_FORMAT(insert_time, '%%Y-%%m-%%d')d"
								."		 FROM tb_yl_theme_info"
								."		 WHERE type = %d AND valid = 1" 
								."	 	 ORDER BY d DESC, asort ASC LIMIT %d, %d )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid" 
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	
								." ORDER BY theme.id DESC, prev_url ASC");

define("SQL_SELECT_THEME_INFO_BY_TYPE_RATIO_LIMIT", "SELECT pay.*, dl.download_times, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url," 
       							." theme.height, theme.width, theme.img_num," 
       							." theme.size,  theme.type, theme.note, theme.author, theme.md5,"
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro, theme.star_level, "       									
       							." theme.insert_user, theme.insert_time,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT *, DATE_FORMAT(insert_time, '%%Y-%%m-%%d')d"
								."		 FROM tb_yl_theme_info" 
								." 		 WHERE type = %d AND valid = 1 AND kernel = %d AND width = %d AND height = %d %s "
								." 		 ORDER BY d DESC, asort ASC LIMIT %d, %d )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid" 
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	
								." ORDER BY theme.id DESC, prev_url ASC");

defined("SQL_SELECT_THEME_ALBUMS")
	or define("SQL_SELECT_THEME_ALBUMS", "SELECT pay.*, dl.download_times, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url," 
       							." theme.height, theme.width, theme.img_num," 
       							." theme.size,  theme.type, theme.note, theme.author, theme.md5,"
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro, theme.star_level, "       									
       							." theme.insert_user, theme.insert_time,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT theme.* "
								."		 FROM tb_yl_albums a"
								."		 LEFT JOIN tb_yl_albums_res ares on ares.identity = a.identity " 
								."		 LEFT JOIN tb_yl_theme_info theme on theme.cpid = ares.resid " 
								." 		 WHERE ares.type = 0  AND a.valid = 1 AND theme.valid = 1 AND kernel = %d AND width = %d AND height = %d )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid" 
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	
								." ORDER BY theme.id DESC, prev_url ASC");
	
defined("SQL_SELECT_THEME_INFO_WITH_ID")
	or define("SQL_SELECT_THEME_INFO_WITH_ID", "SELECT pay.*, dl.download_times, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url,"
								." theme.height, theme.width, theme.img_num,"
								." theme.size,  theme.type, theme.note, theme.author, theme.md5,"
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro, theme.star_level, "
								." theme.insert_user, theme.insert_time,"
								." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT *, DATE_FORMAT(insert_time, '%%Y-%%m-%%d')d"
								."		 FROM tb_yl_theme_info"
								." 		 WHERE identity = '%s' )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid"
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"
								." ORDER BY prev_url ASC");


define("SQL_SELECT_THEME_FOR_WEB", " SELECT theme.ischarge, theme.identity, theme.cpid, theme.name, "
								." theme.height, theme.width, " 
								." theme.size, preview.url AS prev_url, "
								." dl.download_times "
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	
								." WHERE theme.type = %d AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 "
								." ORDER BY theme.id DESC LIMIT %d, %d ");

define("SQL_SELECT_THEME_HOT", " SELECT theme.ischarge, theme.identity, theme.cpid, theme.name, "
								." theme.height, theme.width, "
								." theme.size, preview.url AS prev_url, "
								." dl.download_times "
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	
								." WHERE theme.type = %d AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 "
								." ORDER BY dl.download_times DESC LIMIT %d, %d ");

define("SQL_SELECT_THEME_LAST", " SELECT theme.ischarge, theme.identity, theme.cpid, theme.name, "
								." theme.height, theme.width, "
								." theme.size, preview.url AS prev_url, "
								." dl.download_times "
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"
								." WHERE theme.type = %d AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 "
								." ORDER BY theme.insert_time DESC LIMIT %d, %d ");

define("SQL_SELECT_THEME_CHOICE", " SELECT theme.ischarge, theme.identity, theme.cpid, theme.name, "
								." theme.height, theme.width, "
								." theme.size, preview.url AS prev_url, "
								." dl.download_times "
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"
								." WHERE theme.type = %d AND theme.choice = 1 AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 "
								." ORDER BY theme.insert_time DESC LIMIT %d, %d ");

define("SQL_SELECT_THEME_HOLIDAY", " SELECT theme.ischarge, theme.identity, theme.cpid, theme.name, "
								." theme.height, theme.width, "
								." theme.size, preview.url AS prev_url, "
								." dl.download_times "
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"
								." WHERE theme.type = %d AND theme.holiday = 1 AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 "
								." ORDER BY theme.insert_time DESC LIMIT %d, %d ");

define("SQL_COUNT_THEME_HOLIDAY", " SELECT COUNT(*) "
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"
								." WHERE theme.type = %d AND theme.holiday = 1 AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 ");

define("SQL_COUNT_THEME_FOR_WEB", " SELECT COUNT(*)"
								." FROM tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." WHERE theme.type = %d AND theme.valid = 1 AND theme.kernel = %d AND theme.width = %d AND theme.height = %d AND preview.type = 1 ");


define("SQL_SELECT_THEME_INFO_BY_NEW_LIMIT", "SELECT * FROM tb_yl_theme_info"
								." ORDER BY insert_time DESC"  
								." LIMIT %d, %d ");

define("SQL_SELECT_THEME_INFO_BY_NEW_RATIO_LIMIT", "SELECT * FROM tb_yl_theme_info "
								." WHERE width = %d AND height = %d "
								." ORDER BY insert_time DESC  "
								." LIMIT %d, %d ");
define("SQL_CHECK_THEME_NAME", "SELECT COUNT(*) FROM tb_yl_theme_info "
							    ." WHERE name = %s");

define("SQL_INSERT_THEME_INFO","INSERT INTO tb_yl_theme_info" 
				." (identity, cpid, name, folder, url, size, note, insert_time, insert_user, md5, author,type, img_num, width, height) "
				." VALUES ('%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d)");

define("SQL_INSERT_THEME_RATIO_INFO","INSERT INTO tb_yl_theme_info (name, folder, url, size, note, insert_time, insert_user, theme_file_md5, author,type, img_num, width, height) VALUES ('%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d)");

defined("SQL_SELECT_THEMES_DL_URL")
	or define("SQL_SELECT_THEMES_DL_URL", "SELECT url FROM tb_yl_theme_info WHERE identity = '%s'");


define("SQL_DELETE_THEME_BY_ID", "DELETE * FROM tb_yl_theme_info WHERE id=%d");
define("SQL_DELETE_THEMES_ALL", "DELETE ALL FROM tb_yl_theme_info");

defined("SQL_CHECK_THEME_ISCHARGE")
	or define("SQL_CHECK_THEME_ISCHARGE","SELECT ischarge FROM tb_yl_theme_info WHERE identity = '%s' ");
	
defined("SQL_SELECT_THEME_LIST")	
	or define("SQL_SELECT_THEME_LIST","SELECT identity, cpid, name" 
								." FROM tb_yl_theme_info WHERE valid = 1 AND width = %d AND height = %d and kernel = %d "
								." ORDER BY id DESC LIMIT %d, %d  ");
	
defined("SQL_COUNT_THEME_LIST")
	or define("SQL_COUNT_THEME_LIST","SELECT COUNT(*)"
			." FROM tb_yl_theme_info WHERE valid = 1 AND width = %d AND height = %d and kernel = %d ");
	
defined("SQL_SELECT_THEMES_DETAILS_URL")	
	or define("SQL_SELECT_THEMES_DETAILS_URL", "SELECT pay.*, dl.download_times, " 
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, "
								." theme.height, theme.width, theme.img_num, "
								." theme.size,  theme.type, theme.note, theme.author, theme.md5, "
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro, theme.star_level, "
								." theme.insert_user, theme.insert_time, "
								." preview.url AS prev_url, preview.type AS prev_type"
								." FROM  tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid "
								." WHERE theme.identity = '%s'"
								." ORDER BY preview.url ASC " );

defined("SQL_SELECT_THEMES_DETAILS_URL_RATIO")
	or define("SQL_SELECT_THEMES_DETAILS_URL_RATIO",	 "SELECT pay.*, dl.download_times, " 
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, "
								." theme.height, theme.width, theme.img_num, "
								." theme.size,  theme.type, theme.note, theme.author, theme.md5, "
								." theme.effect,  theme.font_style, theme.keyguard_style,  theme.kernel, theme.intro, theme.star_level, "
								." theme.insert_user, theme.insert_time, "
								." preview.url AS prev_url, preview.type AS prev_type"
								." FROM  tb_yl_theme_info theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid "
								." WHERE theme.cpid = '%s' AND width= %d height %d "
								." ORDER BY preview.url ASC " );
	
	
defined("SQL_SELECT_THEMES_BANNER")	
	or define("SQL_SELECT_THEMES_BANNER",	"SELECT pay.*, dl.download_times, " 
								." theme.bannerid, theme.bannername, theme.bannerurl, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, " 
								." theme.height, theme.width, theme.img_num, " 
								." theme.size,  theme.type, theme.note, theme.author, theme.md5, " 
								." theme.effect,  theme.font_style, theme.keyguard_style,  theme.kernel, theme.intro, theme.star_level, " 
								." theme.insert_user, theme.insert_time, " 
								." preview.url AS prev_url, preview.type AS prev_type "
								." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, th.*" 
 								." 		FROM tb_yl_banner_list  bl " 
 								." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid "
 								."		LEFT JOIN tb_yl_theme_info th ON th.cpid = bl.cpid "
 								." 		WHERE bl.valid = 1 AND bl.cooltype = 0 AND b.valid = 1 "
      							." 		AND th.valid = 1 AND th.width = %d AND th.height = %d AND th.kernel = %d "
								."		)theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON preview.identity = theme.identity "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid "   	
								." ORDER BY theme.istop DESC, prev_url ASC ");
?>