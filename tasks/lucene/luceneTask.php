<?php
require_once 'public/public.php';
require_once 'tasks/lucene/lucene.class.php';

class luceneTask
{
	//const URL = 'http://172.16.45.67:8080';
	const URL = 'http://192.168.30.101:8080';
	
	public function __construct()
	{
		
	}
	
	public function getLuceneResult($subType, $keyWord, $nLimit)
	{
		$datas = 'keyword='.$keyWord.'&subtype='.$subType.'&numreq='.$nLimit;
		if (empty($subType)){
			$datas = 'keyword='.$keyWord.'&numreq='.$nLimit;
		}
		
		$jsonResult = get_respond_by_url(self::URL, $datas);
		
		$arrResult = json_decode($jsonResult);
		
		$arrCoolshow = array();
		foreach ($arrResult as $row){
			$lucene = new Lucene();
			$lucene->setParam($row);
			if(array_key_exists($row->subtype, $arrCoolshow)){
				array_push($arrCoolshow[$row->subtype], $lucene);
			}else {
				$arrCoolshow[$row->subtype] = array($lucene);
			}
		}
		
		
		$arrSearch = array("result"=>true, "coolshow"=>$arrCoolshow );
		
		return json_encode($arrSearch);
	}
	
}