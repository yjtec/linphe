<?php

namespace Yjtec\Linphe;

use Exception;
use ReflectionMethod;
use Throwable;
use Closure;

class Core
{

    /**
     * 应用程序初始化
     * @access public
     * @return void
     */
    public static function start()
    {
        try {
            $routes = Router::findCLS(); //获取路由信息
            if (empty($routes)) {
                throw new Exception('Router is not exsits!');
            }
            $classOrCallbackFunc = $routes[1];
            if (self::isFunction($classOrCallbackFunc)) {
                return $classOrCallbackFunc();
            }
            if (!class_exists($classOrCallbackFunc)) {
                throw new Exception('User Class is not exsits!class=' . $classOrCallbackFunc);
            }
            $function = $routes[2];
            $paramArr = Ioc::getMethodParams($classOrCallbackFunc, $function); // 获取该方法所需要依赖注入的参数
            if ($function && (new ReflectionMethod($classOrCallbackFunc, $function))->isStatic()) {
                return $classOrCallbackFunc::{$function}(...$paramArr);
            }
            $instance = Ioc::make($classOrCallbackFunc); // 获取类的实例
            return $instance->{$function}(...$paramArr);
        } catch (Throwable $ex) {
            throw $ex;
        }
    }
    public static  function isFunction($f)
    {
        return (is_string($f) && function_exists($f)) || (is_object($f) && ($f instanceof Closure));
    }
}
