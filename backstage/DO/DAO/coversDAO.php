<?php
//error_reporting(0);
require_once(BACKSTAGE_DIR.'/DO/DBC.class.php');
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/3/16
 * Time: 下午12:16
 */

class coversDAO extends DBC{
    public function __construct(){
        return parent:: __construct(get_class($this));
    }

    /**
     * 更新封面
     * @param $id
     * @param $info
     * @return int|PDOStatement
     */
    public function updateCover($id, $info){
        $info['id'] = $id;
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
     * 新增封面
     * @param $info
     * @return int|PDOStatement
     */
    public function addCover($info){
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




}
//
//$a = new photoAlbumDAO();
//var_dump($a);
////var_dump($a->getAllAlbums());
//$a->updateAlbumById(array("remark"=>"来自php","editable"=>"true","count"=>3));