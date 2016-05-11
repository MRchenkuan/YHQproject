<?php
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
            <li id="menu_home" style="background: url('img/ui/home.png') no-repeat;background-size: contain;width: 65px"
                onclick="displayBoard('home')"></li>
            <li id="menu_photos"
                style="background: url('img/ui/gallery.png') no-repeat;background-size: contain;width: 70px"
                onclick="displayBoard('photos')"></li>
            <li id="menu_contact"
                style="background: url('img/ui/contact.png') no-repeat;background-size: contain;width: 95px"
                onclick="displayBoard('contact')"></li>
            <a href="http://terryyhq.diandian.com/" target="_blank"><li id="menu_stories"
                style="background: url('img/ui/stories.png') no-repeat;background-size: contain;width: 65px"></li></a>
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


<!--各种面板 home-->
<div id="home" class="contentBoard currentboard">
    <div id="slider"
         style="height: 450px;width: 1024px;margin: 0 auto;font-family:'Arial','Times New Roman', Times, serif;color: grey ">

        <!--五图版本-->
        <div class="sliderImg" data-rdrt="./" style="background-image: url('img/home/1.jpg');"></div>
        <div class="sliderImg" data-rdrt="./" style="background-image: url('img/home/2.jpg');"></div>
        <div class="sliderImg" data-rdrt="./" style="background-image: url('img/home/3.jpg');"></div>
        <div class="sliderImg" data-rdrt="./" style="background-image: url('img/home/4.jpg');"></div>
        <div class="sliderImg" data-rdrt="./" style="background-image: url('img/home/5.jpg');"></div>

    </div>
</div>

<!--各种面板 contact-->
<introduce id="contact" class="contentBoard" style="font-family:'微软雅黑',Arial,'黑体';color: #202020">
    <div id="contact-frame" style="height: 350px;width: 1024px;margin: 20px auto;">
        <img src="img/contact/1.jpg" alt="" style="height: 350px;"/>

        <div style="height:350px;width:530px;float: right;position: relative">
            <div style="height: 220px;border-bottom: 1px solid grey">
                <span style="display: block">About</span>

                <div class='abouttext' style="width: 180px;">
                    <br>
                    喻虎奇 我是一名摄影师。<br>
                    我拍摄不同题材的照片。<br>
                    我的拍摄对象大多是普通人。<br>
                    我追求真实自然精致的画面。<br>
                    我认为照片承载的是一段记忆，<br>
                    是一种情感表达，<br>
                    更是一个意犹未尽的故事……<br>
                    期待遇见有故事的你！<br>
                </div>
                <div class='abouttext' style="width: 348px;">
                    <br>
                    Terry Yu<br>
                    I am a photographer.<br>
                    I shoot different kinds of photos.<br>
                    My customers are mostly ordinary people.<br>
                    I pursue the pictures which are true, natural and exquisite.<br>
                    I consider that a photo is like a kind of memory,<br>
                    a kind of emotion and a boundless story.<br>
                    I am looking forward to meeting you and your story.<br>
                    <br>
                </div>
            </div>
            <div style="position: absolute;bottom: 0">
                <div class="erweima"><span>Wechat</span><img src="img/contact/2.png"></div>
                <div class="erweima"><span>Weibo</span><img src="img/contact/3.png"></div>
                <div class="erweima" style="height: 123px;width: 240px;">
                    <span></span>
                    wechat: terryyhq<br>
                    QQ：444010958<br>
                    Weibo: weibo.com/terryyhq<br>
                    Email: 444010958@qq.com<br>
                    Instagram: terryyhq
                </div>
            </div>
        </div>
    </div>
</introduce>

<!--各种面板 相册-->
<div id="photos" class="contentBoard">
    <span id="leftward" style="background:url('img/ui/left.png');background-size: contain"> </span>
    <span id="rightward" style="background:url('img/ui/right.png');background-size: contain"> </span>

    <div id="photos_nav">
        <ul>
            <li id="prewedding"><a href="javascript:void(0);">PREWEDDING</a></li>
            <li id="wedding"><a href="javascript:void(0);">WEDDING</a></li>
            <li id="boysandgirls"><a href="javascript:void(0);">BOYS&GIRLS</a></li>
            <li id='childrenandfamily'><a href="javascript:void(0);">KIDS&FAMILY</a></li>
            <li id='street'><a href="javascript:void(0);">STREET</a></li>
        </ul>
    </div>
    <div id="photos_frame">
        <div id="photos_content">
            <!--<div class="imgbox"><img src="http://placehold.it/350x350/abc/aaa/&text=CaptureThumbs"/></div>-->
        </div>
    </div>
</div>

<div id="stories" class="contentBoard">
    <div style="background: #efa;margin: 0 auto;width: 60%;height: 500px;"></div>
</div>

<!--底部部分-->
<div id="fot"><span>Copyright ©2014 Terryyhq.com Powered By Adol Version 1.0.0 - <a href="http://www.miitbeian.gov.cn/">湘ICP备:24320053609</a></span></div>
</body>

<script src="js/jquery.customer.slider.js"></script>
<script src="js/jquery.customer.board_switch.js"></script>
<script src="js/jquery.customer.photo_wall.js"></script>

<script>
//<!--图片地址的数据结构-->
var ImagePool = {
    prewedding: [
        <?php echo getImgPath('img20141010/1prewedding');?>,
        <?php echo getImgPath('1prewedding');?>
        ],
    wedding: [
        <?php echo getImgPath('img20141010/2wedding');?>,
        <?php echo getImgPath('2wedding');?>
        ],
    boysandgirls: [
        <?php echo getImgPath('img20141010/3boysNgirls');?>,
        <?php echo getImgPath('3boysNgirls');?>
        ],
    childrenandfamily: [
        <?php echo getImgPath('img20141010/4childrenNfamily');?>,
        <?php echo getImgPath('4childrenNfamily');?>
        ],
    street: [
        <?php echo getImgPath('img20141010/5street');?>,
        <?php echo getImgPath('5street');?>
        ]
};

//文档加载入口
var photosframe;
$(document).ready(function () {
    //
    <!--100%高的方法-->
    $('body').height(window.innerHeight);
    $(window).resize(function () {
        $('body').height(window.innerHeight);
    });

    //    首页出现
    $('.contentBoard').eq(0).moveDownIn(1000, (window.innerHeight - 450) / 4);

    var slider = $('#slider').buildSlider();
    setInterval(function () {
        slider.slidernext()
    }, 2000);

    boxjumping(document.getElementById('menu_home'), '20px', function () {
        boxjumping(document.getElementById('menu_photos'), '20px', function () {
            boxjumping(document.getElementById('menu_contact'), '20px', function () {
                boxjumping(document.getElementById('menu_stories'), '20px', function () {
                })
            });
        });
    });

    boxjumping(document.getElementById('prd'), '20px', function () {
        boxjumping(document.getElementById('wed'), '20px', function () {
            boxjumping(document.getElementById('bng'), '20px', function () {
                boxjumping(document.getElementById('cnf'), '20px')
            });
        });
    });

//    注册各个按钮点击事件
    $('#prewedding').click(function () {
        photosframe = $('#photos_frame').imagesInject(ImagePool.prewedding)
    });

    $('#wedding').click(function () {
        photosframe = $('#photos_frame').imagesInject(ImagePool.wedding);
    });

    $('#boysandgirls').click(function () {
        photosframe = $('#photos_frame').imagesInject(ImagePool.boysandgirls)
    });

    $('#childrenandfamily').click(function () {
        photosframe = $('#photos_frame').imagesInject(ImagePool.childrenandfamily)
    });

    $('#street').click(function () {
        photosframe = $('#photos_frame').imagesInject(ImagePool.street)
    });


    $('#leftward').click(function () {
        photosframe.moveLeft();
    });
    $('#rightward').click(function () {
        photosframe.moveRight();
    });

});


//  定义各个板块出现的方法
var timer;//定时器
function displayBoard(id, order) {
//  滑下当前board
    if (!id || id === '')return;
    var targetElem = $("#" + id);
    var currentElem = $('.currentboard');
    if (currentElem.is(":animated"))return;

//  滑出板块
    currentElem.moveDownOut(500).queue(function () {

//      需要放到动画结束时来判断框架尺寸
        if (id == 'photos') {
            currentElem.removeClass('currentboard');
            targetElem.moveDownIn(1000, (window.innerHeight - 590) / 2);
            targetElem.addClass('currentboard');

            clearInterval(timer);
            var t = $('#photos_frame');//框架对象
            switch (order) {
                case 1:
                    photosframe = t.imagesInject(ImagePool.prewedding);
                    break;
                case 2:
                    photosframe = t.imagesInject(ImagePool.wedding);
                    break;
                case 3:
                    photosframe = t.imagesInject(ImagePool.boysandgirls);
                    break;
                case 4:
                    photosframe = t.imagesInject(ImagePool.childrenandfamily);
                    break;
                case 5:
                    photosframe = t.imagesInject(ImagePool.street);
                    break;
                default :
                    photosframe = t.imagesInject(ImagePool.prewedding);
                    break;
            }
            var photonav = $('#photos_nav');
//            用以把图框上下居中,不能用windows来计算，因为外层框架设置了相对定位,所以只能相对外层框架定位
//            t.css({top:(t.parent().height()- t.height())/2 - photonav.height()});

            //相册页导航栏位置用于左右对齐
            photonav.css({'margin-left': (window.innerWidth - t.width()) / 2 + 2});

            // 左右箭头动画
            var leftward = $('#leftward').css({left: 50});
            var rightward = $('#rightward').css({right: 50});
            var both = $('#leftward,#rightward');
            timer = setInterval(function () {
                leftward.css({left: 50, opacity: 1}, 300).animate({left: "-=25", opacity: 0}, 1000);
                rightward.css({right: 50, opacity: 1}, 300).animate({right: "-=25px", opacity: 0}, 1000);
            }, 1500);

            both.mouseover(function () {
                clearInterval(timer);
                both.stop().css({opacity: 1});
            })
        }

        if (id == 'contact') {
            currentElem.removeClass('currentboard');
//          为什么要除以4，暂时还搞不清楚，反正除以四就对了
            targetElem.moveDownIn(1000, (window.innerHeight - 350) / 4);
            targetElem.addClass('currentboard');
        }

        if (id == 'stories') {
            currentElem.removeClass('currentboard');
            targetElem.moveDownIn(1000, (window.innerHeight - 590) / 2);
            targetElem.addClass('currentboard');
        }

        if (id == 'home') {
            currentElem.removeClass('currentboard');
            // 除4的原因同上
            targetElem.moveDownIn(1000, (window.innerHeight - 450) / 4);
            targetElem.addClass('currentboard');


            boxjumping(document.getElementById('menu_home'), '20px', function () {
                boxjumping(document.getElementById('menu_photos'), '20px', function () {
                    boxjumping(document.getElementById('menu_contact'), '20px', function () {
                        boxjumping(document.getElementById('menu_stories'), '20px', function () {
                        })
                    });
                });
            });

            boxjumping(document.getElementById('prd'), '20px', function () {
                boxjumping(document.getElementById('wed'), '20px', function () {
                    boxjumping(document.getElementById('bng'), '20px', function () {
                        boxjumping(document.getElementById('cnf'), '20px')
                    });
                });
            });
        }
    });
}

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
