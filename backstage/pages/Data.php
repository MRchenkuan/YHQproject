<?php
//error_reporting(0);
session_start();
require_once('../definitions.php');

$APIID = $_GET['id'] ? $_GET['id'] : 'defaultMethod';

$DATABASEURL = DATA_TABLE_DIR.'T_TABLE_ADVTS.xml';

/**
 * 引入数据库
 */
require_once(DATABASE_DAO_DIR."photoAlbumDAO.php");
require_once(DATABASE_DAO_DIR."newsDAO.php");
require_once(DATABASE_DAO_DIR."photosDAO.php");
require_once(DATABASE_DAO_DIR."coversDAO.php");

/**
 * 引入工具
 */
require_once(TOOLS_PATH."imgUploader.php");


/*****************************************************
 *
 *                  转发路由
 *
 *****************************************************/

$config = array(
    'test'=>test,
    'defaultMethod' => defaultMethod,
    'uploadImg' => uploadImg,
    'userLogin' => userLogin,
    'userVerify' => userVerify,
    'createAdvt' => createAdvt,
    'delAdvt' => delAdvt,
    'uploadImgAjax' => uploadImgAjax,
    'moveImage' => moveImage,
    'removeImage' => removeImage,
    'createAlbum' => createAlbum,
    'delAlbum' => delAlbum,
    'getPhotosByAlbumId' => getPhotosByAlbumId,
    'getAlbumsByGroupId'=>getAlbumsByGroupId,
    'setCover'=>setCover,
    'uploadCover'=>uploadCover
);
$config[$APIID]();

/*****************************************************
 *
 *                  test
 *
 *****************************************************/

function test(){
    $dao = new photosDAO();
    $count = $dao->addImageInfo(array(
        'ALBUMID'=>"12",
        'PATH'=>"fdsafdsafsdafsaasf",
        'THUMB'=>"fdsafdsafasfdf",
    ));
    echo 123131;
    var_dump($count);
    echo 123131;
    var_dump($dao);
    echo 123131;
}



/*****************************************************
 *
 *                  controllor
 *
 *****************************************************/
/**
 * 根据相册分类获取所有相册
 */
function getAlbumsByGroupId(){

    $groupId = $_GET['groupId'];
    $db = new photoAlbumDAO();
    echo json_encode($db->getAlbumsByGroupId($groupId));
}
/**
 * 根据相册ID获取所有照片
 */
function getPhotosByAlbumId(){

    $albumId = $_GET['albumId'];
    $db = new photoAlbumDAO();
    echo json_encode($db->getPhotosByAlbumId($albumId));
}

/*****************************************************
 *
 *                  通用的处理函数
 *
 *****************************************************/

/**
 * 用户登陆的方法
 * */
function userLogin()
{

    $username = $_GET['username'];
    $password = $_GET['password'];
    $trylimit = 10;//最大登录尝试次数

//    if (!$_COOKIE['_auth']) return;

    if ($_SESSION['trycount'] && $_SESSION['trycount'] >= $trylimit) {
        echo json_encode(array(
            'stat' => 205,
            'msg' => 'login failed! too frequently you try!'
        ));
        return;
    }
    if ($username == "ss" && $password == "ss") {
        /*记录session值并写入cookie*/
        setcookie('SSID', session_id(),time()+43200);
        $_SESSION['stat'] = 'login';
        $_SESSION['Verifyed'] = true;
        $_SESSION['trycount'] = 1;
        echo json_encode(array(
            'stat' => 200,
            'msg' => 'login sucessed!'
        ));
    } else {
        if (!$_SESSION['trycount']) {
            $_SESSION['trycount'] = 0;
        }
        $_SESSION['trycount'] += 1;

        echo json_encode(array(
            'stat' => 201,
            'msg' => 'login failed!'
        ));
    }
}

/**
 * 用户登陆态验证的方法
 * */
function userVerify()
{
    if ($_SESSION['stat'] == 'login') {
        return true;
    } else {
        return false;
    }
}


/*****************************************************
 *
 *                  广告的处理函数
 *
 *****************************************************/

/**
 * 用户创建广告的方法
 * */
function createAdvt()
{
    $id = $_GET['adid'];
    $link = $_GET['link'];
    $order = $_GET['order'];
    $cover = $_GET['cover'];

    if (!userVerify()) {
        /*验证用户登陆*/
        echo json_encode(array(
            'stat' => 201,
            'msg' => 'login failed!'
        ));
        echo false;
    }

    $dao = new coversDAO();
    $dataitem = array(
        'COVER' => $cover,
        'ORDER' => $order,
        'LINK'=> $link
    );

    /*更新或者新增取决于ID是否存在*/
    if ($id && $id != '') {
        $dao->updateCover($id, $dataitem);
    } else {
        $dao->addCover($dataitem);
    }
    echo json_encode(array(
        'stat' => 200,
        'msg' => 'add sucess！'
    ));
}

/**
 * 用户删除广告方法
 * */
function delAdvt()
{
    $id = $_POST['adid'];
    $coverDao = new coversDAO();
    $dao = new photoAlbumDAO();

    $imgId = $_POST['imgid']; // 相册id
    $dao = new photosDAO();
    $photoInfo = $dao->getPhotoInfoById($imgId);

    $photo_path = $photoInfo['FS_PATH'];
    $photo_src_org = $photoInfo['PATH'];
    $photo_src_tmb = $photoInfo['THUMB'];
    $fileName = basename($photo_path);

    /*新建回收站*/
    $dustbin_dir = DUSTBIN_DIR.date('Ymd').'/';
    if (!file_exists($dustbin_dir)) {
        if (mkdir($dustbin_dir)) {
            chmod($dustbin_dir, 0777);
        } else {
            echo 'faile to create ' . $dustbin_dir . 'maybe the path you have no permit!<br>';
        };
    }


    // 删库
    $dao->delImageById($imgId);

    // 移图
    if(rename(IMAGE_BED_DIR.$photo_path,$dustbin_dir.$fileName )){
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"{$imgId}在数据库中删除，{$fileName}移动到服务器回收站",
        ));
        return true;
    }else{
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"数据库删除成功，但服务器无此文件",
            '$imgsrc'=>$photo_src_org,
            '$dustbin_dir.$filename'=>$dustbin_dir.$fileName,
        ));
        return true;
    }
}

/**
 * 默认返回的方法
 * */
function defaultMethod()
{
    echo 'api unformated!';
}

/**
 * 图片上传的方法
 * */
function uploadImg()
{
    $uploaddir = STATIC_DIR.'images/' . date('Ymd') . '/';
    if (!file_exists($uploaddir)) {
        if (mkdir($uploaddir)) {
            chmod($uploaddir, 0777);
        } else {
            echo 'faile to create ' . $uploaddir . 'maybe the path you have no permit!<br>';
        };
    }
    $uploadfileUrl = $uploaddir . time() . '.jpg';

    if ($_FILES['userfile']['error'] !== 0) {
        echo 'upload failed! error code:' . $_FILES['userfile']['error'];
        var_dump($_FILES['userfile']);
    } else {
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfileUrl)) {
            /*********
             * 记录入库
             ********/
            $Kodbc = new Kodbc('T_TABLE_PHOTOBASE');
            $Kodbc->insertItem(array(
                'albumid'=>'0',
                'remark'=>'from uploadImg',
                'imgsrc'=>$uploadfileUrl,
                'pubdata'=>date('Y-m-d\TH:i')
            ));

            /*********
             * 页面输出
             ********/
            echo "<body style='padding: 0;margin: 0'>";
            echo "<form style='padding: 0;margin: 0;' enctype='multipart/form-data' action='Data.php?id=uploadImg' method='POST' name='form'>";
            echo "<img id='uploadCallBack-ImgSrc' style='height:100%;max-width: 300px;' src='" . $uploadfileUrl . "'>";
            echo "<input style='float: right' id='userfile' name='userfile' type='file' onchange=\"document.getElementById('uploadform').submit()\">";
//                echo "<input style='float: right' type='submit' value='上传图片'>";
            echo "</body>";
            echo "</form>";
            // header($uploadfileUrl); // 此处有问题 导致服务器报500
        } else {
            header('#');
        }
    }
}



/*****************************************************
 *
 *                  图库的处理函数
 *
 *****************************************************/
/**
 * 异步上传图片
 */
function uploadImgAjax()
{

    $imgdatastring = $_POST['imgDataString'] or null;
    $albumId = $_POST['albumid'];
    $onlineurl = $_POST['onlineurl'];

    $resulte = uploadImage(array(
        "imgDataString"=>$imgdatastring,
        "albumid"=>$albumId,
        "onlineurl"=>$onlineurl,
    ));

    echo json_encode($resulte);
}

/**
 * 图片设为相册封面
 */
function setCover(){
    $photoId = $_POST['photoId'];
    if(!$photoId){
        echo json_encode(array(
            'stat'=>202,
            'msg'=>'相册ID不存在',
        ));
        return;
    }
    $dao = new photoAlbumDAO();
    $dao->setCover($photoId);
    echo json_encode(array(
        'stat'=>200,
        'msg'=>'封面设置成功'
    ));
}

/**
 * 上传封面照片
 */
function uploadCover()
{
    $imgdatastring = $_POST['imgDataString'] or null;
    $albumId = $_POST['albumid'];
    $order = $_POST['order'];
    $link = $_POST['link'];
    $onlineurl = $_POST['onlineurl'];

    // 上传图片
    $resulte = uploadImage(array(
        "imgDataString"=>$imgdatastring,
        "albumid"=>$albumId,
        "onlineurl"=>$onlineurl,
    ));

    // 设置封面
    if($resulte["pid"]){
        $coverDao = new coversDAO();
        // 设为封面
        $coverDao->addToCover(array(
            "COVER"=>$resulte["pid"],
            "ORDER"=>$order,
        ));
        echo json_encode(array(
            "stat"=>200,
            "msg"=>"封面上传成功!"
        ));
    }else{
        echo json_encode(array(
            "stat"=>200,
            "msg"=>"图片上传失败!"
        ));
    }


}

/**
 * 相册间移动图片
 */
function moveImage(){
    $albumid=$_GET['albumid'];
    $Kodbc = new Kodbc('T_TABLE_PHOTOBASE');
    if($_GET['imgid']){
        $imgid=$_GET['imgid'];
        $Kodbc->updateItem($imgid,array(
            'albumid'=>$albumid
        ));
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"{$imgid}移动到{$albumid}",
        ));
    }elseif($_GET['imgsrc']){
        $imgsrc=$_GET['imgsrc'];
        $Kodbc->insertItem(array(
            'albumid'=>'0',
           'imgsrc'=>$imgsrc,
            'pubdata'=>date('Y-m-d\TH:i'),
            'remark'=>'from images binding'
        ));
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"{$imgsrc}绑定到{$albumid}",
        ));
    }else{
        echo json_encode(array(
            'stat'=>202,
            'msg'=>"既没有图片ID也没有图片地址"
        ));
    }

}


/**
 * 物理删除图片
 */
function removeImage(){
    $imgId = $_POST['imgid']; // 相册id
    $dao = new photosDAO();
    $photoInfo = $dao->getPhotoInfoById($imgId);

    $photo_path = $photoInfo['FS_PATH'];
    $photo_src_org = $photoInfo['PATH'];
    $photo_src_tmb = $photoInfo['THUMB'];
    $fileName = basename($photo_path);

    /*新建回收站*/
    $dustbin_dir = DUSTBIN_DIR.date('Ymd').'/';
    if (!file_exists($dustbin_dir)) {
        if (mkdir($dustbin_dir)) {
            chmod($dustbin_dir, 0777);
        } else {
            echo 'faile to create ' . $dustbin_dir . 'maybe the path you have no permit!<br>';
        };
    }


    // 删库
    $dao->delImageById($imgId);

    // 移图
    if(rename(IMAGE_BED_DIR.$photo_path,$dustbin_dir.$fileName )){
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"{$imgId}在数据库中删除，{$fileName}移动到服务器回收站",
        ));
        return true;
    }else{
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"数据库删除成功，但服务器无此文件",
            '$imgsrc'=>$photo_src_org,
            '$dustbin_dir.$filename'=>$dustbin_dir.$fileName,
        ));
        return true;
    }
}


/**
 * 创建相册
 */
function createAlbum(){
    try{
        $album_id = $_POST['albumid'] or null;
        $album_name = $_POST['albumname'] or null;
        $group_id = $_POST['groupid'] or null;
        $album_desc = $_POST['desc'] or null;
        $album_stat = $_GET['stat'] or null;

        $album_info = array(
            "NAME"=>$album_name,
            "GROUP"=>$group_id,
            "DESC"=>$album_desc,
            "stat"=>$album_stat,
            );
        $dao = new photoAlbumDAO();
        if($album_id){
            $dao->updateAlbumById($album_id,$album_info);
            echo json_encode(array(
                'stat'=>200,
                'msg'=>"《{$album_name}》修改成功",
            ));
        }else{
            $dao->addAlbumById($album_info);
            echo json_encode(array(
                'stat'=>200,
                'msg'=>"《{$album_name}》新增成功",
            ));
        }
    }catch (Exception $e){
        echo json_encode(array(
            'stat'=>202,
            'msg'=>"出现异常:{$e}",
        ));
    }

}

/**
 * 删除相册
 * */
function delAlbum(){
    if($_POST['albumid']){
        $albumid = $_POST['albumid'];
        $dao = new photoAlbumDAO();
            $dao->updateAlbumById($albumid,array("IS_VALID"=>"N"));
            echo json_encode(array(
                'stat'=>200,
                'msg'=>"相册删除成功",
            ));
//        }
    }else{
        echo json_encode(array(
            'stat'=>202,
            'msg'=>"并没有找到什么卵ID",
        ));
    }
}
