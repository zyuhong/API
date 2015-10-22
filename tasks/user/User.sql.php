<?php
/**
 * @example    DATABASE
 *
 * TABLE NAME IS 'tb_yl_user'
 * ----------------------------
 * identify |         id        |   password   |  name  |    email    |power
 * ---------------------------------------------------------------------------
 * type     |char(7) key primary|   char(40)   |char(40)|   char(80)  | int(1)
 * ---------------------------------------------------------------------------
 * example  |      yl12345      |sh1($password)|  joy li| abc@abc.com |   0
 * 
 */

define("TB_USER_NAME",   "tb_yl_user");
define("SQL_COUNT_USER", "SELECT COUNT(*) FROM tb_yl_user");
			
define("SQL_CHECK_USER_BY_ID",    "SELECT COUNT(*) FROM tb_yl_user WHERE id='%s'"); 	
define("SQL_CHECK_USER_NAME",  "SELECT COUNT(*) FROM tb_yl_user WHERE name='%s'"); 	
define("SQL_CHECK_USER_EMAIL", "SELECT COUNT(*) FROM tb_yl_user WHERE email='%s'"); 	
define("SQL_CHECK_USER_PHONE", "SELECT COUNT(*) FROM tb_yl_user WHERE phone='%s'"); 	
define("SQL_CHECK_USER_PASSWD", "SELECT COUNT(*) FROM tb_yl_user WHERE name='%s'AND passwd='%s'");
define("SQL_CHECK_USER_INFO",  "SELECT COUNT(*) FROM tb_yl_user WHERE id='%s' AND name = '%s' AND email = '%s'");

define("SQL_SELECT_USER_BY_NAME",   	"SELECT * FROM tb_yl_user WHERE name='%s'"); 
define("SQL_SELECT_USER_BY_NAME_PSW",   "SELECT * FROM tb_yl_user WHERE name = '%s' AND passwd = '%s'");

define("SQL_INSERT_USER_INFO",    "INSERT INTO tb_yl_user (passwd, name, email, power, phone, insert_time, insert_user) VALUES ( '%s', '%s', '%s', %d, '%s', '%s', '%s')");

define("SQL_UPDATE_USER_PASSWD_BY_NAME_PASSWD", "UPDATE tb_yl_user SET passwd = '%s' WHERE name = '%s' AND passwd = '%s'");
define("SQL_UPDATE_USER_PASSWD_BY_EMAIL","UPDATE tb_yl_user SET passwd = '%s' WHERE name = '%s' AND email='%s'");

define("SQL_DELETE_USER_BY_NAME", "DELETE FROME tb_yl_user WHERE name = '%s'"); 
?>