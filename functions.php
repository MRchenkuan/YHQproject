<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 16/5/22
 * Time: 下午6:55
 */
include_once "./backstage/definitions.php";
include_once(DATABASE_DAO_DIR."photoAlbumDAO.php");
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
 * @param $groupId
 * @return photoAlbumDAO
 */
function getAlbumList($groupId){
    $data = new photoAlbumDAO();
    $data = $data->getAlbumList($groupId);
    return $data;
}

/**
 * 获取目录下所有的文件列表
 * @param $path :路径
 * @param $type :类型列表
 * @return array
 */
function getFileListByType($path,$type)
{
    $files = scandir($path);
    $arr = array();
    foreach ($files as $key => $value) {
        $fileEnd = substr($value, strrpos($value, '.') + 1);
        if($value!='.' && $value!='..'){
            if(in_array($fileEnd, $type))
            array_push($arr, $value);
        }
    }
    return $arr ;
}