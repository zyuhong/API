<?php
abstract class Protocol
{
	const YL_DOWNLOAD_CHANNEL_COOLSHOW 	= 0;	//下载通道酷派秀
	const YL_DOWNLOAD_CHANNEL_WIDGET 	= 1;	//下载通道WIDGET
		
	protected  $_product;		//产品
	protected  $_vercode;		//当前版本号
	protected  $_nKernelCode;	//模块内核版本
	
	public $id;					//ID下载及下载统计用
	public $cpid;				//资源ID
	protected  $_userid;		//主题作者ID
	public $cyid;				//主题作者ID
	public $author;				//主题作者
	public $type;				//类型
	public $download_times;		//下载次数
// 	public $tag;				//分组标签（默认xxxx年xx月xx日）
	
	public $chargePointNew;		//新版计费点ID
	public $chargePointName;	//新版计费点名称
	
	public $chargePoint;		//计费点
	public $price;				//价格(单位为分)
	public $exOrderNo;			//外部订单号，规则可以根据自己需要随意定义，长度不超过50，无特殊字符
	public $productId;			//旧商品ID 新的应用 编号 
	public $waresId;			//新商品ID
	public $appKey;				//商品密钥
	public $appRespPkey;		//支付公钥
	public $appModKey;			//MODKEY
	public $isCharge;			//是否收费
	public $channel;			//下载渠道
	
	public $ruleid;				//消耗积分规则ID
	public $score;				//积分分值
	public $incruleid;			//积累积分规则ID
	public $incscore;			//积分分值

    public  $corner_mark;   //角标地址
    public  $mark_gravity;  //角标位置
    public $price_tag;      //价格标签
	
	public function __construct(){
		$this->_product 			= '';	
		$this->_vercode				= 0;
		$this->_nKernelCode			= 1;
		
		$this->cpid					= '';
		$this->_userid				= '';
		$this->cyid					= '';
		
		$this->type					= 0;
		$this->download_times		= 0;
		$this->tag					= '';
		
		$this->chargePointNew		= '';
		$this->chargePointName		= '';
		
		$this->chargePoint			= 1;
		$this->price				= 0;
		$this->exOrderNo			= '';
		$this->productId			= '';
		$this->waresId				= '';
		$this->appKey				= '';
		$this->appRespPkey			= '';
		$this->appModKey			= '';
		$this->isCharge				= false;
		$this->channel				= 0;
		
		$this->ruleid				= '';
		$this->incruleid			= '';
		$this->score				= 0;
		$this->incscore				= 0;

        $this->corner_mark = '';
        $this->mark_gravity = 0;
        $this->price_tag = '';
	}
	
	public function getUserid()
	{
		return $this->_userid;
	}
	
	public function setVercode($vercode)
	{
		$this->_vercode	= $vercode;
	}

	public function setKernelCode($nKernelCode)
	{
		$this->_nKernelCode = $nKernelCode;
	}
	
	protected  function setCommonParam($row, $channel = 0)
	{
		$this->author 				= isset($row['author'])?$row['author']:'CoolUI';
		$this->_userid 				= isset($row['userid'])?$row['userid']:'CoolUI';
		$this->cyid 				= isset($row['userid'])?$row['userid']:'coolpad';
		
		$bCharge = false;
		$bCharge 					= isset($row['ischarge'])?$row['ischarge']:false;
		$this->isCharge				= $bCharge?true:false;
		$this->download_times 		=  (int)isset($row['download_times'])?$row['download_times']:0;
		if($bCharge){
			$this->download_times 		+= ((int)($this->id)) %1000; //rand(1000, 10000);
		}else{
			$this->download_times 		+= ((int)($this->id)) %10000; //rand(1000, 10000);
		}
		
// 		$date = isset($row['tdate'])?$row['tdate']:date('Y-m-d');
// 		if((strtotime(date('Y-m-d')) - strtotime($date)) > 30 * 24 * 60 * 60){
// 			$tag = '更早';
// 		}else{
// 			$tag = sprintf('%s月%s日', strftime('%m', strtotime($date)), strftime('%d', strtotime($date)));//sprintf('%s年%s月%s日', strftime('%Y', strtotime($date)), strftime('%m', strtotime($date)), strftime('%d', strtotime($date)));
// 		}
// 		$this->tag 					= $tag;
		
		$this->chargePointNew		= isset($row['chargepointnew'])?$row['chargepointnew']:'';
		$this->chargePointName		= isset($row['chargepointname'])?$row['chargepointname']:'';
		
		$this->chargePoint			= (int)isset($row['chargepoint'])?$row['chargepoint']:1;
		$this->price				= (int)isset($row['price'])?$row['price']:0;
		$this->exOrderNo			= isset($row['identity'])?$row['identity']:'';
		$this->productId			= isset($row['appid'])?$row['appid']:'';
		$this->waresId				= isset($row['waresid'])?$row['waresid']:'';
		$this->appKey				= isset($row['appkey'])?$row['appkey']:'';
		$this->appRespPkey			= isset($row['appresppley'])?$row['appresppley']:'';
		$this->appModKey			= isset($row['appmodkey'])?$row['appmodkey']:'';
		$this->channel				= $channel;
		
		$this->ruleid				= isset($row['ruleid'])?$row['ruleid']:'';
		$this->incruleid				= isset($row['incruleid'])?$row['incruleid']:'';
		$this->score				= (int)(isset($row['score'])?$row['score']:0);
		$this->incscore				= (int)(isset($row['incscore'])?$row['incscore']:0);
	}
	abstract function setProtocol($row, $nChannel = 0);
}