<?php

namespace Yjtec\Linphe\Lib;

use Yjtec\Linphe\Core;

/**
 * 定时任务
 *
 * @author Administrator
 */
class Schedule {

    public static $Schedules;

    public static function run() {
        foreach (self::$Schedules as $key => $sch) {
            self::$Schedules[$key]['time'] = $sch['time'] = isset($sch['isFormatTime']) && $sch['isFormatTime'] ? $sch['time'] : self::formateTime($sch['time']);
            self::$Schedules[$key]['isFormatTime'] = $sch['isFormatTime'] = 1;
            if (!self::inTime($sch['time'])) {
                continue;
            }
            Core::doClass($sch['class'], $sch['func'], [$_SERVER['argv']]);
        }
        return true;
    }

    public static function inTime($time) {
        $now = time();
        if (!self::inArea(date('w', $now), $time[4])) {//周
            return false;
        }
        if (!self::inArea(date('m', $now), $time[3])) {//月
            return false;
        }
        if (!self::inArea(date('d', $now), $time[2])) {//日
            return false;
        }
        if (!self::inArea(date('h', $now), $time[1])) {//时
            return false;
        }
        if (!self::inArea(date('i', $now), $time[0])) {//时
            return false;
        }
        return true;
    }

    private static function inArea($num, $area) {
        if ($area == '*') {
            return true;
        }
        if (strpos($area, '\/') !== false) {//形如*/2,15/2
            list($k, $v) = explode('\/', $area);
            if ($k == '*') {
                if (intval($num) == intval($v)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if (intval($num) == intval($k / $v)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        if (strpos($area, '-') !== false) {//形如*-2,3-7
            list($v1, $v2) = explode('-', $area);
            if (intval($num) >= intval($v1) && intval($num) <= intval($v2)) {
                return true;
            } else {
                return false;
            }
        }
        if (intval($num) == intval($area)) {
            return true;
        }
        return false;
    }

    /**
     * 返回标准型Time，也就是* * * * *
     * @param type $time
     */
    private static function formateTime($time) {
        if (!is_string($time)) {
            $time = strval($time);
        }
        $t = '';
        for ($i = 0; $i < strlen($time); $i++) {
            if ($time[$i] == '*' || $time[$i] == '-' || $time[$i] == '/' || $time[$i] == ' ' || is_numeric($time[$i])) {
                $t .= $time[$i];
            }
        }
        $times = array_filter(explode(' ', $t));
        if (count($times) < 5) {
            $times = array_merge($times, array_fill(count($times), 5 - count($times), '*'));
        }
        $times[4] = (intval($times[4]) >= 1 && intval($times[4]) <= 7 ? intval(intval($times[4])) : '*'); //周，1-7
        $times[3] = (intval($times[3]) >= 1 && intval($times[3]) <= 12 ? intval(intval($times[3])) : '*'); //月，1-12
        $times[2] = (intval($times[2]) >= 1 && intval($times[2]) <= 31 ? intval(intval($times[2])) : '*'); //日，1-31
        $times[1] = (intval($times[1]) >= 0 && intval($times[1]) <= 23 && $times[1] != '*' ? intval(intval($times[1])) : '*'); //时，0-23
        $times[0] = (intval($times[0]) >= 0 && intval($times[0]) <= 59 && $times[0] != '*' ? intval(intval($times[0])) : '*'); //时，0-59
        return $times;
    }

    public static function set($time, $class, $func) {
        self::$Schedules[] = ['time' => $time, 'class' => $class, 'func' => $func];
    }

}
