<?php
//error_reporting(0);
session_start();
$pageID='home';
require($_SERVER['DOCUMENT_ROOT'] . '/backstage/definitions.php');
include(WIDGETS_DIR.'head.php');
?>


<!--登录框-->

    <div class="page-header" style="width: 960px;margin: 60px auto 0 auto">
        <h1>Home page header <small>Subtext for header</small></h1>
    </div>
    <div class="jumbotron" style="width: 960px;margin: 20px auto;border-radius: 10px;padding: 30px;">
        <h2>Hello backstage</h2>
        <p>轻便后台管理系统</p>
        <div class="row">
            <div class="col-xs-6 col-md-3">
                <a href="#" class="thumbnail">
                    <img src="http://tangweimm.com/img/foodveiw/4.jpg" alt="...">
                </a>
            </div>

            <div class="col-xs-6 col-md-3">
                <a href="#" class="thumbnail">
                    <img src="http://tangweimm.com/img/foodveiw/3.jpg" alt="...">
                </a>
            </div>

            <div class="col-xs-6 col-md-3">
                <a href="#" class="thumbnail">
                    <img src="http://tangweimm.com/img/foodveiw/2.jpg" alt="...">
                </a>
            </div>

            <div class="col-xs-6 col-md-3">
                <a href="#" class="thumbnail">
                    <img src="http://tangweimm.com/img/foodveiw/1.jpg" alt="...">
                </a>
            </div>
        </div>
    <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a></p>
</div>

<?php
include WIDGETS_DIR."foot.php";
?>