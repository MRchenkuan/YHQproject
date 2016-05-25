<?php
require_once "functions.php";
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="keywords" content="摄影师喻虎奇的官网，喻虎奇，摄影师喻虎奇，长沙摄影师，长沙摄影师喻虎奇，长沙旅拍摄影，独立摄影师，长沙独立摄影师">
    <meta name="description" content="喻虎奇 我是一名摄影师。我拍摄不同题材的照片。我的拍摄对象大多是普通人。我追求真实自然精致的画面。我认为照片承载的是一段记忆，是一种情感表达，更是一个意犹未尽的故事……期待遇见有故事的你！">
    <script src="js/jQuery1.9.js"></script>
    <script src="js/jquery.easing.min.js"></script>
    <link rel="shortcut icon" type="image/jpeg" href="img/ui/logo.png">
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <title>VISION</title>
</head>
<style>


</style>
<body>
<!--音乐部分-->
<audio src="media/Laura_Story_Grace.mp3" hidden="true" autoplay="true" loop="true"></audio>
<!--导航部分-->
<div id="nav">
    <span style="position:absolute;left:110px;display: inline;color: #202020;line-height: 30px;">
        <img src="img/ui/logo.png" style="width: 100px;float: left;margin-right: 10px;opacity:.65;">
    </span>

    <div id="index_nav">
        <ul>
            <li id="menu_home" style="background-image: url('img/ui/home.png') ;width: 65px" data-tar="home"></li>
            <li id="menu_photos" style="background-image: url('img/ui/gallery.png');width: 70px" data-tar="photos"></li>
            <li id="menu_contact" style="background-image: url('img/ui/contact.png');width: 95px" data-tar="contact"></li>
            <a href="http://terryyhq.diandian.com/" target="_blank"><li id="menu_stories" style="background-image: url('img/ui/stories.png');width: 65px"></li></a>
            <script>
                $('#index_nav').find('li').each(function () {
                    var t = $(this);
                    t.mouseover(function () {
                        if (t.is(':animated'))return;
                        t.stop().css({top: 0}).animate({'margin-top': 5})
                    });
                    t.mouseout(function () {
                        t.stop().css({top: 0}).animate({'margin-top': 0})
                    });
                })
            </script>
        </ul>
    </div>
</div>

<!--面板部分-->
<div id="boards">
    <!--面板 home-->
    <div id="home" class="contentBoard currentboard">
        <div id="slider" style="border: 1px solid grey ;position:relative;width: 100%;height: 100%">
            <!--封面-->
            <?php
            $coverList = getCoverList();
            foreach($coverList as $item){
                echo "<div class=\"sliderImg\" data-rdrt=\"".$item['LINK']."\" style=\"background-image: url('".$item['PATH']."');\"></div>";
            } ?>
        </div>
    </div>

    <!--面板 相册-->
    <div id="photos" class="contentBoard"  style="border: 1px dashed grey;">
        <div id="photos_nav">
            <ul>
                <!--导航条-->
                <?php
                $groupList = getGroupList();
                foreach($groupList as $item){
                    echo "<li class=\"groups\" data-groupid=\"".$item['id']."\"><a href=\"javascript:void(0);\">".$item['NAME']."</a></li>";
                } ?>
            </ul>
        </div>
        <div id="albums" style="border:1px solid grey">
            <!--相册-->
            <?php
            $albumId = $groupList[0]['id'];
            $albumList = getAlbumList($albumId);
            foreach($albumList as $item){
                echo "<div class=\"album\" data-id=\"".$item['id']."\" >".$item['COVER']."<br>".$item['NAME']."<br>".$item['DESC']."</div>";
            } ?>
        </div>
    </div>


    <!--面板 contact-->
    <div id="contact" class="contentBoard" style="border: 1px dashed grey;">
        fdsafdsafdsafdsafdsfds

    </div>

</div>

<!--底部-->
<div id="fot"><span>Copyright ©2014 Terryyhq.com Powered By Adol Version 1.0.0 - <a href="http://www.miitbeian.gov.cn/">湘ICP备:24320053609</a></span></div>
</body>



<script src="js/jquery.customer.slider.js"></script>
<script src="js/jquery.customer.board_switch.js"></script>
<script src="js/jquery.customer.photo_wall.js"></script>
<script src="js/jquery.customer.ajax_maneger.js"></script>
<script>

    var albumPool =
        <?php
        $allGroup = array();
        foreach($groupList as $item){
            $oneGroup = array(
                "name"=>$item['NAME'],
                "list"=>getAlbumList($item['id'])
            );
            $allGroup[$item['id']] = $oneGroup;
        }
        echo json_encode($allGroup);
        ?>
    ;

//文档加载入口
var photosframe;
$(document).ready(function () {
    //    首页出现
    $('.contentBoard').eq(0).moveDownIn(1000).queue(function(){
        var $slider = $('#slider');
        $slider.height("100%");
        var slider = $slider.buildSlider();
        setInterval(function () {
            slider.slidernext()
        }, 2000);
    });





    boxjumping(document.getElementById('menu_home'), '20px', function () {
        boxjumping(document.getElementById('menu_photos'), '20px', function () {
            boxjumping(document.getElementById('menu_contact'), '20px', function () {
                boxjumping(document.getElementById('menu_stories'), '20px', function () {
                })
            });
        });
    });

//    注册各个分组按钮点击事件
    $('.groups').click(function () {
        var group_id = this.getAttribute("data-groupid");
        var albumList = albumPool[group_id]['list'];
        var innerHtml = "";
        for(var i=0;i<albumList.length;i++){
            innerHtml += "<div class='album' data-id='"+albumList[i]['id']+"' >"+albumList[i]['NAME']+"</div>"
        }
        $("#albums").html(innerHtml);
    });



    //  注册主菜单点击事件
    $("#index_nav>ul>li").click(function(){
        var id = $(this).attr('data-tar');
        //  滑下当前board
        if (!id || id === '')return;
        var targetElem = $("#" + id);
        var currentElem = $('.currentboard');
        if (currentElem.is(":animated"))return;
        //  滑出板块
        currentElem.moveDownOut(500).queue(function(){
            currentElem.removeClass("currentboard");
            targetElem.moveDownIn(1000);
            targetElem.addClass("currentboard");
        }).dequeue();


    });
});


//  定义各个板块出现的方法
var timer;//定时器

//  定义图片跳跃方法
function boxjumping(elem, distance, callback) {
    var t = $(elem);
    t.css({left: 0});
    t.animate({
        top: "-=" + distance
    }, 150, 'swing', function () {
        if (callback)callback();
    }).animate({
        top: "+=" + distance
    }, 600, 'easeInOutQuad');
}

//定义左右动画
</script>
<!--IE 8无法识别百分比高度，如果要兼容IE8，就必须要做针对性优化。-->
</html>
