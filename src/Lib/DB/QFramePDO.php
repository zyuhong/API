<?php

namespace Lib\DB;

class QFrameDBPDO
{
    private $_config         = array();
    var $_conn           = null;
    private $_fetch_type     = \PDO::FETCH_ASSOC;
    private $_debug          = false;
    private $_log            = null;
    private $_optimize       = false;
    private $_transaction    = false;
    private $_error_mode     = \PDO::ERRMODE_EXCEPTION;
    private $_reconnected    = false;
    private $_auto_reconnect = true;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    private function _connect()
    {/*{{{*/
        if($this->_conn == null) {
            if($this->_config["unix_socket"]) {
                $dsn = "mysql:dbname={$this->_config["database"]};unix_socket={$this->_config["unix_socket"]}";
            } else {
                $dsn = "{$this->_config["driver"]}:dbname={$this->_config["database"]};host={$this->_config["host"]};port={$this->_config["port"]}";
            }

            $username   = $this->_config["username"];
            $password   = $this->_config["password"];
            $options    = array_unique(array_merge([\PDO::ATTR_PERSISTENT=>$this->_config["persistent"]], $this->_config["options"]));

            try {
                $this->_conn = new \PDO($dsn, $username, $password, $options);
            } catch (\PDOException $e) {
                throw new QFrameDBException($e->getMessage(), $e->getCode());
            }

            $this->_conn->setAttribute(PDO::ATTR_ERRMODE, $this->_error_mode);
            $this->execute("SET NAMES '{$this->_config["charset"]}'");
            $this->execute("SET character_set_client=binary");
        }
    }/*}}}*/

    private function _exec($sql, $params)
    {/*{{{*/
        $this->_connect();

        if($this->_debug) {
            print $this->getBindedSql($sql, $params)."\n";
        }

        $stmt = new QFrameDBStatment($this->_conn->prepare($sql));
        if(is_array($params)) {
            if(!empty($params)) {
                $i = 0;
                foreach($params as $value) {
                    $stmt->bind(++$i, $value);
                }
            }
        } else {
            $stmt->bind(1, $params);
        }
        $execute_return = $stmt->execute();

        if($this->_optimize && preg_match("/^select/i", $sql)) {
            $fetch_mode = $this->_fetch_type;
            $this->setFetchMode(PDO::FETCH_ASSOC);
            $debug = $this->_debug;
            $this->setDebug(true);
            QFrameDBExplainResult::draw($this->getAll("explain ".$this->getBindedSql($sql, $params)));
            $this->setDebug($debug);
            $this->setFetchMode($fetch_mode);
        }

        return array("stmt"=>$stmt, "execute_return"=>$execute_return);
    }/*}}}*/

    private function _process($sql, $params)
    {/*{{{*/
        if(in_array(preg_replace("/\s{2,}/", " ", strtolower($sql)), array("begin", "commit", "rollback", "start transaction", "set autocommit=0", "set autocommit=1"))) {
            throw new QFrameDBException("存储过程预处理失败[startTrans, commit, rollback]");
        }

        if($this->_transaction) {
            if($this->_reconnected) {
                throw new QFrameDBException("预处理失败！");
            } else {
                try {
                    $arr_exec_result = $this->_exec($sql, $params);
                } catch (PDOException $e) {
                    if(in_array($e->errorInfo[1], array(2013, 2006))) {
                        $this->_reconnected = true;
                    }

                    throw new QFrameDBException($e->errorInfo[2], $e->errorInfo[1]);
                }
            }
        } else {
            try {
                $arr_exec_result = $this->_exec($sql, $params);
            } catch (PDOException $e) {
                if($this->_auto_reconnect && $e->errorInfo[1] == 2013) {
                    try {
                        $arr_exec_result = $this->_exec($sql, $params);
                        $this->_reconnected = true;
                    } catch (PDOException $e) {
                        throw new QFrameDBException($e->errorInfo[2], $e->errorInfo[1]);
                    }
                } else {
                    throw new QFrameDBException($e->errorInfo[2], $e->errorInfo[1]);
                }
            }
        }

        return $arr_exec_result;
    }/*}}}*/

    private function _checkSafe($sql, $is_open_safe = false)  //改为false
    {/*{{{*/
        if(!$is_open_safe) {
            return true;
        }

        $string  = strtolower($sql);
        $operate = strtolower(substr($sql, 0, 6));
        $is_safe = true;

        switch ($operate) {
            case "select":
                if(strpos($string, "where") && !preg_match("/\(.*\)/", $string) && !strpos($string, "?")) {
                    $is_safe = false;
                }
                break;
            case "insert":
            case "update":
            case "delete":
                if(!strpos($string, "?")) {
                    $is_safe = false;
                }
                break;
        }

        if(!$is_safe) {
            //echo 'exception';
            throw new QFrameDBException("SQL:[$sql],本查询操作使用了强制预处理策略，但是没有发现需要预处理内容。");
        }

        return $is_safe;
    }/*}}}*/

    public function getInsertId()
    {/*{{{*/
        return $this->_conn->lastInsertId();
    }/*}}}*/

    public function execute($sql, $params = array(), $is_open_safe = false) //改为false
    {/*{{{*/
        $this->_checkSafe($sql, $is_open_safe);
        $arr_process_result = $this->_process($sql, $params);

        if($arr_process_result["execute_return"]) {
            $operate = strtolower(substr($sql, 0, 6));
            switch ($operate) {
                case "insert":
                    $arr_process_result["execute_return"] = $this->getInsertId();
                    break;
                case "update":
                case "delete":
                    $arr_process_result["execute_return"] = $arr_process_result["stmt"]->getEffectedRows();
                    break;
                default:
                    break;
            }
        }

        if($this->_log != null) {
            $this->_log->sql($sql, $params);
        }

        return $arr_process_result["execute_return"];

    }/*}}}*/

    public function query($sql, $params = array(), $is_open_safe = false)
    {/*{{{*/
        $this->_checkSafe($sql, $is_open_safe);
        $result = $this->_process($sql, $params);
        return $result["stmt"];
    }/*}}}*/

    public function getOne($sql, $params = array(), $safe = true)
    {/*{{{*/
        $stmt   = $this->query($sql, $params, $safe);
        $record = $stmt->fetch($this->_fetch_type);
        return is_array($record) && !empty($record) ? array_shift($record) : null;
    }/*}}}*/

    public function getRow($sql, $params = array(), $safe = true)
    {/*{{{*/
        $stmt   = $this->query($sql, $params, $safe);
        $record = $stmt->fetch($this->_fetch_type);
        return is_array($record) && !empty($record) ? $record : array();
    }/*}}}*/

    public function getAll($sql, $params = array(), $safe = false )
    {/*{{{*/
        $stmt = $this->query($sql, $params, $safe);
        $data = array();
        while ($record = $stmt->fetch($this->_fetch_type)) {
            $data[] = $record;
        }
        return $data;
    }/*}}}*/

    private function _operate($table, $record, $operate, $condition = "", $params = array())
    {/*{{{*/
        if(in_array($operate, array("insert", "replace", "update"))) {
            $fields = is_array($record) ? array_keys($record)   : array();
            $values = is_array($record) ? array_values($record) : array();

            if(empty($fields)) {
                throw new QFrameDBException("\$record 没有按照");
            }
        }

        switch ($operate) {
            case "insert":
            case "replace":
                $sql = "$operate into $table (`".implode("`,`", $fields)."`) values (".str_repeat("?,", count($fields) - 1)."?)";
                return $this->execute($sql, $values);
                break;
            case "update":
                $sql = "update $table set ";
                foreach($fields as $field) {
                    $sql .= "$field=?,";
                }
                $sql = substr($sql, 0, -1);

                if($condition) {
                    $sql .= " where ".$condition;
                }
                is_array($params) ? $values = array_merge($values, $params) : $values[] = $params;
                return $this->execute($sql, $values);
                break;
            case "delete":
                $sql = "delete from $table where $condition";
                return $this->execute($sql, $params);
                break;
        }
        return true;
    }/*}}}*/

    public function insert($table, $record)
    {/*{{{*/
        return $this->_operate($table, $record, "insert");
    }/*}}}*/

    public function replace($table, $record)
    {/*{{{*/
        return $this->_operate($table, $record, "replace");
    }/*}}}*/

    public function update($table, $record, $condition, $params=array())
    {/*{{{*/
        try {
            return $this->_operate($table, $record, "update", $condition, $params);
        } catch (QFrameDBException $e) {
            throw new QFrameDBException($e->getMessage());
        }
    }/*}}}*/

    public function delete($table, $condition, $params)
    {/*{{{*/
        return $this->_operate($table, null, "delete", $condition, $params);
    }/*}}}*/

    public function setWaitTimeOut($seconds)
    {/*{{{*/
        $this->execute("set wait_timeout=$seconds");
    }/*}}}*/

    public function setAutoReconnect($flag)
    {/*{{{*/
        $this->_auto_reconnect = $flag;
    }/*}}}*/

    public function setDebug($flag = false)
    {/*{{{*/
        $this->_debug = $flag;
    }/*}}}*/

    public function setOptimize($flag = false)
    {/*{{{*/
        $this->_optimize = $flag;
    }/*}}}*/

    public function setFetchMode($fetch_type = PDO:: FETCH_ASSOC)
    {/*{{{*/
        $this->_fetch_type = $fetch_type;
    }/*}}}*/

    public function setLog($log)
    {/*{{{*/
        $this->_log = $log;
    }/*}}}*/

    public function startTrans()
    {/*{{{*/
        if($this->_transaction) {
            throw new QFrameDBException("有事务未提交!");
        }

        $this->_connect();

        try {
            $this->_conn->beginTransaction();
        } catch (PDOException $e) {
            $errorInfo = $this->_conn->errorInfo();
            throw new QFrameDBException($errorInfo[2], $errorInfo[1]);
        }

        $this->_transaction = true;
        $this->_reconnected = false;
    }/*}}}*/

    public function commit()
    {/*{{{*/
        if(!$this->_transaction) {
            throw new QFrameDBException("并未在事务流程中!");
        }

        $this->_transaction = false;
        $this->_reconnected = false;

        try {
            $this->_conn->commit();
        } catch (PDOException $e) {
            $errorInfo = $this->_conn->errorInfo();
            throw new QFrameDBException($errorInfo[2], $errorInfo[1]);
        }
    }/*}}}*/

    public function rollback()
    {/*{{{*/
        if(!$this->_transaction) {
            throw new QFrameDBException("并未在事务流程中!");
        }

        $this->_transaction = false;
        $this->_reconnected = false;

        try {
            $this->_conn->rollback();
        } catch (PDOException $e) {
            $errorInfo = $this->_conn->errorInfo();
            throw new QFrameDBException($errorInfo[2], $errorInfo[1]);
        }
    }/*}}}*/

    public function commit_start()
    {/*{{{*/
        $this->_conn->autocommit(false);
    }/*}}}*/

    public function commit_end()
    {/*{{{*/
        $this->_conn->autocommit(true);
    }/*}}}*/

    public function commit_errno(){
        return $this->_conn->errno;
    }

    public function close()
    {/*{{{*/
        $this->_conn = null;
    }/*}}}*/

    public function getBindedSql($sql, $params = array())
    {/*{{{*/
        if (!preg_match("/\?/", $sql)) {
            return $sql;
        }

        preg_match_all('/(?<!\\\\)\'.*(?<!\\\\)\'/U', $sql, $arr_match_list);
        $arr_exists_list = $arr_match_list[0];
        foreach($arr_match_list[0] as $value) {
            $sql = str_replace($value, "#", $sql);
        }

        if(!is_array($params)) {
            $params = array($params);
        }

        preg_match_all("/[#\?]/", $sql, $arr_match_list);
        $arr_split_list = preg_split("/[#\?]/", $sql);

        $sql = "";
        foreach($arr_match_list[0] as $key=>$flag) {
            $sql .= $arr_split_list[$key].($flag == "#" ? array_shift($arr_exists_list) : $this->quote(array_shift($params)));
        }

        if (isset($arr_split_list[$key + 1])) {
            $sql .= $arr_split_list[$key + 1];
        }

        return $sql;
    }/*}}}*/

    public function quote($string)
    {/*{{{*/
        return $this->_conn->quote($string);
    }/*}}}*/
}