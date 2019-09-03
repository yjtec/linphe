<?php

namespace Yjtec\Linphe\Core;

use Exception;
use Yjtec\Linphe\Lib\Db\DbIntf;
use Yjtec\Linphe\Lib\Tool;

/**
 * Description of Model
 *
 * @author Administrator
 */
class Model implements DbIntf {

    public $db;
    protected $dbConfig;
    protected $tableName;
    protected $tablePreFix;
    protected $fields;

    public function __construct($config = '') {
        $this->dbConfig = $this->parseConfig($config);
        $this->db($this->dbConfig);
    }

    public function db($config = '') {
        if (!$this->db) {
            $this->db = $this->getDb($config);
        }
        return $this->db;
    }

    private function getDb($dbConfig) {
        static $_instance = array();
        $guid = Tool::toGuidString($dbConfig);
        if (!isset($_instance[$guid])) {
            $dbConfig = $this->parseConfig($dbConfig); // 读取数据库配置
            $dbType = ucwords(strtolower($dbConfig['db_type']));
            $class = '\\Yjtec\Linphe\\Lib\\Db\\' . $dbType;
            if (class_exists($class)) {// 检查驱动类
                $_instance[$guid] = new $class($dbConfig);
            } else {
                throw new Exception('数据库驱动不存在');
            }
        }
        return $_instance[$guid];
    }

    private function parseConfig($db_config = '') {
        if (!is_array($db_config)) {
            $db_config = array();
        }
        return array(
            'db_type' => isset($db_config['db_type']) && $db_config['db_type'] ? $db_config['db_type'] : "pdo",
            'db_user' => isset($db_config['db_user']) && $db_config['db_user'] ? $db_config['db_user'] : "root",
            'db_pwd' => isset($db_config['db_pwd']) && $db_config['db_pwd'] ? $db_config['db_pwd'] : "",
            'db_host' => isset($db_config['db_host']) && $db_config['db_host'] ? $db_config['db_host'] : "localhost",
            'db_port' => isset($db_config['db_port']) && $db_config['db_port'] ? $db_config['db_port'] : "3306",
            'db_name' => isset($db_config['db_name']) && $db_config['db_name'] ? $db_config['db_name'] : "test",
            'db_params' => isset($db_config['db_params']) && $db_config['db_params'] ? $db_config['db_params'] : array(),
            'db_charset' => isset($db_config['db_charset']) && $db_config['db_charset'] ? $db_config['db_charset'] : 'utf8',
        );
    }

    public function query($sql, $bind = array()) {
        return $this->db->query($sql, $bind);
    }

    public function execute($sql, $bind = array()) {
        return $this->db->execute($sql, $bind);
    }

    public function getTableName() {
        if (empty($this->tableName)) {
            $tablePreFix = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if (!empty($this->tableName)) {
                $tablePreFix .= $this->tableName;
            } else {
                $tableName .= Tool::parseName($this->getModelName());
            }
            $this->tableName = strtolower($tableName);
        }
        return $this->dbConfig['db_name'] . '.' . $this->tableName;
    }

    public function getModelName() {
        $name = substr(get_class($this), 0, -strlen('Md'));
//        if ($pos = strrpos($name, '\\')) {//有命名空间
//            return substr($name, $pos + 1);
//        }
        return $name;
    }

    private function setDbTableName() {
        return $this->db->tableName = $this->getTableName();
    }

    /**
     * 
     * @param type $data
     * @param type $all
     */
    public function add($data, $all = false) {
        $this->setDbTableName();
        return $this->db->add($data, $all);
    }

    public function del() {
        $this->setDbTableName();
        return $this->db->del();
    }

    public function upd($data) {
        $this->setDbTableName();
        return $this->db->upd($data);
    }

    public function slt($one = false) {
        $this->setDbTableName();
        return $this->db->slt($one);
    }

    public function fld($field = '*') {
        $this->db->fld($field);
        return $this;
    }

    public function whr($where, $linkSn = 'and') {
        $this->db->whr($where, $linkSn);
        return $this;
    }

    public function lmt($offset = 0, $rows = null) {
        $this->db->lmt($offset, $rows);
        return $this;
    }

    public function ord($order) {
        $this->db->ord($order);
        return $this;
    }

    public function commit() {
        
    }

    public function rollback() {
        
    }

    public function startTrans() {
        
    }

}
