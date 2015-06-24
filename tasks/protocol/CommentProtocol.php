<?php

class CommentProtocol
{
	public $cyid;			//酷云ID
	public $nickName;		//昵称 
	public $score;			//评分
	public $comment;		//评论
	public $product;		//机型
	public $creattime;		//评论时间
	
	public function __construct(){
		$this->cyid 	= '';
		$this->nickName	= '';
		$this->score 	= 0;
		$this->comment 	= '';
		$this->product	= '';		
		$this->creattime = '';
	}

	public function setComment($row){
		$this->cyid 	= isset($row['cyid'])?$row['cyid']:'';
		$this->nickName	= isset($row['nickName'])?$row['nickName']:'';
		$this->score 	= (int)(isset($row['score'])?$row['score']:0);
		$this->comment 	= isset($row['comment'])?$row['comment']:'';
		$this->product	= isset($row['product'])?$row['product']:'';
		$this->creattime = isset($row['insert_time'])?$row['insert_time']:'';
	}
}