<?php
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'tasks/CoolXiu/CoolXiuDb.class.php';	
require_once 'tasks/ring/RingDb.class.php';

class BannerFactory
{
	public $_nCoolType;
	public $_nWidth;
	public $_nHeight;
	public $_nKernelCode;
	public $_nVersionCode;
	public function __construct()
	{
		$this->_nCoolType = 0;
		$this->_nWidth = 0;
		$this->_nHeight = 0;
		$this->_nKernelCode = 0;
		$this->_nVersionCode = 0;
	}
	
	public function setParam()
	{
		
		$nCoolType = isset($_GET['type'])?$_GET['type']:4;  //cooltype:主题、壁纸、铃声、字体等分类
		$nWidth    = isset($_GET['width'])?$_GET['width']:540;
		$nHeight   = isset($_GET['height'])?$_GET['height']:960;
		$nKernelCode = isset($_GET['kernelcode'])?$_GET['kernelcode']:3;
		$nVercode  = (int)(isset($_GET['versionCode'])?$_GET['versionCode']:0);
		
		$this->_nCoolType = $nCoolType;
		$this->_nWidth = $nWidth;
		$this->_nHeight = $nHeight;
		$this->_nKernelCode = $nKernelCode;
		$this->_nVersionCode = $nVersionCode;
	}
	
	public function getBanner($channel = 0)
	{
		$bResutl = true;
		$protocol = null;
		switch ($this->_nCoolType ){
			case COOLXIU_TYPE_THEMES:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:
				$coolxiu = new CoolXiuDb();
				$protocol = $coolxiu->getBanner($this->_nCoolType, $this->_nWidth, $this->_nHeight,
										   		$this->_nKernelCode, $this->_nVersionCode, $channel);
				break;
			case COOLXIU_TYPE_RING:
				$ring = new RingDb();
				$protocol = $ring->getBanner();
				break;
			case COOLXIU_TYPE_FONT:
				$font = new FontDb();
				$protocol = $font->getBanner();
				break;
			case COOLXIU_TYPE_SCENE:	
				break;
			default:
				return false;
				break;
		}
		
		if(!$protocol || count($protocol) <= 0){
			return get_rsp_result(false, 'get banner error');
		}
		$result = array('result'=>true,
						'banners'=>$protocol);
		return json_encode($result);
	}
}