<?php
defined("SQL_INSERT_WIDGET_THEME")
	or define("SQL_INSERT_WIDGET_THEME","INSERT INTO tb_yl_theme_widget "
								." (identity, cpid, fname, width, height, url, "
								." note, size, md5, insert_time, insert_user ) "
								." VALUES('%s', '%s', '%s', %d, %d, '%s', "
								." '%s', %d, '%s', '%s', '%s')");


defined("SQL_SELECT_WIDGET_THEME")
	or define("SQL_SELECT_WIDGET_THEME", " SELECT pay.*, dl.download_times, "
										." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url," 
		       							." theme.height, theme.width, theme.img_num," 
		       							." theme.size,  theme.type, theme.note, theme.author, theme.md5,"
										." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro,  theme.star_level, "       									
		       							." theme.insert_user, theme.insert_time,"
		       							." preview.url AS prev_url, preview.type AS prev_type "
										." FROM (SELECT t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
										." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
										." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style "
										." 		FROM tb_yl_theme_widget widget "
										." 		INNER JOIN tb_yl_theme t ON t.cpid = widget.cpid"
										." 		INNER JOIN tb_yl_theme_info tinfo ON widget.cpid = tinfo.cpid AND widget.width = tinfo.width AND widget.height = tinfo.height " 
										." 		WHERE widget.valid = 1 AND tinfo.kernel = %d AND widget.width = %d AND widget.height = %d %s " 
										."		ORDER BY RAND() LIMIT 1)theme "
										." LEFT JOIN tb_yl_theme_download dl ON  dl.cpid = theme.cpid " 
										." INNER JOIN tb_yl_theme_preview_info preview ON preview.identity = theme.identity"
										." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid AND pay.appid = theme.appid " 
										." ORDER BY preview.url ASC");

defined("SQL_SELECT_WIDGE_SIZE_TAG")
	or define("SQL_SELECT_WIDGE_SIZE_TAG", "SELECT size_res, size_mid, size_small "
										." FROM transfer WHERE size_req = '%s'");
	
defined("SQL_SELECT_WIDGET_TAG_WALLPAPER")
	or define("SQL_SELECT_WIDGET_TAG_WALLPAPER", " SELECT ad.ad_id AS name, ad.adid AS cpid, ad.thumb_url AS turl,"
										."	ad_list.id_wallpaper AS id, ad_list.%s AS url, ad_list.%s AS mid_url, ad_list.%s AS small_url,"
										."  ad_list.ad_rank AS download_times, ad_list.cp_rank "
										."	FROM (SELECT * "
										."  	  FROM ad_info_list "
										."		  WHERE TYPE = 'tag' "
										."		 ORDER BY  RAND() LIMIT 1)ad "
										."	LEFT JOIN ad_list ON ad.adid = ad_list.id_adid "
										."	LIMIT 0, 50");
	
	
defined("SQL_SELECT_WIDGET_WALLPAPER")
	or define("SQL_SELECT_WIDGET_WALLPAPER", "SELECT id_wallpaper AS id, id_wallpaper AS cpid, %s AS url,  %s AS mid_url, %s AS small_url, "
										." ad_rank AS download_times, cp_rank"
										." FROM commend LIMIT %d, %d ");
	
	