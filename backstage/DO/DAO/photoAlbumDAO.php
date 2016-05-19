<?php
//error_reporting(0);
require_once($_SERVER['DOCUMENT_ROOT'] . '/backstage/definitions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/backstage/DO/DBC.class.php');
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
     * 根据相册分类查找所有相册
     * @param $id
     * @return mixed
     */
    function getAlbumsByGroupId($id)
    {
        echo 12;
        $rs = $this->getResult(__FUNCTION__,array('id'=>$id));
        $row = $rs->fetch();
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
    public function getAllAlbums($count = 100)
    {
        $rs = $this->getResult(__FUNCTION__,array("count"=>$count));
        $rows = $rs->fetchAll();
        return $rows;
    }

    /**
     * 更新相册
     * @param $id
     * @param $info
     * @return mixed
     */
    public function updateAlbumById($info){

        $colname = array();
        $value = array();
        foreach($info as $k=>$v ){
            array_push($colname,$k);
            array_push($value,"'".$v."'");
        }
        $colname = implode(",",$colname);
        $value = implode(",",$value);
        $rs = $this->getResult(__FUNCTION__,array('colname'=>$colname,'value'=>$value));
        return $rs;
    }

    /**
     * 更新相册 - 兼容文件数据库
     * @param $id
     * @param $info
     */
    public function updateItem($id, $info){
        $info['id'] = $id;
        $this->updateAlbumById($id,$info);
    }

}
//
//$a = new photoAlbumDAO();
//var_dump($a);
////var_dump($a->getAllAlbums());
//$a->updateAlbumById(array("remark"=>"来自php","editable"=>"true","count"=>3));