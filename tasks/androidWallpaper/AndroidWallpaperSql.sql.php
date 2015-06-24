<?php
/**
 * MODIFY BY liangweiwei@yulong.com AT 2012-08-03
 */

defined("SQL_SELECT_SIZE_TAG_FOR_REQ")
		or define("SQL_SELECT_SIZE_TAG_FOR_REQ", "SELECT size_res, size_mid, size_small "
												." FROM transfer WHERE size_req = '%s'");

defined("SQL_SELCET_COUNT_ALL")
		or define("SQL_SELCET_COUNT_ALL", "SELECT COUNT(*) FROM %s");

defined("SQL_SELECT_ANDROIDWALLPAPER_BY_TYPE_LIMIT")
		or define("SQL_SELECT_ANDROIDWALLPAPER_BY_TYPE_LIMIT", "SELECT id AS d, id_wallpaper AS id, %s AS url,  %s AS mid_url, %s AS small_url, "
																." origin_w, origin_h, ad_rank AS download_times, cp_rank"
																." FROM %s %s LIMIT %d, %d ");
		
defined("SQL_SELECT_ANDROIDWALLPAPER_WITH_ID")
		or define("SQL_SELECT_ANDROIDWALLPAPER_WITH_ID", "SELECT id_wallpaper AS id, %s AS url,  %s AS mid_url, %s AS small_url, "
																." origin_w, origin_h, ad_rank AS download_times, cp_rank"
																." FROM id_info WHERE id_wallpaper = '%s'");		
		
defined("SQL_SELECT_ANDROIDWALLPAPER_LAST")
		or define("SQL_SELECT_ANDROIDWALLPAPER_LAST", "SELECT id_wallpaper AS id, %s AS url,  %s AS mid_url, %s AS small_url, "
																." origin_w, origin_h, ad_rank AS download_times, cp_rank"
																." FROM %s ORDER BY id DESC LIMIT %d, %d ");

defined("SQL_SELECT_ANDROIDWALLPAPER_HOT")
		or define("SQL_SELECT_ANDROIDWALLPAPER_HOT", "SELECT id_wallpaper AS id, %s AS url,  %s AS mid_url, %s AS small_url, "
																." origin_w, origin_h, ad_rank AS download_times, cp_rank"
																." FROM %s ORDER BY ad_rank DESC LIMIT %d, %d ");
defined("SQL_SELECT_ANDROIDWALLPAPER_CHOICE")
		or define("SQL_SELECT_ANDROIDWALLPAPER_CHOICE", "SELECT id_wallpaper AS id, %s AS url,  %s AS mid_url, %s AS small_url, "
																." origin_w, origin_h, ad_rank AS download_times, cp_rank"
																." FROM commend LIMIT %d, %d ");		
defined("SQL_SELECT_WP_AD_TYPE")
		or define("SQL_SELECT_WP_AD_TYPE", "SELECT type, adid AS adid, thumb_url AS url FROM ad_info_list WHERE valid = 1 ");

defined("SQL_SELECT_WP_AD_WITH_ID")
		or define("SQL_SELECT_WP_AD_WITH_ID", "SELECT type, adid AS adid, thumb_url AS url FROM ad_info_list WHERE adid = '%s'");
		
		
defined("SQL_SELECT_WP_AD_BY_TYPE")
		or define("SQL_SELECT_WP_AD_BY_TYPE", "SELECT id_wallpaper AS id, %s AS url, %s AS mid_url, %s AS small_url,"
																." ad_rank AS download_times, cp_rank"
																." FROM ad_list" 
																." WHERE id_adid = '%s' LIMIT %d, %d"
																);
																
defined("SQL_COUNT_WP_AD_BY_TYPE")
		or define("SQL_COUNT_WP_AD_BY_TYPE", "SELECT COUNT(*)"
											." FROM ad_list" 
											." WHERE id_adid = '%s'");												
?>