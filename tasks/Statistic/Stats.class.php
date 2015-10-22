<?php
/**
 * 统计访问量类
*
* 表:
* CREATE TABLE `meng_stats` (
		* `type` char(16) NOT NULL,
		* `variable` char(20) NOT NULL,
		* `count` int(12) unsigned NOT NULL default '0',
		* PRIMARY KEY (`type`,`variable`)
		* ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*
* type:统计类型
* variable统计标识量
* count统计值
*
*/
require_once('lib/mySql.lib.php');
require_once('configs/config.php');
require_once ('Stats.sql.php');
class Stats {
	/**
	 * 统计需要用session
	 * 
	 */
	private $stats_conn;
	private $count;
	function __construct() {
		if (session_id() == '') {
			session_start();
		}
		global  $g_arr_db_conn;
		$this->$stats_conn = new mysql(
				$g_arr_db_conn['host'],
				$g_arr_db_conn['user'],
				$g_arr_db_conn['pwd'],
				$g_arr_db_conn['db'],
				$g_arr_db_conn['type'],
				$g_arr_db_conn['coding']);
	}

	/**
	 * 验证该统计是否已经存在
	 *
	 * @param string $type
	 * @param string $variable
	 * @return bool
	 */
	private function _verify($type, $variable) {
		$sql = sprintf(SQL_SELECT_COUNT_STATS, $type, $variable);
		$result = $this->stats_conn->commit_query($sql);
		if(!$result){
			Log::write("Stats::_verify()：commit_query() failed", "log");
			return false;
		}
		if ($this->stats_conn->db_num_rows() == 0){
			return false; // 该统计尚未存在
		}		
		$row = $this->stats_conn->fetch_assoc();
		$this->count = $row['count'];
		return true; // 该统计已经存在
	}
	
	/**
	 * 获取统计数
	 */
	public function getCount(){
		return $this->count;
	}

	/**
	 * 新建统计
	 *
	 * @param string $type 统计分类
	 * @param string $variable 统计标志变量
	 * @return bool
	 */
	private function _insertStats($type, $variable, $count) {
		$sql = sprintf(SQL_INSERT_STATS, $type, $variable, $count);
		$result = $this->stats_conn->commit_query($sql);
		if(!$result){
			Log::write("Stats::_verify()：commit_query() failed", "log");
			return false;
		}
		return true;
	}

	/**
	 * 获取统计信息
	 *
	 * @param string $type
	 * @param string $variable
	 * @return string
	 */
	public function get($type, $variable, $verify = true, $update = true){

		/* 验证是否添加一个统计 */
		if ($verify) {
			/* 如果还没有该统计,则添加 */
			if (!$this->_verify($type, $variable)) {
				$count = 0;
				$result = $this->_insertStats($type, $variable, $count);
				if(!$result){
					Log::write("Stats::get():insertStats() failed", "log");
					return false;
				}
			}
		}

		/* 更新统计 */
		if ($update) {
			$this->_update($type, $variable);
		}
		return $this->count;
	}

	/**
	 * 更新统计信息
	 *
	 * @param string $type
	 * @param string $variable
	 * @return bool
	 */
	private function _update($type, $variable) {
		/* 防刷新 */
		if (isset($_SESSION['stats_' . $type . $variable])){
			return true; // 函数运行完成,不更新数据库
		}
		
		$_SESSION['stats_' . $type . $variable] = true;
		
		/* 更新统计 */
		$count = $this->get($type, $variable) + 1;
		$sql = sprintf(SQL_UPDATE_STATS, $count, $type, $variable);
		$result = $this->stats_conn->commit_query($sql);
		
		if (!$result){
			Log::write("Stats::_update():commit_query() failed", "log");
			return false;
		}
		
		$this->count = $count;
		return false;
	}
	
	/**
	 * 移动应用好像不需要确定IP，先放着吧	 * 
	*/
	private function _getIp(){
		if (isset($_SERVER)){
			if (isset($_SERVER[HTTP_X_FORWARDED_FOR]) && strcasecmp($_SERVER[HTTP_X_FORWARDED_FOR], "unknown")){
				$realip = $_SERVER[HTTP_X_FORWARDED_FOR];
			}
			elseif(isset($_SERVER[HTTP_CLIENT_IP]) && strcasecmp($_SERVER[HTTP_CLIENT_IP], "unknown")){
				$realip = $_SERVER[HTTP_CLIENT_IP];
			}elseif(isset($_SERVER[REMOTE_ADDR]) && strcasecmp($_SERVER[REMOTE_ADDR], "unknown")){
				$realip = $_SERVER[REMOTE_ADDR];
			}else{
				return false;
			}
		}else{
			if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
				$realip = getenv("HTTP_X_FORWARDED_FOR");
			}elseif(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
				$realip = getenv("HTTP_CLIENT_IP");
			}elseif(getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
				$realip = getenv("REMOTE_ADDR");
			}else{
				return false;
			}
		}
		return $realip;
	}
	
	/**
	 * 以IP作为访问统计不现实，暂时搁置
	 */
	private function _modifyIpCount($ip){
		$query="SELECT * FROM ip where ipdata='".$ip."'";
		$result=mysql_query($query);
		$row=mysql_fetch_array($result);
		$iptime=time();
		$day=date('j');
		
		if(!$row){
			$query="INSERT INTO ip (ipdata,iptime) VALUES ('".$ip."','".$iptime."')";
			mysql_query($query);
			
			$query="SELECT day,todayipcount,allipcount FROM count";
			$result=mysql_query($query);
			$row=mysql_fetch_array($result);
			
			$allipcount=$row['allipcount']+1;
			$todayipcount=$row['todayipcount']+1;
			
			if($day==$row['day']){
				$query="UPDATE count SET allipcount='".$allipcount."',todayipcount='".$todayipcount."'";
			}else{
				$query="UPDATE count SET allipcount='".$allipcount."',day='".$day."',todayipcount='1'";
			}
			mysql_query($query);
		}else{
			$query="SELECT iptime FROM ip WHERE ipdata='".$ip."'";
			$result=mysql_query($query);
			$row=mysql_fetch_array($result);
			
			$query="SELECT day,todayipcount,allipcount FROM count";
			$result=mysql_query($query);
			$row1=mysql_fetch_array($result);
			
			if($iptime-$row['iptime']>86400){
				$query="UPDATE ip SET iptime='".$iptime."' WHERE ipdata='".$ip."'";
				mysql_query($query);
				$allipcount=$row1['allipcount']+1;
				if($day==$row1['day']){
					$query="UPDATE count SET allipcount='".$allipcount."'";
				}else{
					$query="UPDATE count SET allipcount='".$allipcount."',day='".$day."',todayipcount='1'";
				}
				mysql_query($query);
			}

			if($day!=$row1['day']){
				$query="UPDATE count SET day='".$day."',todayipcount='1'";
				mysql_query($query);
			}
		}
	}
}
?>