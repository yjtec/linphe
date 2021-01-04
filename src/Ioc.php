<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Yjtec\Linphe;

use ReflectionClass;
use Throwable;

/**
 * Description of Ioc
 *
 * @author Administrator
 */
class Ioc {

    public static $instances = [];

    /**
     * 执行类的方法
     * @param  [type] $className  [类名]
     * @return [type]             [description]
     */
    public static function make($className) {
        // 获取类的实例
        return self::getInstance($className);
    }

    /**
     * 
     * @param  [type] $instance  [对象]
     * @param  [type] $methodName [方法名称]
     * @param  [type] $params     [额外的参数]
     * @return type
     */
    public static function doMethod($instance, $methodName, $params = []) {
        // 获取该方法所需要依赖注入的参数
        $paramArr = self::getMethodParams(get_class($instance), $methodName);
        return $instance->{$methodName}(...array_merge($paramArr, $params));
    }

    /**
     * 获取类的实例对象
     * @param  [string] $className  [类名]
     * @return [object]             [description]
     */
    // 获得类的对象实例
    public static function getInstance($className) {
        try {
            $paramArr = self::getMethodParams($className);
            $class = new ReflectionClass($className);
            return $class->newInstanceArgs($paramArr);
        } catch (Throwable $ex) {
            throw $ex;
        }
    }

    /**
     * 获得类的方法参数，只获得有类型的参数
     * @param  [string] $className   [description]
     * @param  [string] $methodsName [description]
     * @return [array]              [description]
     */
    public static function getMethodParams($className, $methodsName = '__construct') {
        $class = new ReflectionClass($className); // 通过反射获得该类
        $paramArr = []; // 记录参数，和参数类型
        if ($class->hasMethod($methodsName)) { // 判断该类是否有函数
            $function = $class->getMethod($methodsName); // 获得函数
            $params = $function->getParameters(); // 判断函数是否有参数
            if (count($params) > 0) {
                foreach ($params as $key => $param) {
                    try {
                        $paramClass = $param->getClass(); // 判断参数类型
                    } catch (Throwable $ex) {
                        throw $ex;
                    }
                    if ($paramClass) {
                        $paramClassName = $paramClass->getName(); // 获得参数类型名称
                        $args = self::getMethodParams($paramClassName); // 获得参数类型
                        $paramArr[] = (new ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                    }
                }
            }
        }
        return $paramArr;
    }

}
