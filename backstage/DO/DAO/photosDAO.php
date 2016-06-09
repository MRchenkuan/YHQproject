<?php
//error_reporting(0);
require_once(BACKSTAGE_DIR.'/DO/DBC.class.php');
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/16
 * Time: 下午12:16
 */

class photosDAO extends DBC{
    public function __construct(){
        return parent:: __construct(get_class($this));
    }

    /**
     * 更新图片信息
     * @param $id
     * @param $info
     * @return mixed
     */
    function updateImageInfo($id,$info)
    {
        $colname = array();
        $value = array();
        foreach($info as $k=>$v ){
            array_push($colname,"`".$k."`");
            array_push($value,"'".$v."'");
        }
        $colname = implode(",",$colname);
        $value = implode(",",$value);
        $rs = $this->getResult(__FUNCTION__,array('colname'=>$colname,'value'=>$value,'id'=>$id));
        $row = $rs->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    /**
     * 增加啊图片信息
     * @param $info "必须包含 albumid path thumb"
     * @return mixed
     */
    function addImageInfo($info)
    {
        $rs = $this->getResult(__FUNCTION__,$info);
        return $rs;
    }

    /**
     * 根据id删除照片
     * @param $photoId
     * @return int|PDOStatement
     */
    function delImageById($photoId)
    {
        $rs = $this->getResult(__FUNCTION__,array("photoId"=>$photoId));
        return $rs;
    }

    /**
     * 根据图片ID获取图片信息
     * @param $photoId
     * @return array
     */
    public function getPhotoInfoById($photoId)
    {
        $rs = $this->getResult(__FUNCTION__,array('photoId'=>$photoId));
        $row = $rs->fetch();
        return $row;
    }
}