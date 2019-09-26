<?php

use Config\Conf;
use Yjtec\Linphe\Lib\Router;
use Yjtec\Linphe\Lib\Schedule;

if (php_sapi_name() == "cli") {// 只允许在cli下面运行 
//////////////////消费者//////////////////
    Router::cli("/worker(\/.*)*/u", "app\\worker\\worker", 'start', function () {
        if (!class_exists('\\Redis', false)) {
            die('必须开启Redis扩展' . PHP_EOL);
        }
        if (!function_exists('pcntl_fork') && Conf::CUR_ENV != 'dev.') {
            die('您的运行环境不支持pcntl_fork函数' . PHP_EOL);
        }
    }
    );
    Schedule::set('* * * * *', 'app\\index\\index', 'index'); //分 时 天 月 周（只能是数字或星号，支持/和-），类，方法
    //注意，使用schedule的cli路由，会导致定时任务失效
} else {
//////////////////上传-生产者//////////////////
//上传
    Router::post("/upload/u", "app\\index", 'upload'); //支持普通方法和静态方法
//查询
    Router::get("/status\/[0-9]*/u", "app\\index", 'status');
    Router::get("/notify/u", "app\\index", 'notify');
//管理员
    Router::get("/admin\/jobs(\/[0-9]*)(\/[0-9]*)/u", "app\\admin\\mkpano\\jobs", 'jlist');
}