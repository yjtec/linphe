<?php

namespace Test\Lib;

/**
 * Description of C
 *
 * @author Administrator
 */
class C extends B {

    public function __construct($x = null, \Test\Lib\E $e=null) {
        parent::__construct();
        echo 'C-Class' . PHP_EOL;
    }

}
