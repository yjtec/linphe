<?php

namespace Yjtec\Linphe\Core;

use Exception;
use Yjtec\Linphe\Lib\Db\Intf;
use Yjtec\Linphe\Lib\Tool;

/**
 * 模型基类，数据库模型均继承与此类
 *
 * @author Administrator
 */
class Model implements Intf {

    public static $dbInstance;
    public $db;
    protected $dbConfig;
    protected $tableName;

    public function __construct($config = '') {
        $this->dbConfig = $this->parseConfig($config);
        $this->db = $this->getDb($this->dbConfig);
    }

    private function getDb($dbConfig) {
        $guid = Tool::toGuidString($dbConfig);
        if (!isset(self::$dbInstance[$guid])) {
            $dbType = ucwords(strtolower($dbConfig['db_type']));
            $class = '\\Yjtec\Linphe\\Lib\\Db\\' . $dbType;
            if (class_exists($class)) {// 检查驱动类
                self::$dbInstance[$guid] = new $class($dbConfig);
            } else {
                throw new Exception('数据库驱动不存在');
            }
        }
        return self::$dbInstance[$guid];
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
            'db_prefix' => isset($db_config['db_prefix']) && $db_config['db_prefix'] ? $db_config['db_prefix'] : "",
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
            $this->tableName = Tool::parseName($this->dbConfig['db_prefix'] . $this->getModelName());
        }
        return $this->dbConfig['db_name'] . '.' . $this->tableName;
    }

    public function getModelName() {
        $name = substr(get_class($this), 0, -strlen('Md'));
        if ($pos = strrpos($name, '\\')) {//有命名空间
            $name = substr($name, $pos + 1);
        }
        return strtolower(preg_replace("/([A-Z])/", "_\\1", $name));
    }

    private function setDbTableName() {
        return $this->db->setTableName($this->getTableName());
    }

    /**
     * 
     * @param type $data
     * @param type $all
     */
    public function add($data) {
        $this->setDbTableName();
        return $this->db->add($data);
    }

    public function delete() {
        $this->setDbTableName();
        return $this->db->delete();
    }

    public function update($data) {
        $this->setDbTableName();
        return $this->db->update($data);
    }

    public function select($one = false) {
        $this->setDbTableName();
        return $this->db->select($one);
    }

    public function field($field = '*') {
        $this->db->field($field);
        return $this;
    }

    public function where($where, $linkSn = 'and') {
        $this->db->where($where, $linkSn);
        return $this;
    }

    public function limit($offset = 0, $rows = null) {
        $this->db->limit($offset, $rows);
        return $this;
    }

    public function order($order) {
        $this->db->order($order);
        return $this;
    }

    public function startTrans() {
        $this->db->startTrans();
        return $this;
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollback() {
        return $this->db->rollback();
    }

}
