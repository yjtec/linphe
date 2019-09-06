<?php

namespace Yjtec\Linphe\Lib\Cache;

/**
 * 写一半不想写了，下次再说吧
 *
 * @author Administrator
 */
class File extends Driver {

    public $config;
    public $handle;

    public function __construct($options = array()) {
        $this->config = $this->parseConfig($options);
        if (!is_dir($this->config['path']) || !is_writable($this->config['path'])) {
            throw new Exception('缓存目录不可写');
        }
        $this->config['path'] .= rtrim($this->config['path'], '/') . '/cache';
        if (!is_dir($this->config['path'])) {
            mkdir($this->config['path']);
        }
    }

    public function get($key) {
        $name = $this->cacheName($key);
        if (file_exists($name) && $data = json_decode(file_get_contents($name), true)) {
            if ($data['exp'] > 0 && time() < $data['exp']) {
                
            }
        }
        return false;
    }

    public function remove($key = null) {
        
    }

    public function set($key, $val, $expire = null) {
        if (is_null($expire)) {
            $expire = $this->config['expire'];
        }
        $name = $this->cacheName($key);
        $value = (is_object($val) || is_array($val)) ? json_encode($val) : $val; //对数组/对象数据进行缓存处理，保证数据完整性
        return file_put_contents($name, json_encode(['data' => $value, 'exp' => is_int($expire) && $expire > 0 ? (time() + intval($expire)) : 0]));
    }

    private function cacheName($key) {
        return $this->config['path'] . '/' . md5($this->config['prefix'] . $key) . '.cache';
    }

    public function parseConfig($config = array()) {
        if (!is_array($config)) {
            $config = array();
        }
        return array(
            'dbtype' => isset($config['DBTYPE']) && $config['DBTYPE'] ? $config['DBTYPE'] : "File",
            'path' => isset($config['PATH']) && $config['PATH'] ? $config['PATH'] : ".",
            'prefix' => isset($config['PREFIX']) && $config['PREFIX'] ? $config['PREFIX'] : "YjtecLinpheCacheFile_",
            'expire' => isset($config['EXPIRE']) && $config['EXPIRE'] ? intval($config['EXPIRE']) : 0
        );
    }

}
