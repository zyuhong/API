<?php 
defined("SQL_SELECT_SCENE")
	or define("SQL_SELECT_SCENE", "SELECT pay.*, scene.*, scene.ischarge, kernel.totalSize "
								  ." FROM tb_yl_scene scene"
								  ." LEFT JOIN tb_yl_scene_kernel kernel ON scene.sceneCode = kernel.sceneCode "
								  ." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid " 	
								  ." WHERE kernel.kernelCode = %d %s "
								  ." ORDER BY scene.sceneCode DESC  "
								  ." LIMIT %d, %d");

defined("SQL_COUNT_SCENE")
	or define("SQL_COUNT_SCENE", "SELECT COUNT(scene.id) "
								  ." FROM tb_yl_scene scene"
								  ." LEFT JOIN tb_yl_scene_kernel kernel ON scene.sceneCode = kernel.sceneCode "
								  ." WHERE kernel.kernelCode = %d %s ");
	
defined("SQL_SELECT_SCENE_APK")
	or define("SQL_SELECT_SCENE_APK", "SELECT pay.*, scene.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
									dl.download_times, tinfo.url as turl "
								." FROM tb_yl_scene scene"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid "
								." LEFT JOIN tb_yl_scene_download dl ON dl.cpid = scene.sceneCode"
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = scene.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = scene.incruleid " 	
								." LEFT JOIN (SELECT * FROM tb_yl_theme_info WHERE kernel >= 3 AND valid = 1 AND width = %d AND height=%d) "
								."      tinfo ON tinfo.cpid = scene.sceneCode"
								." WHERE scene.valid = 1 %s "
								." %s  "
								." LIMIT %d, %d");

#获取锁屏专辑20141107	
defined("SQL_SELECT_SCENE_ALBUM")
	or define("SQL_SELECT_SCENE_ALBUM", "SELECT pay.*, scene.*,  rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
										dl.download_times, tinfo.url AS turl "
									." FROM (SELECT scene.* "
									."		FROM tb_yl_albums_res ares " 
									."		LEFT JOIN tb_yl_scene scene ON scene.sceneCode = ares.cpid " 
									."		WHERE ares.cooltype = 6  AND ares.valid = 1 AND scene.valid = 1 AND ares.albumid = '%s' )scene "
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid " 
									." LEFT JOIN tb_yl_scene_download dl ON dl.cpid = scene.sceneCode "
									." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = scene.ruleid "
									." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = scene.incruleid " 	
									." LEFT JOIN (SELECT * FROM tb_yl_theme_info 
													WHERE kernel = 3 AND valid = 1 AND width = %d AND height=%d) tinfo ON tinfo.cpid = scene.sceneCode "
									." WHERE scene.valid = 1 %s " 
									." ORDER BY scene.id DESC "
									." LIMIT %d, %d ");	
	
defined("SQL_COUNT_SCENE_APK")
	or define("SQL_COUNT_SCENE_APK", "SELECT COUNT(id)"
								." FROM tb_yl_scene scene"
								." WHERE scene.valid = 1 %s ");
		
defined("SQL_SELECT_SCENE_WITH_ID")
	or define("SQL_SELECT_SCENE_WITH_ID", "SELECT pay.*, scene.*, scene.cyid AS userid, dl.download_times"
								." FROM tb_yl_scene scene"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid "
								." LEFT JOIN tb_yl_scene_download dl ON dl.cpid = scene.sceneCode"
								." WHERE scene.sceneCode= '%s' ");	
/**
 * 根据ID获取资源基本信息
 */
defined("SQL_SELECT_SCENE_BY_ID")
	or define("SQL_SELECT_SCENE_BY_ID", "SELECT ischarge, sceneCode, fname, totalSize, md5, zhName, enName, url"
			." FROM tb_yl_scene"
			." WHERE sceneCode = '%s'");


/**
 * 查询下载md5
 */
defined("SQL_SELECT_SCENE_DL_MD5")
    or define("SQL_SELECT_SCENE_DL_MD5", "SELECT dl_md5 FROM tb_yl_scene "
                                             ." WHERE sceneCode = '%s' and valid=1 and kernel=%d ");

defined("SQL_SELECT_SCENE_THEME_DL_MD5")
    or define("SQL_SELECT_SCENE_THEME_DL_MD5", " SELECT theme.dl_md5 FROM `tb_yl_scene` scene "
                                            ." LEFT JOIN `tb_yl_theme_info` theme ON scene.`sceneCode` = theme.`cpid` "
                                            ." WHERE sceneCode=%d AND scene.kernel=%d AND theme.`width` = %d AND theme.`height` = %d "
                                            ." AND theme.`valid`=1 AND scene.`valid`=1 AND theme.kernel >=3;");
/**
 * 获取锁屏网站资源
 */	
defined("SQL_SELECT_SCENE_WEB")
	or define("SQL_SELECT_SCENE_WEB", "SELECT pay.*, scene.*, scene.ischarge, kernel.totalSize "
								  ." FROM tb_yl_scene scene"
								  ." LEFT JOIN tb_yl_scene_kernel kernel ON scene.sceneCode = kernel.sceneCode "
								  ." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid " 	
								  ." WHERE kernel.kernelCode = %d %s "
								  ." %s  "
								  ." LIMIT %d, %d");
								  
defined("SQL_SELECT_SCENE_APK_WEB")
	or define("SQL_SELECT_SCENE_APK_WEB", "SELECT pay.*, scene.*, dl.download_times"
								." FROM tb_yl_scene scene"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid "
								." LEFT JOIN tb_yl_scene_download dl ON dl.cpid = scene.sceneCode"
								." WHERE scene.valid = 1 "
								." %s  "
								." LIMIT %d, %d");
/**
 * 获取锁屏详情信息
 */	
defined("SQL_SELECT_SCENE_DETAILS")
	or define("SQL_SELECT_SCENE_DETAILS", "SELECT pay.*, scene.*, rule.ruleid as ruleid, rule.score as score, incrule.ruleid as incruleid, incrule.score as incscore, 
										dl.download_times"
								." FROM tb_yl_scene scene"
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid AND pay.appid = scene.appid "
								." LEFT JOIN tb_yl_scene_download dl ON dl.cpid = scene.sceneCode"
								." LEFT JOIN tb_yl_score_rule rule ON rule.ruleid = scene.ruleid "
								." LEFT JOIN tb_yl_score_incrule incrule ON incrule.ruleid = scene.incruleid " 	
								." WHERE scene.sceneCode = %s ");
	