<?php
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/16
 * Time: 下午7:49
 */

interface KodbcAdapter {
    /*初始化列数*/
    function initCol();

    /*根据id查找节点*/
    function getById($id);

    /*根据ID删除节点*/
    function delById($id);

    /*根据属性查找结果集*/
    function getByAttr($attr,$val);

    /*得到所有item列表*/
    function getAllItems();


    function sort($arr,$by,$rule);

    /**
     * 统计结果集
     * @return mixed
     */
    function count();

    /*根据起止时间查找ID*/
    function getByPeriod($start,$end);

    /*根据属性插入新节点*/
    function insertItem($attrs);

    /*根据id和对象更新节点*/
    function updateItem($id,$info);
    /* 取当前最新的id */
    function getNewestId();
}