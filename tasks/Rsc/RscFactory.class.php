<?php
require_once('configs/config.php');
require_once("tasks/CoolXiu/CoolXiuDb.class.php");	
require_once("tasks/androidWallpaper/AndroidWallpaperDb.class.php");
require_once 'tasks/ring/RingDb.class.php';
require_once 'tasks/Font/FontDb.class.php';
require_once 'tasks/LockScreen/ScreenDb.class.php';

class RscFactory
{
	public $_nCoolType;
	public $_strId;
	public $_nType;
	public $_nAdType;
	public $_nWidth;
	public $_nHeight;
	public $_nSceneCode;
	public $_nKernelCode;
	public $_bIsOrder;	
	public $_bIsGoing;
	public $_bIsLarge;
	public $_strUrl;
	
	public function __construct()
	{
		$this->_nCoolType = 0;
		$this->_strId = 0;
		$this->_nType = 0;
		$this->_nAdType = 0;
		$this->_nWidth = 0;
		$this->_nHeight = 0;
		$this->_nSceneCode = 0;
		$this->_nKernelCode = 0;
		$this->_bIsOrder = false;
		$this->_bIsGoing = false;
		$this->_bIsLarge = false;
		$this->_strUrl	 = '';
	}
	
	public function setParam($nCoolType, $strId, 
							 $nType, $nAdType,
							 $nWidth, $nHeight, 
							 $nSceneCode, $nKernelCode,
							 $nIsOrder = false, $nIsGoing = false, $nIsLarge = false, $strUrl = '')
			
	{
		$this->_nCoolType = $nCoolType;
		$this->_strId = $strId;
		$this->_nType = $nType;
		$this->_nAdType = $nAdType;
		$this->_nWidth = $nWidth;
		$this->_nHeight = $nHeight;
		$this->_nSceneCode = $nSceneCode;
		$this->_nKernelCode = $nKernelCode;
		$this->_bIsOrder = $nIsOrder?true:false;
		$this->_bIsGoing = $nIsGoing?true:false;
		$this->_bIsLarge = $nIsLarge?true:false;	
		$this->_strUrl   = $strUrl;
	}
	
	public function getSrc($channel = 0, $strTitle = '', $strContent = '')
	{
		$coolxiu = '';
		$protocol = null;
		$strId	  = $this->_strId;
		switch ($this->_nCoolType ){
			case COOLXIU_TYPE_THEMES:
				$theme = new CoolXiuDb();
				$protocol = $theme->getRsc($this->_strId, $channel);
				$coolxiu = 'themes';
				break;
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:
				$wp = new AndroidWallpaperDb();
				$protocol = $wp->getRsc($this->_strId, $this->_nWidth, $this->_nHeight, $this->_nType, $this->_nAdType);
				$coolxiu = 'wallpapers';
				break;
			case COOLXIU_TYPE_RING:
				$ring = new RingDb();
				$protocol = $ring->getRsc($this->_strId, $channel);
				$coolxiu = 'ring';
				break;
			case COOLXIU_TYPE_FONT:
				$font = new FontDb();
				$protocol = $font->getRsc($this->_strId, $channel);
				$coolxiu = 'fonts';
				break;
			case COOLXIU_TYPE_SCENE:	
				$screen = new ScreenDb();
				$protocol = $screen->getRsc($this->_nSceneCode, $this->_nKernelCode);
				$coolxiu = 'lockscreens';
				$strId = $this->_nSceneCode;
				break;
			default:
				return false;
				break;
		}
		
		$arrResult = array('isdirect'=>true,
						   'entity'=>array('pushtype'=>(int)$this->_nCoolType,
											'isorder'=>$this->_bIsOrder?true:false,
											'title' => $strTitle,
											'content' => $strContent,
											'isgoing' => $this->_bIsGoing?true:false,
											'islarge' => $this->_bIsLarge?true:false,
											'url' 	  => $this->_strUrl,
											'id' => $strId,
											$coolxiu=>$protocol));
				
		return json_encode($arrResult);
	}
	
	public function getResc($nCoolType, $strId, $channel = 0)
	{
		switch ($nCoolType ){
			case COOLXIU_TYPE_THEMES:
				$theme = new CoolXiuDb();
				$protocol = $theme->getRsc($strId, $channel);
				$coolxiu = 'themes';
				break;
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:
				break;
			case COOLXIU_TYPE_RING:
				$ring = new RingDb();
				$protocol = $ring->getRsc($strId, $channel);
				$coolxiu = 'ring';
				break;
			case COOLXIU_TYPE_FONT:
				$font = new FontDb();
				$protocol = $font->getRsc($strId, $channel);
				$coolxiu = 'fonts';
				break;
			case COOLXIU_TYPE_SCENE:
				break;
			default:
				return false;
				break;
		}
		return $protocol;
	}
}