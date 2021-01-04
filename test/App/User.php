<?php

namespace Test\App;

use Test\Lib\C;
use Test\Lib\D;

/**
 * Description of User
 *
 * @author Administrator
 */
class User {

    public function __construct(C $c) {
        
    }

    public static function start(D $d, Request\Cli $req) {
        echo 'start' . PHP_EOL;
        \Yjtec\Linphe\Ioc::make('Test\\Lib\\E', $methodName);
    }

}
