<?php
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/CoolShow/CoolShow.class.php';
require_once 'tasks/CoolShow/AlbumSql.sql.php';
require_once 'tasks/protocol/BannerProtocol.php';
require_once 'configs/config.php';

class Album extends CoolShow
{

	public function __construct()
	{
		parent::__construct();
	}
	
	public function getSelectBannerListSql($nCoolType, $bAlbum = 0)
	{
		$strCondition = '';
		if (!$bAlbum){
			$strCondition = ' AND album = 0 ';
		}
		$sql = sprintf(SQL_SELECT_ALBUM_LIST, $nCoolType, $strCondition);
		return $sql;
	}
	
	public function getSelectAndroideskBannerListSql()
	{
		$sql = sprintf(SQL_SELECT_ANDROIDESK_ALBUM_LIST);
		return $sql;
	}

	public function getLucene($rows)
	{
		return $this->getProtocol($rows);
	}
	
	public function getProtocol($rows, $nType = 0)
	{
		$arrTop = array();
		$arrBottom = array();
		$arrFoot = array();
		foreach($rows as $row){
			$banner = new BannerProtocol();
			$banner->setProtocol($row, $nType);
			$banner->setType($nType);
			if($this->_bSceneWallpaer){
				$banner->setSubType(1);
			}
			$bIsTop = isset($row['istop'])?$row['istop']:0;
			switch($bIsTop){
				case 1:
					array_push($arrTop, $banner);break;
				case 0:
					array_push($arrBottom, $banner);break;
				case 2:
					array_push($arrFoot, $banner);break;
			}
		}
		return array('top'=>$arrTop,
					 'bottom' => $arrBottom,
					 'foot' => $arrFoot,);
	}
	
	public function getBannerProtocol($rows, $nType = 0)
	{
		$arrProtocol = array();
		foreach($rows as $row){
			$banner = new BannerProtocol();
			$banner->setProtocol($row, $nType);
			$banner->setType($nType);
			if($this->_bSceneWallpaer){
				$banner->setSubType(1);
			}
			array_push($arrProtocol, $banner);
		}
		return  $arrProtocol;
	}
	
	public function setPayRatio(){}
	public function getCoolShowListSql($nStart = 0, $nLimit = 0){}
	public function getCoolShowCountSql(){}
	public function getSelectBannerSql(){}
	public function getSelectRscSql($id){}
	public function getSelectAlbumsSql($strId, $nStart = 0, $nNum = 100){}
	public function getSelectInfoByIdSql($id, $nChannel = 0){}
	
	public function getCoolShowWebSql($nSortType, $nStart = 0, $nLimit = 10){}
	public function getCoolShowWebCountSql(){}
	public function getWebProtocol($rows){}
}