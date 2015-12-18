<?php

namespace Model;

use Lib\DB\QFrameDB;

class Base
{
    protected $db;
    protected $table;
    protected $p_key;

    public $refresh;
    const FLAG_REDIS_REFRESH = 1;       //redis刷新标识

    public function __construct($table, $p_key, $config = [])
    {
        // 数据库初始化 默认是从数据库，如果需要指定数据库配置，则传进$config
        global $arr_slave_config;
        $mysql_config = empty($config) ? $arr_slave_config : $config;

        $this->table = $table;
        $this->p_key = $p_key;
        $this->db = QFrameDB::getInstance($mysql_config);

        $this->refresh = 0;
    }

    public function setRenew($iRenew)
    {
        $this->refresh = $iRenew;
    }

    public function getByPk($id, $cols = '*')
    {
        $p_key = $this->p_key;
        $sql = "SELECT {$cols} FROM {$this->table} WHERE {$p_key}=?";
        $ret = $this->db->getRow($sql, $id);

        if (empty($ret)) {
            return false;
        }

        return $ret;
    }

    public function load($obj)
    {
        $p_key = $this->p_key;
        $sql = "SELECT * FROM {$this->table} WHERE {$p_key}=?";
        $ret = $this->db->getRow($sql, $obj->$p_key);

        if (empty($ret)) {
            return false;
        }

        $vals = $obj->getVals();
        foreach($vals as $key => $val) {
            if(isset($ret[$key])) {
                $obj->$key = $ret[$key];
            }
        }

        return true;
    }

    public function loadByKey($obj, $ext_where = "", $orderby = "")
    {
        $arr = $obj->getVals();
        $keys = $vals = [];
        foreach ($arr as $key => $val) {
            if (!empty($val)) {
                $keys[] = $key;
                $vals[] = $val;
            }
        }
        $where = join("=? AND ", $keys);
        $where .= empty($where) ? "" : "=?";
        $where = (empty($where) && empty($ext_where)) ? "" : "WHERE ({$where} {$ext_where})" ;

        if (empty($orderby)) {
            $orderby = "{$this->p_key} DESC";
        }
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$orderby} LIMIT 1";
        $ret = $this->db->getRow($sql, $vals);

        if (empty($ret)) {
            return false;
        }

        reset($arr);

        foreach ($arr as $key => $val) {
            if (isset($ret[$key])) {
                $obj->$key = $ret[$key];
            }
        }

        return true;

    }

    public function getList($arr, $start = 0, $num = 20)
    {
        $arr = array_merge([
                'cols' => '*',
                'where' => '',
                'order' => '',
                'value' => ''
            ], $arr
        );

        $where = empty($arr["where"]) ? "" : "{$arr['where']}";
        $sql = "SELECT {$arr['cols']} FROM {$this->table}"
            . " {$where} {$arr['order']}"
            . " LIMIT {$start}, {$num}";
        $ret = $this->db->getAll($sql, $arr["value"]);
        return $ret;
    }

    public function count($where, $bind = [])
    {
        $sql = "SELECT COUNT(1) count FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $ret = $this->db->getRow($sql, $bind);
        return intval($ret["count"]);
    }

    public function check($arr)
    {
        $sql = "SELECT status FROM {$this->table} WHERE ";
        $where = array();
        foreach ($arr as $key =>$val) {
            $where[] = "{$key} = ?";
        }
        $sql .= join(" AND ", $where);
        $ret = $this->db->getRow($sql, array_values($arr));
        return $ret["status"];
    }

    public function update($arr, $condition, $params = array())
    {
        try {
            $ret = $this->db->update($this->table, $arr, $condition, $params);
            return $ret;
        } catch (\Exception $e) {
            Log::append($e, "update_{$this->table}");
        }
        return false;
    }

    public function delete($condition)
    {
        try {
            $ret = $this->db->delete($this->table, $condition);
            return $ret;
        } catch (\Exception $e) {
            Log::append($e, "delete_{$this->table}");
        }
        return false;
    }

    public function add($obj)
    {
        try {
            $arr = $obj->getVals();
            $ret = $this->db->insert($this->table, $arr);
            return $ret;
        } catch (\Exception $e) {
            Log::append($e, "insert_{$this->table}");
        }
        return false;
    }

    public function save($arr)
    {
        try {
            $ret = $this->db->insert($this->table, $arr);
            return $ret;
        } catch (\Exception $e) {
            Log::append($e, "insert_{$this->table}");
        }
    }

    public function getAll($arr = [])
    {
        $arr = array_merge([
                'cols' => '*',
                'left_join' => '',
                'where' => '',
                'order' => '',
                'value' => '',
                'limit' => ''
            ], $arr
        );

        $ljoin = empty($arr["left_join"]) ? "" : "left join {$arr['left_join']}";
        $where = empty($arr["where"]) ? "" : ("where " . $arr['where']);
        $order = empty($arr["order"]) ? "" : ("order by " . "{$arr['order']}");
        $value = empty($arr["value"]) ? "" : $arr['value'];
        $limit = empty($arr['limit']) ? "" : "limit " . $arr['limit'];

        $sql = "SELECT {$arr['cols']} FROM {$this->table} {$ljoin} {$where} {$order} {$limit}";

        $ret = $this->db->getAll($sql, $value);
        return $ret;
    }

    public function truncateTable()
    {
        try {
            $sql = "truncate {$this->table}";
            $ret = $this->db->execute($sql, array());
            return $ret;
        } catch (\Exception $e) {
            Log::append($e, "truncate_{$this->table}");
        }
        return false;
    }
}
