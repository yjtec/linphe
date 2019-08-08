<?php


namespace Linphe\Lib;


class Session
{
    /**
     * 设置session
     * @param $key
     * @param $value
     * @return bool
     */
    public function setSession($key,$value){
        return $_SESSION[$key] = $value;
    }

    /**
     * 获取session值
     * @param $key
     * @return \Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     */
    public function getSession($key){
         return !empty($_SESSION[$key]) ? $_SESSION[$key] : array();
    }
}