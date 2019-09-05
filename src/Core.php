<?php

namespace Yjtec\Linphe;

use Yjtec\Linphe\Lib\Router;

class Core {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function start($app = 'app') {
        $routes = Router::getCLS(); //获取路由信息
        $cls = $app . '\\' . $routes[0];
        $lastDec = strrpos($cls, '\\');
        $class = substr($cls, 0, $lastDec);
        $function = substr($cls, $lastDec + 1);
        if (!class_exists($class)) {
            throw new Exception('不存在的类');
        }
        $controller = new $class();
        if (!method_exists($controller, $function)) {
            throw new Exception('不存在的方法');
        }
        return call_user_func_array(array($controller, $function), $routes[1]);
    }

}
