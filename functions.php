<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/5/22
 * Time: 下午6:55
 */
require_once "backstage/definitions.php";
require_once(DATABASE_DAO_DIR."photoAlbumDAO.php");
// 获取图片地址
function getImgPath($path){
    $dirs = dirname('./img/'.$path.'/1');
    $files = scandir($dirs);
    $arr = array();
    foreach ($files as $key => $value) {
        if($value!='.' && $value!='..'){
            array_push($arr, $path.'/'.$value);
        }
    }
    $pathstring = '\''.join('\',\'',$arr).'\'';
    return $pathstring;
}

/**
 * 获取图片分类地址
 */
function getGroupList(){
    $data = new photoAlbumDAO();
    $data = $data->getGroupList();
    return $data;
}

/**
 * 获取图片分类地址
 */
function getCoverList(){
    $data = new photoAlbumDAO();
    $data = $data->getCoverList();
    return $data;
}

/**
 * 获取相册列表
 * @param $albumId
 * @return photoAlbumDAO
 */
function getAlbumList($albumId){
    $data = new photoAlbumDAO();
    $data = $data->getAlbumList($albumId);
    return $data;
}