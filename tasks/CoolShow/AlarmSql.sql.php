<?php 
/**
 * 闹钟铃声资源SQL
 * 
 */

/**
 * 获取闹钟铃声分类列表/专辑列表
 */
defined("SQL_SELECT_ALARM_LABEL")
		or define("SQL_SELECT_ALARM_LABEL", "SELECT al.*, sum(dl.download_times) download_times, sum(alarm.size) size, COUNT(1) num 
											FROM tb_yl_alarm_label al
											LEFT JOIN tb_yl_alarm alarm ON alarm.subtype = al.code
											LEFT JOIN tb_yl_alarm_download dl ON dl.cpid = alarm.identity
											GROUP BY al.code
											ORDER BY al.sort ASC	
											LIMIT %d, %d
											");
/**
 * 获取铃声列表
 */
defined("SQL_SELECT_ALARM")
		or define("SQL_SELECT_ALARM", "SELECT pay.*, dl.download_times, " 
									." al.imgurl, "
									." alarm.ischarge, alarm.identity, alarm.subtype as type, alarm.name, alarm.fname, alarm.url, alarm.size " 
									." FROM tb_yl_alarm alarm " 
									." LEFT JOIN tb_yl_alarm_label al ON alarm.subtype = al.code " 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = alarm.waresid AND pay.appid = alarm.appid "
									." LEFT JOIN tb_yl_alarm_download dl ON dl.cpid = alarm.identity "  		
									." WHERE alarm.valid = 1 AND alarm.subtype = %d %s "
									." ORDER BY alarm.id DESC "
									." LIMIT %d, %d");	

defined("SQL_COUNT_ALARM")
		or define("SQL_COUNT_ALARM", "SELECT COUNT(*) FROM tb_yl_alarm WHERE subtype = %d %s ");	

/**
 * 获取专辑铃声资源
 */		
defined("SQL_SELECT_ALARM_ALBUMS")
		or define("SQL_SELECT_ALARM_ALBUMS", "SELECT pay.*, dl.download_times, "
									." al.imgurl, alarm.ischarge, alarm.identity, alarm.type, alarm.name, alarm.fname, alarm.url, alarm.size "
									." FROM tb_yl_albums_res ares  "
									." LEFT JOIN tb_yl_alarm alarm ON alarm.identity = ares.cpid "
									." LEFT JOIN tb_yl_alarm_label al ON alarm.subtype = al.code " 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = alarm.waresid AND pay.appid = alarm.appid "
									." LEFT JOIN tb_yl_alarm_download dl ON dl.cpid = alarm.identity"
									." WHERE ares.cooltype = 14 AND ares.valid = 1 AND alarm.valid = 1 AND ares.albumid = '%s' "
									." ORDER BY alarm.id DESC, alarm.asort ASC "
									." LIMIT %d, %d "); 				
/**
 * 根据ID获取铃声资源
 */						
defined("SQL_SELECT_ALARM_WITH_ID")
		or define("SQL_SELECT_ALARM_WITH_ID", "SELECT pay.*, dl.download_times, "
									." alarm.ischarge, alarm.identity, alarm.type, alarm.name, alarm.fname, alarm.url, alarm.size "
									." FROM tb_yl_alarm alarm"
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = alarm.waresid AND pay.appid = alarm.appid "
									." LEFT JOIN tb_yl_alarm_download dl ON dl.cpid = alarm.identity"
									." WHERE alarm.identity = '%s' ");
/**
 * 根据ID获取铃声资源
 */
defined("SQL_SELECT_ALARM_BY_ID")
		or define("SQL_SELECT_ALARM_BY_ID", "SELECT identity, type, fname, url FROM tb_yl_alarm WHERE identity = '%s'");
		
/**
 * 获取铃声banner区信息
 */
defined("SQL_SELECT_ALARM_BANNER")
		or define("SQL_SELECT_ALARM_BANNER", "SELECT pay.*, dl.download_times, "
									." alarm.imgurl, alarm.bannerid, alarm.bannername, alarm.bannerurl, "  
									." alarm.ischarge, alarm.identity, alarm.type, alarm.name, alarm.fname, alarm.url, alarm.size " 
									." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, al.imgurl, alarm.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_alarm alarm ON alarm.identity = bl.cpid "
									." 		LEFT JOIN tb_yl_alarm_label al ON alarm.type = al.code " 	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 14 AND b.valid = 1 )alarm"  
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = alarm.waresid AND pay.appid = alarm.appid "  
									." LEFT JOIN tb_yl_alarm_download dl ON dl.cpid = alarm.identity"  
									." ORDER BY alarm.istop DESC ");	
		
/**
 * 获取铃声网站资源
 */
defined("SQL_SELECT_ALARM_WEB")
		or define("SQL_SELECT_ALARM_WEB", "SELECT pay.*, dl.download_times, "
									." alarm.ischarge, alarm.identity AS id, alarm.type, alarm.name, alarm.fname, alarm.url, alarm.size "
									." FROM tb_yl_alarm alarm"
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = alarm.waresid AND pay.appid = alarm.appid "
									." LEFT JOIN tb_yl_alarm_download dl ON dl.cpid = alarm.identity"
									." WHERE alarm.type = %d %s "
									." %s "
									." LIMIT %d, %d");