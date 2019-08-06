<?php

namespace Yjtec\Linphe\Core;

use Exception;

/**
 * Description of Model
 *
 * @author Administrator
 */
class Model {

    public $db;
    protected $tableName;
    protected $tablePreFix;

    public function __construct($config = '') {
        $this->db($config);
    }

    /**
     * 切换当前的数据库连接
     * @access public
     * @param integer $linkNum  连接序号
     * @param mixed $config  数据库连接信息
     * @param boolean $force 强制重新连接
     * @return Model
     */
    public function db($config = '') {
        if (!$this->db) {
            if (NULL === $config) {
                $this->db->close(); // 关闭数据库连接
                unset($this->db);
                return;
            } else {
                $this->db = $this->getDb($config);
            }
        }
        return $this->db;
    }

    private function getDb($db_config) {
        static $_instance = array();
        $guid = $this->to_guid_string($db_config);
        if (!isset($_instance[$guid])) {
            $db_config = $this->parseConfig($db_config); // 读取数据库配置
            if (empty($db_config['dbms'])) {
                throw new Exception('数据库类型错误');
            }
            if (strpos($db_config['dbms'], '\\')) {// 数据库类型
                $class = $db_config['dbms'];
            } else {
                $dbType = ucwords(strtolower($db_config['dbms']));
                $class = '\\Yjtec\Linphe\\Lib\\Db\\' . $dbType;
            }
            if (class_exists($class)) {// 检查驱动类
                $_instance[$guid] = new $class($db_config);
            } else {
                throw new Exception('数据库驱动不存在');
            }
        }
        return $_instance[$guid];
    }

    /**
     * 根据PHP各种类型变量生成唯一标识号
     * @param mixed $mix 变量
     * @return string
     */
    function to_guid_string($mix) {
        if (is_object($mix)) {
            return spl_object_hash($mix);
        } elseif (is_resource($mix)) {
            $mix = get_resource_type($mix) . strval($mix);
        } else {
            $mix = serialize($mix);
        }
        return md5($mix);
    }

    /**
     * 分析数据库配置信息，支持数组和DSN
     * @access private
     * @param mixed $db_config 数据库配置信息
     * @return string
     */
    private function parseConfig($db_config = '') {
        if (!empty($db_config) && is_string($db_config)) {
            $db_config = $this->parseDSN($db_config); // 如果DSN字符串则进行解析
        } elseif (is_array($db_config)) { // 数组配置
            $db_config = array_change_key_case($db_config);
            $db_config = array(
                'dbms' => $db_config['db_type'],
                'username' => $db_config['db_user'],
                'password' => $db_config['db_pwd'],
                'hostname' => $db_config['db_host'],
                'hostport' => $db_config['db_port'],
                'database' => $db_config['db_name'],
                'dsn' => isset($db_config['db_dsn']) ? $db_config['db_dsn'] : '',
                'params' => isset($db_config['db_params']) ? $db_config['db_params'] : array(),
                'charset' => isset($db_config['db_charset']) ? $db_config['db_charset'] : 'utf8',
            );
        } elseif (empty($db_config)) {
// 如果配置为空，读取配置文件设置
            $db_config = array(
                'dbms' => 'pdo',
                'username' => 'root',
                'password' => '',
                'hostname' => 'localhost',
                'hostport' => '6379',
                'database' => 'Yjtec\Linphe',
                'charset' => 'utf-8',
            );
        }
        return $db_config;
    }

    /**
     * select，需要自己写sql语句
     * @param type $sql
     * @param type $param
     * @return type
     */
    public function select($sql, $param = null) {
        return $this->db->query($sql, $param);
    }

    /**
     * find，需要自己写sql语句
     * @param type $sql
     * @param type $param
     * @return type
     */
    public function find($sql, $param) {
        $data = $this->db->query($sql, $param);
        if ($data) {
            return $data[0];
        }
        return array();
    }

    /**
     * 更新数据
     * @param type $table 表名
     * @param type $param 字段与值
     * @param type $where 更新条件
     * @return type
     */
    public function update($table, $param, $where) {
        $this->arrayLink($param);
        $sql = "update {$table} set {$this->linkStr}";
        if ($where) {
            $this->where($where);
            $sql .= ' where ' . $this->whereStr;
        }
        $p = array_merge($this->linkArray, $this->whereArray);
        $this->_resetStrArray();
        return $this->db->execute($sql, $p);
    }

    /**
     * 更新的之后，重置条件和字段值
     */
    public function _resetStrArray() {
        $this->whereArray = array();
        $this->linkArray = array();
        $this->linkStr = '';
        $this->whereStr = '';
    }

    private $linkStr;
    private $linkArray;

    public function arrayLink(&$param, $linkSign = ',') {
        $str = '';
        foreach ($param as $k => $v) {
            $this->linkArray[':Yjtec\LinpheLink_' . $k] = $v;
            $str .= ('`' . $k . '`' . '=:Yjtec\LinpheLink_' . $k . $linkSign);
        }
        return $this->linkStr .= substr($str, 0, -1);
    }

    private $whereStr;
    private $whereArray;

    /**
     * 传入一维或二维数组
     * @param type $param
     */
    public function where($param) {
        $str = '';
        if (!empty($param) && is_array($param)) {
            $str = $this->whereArray($param);
        } elseif (is_string($param)) {
            $str = $this->whereString($param, ' and ');
        }
        $this->whereStr .= $str;
        return $this;
    }

    private function whereString($param, $parse) {
        return $parse . $param;
    }

    private function whereArray(&$param) {
        $parse = $this->isParse($param);
        $mainStr = '1';
        foreach ($param as $field => $value) {
            if (is_array($value)) {
                $tempParse = $this->isParse($value);
                $str = ' (1 ';
                $t = 1;
                foreach ($value as $sign => $v2) {
                    $str .= $this->whereString(' `' . $field . '`' . $sign . ' :Yjtec\LinpheWhere_' . $field . $t, $tempParse); //如果是key value形式，就key=value
                    $this->whereArray[':Yjtec\LinpheWhere_' . $field . $t] = $v2;
                    $t++;
                }
                $str .= ')';
                $mainStr .= $this->whereString($str, $parse);
            } else {
                $this->whereArray[':Yjtec\LinpheWhere_' . $field] = $value;
                $mainStr .= $this->whereString(' `' . $field . '`=:Yjtec\LinpheWhere_' . $field, $parse); //如果是key value形式，就key=value
            }
        }
        return $mainStr;
    }

    private function isParse(&$param) {
        if (isset($param['sign'])) {
            $temp = $param['sign'];
            unset($param['sign']);
            return ' ' . $temp . ' ';
        } else {
            return ' and ';
        }
    }

}
