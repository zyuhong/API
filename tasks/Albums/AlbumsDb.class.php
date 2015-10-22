<?php
require_once('configs/config.php');
require_once("tasks/CoolXiu/CoolXiuDb.class.php");	
require_once("tasks/androidWallpaper/AndroidWallpaperDb.class.php");
require_once 'tasks/ring/RingDb.class.php';

class AlbumsDb
{
	public $url;
	public function __construct()
	{
		$this->_nCoolType = 0;
		$this->_strProduct = '';
		$this->_strId = '';
		$this->_nType = 0;
		$this->_nAdType = 0;
		$this->_nWidth = 0;
		$this->_nHeight = 0;
		$this->_nKernelCode = 0;
		$this->_nChannel = 0;
	}
	
	public function setParam($nCoolType,
							 $strProduct, $strId, 
							 $nType, $nAdType,
							 $nWidth, $nHeight, 
							 $nKernelCode)
	{
		$this->_nCoolType = $nCoolType;
		$this->_strProduct = $strProduct;
		$this->_strId = $strId;
		$this->_nType = $nType;
		$this->_nAdType = $nAdType;
		$this->_nWidth = $nWidth;
		$this->_nHeight = $nHeight;
		$this->_nKernelCode = $nKernelCode;
	}
	
	public function getAlbums()
	{
		$coolxiu = '';
		$protocol = null;
		switch ($this->_nCoolType ){
			case COOLXIU_TYPE_THEMES:
				$protocol = $this->_getThemeAlbum($coolxiu);
				break;
			case COOLXIU_TYPE_WALLPAPER:
				$protocol = $this->_getWpAlbum($coolxiu);
				break;
			case COOLXIU_TYPE_RING:
				$protocol = $this->_getRingAlbum($coolxiu);
				break;
			case COOLXIU_TYPE_ALBUMS:
				
				$thProtocol = $this->_getThemeAlbum($coolxiu);
				$nthCount 	= count($thProtocol);				
				
				$wpProtocol = $this->_getWpAlbum($coolxiu);
				$nwpCount 	= count($wpProtocol);
				
				$rProtocol 	= $this->_getRingAlbum($coolxiu);
				$nrCount 	= count($rProtocol);
				
				return  json_encode(array('adUrl'	=>'http://61.141.236.11/themes/widget/369073055/1440x1280/1629302002.jpg',
										  'themeNum'=>(int)$nthCount,
										  'themes'	=>$thProtocol,
										  'wallpaperNum'=>$nwpCount,
										  'wallpapers' =>$wpProtocol,
										  'ringNum'	=>$nrCount,
										  'ring' =>$rProtocol));
			default:
				return false;
				break;
		}
		
		return json_encode(array('num'=>(int)count($protocol),
								 $coolxiu =>$protocol));
	}
	
	private function _getThemeAlbum(&$coolxiu)
	{
		$theme = new CoolXiuDb();
		$theme->setSearchCondition($this->_nWidth, $this->_nHeight, $this->_nKernelCode);
		$protocol = $theme->getAlbum(COOLXIU_TYPE_THEMES);
		$coolxiu = 'themes';
		return $protocol;
	}
	
	private function _getWpAlbum(&$coolxiu)
	{
		$wp = new CoolXiuDb();
		$wp->setSearchCondition($this->_nWidth, $this->_nHeight);
		$protocol = $wp->getAlbum(COOLXIU_TYPE_WALLPAPER);
		$coolxiu = 'wallpapers';
		return $protocol;
	}
	
	private function _getRingAlbum(&$coolxiu)
	{
		$ring = new RingDb();
		$protocol = $ring->getAlbum();
		$coolxiu = 'ring';
		return $protocol;
	}
}