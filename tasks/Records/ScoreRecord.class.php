<?php
require_once 'lib/WriteLog.lib.php';
require_once 'configs/config.php';
require_once 'public/public.php';
require_once 'tasks/Records/MongoRecord.class.php';
require_once 'tasks/Records/Score.class.php';
require_once 'tasks/protocol/CommentProtocol.php';

class ScoreRecord extends MongoRecord
{
	public function __construct()
	{
		parent::__construct();
		$this->_collection = 'cl_yl_score_record';
	}
	
	public function saveRecord($nCoolType, Record $record)
	{
		
	}
	
	public function saveScore()
	{
		try {
			$score = new Score();
			$score->setScore();
			
			$nCoolType = $score->getType();
			
			$this->_setScoreDatabase($nCoolType);
			$this->_setScoreCollection($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('ScoreRecord::saveRecord():connect() failed', 'log');
				return false;
			}
			
			$this->addIndex(array('insert_time'=>-1, 'id'=>1, 'cpid'=>1, 'cyid'=>1,
							      'cpcy'=>array('cpid'=>1, 'cyid'=>1)));
			$result = $this->_mongo->insert($this->_collection, object_to_array($score));
			
			if($result === false){
				Log::write('ScoreRecord::saveRecord():insert() failed', 'log');
				return false;
			}
			return true;
				
		}catch (Exception $e){
			Log::write('ScoreRecord::saveRecord() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}

	private  function _setScoreCollection($type)
	{
		$strCollection = 'db_yl_score_record';
		switch($type){
			case COOLXIU_TYPE_WALLPAPER:
			case COOLXIU_TYPE_ANDROIDESK_WALLPAPER:{
				$strCollection = 'cl_yl_score_wp_record';
			}break;
			case COOLXIU_TYPE_THEMES:{
				$strCollection = 'cl_yl_score_theme_record';
			}break;
			case COOLXIU_TYPE_FONT:{
				$strCollection = 'cl_yl_score_font_record';
			}break;
			case COOLXIU_TYPE_RING:{
				$strCollection = 'cl_yl_score_ring_record';
			}break;
			case COOLXIU_TYPE_WIDGET:{
				$strCollection = 'cl_yl_score_widget_record';
			}break;
			case COOLXIU_TYPE_SCENE:{
				$strCollection = 'cl_yl_score_scene_record';
			}break;
			default:return false;
		}
		$this->_collection = $strCollection;
		return true;
	}
	
	public function searchCpidRecord($nCoolType, $strCpid, $limit, $skip)
	{
		try {
			$this->_setScoreDatabase($nCoolType);
			$this->_setScoreCollection($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('ScoreRecord::saveRecord():connect() failed', 'log');
				return false;
			}
			
			$result = $this->_mongo->select($this->_collection, 
								  array('cpid'=>$strCpid), 
								  array(), array('insert_time'=>-1), 
								  $limit, $skip);
			if(!is_array($result)){
				return false;
			}
			$nCount = $this->_mongo->count($this->_collection, array('cpid'=>$strCpid));

			$arrComment = array();
			foreach ($result as $row ){
				$comment =  new CommentProtocol();
				$comment->setComment($row);
				array_push($arrComment, $comment);
			}
			
			$result = array('result'=>true,
							'count'=> count($arrComment),
							'total'=>(int)$nCount,
							'comments'=>$arrComment);
			
			return json_encode($result);
		}catch (Exception $e){
			Log::write('ScoreRecord::searchCpidRecord() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
	
	public function searchCpidScore($nCoolType, $strCpid)
	{
		try {
			$this->_setScoreDatabase($nCoolType);
			$this->_setScoreCollection($nCoolType);
			$result = $this->connect();
			if(!$result){
				Log::write('ScoreRecord::searchCpidScore():connect() failed', 'log');
				return false;
			}
			$keys = array('score' => 1);
			$initial = array('count' => 0);
			$reduce = "function (obj, prev) { prev.count++; }";
			
			$result = $this->_mongo->group($this->_collection, $keys, $initial, $reduce);
			if(!is_array($result)){
				return false;
			}
		
			return $result;
			
		}catch (Exception $e){
			Log::write('ScoreRecord::searchCpidScore() exception, mongErr:'.$this->_mongo->getError()
					.' err:'
					.' file:'.$e->getFile()
					.' line:'.$e->getLine()
					.' message:'.$e->getMessage()
					.' trace:'.$e->getTraceAsString(), 'log');
		}
		return false;
	}
}