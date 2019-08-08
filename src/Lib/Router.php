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

    public static $requestType;
    public static $getRouters;
    public static $postRoutes;

    public static function get($route, $mcf) {
        self::$getRouters[$route] = $mcf;
    }

    public static function post($route, $mcf) {
        self::$postRoutes[$route] = $mcf;
    }

    public static function getCLS() {
        self::requestType();
        switch (self::$requestType) {
            case 'GET':
                foreach (self::$getRouters as $key => $route) {
                    if (preg_match($key, $_SERVER['REQUEST_URI'], $param)) {
                        return [$route, substr($param[0], strpos($param[0], '/') + 1)];
                    }
                }
                break;
            case 'POST':
                foreach (self::$postRoutes as $key => $route) {
                    if (preg_match($key, $_SERVER['REQUEST_URI'], $param)) {
                        return [$route, $_POST];
                    }
                }
                break;
        }
        return ['index\\index', []];
    }

    public static function requestType() {
        self::$requestType = $_SERVER['REQUEST_METHOD'];
    }

}
