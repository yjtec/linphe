<?php

use Yjtec\Linphe\Core;

if (php_sapi_name() != "cli") {// 只允许在cli下面运行 
    die('仅允许在cli下测试' . PHP_EOL);
}
require_once __DIR__ . '/Autoload.php';
if (!class_exists('Test\\Autoload', false)) {
    die('自动加载类错误' . PHP_EOL);
}
spl_autoload_register('\\Test\\Autoload::autoload');
require_once __DIR__ . '/App/Route.php';
try {
    Core::start();
} catch (\Throwable $ex) {
    echo ($ex->getMessage());
}
