<?php

namespace Yjtec\Linphe;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Throwable;
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
                throw new Exception('Router is not exsite.');
            } else {
                Event::callEvent('findRouter');
            }
            $class = $routes[0];
            $function = $routes[1];
            $rs = self::doClass($class, $function, $routes[2]);
            Event::callEvent('afterEndCore');
            return $rs;
        } catch (Throwable $ex) {
            throw $ex;
        }
    }

    public static function doClass($class, $function, $params) {
        try {
            Event::callEvent('beforeFindUserClass');
            if (!class_exists($class)) {
                Event::callEvent('notFindUserClass');
                throw new Exception('User class is not exsits;');
            } else {
                Event::callEvent('findUserClass');
            }
            Event::callEvent('afterFindUserClass');
            if ($function && (new ReflectionMethod($class, $function))->isStatic()) {
                Event::callEvent('beforeDoUserClass');
                $rs = call_user_func_array(array($class, $function), $params);
                Event::callEvent('afterDoUserClass');
                return $rs;
            }
            return self::make($class, $function);
        } catch (Throwable $ex) {
            exit;
            throw $ex;
        }
    }

    /**
     * 执行类的方法
     * @param  [type] $className  [类名]
     * @param  [type] $methodName [方法名称]
     * @param  [type] $params     [额外的参数]
     * @return [type]             [description]
     */
    public static function make($className, $methodName, $params = []) {
        Event::callEvent('beforeNewUserClass');
        $instance = self::getInstance($className); // 获取类的实例
        Event::callEvent('afterNewUserClass');
        $paramArr = self::getMethodParams($className, $methodName); // 获取该方法所需要依赖注入的参数
        Event::callEvent('beforeDoUserClass');
        $r = $instance->{$methodName}(...array_merge($params, $paramArr));
        Event::callEvent('afterDoUserClass');
        return $r;
    }

    // 获得类的对象实例
    public static function getInstance($className) {
        try {
            $paramArr = self::getMethodParams($className);
            return (new ReflectionClass($className))->newInstanceArgs($paramArr);
        } catch (Throwable $ex) {
            throw $ex;
        }
    }

    /**
     * 获得类的方法参数，只获得有类型的参数
     * @param  [type] $className   [description]
     * @param  [type] $methodsName [description]
     * @return [type]              [description]
     */
    public static function getMethodParams($className, $methodsName = '__construct') {
        // 通过反射获得该类
        $class = new ReflectionClass($className);
        $paramArr = []; // 记录参数，和参数类型
        // 判断该类是否有构造函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            $construct = $class->getMethod($methodsName);
            // 判断构造函数是否有参数
            $params = $construct->getParameters();
            if (count($params) > 0) {
                // 判断参数类型
                foreach ($params as $key => $param) {
                    if ($param->isDefaultValueAvailable()) {
                        break;
//                        $paramArr[] = $param->getDefaultValue();
//                        continue;
                    }
                    try {
                        $paramClass = $param->getClass();
                    } catch (Throwable $ex) {
                        throw $ex;
                    }
                    if ($paramClass) {
                        $paramClassName = $paramClass->getName(); // 获得参数类型名称
                        $args = self::getMethodParams($paramClassName); // 获得参数类型
                        $paramArr[] = (new ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                        continue;
                    }
                    $paramType = $param->getType();
                    if ($paramType instanceof \ReflectionNamedType) {
                        $paramArr[] = null;
                        continue;
                    }
                    if (!$paramType) {
                        $paramArr[] = null;
                        continue;
                    }
                    if ($paramType->allowsNull()) {
                        $paramArr[] = null;
                    }
                }
            }
        }
        return $paramArr;
    }

}
