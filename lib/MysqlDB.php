<?php

/**
 * Created by PhpStorm.
 * User: louis
 * Date: 16/7/31
 * Time: 下午7:21
 */
class MysqlDB
{
    private $conn;
    private $sql = '';

    public function __construct($config)
    {
        $this->conn = $this->connect($config);
    }

    /**
     * desc   连接数据库
     * @param $config
     * @return mysqli
     */
    public function connect($config)
    {
        $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db'], $config['port']);
        if (!$conn->connect_errno && isset($config['charset'])) {
            $conn->set_charset($config['charset']);
            $conn->query('set names utf8');
        }
        return $conn;
    }

    /**
     * desc 选择数据库
     * date 2017-06-10
     * @param string $sDbname
     * @return bool
     */
    public function selectDB($sDbname)
    {
        if(!$this->conn) {
            error_log("selectDB failed, [error : database not connected!]");
            return false;
        }

        return mysqli_select_db($this->conn, $sDbname);
    }

    /**
     * mysql 查询操作
     * @param $sql
     * @return bool
     */
    public function query($sql)
    {
        if ($this->conn) {
            $this->sql = $sql;
            $dbRet     = $this->conn->query($sql);
            if (false === $dbRet) {
                $error = $this->error();
                error_log("db failed [sql : $sql] [error : $error]");
            }
            return $dbRet;
        }
        error_log("db failed [sql : $sql] [error : database not connected!]");
        return false;
    }

    /**
     * desc   查询数据
     * @param $sql
     * @return bool|mixed
     */
    public function select($sql)
    {
        $dbRet = $this->query($sql);
        return $dbRet === false ? false : $dbRet->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * desc
     * @param $sql
     * @return array|bool
     */
    public function selectOne($sql)
    {
        $dbRet = $this->query($sql);
        return $dbRet === false ? false : $dbRet->fetch_assoc();
    }

    /**
     * desc   插入一条数据, 返回插入数据的id
     * @param $table
     * @param $array
     * @return bool
     */
    public function insert($table, $array)
    {
        $fields = array_keys($array);
        $fields = $this->escapeArray($fields);
        $fields = array_map(function ($field) {
            return "`$field`";
        }, $fields);
        $fields = implode(',', $fields);

        $values = array_values($array);
        $values = $this->escapeArray($values);
        $values = array_map(function ($value) {
            return "'$value'";
        }, $values);
        $values = implode(',', $values);

        $sql = "insert into $table ($fields) values ($values);";
        return $this->query($sql) ? $this->conn->insert_id : false;
    }

    /**
     * desc   插入一条数据
     * @param $table
     * @param $array
     * @param $conds
     * @return bool
     */
    public function update($table, $array, $conds)
    {
        $sql       = "update $table set ";
        $arrUpdate = array();
        foreach ($array as $key => $val) {
            $key         = $this->escape($key);
            $val         = $this->escape($val);
            $arrUpdate[] = "$key = '$val'";
        }
        $sql .= implode(",", $arrUpdate);
        $sql .= " where $conds";

        if ($this->conn) {
            $this->sql = $sql;
            return $this->conn->query($sql);
        }
        return false;
    }


    /**
     * 对一个字符串进行特殊字符转义
     * @param $str
     * @return bool
     */
    public function escape($str)
    {
        return isset($this->conn) ? $this->conn->real_escape_string($str) : false;
    }

    /**
     * desc   对数组进行特殊字符转义
     * @param $arr
     * @return array|bool
     */
    public function escapeArray($arr)
    {
        if (isset($this->conn)) {
            $arrRet = array();
            foreach ($arr as $key => $val) {
                $arrRet[ $key ] = $this->conn->real_escape_string($val);
            }
            return $arrRet;
        }
        return false;
    }

    /**
     * desc 关闭数据库连接
     */
    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    /**
     * desc    获取错误信息
     * @return string
     */
    public function error()
    {
        return isset($this->conn) ? $this->conn->error : 'database not connected!';
    }

    /**
     * desc    获取最近一次调用的sql
     * @return string
     */
    public function getLastSql()
    {
        return $this->sql;
    }

    /**
     * desc 事务开始
     * date 20161112
     * @return bool
     */
    public function begin()
    {
        if ($this->conn) {
            return $this->conn->begin_transaction();
        }
        return false;
    }

    /**
     * desc 事务回滚
     * date 20161112
     * @return bool
     */
    public function rollback()
    {
        if ($this->conn) {
            return $this->conn->rollback();
        }
        return false;
    }

    /**
     * desc 事务提交
     * date 20161112
     * @return bool
     */
    public function commit()
    {
        if ($this->conn) {
            return $this->conn->commit();
        }
        return false;
    }
}