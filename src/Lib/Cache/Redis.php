<?php

namespace Yjtec\Linphe\Lib\Cache;

/**
 * Description of Redis
 *
 * @author Administrator
 */
class Redis extends Driver {

    public $config;
    public $handle;

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    public function __construct($options = array()) {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis扩展不存在');
        }
        $this->config = $this->parseConfig($options);
        $func = $this->config['persistent'] ? 'pconnect' : 'connect'; //是否为长链接
        try {
            $this->handle = new \Redis();
            if ($this->config['timeout']) {
                $this->handle->$func($this->config['host'], $this->config['port'], $this->config['timeout']);
            } else {
                $this->handle->$func($this->config['host'], $this->config['port']);
            }
            !$this->config['pwd'] ?: $this->handle->auth($this->config['pwd']);
            !$this->config['dbname'] ?: $this->handle->select($this->config['dbname']);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    //put your code here
    public function get($key) {
        $value = $this->handle->get($this->config['prefix'] . $key);
        $jsonData = json_decode($value, true);
        return ($jsonData === NULL) ? $value : $jsonData; //检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }

    public function remove($key = null) {
        return $this->handle->delete($this->config['prefix'] . $key);
    }

    public function set($key, $val, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->config['expire'];
        }
        $name = $this->config['prefix'] . $key;
        $value = (is_object($val) || is_array($val)) ? json_encode($val) : $val; //对数组/对象数据进行缓存处理，保证数据完整性
        if (is_int($expire) && $expire) {
            return $this->handle->setex($name, $expire, $value);
        } else {
            return $this->handle->set($name, $value);
        }
    }

    public function parseConfig($config = []) {
        if (!is_array($config)) {
            $config = array();
        }
        return array(
            'dbtype' => "Redis",
            'pwd' => isset($config['PWD']) && $config['PWD'] ? $config['PWD'] : "",
            'host' => isset($config['HOST']) && $config['HOST'] ? $config['HOST'] : "127.0.0.1",
            'port' => isset($config['PORT']) && $config['PORT'] ? $config['PORT'] : 6379,
            'dbname' => isset($config['DBNAME']) && $config['DBNAME'] ? $config['DBNAME'] : 0,
            'prefix' => isset($config['PREFIX']) && $config['PREFIX'] ? $config['PREFIX'] : "YjtecLinpheCacheRedis_",
            'expire' => isset($config['EXPIRE']) && $config['EXPIRE'] ? $config['EXPIRE'] : 0,
            'timeout' => isset($config['TIMEOUT']) && $config['TIMEOUT'] ? $config['TIMEOUT'] : false,
            'persistent' => isset($config['PERSISTENT']) && $config['PERSISTENT'] ? $config['PERSISTENT'] : false
        );
    }

}
