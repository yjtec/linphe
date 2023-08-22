<?php

namespace Yjtec\Linphe\Lib;

/**
 * Description of Request
 *
 * @author Administrator
 */
abstract class Request
{

    public static $_reqUri, $_reqType, $_get, $_post, $_file, $_put, $_cli, $_input;
    public $get, $post, $file, $put, $cli, $input;

    public function __construct()
    {
        $this->_get();
        $this->_post();
        $this->_file();
        $this->_put();
        $this->_cli();
        $this->_input();
        $this->verify();
    }

    public abstract function verify();

    protected function _get()
    {
        if (self::$_get) {
            $this->get = self::$_get;
        }
        return self::$_get;
    }

    protected function _post()
    {
        if (self::$_post) {
            foreach (self::$_post as $k => $v) {
                $this->{$k} = $v;
            }
            $this->post = self::$_post;
        }
        return self::$_post;
    }

    protected function _file()
    {
        if (self::$_file) {
            $this->file = self::$_file;
        }
        return self::$_file;
    }

    protected function _put()
    {
        if (self::$_put) {
            $this->put = self::$_put;
        }
        return self::$_put;
    }

    protected function _cli()
    {
        if (self::$_cli) {
            $this->cli = self::$_cli;
        }
        return self::$_cli;
    }
    protected function _input()
    {
        if (self::$_input) {
            $this->input = self::$_input;
        }
        return self::$_input;
    }
}
