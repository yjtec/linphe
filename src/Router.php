<?php

namespace Yjtec\Linphe;

/**
 * 路由，Router::get(正则，匹配的类，类的方法，预调用函数);
 *
 * @author Administrator
 */
class Router {

    const supportRequestGet = 'get';
    const supportRequestPost = 'post';
    const supportRequestPut = 'put';
    const supportRequestDelete = 'delete';
    const supportRequestOption = 'options';
    const supportRequestHead = 'head';
    const supportRequestCli = 'cli';
    const supportRequestType = [self::supportRequestGet, self::supportRequestPost, self::supportRequestPut, self::supportRequestDelete, self::supportRequestOption, self::supportRequestHead, self::supportRequestCli];

    public static $Routers = array(); //所有的路由,get路由,post路由,cli路由
    public static $requestType; //请求类型，GET,POST等,特殊的CLI
    public static $requestUri; //uri
    public static $CurRouter = array();

    /**
     * 一个router的标准样子，array[类，方法，参数]，其中方法和参数可以为空
     * @return array
     */
    public static function findCLS() {
        self::requestType();
        self::requestUri();
        self::$CurRouter = [];
        if (self::$Routers[self::$requestType]) {
            foreach (self::$Routers[self::$requestType] as $route => $class_function) {
                if (preg_match($route, self::$requestUri, $matches)) {
                    self::$CurRouter = [$class_function[0], $class_function[1]];
                    self::getParam($matches);
                    break;
                }
            }
        }
        return self::$CurRouter;
    }

    public static function requestType() {
        if (PHP_SAPI === 'cli') {
            self::$requestType = 'cli';
        } else {
            self::$requestType = strtolower($_SERVER['REQUEST_METHOD']);
        }
    }

    public static function requestUri() {
        if (PHP_SAPI === 'cli') {
            self::$requestUri = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : null;
        } else {
            self::$requestUri = $_SERVER['REQUEST_URI'];
        }
    }

    public static function getParam($matches) {
        $param = [];
        switch (self::$requestType) {
            case self::supportRequestCli:
                Lib\Request::$_cli = $_SERVER['argv'];
                break;
            case self::supportRequestPost:
                Lib\Request::$_post = $_POST;
                Lib\Request::$_file = $_FILES;
                break;
            case self::supportRequestPut:
                if (is_null($_PUT)) {
                    parse_str(file_get_contents('php://input'), $_PUT);
                }
                Lib\Request::$_put = $_PUT;
                break;
            case self::supportRequestGet:
            default :
                unset($matches[0]);
                $param = array_values(str_replace('/', '', $matches));
                Lib\Request::$_get = $param;
        }
        return $param;
    }

/////////////////////////////以下方法为设置路由/////////////////////////////
    private static function setRoute($type, $route, $class, $function = '') {
        self::$Routers[$type][$route] = [$class, $function];
    }

    public static function __callStatic($name, $arguments) {
        $func = strtolower($name);
        if (in_array($func, self::supportRequestType)) {
            if (!isset($arguments[0]) || !isset($arguments[1])) {
                return false;
            }
            self::setRoute($func, $arguments[0], $arguments[1], isset($arguments[2]) && $arguments[2] ? $arguments[2] : '');
        } else {
            throw new \Exception('不支持的请求类型');
        }
    }

}
