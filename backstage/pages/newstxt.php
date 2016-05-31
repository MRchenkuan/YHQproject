<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../bootstrap/bootstrap-theme.min.css">

    <script src="../PUBLIC/javascripts/jquery.min.js"></script>
    <script src="../bootstrap/bootstrap.min.js"></script>
    <style>
        .input-group{margin: 10px auto;}
    </style>
    <title></title>
</head>
<body style="overflow-x: hidden;padding: 20px 5%;">
<!--content-->
<?php
require_once('../definitions.php');
/*连接数据库*/
$newsid = $_GET['id'];

/*--连接数据库--*/
if(strtoupper(DB_TYPE)=='FILE') {
    require_once(KODBC_PATH);
    $kodbc = new Kodbc('T_TABLE_NEWS');
    $news = $kodbc->getById($newsid);

}else{
    require_once(DATABASE_DAO_DIR."newsDAO.php");
    $dao = new newsDAO();
    $news = $dao->getNewsDataById($newsid);
}

$newsfile = fopen($news['text'],'r') or die('can not find newsfiles,because no newsfiles file found');
echo htmlspecialchars_decode(fread($newsfile,filesize($news['text'])));
fclose($newsfile);
?>
</body>
</html>