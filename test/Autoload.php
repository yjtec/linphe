<?php

namespace Test;

/**
 * 自动加载类
 * 
 * @author Linko
 * @email 18716463@qq.com
 * @version 1.0.0
 */
class Autoload {

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        // 检查是否存在映射
        if (false !== strpos($class, '\\')) {
            $filename = str_replace('\\', '/', $class) . '.php';
            $FirstNamespace = substr($filename, 0, strpos($filename, '/'));
            switch ($FirstNamespace) {
                case 'Yjtec':
                    $filename = dirname(__DIR__) . '/src' . substr($filename, 12); //去掉前边的Yjtec\Linphe
                    break;
                default :
                    $filename = __DIR__ . substr($filename, 4);
            }
//            print_r(is_file($filename) . $class . $filename . PHP_EOL);
            if (is_file($filename)) {
                include $filename;
            }
        }
    }

}
