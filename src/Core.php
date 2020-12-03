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
        try {
            Event::bindSystemEvent();
            Event::callEvent('beforStartCore');
            Event::callEvent('beforeFindRouter');
            $routes = Router::findCLS(); //获取路由信息
            Event::callEvent('afterFindRouter');
            if (empty($routes)) {
                Event::callEvent('notFindRouter');
                throw new \Exception('Router is not exsite.');
            } else {
                Event::callEvent('findRouter');
            }
            $class = $routes[0];
            $function = $routes[1];
            $rs = self::doClass($class, $function, $routes[2]);
            Event::callEvent('afterEndCore');
            return $rs;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public static function doClass($class, $function, $params) {
        try {
            Event::callEvent('beforeFindUserClass');
            if (!class_exists($class)) {
                Event::callEvent('notFindUserClass');
                throw new \Exception('不存在的类');
            } else {
                Event::callEvent('findUserClass');
            }
            Event::callEvent('afterFindUserClass');
            if ($function && (new \ReflectionMethod($class, $function))->isStatic()) {
                Event::callEvent('beforeDoUserClass');
                $rs = call_user_func_array(array($class, $function), $params);
                Event::callEvent('afterDoUserClass');
                return $rs;
            }
            Event::callEvent('beforeNewUserClass');
            // 获取类的实例
            $instance = Ioc::getInstance($class);
            Event::callEvent('afterNewUserClass');
            // 获取该方法所需要依赖注入的参数
            $paramArr = Ioc::getMethodParams($class, $function);
            Event::callEvent('beforeDoUserClass');
            $r = $instance->{$function}(...array_merge($paramArr, $params));
            Event::callEvent('afterDoUserClass');
            return $r;
//            $controller = new $class();
            if ($function && method_exists($controller, $function)) {
                Event::callEvent('beforeDoUserClass');
                $rs = call_user_func_array(array($controller, $function), $params);
                Event::callEvent('afterDoUserClass');
                return $rs;
            }
            if ($function && method_exists($controller, '__call')) {
                Event::callEvent('beforeDoUserClass');
                $rs = call_user_func_array(array($controller, $function), $params);
                Event::callEvent('afterDoUserClass');
                return $rs;
            }
            if ($function && method_exists($class, '__callStatic')) {
                Event::callEvent('beforeDoUserClass');
                $rs = call_user_func_array(array($class, $function), $params);
                Event::callEvent('afterDoUserClass');
                return $rs;
            }
            Event::callEvent('notDoUserClass');
            return true;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
