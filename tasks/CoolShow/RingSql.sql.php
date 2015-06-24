<?php 
/**
 * 获取铃声列表
 */
defined("SQL_SELECT_RING")
		or define("SQL_SELECT_RING", "SELECT pay.*, dl.download_times, " 
									." ring.ischarge, ring.identity, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM tb_yl_ring ring" 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid AND pay.appid = ring.appid "
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  		
									." WHERE ring.type = %d %s "
									." ORDER BY ring.id DESC, msort DESC, asort ASC "
									." LIMIT %d, %d");	

defined("SQL_COUNT_RING")
		or define("SQL_COUNT_RING", "SELECT COUNT(*) FROM tb_yl_ring WHERE type = %d %s ");	

/**
 * 获取专辑铃声资源
 */		
defined("SQL_SELECT_RING_ALBUMS")
		or define("SQL_SELECT_RING_ALBUMS", "SELECT pay.*, dl.download_times, "
									." ring.ischarge, ring.identity, ring.type, ring.name, ring.fname, ring.url, ring.size "
									." FROM tb_yl_albums_res ares  "
									." LEFT JOIN tb_yl_ring ring ON ring.identity = ares.cpid "
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid AND pay.appid = ring.appid "
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"
									." WHERE ares.cooltype = 4 AND ares.valid = 1 AND ring.valid = 1 AND ares.albumid = '%s' "
									." ORDER BY ring.id DESC, ring.asort ASC "
									." LIMIT %d, %d "); 				
/**
 * 根据ID获取铃声资源
 */						
defined("SQL_SELECT_RING_WITH_ID")
		or define("SQL_SELECT_RING_WITH_ID", "SELECT pay.*, dl.download_times, "
									." ring.ischarge, ring.identity, ring.type, ring.name, ring.fname, ring.url, ring.size "
									." FROM tb_yl_ring ring"
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid AND pay.appid = ring.appid "
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"
									." WHERE ring.identity = '%s' ");
/**
 * 根据ID获取铃声资源
 */
defined("SQL_SELECT_RING_BY_ID")
		or define("SQL_SELECT_RING_BY_ID", "SELECT identity, type, fname, url FROM tb_yl_ring WHERE identity = '%s'");
		
/**
 * 获取铃声banner区信息
 */
defined("SQL_SELECT_RING_BANNER")
		or define("SQL_SELECT_RING_BANNER", "SELECT pay.*, dl.download_times, "
									." ring.bannerid, ring.bannername, ring.bannerurl, "  
									." ring.ischarge, ring.identity, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, r.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_ring r ON r.identity = bl.cpid "	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 4 AND b.valid = 1 %s )ring"  
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid AND pay.appid = ring.appid "  
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  
									." ORDER BY ring.istop DESC ");		
/**
 * 获取铃声网站资源
 */			
defined("SQL_SELECT_RING_WEB")
		or define("SQL_SELECT_RING_WEB", "SELECT pay.*, dl.download_times, " 
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM tb_yl_ring ring" 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid AND pay.appid = ring.appid "
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  		
									." WHERE ring.type = %d %s "
									." %s "
									." LIMIT %d, %d");	