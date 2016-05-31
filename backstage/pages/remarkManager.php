<!--here is head-->
<?php
error_reporting(0);
session_start();
$pageID='remarkManager';
require_once('../definitions.php');
include(WIDGETS_DIR.'/head.php');

/*--连接数据库--*/
require_once('../DO/Kodbc.class.php');
?>

<div class="panel panel-default" style="width: 960px;margin: 80px auto 0 auto">
    <div class="panel-heading">
        <h3 class="panel-title">最新评论列表</h3>
    </div>

    <div class="panel-body">
        <table class="table">
            <thead>
                <tr><td>评论人</td><td>评论来源</td><td>评论内容</td><td>评论时间</td><td>操作</td><tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>2</td><td>1</td><td>2</td><td><button class="btn btn-default">审核</button></td></tr>
                <tr><td>1</td><td>2</td><td>1</td><td>2</td><td><button class="btn btn-default">审核</button></td></tr>
                <tr><td>1</td><td>2</td><td>1</td><td>2</td><td><button class="btn btn-default">审核</button></td></tr>
                <tr><td>1</td><td>2</td><td>1</td><td>2</td><td><button class="btn btn-default">审核</button></td></tr>
            </tbody>

        </table>
    </div>
</div>
<!--here this foot-->
<?php
include WIDGETS_DIR."foot.php";
?>