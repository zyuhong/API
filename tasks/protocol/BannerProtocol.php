<?php
require_once 'configs/config.php';

class BannerProtocol
{
	public $bannerUrl;			//URL
	public $bannerName;			//Name
	public $bannerRes;
    public $bannerType;         //banner类型 1 单个 2 列表 3 混合 4 网页
	//新版banner 20141107
	//修改为拉取banner图片列表与绑定的bannerid，二次获取bannerid关联的资源
	public $id;
	public $type;
	public $msubtype;

	public $isH5;
	public $H5Url;
	
	public function __construct(){
		$this->bannerUrl	= '';
		$this->bannerName	= '';
		$this->bannerRes	= array();
        $this->bannerType = 0;
		
		$this->id			= '';
		$this->type			= 0;	#模块类型
		$this->msubtype		= 0;	#模块子类型

		$this->isH5			= false; #20150505
		$this->H5Url		= '';
	}

	public function setBanner($strUrl, $strName, $strBannerId = '', $nType = 0){
		global $g_arr_host_config;
		$this->bannerUrl	= $g_arr_host_config['cdnhost'].$strUrl;
		$this->bannerName	= $strName;
		$this->id			= $strBannerId;
		$this->type			= (int)$nType;
	}
	
	public function setBannerRes($strId, $res){
		$this->bannerRes = $this->bannerRes + array($strId => $res);
	}
	
	public function setType($nType)
	{
		$this->type = $nType;
	}
	
	public function setSubType($nType)
	{
		$this->msubtype = $nType;
	}
	
	public function setProtocol($row, $nCoolType = 0)
	{
		$protocol = (int)(isset($_GET['protocolCode'])?$_GET['protocolCode']:0);
		$strUrl  =  isset($row['url'])?$row['url']:'';
		if ($protocol >= 3 ){
			$strTemp  =  isset($row['newurl'])?$row['newurl']:'';
			$strUrl = empty($strTemp)?$strUrl:$strTemp;
		}
		global $g_arr_host_config;
		$this->bannerUrl	= $g_arr_host_config['cdnhost'].$strUrl;
		if($nCoolType == COOLXIU_TYPE_ANDROIDESK_WALLPAPER){
			$this->bannerUrl	= $strUrl;
		}
		$this->bannerName	= isset($row['name'])?$row['name']:'';
		
		$this->id			= isset($row['identity'])?$row['identity']:'';
		$this->type			= (int)(isset($row['cooltype'])?$row['cooltype']:0);
		$this->msubtype		= (int)(isset($row['msubtype'])?$row['msubtype']:0);
        $this->bannerType = (int)(isset($row['bannertype'])?$row['bannertype']:0);
		
		$nH5				= isset($row['H5'])?$row['H5']:0;
		$this->isH5			= $nH5?true:false;
		$this->H5Url		= isset($row['H5Url'])?$row['H5Url']:'';
	}	
	
	public function getProtocol($resName)
	{
		$arrBannerRes = array();
		foreach($this->bannerRes as $key => $value){
			array_push($arrBannerRes, $value);
		}
 		$this->bannerRes = $arrBannerRes;
		
		return array('bannerUrl'	=>$this->bannerUrl,
					 'bannerName' 	=>$this->bannerName,
					 $resName	  	=>$this->bannerRes);
	}
}