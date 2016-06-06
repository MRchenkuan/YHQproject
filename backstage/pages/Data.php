<?php
//error_reporting(0);
session_start();
require_once('../definitions.php');
//require_once(KODBC_PATH);

$APIID = $_GET['id'] ? $_GET['id'] : 'defaultMethod';

$DATABASEURL = DATA_TABLE_DIR.'T_TABLE_ADVTS.xml';

/**
 * 引入数据库
 */
require_once(DATABASE_DAO_DIR."photoAlbumDAO.php");
require_once(DATABASE_DAO_DIR."newsDAO.php");
require_once(DATABASE_DAO_DIR."photosDAO.php");

/*****************************************************
 *
 *                  转发路由
 *
 *****************************************************/

$config = array(
    'defaultMethod' => defaultMethod,
    'getNews' => getNews,
    'uploadImg' => uploadImg,
    'userLogin' => userLogin,
    'userVerify' => userVerify,
    'createAdvt' => createAdvt,
    'delAdvt' => delAdvt,
    'createNews' => createNews,
    'delNews' => delNews,
    'uploadImgAjax' => uploadImgAjax,
    'moveImage' => moveImage,
    'removeImage' => removeImage,
    'createAlbum' => createAlbum,
    'delAlbum' => delAlbum,
    'getNewsContent' => getNewsContent,
    'getPhotosByAlbumId' => getPhotosByAlbumId,
    'getAlbumsByGroupId'=>getAlbumsByGroupId
);
$config[$APIID]();

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
    $order = $_GET['order'];
    $imgsrc = $_GET['imgsrc'];

    if (!userVerify()) {
        /*验证用户登陆*/
        echo json_encode(array(
            'stat' => 201,
            'msg' => 'login failed!'
        ));
        echo false;
    }

    $Kodbc = new photoAlbumDAO();
    $dataitem = array(
        'ORDER' => $order,
        'stat' => 'disable',
        'PATH' => $imgsrc,
        'THUMB'=> $imgsrc
    );

    /*更新或者新增取决于ID是否存在*/
    if ($id && $id != '') {
        $Kodbc->updateItem($id, $dataitem);
    } else {
        $Kodbc->insertItem($dataitem);
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
    $id = $_GET['adid'];
    $Kodbc = new Kodbc('T_TABLE_ADVTS');
    echo $Kodbc->delById($id);
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
 *                  新闻的处理函数
 *
 *****************************************************/

function createNews(){

    $newsid     =   $_POST['newsid'];
    $title     =   $_POST['title'];
    $auth       =   $_POST['auth'];
    $origin     =   $_POST['origin'];
    $pubdata    =   $_POST['pubdata'];
    $stat    =   $_POST['stat'];
    $cover    =   $_POST['cover'];
    $text       =   htmlspecialchars($_POST['text']);
    $db         = $_POST['target'];

    $Kodbc = new Kodbc('T_TABLE_NEWS');

    /*************
     *
     * 储存大文本
     *
     ************/
    $newsDir = NEWS_FILE_DIR.date('Ymd').'/';
    if (!file_exists($newsDir)) {
        if (mkdir($newsDir)) {
            chmod($newsDir, 0777);
        } else {
            echo 'faile to create ' . $newsDir . 'maybe the path you have no permit!<br>';
        };
    }

    /*判断是修改文件还是新增文件*/
    if ($newsid && $newsid != ''){
        $item = $Kodbc->getById($newsid);
        $newsFileUrl = $item['text'];
    }else{
        $newsFileUrl = $newsDir . time() . '.newsfiles';
    }

    $fp = fopen($newsFileUrl, "w+");
    if(!fwrite($fp,$text)){
        fclose($fp);
        echo json_encode(array(
            'stat' => 201,
            'msg' => 'login failed!'
        ));
        throw new Exception("文件保存异常");
    }
    fclose($fp);
    /**************
     * 文件储存结束
     **************/

    if (!userVerify()) {
        /*验证用户登陆*/
        echo json_encode(array(
            'stat' => 201,
            'msg' => 'login failed!'
        ));
    }

    $dataitem = array(
        'stat' => $stat,
        'title' => $title,
        'auth' => $auth,
        'origin' => $origin,
        'pubdata' => $pubdata,
        'text' =>$newsFileUrl,
        'cover' => $cover
    );

    /*更新或者新增取决于ID是否存在*/
    if ($newsid && $newsid != '') {
        $Kodbc->updateItem($newsid, $dataitem);
    } else {
        $newsid = $Kodbc->insertItem($dataitem);
    }
    echo json_encode(array(
        'stat' => 200,
        'msg' => $newsid.' add sucess！',
        'articId'=>$newsid[0],
    ));
}

/**
 * 用户删除新闻方法
 * */
function delNews()
{
    $id = $_GET['newsid'];
    $Kodbc= new Kodbc(DATA_TABLE_DIR.'T_TABLE_NEWS.xml');
    echo $Kodbc->delById($id);
}

/**
 * 获得新闻内容的方法
 * */
function getNewsContent(){
    $id=$_REQUEST['newsid'];

    $Kodbc= new Kodbc(DATA_TABLE_DIR.'T_TABLE_NEWS.xml');

    $item= $Kodbc->getById($id);
    $contentsrc = $item['text'];
    $newsfile = fopen($contentsrc,'r') or die('can not find newsfiles,because no newsfiles file found');
    echo json_encode(array(
        'stat' => 200,
        'msg' => $id.' get sucess！',
        'content'=>htmlspecialchars_decode(fread($newsfile,filesize($contentsrc)))
    ));
//    fclose($newsfile);

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
    $uploaddir = IMAGE_BED_DIR . date('Ymd') . '/';
    if (!file_exists($uploaddir)) {
        if (mkdir($uploaddir)) {
            chmod($uploaddir, 0777);
        } else {
            echo 'faile to create ' . $uploaddir . 'maybe the path you have no permit!<br>';
        };
    }

    /*base64保存为图片，并写入数据库*/
    if($imgdatastring){
        //do someting for 保存图片
        if (preg_match('/^(data:\s*images\/(\w+);base64,)/', $imgdatastring, $result)){
            $type = $result[2];
            $uploadfileUrl = $uploaddir. time().'.'.$type;
            if (file_put_contents($uploadfileUrl, base64_decode(str_replace($result[1], '', $imgdatastring)))){
                //写入数据库
                $Kodbc = new Kodbc('T_TABLE_PHOTOBASE');
                $Kodbc->insertItem(array(
                        'albumid'=>$_POST['albumid'],
                        'stat'=>'active',
                        'remark'=>$_POST['remark'],
                        'imgsrc'=>$uploadfileUrl,
                        'pubdata'=> date('Y-m-d\TH:i')
                    )
                );

                echo json_encode(array(
                    'stat'=>200,
                    'imgurl'=>$uploadfileUrl,
                    'msg'=>'图片上传成功',
                ));
            }
        }else if($_POST['onlineurl']){
            /*如果没有图片但是有imgurl时*/
            $Kodbc = new Kodbc('T_TABLE_PHOTOBASE');
            $Kodbc->insertItem(array(
                    'albumid'=>$_POST['albumid'],
                    'stat'=>'active',
                    'remark'=>$_POST['remark'],
                    'imgsrc'=>$_POST['onlineurl'],
                    'pubdata'=> date('Y-m-d\TH:i')
                )
            );
            echo json_encode(array(
                'stat'=>200,
                'imgurl'=>$_POST['onlineurl'],
                'msg'=>'网络URL,图片添加成功',
            ));

        }else{
            echo json_encode(array(
                'stat'=>202,
                'imgurl'=>null,
                'msg'=>'图片字符串匹配失败！也未填写图片URL',
            ));
        }

    }else{
        echo json_encode(array(
            'stat'=>202,
            'msg'=>'后端未收到前端图片数据',
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
    $imgsrc = $_GET['imgsrc'];
    $filename=end(explode('/',$imgsrc));
    /*新建回收站*/
    $dashbindir = DUSTBIN_DIR.date('Ymd').'/';
    if (!file_exists($dashbindir)) {
        if (mkdir($dashbindir)) {
            chmod($dashbindir, 0777);
        } else {
            echo 'faile to create ' . $dashbindir . 'maybe the path you have no permit!<br>';
        };
    }

    $Kodbc = new Kodbc('T_TABLE_PHOTOBASE');
    if($_GET['imgid']){
        $imgid=$_GET['imgid'];
        $Kodbc->delById($imgid);
    }

    if(rename($imgsrc,$dashbindir.$filename )){
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"{$_GET['imgid']}在数据库中删除，{$filename}移动到服务器回收站",
        ));
        return true;
    }else{
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"数据库删除成功，但服务器无此文件",
            '$imgsrc'=>$imgsrc,
            '$dashbindir.$filename'=>$dashbindir.$filename,
        ));
        return true;
    }
}


/**
 * 创建相册
 */
function createAlbum(){
    try{

        $albumname = $_GET['albumname'];
        $stat = $_GET['stat'];

        $Kodbc = new photoAlbumDAO();
        $Kodbc->updateItem($_GET['albumid'],array(
            'stat'=>$stat,
            'NAME'=>$albumname,
        ));
        echo json_encode(array(
            'stat'=>200,
            'msg'=>"《{$albumname}》修改成功",
        ));
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
    if($_GET['albumid']){
        $albumid = $_GET['albumid'];
        $Kodbc = new Kodbc(DATA_TABLE_DIR.'T_TABLE_PHOTO_ALBUM.xml');
        $item = $Kodbc->getById($albumid);
        if($item['count']>0){
            echo json_encode(array(
                'stat'=>200,
                'msg'=>"相册内有照片，需要先清空相册！",
            ));
        }else{
            $Kodbc->delById($albumid);
            echo json_encode(array(
                'stat'=>200,
                'msg'=>"{$albumid}:成功删除",
            ));
        }
    }else{
        echo json_encode(array(
            'stat'=>202,
            'msg'=>"并没有找到什么卵ID",
        ));
    }
}