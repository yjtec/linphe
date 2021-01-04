<?php

namespace Yjtec\Linphe\Lib;

/**
 * Description of Request
 *
 * @author Administrator
 */
abstract class Request {

    public static $_reqType;
    public static $_get;
    public static $_post;
    public static $_file;
    public static $_put;
    public static $_cli;
    public $get, $post, $file, $put, $cli;

    public function __construct() {
        $this->_get();
        $this->_post();
        $this->_file();
        $this->_put();
        $this->_cli();
        $this->verify();
    }

    public abstract function verify();

    protected function _get() {
        if (self::$_get) {
            $this->get = self::$_get;
        }
        return self::$_get;
    }

    protected function _post() {
        if (self::$_post) {
            foreach (self::$_post as $k => $v) {
                $this->{$k} = $p;
            }
        }
        return self::$_post;
    }

    protected function _file() {
        if (self::$_file) {
            $this->file = self::$_file;
        }
        return self::$_file;
    }

    protected function _put() {
        if (self::$_put) {
            $this->put = self::$_put;
        }
        return self::$_put;
    }

    protected function _cli() {
        if (self::$_cli) {
            $this->cli = self::$_cli;
        }
        return self::$_cli;
    }

}
