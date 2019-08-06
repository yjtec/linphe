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
        $cls = 'app\\' . $routes[0];
        $lastDec = strrpos($cls, '\\');
        $class = substr($cls, 0, $lastDec);
        $function = substr($cls, $lastDec + 1);
        $controller = new $class();
        call_user_func(array($controller, $function), $routes[1]);
    }

}
