<?php

//请求记录
defined("SQL_INSERT_REQ_RECORD")
		or define("SQL_INSERT_REQ_RECORD", "INSERT INTO %s "
				." (ip, session, product, cooltype, kernel, type, height, width, imei, imsi, imeid, net, channel, vercode, insert_time) "
				." VALUES ('%s', '%s', '%s', %d, %d, %d, %d, %d, '%s', '%s', '%s', '%s', %d, %d, '%s')");

//下载记录
defined("SQL_INSERT_DL_RECORD")
		or define("SQL_INSERT_DL_RECORD", "INSERT INTO %s "
				." (ip, session, identity, cpid, product, imeid, imei, imsi, type, cooltype, channel, height, width, url, net, insert_time)"
				." VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, '%s', '%s', '%s')");

//浏览记录
defined("SQL_INSERT_BROWSE_RECORD")
		or define("SQL_INSERT_BROWSE_RECORD", "INSERT INTO %s" 
				." (ip, session, identity, cpid, product, imeid, imei, imsi, type, cooltype, channel, height, width, url, net, insert_time) "
				." VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, '%s', '%s', '%s')");

//主题、壁纸、铃声应用统计
defined("SQL_INSERT_APPLY_RECORD")
		or define("SQL_INSERT_APPLY_RECORD", "INSERT INTO %s "
				." (ip, session, identity, cpid, product, imeid, imei, imsi, applytype, cooltype, height, width, net, insert_time)" 
				." VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d, '%s', '%s')");

//安卓壁纸封面浏览记录
defined("SQL_INSERT_COVER_RECORD")
		or define("SQL_INSERT_COVER_RECORD", "INSERT INTO tb_yl_adwp_cover_req_record "
				." (ip, session, product, imeid, imei, imsi, height, width, net, insert_time) "
				." VALUES ('%s', '%s', '%s', '%s', '%s', '%s', %d, %d, '%s', '%s')");
		
//安卓壁纸封面列表浏览记录及封面（广告）点击测试
defined("SQL_INSERT_COVER_LIST_RECORD")
		or define("SQL_INSERT_COVER_LIST_RECORD", "INSERT INTO tb_yl_adwp_cover_list_req_record"
				." (identity, ip, session, product, imeid, imei, imsi, height, width, net, insert_time) "
				." VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d, '%s', '%s')");
		
