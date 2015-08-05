<?php 
/**
 * banner
 * 专辑SQL语句仅对终端接口
 * 
 * @var unknown_type
 * @date 20141107
 */


defined("SQL_SELECT_ALBUM_LIST")
	or define("SQL_SELECT_ALBUM_LIST", " SELECT * FROM tb_yl_albums WHERE valid = 1 AND cooltype = %d %s");

defined("SQL_SELECT_ANDROIDESK_ALBUM_LIST")
	or define("SQL_SELECT_ANDROIDESK_ALBUM_LIST", " SELECT ad_id AS name, adid AS identity, thumb_url AS url FROM ad_info_list WHERE valid = 1 ");