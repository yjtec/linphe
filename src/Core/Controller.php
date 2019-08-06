<?php

namespace Yjtec\Linphe\Core;

/**
 * Description of Controller
 *
 * @author Administrator
 */
class Controller {

    /**
     * 携带变量
     * @param type $name
     * @param type $value
     * @return $this
     */
    protected function take($name, $value = '') {
        View::take($name, $value);
        return $this;
    }

    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    protected function ajaxReturn($data, $type = 'JSON') {
        switch (strtoupper($type)) {
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            default :
        }
    }

    /**
     * 魔术方法
     * @param type $name
     * @param type $arguments
     */
    public function __call($name, $arguments) {
        return true;
    }

    public function __destruct() {
        
    }

}
