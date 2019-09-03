<?php

namespace Yjtec\Linphe\Lib\Db;

use PDOException;

/**
 * PDO数据库驱动 
 */
class Pdo extends Driver {

    protected $PDOStatement = null;
    protected $linkPDO = null;
    // 是否使用永久连接
    protected $pconnect = false;
    protected $config;
    protected $tableName;
    protected $queryStr;
    protected $transTimes = 0;
    protected $error;
////////////sql中用到的变量////////////
    protected $whereStr = '1';
    protected $whereBindArray = [];
    protected $fields = '';
    protected $limit = '';
    protected $order = '';

    /**
     * 架构函数 读取数据库配置信息
     * @access public
     * @param array $config 数据库配置数组
     */
    public function __construct($config = '') {
        $this->config = empty($config) ? [] : $config;
    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @param array $bind 参数绑定
     * @return mixed
     */
    public function query($str, $bind = array()) {
        $this->connect();
        if (!empty($this->PDOStatement)) {
            $this->free(); //释放前次的查询结果
        }
        $this->PDOStatement = $this->linkPDO->prepare($str);
        if (false === $this->PDOStatement) {
            throw new Exception($this->error());
        }
        $this->bindPdoParam($bind); // 参数绑定
        $result = $this->PDOStatement->execute();
        if (false === $result) {
            $this->error();
            return false;
        } else {
            //返回数据集
            $result = $this->PDOStatement->fetchAll(\PDO::FETCH_ASSOC);
            $this->numRows = count($result);
            return $result;
        }
    }

    /**
     * 执行语句
     * @access public
     * @param string $str  sql指令
     * @param array $bind 参数绑定
     * @return integer
     */
    public function execute($str, $bind = array()) {
        $this->connect();
        if (!empty($this->PDOStatement)) {
            $this->free(); //释放前次的查询结果
        }
        $this->PDOStatement = $this->linkPDO->prepare($str);
        if (false === $this->PDOStatement) {
            throw new Exception($this->error());
        }
        $this->bindPdoParam($bind); // 参数绑定
        $result = $this->PDOStatement->execute();
        if (false === $result) {
            throw new \Exception($this->error());
        } else {
            $this->numRows = $this->PDOStatement->rowCount();
            return $this->numRows;
        }
    }

    /**
     * 参数绑定
     * @access protected
     * @return void
     */
    protected function bindPdoParam($bind) {
        // 参数绑定
        if (!empty($bind)) {
            foreach ($bind as $key => $val) {
                if (is_array($val)) {
                    array_unshift($val, $key);
                } else {
                    $val = array($key, $val);
                }
                call_user_func_array(array($this->PDOStatement, 'bindValue'), $val);
            }
        }
    }

/////////////////////////////////////以下方法为新支持方法/////////////////////////////////////
    public function add($data, $all = false) {
        
    }

    public function del() {
        
    }

    public function upd($data) {
        
    }

    public function slt($one = false) {
        $limit = $one ? "LIMIT 0,1" : $this->limit;
        $sql = "SELECT " . $this->fields ? $this->fields : "*" . ' FROM ' . $this->tableName . ' WHERE ' . $this->whereStr . ' ' . $this->order . ' ' . $limit . ' ';
        $result = $this->query($sql, $this->whereBindArray);
        if ($one) {
            return empty($result) && !isset($result[0]) ? [] : $result[0];
        }
        return $result;
    }

    /**
     * 两种形式
     * 数组[filed,field,field]
     * 字符串'filed,field,field'
     * @param type $field
     * @return type
     */
    public function fld($field = '*') {
        if (is_string($field)) {
            $field = explode(',', $field);
        }
        $fStr = '';
        if (is_array($field) && !empty($field)) {
            foreach ($field as $f) {
                $fStr .= '`' . str_replace('`', '', $f) . '`,';
            }
            $fStr = rtrim($fStr, ',');
        }
        $this->fields .= $field ? ',' . $field : null;
        return $this->fields;
    }

    public function lmt($offset = 0, $rows = null) {
        $this->limit = 'LIMIT ' . $offset . $rows ? ',' . $rows : null;
        return $this->limit;
    }

    public function ord($order) {
        $this->order = 'ORDER BY ' . $order;
        return $this->order;
    }

    /**
     * Where条件形式
     * ①数组[field=>value,field=>value,[field=>value,'linkSn'=>'<>'],[field=>value,'linkSn'=>'<>'],field=>value,'linkSn'=>'and/or/like']
     * ②字符串，直接拼接，注意linkSn问题
     * @param type $where
     * @param type $linkSn
     */
    public function whr($where, $linkSn = 'and') {
        if (is_string($where)) {
            $this->whereStr .= ' ' . $linkSn . ' ' . $where;
        } elseif (is_array($where)) {
            $linkSn = isset($where['linkSn']) ? $where['linkSn'] : $linkSn;
            foreach ($where as $key => $val) {
                if (!is_array($val)) {
                    $bindKey = ':YjtecWhereBind' . strval($key);
                    $this->whereStr .= ' ' . $linkSn . ' `' . $key . '`=' . $bindKey;
                    $this->whereBindArray[$bindKey] = $val;
                } else {
                    $KeyValLinkSn = isset($val['linkSn']) ? $val['linkSn'] : '=';
                    foreach ($val as $k => $v) {
                        $bindKey2 = ':YjtecWhereBind' . strval($k);
                        $this->whereStr .= ' ' . $linkSn . ' `' . $k . '`' . $KeyValLinkSn . $bindKey2;
                        $this->whereBindArray[$bindKey2] = $v;
                    }
                }
            }
        }
        return $this;
    }

    private function resetWord() {
        $this->whereBindArray = [];
        $this->whereStr = '1';
        $this->fields = '';
        $this->limit = '';
        $this->order = '';
    }

/////////////////////////////////////以下方法为旧方法-不需要修改的方法/////////////////////////////////////

    /**
     * 关闭数据库
     * @access public
     */
    public function close() {
        $this->linkPDO = null;
    }

    /**
     * 数据库错误信息
     * 并显示当前的SQL语句
     * @access public
     * @return string
     */
    public function error() {
        if ($this->PDOStatement) {
            $error = $this->PDOStatement->errorInfo();
            $this->error = $error[1] . ':' . $error[2];
        } else {
            $this->error = '';
        }
        return $this->error;
    }

    /**
     * 获取最后插入id
     * @access public
     * @return integer
     */
    public function getLastInsertId() {
        return $this->linkPDO->lastInsertId();
    }

    /**
     * 启动事务
     * @access public
     * @return void
     */
    public function startTrans() {
        $this->connect();
        if ($this->transTimes == 0) {//事务 只需要开启1次
            $this->linkPDO->beginTransaction();
        }
        $this->transTimes++;
        return;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return boolen
     */
    public function commit() {
        if ($this->transTimes > 0) {
            $result = $this->linkPDO->commit();
            $this->transTimes = 0;
            if (!$result) {
                throw new Exception($this->error());
            }
        }
        return true;
    }

    /**
     * 事务回滚
     * @access public
     * @return boolen
     */
    public function rollback() {
        if ($this->transTimes > 0) {
            $result = $this->linkPDO->rollback();
            $this->transTimes = 0;
            if (!$result) {
                throw new Exception($this->error());
            }
        }
        return true;
    }

    /**
     * 释放查询结果
     * @access public
     */
    public function free() {
        $this->PDOStatement = null;
    }

    /**
     * 初始化数据库连接
     * @access public
     */
    public function connect($config = '') {
        if (!isset($this->linkPDO)) {
            if ($this->pconnect) {
                $this->config['db_params'][Pdo::ATTR_PERSISTENT] = true;
            }
            if (version_compare(PHP_VERSION, '5.3.6', '<=')) { //禁用模拟预处理语句
                $this->config['db_params'][Pdo::ATTR_EMULATE_PREPARES] = false;
            }
            try {
                $this->linkPDO = new \PDO("mysql:host=" . $this->config['db_host'] . ";port=" . $this->config['db_port'] . ";dbname=" . $this->config['db_name'] . "", $this->config['db_user'], $this->config['db_pwd'], $this->config['db_params']);
                $this->linkPDO->exec('SET NAMES ' . $this->config['db_charset']);
            } catch (PDOException $e) {
                throw $e;
            }
        }
        if (!$this->linkPDO) {
            throw new Exception('数据库连接失败');
        }
        return $this->linkPDO;
    }

}
