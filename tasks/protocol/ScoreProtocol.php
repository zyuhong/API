<?php

class ScoreProtocol
{
	public $overall;		//综合评分
	public $total;			//总人数
	public $star1;			//星级1
	public $star2;			//星级1
	public $star3;			//星级1
	public $star4;			//星级1
	public $star5;			//星级1
	
	public function __construct(){
		$this->overall 	= 0;
		$this->total	= 0;
		$this->star1	= 0;
		$this->star2	= 0;
		$this->star3	= 0;
		$this->star4	= 0;
		$this->star5	= 0;
	}

	public function setScore($nOverall, $nTotal, $nStar1, $nStar2, $nStar3, $nStar4, $nStar5){
		$this->overall 	= $nOverall;
		$this->total	= $nTotal;
		$this->star1	= $nStar1;
		$this->star2	= $nStar2;
		$this->star3	= $nStar3;
		$this->star4	= $nStar4;
		$this->star5	= $nStar5;
	}
	
}