<?php
defined("YL_SQL_INSERT_CHARGE_RECORD")
	or define("YL_SQL_INSERT_CHARGE_RECORD", "INSERT INTO tb_yl_coolpadtone_record "
											." (exorderno, transid, appid, waresid, changepoint, feetype, money, count, result, transtype, transtime, sign, insert_time) "
											." VALUES ('%s', '%s', '%s', '%s', '%s', %d, %d, %d, %d, %d, '%s', '%s', '%s')");

defined("YL_SQL_INSERT_N_CHARGE_RECORD")
	or define("YL_SQL_INSERT_N_CHARGE_RECORD", "INSERT INTO tb_yl_coolpadtone_record "
											." (exorderno, transid, appid, appname, waresid, mername, "
											."  changepoint, chargepointname, money, count, result, transtime, "
											." paytype, paytypename, phone, operators,"
											." sign, insert_time) "
											." VALUES ('%s', '%s', '%s', '%s', '%s', '%s', "
											."         '%s', '%s', %d,   %d,    %d,  '%s', "
											." 		   '%s', '%s', '%s', '%s',"
											."  	   '%s', '%s')");