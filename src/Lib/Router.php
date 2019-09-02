<?php

namespace Yjtec\Linphe\Lib;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Router
 *
 * @author Administrator
 */
class Router {

    const supportRequestType = ['get', 'post', 'put', 'delete', 'options', 'head', 'cli'];

    public static $Routers = array(); //所有的路由,get路由,post路由,cli路由
    public static $requestType; //请求类型，GET,POST,CLI等
    public static $requestUri; //uri

    public static function getCLS() {
        self::requestType();
        self::requestUri();
        if (self::$Routers[self::$requestType]) {
            foreach (self::$Routers[self::$requestType] as $key => $route) {
                if (preg_match($key, self::$requestUri, $matches)) {
                    $param = self::$requestType == 'get' ? substr($matches[0], strpos($matches[0], '/') + 1) : (self::$requestType == 'post' ? $_POST : (self::$requestType == 'cli' ? $_SERVER['argv'] : array()));
                    return [$route, $param];
                }
            }
        }
        return ['index\\index', []];
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

/////////////////////////////以下方法为设置路由/////////////////////////////
    private static function setRoute($type, $route, $mcf) {
        self::$Routers[$type][$route] = $mcf;
    }

    public static function __callStatic($name, $arguments) {
        $func = strtolower($name);
        if (in_array($func, self::supportRequestType)) {
            self::setRoute($func, $arguments[0], $arguments[1]);
        } else {
            return '不支持的请求类型';
        }
    }

}
