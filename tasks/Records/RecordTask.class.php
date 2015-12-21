<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';

require_once 'tasks/Records/Request.class.php';
require_once 'tasks/Records/RequestRecord.class.php';

require_once 'tasks/Records/Browse.class.php';
require_once 'tasks/Records/BrowseRecord.class.php';

require_once 'tasks/Records/Banner.class.php';
require_once 'tasks/Records/BannerRecord.class.php';

require_once 'tasks/Records/Albums.class.php';
require_once 'tasks/Records/AlbumsRecord.class.php';

require_once 'tasks/Records/DownloadCount.class.php';
require_once 'tasks/Records/DownloadOrder.class.php';
require_once 'tasks/Records/DownloadRecord.class.php';

require_once 'tasks/Records/Apply.class.php';
require_once 'tasks/Records/ApplyRecord.class.php';
		
require_once 'tasks/Records/Widgets.class.php';
require_once 'tasks/Records/WidgetsRecord.class.php';

require_once 'tasks/Records/Setting.class.php';
require_once 'tasks/Records/SettingRecord.class.php';

require_once 'tasks/Records/LuceneSearch.class.php';
require_once 'tasks/Records/LuceneRecord.class.php';

require_once 'tasks/Records/QueueTask.class.php';

class RecordTask
{
	const YL_COOLSHOW_OPTYPE_REQ = 0;
	const YL_COOLSHOW_OPTYPE_BROWSER = 1;
	const YL_COOLSHOW_OPTYPE_DOWNLOAD = 2;
	const YL_COOLSHOW_OPTYPE_APPLY = 3;
	
	public function __construct()
	{
		
	}
	public function saveWebRequest($nCoolType)
	{
		$record = new RequestRecord();
	
		$req = new Request();
		$req->setRecord();
		$req->setCoolType($nCoolType);
	
		$result = $record->saveWebRecord($nCoolType, $req);
		if(!$result){
			Log::write('RecordTask::saverRequest():saveRecord() failed', 'log');
			// 			return false;
		}
	
		$record->close();
	
		return true;
	}
	
	public function saveXRequest($nCoolType)
	{
  		$record = new RequestRecord();
		
		$req = new Request();
		$req->setRecord();
		$req->setCoolType($nCoolType);
		
		$result = $record->saveRecord($nCoolType, $req);
		if(!$result){
			Log::write('RecordTask::saverRequest():saveRecord() failed', 'log');
// 			return false;
		}

		$record->close();
	}
	
	public function saveRequest($nCoolType)
	{
		try {
	// 		$record = new RequestRecord();
			
			$req = new Request();
			$req->setRecord();
			
	// 		$result = $record->saveRecord($nCoolType, $req);
	// 		if(!$result){
	// 			Log::write('RecordTask::saverRequest():saveRecord() failed', 'log');
	// // 			return false;
	// 		}
			
	// 		$record->close();
			$mystatis = (array)$req;
			$mystatis['optype'] = 'request';
			Log::appendJson($mystatis, "mstatis");
	  		$queue = new QueueTask();
	  		$queue->push('request', $nCoolType, json_encode($req), 'coolshow_req_count');
  		}catch (Exception $e){
			Log::write('RecordTask::saverRequest():QueueTask():push() failed', 'log');
		}
		return true;
	}
	
	public function saveBrowse($nCoolType)
	{
		try {
	// 		$record = new BrowseRecord();
		
			$br = new Browse();
			$br->setRecord();
		
	// 		$result = $record->saveRecord($nCoolType, $br);
	// 		if(!$result){
	// 			Log::write('RecordTask::saverBrowse():saveRecord() failed', 'log');
	// // 			return false;
	// 		}
	
	// 		$record->close();
	// 		Log::write('RecordTask::saverRequest():saveBrowse()', 'debug');
			$mystatis = (array)$br;
			$mystatis['optype'] = 'browse';
			Log::appendJson($mystatis, "mstatis");
			$queue = new QueueTask();
			$queue->push('browse', $nCoolType, json_encode($br), 'coolshow_browse_count');
		}catch (Exception $e){
			Log::write('RecordTask::saveBrowse():QueueTask():push() failed', 'log');
		}
		return true;
	}
	
	public function saveAdBrowse($nCoolType)
	{
		try {
			$record = new BrowseRecord();
	
			$br = new Browse();
			$br->setRecord();
	
			$result = $record->saveRecord($nCoolType, $br);
			if(!$result){
				Log::write('RecordTask::saveAdBrowse():saveRecord() failed', 'log');
// 				return false;
			}
	
			$record->close();
			
		}catch (Exception $e){
			Log::write('RecordTask::saveAdBrowse():QueueTask():push() failed', 'log');
		}
		return true;
	}
	/**
	 * 下载记录
	 * @param unknown_type $nCoolType
	 * @return boolean
	 */
	public function saveDownload($nCoolType)
	{
		$record = new DownloadRecord();
		
		$dl = new Download();
		$dl->setRecord();
// 		Log::write('RecordTask::saveDownload() cpid:'.$dl->cpid.', channer:'.$dl->channel, 'error');
		
		$result = $record->saveRecord($nCoolType, $dl);
		if(!$result){
			Log::write('RecordTask::saveDownload():saveRecord() failed', 'log');
// 			return false;
		}
		
// 		$record = new DownloadRecord();
// 		$dlCount = new DownloadCount();
// 		$dlCount->setRecord();
		
// 		$result = $record->saveCountRecord($nCoolType, $dlCount);
// 		if(!$result){
// 			Log::write('RecordTask::saveDownload():saveCountRecord() failed', 'log');
// 			return false;
// 		}

// 		$record->close();
		$mystatis = (array)$dl;
		$mystatis['optype'] = 'download';
		Log::appendJson($mystatis, "mstatis");
		$queue = new QueueTask();
		$queue->push('dl', $nCoolType, json_encode($dl), 'coolshow_dl_count');
		
		return true;
	}
	/**
	 * 订单记录
	 * @param unknown_type $nCoolType
	 * @param unknown_type $strId
	 * @param unknown_type $cpid
	 * @param unknown_type $author
	 * @param unknown_type $type
	 * @param unknown_type $waresid
	 * @param unknown_type $money
	 * @param unknown_type $strExorder
	 * @return boolean
	 */
	public function saveOrder($nCoolType, $strId, $cpid, $ruleid, $score,
							  $name, $userid, $author, $type, 
							  $appid, $waresid, $money, $strExorder, $channel = 'yx')
	{
		$record = new DownloadRecord();
		
		$order = new DownloadOrder();
		$order->setRecord();
		$order->setCoolType($nCoolType);
		$order->setOrderParam($strId, $cpid, $ruleid, $score,
							  $name, $userid, $author, $type, 
							  $appid, $waresid, $money, $strExorder, $channel);
		
		$result = $record->saveOrderRecord($nCoolType, $order);
		if(!$result){
			Log::write('RecordTask::saveOrder():saveOrderRecord() failed', 'log');
// 			return false;
		}

		$record->close();
		return true;
	}
	
	public function updateOrder($nCoolType, $strExorder, $isScore)
	{
		$record = new DownloadRecord();
		
		$order = new DownloadOrder();
		$order->setOrder($strExorder, $isScore);
		$order->setCoolType($nCoolType);
		
		$result = $record->updateOrderRecord($nCoolType, $order);
		if(!$result){
			Log::write('RecordTask::updateOrder():updateOrderRecord() failed', 'log');
// 			return false;
		}

		$record->close();
		return true;
	}
	
	public function saveBanner($nCoolType)
	{
		$record = new BannerRecord();
		
		if ($nCoolType == COOLXIU_TYPE_SCENE_WALLPAPER){
			$nCoolType = COOLXIU_TYPE_ANDROIDESK_WALLPAPER;
		}
		
		$banner = new Banner();
		$banner->setRecord();
		
		$result = $record->saveRecord($nCoolType, $banner);
		if(!$result){
			Log::write('RecordTask::saveBanner():saveRecord() failed', 'log');
// 			return false;
		}

		$record->close();
		
		$queue = new QueueTask();
		$queue->push('banner', $nCoolType, json_encode($banner), 'coolshow_banner_count');
		return true;
	}
	
	public function saveAlbums($nCoolType)
	{
		$record = new AlbumsRecord();
	
		$albums = new Albums();
		$albums->setRecord();
	
		$result = $record->saveRecord($nCoolType, $albums);
		if(!$result){
			Log::write('RecordTask::saveAlbums():saveRecord() failed', 'log');
			// 			return false;
		}
	
		$record->close();
	
		$queue = new QueueTask();
		$queue->push('albums', $nCoolType, json_encode($albums), 'coolshow_albums_count');
		return true;
	}
	
	public function saveWidget($nCoolType)
	{
		$record = new WidgetsRecord();
	
		$widget = new Widgets();
		$widget->setRecord();
		$widget->setCoolType($nCoolType);
		
		$result = $record->saveRecord($nCoolType, $widget);
		if(!$result){
			Log::write('RecordTask::saveWidget():saveRecord() failed', 'log');
// 			return false;
		}

		$record->close();
		return true;
	}
	
	public function saveApply($nCoolType)
	{
		$record = new ApplyRecord();
	
		$apply  = new Apply();
		$apply->setRecord();
		
		$result = $record->saveRecord($nCoolType, $apply);
		if(!$result){
			Log::write('RecordTask::saveApply():saveRecord() failed', 'log');
// 			return false;
		}

		$record->close();
		
		$mystatis = (array)$apply;
		$mystatis['optype'] = 'apply';
		Log::appendJson($mystatis, "mstatis");
		$queue = new QueueTask();
		$queue->push('apply', $nCoolType, json_encode($apply), 'coolshow_apply_count');
		return true;
	}
	
	public function saveStaticRecord()
	{
		$strId 	 	 = isset($_GET['id'])?$_GET['id']:'';
		$strCpid 	 = isset($_GET['cpid'])?$_GET['cpid']:'';
		$nCoolType 	 = isset($_GET['moduletype'])?$_GET['moduletype']:'';
		$nOpType  	 = isset($_GET['optype'])?$_GET['optype']:-1;
		$nType 	   	 = (int)(isset($_GET['type'])?$_GET['type']:0);
		if ($nCoolType == COOLXIU_TYPE_RING){
			$nType = $nType - 1;
		}
		
		#ICON图标、通讯录都是主题模块的别名
		if($nCoolType == COOLXIU_TYPE_THEMES_CONTACT
		|| $nCoolType == COOLXIU_TYPE_THEMES_ICON){
			$nCoolType 	= COOLXIU_TYPE_THEMES;
		}
		if($nCoolType == COOLXIU_TYPE_LIVE_WALLPAPER){
			$nCoolType 	= COOLXIU_TYPE_SCENE;
		}
		
		$result = false;		
		switch ($nOpType){
			case self::YL_COOLSHOW_OPTYPE_REQ:
				if ($nCoolType == COOLXIU_TYPE_RING && $nType == 2){
					$result = $this->saveXRequest(COOLXIU_TYPE_X_RING);
				}else{
					$result = $this->saveRequest($nCoolType);
				}
				break;
			case self::YL_COOLSHOW_OPTYPE_BROWSER:
				$result = $this->saveBrowse($nCoolType);
				break;
			case self::YL_COOLSHOW_OPTYPE_DOWNLOAD:
				$result = $this->saveDownload($nCoolType);
				break;
			case self::YL_COOLSHOW_OPTYPE_APPLY:
				$result = $this->saveApply($nCoolType);
				break;
			case -1:
				Log::write('RecordTask::saveStaticRecord no module type failed', 'error');
				break;
		}
		return $result;
	}
	
	public function saveAdStaticRecord()
	{
		$strId 	 	 = isset($_GET['id'])?$_GET['id']:'';
		$nCoolType   = (int)(isset($_GET['type'])?$_GET['type']:0);//banner\adver
		$nOpType  	 = isset($_GET['optype'])?$_GET['optype']:-1;
		
		
		$nCoolType = 23 + $nCoolType;
		
		$result = false;
		switch ($nOpType){
			case self::YL_COOLSHOW_OPTYPE_REQ:
				$result = $this->saveAdBrowse($nCoolType);
				break;
			case self::YL_COOLSHOW_OPTYPE_BROWSER:
				$result = $this->saveApply($nCoolType);
				break;
			default:
				Log::write('RecordTask::saveStaticRecord no module type failed', 'error');
				break;
		}
		return $result;
	}

    public function saveSetting()
    {
        $record = new SettingRecord();

        $set = new Setting();
        $set->setRecord();
        //$set->setCoolType($nCoolType);
        $set->setCoolType(0);   //更新：2015-11-07

        $result = $record->saveRecord(0, $set);
        if(!$result){
            Log::write('RecordTask::saveSetting():saveRecord() failed', 'log');
            // 			return false;
        }

        $record->close();
        return true;
    }
	
	public function saveLucene($nCoolType)
	{
		$record = new LuceneRecord();
	
		$lucene = new LuceneSearch();
		$lucene->setRecord();
		$lucene->setCoolType($nCoolType);
	
		$result = $record->saveRecord($nCoolType, $lucene);
		if(!$result){
			Log::write('RecordTask::saveLucene():saveRecord() failed', 'log');
			// 			return false;
		}
	
		$record->close();
		return true;
	}
	
	public function saveMyrsc($nCoolType)
	{
		$record = new ApplyRecord();
	
		$apply  = new Apply();
		$apply->setRecord();
	
		$result = $record->saveRecord($nCoolType, $apply);
		if(!$result){
			Log::write('RecordTask::saveApply():saveRecord() failed', 'log');
			// 			return false;
		}
	
		$record->close();
	
		$queue = new QueueTask();
		$queue->push('apply', $nCoolType, json_encode($apply), 'coolshow_apply_count');
		return true;
	}
	
}