<?php
/**
 *数据库操作语句
 *
 *预定义数据库操作相关语句
 */
define("DB_CREATE_THEME", "CREATE DATABASE `db_yl_theme`");
define("DB_CREATE_TABLE_THEME_INFO", "CREATE TABLE tb_yl_theme_info`
		(`name` varchar(256)  NOT NULL ,
		`url` varchar(256)  NOT NULL ,
		`size` DOUBLE NOT NULL ,
		`note` TEXT NOT NULL ,
		`id` INT NOT NULL ,
		`insert_time` DATETIME NULL ,
		`insert_user` INT NULL ,
		`theme_file_md5` DOUBLE NOT NULL ,
		`author` TEXT NOT NULL ,
		`type` INT NOT NULL ,
		`img_num` INT NOT NULL ,
		PRIMARY KEY ( `name` ) ,
		INDEX (`id`)
		) ENGINE = InnoDB;");

define("DB_CREATE_TABLE_THEME_PREV_INFO", "CREATE TABLE `tb_yl_theme_preview_info`
		(`theme_name` varchar(256)  NOT NULL ,
		`prev_url` varchar(256)  NOT NULL ,
		`prev_name` varchar(256)  NOT NULL ,
		`size` DOUBLE NOT NULL ,
		`note` TEXT NOT NULL ,
		`id` INT NOT NULL ,
		`prev_file_md5` DOUBLE NOT NULL ,
		PRIMARY KEY ( `prev_url` )
		) ENGINE = InnoDB;");

define("DB_THEME_NAME", "tb_yl_theme_info");

?>