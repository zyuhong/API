<?php
require_once 'public/public.php';

abstract class Record
{
	public $product;		//机型名称
// 	public $height;			//分辨率高
// 	public $width;			//分辨率宽
	public $cyid;			//下载者的酷云名称
	public $imsi;			//手机串号
	public $meid;			//手机串号
	public $vercode;		//应用版本
	public $kernel;			//主题内核
	public $cooltype;		//模块分类
	public $type;			//分类
	public $subtype;		//子分类
	public $channel;		//渠道
	public $ip;				//IP地址
	public $net;			//网络类型
	public $prover;			//协议版本
    public $oversea;        //海外
    public $language;       //语言
    public $coolshowchannel;  //渠道
    public $outerVersion;     //外部版本
    public $innerVersion;       //内部版本
    public $uiVersion;          //UI版本
    public $from_banner;       //banner来源
    public $banner_id;        //banner id
	public $insert_time;	//时间

    public function __construct()
    {
        $this->product 	= '';
        //$this->height 	= 0;
        //$this->width 	= 0;
        $this->cyid		= '';
        $this->imsi 	= '';
        $this->meid 	= '';
        $this->vercode 	= 0;
        $this->kernel 	= 0;
        $this->type 	= 0;
        $this->cooltype = 0;
        $this->subtype 	= 0;
        $this->channel 	= 0;
        $this->ip 		= '';
        $this->net 		= '';
        $this->prover	= 0;
        $this->oversea = '';
        $this->language = '';
        $this->coolshowchannel = '';
        $this->outerVersion = '';
        $this->innerVersion = '';
        $this->uiVersion = '';
        $this->from_banner = 0;
        $this->banner_id = '';
        $this->insert_time = date('Y-m-d H:i:s');
    }

    public function setCoolType($nCoolType)
    {
        $this->cooltype = (int)$nCoolType;
    }

    protected function setParam()
    {
        $this->vercode	= (int)(isset($_GET['versionCode']) ? $_GET['versionCode'] : 0);
        if (isset($_POST['statis'])) {
            $json_param = isset($_POST['statis']) ? $_POST['statis'] : '';
            $json_param = stripslashes($json_param);
            $arr_param = json_decode($json_param, true);

            $this->product	= isset($arr_param['product']) ? $arr_param['product'] : 'CoolUI';
            $this->cyid 	= isset($arr_param['cyid']) ? $arr_param['cyid'] : '';
            $this->imsi 	= isset($arr_param['imsi']) ? $arr_param['imsi'] : '';
            $this->meid 	= isset($arr_param['meid']) ? $arr_param['meid'] : '';
            $this->vercode	= isset($arr_param['versionCode']) ? $arr_param['versionCode'] : $this->vercode;
            //$this->width 	= isset($arr_param['width'])?$arr_param['width']:0;
            //$this->height 	= isset($arr_param['height'])?$arr_param['height']:0;
            $this->net 		= isset($arr_param['network']) ? $arr_param['network'] : 'net';
            $this->prover = (int)(isset($arr_param['protocolCode']) ? $arr_param['protocolCode'] : 0);
            $this->oversea = isset($arr_param['oversea']) ? $arr_param['oversea'] : '';
            $this->language = isset($arr_param['language']) ? $arr_param['language'] : '';
            $this->coolshowchannel = isset($arr_param['coolshowchannel']) ? $arr_param['coolshowchannel'] : '';
            $this->outerVersion = isset($arr_param['outerVersion']) ? $arr_param['outerVersion'] : '';
            $this->innerVersion = isset($arr_param['innerVersion']) ? $arr_param['innerVersion'] : '';
            $this->uiVersion = isset($arr_param['uiVersion']) ? $arr_param['uiVersion'] : '';
        }

        $this->kernel	= (int)(isset($_GET['kernelCode']) ? $_GET['kernelCode'] : 0);
        $this->from_banner = (int)(isset($_GET['from_banner']) ? $_GET['from_banner'] : 0);
        $this->banner_id = isset($_GET['banner_id']) ? $_GET['banner_id'] : '';
        $this->ip = isset($_SERVER['HTTP_X_REAL_IP']) ? $_SERVER['HTTP_X_REAL_IP'] : $_SERVER['REMOTE_ADDR'];
    }
	
	public function checkParam()
	{	
		$this->product 	= sql_check_str($this->product, 30);
		$this->cyid 	= sql_check_str($this->cyid, 50);
		$this->imsi 	= sql_check_str($this->imsi, 50);
		$this->meid 	= sql_check_str($this->meid, 50);
		$this->net 		= sql_check_str($this->net, 20);
	}
	
	abstract function setRecord();
}