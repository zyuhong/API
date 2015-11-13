<?php 
/**
 * 主题SQL语句仅对终端接口
 * 
 * @var unknown_type
 */

/**
 * 获取主题列表
 */
defined("SQL_SELECT_THEME_INFO")
	or define("SQL_SELECT_THEME_INFO", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." t.download_times, ad.ad, ad.adurl, ad.adicon, "
								." t.ischarge, t.identity, t.cpid, t.name, t.url, t.pre_url, t.pre_icon, t.pre_mms, t.pre_contact, " 
       							." t.height, t.width, t.img_num," 
       							." t.size,  t.type, t.note, t.author, t.cyid as userid, t.md5,"
								." t.effect,  t.font_style, t.keyguard_style, t.kernel, t.intro,  t.star_level, "       									
       							." t.insert_user, t.insert_time, t.tdate,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style, dl.mdl, dl.download_times "
								." 		FROM tb_yl_theme t "
								." 		INNER JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid 
										LEFT JOIN tb_yl_theme_download dl ON dl.cpid = t.cpid "
								." 		WHERE t.type = %d AND t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d %s "
								." 		%s LIMIT %d, %d )t"
								." LEFT JOIN tb_yl_theme_preview_info preview ON t.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = t.waresid AND pay.appid = t.appid " 
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = t.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = t.incruleid " 	
								." LEFT JOIN tb_yl_adver ad ON ad.adid = t.adid " 
								." %s, t.id DESC, prev_url ASC");

	
defined("SQL_COUNT_THEME_INFO")
 	or define("SQL_COUNT_THEME_INFO", "SELECT count(t.id)"
								." FROM tb_yl_theme t "
								." INNER JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." WHERE t.type = %d AND t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d %s ");
//获取推荐主题的CPID,按推荐分数取前三								
defined("SQL_SELECT_RECIOMMEND_CPID")
	or define("SQL_SELECT_RECIOMMEND_CPID", "SELECT recommend FROM tb_yl_recommend WHERE cpid = '%s' ORDER bY point DESC LIMIT 0, 3");

/**
 *根据CPID获取资源信息　
 */
defined("SQL_SELECT_THEME_INFO_WITH_CPID")
	or define("SQL_SELECT_THEME_INFO_WITH_CPID", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." t.download_times, "
								." t.ischarge, t.identity, t.cpid, t.name, t.url, ad.ad, ad.adurl, ad.adicon, " 
       							." t.height, t.width, t.img_num," 
       							." t.size,  t.type, t.note, t.author, t.cyid as userid,  t.md5, t.pre_url, t.pre_icon, t.pre_mms, t.pre_contact, "
								." t.effect,  t.font_style, t.keyguard_style, t.kernel, t.intro,  t.star_level, "       									
       							." t.insert_user, t.insert_time, t.tdate,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style, dl.download_times "
								." 		FROM tb_yl_theme t "
								." 		INNER JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid 
										LEFT JOIN tb_yl_theme_download dl ON dl.cpid = t.cpid "
								." 		WHERE t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d AND t.cpid = '%s' %s)t"
								." LEFT JOIN tb_yl_theme_preview_info preview ON t.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = t.waresid AND pay.appid = t.appid " 
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = t.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = t.incruleid " 
								." LEFT JOIN tb_yl_adver ad ON ad.adid = t.adid " 
								." ORDER BY prev_url ASC");	
	
//随机获取主题，主题推荐测试用								
defined("SQL_SELECT_RAND_THEME_INFO")
	or define("SQL_SELECT_RAND_THEME_INFO", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." dl.download_times, ad.ad, ad.adurl, ad.adicon, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, theme.pre_url, theme.pre_icon, theme.pre_mms, theme.pre_contact, " 
       							." theme.height, theme.width, theme.img_num," 
       							." theme.size,  theme.type, theme.note, theme.author, theme.cyid as userid, theme.md5,"
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro,  theme.star_level, "       									
       							." theme.insert_user, theme.insert_time, theme.tdate,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style "
								." 		 FROM tb_yl_theme t "
								." 		 INNER JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." 		 WHERE t.type = %d AND t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d %s "
								." 		 ORDER BY rand()  LIMIT 3 )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid AND pay.appid = theme.appid " 
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = theme.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = theme.incruleid " 	
								." LEFT JOIN tb_yl_adver ad ON ad.adid = theme.adid " 
								." ORDER BY theme.asort DESC, prev_url ASC");
/**
 * 获取专辑主题信息
 */ 
defined("SQL_SELECT_THEME_ALBUMS")
	or define("SQL_SELECT_THEME_ALBUMS", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." dl.download_times, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, ad.ad, ad.adurl, ad.adicon," 
       							." theme.height, theme.width, theme.img_num," 
       							." theme.size,  theme.type, theme.note, theme.author, theme.cyid as userid, theme.md5, theme.pre_url, theme.pre_icon, theme.pre_mms, theme.pre_contact,  "
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro,  theme.star_level, "       									
       							." theme.insert_user, theme.insert_time, theme.tdate,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT t.*,  tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style "
								."		 FROM tb_yl_albums_res ares " 
								."		 LEFT JOIN tb_yl_theme t on t.cpid = ares.cpid " 
								."		 LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid  " 
								." 		 WHERE ares.cooltype = 0  AND ares.valid = 1 AND t.valid = 1 AND tinfo.valid = 1 AND ares.albumid = '%s' AND tinfo.width = %d AND tinfo.height = %d %s "
								."       LIMIT %d, %d )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid AND pay.appid = theme.appid " 
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid"  	 	
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = theme.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = theme.incruleid " 	
								." LEFT JOIN tb_yl_adver ad ON ad.adid = theme.adid " 
								." ORDER BY theme.asort DESC, prev_url ASC");
/**
 * 根据ID获取主题资源
 */	
defined("SQL_SELECT_THEME_INFO_WITH_ID")
	or define("SQL_SELECT_THEME_INFO_WITH_ID", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." dl.download_times, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, ad.ad, ad.adurl, ad.adicon, "
								." theme.height, theme.width, theme.img_num,"
								." theme.size,  theme.type, theme.note, theme.author, theme.cyid as userid, theme.md5, theme.pre_url, theme.pre_icon, theme.pre_mms, theme.pre_contact,  "
								." theme.effect,  theme.font_style, theme.keyguard_style, theme.kernel, theme.intro, theme.star_level, "
								." theme.insert_user, theme.insert_time, theme.tdate,"
								." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style "
								."		 FROM tb_yl_theme t " 
								."		 LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid  "
								." 		 WHERE tinfo.identity = '%s' )theme"
								." LEFT JOIN tb_yl_theme_preview_info preview ON theme.identity = preview.identity"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid AND pay.appid = theme.appid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid" 	
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = theme.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = theme.incruleid " 	
								." LEFT JOIN tb_yl_adver ad ON ad.adid = theme.adid " 
								." ORDER BY prev_url ASC");	
/**
 * 获取主题bnner区信息
 */								
defined("SQL_SELECT_THEMES_BANNER")	
	or define("SQL_SELECT_THEMES_BANNER",	"SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." dl.download_times, " 
								." theme.bannerid, theme.bannername, theme.bannerurl, "
								." theme.ischarge, theme.identity, theme.cpid, theme.name, theme.url, ad.ad, ad.adurl, ad.adicon,  " 
								." theme.height, theme.width, theme.img_num, " 
								." theme.size,  theme.type, theme.note, theme.author, theme.cyid as userid, theme.md5, theme.pre_url, theme.pre_icon, theme.pre_mms, theme.pre_contact,  " 
								." theme.effect,  theme.font_style, theme.keyguard_style,  theme.kernel, theme.intro, " 
								." theme.insert_user, theme.insert_time, theme.tdate, " 
								." preview.url AS prev_url, preview.type AS prev_type "
								." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, "
								."              t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style " 
 								." 		FROM tb_yl_banner_list  bl " 
 								." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid "
 								."		LEFT JOIN tb_yl_theme t ON t.cpid = bl.cpid" 
								."		LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid  "
 								." 		WHERE bl.valid = 1 AND bl.cooltype = 0 AND b.valid = 1 "
      							." 		AND t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d %s "
								."		)theme "
								." LEFT JOIN tb_yl_theme_preview_info preview ON preview.identity = theme.identity "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = theme.waresid AND pay.appid = theme.appid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = theme.cpid "   	
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = theme.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = theme.incruleid "  	
								." LEFT JOIN tb_yl_adver ad ON ad.adid = theme.adid " 
								." ORDER BY theme.istop DESC, prev_url ASC ");	

/**
 * 主题Widget #废弃
 */
defined("SQL_SELECT_THEME_WIDGET")
	or define("SQL_SELECT_THEME_WIDGET", " SELECT dl.download_times, widget.identity AS widgetid, widget.cpid, widget.url AS turl, "
       									." widget.thid AS identity, widget.name, widget.kernel, widget.img_num, widget.type, "
       									." widget.size, widget.effect, widget.font_style, widget.keyguard_style, "
									    ." preview.type AS prev_type, preview.url AS prev_url "
										." FROM (SELECT widget.identity, widget.cpid, widget.url, "
										."      t.name, t.type, tinfo.identity AS thid, theme.kernel, theme.img_num,  "
										." 		tinfo.size, tinfo.effect, tinfo.font_style, tinfo.keyguard_style "
										." 		FROM tb_yl_theme_widget widget "
										."		LEFT JOIN tb_yl_theme t ON t.cpid = widget.cpid" 
										."		LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid AND widget.width = tinfo.width AND widget.height = tinfo.height "
										." 		WHERE widget.valid = 1 AND t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d %s " 
										."		ORDER BY RAND() LIMIT 1)widget "
										." LEFT JOIN tb_yl_theme_download dl ON  dl.cpid = widget.cpid " 
										." INNER JOIN tb_yl_theme_preview_info preview ON preview.identity = widget.thid"
										." ORDER BY preview.url ASC");
	
/**
 * 获取主题的下载URL
 */	
defined("SQL_SELECT_THEMES_DL_URL")
	or define("SQL_SELECT_THEMES_DL_URL", "SELECT t.ischarge, tinfo.url "
										  ." FROM tb_yl_theme_info tinfo "
										  ." LEFT JOIN tb_yl_theme t ON t.cpid = tinfo.cpid "
										  ." WHERE tinfo.identity = '%s'");

/**
 * 检测是否付费资源
 */	
defined("SQL_CHECK_THEME_ISCHARGE")
	or define("SQL_CHECK_THEME_ISCHARGE","SELECT t.ischarge "
										 ." FROM tb_yl_theme_info tinfo "
										 ." LEFT JOIN tb_yl_theme t ON t.cpid = tinfo.cpid "
										 ." WHERE tinfo.identity = '%s' ");
/**
 * 查询下载md5
 */
defined("SQL_SELECT_THEMES_DL_MD5")
    or define("SQL_SELECT_THEMES_DL_MD5", "SELECT dl_md5 FROM tb_yl_theme_info WHERE identity = '%s' and valid=1 ");
	
/**
 * 网站访问资源
 * @var unknown_type
 */	
defined("SQL_SELECT_THEME_WEB_INFO")
	or define("SQL_SELECT_THEME_WEB_INFO", " SELECT t.ischarge, t.cpid, t.name, "
								." tinfo.identity, tinfo.height, tinfo.width, " 
								." tinfo.size, preview.url AS prev_url, "
								." dl.download_times "
								." FROM tb_yl_theme t "
								." LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." LEFT JOIN tb_yl_theme_preview_info preview ON tinfo.identity = preview.identity "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = t.cpid"  	
								." WHERE t.type = %d AND t.valid = 1 AND tinfo.valid = 1 %s AND tinfo.width = %d AND tinfo.height = %d AND preview.type = 1 "
								." %s LIMIT %d, %d ");

defined("SQL_COUNT_THEME_WEB_INFO")
	or define("SQL_COUNT_THEME_WEB_INFO", " SELECT COUNT(*)"
								." FROM tb_yl_theme t "
								." LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." LEFT JOIN tb_yl_theme_preview_info preview ON tinfo.identity = preview.identity "
								." WHERE t.type = %d AND t.valid = 1 AND tinfo.valid = 1 %s AND tinfo.width = %d AND tinfo.height = %d AND preview.type = 1 ");
/**
 * 获取主题详情信息
 */
defined("SQL_SELECT_THEMES_DETAILS")	
	or define("SQL_SELECT_THEMES_DETAILS", "SELECT pay.*, "
								." rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." dl.download_times, ad.ad, ad.adurl, ad.adicon, "
								." t.ischarge, t.cpid, t.name, t.type, t.intro, t.star_level, t.note, t.author, t.cyid as userid, "
								." tinfo.identity, tinfo.url, tinfo.height, tinfo.width, tinfo.img_num, "
								." tinfo.size,  tinfo.md5, "
								." tinfo.effect,  tinfo.font_style, tinfo.keyguard_style, tinfo.kernel, "
								." t.insert_user, t.insert_time, t.tdate, "
								." preview.url AS prev_url, preview.type AS prev_type"
								." FROM  tb_yl_theme t "
								." LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." LEFT JOIN tb_yl_theme_preview_info preview ON tinfo.identity = preview.identity "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = t.waresid AND pay.appid = t.appid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = t.cpid " 	
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = t.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = t.incruleid " 	
								." LEFT JOIN tb_yl_adver ad ON ad.adid = t.adid " 
								." WHERE tinfo.identity = '%s'"
								." ORDER BY preview.url ASC " );
/**
 * 获取主题详情信息
 * 根据分辨率过来资源
 */								
defined("SQL_SELECT_THEMES_DETAILS_RATIO")
	or define("SQL_SELECT_THEMES_DETAILS_RATIO",	 "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." dl.download_times, ad.ad, ad.adurl, ad.adicon, " 
								." t.ischarge, t.cpid, t.name, t.type, t.intro, t.star_level, t.note, t.author, t.cyid as userid,  t.pre_url, t.pre_icon, t.pre_mms, t.pre_contact, "
								." tinfo.identity, tinfo.url, tinfo.height, tinfo.width, tinfo.img_num, "
								." tinfo.size,  tinfo.md5, "
								." tinfo.effect,  tinfo.font_style, tinfo.keyguard_style, tinfo.kernel, "
								." t.insert_user, t.insert_time, t.tdate, "
								." preview.url AS prev_url, preview.type AS prev_type"
								." FROM  tb_yl_theme t "
								." LEFT JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." LEFT JOIN tb_yl_theme_preview_info preview ON tinfo.identity = preview.identity "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = t.waresid AND pay.appid = t.appid "
								." LEFT JOIN tb_yl_theme_download dl ON dl.cpid = t.cpid "
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = t.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = t.incruleid " 
								." LEFT JOIN tb_yl_adver ad ON ad.adid = t.adid " 
								." WHERE t.cpid = '%s' AND tinfo.width= %d AND tinfo.height = %d %s "
								." ORDER BY preview.url ASC " );

/**
 *获取设计师主题 20150504
 */
defined("SQL_SELECT_DESIGNER_THEME_INFO")
	or define("SQL_SELECT_DESIGNER_THEME_INFO", "SELECT pay.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, "
								." t.download_times, "
								." t.ischarge, t.identity, t.cpid, t.name, t.url, ad.ad, ad.adurl, ad.adicon, " 
       							." t.height, t.width, t.img_num," 
       							." t.size,  t.type, t.note, t.author, t.cyid as userid,  t.md5, t.pre_url, t.pre_icon, t.pre_mms, t.pre_contact, "
								." t.effect,  t.font_style, t.keyguard_style, t.kernel, t.intro,  t.star_level, "       									
       							." t.insert_user, t.insert_time, t.tdate,"
       							." preview.url AS prev_url, preview.type AS prev_type"
								." FROM (SELECT t.*, tinfo.identity, tinfo.kernel, tinfo.height, tinfo.width," 
								." 				tinfo.url, tinfo.size, tinfo.md5, tinfo.img_num, " 
								." 				tinfo.effect, tinfo.font_style, tinfo.keyguard_style, dl.download_times "
								." 		FROM tb_yl_theme t "
								." 		INNER JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid 
										LEFT JOIN tb_yl_theme_download dl ON dl.cpid = t.cpid "
								." 		WHERE t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d AND t.cyid = '%s' "								
								." 		LIMIT %d, %d )t"
								." LEFT JOIN tb_yl_theme_preview_info preview ON t.identity = preview.identity" 
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = t.waresid AND pay.appid = t.appid " 
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = t.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = t.incruleid " 
								." LEFT JOIN tb_yl_adver ad ON ad.adid = t.adid " 
								." ORDER BY prev_url ASC");	
								
defined("SQL_COUNT_DESIGNER_THEME_INFO")
 	or define("SQL_COUNT_DESIGNER_THEME_INFO", "SELECT count(t.id)"
								." FROM tb_yl_theme t "
								." INNER JOIN tb_yl_theme_info tinfo ON t.cpid = tinfo.cpid "
								." WHERE t.valid = 1 AND tinfo.valid = 1 AND tinfo.width = %d AND tinfo.height = %d AND cyid = '%s' ");
