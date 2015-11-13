<?php
/**
 * 获取字体列表
 */
 
defined("SQL_SELECT_FONT")
	or define("SQL_SELECT_FONT", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
										dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, purl, lurl, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid AND pay.appid = font.appid " 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = font.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = font.incruleid " 													
								." WHERE 1=1 %s "
								." %s "
								." LIMIT %d, %d");
#获取字体专辑	
defined("SQL_SELECT_FONT_ALBUM")
	or define("SQL_SELECT_FONT_ALBUM", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
										dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, purl, lurl, largepreurl, preview_url, width, height"
								." FROM tb_yl_albums_res ares"
								." LEFT JOIN  tb_yl_font font ON font.identity = ares.cpid "
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid AND pay.appid = font.appid "
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = font.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = font.incruleid " 	
								." WHERE 1=1 AND font.valid = 1 AND ares.valid = 1 AND ares.cooltype = 5 AND ares.albumid = '%s' "
								." ORDER BY asort ASC"
								." LIMIT %d, %d ");
	

defined("SQL_COUNT_FONT_")
	or define("SQL_COUNT_FONT", "SELECT COUNT(*) FROM tb_yl_font font WHERE 1=1 %s ");
	
/**
 * 根据ID获取的下载URL及是否付费等个别信息
 */
defined("SQL_SELECT_FONT_DL_URL")
	or define("SQL_SELECT_FONT_DL_URL", "SELECT ischarge,  url FROM tb_yl_font font WHERE identity='%s'");

/**
 * 查询下载md5
 */
defined("SQL_SELECT_FONT_DL_MD5")
    or define("SQL_SELECT_FONT_DL_MD5", "SELECT dl_md5 FROM tb_yl_font  WHERE identity = '%s' and valid=1");

/**
 * 根据ID获取字体资源
 */	
defined("SQL_SELECT_FONT_BY_ID")
	or define("SQL_SELECT_FONT_BY_ID", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
										dl.download_times, "
								." font.ischarge, font.identity, font.author, font.cyid as userid, "
								." font.fname, font.size, font.md5, name, url, purl, lurl, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid AND pay.appid = font.appid " 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  	
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = font.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = font.incruleid " 												
								." WHERE font.identity='%s'");
	
/**
 * 获取字体banner区信息
 */
defined("SQL_SELECT_FONT_BANNER")
		or define("SQL_SELECT_FONT_BANNER", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
										dl.download_times, "
									." font.bannerid, font.bannername, font.bannerurl,"
									." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, purl, lurl, largepreurl, preview_url, width, height"
									." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, f.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_font f ON f.identity = bl.cpid "	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 5 AND b.valid = 1 %s )font"  
									." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid AND pay.appid = font.appid "  
									." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  
									." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = font.ruleid "
									." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = font.incruleid " 	
									." ORDER BY font.istop DESC ");	
		
/**
 * 获取网站字体资源
 */		
defined("SQL_SELECT_FONT_WEB")
	or define("SQL_SELECT_FONT_WEB", "SELECT pay.*, dl.download_times, "
								." font.ischarge, font.identity, font.fname, font.size, font.md5, name, url, purl, lurl, largepreurl, preview_url, width, height"
								." FROM tb_yl_font font"
								." LEFT JOIN tb_yl_font_preview preview ON font.identity= preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = font.waresid AND pay.appid = font.appid " 
								." LEFT JOIN tb_yl_font_download dl ON dl.cpid = font.identity"  	
								." WHERE 1=1 %s "											
								." %s "
								." LIMIT %d, %d");