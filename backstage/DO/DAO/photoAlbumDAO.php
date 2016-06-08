<?php
//error_reporting(0);
require_once(BACKSTAGE_DIR.'/DO/DBC.class.php');
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/16
 * Time: 下午12:16
 */

class photoAlbumDAO extends DBC{
    public function __construct(){
        return parent:: __construct(get_class($this));
    }


    /**
     * 获取所有相册组信息
     */
    public function getAllGroups(){
        $rs = $this->getResult(__FUNCTION__);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * 根据相册ID获取相册照片
     * @param $albumId
     * @return array
     */
    public function getPhotosByAlbumId($albumId)
    {
        $rs = $this->getResult(__FUNCTION__,array('albumId'=>$albumId));
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * 获取网站封面
     */
    public function getCoverList()
    {
        $rs = $this->getResult(__FUNCTION__);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * 获取groupList
     */
    public function getGroupList()
    {
        $rs = $this->getResult(__FUNCTION__);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * 根据相册id获取相册列表
     * @param $groupId
     * @return array
     */
    public function getAlbumList($groupId)
    {
        $rs = $this->getResult(__FUNCTION__,array('groupId'=>$groupId));
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }




    /**
     * 根据相册分类查找所有相册
     * @param $id
     * @return mixed
     */
    function getAlbumsByGroupId($id)
    {
        $rs = $this->getResult(__FUNCTION__,array('id'=>$id));
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * 根据ID查找相册
     * @param $id
     * @return mixed
     */
    function getAlbumInfoById($id)
    {
        $rs = $this->getResult(__FUNCTION__,array('id'=>$id));
        $row = $rs->fetch();
        return $row;
    }

    /**
     * 根据相册id查找图片
     * @param $id
     * @return mixed
     */
    public function getPhotoInfoByAlbumId($id)
    {
        $rs = $this->getResult(__FUNCTION__,array('id'=>$id));
        $rows = $rs->fetchAll();
        return $rows;
    }

    /**
     * 获得所有相册
     * @param int $count
     * @return array
     */
    public function getAllAlbums($count = 1000)
    {
        $rs = $this->getResult(__FUNCTION__,array("count"=>$count));
        $rows = $rs->fetchAll();
        return $rows;
    }

    /**
     * 更新相册
     * @param $id
     * @param $information
     * @return int|PDOStatement
     */
    public function updateAlbumById($id, $information)
    {
        $info = array();
        foreach($information as $k=> $v ){
            array_push($info,"`".$k."`"."="."'".$v."'");
        }
        $info = implode(",",$info);
        $rs = $this->getResult(__FUNCTION__,array("id"=>$id,'info'=>$info));
        return $rs;
    }

    /**
     * 新增相册信息
     * @param $album_info
     * @return int|PDOStatement
     */
    public function addAlbumById($album_info)
    {
        $colname = array();
        $value = array();
        foreach($album_info as $k=>$v ){
            array_push($colname,"`".$k."`");
            array_push($value,"'".$v."'");
        }
        $colname = implode(",",$colname);
        $value = implode(",",$value);
        $rs = $this->getResult(__FUNCTION__,array('colname'=>$colname,'value'=>$value));
        return $rs;
    }


}