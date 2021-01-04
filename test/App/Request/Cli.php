<?php

namespace Test\App\Request;

use Yjtec\Linphe\Lib\Request;

/**
 * Description of Cli
 *
 * @author Administrator
 */
class Cli extends Request {

    public function __construct() {
        parent::__construct();
    }

    public function verify() {
        echo 'auto veriry' . PHP_EOL;
    }

}
