<?php
defined("SQL_SELECT_RING_TYPE_NAME")
		or define("SQL_SELECT_RING_TYPE_NAME", "SELECT name FROM tb_yl_ring_type WHERE type=%d");

defined("SQL_INSERT_RING")
		or define("SQL_INSERT_RING", "INSERT INTO tb_yl_ring " 
									." (identity, type, name, note, fname, url, size, md5, insert_time, insert_user)"
									." VALUES ('%s', %d, '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s')");
defined("SQL_SELECT_RING_BY_LIMIT")
		or define("SQL_SELECT_RING_BY_LIMIT", "SELECT pay.*, dl.download_times, " 
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM tb_yl_ring ring" 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid"
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  		
									." WHERE ring.type = %d %s %s "
									." ORDER BY ring.id DESC, msort DESC, asort ASC "
									." LIMIT %d, %d");	
			
defined("SQL_SELECT_RING_FOR_WEB")
		or define("SQL_SELECT_RING_FOR_WEB", "SELECT pay.*, dl.download_times, " 
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM tb_yl_ring ring" 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid"
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  		
									." WHERE ring.type = %d %s "
									." ORDER BY asort ASC "
									." LIMIT %d, %d");	
				
defined("SQL_SELECT_RING_LAST")
		or define("SQL_SELECT_RING_LAST","SELECT pay.*, dl.download_times, " 
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM tb_yl_ring ring" 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid"
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  		
									." ORDER BY ring.id DESC "
									." WHERE ring.type = %d %s "
									." LIMIT %d, %d");
		
defined("SQL_SELECT_RING_HOST")
		or define("SQL_SELECT_RING_HOST", "SELECT pay.*, dl.download_times, " 
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM tb_yl_ring ring" 
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid"
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  		
									." WHERE ring.type = %d %s "
									." ORDER BY dl.download_times DESC "
									." LIMIT %d, %d");
				
defined("SQL_SELECT_RING_WITH_ID")
		or define("SQL_SELECT_RING_WITH_ID", "SELECT pay.*, dl.download_times, "
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size "
									." FROM tb_yl_ring ring"
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid"
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"
									." WHERE ring.identity = '%s' ");
		

defined("SQL_SELECT_RING_ALBUMS")
		or define("SQL_SELECT_RING_ALBUMS", "SELECT pay.*, dl.download_times, "
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size "
									." FROM tb_yl_albums a "
									." LEFT JOIN tb_yl_albums_res ares ON ares.identity = a.identity "
									." LEFT JOIN tb_yl_ring ring ON ring.identity = ares.resid "
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid"
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"
									." WHERE ares.type = 4 AND a.valid = 1" 
									." ORDER BY ring.id DESC, ring.asort ASC "); 				

defined("SQL_COUNT_RING")
		or define("SQL_COUNT_RING", "SELECT COUNT(*) FROM tb_yl_ring WHERE type = %d %s %s ");

defined("SQL_SELECT_RING_BY_ID")
		or define("SQL_SELECT_RING_BY_ID", "SELECT identity, type, fname, url FROM tb_yl_ring WHERE identity = '%s'");

defined("SQL_SELECT_RING_BANNER")
		or define("SQL_SELECT_RING_BANNER", "SELECT pay.*, dl.download_times, "
									." ring.bannerid, ring.bannername, ring.bannerurl, "  
									." ring.ischarge, ring.identity AS id, ring.type, ring.name, ring.fname, ring.url, ring.size " 
									." FROM (SELECT b.identity AS bannerid, b.name AS bannername, b.url AS bannerurl, b.istop, r.* " 		
									."		FROM tb_yl_banner_list  bl "  		
									." 		LEFT JOIN tb_yl_banner b ON b.identity = bl.bannerid " 			
									." 		LEFT JOIN tb_yl_ring r ON r.identity = bl.cpid "	  		
									."		WHERE bl.valid = 1 AND bl.cooltype = 4 AND b.valid = 1 )ring"  
									." LEFT JOIN tb_yl_pay pay ON pay.waresid = ring.waresid "  
									." LEFT JOIN tb_yl_ring_download dl ON dl.cpid = ring.identity"  
									." ORDER BY ring.istop DESC ");