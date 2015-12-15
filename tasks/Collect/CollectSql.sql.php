<?php

/**
 * 验证历史关注
*/
defined("SQL_CHECK_COLLECT")
	or define("SQL_CHECK_COLLECT", "SELECT COUNT(id) FROM tb_yl_collect_designer WHERE cyid = '%s' AND authorid = '%s'");
/**
 *新增关注
 */
defined("SQL_INSERT_COLLECT")
	or define("SQL_INSERT_COLLECT", "INSERT INTO tb_yl_collect_designer 
									(cyid, authorid, authorname, collect, insert_time) 
									VALUE('%s', '%s', '%s', 1, '%s')" );
/**
 * 更新关注状态
 */
defined("SQL_UPDATE_COLLECT")
	or define("SQL_UPDATE_COLLECT", "UPDATE tb_yl_collect_designer SET collect = %d 
			 						WHERE cyid = '%s' AND authorid = '%s'" );
	
/**
 * 获取我的设计是
 */
defined("SQL_SELECT_COLLECT")
	or define("SQL_SELECT_COLLECT", "SELECT * FROM tb_yl_collect_designer WHERE cyid = '%s' AND collect = 1 " );

/**
 *获取设计师详细信息 
 */
defined("SQL_SELECT_DESIGNER")
	or define("SQL_SELECT_DESIGNER", "SELECT * FROM tb_yl_coolyun_user user 
									  LEFT JOIN tb_yl_coolyun_userinfo info ON user.userid =  info.userid 
									  WHERE username = '%s' " );
	
/**
 * 获取我的设计是
 */
defined("SQL_SELECT_COLLECT_STATUS")
    or define("SQL_SELECT_COLLECT_STATUS", "SELECT * FROM tb_yl_collect_designer WHERE cyid = '%s' AND authorid = '%s' " );