<?php

namespace Yjtec\Linphe;

use Exception;
use ReflectionMethod;
use Throwable;

class Core {

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function start() {
        try {
            $routes = Router::findCLS(); //获取路由信息
            if (empty($routes)) {
                throw new Exception('Router is not exsite.');
            }
            $class = $routes[0];
            if (!class_exists($class)) {
                throw new Exception('User Class is not exsits;');
            }
            $function = $routes[1];
            $paramArr = Ioc::getMethodParams($class, $function); // 获取该方法所需要依赖注入的参数
            if ($function && (new ReflectionMethod($class, $function))->isStatic()) {
                return $class::{$function}(...$paramArr);
            }
            $instance = Ioc::make($class); // 获取类的实例
            return $instance->{$function}(...$paramArr);
        } catch (Throwable $ex) {
            throw $ex;
        }
    }

}
