<?php
include_once "functions.php";
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
<audio src="media/783.wav" hidden="true"></audio>
<audio src="media/1374.wav" hidden="true"></audio>
<!--导航部分-->
<img src="img/ui/logo.png" id="logo">
<div id="nav" style="overflow: visible">
    <div id="index_nav">
        <ul>
            <li class="home_menu" id="menu_home"  data-tar="home">HOME 主页</li>
            <li class="home_menu" id="menu_photos" data-tar="photos">GALLERY 相册</li>
            <li class="home_menu" id="menu_contact" data-tar="contact">CONTACT 联系</li>
<!--            <a href="http://terryyhq.diandian.com/" target="_blank"><li id="menu_stories" style="background-image: url('img/ui/stories.png');width: 65px"></li></a>-->
        </ul>
    </div>
</div>

<!--面板部分-->
<div id="boards">
    <!--面板 home-->
    <div id="home" class="contentBoard currentboard">
        <div id="slider" style="position:relative;width: 100%;height: 100%">
            <!--封面-->
            <?php
            $coverList = getCoverList();
            try{
                foreach($coverList as $item){
                    echo "<div class=\"sliderImg\" data-rdrt=\"".$item['LINK']."\" style=\"background-image: url('".$item['PATH']."');\"></div>";
                }
            }catch (Exception $e){
                var_dump($e->getTrace());
            } ?>
        </div>
    </div>

    <!--面板 相册-->
    <div id="photos" class="contentBoard">
        <div id="photos_nav">
            <ul>
                <!--导航条-->
                <?php
                $groupList = getGroupList();
                try{
                    foreach($groupList as $item) {
                        echo "<li class=\"groups\" data-groupid=\"" . $item['id'] . "\"><a href=\"javascript:void(0);\">" . $item['NAME'] . "</a></li>";
                    }
                }catch (Exception $e){
                    var_dump($e->getTrace());
                } ?>
            </ul>
        </div>
        <div id="albums">

        </div>
    </div>


    <!--面板 contact-->
    <div id="contact" class="contentBoard">
        <div class="frame">
            <div class="contact_blocks" style="background-image: url('./img/ui/avt.jpg')"></div>
            <div class="contact_blocks">
                <span class="lines">我们是一群有爱的摄影师</span>
                <span class="lines">我们拍摄不同败材的照片</span>
                <span class="lines">我们的拍摄对象大多是普通人</span>
                <span class="lines">我们追求真实自然精致的画面</span>
                <span class="lines">我们认为照片承载的是一段记忆</span>
                <span class="lines">是一种情感的表达,更是意犹未尽的故事.</span>
                <span class="lines">我们期待遇见有故事的你。</span>
                <span class="lines"></span>
                <span class="lines">We are a team of photographers with love and pattence.</span>
                <span class="lines">We shoot different kinds of photos.</span>
                <span class="lines">Our customers are mostly ordinary people.</span>
                <span class="lines">We pursue the pictures which are true, natural and exquisite. </span>
                <span class="lines">We consider that a photo is like a kind of memory,</span>
                <span class="lines">a kind of emotion and a boundless story</span>
                <span class="lines">We are tooking forward to meeting you and your story</span>
            </div>
            <div class="contact_blocks">
                <div id="small_slider">
                    <?php
                        try{
                            foreach($coverList as $item){
                                echo "<div style=\"background-image: url('".$item['PATH']."')\"></div>";
                            }
                        }catch (Exception $e){
                            var_dump($e->getTrace());
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!--底部-->
<div id="fot"><span>Copyright ©2014 Terryyhq.com Powered By Adol Version 1.0.0 - <a href="http://www.miitbeian.gov.cn/">湘ICP备:24320053609</a></span></div>
</body>



<script src="js/jquery.customer.slider.js"></script>
<script src="js/jquery.customer.board_switch.js"></script>
<script src="js/jquery.customer.photo_veiwer.js"></script>
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
$(document).ready(function () {
    // 相册组容器
    var $albumGroupFrame = $("#albums");
    var $photosNav = $('#photos_nav');

    // 首页出现
    $('.contentBoard').eq(0).moveDownIn(1000).queue(function(){
        var $slider = $('#slider');
        $slider.height("100%");
        var slider = $slider.buildSlider();
        setInterval(function () {
            slider.slidernext()
        }, 2000);
    });

    // 注册各个分组按钮点击事件
    $photosNav.delegate(".groups","click",function () {
        var group_id = this.getAttribute("data-groupid");
        var albumList = albumPool[group_id]['list'];
        var innerHtml = "";
        for(var i=0;i<albumList.length;i++){
            var albumId = albumList[i]['id'];
            var coverSrc = albumList[i]['THUMB']?albumList[i]['THUMB']:albumList[i]['COVER'];
            var albumName = albumList[i]['NAME'];
            var albumDesc = albumList[i]['DESC'];
            innerHtml += ("<div class='album' data-id='"+albumId
                +"' data-cover='"+coverSrc
                +"' data-name='"+albumName
                +"' data-desc='"+albumDesc+"'>"
                + "<div class='cover'></div><div class='albumRemark'>"+albumName+"</div></div>")
        }
        $albumGroupFrame.html(innerHtml);
        $albumGroupFrame.resizeAlbumsSize();
    });

    //  注册主菜单点击事件
    $("#index_nav").find(">ul>li").click(function(){
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
            // 展开相册组时,默认点击第一个相册
            if(id =='photos'){
                if($albumGroupFrame.children().length<=0){
                    $('.groups').eq(0).click();
                }
            }

            // 展开联系人页面时,默认修改样式
            if(id =='contact'){
                var $this = $('#contact').find('.frame');
                var frameHeight =$this.height();
                var frameWidth =$this.width();
                var aspectRatio = frameWidth/frameHeight;
                var small_slider = $("#small_slider");
                var $all_blocks = $('.contact_blocks');
                // 修正宽高
                if(aspectRatio>1){
                    // 外围框架
                    $this.css({
                        height  :   $this.parent().height() *.6,
                        top     :   $this.parent().height() * .2,
                        overflow:   "hidden"
                    });

                    // 三区块
                    $all_blocks.css({
                        width:"33%",
                        height:"100%",
                        float:"left"
                    });

                    // 中间区块行高
                    $(".contact_blocks .lines").each(function(){
                        var $this = $(this);
                        $this.css({
                            height:$all_blocks.height()/15
                        })
                    });
                }else{
                    // 竖向样式

                    // 三区块
                    $all_blocks.css({
                        width:"100%",
                        padding:0,
                        marginBottom:10
                    });

                    // 第一块
                    $all_blocks.eq(0).css({
                        height:$(this).width()*3/4
                    });


                    // lines样式
                    $('.lines').css({
                        padding:0
                    });

                    // slider样式重置
                    small_slider.css({
                        width:"100%",
                        height:$(this).width()/2
                    })

                }

                // 构建个人介绍页的silder
                small_slider.buildSmallSlider();

            }
        }).dequeue();
    });

    // 计算相册图片大小和位置
    $.fn.resizeAlbumsSize = function(){
        // 基本数据
        var $this = $(this);
        var frameHeight =$this.height();
        var frameWidth =$this.width();
        var verticalCount,horizontalCount;
        // 设置横向竖向图片数
        var aspectRatio = frameWidth/frameHeight;
        if(aspectRatio>=1){
            // 宽高比不同的情况下的展示方式
            verticalCount=3;
            horizontalCount=4;
        }else{
            verticalCount=4;
            horizontalCount=3;
        }

        var albumFullHeight =frameHeight*(1/verticalCount);
        var albumFullWidth = frameWidth*(1/horizontalCount);
        var albumHeight = albumFullHeight*.95;//根据竖向个数计算相册高度-除去边距
        var albumWidth = albumFullWidth*.95;

        var $innerAlbums = $this.find('.album');
        // 设置相册容器宽度和滚动
        $this.css({
            position:"relative",
            minWidth:"100%",
            overflowY:"hidden",
            overflowX:"auto"
        });

        // 设置相册的大小/位置/背景图
        $innerAlbums.each(function(index,ele){
            var $$this =$(ele);
            // 每张相册的序号
            var position = {// x:0-n,y:0-3
                y:index%verticalCount,
                x:Math.ceil((index+1)/verticalCount)-1
            };

            // 每张全尺寸相册的位置
            var albumFullPosition = {
                top:position.y*albumFullHeight,
                left:position.x*albumFullWidth
            };

            // 每张净尺寸相册的位置
            var albumPosition = {
                // 上下左右边距相等
//                top: albumFullPosition.top + (albumFullHeight-albumHeight)/2,
//                left: albumFullPosition.left + (albumFullWidth-albumWidth)/2

                // 上左顶格 3px 表示与副菜单对齐,副菜单有3px的padding
                top: albumFullPosition.top + 3,
                left: albumFullPosition.left + 3
            };

            // 布局
            $$this.css({
                position:"absolute",
                opacity:1,
                height:albumHeight,
                width:albumWidth,
                top:albumPosition.top,
                left:albumPosition.left,
                backgroundImage:"url("+$$this.attr('data-cover')+")"
            });

            // 图片加载动画
            var _img = document.createElement("img");
            _img.src = $$this.attr('data-cover');
            $(_img).load(function(){
                // 图片加载完成时
                $$this.find('.cover').css({
                    width:"0"
                });
            });

        });
    };

    // 相册的点击事件
    $albumGroupFrame.delegate(".album","click",function(){
        $(this).createPhotoViewer();
        soundPlay("media/1374.wav")
    });

    // 相册鼠标移入的声音
    $albumGroupFrame.delegate(".album","mouseover",function(e){
        e.preventDefault();
        soundPlay("media/783.wav")
    });

    // 相片鼠标移入的声音
    $albumGroupFrame.delegate(".thumbBox","mouseover",function(e){
        soundPlay("media/783.wav")
    });

    // 二级菜单鼠标移入的声音
    $photosNav.delegate(".groups,.backOff","mouseover",function(e){
        e.stopPropagation();
        soundPlay("media/783.wav")
    });

    // 二级菜单鼠标移入的声音
    $photosNav.delegate(".groups,.backOff","click",function(){
        soundPlay("media/783.wav")
    });

    // 防止浮层起泡
    $photosNav.delegate(".cover,.albumRemark","mouseover",function(e){
        e.stopPropagation();
    });

});

// 声音播放
function soundPlay(src) {
    var sound = document.createElement("audio");
    sound.src = src;
    sound.play();
    sound.remove();
}


//定义左右动画
</script>
<!--IE 8无法识别百分比高度，如果要兼容IE8，就必须要做针对性优化。-->
</html>
