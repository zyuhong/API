<?php

require_once 'tasks/label/Label.class.php';
require_once 'tasks/protocol/LabelProtocol.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/MemDb.lib.php';

class LabelDb extends DBManager
{
	private $_label;
	public function __construct()
	{
		$this->_label = new Label();
		global $g_arr_db_config;
		$this->connectMySqlPara($g_arr_db_config['coolshow']);
		global $g_arr_memcache_config;
		$this->_memcached = new MemDb();
		$this->_memcached->connectMemcached($g_arr_memcache_config);
		
	}
	
	public function setLabelParam($nType, $nSubtype, $nWidth, $nHeight)
	{
		$this->_label->setLabelParam($nType, $nSubtype, $nWidth, $nHeight);
	}
	
	private function _getLabelList($rows)
	{
		$arrLabel = array();
		$i = 0;
		foreach ($rows as $row){
			$label = $this->_label->getProtocol();//new LabelProtocol();
			$label->setProtocol($row);
			$label->setIndex($i);
			++$i;
			array_push($arrLabel, $label);
		}
		return $arrLabel;
	}
	
	public function searchLabel($start, $limit)
	{
		try{				
			$sql = $this->_label->getSelectLabelSql($start, $limit);
			$protocol = (int)(isset($_GET['protocolCode'])?$_GET['protocolCode']:0);
			$result = $this->_memcached->getSearchResult($sql.$protocol);
			if($result){
				return json_encode($result);
			}

			$rows = $this->executeQuery($sql);
			if($rows === false){
				Log::write('LabelDb::searchLabel() SQL:'.$sql.' failed', 'log');
				$result = get_rsp_result(false, 'get label sql failed');
				return $result;
			}
			
			$arrLabel = $this->_getLabelList($rows);
			$nCount = $this->getQueryCount();
			
			$result = array('result'=>true,
							'count' => $nCount,
							'label' => $arrLabel);
			
			$bResult = $this->_memcached->setSearchResult($sql.$protocol, $result, 3600);
			if(!$bResult){
				Log::write("LabelDb::searchLabel failed", "log");
			}
		}catch(Exception $e){
			Log::write("LabelDb::searchLabel()exception error:".$e->getMessage(), "log");
			return false;
		}		
		return json_encode($result);
	}
}