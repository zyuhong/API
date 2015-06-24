<?php
defined("SQL_SELECT_SCENE")
	or define("SQL_SELECT_SCENE", "SELECT pay.*, scene.*, scene.ischarge, kernel.totalSize "
								  ." FROM tb_yl_scene scene"
								  ." LEFT JOIN tb_yl_scene_kernel kernel ON scene.sceneCode = kernel.sceneCode "
								  ." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid" 	
								  ." WHERE kernel.kernelCode = %d %s "
								  ." ORDER BY scene.sceneCode DESC  "
								  ." LIMIT %d, %d");
	
defined("SQL_SELECT_SCENE_APK")
	or define("SQL_SELECT_SCENE_APK", "SELECT pay.*, scene.*, dl.download_times"
								  ." FROM tb_yl_scene scene"
								  ." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid"
								  ." LEFT JOIN tb_yl_scene_download dl ON dl.cpid = scene.sceneCode"  
								  ." WHERE scene.valid = 1 "	 	
								  ." ORDER BY scene.sceneCode DESC  "
								  ." LIMIT %d, %d");
	
defined("SQL_COUNT_SCENE_APK")	
	or define("SQL_COUNT_SCENE_APK", "SELECT COUNT(id)"
								  ." FROM tb_yl_scene scene"
								  ." WHERE scene.valid = 1 ");
	
defined("SQL_SELECT_SCENE_WITH_ID")
	or define("SQL_SELECT_SCENE_WITH_ID", "SELECT pay.*, scene.*, scene.ischarge, kernel.totalSize "
								." FROM tb_yl_scene scene"
								." LEFT JOIN tb_yl_scene_kernel kernel ON scene.sceneCode = kernel.sceneCode "
								." LEFT JOIN tb_yl_pay pay ON pay.waresid = scene.waresid"
								." WHERE scene.sceneCode = %d AND kernel.kernelCode = %d ");
	
defined("SQL_COUNT_SCENE")
	or define("SQL_COUNT_SCENE", "SELECT COUNT(scene.id) "
								  ." FROM tb_yl_scene scene"
								  ." LEFT JOIN tb_yl_scene_kernel kernel ON scene.sceneCode = kernel.sceneCode "
								  ." WHERE kernel.kernelCode = %d %s ");
	

defined("SQL_SELECT_SCENE_BY_ID")
	or define("SQL_SELECT_SCENE_BY_ID", "SELECT sceneCode, fname, totalSize, md5, zhName, enName, url"
								." FROM tb_yl_scene"
								." WHERE sceneCode = '%s'");
	