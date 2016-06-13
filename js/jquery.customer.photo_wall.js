/**
 * 展示相册的方法
 */
;(function($, w){
    var defaults = {
            frameSize: {x: 5, y: 3},
            imgBoxMargin: 5,
            lineMargin: 5,
            imagboxbg: './img/ui/stories.png',
            shieldbg: '#ddd',
            Closespeed: 400,
            Openspeed: 1000,
            frameoverflow: 'visible',
            loadingimg:'./img/ui/loading.gif',
            bigimgsize:0.85
    },method={};

    $.fn.albumDisplay = function(albumId){

    }
})($, window)


//---------实现横向瀑布流插件----------//
;(function ($, w) {
    var defaults = {
            frameSize: {x: 5, y: 3},
            imgBoxMargin: 5,
            lineMargin: 5,
            imagboxbg: './img/ui/stories.png',
            shieldbg: '#ddd',
            Closespeed: 400,
            Openspeed: 1000,
            frameoverflow: 'visible',
            loadingimg:'./img/ui/loading.gif',
            bigimgsize:0.85
        },
        opt, photosFrame, photosContent,
        imgCount, lineImgboxCount, imgboxHeight,
        imgboxWidth, i, imageboxes, imgBoxLine, nowcul,
        publicmethod = {}, timer;

    //定义大图遮罩
    var divNode = document.createElement('div');
    var div = $(divNode);
    div.attr("style", "z-index:9998;display:none;position: absolute;width: 100%;height: 100%;top: 0;left: 0;background: #15192f;opacity: 0.7");
    //定义图片弹框
    var imgbox = $(document.createElement('div'));
    imgbox.attr('style', 'position:absolute;opacity:1;display:none;z-index:9999;top:0;left:0;bord:1px solid black');
    var $loadingimg = $(document.createElement('img'));
    $loadingimg.attr('src',defaults.loadingimg);
    console.log('jiazaitu'+$loadingimg.height());
    $loadingimg.css({
        position:'absolute',
        height:300,
        width:400,
        top: (w.innerHeight-300)/2,
        left:(w.innerWidth-400)/2
    });
    div.append($loadingimg);

    // 定义大图遮罩的取消事件
    div.click(function () {
        if (div.is(':animated'))return;
        div.fadeOut(500).queue();
        $loadingimg.hide();
        imgbox.hide();
        imgbox.empty();
    });

    //挂载大图遮罩和图片盒子
    document.body.appendChild(divNode);
    $('body').append(imgbox);

    //定义主方法
    $.fn.imagesInject = function (urllist, option) {

        opt = $.extend(defaults, option || '');//合并基本参数
        photosFrame = this;//定义图片框架
        $(photosFrame).css({'width': '100%', 'height': '460px'});//设定图片框架宽度，用以做整体参照
        photosContent = $(photosFrame).find('div')[0];//定义图片容器
        var $photosContent = $(photosContent);
        imgCount = urllist.length;//图片总数
        lineImgboxCount = Math.ceil(imgCount / opt.frameSize.y);//每行图片个数,总图片数除以行数，这里是person相册，到后期再替换
        imgboxHeight = parseInt($(photosFrame).height() / opt.frameSize.y - opt.imgBoxMargin * 2);//单张图片的高,用框架高度除以行数，这里会有上下边距的问题，先不计算，最后再修正
        imgboxWidth = parseInt($(photosFrame).width() / ( opt.frameSize.x + 1 ) - opt.imgBoxMargin * 2);//单张图片的宽度，计算方式和高度计算一致，但是需要多算左右两列图片

        //  调整图片框架宽度,和图片容器的位置
        $(photosFrame).css({'width': (imgboxWidth + 2 * opt.imgBoxMargin) * (opt.frameSize.x), 'overflow': opt.frameoverflow});
        $photosContent.css({'position': 'relative', 'left': -(imgboxWidth + 2 * opt.imgBoxMargin) * ((nowcul) || 0)});//定位模式和更新之后的左距

        imgBoxLine = $('.imgBoxLine');
        if (imgBoxLine.length != 0) {
            imgBoxLine.each(function () {
                $(this).find('.imgbox').slice(nowcul, nowcul + opt.frameSize.x).find('a').css({'opacity': '1', 'width': 0}).animate({'width': "100%"}, opt.Closespeed).queue(function () {
                    $photosContent.empty().parent().imagesInject(urllist, option);
                });
            });
        } else {

            //  准备一个行容器
            var imgBoxLines = [];
            //  添加面板图片行
            for (i = 0; i < opt.frameSize.y; i++) {
                imgBoxLine = document.createElement('div');
                imgBoxLine.setAttribute('class', 'imgBoxLine');
                imgBoxLine.setAttribute('style',
                    "width:" + ((imgboxWidth + opt.imgBoxMargin * 2) * lineImgboxCount)
                    + "px;height:" + imgboxHeight + "px;margin:"
                    + opt.lineMargin + 'px' + " 0;clear:both;"
                );
                imgBoxLines.push(imgBoxLine);
            }

            //构建框架，像每行添加图片，
            var displayCount = opt.frameSize.x * opt.frameSize.y;
            for (i = 0; i < imgCount; i++) {
                var lineOrder = i % opt.frameSize.y; // 每一张图片的行号
                var imgdiv = document.createElement('div');// 图片容器
                var img = document.createElement('img');
                img.src = opt.imagboxbg; //图片的默认图

                var url = urllist[i];
                img.setAttribute('data-orgsrc', 'img/' + url); // 原图
                img.setAttribute('data-src', 'img/thumbs/' + url); // 小图
                img.style.position = 'absolute';
                imgdiv.className = 'imgbox';
                //  图片的尺寸和边距,以及初始化所有图片,同时把前几张图片显示出来
                imgdiv.setAttribute('style',
                    "width:" + imgboxWidth + "px;height:" + imgboxHeight + "px;margin:0 " + opt.imgBoxMargin + 'px;'
                    + (i < displayCount ? 'opacity:1;display:block' : 'opacity:0;display:none')
                    + ";background-size:cover;position:relative;overflow:hidden"
                );
                img.setAttribute('data-pos', i);

                // 挂载图床
                imgdiv.appendChild(img);

                //设置遮罩层
                var a = document.createElement('a');
                a.href = 'javascript:void(0);';
                a.setAttribute('style', 'width:100%;height:100%;position:absolute;display:block;right:0;background:' + opt.shieldbg);
                a.setAttribute('class', 'imageLink');

                imgdiv.appendChild(a);

                //挂载链接、图片、图片盒子到每一行容器
                imgBoxLines[lineOrder].appendChild(imgdiv);
            }

            //  将图片盒子挂载到图片容器
            for (i = 0; i < imgBoxLines.length; i++) {
                $photosContent.append(imgBoxLines[i]);
            }

            //  3.显示前n个 n=x*y tips:jQuery竟然可以包装数组
            imgBoxLines = $(imgBoxLines);
            imgBoxLines.each(function () {
                $(this).find('.imgbox').slice(0, opt.frameSize.x).find('img').each(function () {
                    var t = $(this);
                    var that = this;
                    t.attr('src', t.attr('data-src'));//交换真实地址
                    that.isloaded=false;//给图片一个加载完成标志，用以决定是否启用图片移入移出事件的监听
                    //定义完成加载的回调函数,同时调整图片大小和位置
                    t.load(function () {
                        that.isloaded=true;//表示图片已经加载完成了
                        if (( t.width() / t.height() ) > ( t.parent().width() / t.parent().height() )) {
                            t.height('100%');
                            t.width('auto');
                            t.css({'left': (t.parent().width() - t.width()) / 2});
                        } else {
                            t.width('100%');
                            t.height('auto');
                            t.css({'top': (t.parent().height() - t.height()) / 2})
                        }
                        t.next('a').animate({'width': 0}, opt.Openspeed, function () {
                            $(this).css({'width': '100%', 'opacity': 0})
                        });

                    })
                });
            });
        }


        nowcul = 0;
        //  左右移动方法
        publicmethod.moveLeft = function () {
            //向坐移动
            if (nowcul > 0) {
                if ($photosContent.is(":animated"))return false;
                nowcul--;
                $photosContent.animate({left: '+=' + (imgboxWidth + 2 * opt.imgBoxMargin)}, 500, 'easeOutBack');
                imgBoxLines.each(function () {
                    var imgbox = $(this).find('.imgbox');
                    imgbox.eq(nowcul).animate({opacity: 1}).find('a').width('100%');
                    imgbox.eq(nowcul + opt.frameSize.x).animate({opacity: 0});
                });
                return true;
            } else {
                return false;
            }
        };

        publicmethod.moveRight = function () {
            //向右移动
            if (nowcul < lineImgboxCount - opt.frameSize.x) {
                if ($photosContent.is(":animated"))return false;
                $photosContent.animate({left: '-=' + (imgboxWidth + 2 * opt.imgBoxMargin)}, 1000, 'easeOutExpo');
                imgBoxLines.each(function () {
                    var imgbox = $(this).find('.imgbox');//找到所有图片盒子
                    imgbox.eq(nowcul).animate({opacity: 0}, 1500).find('a').width(0);//将最前一列设为不可见
                    imgbox.eq(nowcul + opt.frameSize.x).show().animate({opacity: 1});//将最后一列设为可见

                    var img = imgbox.eq(nowcul + opt.frameSize.x).find('img');//定义图片对象
                    img.attr('src', img.attr('data-src'));//交换src

                    //开始给每个图片注册加载事件
                    img.each(function () {
                        var t = $(this);
                        var a = t.next('a');
                        t.load(function () {
//                        ******************** 此处调整图片大小 *******************
                            if (( t.width() / t.height() ) > ( t.parent().width() / t.parent().height() )) {
                                t.height('100%');
                                t.width('auto');
                                t.css({'left': (t.parent().width() - t.width()) / 2});
                            } else {
                                t.width('100%');
                                t.height('auto');
                                t.css({'top': (t.parent().height() - t.height()) / 2})
                            }
                            a.animate({'width': 0}, opt.Openspeed, function () {
                                $(this).css({'width': '100%', 'opacity': 0})
                            });
                        });
                    });
                });
                console.log(nowcul);
                nowcul++;
                return true;
            } else {
                return false;
            }
        };


        //每个图片的A标签的事件委托
        $photosContent.delegate('.imageLink', 'mouseenter', function () {
            var $this = $(this);
            if ($this.is(":animated")||(!$this.prev()[0].isloaded&&$this.prev()[0].isloaded!=undefined))return;
            $this.css({'opacity': '0.5', 'background': 'black'})
        });

        $photosContent.delegate('.imageLink', 'mouseout', function () {
            var $this = $(this);
            if ($this.is(":animated")||(!$this.prev()[0].isloaded&&$this.prev()[0].isloaded!=undefined))return;
            $this.css({'opacity': '0', 'background': opt.shieldbg})
        });

        //大图遮罩触发方法
        $photosContent.delegate('.imageLink', 'click', function () {
            var t = $(this);
            $loadingimg.show();
            //清空遮罩内容，先设置默认图，然后加载原始图片
            imgbox.empty();
            var imgORG = $(document.createElement('img'));
            imgbox.append(imgORG);

            // 定义图片左右操作
            var orgOptions = $([document.createElement('div'), document.createElement('div')]);
            orgOptions.attr('style', 'height:100%;width:45%;float:left;position:absolute;top:0;z-index:5');
            orgOptions.eq(0).css({'left': 0, 'cursor': "w-resize" });
            orgOptions.eq(1).css({'right': 0, 'cursor': "e-resize" });
            imgbox.append(orgOptions);


            // 定义前后属性
            var thumbs = t.prev('img'); //从缩略图找原图地址
            imgORG.attr('src', thumbs.attr('data-orgsrc'));

            // 定义左右事件 通过$("[href='#']")来找已经定义好的 data-pos 属性
            var nowimgpos = t.prev('img').attr('data-pos');
            orgOptions.eq(0).click(function () {

                if (nowimgpos < 1) {
                    alert('已经是第一张照片了，亲！');
                    return;
                }
                nowimgpos--;
                imgORG.attr('src', $("[data-pos='" + nowimgpos + "']").attr('data-orgsrc'));
                console.log(nowimgpos)

            });
            orgOptions.eq(1).click(function () {
                var limit = (opt.frameSize.x + nowcul) * opt.frameSize.y - 2;//已经加载出的最大图片数
                if (nowimgpos > limit) {
                    alert('亲！往右边翻出更多的照片再查看吧！');
                    return;
                }
                nowimgpos++;
                imgORG.attr('src', $("[data-pos='" + nowimgpos + "']").attr('data-orgsrc'));
                console.log(nowimgpos)
            });

            div.fadeIn(500);
            imgORG.load(function () {
                console.log('yesits');
                // 框架位置的初始化
                if (( t.prev('img').width() / t.prev('img').height() ) < ( w.innerWidth / w.innerHeight )) {
                    imgORG.css({
                        height: w.innerHeight * opt.bigimgsize,
                        width: 'auto'
                    })
                } else {
                    imgORG.css({height: 'auto', width: w.innerWidth * opt.bigimgsize})
                }


                // 盒子出现动画2 --直接出现
                imgbox.css({
                    top: t.offset().top + 'px',
                    left: t.offset().left + 'px'
                }).show().css({
                    top: (w.innerHeight - imgORG.height()) / 2,
                    left: (w.innerWidth - imgORG.width()) / 2
                }, 500);


                //图片放大动画2 -- 渐显
                if (( imgORG.width() / imgORG.height() ) < ( w.innerWidth / w.innerHeight )) {
                    imgORG.css({
                        height: parseInt(w.innerHeight * opt.bigimgsize)
                    }).hide().fadeIn();
                } else {
                    imgORG.css({
                        width: parseInt(w.innerWidth * opt.bigimgsize)
                    }).hide().fadeIn();
                }

            });
        });
//        返回公共方法
        return publicmethod;
    };
//        如何私有化？
//        //    矩阵化的方法
//
//        function matrix(stringLen,size){
//            var x=size.x,y=size.y;
//            var matrix=[];
//            for(var i=0;i<x;i++){
//                var colum=[];
//                for(var j=0;j<y;j++){
//                    colum.push(i+j*x);
//                }
//                matrix.push(colum);
//            }
//            return matrix;
//        }
})(jQuery, window);