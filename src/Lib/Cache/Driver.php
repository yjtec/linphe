<?php

namespace Yjtec\Linphe\Lib\Cache;

/**
 * Description of Driver
 *
 * @author Administrator
 */
abstract class Driver implements Intf {

    public $config;
    public $handle;

    abstract public function parseConfig($config = []);
}
