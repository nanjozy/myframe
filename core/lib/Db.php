<?php

namespace core\lib;
class Db
{
    private static $_instance;
    public $_conn;
    private $_servername;
    private $_username;
    private $_password;
    private $_dbname;

    public function __construct()
    {
        $config = config();
        $this->_servername = isset($config['DB_SERVERNAME']) ? $config['DB_SERVERNAME'] : "localhost";
        $this->_username = isset($config['DB_USERNAME']) ? $config['DB_USERNAME'] : "root";
        $this->_password = isset($config['DB_PASSWORD']) ? $config['DB_PASSWORD'] : "root";
        $this->_dbname = isset($config['DB_NAME']) ? $config['DB_NAME'] : "project";
        $this->_conn = mysqli_connect($this->_servername, $this->_username, $this->_password, $this->_dbname);
        $this->query("set names utf8");
    }

    public function query($sql, $n = false)
    {
        $result = mysqli_query($this->_conn, $sql);
        if (is_bool($result)) {
            return $result;
        } else {
            $rows = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            if ($n == true) {
                return current($rows);
            } else {
                return $rows;
            }
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self(func_get_args());
        }
        return self::$_instance;
    }

    public function getSqls($tabName, $id = NULL)
    {
        $sql = "SELECT * FROM $tabName";
        if (is_null($id)) {

        } else if (is_string($id) && $id) {
            $sql .= " WHERE $id";
        } else if (is_array($id) && $id) {
            $sql .= " WHERE";
            $arr = '';
            foreach ($id as $v) {
                if ($arr) {
                    $arr .= ' AND';
                }
                $arr .= " $v";
            }
            $sql .= $arr;
        }
        $results = $this->query($sql);
        return $results;
    }

    public function inSql($tabName, $message, $n = 0)
    {
        $time = time();
        switch ($n) {
            case 0:
                $sql = "INSERT INTO $tabName (user,message,date) VALUES ('$message[0]','$message[1]',$time)";
        }
        $result = $this->query($sql);
        return $result;
    }

    public function getSys()
    {
        $re = $this->getSql('systeminfo', 1);
        return $re;
    }

    public function getSql($tabName, $id = NULL)
    {
        if (is_null($id) || !$id) {
            $sql = "SELECT * FROM $tabName ORDER BY id DESC limit 1";
        } else if (is_array($id) && $id) {
            $sql = "SELECT * FROM $tabName WHERE";
            $arr = '';
            foreach ($id as $k => $v) {
                if ($arr) {
                    $arr .= ' AND';
                }
                if (is_string($v)) {
                    $v = "'$v'";
                }
                $arr .= " $k = $v";
            }
            $sql .= $arr;
        } else if ($id) {
            $sql = "SELECT * FROM $tabName WHERE id = $id";
        }
        $result = $this->query($sql, true);
        return $result;
    }
}