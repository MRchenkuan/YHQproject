<?php
/**
 * Created by PhpStorm.
 * User: chenkuan
 * Date: 16/6/12
 * Time: 上午11:55
 */
require_once(TOOLS_PATH."img2thumb.php");


/**
 * 工具方法 , 图片上传
 * @param $arr $imgDataString $albumid $onlineurl
 * @return array
 */
function uploadImage($arr){

    /**
     * 必填参数
     */
    $imgdatastring = $arr['imgDataString'] or null;
    $albumId = $arr['albumid'] or null;
    $onlineurl = $arr['onlineurl'] or null;


    $thumbPath = "";
    $thumbUrl = "";
    $imgHostUrl = Config::getSection("PROPERTIES")["IMG_HOST_URL"];

    $relative_path = date('Ymd') . '/';
    $uploaddir = IMAGE_BED_DIR . $relative_path;
    $thumbsDir = $uploaddir."thumbs/";

    // 创建目录
    if (!file_exists($uploaddir)) {
        if (mkdir($uploaddir)) {
            chmod($uploaddir, 0777);
        } else {
            return  array(
                "msg"=>'faile to create ' . $uploaddir . 'maybe the path you have no permit!<br>'
            );
        };
    }
    // 创建缩略图目录
    if (!file_exists($thumbsDir)) {
        if (mkdir($thumbsDir)) {
            chmod($thumbsDir, 0777);
        } else {
            return array(
                "msg" => 'faile to create ' . $thumbsDir . 'maybe the path you have no permit!<br>'
            );
        };
    }


    /*图片保存*/
    if($imgdatastring){
        $dao = new photosDAO();
        if (preg_match('/^(data:(\w+)\/(\w+);base64,)/', $imgdatastring, $result)){
            $type = $result[3]; //  图片类型
            $filename = microtime(true).".".substr("000".rand(0,999),-3,3).'.'.$type; // 图片文件名
            $innerFileUrl = $uploaddir. $filename; // 图片服务器地址
            $innerThumbsUrl = $thumbsDir.$filename; // 图片缩略图服务器地址
            $outHostUrl = $imgHostUrl.$relative_path.$filename; // 图片外部访问地址
            $thumbUrl = $imgHostUrl.$relative_path."thumbs/".$filename; // 缩略图外部访问地址

            if (file_put_contents($innerFileUrl, base64_decode(str_replace($result[1], '', $imgdatastring)))){
                // 生成缩略图
                img2thumb($innerFileUrl, $innerThumbsUrl);
                // 入库
                $id = $dao->addImageInfo(array(
                    'ALBUMID'=>$albumId,
                    'PATH'=>$outHostUrl,
                    'THUMB'=>$thumbUrl,
                    "FS_PATH"=>$relative_path.$filename
                ));

                if($id>0){
                    return array(
                        'stat'=>200,
                        'imgurl'=>$outHostUrl,
                        'thumburl'=>$thumbUrl,
                        'pid'=>$id,
                        'msg'=>'图片上传成功',
                    );
                }else{
                    return array(
                        'stat'=>203,
                        'pid'=>$id,
                        'thumburl'=>$thumbUrl,
                        'imgurl'=>$outHostUrl,
                        'msg'=>'图片保存失败',
                    );
                }

            }
        }else if($_POST['onlineurl']){
            /*如果没有图片但是有imgurl时*/
            $dao->addImageInfo(array(
                'ALBUMID'=>$albumId,
                'PATH'=>$onlineurl,
                'THUMB'=>$thumbPath,
                "FS_PATH"=>""
            ));
            return array(
                'stat'=>200,
                'imgurl'=>$_POST['onlineurl'],
                'msg'=>'网络URL,图片添加成功',
            );

        }else{
            return array(
                'stat'=>202,
                'imgurl'=>null,
                'imgdata'=>$imgdatastring,
                'msg'=>'图片字符串匹配失败！也未填写图片URL',
            );
        }

    }else{
        return array(
            'stat'=>202,
            'msg'=>'后端未收到前端图片数据',
        );
    }
}



/**
 * 物理删除图片
 */
function deleteImage($pid){
    $imgId = $pid; // 相册id
    $dao = new photosDAO();
    $photoInfo = $dao->getPhotoInfoById($imgId);

    $photo_path = $photoInfo['FS_PATH'];


    $photo_src_org = $photoInfo['PATH'];
    $photo_src_tmb = $photoInfo['THUMB'];
    $fileName = basename($photo_path);
    $photo_tmb_path = dirname($photo_path)."/thumbs/".$fileName;

    // 删库
    $dao->delImageById($imgId);
    // 删文件
    if($photo_path){
        /*新建回收站*/
        $dustbin_dir = DUSTBIN_DIR.date('Ymd').'/';
        $dustbin_thumb_dir = $dustbin_dir.'thumbs/';
        if (!file_exists($dustbin_dir)) {
            if (mkdir($dustbin_dir)) {
                chmod($dustbin_dir, 0777);
            } else {
                return array(
                    "msg" => 'faile to create ' . $dustbin_dir . 'maybe the path you have no permit!<br>'
                );
            };
        }

        // 创建缩略图目录
        if (!file_exists($dustbin_thumb_dir)) {
            if (mkdir($dustbin_thumb_dir)) {
                chmod($dustbin_thumb_dir, 0777);
            } else {
                return array(
                    "msg" => 'faile to create ' . $dustbin_thumb_dir . 'maybe the path you have no permit!<br>'
                );
            };
        }

        rename(IMAGE_BED_DIR.$photo_path,$dustbin_dir.$fileName );
        rename(IMAGE_BED_DIR.$photo_tmb_path,$dustbin_thumb_dir.$fileName );

        return array(
            'stat'=>200,
            'msg'=>"{$imgId}在数据库中删除，{$fileName}移动到服务器回收站",
        );

    }else{
        return array(
            'stat'=>200,
            'msg'=>"数据库删除成功，但服务器无此文件",
            'imgsrc'=>$photo_src_org
        );
    }

}