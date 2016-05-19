<?php
error_reporting(0);
require_once($_SERVER['DOCUMENT_ROOT'] . '/backstage/definitions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/backstage/DO/DBC.class.php');/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/16
 * Time: 下午12:16
 */

class newsDAO extends DBC{
    public function __construct(){
        return parent:: __construct(get_class($this));
    }

    /**
     * 根据ID获取新闻数据
     * @return int|PDOStatement
     */
    public function getNewsDataById($id){
        $rs = $this->getResult(__FUNCTION__,array('id'=>$id));
        $row = $rs->fetch();
        return $row;
    }

    /**
     * 获取最新的新闻数据
     * @param $count
     * @return int|PDOStatement
     */
    public function getRecentNews($count)
    {
        $rs = $this->getResult(__FUNCTION__, array('count' => $count));
        $arr=$rs->fetchAll();
        return $arr;
    }

    /**
     * 根据分页取数据
     * @param $pagenow
     * @param $pagesize
     * @return array
     */
    public function  getRecentNewsByPage($pagenow,$pagesize){
//        select * from T_NEWS a order by a.pubdata desc limit 4,12
        $rs = $this->getResult(__FUNCTION__, array('start' => ($pagenow-1)*$pagesize,'size' => $pagesize));
        $arr=$rs->fetchAll();
        return $arr;
    }
    /**
     * 根据分页取数据
     * @param $pagenow
     * @param $pagesize
     * @return array
     */
    public function  getRecentADVTByPage($pagenow,$pagesize){
//        select * from T_NEWS a order by a.pubdata desc limit 4,12
        $rs = $this->getResult(__FUNCTION__, array('start' => ($pagenow-1)*$pagesize,'size' => $pagesize));
        $arr=$rs->fetchAll();
        return $arr;
    }

    /**
     * 获取新闻总条数
     * @return int
     */
    public function  getNewsCount(){
//        select * from T_NEWS a order by a.pubdata desc limit 4,12
        $rs = $this->getResult(__FUNCTION__);
        return (int)$rs->fetchColumn();
    }
    /**
     * 获取新闻总条数
     * @return int
     */
    public function  getADVTCount(){
//        select * from T_NEWS a order by a.pubdata desc limit 4,12
        $rs = $this->getResult(__FUNCTION__);
        return (int)$rs->fetchColumn();
    }
}
