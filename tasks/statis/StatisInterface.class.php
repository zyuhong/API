<?php
require_once 'tasks/statis/ReqStatis.class.php';
require_once 'configs/config.php';
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/Records/RecordTask.class.php';

class StatisInterface
{
	const COOLSHOW_OPTYPE_REQ = 0;
	const COOLSHOW_OPTYPE_PREV = 1;
	const COOLSHOW_OPTYPE_DOWNLOAD = 2;
	const COOLSHOW_OPTYPE_APPLY = 3;
	
	public $strId;
	public $strCpid;
	public $nModuleType;
	public $nMSubType;
	public $nType;
	public $nOpType;
	public $nOpSubType;
	public $nChannel;
	private $_statis;
	
	public function __construct()
	{
		$this->_statis = new ReqStatis();

		$this->strId		= '';
		$this->strCpid		= '';
		$this->nModuleType	= 0;
		$this->nMSubType 	= 0;
		$this->nType 		= 0;
		$this->nOpType 	= 0;
		$this->nOpSubType	= 0;
		$this->nChannel	= 0;
	}
	
	public function setStatisParam()
	{
		$this->strId 	 	 = isset($_GET['id'])?$_GET['id']:'';
		$this->strCpid 	 	 = isset($_GET['cpid'])?$_GET['cpid']:'';
		$this->nModuleType	 = isset($_GET['moduletype'])?$_GET['moduletype']:'';
		$this->nMSubType	 = isset($_GET['msubtype'])?$_GET['msubtype']:'';
		$this->nType	 	 = (int)isset($_GET['type'])?$_GET['type']:$this->nMSubType;
		$this->nOpType  	 = isset($_GET['optype'])?$_GET['optype']:'';
		$this->nOpSubType 	 = isset($_GET['opsubtype'])?$_GET['opsubtype']:'';
		$this->nChannel 	 = (int)(isset($_GET['channel'])?$_GET['channel']:0);
		
		#ICON图标、通讯录都是主题模块的别名
		if ($this->nModuleType == COOLXIU_TYPE_THEMES_CONTACT
				|| $this->nModuleType == COOLXIU_TYPE_THEMES_ICON) {
			$this->nType	 = 	$this->nModuleType;
			$this->nModuleType 	= COOLXIU_TYPE_THEMES;
		}
		if ($this->nModuleType == COOLXIU_TYPE_LIVE_WALLPAPER) {
			$this->nType	 = 	$this->nModuleType;
			$this->nModuleType 	= COOLXIU_TYPE_SCENE;
		}
	}
	
	public function doStatis()
	{
		$height = 0;
		$width  = 0;
		
		$this->setStatisParam();
		
		$rt = new RecordTask();
		
		switch ($this->nOpType) {
			case self::COOLSHOW_OPTYPE_REQ:{
// 				$this->_statis->recordRequest($this->nMSubType, $this->nModuleType, $height, $width);
			}break;
			case self::COOLSHOW_OPTYPE_PREV:{
				if ($this->nModuleType == COOLXIU_TYPE_WIDGET
					&& $this->nMSubType == 1) {
					$this->_statis->recordBrowseRequest($this->strId, COOLXIU_TYPE_WIDGET, COOLXIU_TYPE_THEMES, $height, $width, $this->strCpid, '', $this->nChannel);
					return true;
				}
// 				$this->_statis->recordBrowseRequest($this->strId, $this->nMSubType, $this->nModuleType, $height, $width, $this->strCpid, '', $this->nChannel);
				
				$rt->saveBrowse($this->nModuleType);
// 				Log::write('StatisInterface browse module type:'.$this->nModuleType, 'debug');
				
			}break;
			case self::COOLSHOW_OPTYPE_DOWNLOAD:{
				$this->_statis->recordDownloadRequest($this->strId, $this->nModuleType, $height, $width, $this->strCpid, '', $this->nMSubType, $this->nChannel);
//				$rt->saveDownload($this->nModuleType);
			}break;
			case self::COOLSHOW_OPTYPE_APPLY:{
				$this->_statis->recordApply($height, $width, $this->nMSubType, $this->nModuleType, $this->strId, $this->strCpid);
				$rt->saveApply($this->nModuleType);
			}break;
		}
		return true;
	}
	
	public function saveStatis()
	{
		$height = 0;
		$width  = 0;
		$this->setStatisParam();

		switch ($this->nOpType) {
			case self::COOLSHOW_OPTYPE_PREV:{
				if ($this->nModuleType == COOLXIU_TYPE_WIDGET && $this->nType == 1) {
					$this->_statis->recordBrowseRequest($this->strId, COOLXIU_TYPE_WIDGET, COOLXIU_TYPE_THEMES, $height, $width, $this->strCpid, '', $this->nChannel);
					return true;
				}
// 				$this->_statis->recordBrowseRequest($this->strId, $this->nType, $this->nModuleType, $height, $width, $this->strCpid, '', $this->nChannel);
			}break;
			case self::COOLSHOW_OPTYPE_DOWNLOAD:{
                //添加下载记录到已购记录表中
                $this->saveUserDLRecord();

				$this->_statis->recordDownloadRequest($this->strId, $this->nModuleType, $height, $width, $this->strCpid, '', $this->nMSubType, $this->nChannel);
			}break;
			case self::COOLSHOW_OPTYPE_APPLY:{
				$this->_statis->recordApply($height, $width, $this->nType, $this->nModuleType, $this->strId, $this->strCpid);
			}break;
		}
		return true;
	}

    //查询是否已记录该用户该记录，如果未记录则保存
    private function saveUserDLRecord()
    {
        require_once 'tasks/Exorder/ExorderRecordDb.class.php';

        $strCyid = '';
        $nVerCode = 0;
        if (isset($_POST['statis'])) {
            $json_param = $_POST['statis'];

            $json_param = stripslashes($json_param);
            $arr_param = json_decode($json_param, true);
            $strCyid   = isset($arr_param['cyid'])?$arr_param['cyid']:'';
            $nVerCode   = (int)(isset($arr_param['versionCode'])?$arr_param['versionCode']:0);
        }

        //过滤旧版本
        if ($nVerCode < 44 || $nVerCode >= 80) {
            return true;
        }

        //过滤记录类型
        if ($this->nModuleType != COOLXIU_TYPE_THEMES
            && $this->nModuleType != COOLXIU_TYPE_FONT
            && $this->nModuleType != COOLXIU_TYPE_SCENE
            && $this->nModuleType != COOLXIU_TYPE_LIVE_WALLPAPER) {
            return true;
        }

        $erDb = new ExorderRecordDb();
        $bRet = $erDb->checkFreeRecord($this->nModuleType, $this->strId, $this->strCpid, $strCyid);
        if ($bRet === true) {
            return true;
        }

        if (! empty($strCyid)) {
        	//cpid和identity为空或为0时，不上报
        	if (empty($this->strId) || empty($this->strCpid)) {
        		return true;
        	}
            $erDb->saveChargeRecord('', $strCyid, $this->nModuleType, $this->strId, $this->strCpid);
        } else {
            Log::write("cyid is null, no record download", "debug");
        }
    }
	
}