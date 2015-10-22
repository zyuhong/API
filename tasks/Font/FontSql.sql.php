<?php
//插入字体
defined("SQL_INSERT_FONT")
	or define("SQL_INSERT_FONT", "INSERT INTO tb_yl_font (identity, language, name, fname, url, "
								." author, designer, version, uiversion,"
								." note, size, md5, insert_time, insert_user ) "
								." VALUES('%s', '%s', '%s', '%s', '%s', "
								." '%s', '%s', '%s', '%s', "		
								." '%s', %d, '%s', '%s', '%s')");
//插入预览图	
defined("SQL_INSERT_FONT_PREVIEW")
	or define("SQL_INSERT_FONT_PREVIEW", "INSERT INTO tb_yl_font_preview "
			." (identity, width, height, fname, preview_url, size, md5) "
			." VALUES('%s', %d, %d, '%s', '%s', %d, '%s')");
	
defined("SQL_SELECT_FONT_LIMIT")
	or define("SQL_SELECT_FONT_LIMIT", "SELECT pay.*, dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid" 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  												
								." WHERE 1=1 %s "
								." ORDER BY asort ASC"
								." LIMIT %d, %d");

defined("SQL_SELECT_FONT_FOR_WEB")
	or define("SQL_SELECT_FONT_FOR_WEB", "SELECT pay.*, dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid" 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  												
								." ORDER BY asort ASC"
								." LIMIT %d, %d");
	
defined("SQL_SELECT_FONT_LAST")
	or define("SQL_SELECT_FONT_LAST",  "SELECT pay.*, dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid" 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  												
								." ORDER BY font.id DESC"
								." LIMIT %d, %d");

defined("SQL_SELECT_FONT_HOT")
	or define("SQL_SELECT_FONT_HOT", "SELECT pay.*, dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid" 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  												
								." ORDER BY dl.download_times DESC"
								." LIMIT %d, %d");
	
defined("SQL_SELECT_FONT_WITH_ID")
	or define("SQL_SELECT_FONT_WITH_ID", "SELECT pay.*, dl.download_times, "
			." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, largepreurl, preview_url, width, height"
			." FROM tb_yl_font font"
			." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
			." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid"
			." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"
			." WHERE font.identity = '%s'");
	
defined("SQL_SELECT_ALL_FONT_LIMIT")
	or define("SQL_SELECT_ALL_FONT_LIMIT", "SELECT font.identity, font.language, font.fname, font.size, font.md5, name, url"
								." FROM tb_yl_font font"
								." LIMIT %d, %d");
	
defined("SQL_SELECT_FONT_PREVIEW_BY_ID")
	or define("SQL_SELECT_FONT_PREVIEW_BY_ID", "SELECT width, height, largepreurl, preview_url AS url"
								." FROM tb_yl_font_preview"
								." WHERE identity = '%s'");
//统计数量	
defined("SQL_COUNT_FONT_")
	or define("SQL_COUNT_FONT", "SELECT COUNT(*) FROM tb_yl_font font WHERE 1=1 %s ");
		
defined("SQL_SELECT_FONT_BY_ID")
	or define("SQL_SELECT_FONT_BY_ID", "SELECT identity, fname, size, md5, name, url"
								." FROM tb_yl_font"
								." WHERE identity='%s'");
	
//获取分辨率列表
defined("SQL_SELECT_FONT_RATIO")
	or define("SQL_SELECT_FONT_RATIO", "SELECT width, height FROM tb_yl_font_ratio");
	

/**
 * 获取字体banner区信息
 */
defined("SQL_SELECT_FONT_BANNER")
		or define("SQL_SELECT_FONT_BANNER", "SELECT pay.*, dl.download_times, "
									." font.bannerid, font.bannername, font.bannerurl,"
									." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, largepreurl, preview_url, width, height"
									." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, f.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_font f ON f.identity = bl.cpid "	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 5 AND b.valid = 1 )font"  
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid "  
									." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  
									." ORDER BY font.istop DESC ");	