<?php
require_once 'tasks/Records/Download.class.php';
class DownloadOrder extends Download
{
// 	public $product;
// 	public $id;			//资源ID
// 	public $cpid;		//资源的公共ID
// 	public $author;		//作者
// 	public $cyid;		
// 	public $meid;
// 	public $insert_time;
	
	public $name;		//资源的名称
	public $userid;		//作者ID
	public $order;		//订单
	public $count;		//数量
	public $appid;		//应用编号
	public $waresid;	//商品编号
	public $money;		//金额
	public $transid;	//交易号
	public $feetype;	//计费类型
	public $result;		//交易结果
	public $transtype;	//交易类型
	public $transtime;	//交易时间
	public $status;		//付费状态
	public $ruleid;		//积分规则
	public $score;		//积分值
	public $isscore;	//是否积分消费
	public function __construct()
	{	
// 		$this->product	 = '';
// 		$this->id		 = '';
// 		$this->cpid		 = '';		
// 		$this->author	 = '';		
// 		$this->cyid		 = '';
// 		$this->meid		 = '';
		parent::__construct();
		$this->name		 = '';
		$this->userid	 = '';
		$this->order	 = '';
		$this->count	 = 1;
		$this->transid	 = '';
		$this->appid	 = '';
		$this->waresid	 = '';
		$this->feetype	 = 0;
		$this->money	 = 0;
		$this->result	 = 0;
		$this->transtype = 0;
		$this->transtime = '';
		$this->status	 = 0;
		$this->ruleid	 = '';
		$this->score	 = 0;
		$this->isscore	 = 0;
	}

	public function setOrder($strExorder, $isScore)
	{
		$this->order	 = $strExorder;
		$this->isscore  = $isScore;
		$this->transtime = date("Y-m-d H:i:s");
	}
	
	public function setOrderParam($strId, $cpid, $ruleid, $score, 
								  $name, $userid, $author, $type, 
								  $appid, $waresid, $money, $strExorder)
	{

		$this->name		= $name;
		$this->userid	= $userid;
		$this->id 		= $strId;
		$this->cpid 	= $cpid;
		$this->author	= $author;
		$this->type 	= (int)$type;
		$this->appid	= $appid;
		$this->waresid	= $waresid;
		$this->money	= (int)$money;
		$this->order	= $strExorder;
 		$this->count	= 1;

 		$this->ruleid	 = $ruleid;
 		$this->score	 = $score;
// 		$this->transid	= $strTransid;
// 		$this->feetype	= $nFeetype;
// 		$this->result	= $nResult;
// 		$this->transtype= $nTransType;
// 		$this->transtime= $strTransTime;
	}
}