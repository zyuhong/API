<?php
require_once 'CoolXiuDb.class.php';

class CoolXiuList{
	private $_coolXiuDb;
	var $rsp_count;
	
	function __construct(){
		$this->_coolXiuDb = new CoolXiuDb();
	}
	
	function getFaultResult($error_no){
		$count = $error_no;
		$rsp_num = 0;
		$json_rsp =  array(
				'total_number'=>$count,
				'rst_number'=>$rsp_num);
	
		return json_encode($json_rsp);
	}
	/**
	 * 返回请求的主题数量，等于或小于请求数
	 */
	function getReqCoolXiuCount(){
		return $this->rsp_count;
	}
	
	/**
	 * 返回主题总数量
	 */
	function getCoolXiuCount($coolxiu){
		$sql = $coolxiu->getCountSql();
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("CoolXiuDb::getCoolXiuCount():query()".$sql." error", "log");
			return false;
		}
		list($count) = $this->sql_conn->fetch_row();
		return $count;
	}
	/**
	 * 根据请求的数量和开始项获取CoolXiu
	 *
	 * 返回Coolxiu列表
	 * @param unknown_type $xiu_type
	 * @param unknown_type $page
	 * @param unknown_type $req_num
	 */
	function getReqCoolXiuList($xiu_type, $page, $req_num){
		try {
			$result = $this->_coolXiuDb->connectMySql();
			if(!$result){
				Log::write("CoolXiuDb::getReqCoolXiuList():connectMySql() failed", "log");
				$count = -2;
				return $this->getFaultResult($count);
			}
				
			$coolxiu = $this->_coolXiuDb->getCoolXiu($xiu_type);
	
			$start = $page * $req_num;
			$coolxius = $this->getCoolXiuByLimit($xiu_type, $coolxiu, $start, $req_num);
			if(!$coolxius){
				Log::write("CoolXiuDb::getReqCoolXiuList():getThemesByLimit() failed", "log");
				$count = -3;
				return $this->getFaultResult($count);
			}
				
			$result = $this->getCoolXiuCount();
			if(!$result){
				Log::write("CoolXiuDb::getReqCoolXiuList():getThemesCount() failed", "log");
				$count = -4;
				return $this->getFaultResult($count);
			}
				
			$count = (int)$result;
			$rsp_num = $this->getReqCoolXiuCount();
			$json_rsp =  array('total_number'=>$count,
					'rst_number'=>$rsp_num,
					'themes'=>$coolxius);
		}catch(Exception $e){
			Log::write("CoolXiuDb::getReqCoolXiuList(): excepton error:".$e->getMessage(), "log");
			$count = -1;
			return $this->getFaultResult($count);
		}
		return json_encode($json_rsp);
	}
	
	function getCoolXiuByLimit($xiu_type, $coolxiu, $start, $req_num){
		try{
			$sql = $coolxiu->getSelectLimitSql($start, $req_num);
			$result = $this->sql_conn->query($sql);
			if(!$result){
				Log::write("ThemesDbManager::GetThemesByLimit()".$sql." error", "log");
				return false;
			}
			$this->rsp_count = $this->sql_conn->db_num_rows();
			if ($this->rsp_count == 0){
				return true;
			}
			$rows = $this->sql_conn->fetch_assoc_rows();
	
			$coolxius = $this->getCoolXius($xiu_type, $rows);
				
		}catch(Exception $e){
			Log::write("ThemesManager::getThemesByLimit()exception".$e->getMessage(), "log");
			return false;
		}
		return $coolxius;
	}
	
	function getCoolXius($xiu_type, $rows){
		$coolxius = array();
		switch ($xiu_type){
			case COOLXIU_TYPE_THEMES:
				{
					foreach($rows as $row){
							
						$theme = new ThemesProtocol();
						$theme->setProtocol($row);
							
						$prev_imgs = array();
						$img_num = 0;
						$main_url = "";
						$result = $this->getPrevsByTheme($row["folder"],$img_num, $main_url, $prev_imgs);
						if(!$result){
							Log::write("ThemesManager::getThemesByLimit():getPrevsByTheme()false", "log");
							return false;
						}
						$theme->setPrevImgs($img_num, $main_url, $prev_imgs);
						array_push($coolxius, $theme);
					}
				}
				break;
			case COOLXIU_TYPE_PREV:
				break;
			case COOLXIU_TYPE_WALLPAPER:
				{
					foreach($rows as $row){
						$wallpaper = new WallpaperProtocol();
						$wallpaper->setWallpaper($row);
						array_push($coolxius, $wallpaper);
					}
				}
				break;
			default:
				{
					return false;
				}break; 
		}
		return $coolxius;
	}
	
	/**
	 *
	 * 根据主题文件夹获取缩略图
	 * @param unknown_type $theme_folder 主题文件夹
	 * @param unknown_type $img_num		 缩略图数量 引用
	 * @param unknown_type $main_img	主缩略图 引用
	 * @param unknown_type $prev_imgs	缩略图数组 引用
	 * @return boolean
	 */
	function getPrevsByTheme($theme_folder, &$img_num, &$main_img, &$prev_imgs){
	
		$sql = sprintf(SQL_SELECT_THEME_PREV_INFO, $theme_folder);
	
		$result = $this->sql_conn->query($sql);
		if(!$result){
			Log::write("ThemesManager::getPrevsByTheme()".$sql." error", "log");
			return false;
		}
			
		if($this->sql_conn->db_num_rows() > 0){
			$rows = $this->sql_conn->fetch_assoc_rows();
			foreach($rows as $row){
				$prev = new PrevProtocol();
				$prev->setPrev($row);
				++$img_num;
				if($main_img == ""){
					$main_img = $prev->img_url;
				}
				array_push($prev_imgs, $prev);
			}
		}
		return true;
	}
}