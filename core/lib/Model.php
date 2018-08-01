<?php

namespace core\lib;

class Model implements \ArrayAccess, \IteratorAggregate
{
    public $dataArr = [];
    protected $_primaryKey = null;
    // 表名
    protected $_db = null;
    // data
    protected $_table = null;
    protected $_attribute = [];
    // 操作条件
    protected $_options = [];
    // 默认表的添加时间字段
    protected $_createTime = 'createtime';
    // 默认表的更新时间字段
    protected $_updateTime = 'updatetime';
    protected $_dateType = 'Y-m-d H:i';

    public function __construct($table = null)
    {
        if (is_null($this->_table)) {
            if ($table) {
                $this->_table = $table;
            } else {
                $this->_table = strtolower(trim(strrchr(get_class($this), '\\'), '\\'));
            }
        }
        $this->_db = Db::getInstance();
        $this->reset();
        $this->_getAttribute();
    }

    //数组式访问

    public function reset($n = true)
    {
        $this->_options['field'] = '*';
        $this->_options['where'] = '';
        $this->_options['order'] = '';
        $this->_options['limit'] = '';
        $this->_options['join'] = '';
        if ($n) {
            $this->dataArr = [];
        }
    }

    private function _getAttribute()
    {
        $items = $this->_db->query("desc {$this->_table}");
        foreach ((array)$items as $item) {
            if ($item['Key'] == 'PRI') {
                array_unshift($this->_attribute, $item['Field']);
                $this->_primaryKey = $item['Field'];
            } else {
                $this->_attribute[] = $item['Field'];
            }
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->dataArr);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->dataArr[] = $value;
        } else {
            $this->dataArr[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->dataArr[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->dataArr[$offset]);
    }

    // 获取表的字段映射

    public function offsetGet($offset)
    {
        return isset($this->dataArr[$offset]) ? $this->dataArr[$offset] : null;
    }

    // 初始化条件

    public function is_set()
    {
        return isset($this->dataArr);
    }

    public function field($field = '*')
    {
        $this->_options['field'] = $field;
        return $this;
    }

    public function order($order = '')
    {
        if ($order) {
            $this->_options['order'] = "order by $order";
        }
        return $this;
    }

    public function limit($limit = '')
    {
        if ($limit) {
            $this->_options['limit'] = "limit $limit";
        }
        return $this;
    }

    public function value($field = null)
    {
        if (is_null($field)) {
            $this->reset(false);
            die('请传入字段');
        }
        $data = $this->find();
        if (!$data) {
            $this->reset(false);
            die('未找到匹配项');
        }
        if (in_array($field, $this->_attribute)) {
            $this->reset(false);
            return $data[$field];
        } else {
            $this->reset(false);
            die('字段不存在');
        }
    }

    //操作

    public function find($primaryKey = null)
    {
        if ($primaryKey) {
            if ($this->_primaryKey) {
                $this->where("$this->_primaryKey = $primaryKey");
            } else {
                die("$this->_table 没有主键");
            }
        }
        $datas = $this->select();
        $this->dataArr = $datas ? current($datas) : false;
        return $this;
    }

    public function where($where = '')
    {
        if ($where) {
            if (is_array($where)) {
                $w = '';
                foreach ($where as $k => $v) {
                    if (in_array($k, $this->_attribute)) {
                        $w .= "$k='$v'&&";
                    }
                }
                $w = trim($w, '&&');
                if ($w) {
                    $where = $w;
                }
            }
            if (is_string($where)) {
                if ($this->_options['where']) {
                    $this->_options['where'] = rtrim($this->_options['where'], ')') . "&&($where))";
                } else {
                    $this->_options['where'] = "where({$where})";
                }
            }
        } else {
            $this->_options['where'] = '';
        }
        return $this;
    }

    public function select($primaryKey = null)
    {
        if ($primaryKey) {
            $primaryKey = $this->prastick($primaryKey);
            if ($this->_primaryKey) {
                $this->where("$primaryKey");
            }
        }
        $sql = "select {$this->_options['field']} from $this->_table {$this->_options['join']} {$this->_options['where']} {$this->_options['order']} {$this->_options['limit']}";
        $data = $this->_db->query($sql);
        $this->reset(false);
        return $data;
    }

    protected function prastick($primaryKey)
    {
        if (!is_array($primaryKey)) {
            $primaryKey = [$primaryKey];
        }
        $pr = '';
        foreach ($primaryKey as $v) {
            $pr .= "$this->_primaryKey='$v'||";
        }
        $primaryKey = trim($pr, '||');
        return $primaryKey;
    }

    public function create($datas = [])
    {
        if (!is_array($datas)) {
            die('请传入有效数组参数');
        } else {
            foreach ($datas as $key => $data) {
                if (in_array($key, $this->_attribute)) {
                    $this->dataArr[$key] = $data;
                } else {
                    unset($datas[$key]);
                }
            }
            if (!array_key_exists($this->_createTime, $datas)) {
                $datas[$this->_createTime] = date($this->_dateType);
            }
            $field = '';
            $values = '';
            foreach ($datas as $key => $value) {
                $field .= "{$key},";
                $values .= "'{$value}',";
            }
            $field = rtrim($field, ',');
            $values = rtrim($values, ',');
            $sql = "insert into {$this->_table}({$field})values($values)";
            $this->reset(false);
            return $this->_db->query($sql);
        }
    }

    public function update($datas = [])
    {
        if (!is_array($datas)) {
            die('请传入有效数组参数');
        } else if ($datas) {
            foreach ($datas as $key => $data) {
                if (in_array($key, $this->_attribute)) {
                    $this->dataArr[$key] = $data;
                } else {
                    unset($datas[$key]);
                }
            }
            if (!array_key_exists($this->_updateTime, $datas)) {
                $datas[$this->_updateTime] = date($this->_dateType);
            }
            $set = '';
            if ($this->_options['where']) {
                foreach ($datas as $key => $value) {
                    if ($key != $this->_primaryKey) {
                        $set .= "$key = '$value',";
                    } else {
                        $this->_options['where'] = rtrim($this->_options['where'], ')') . "&&$this->_primaryKey = $value)";
                    }
                }
            } else {
                foreach ($datas as $key => $value) {
                    if ($key == $this->_primaryKey) {
                        $this->where("$key = '$value'");
                    } else {
                        $set .= "$key = '$value',";
                    }
                }
            }
            $set = rtrim($set, ',');
            $sql = "update {$this->_table} set {$set} {$this->_options['where']}";
            $this->reset(false);
            $re = $this->_db->query($sql);
            if ($re !== false) {
                $re = mysqli_affected_rows($this->_db->_conn);
            }
            return $re;
        }
    }

    public function leftjoin($s)
    {
        if ($s) {
            $this->_options['join'] = "LEFT JOIN $s";
        }
        return $this;
    }

    public function delete($primaryKey = null)
    {
        if ($primaryKey) {
            $primaryKey = $this->prastick($primaryKey);
            if ($this->_primaryKey) {
                if ($this->_options['where']) {
                    $this->_options['where'] = rtrim($this->_options['where'], ')') . "&&($primaryKey))";
                } else {
                    $this->where("$primaryKey");
                }
            } else {
                die("$this->_table 没有主键");
            }
        }
        $sql = "delete from $this->_table {$this->_options['where']}";
        $re = $this->_db->query($sql);
        if ($re !== false) {
            $re = mysqli_affected_rows($this->_db->_conn);
        }
        $this->reset(false);
        return $re;
    }
}