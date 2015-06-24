<?php
require_once 'lib/WriteLog.lib.php';
require_once 'tasks/charge/ChargeSql.sql.php';

class ChargeRecord
{
	public $strCpid;			//商户单号
	public $strExorderNo;		//外部订单号
	public $strTransid; 		//交易流水号
	public $strAppid;			//应用编号	//应用ID
	public $strWaresid;			//商品编号	//商户ID
	public $strChargePoint;		//计费点编号
	public $nFeeType;			//计费类型  0:开发价格 1：免费 2：按次；3包自然时长4：包账期5：买断；6：包次数7：按时长
								//8：包活跃时长 9：批量购买 100：按次免费试用 101：按时长免费试用
	public $nMoney;				//交易金额，单位：分
	public $nCount;				//本次交易数量
	public $nResult;			//本次交易结果 0 交易成功，1：交易失败  //订单状态	是	200：成功；202：失败
	public $nTransType;			//交易类型， 0：交易 1：冲正
	public $strTransTime;		//交易时间	//订单日期	是	yyyy-MM-dd HH:mm:ss 
	
	#新版本增加字段
	public $strAppName;			//应用名称
	public $strMerName;			//商户名
	public $strChargePointName;	//计费点
	public $strPayType;			//支付方式ID	是	110000000000  短代110000000002  金融	
	public $strPayTypeName;		//支付方式	是	短代 游戏代
	public $strPhone;			//手机号
	public $strOperators;		//所属运营商
	
	public $strSign;			//消息签名
	
	public $insert_time;
	
	public function __construct()
	{
		$this->strCpid			= '';
		$this->strExorderNo		= '';
		$this->strTransid		= '';
		$this->strAppid			= '';
		$this->strWaresid		= '';
		$this->strChargePoint	= '';
		$this->nFeeType			= 0;
		$this->nMoney			= 0;
		$this->nCount			= 1;
		$this->nResult			= 0;
		$this->nTransType		= 0;
		$this->strTransTime		= '';
		$this->strSign			= '';
		$this->insert_time		= date("Y-m-d H:i:s");
	}
	
	public function checkChargeRecord()
	{
		if(empty($this->strExorderNo)){
			Log::write('ChargeRecord::CheckChargeRecord(): exorderno is empty', 'error');
			return false;
		}
		if(empty($this->strTransid)){
			Log::write('ChargeRecord::CheckChargeRecord(): transid is empty', 'error');
			return false;
		}
		if(empty($this->strWaresid)){
			Log::write('ChargeRecord::CheckChargeRecord(): waresid is empty', 'error');
			return false;
		}
		return true;
	}
	
	public function setChargeRecord($arrChargeRecord)
	{
		$this->strCpid			=  isset($arrChargeRecord['cpid'])?$arrChargeRecord['cpid']:'';
		$this->strExorderNo		=  isset($arrChargeRecord['exorderno'])?$arrChargeRecord['exorderno']:'';
		$this->strTransid		=  isset($arrChargeRecord['transid'])?$arrChargeRecord['transid']:'';
		$this->strAppid			=  isset($arrChargeRecord['appid'])?$arrChargeRecord['appid']:'';
		$this->strWaresid		=  isset($arrChargeRecord['waresid'])?$arrChargeRecord['waresid']:'';
		$this->strChargePoint	=  isset($arrChargeRecord['changepoint'])?$arrChargeRecord['changepoint']:'';
		$this->nFeeType			=  isset($arrChargeRecord['feetype'])?$arrChargeRecord['feetype']:0;
		$this->nMoney			=  isset($arrChargeRecord['money'])?$arrChargeRecord['money']:0;
		$this->nCount			=  isset($arrChargeRecord['count'])?$arrChargeRecord['count']:0;
		$this->nResult			=  isset($arrChargeRecord['result'])?$arrChargeRecord['result']:1;
		$this->nTransType		=  isset($arrChargeRecord['transtype'])?$arrChargeRecord['transtype']:0;
		$this->strTransTime		=  isset($arrChargeRecord['transtime'])?$arrChargeRecord['transtime']:'';
	}
	
	public function setNChargeRecord($arrChargeRecord)
	{
		$this->strExorderNo		=  isset($arrChargeRecord['reqOrderId'])?$arrChargeRecord['reqOrderId']:'';
		$this->strTransid		=  isset($arrChargeRecord['orderid'])?$arrChargeRecord['orderid']:'';
		$this->strAppid			=  isset($arrChargeRecord['appid'])?$arrChargeRecord['appid']:'';
		$this->strAppName		=  isset($arrChargeRecord['appname'])?$arrChargeRecord['appname']:'';
		$this->strWaresid		=  isset($arrChargeRecord['merid'])?$arrChargeRecord['merid']:'';
		$this->strMerName		=  isset($arrChargeRecord['mername'])?$arrChargeRecord['mername']:'';
		$this->strChargePoint	=  isset($arrChargeRecord['changepoint'])?$arrChargeRecord['changepoint']:'';
		$this->strChargePointName =  isset($arrChargeRecord['chargepointname'])?$arrChargeRecord['chargepointname']:'';
		$nMoney 				=  isset($arrChargeRecord['amt'])?$arrChargeRecord['amt']:0;
		$this->nMoney 			=  $nMoney * 100;//统一为分		
		$this->nResult			=  isset($arrChargeRecord['ordersatus'])?$arrChargeRecord['ordersatus']:200;
		$this->strTransTime		=  isset($arrChargeRecord['orderdate'])?$arrChargeRecord['orderdate']:'';
		$this->strPayType		=  isset($arrChargeRecord['paytype'])?$arrChargeRecord['paytype']:0;
		$this->strPayTypeName	=  isset($arrChargeRecord['paytypename'])?$arrChargeRecord['paytypename']:0;
		$this->strPhone			=  isset($arrChargeRecord['phone'])?$arrChargeRecord['phone']:0;
		$this->strOperators		=  isset($arrChargeRecord['operators'])?$arrChargeRecord['operators']:0;
// 		$this->strSign			=  isset($arrChargeRecord['operators'])?$arrChargeRecord['operators']:0;
	}
	
	public function setSign($strSign)
	{
		$this->strSign	= $strSign;
	}
	
	public function getInsertSql()
	{
		$sql = sprintf(YL_SQL_INSERT_CHARGE_RECORD,  $this->strExorderNo,
													 $this->strTransid,
													 $this->strAppid,
													 $this->strWaresid,
				 									 $this->strChargePoint,
													 $this->nFeeType,
													 $this->nMoney,
													 $this->nCount,
													 $this->nResult,
													 $this->nTransType,
													 $this->strTransTime,
													 $this->strSign,
													 $this->insert_time);
		return $sql;
	}
	
	public function getNInsertSql()
	{
		$sql = sprintf(YL_SQL_INSERT_N_CHARGE_RECORD,  $this->strExorderNo,
				$this->strTransid,
				$this->strAppid,
				$this->strAppName,
				$this->strWaresid,
				$this->strMerName,
				
				$this->strChargePoint,
				$this->strChargePointName,
				$this->nMoney,
				$this->nCount,
				$this->nResult,
				$this->strTransTime,
				
				$this->strPayType,
				$this->strPayTypeName,
				$this->strPhone,
				$this->strOperators,
				
				$this->strSign,
				$this->insert_time);
		return $sql;
	}
}