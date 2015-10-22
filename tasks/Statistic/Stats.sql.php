<?php
	define('DB_CREATE_TABLE_STATS', 'CREATE TABLE `stats`(
			 `type` char(16) NOT NULL,
			 `variable` char(20) NOT NULL,
 			 `count` int(12) unsigned NOT NULL default 0, PRIMARY KEY (`type`,`variable`)
 			) ENGINE=MyISAM DEFAULT CHARSET=utf8');
	
	define("SQL_INSERT_STATS", "INSERT INTO yl_tb_stats (type, variable, count) VALUES ('%s', '%s', %d)");
	define("SQL_UPDATE_STATS", "UPDATE yl_tb_stats SET count = %d WHERE type = '%s' variable = '%s' LIMIT 1");
	define("SQL_SELECT_COUNT_STATS", "SELECT count FROM yl_tb_stats WHERE type = '%s' variable = '%s'")
?>