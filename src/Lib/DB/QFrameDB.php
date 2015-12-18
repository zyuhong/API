<?php
namespace Lib\DB;

class QFrameDB
{
    private static $_container      = array();
    private static $_default_config = array(
        "driver" => "mysql",
        "host" => "127.0.0.1",
        "port" => "3306",
        "username" => "root",
        "password" => "",
        "charset" => "utf8",
        "database" => "test",
        "persistent" => true,
        "unix_socket" => "",
        "options" => array()
    );

    public static function getInstance($config = array())
    {
        $key = md5(serialize($config));

        if(!isset(self::$_container[$key]) || !(self::$_container[$key] instanceof QFrameDBPDO)) {
            $final_config = array();
            foreach(self::$_default_config as $index=>$value) {
                $final_config[$index] = isset($config[$index]) && !empty($config[$index]) ? $config[$index] : self::$_default_config[$index];
            }
            self::$_container[$key] = new QFrameDBPDO($final_config);
        }

        return self::$_container[$key];
    }
}

class QFrameDBStatment
{
    private $_stmt;

    public function __construct($stmt)
    {
        $this->_stmt = $stmt;
    }

    public function fetch($mode = \PDO::FETCH_ASSOC)
    {
        return $this->_stmt->fetch($mode);
    }

    public function execute()
    {
        return $this->_stmt->execute();
    }

    public function bind($parameter, $value)
    {
        return $this->_stmt->bindValue($parameter, $value);
    }

    public function getEffectedRows()
    {
        return $this->_stmt->rowCount();
    }
}

class QFrameDBException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        $message = "数据库执行异常，[$code]:$message ($code)";

        parent::__construct($message, $code);
    }
}
