<?php

	require_once 'tasks/androidWallpaper/AndroidWallpaperSql.sql.php';
	
	require_once 'configs/config.php';
	
	class AndroidWallpaper{

		const commend 	= 0;
		const women		= 1;
		const landscape	= 2;
		const vision	= 3;
		const cartoon	= 4;
		const city		= 5;
		const sensibility = 6;
		const originality = 7;
		const animal 	= 8;
		const engine	= 9;
		const game		= 10;
		const scenery	= 11;
		const male		= 12;
		const art		= 13;
		const sport		= 14;
		const movie		= 15;
		const others	= 16;
		const hdorigin	= 17;
		const star		= 18;
		const font		= 19;
		
		
		private $width;
		private $height;
		private $start;
		private $req_num;
		private $req_type;
		private $type_name; 	// 请求类型名，对应数据库表名
		private $type_tag;  	// 分辨率所对应的字段名
		
		function __construct(){
			$this->width	 = 0;
			$this->height	 = 0;
			$this->start	 = 0;
			$this->req_num	 = 0;
			$this->req_type	 = 0;
			$this->type_name = "";
			$this->type_tag	 = "";
		}
		
		public function setRatio($width, $height){
			$this->type_tag  = sprintf('id_%d_%d', $width, $height);
		}
	
		function setAndroidWp($width, $height, $start, $req_num, $req_type){
			$this->width	 = $width * 2;
			$this->height	 = $height;
			$this->start	 = $start;
			$this->req_num	 = $req_num;
			$this->req_type  = $req_type;
			global $g_arr_tablename;
			$this->type_name = $g_arr_tablename[$req_type]; // commend 0
			$this->type_tag  = sprintf('id_%d_%d', $this->width, $this->height);//"id_".$this->width."_".$this->height;
				
		}
		
		public function setReqNum($start, $num)
		{
			$this->start	 = $start;
			$this->req_num	 = $num;
		}
		
		function getSelectAndroidWpSizeTagSql(){
			$sql = sprintf(SQL_SELECT_SIZE_TAG_FOR_REQ, $this->type_tag);
			return $sql;
		}
		function getCountAndroidWpSql(){
			
			$sql = sprintf(SQL_SELCET_COUNT_ALL, $this->type_name);
			return $sql;
		}
		
		public function getOrderBy($sorttype = 0)
		{
			$strOrder = ' ORDER BY asort ASC, d ASC ';
			switch($sorttype)
			{
				case COOLXIU_SEARCH_COMMEN:
					$strOrder = ' ORDER BY asort ASC, d ASC ';break;
				case COOLXIU_SEARCH_HOT:
					$strOrder   = ' ORDER BY ad_rank DESC ';break;
				case COOLXIU_SEARCH_CHOICE:
					$strOrder = ' ORDER BY asort ASC, d ASC ';break;
				default:
					$strOrder = ' ORDER BY asort ASC, d ASC ';break;
			}
			
			return $strOrder;
			
		}
		
		function getSelectAndroidWpByLimitSql($arr_size_tag, $sorttype = 0){
			if($this->req_type == self::hdorigin){
				$size_res = 'id_origin';
			}else{
				$size_res 	= $arr_size_tag['size_res'];
			}
			$size_mid 	= $arr_size_tag['size_mid'];
			$size_small = $arr_size_tag['size_small'];
			
			$strOrder = $this->getOrderBy($sorttype);
			if($sorttype == COOLXIU_SEARCH_HOT)$this->type_name = 'id_info'; 
			$sql = sprintf(SQL_SELECT_ANDROIDWALLPAPER_BY_TYPE_LIMIT,
														$size_res,
														$size_mid,
														$size_small,
														$this->type_name, 
														$strOrder, 
														$this->start, $this->req_num);
			return $sql;
		}
		
		function getSelectAndroidWpLastSql($arr_size_tag){
			if($this->req_type == self::hdorigin){
				$size_res = 'id_origin';
			}else{
				$size_res 	= $arr_size_tag['size_res'];
			}
			$size_mid 	= $arr_size_tag['size_mid'];
			$size_small = $arr_size_tag['size_small'];
			$sql = sprintf(SQL_SELECT_ANDROIDWALLPAPER_LAST,
					$size_res,
					$size_mid,
					$size_small,
					$this->type_name, $this->start, $this->req_num);
			return $sql;
		}
		
		function getSelectAndroidWpHotSql($arr_size_tag){
			if($this->req_type == self::hdorigin){
				$size_res = 'id_origin';
			}else{
				$size_res 	= $arr_size_tag['size_res'];
			}
			$size_mid 	= $arr_size_tag['size_mid'];
			$size_small = $arr_size_tag['size_small'];
			$sql = sprintf(SQL_SELECT_ANDROIDWALLPAPER_HOT,
					$size_res,
					$size_mid,
					$size_small,
					$this->type_name, $this->start, $this->req_num);
			return $sql;
		}
		
		function getSelectAndroidWpChoiceSql($arr_size_tag){
			if($this->req_type == self::hdorigin){
				$size_res = 'id_origin';
			}else{
				$size_res 	= $arr_size_tag['size_res'];
			}
			$size_mid 	= $arr_size_tag['size_mid'];
			$size_small = $arr_size_tag['size_small'];
			$sql = sprintf(SQL_SELECT_ANDROIDWALLPAPER_CHOICE,
					$size_res,
					$size_mid,
					$size_small,
					$this->start, $this->req_num);
			return $sql;
		}
		/**
		 * 获取广告类型列表
		 * @return string
		 */
		function getSelectAdTypeListSql(){
			$sql = SQL_SELECT_WP_AD_TYPE;
			return $sql;
		}
		/**
		 * 根据类型获取广告列表
		 * @return string
		 */
		function getSelectAdListByTypeSql($arr_size_tag, $type){
			$size_res 	= $arr_size_tag['size_res'];
			$size_mid 	= $arr_size_tag['size_mid'];
			$size_small = $arr_size_tag['size_small'];
			$sql = sprintf(SQL_SELECT_WP_AD_BY_TYPE, $size_res,
													 $size_mid,
													 $size_small,
													 $type, 
													 $this->start, $this->req_num);
			return $sql;
		}
		
		function getCountAdListSql($type){
			$sql = sprintf(SQL_COUNT_WP_AD_BY_TYPE, $type);
			return $sql;
		}
		
		static function getSelectWpWithIdSql($arr_size_tag, $type, $id){
			if($type == AndroidWallpaper::hdorigin){
				$size_res = 'id_origin';
			}else{
				$size_res 	= $arr_size_tag['size_res'];
			}
			$size_mid 	= $arr_size_tag['size_mid'];
			$size_small = $arr_size_tag['size_small'];
			$sql = sprintf(SQL_SELECT_ANDROIDWALLPAPER_WITH_ID, $size_res,
																$size_mid,
																$size_small,
																$id);
			return $sql;
		}
		/**
		 * 获取推送专题
		 * @return string
		 */
		static function getSelectAdWithIdSql($strId){
			$sql = sprintf(SQL_SELECT_WP_AD_WITH_ID, $strId);
			return $sql;
		}
		
	}