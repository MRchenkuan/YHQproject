<!--here is head-->
<?php
error_reporting(0);
session_start();
$pageID='news';
require_once('../definitions.php');
include(WIDGETS_DIR.'head.php');
?>

<!--content-->
<?php
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

?>

<div class="panel panel-default" style="width: 960px;margin: 60px auto 0 auto">
    <!-- Default panel contents -->
    <div class="panel-heading"><span class="glyphicon glyphicon-calendar"></span> 新闻预览</div>
    <div class="panel-body">
    <?php
        $newsfile = fopen($news['text'],'r') or die('can not find newsfiles,because no newsfiles file found');
        echo htmlspecialchars_decode(fread($newsfile,filesize($news['text'])));
        fclose($newsfile);
    ?>
    </div>
</div>


<!--here this foot-->
<?php
include WIDGETS_DIR."foot.php";
?>