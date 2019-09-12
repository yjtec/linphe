<?php

namespace Yjtec\Linphe\Lib\Db;

/**
 *
 * @author Administrator
 */
interface Intf {

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
    public function add($data);

    /**
     * 删除
     */
    public function delete();

    /**
     * 更新
     * @param type $data
     */
    public function update($data);

    /**
     * 查询
     * @param type $one 是否是返回单个结果
     */
    public function select($one = false);

    /**
     * 只返回数量
     */
    public function count();

    /**
     * 设置slt返回哪些字段
     * @param type $field
     */
    public function field($field = '*');

    /**
     * 设置条件
     * @param type $where
     */
    public function where($where, $linkSn = 'and');

    /**
     * 设置limit
     * @param type $offset
     * @param type $rows
     */
    public function limit($offset = 0, $rows = null);

    /**
     * 设置排序
     * @param type $order
     */
    public function order($order);

    /////////////////////////////事务支持/////////////////////////////
    public function startTrans();

    public function commit();

    public function rollback();
}
