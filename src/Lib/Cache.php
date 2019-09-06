<?php

namespace Yjtec\Linphe\Lib;

use Yjtec\Linphe\Lib\Cache\Intf;

/**
 * Description of Cache
 *
 * @author Administrator
 */
class Cache {

    private static $dbInstance;
    private static $handle;
    private static $config;

    private function __construct() {
        
    }

    public static function setConfig($config) {
        self::$config = $config;
        return self::$config;
    }

    public static function getConfig() {
        return self::$config;
    }

    public static function get($key) {
        self::connect();
        return self::$handle->get($key);
    }

    public static function set($key, $val, $expire = null) {
        self::connect();
        return self::$handle->set($key, $val, $expire);
    }

    public static function remove($key = null) {
        self::connect();
        return self::$handle->remove($key);
    }

    public static function __callStatic($name, $arguments) {
        if (method_exists(self::$handler, $name)) {
            self::connect();
            return call_user_func_array(array(self::$handler, $name), $arguments);
        } else {
            throw new Exception('缓存方法不存在');
        }
    }

    private static function connect() {
        $guid = Tool::toGuidString(self::$config);
        if (!isset(self::$dbInstance[$guid])) {
            $dbType = ucwords(strtolower(self::$config['DBTYPE'] ? self::$config['DBTYPE'] : 'Redis'));
            $class = '\\Yjtec\Linphe\\Lib\\Cache\\' . $dbType;
            if (class_exists($class)) {// 检查驱动类
                self::$dbInstance[$guid] = new $class(self::$config);
            } else {
                throw new Exception('缓存驱动不存在');
            }
        }
        self::$handle = self::$dbInstance[$guid];
        return self::$handle;
    }

    public function __destruct() {
        self::$dbInstance = null;
        self::$handle = null;
        self::$config = null;
    }

}
