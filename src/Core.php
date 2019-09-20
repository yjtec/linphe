<?php

namespace Yjtec\Linphe;

use Yjtec\Linphe\Lib\Router;

class Core {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function start() {
        $routes = Router::getCLS(); //获取路由信息
        if (empty($routes)) {
            throw new Exception('路由不存在');
        }
        $class = $routes[0];
        if (!class_exists($class)) {
            throw new Exception('不存在的类');
        }
        $controller = new $class();
        $function = $routes[1];
        if ($function && method_exists($controller, $function)) {
            return call_user_func_array(array($controller, $function), $routes[2]);
        }
        return true;
    }

}
