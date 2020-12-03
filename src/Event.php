<?php

namespace Yjtec\Linphe;

/**
 * 事件绑定
 *
 * @author Administrator
 */
class Event {

    const sysEvents = [
        //Core
        'beforStartCore',
        'afterEndCore',
        //Router
        'beforeFindRouter',
        'findRouter',
        'notFindRouter',
        'afterFindRouter',
        //UserClass
        'beforeFindUserClass',
        'findUserClass',
        'notFindUserClass',
        'afterFindUserClass',
        'beforeNewUserClass',
        'afterNewUserClass',
        'beforeDoUserClass',
        'afterDoUserClass',
        'notDoUserClass',
    ];

    public static $events;

    public static function regEvent($name, $callback) {
        return self::$events[$name][] = $callback;
    }

    public static function callEvent($name, $param = []) {
        if (!self::$events[$name]) {
            return false;
        }
        foreach (self::$events[$name] as $function) {
            if (!is_callable($function)) {
                return false;
            }
            if ($function instanceof \Closure) {
                return call_user_func($function, $param);
            }
            if (is_array($function)) {
                return call_user_func_array($function, $param);
            }
            return false;
        }
    }

    public static function bindSystemEvent() {
        foreach (self::sysEvents as $event) {
            self::regEvent($event, null);
        }
        return true;
    }

}
