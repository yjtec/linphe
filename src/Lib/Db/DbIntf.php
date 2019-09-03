<?php

namespace Yjtec\Linphe\Lib\Db;

/**
 *
 * @author Administrator
 */
interface DbIntf {

    /**
     * 基础查询方法
     * @param type $sql
     * @param type $bind
     */
    public function query($sql, $bind = array());

    /**
     * 基础执行方法
     * @param type $sql
     * @param type $bind
     */
    public function execute($sql, $bind = array());

    /**
     * 新增
     * @param type $data
     * @param type $all 是否是多新增
     */
    public function add($data, $all = false);

    /**
     * 删除
     */
    public function del();

    /**
     * 更新
     * @param type $data
     */
    public function upd($data);

    /**
     * 查询
     * @param type $one 是否是返回单个结果
     */
    public function slt($one = false);

    /**
     * 设置slt返回哪些字段
     * @param type $field
     */
    public function fld($field = '*');

    /**
     * 设置条件
     * @param type $where
     */
    public function whr($where, $linkSn = 'and');

    /**
     * 设置limit
     * @param type $offset
     * @param type $rows
     */
    public function lmt($offset = 0, $rows = null);

    /**
     * 设置排序
     * @param type $order
     */
    public function ord($order);

    /////////////////////////////事务支持/////////////////////////////
    public function startTrans();

    public function commit();

    public function rollback();
}
