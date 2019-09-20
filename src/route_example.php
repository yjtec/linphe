<?php

use Yjtec\Linphe\Lib\Router;
use Config\Conf;

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
} else {
//////////////////上传-生产者//////////////////
//上传
    Router::post("/upload/u", "app\\index\\upload");
//查询
    Router::get("/status\/[0-9]*/u", "app\\index\\status");
    Router::get("/notify/u", "app\\index\\notify");
//管理员
    Router::get("/admin\/jobs(\/[0-9]*)(\/[0-9]*)/u", "app\\admin\\mkpano\\jobs\\jlist");
}