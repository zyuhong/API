<?php

class Score
{
	private $_nCoolType;		//酷秀模块类型
	public $id;				//资源ID
	public $cpid;			//资源ID
	public $cyid;			//酷云ID
	public $nickName;		//昵称 
	public $score;			//评分
	public $comment;		//评论
	public $product;		//机型
	public $meid;			//终端串号
	public $imsi;			//终端串号
	public $vercode;		//终端串号
	
	public function __construct()
	{
		$this->_nCoolType = 0;
		$this->id		= '';
		$this->cpid 	= '';
		$this->cyid 	= '';
		$this->nickName	= '';
		$this->score 	= 0;
		$this->comment 	= '';
		$this->insert_time = date("Y-m-d H:i:s");
		$this->product	= '';
		$this->meid		= '';
		$this->imsi		= '';
		$this->vercode	= '';
	}
	
	public function getType()
	{
		return $this->_nCoolType;	
	}

	public function setScore()
	{
//		$nCommType= isset($_GET['commType'])?$_GET['commType']:0;		//评论类型：0：全部评论 1：系统bug

		$nCoolType  = 0;
		$strId     	= '';
		$strCpid   	= '';
		$strCyid   	= '';
		$nickName   = '';
		$nScore  	= 0;
		$strComment = '';
		$strProduct	= '';
		$strMeid	= '';
		$strImsi	= '';
		$strVercode	= '';

		if(isset($_POST['score'])){
			$json_param = isset($_POST['score'])?$_POST['score']:'';
			$json_param = stripslashes($json_param);
			$arr_param = json_decode($json_param, true);

			$nCoolType  = (int)(isset($arr_param['type'])?$arr_param['type']:0);
			$strId     	= isset($arr_param['id'])?$arr_param['id']:'';
			$strCpid   	= isset($arr_param['cpid'])?$arr_param['cpid']:'';
			$strCyid   	= isset($arr_param['cyid'])?$arr_param['cyid']:'';
			$nickName   = isset($arr_param['nickName'])?$arr_param['nickName']:'';
			$nScore  	= (int)(isset($arr_param['score'])?$arr_param['score']:0); 
			$strComment = isset($arr_param['commet'])?$arr_param['commet']:''; 
			$strProduct	= isset($arr_param['product'])?$arr_param['product']:'';
			$strMeid	= isset($arr_param['meid'])?$arr_param['meid']:'';
			$strImsi	= isset($arr_param['imsi'])?$arr_param['imsi']:'';
			$strVercode	= isset($arr_param['versionCode'])?$arr_param['versionCode']:'';
		}else{
			$nCoolType	= (int)(isset($_GET['type'])?$_GET['type']:0);
			$strId     	= isset($_GET['id'])?$_GET['id']:'';
			$strCpid   	= isset($_GET['cpid'])?$_GET['cpid']:'';
			$strCyid   	= isset($_GET['cyid'])?$_GET['cyid']:'';
			$nickName   = isset($_GET['nickName'])?$_GET['nickName']:'';
			$nScore  	= (int)(isset($_GET['score'])?$_GET['score']:0); //分数
			$strComment = isset($_GET['commet'])?$_GET['commet']:''; //评论
		}
		$this->_nCoolType = $nCoolType;
		$this->id		= $strId;
		$this->cpid 	= $strCpid;
		$this->cyid 	= $strCyid;
		$this->nickName	= $nickName;
		$this->score 	= $nScore;
		$this->comment 	= $strComment;
		$this->product	= $strProduct;
		$this->meid		= $strMeid;
		$this->imsi		= $strImsi;
		$this->vercode	= $strVercode;
	}
}