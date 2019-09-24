<?php

use Yjtec\Linphe\Lib\Router;

if (php_sapi_name() == "cli") {// 只允许在cli下面运行
    Router::cli('/schedule:run/u', 'Yjtec\\Linphe\\Lib\\Schedule', 'run');
}