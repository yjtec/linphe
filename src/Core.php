<?php

namespace Yjtec\Linphe;

use Yjtec\Linphe\Router;

class Core {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function start() {
        require_once __DIR__ . '/sysRouter.php';
        $routes = Router::getCLS(); //获取路由信息
        if (empty($routes)) {
            throw new \Exception('路由不存在');
        }
        $class = $routes[0];
        $function = $routes[1];
        return self::doClass($class, $function, $routes[2]);
    }

    public static function doClass($class, $function, $params) {
        if (!class_exists($class)) {
            throw new \Exception('不存在的类');
        }
        if ($function && (new \ReflectionMethod($class, $function))->isStatic()) {
            return call_user_func_array(array($class, $function), $params);
        }
        $controller = new $class();
        if ($function && method_exists($controller, $function)) {
            return call_user_func_array(array($controller, $function), $params);
        }
        if ($function && method_exists($controller, '__call')) {
            return call_user_func_array(array($controller, $function), $params);
        }
        if ($function && method_exists($class, '__callStatic')) {
            return call_user_func_array(array($class, $function), $params);
        }
        return true;
    }

}
