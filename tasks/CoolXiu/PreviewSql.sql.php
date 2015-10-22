<?php
/**
 *数据库操作语句
 *
 *预定义数据库操作相关语句
 */
//previews
define("SQL_INSERT_THEME_PREV_INFO", "INSERT INTO tb_yl_theme_preview_info "
				." (identity, folder, url, name, size, note, md5, type)"
				." VALUES ('%s', '%s', '%s', '%s', %d, '%s', '%s', %d)");

define("SQL_SELECT_THEME_PREV_INFO", "SELECT * FROM tb_yl_theme_preview_info WHERE theme_folder = '%s'");
define("SQL_DELETE_THEME_PREV_ALL", "DELETE ALL FROM tb_yl_theme_preview_info");
define("SQL_DELETE_THEME_PREV_BY_FOLDER", "DELETE * FROM tb_yl_theme_preview_info WHERE theme_folder='%s'");

?>