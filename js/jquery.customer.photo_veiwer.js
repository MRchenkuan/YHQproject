/*
* 相册插件
* */
;
(function($){
    // 顶部导航条
    var $navFrame = $("#photos_nav");
    var $groupNav = $navFrame.find(">ul");
    var cached = {};
    // 创建图片浏览器
    var photoViewer = new PhotoViewer();
    $.fn.createPhotoViewer = function(){
        var $this = $(this);
        var albumId = $this.attr("data-id");
        var albumName = $this.attr("data-name");
        var albumDesc = $this.attr("data-desc");

        photoViewer.show(albumName,albumDesc);
        if(cached[albumId]){
            photoViewer.init(cached[albumId]);
        }else{
            photoViewer.showLoading(true);
            $.ajax({
                url:"./backstage/pages/Data.php?id=getPhotosByAlbumId&albumId="+albumId,
                success:function(data){
                    photoViewer.showLoading(false);
                    cached[albumId]=JSON.parse(data);
                    photoViewer.init(cached[albumId]);
                }
            })
        }
    };

    // 创建相册展示器
    function PhotoViewer(){
        var self = this;
        var $displayBox = $("#albums");
        var coverEle = document.createElement("div");
        var frame = document.createElement("div");
        var $coverEle = $(coverEle);
        var $frame = $(frame);
        var backBtn = document.createElement("span");
        var $backBtn = $(backBtn);
        var albumInfoBar = document.createElement("span");
        var $albumInfoBar = $(albumInfoBar);
        albumInfoBar.appendChild(document.createElement("span"));
        albumInfoBar.appendChild(document.createElement("span"));
        var photoWin = new PhotoWin();

        // 定义遮罩样式
        $coverEle.css({
            width:"100%",
            height:"100%",
            position:"absolute",
            backgroundColor:"#fff",
            backgroundImage:"url(img/ui/loading.gif)",
            backgroundRepeat:"no-repeat",
            backgroundPosition:"center"
        });

        // 定义框架样式
        $frame.css({
            width:"100%",
            height:"100%",
            position:"absolute",
            overflowY:"hidden",
            overflowX:"auto",
            display:"none",// !定义为none,后续淡入
            transition:"all .5s ease"

        });


        // 注册关闭事件
        $coverEle.click(function(){
            self.out()
        });
        $frame.click(function(){
            self.out()
        });


        // 展示遮罩方法
        this.show = function(albumName,albumDesc){
            self.albumName = albumName;
            self.albumDesc = albumDesc;
            $displayBox.append(coverEle);
            // 禁用底层滚动
            $displayBox.css({
                overflowX:"hidden"
            });
            // 切换为相册模式
            self.switchToAlbumInfo();
        };

        // 隐藏遮罩方法
        this.out = function(){
            coverEle.remove();
            frame.remove();
            // 启用底层滚动
            $displayBox.css({
                overflowX:"auto"
            });
            // 切换回相册组模式
            self.switchToGroupNav();
        };

        // loading图开关
        this.showLoading = function(isShow){
            $coverEle.css({
                backgroundImage:isShow?"url(img/ui/loading.gif)":"none"
            })
        };

        // 顶部切换回相册组模式
        this.switchToGroupNav = function(){
            /**
             * 先产生退出动画并结束,在执行界面重制
             */
            $navFrame.fadeOut(200,function () {// 淡出动画
                $backBtn.remove();
                $albumInfoBar.remove();
                $navFrame.append($groupNav); // 加入导航条
                $navFrame.show(); // 由于外部退出,所以需要重新进入
                $groupNav.fadeIn(200);
            });
        };

        // 顶部切换为相册信息模式
        this.switchToAlbumInfo = function(){
            /**
             * 先产生退出动画并结束,在执行界面重制
             */
            $groupNav.fadeOut(200,function () {// 淡出动画
                $groupNav.remove(); // 移开导航条
                $navFrame.append($backBtn);
                $navFrame.append($albumInfoBar);
                $navFrame.hide().fadeIn(200);
                // 定义返回按钮样式
                $backBtn.html("返回");
                $backBtn.addClass("backOff");
                $backBtn.click(self.out);
                // 定义相册信息样式
                $albumInfoBar.addClass("albumInfoBar");
                // 更新相册顶部信息
                $albumInfoBar.children(":first").html(self.albumName);
                $albumInfoBar.children(":last").html(self.albumDesc);
            });
        };

        // 相册浏览器初始化方法
        this.init = function(photos,albumName,albumDesc){
            if(photos&&photos.length>0){
                photos.some(function(it,id,ar){
                    // 组合相片到相册
                    var thumbBox = document.createElement("div");
                    var $thumbBox = $(thumbBox);
                    $thumbBox.addClass("thumbBox");
                    $thumbBox.attr("data-id",it["id"]);
                    $thumbBox.attr("data-path",it["THUMB"]||it["PATH"]);
                    $thumbBox.attr("data-desc",it["DESC"]);
                    $thumbBox.attr("data-name",it["NAME"]);
                    $thumbBox.attr("data-index",id+1);
                    $frame.append(thumbBox);
                    $thumbBox.click(function(e){
                        e.stopPropagation();
                        // 触发相册展示
                        photoWin.show(photos,id);
                    });
                    // 遮罩
                    var eleCover = document.createElement("div");
                    var $eleCover = $(eleCover);
                    $eleCover.addClass("eleCover");
                    $thumbBox.append($eleCover);
                });
                $displayBox.append(frame);
                resizePhotos($frame);
                $frame.fadeIn();
            }else{
                alert("没有图片");
                setTimeout(function(){
                    self.out()
                },1000)
            }
        };

    }

    // 相片重排器
    function resizePhotos($frame){
        var $thumbBoxex = $frame.find(".thumbBox");
        var verticalCount=4;
        var frameHeight =$frame.height();
        var frameWidth = $frame.width();
        var aspectRatio = frameWidth/frameHeight;
        var margin = 2;
        var albumHeight,albumWidth;

        // 屏幕宽高比
        if(aspectRatio>1){
            albumHeight = albumWidth = frameHeight / verticalCount - margin;
        }else{
            albumHeight = albumWidth = frameWidth / verticalCount - margin;
        }

        // 批量设置样式
        $thumbBoxex.each(function(index,ele){
            var $ele = $(ele);
            var $eleCover = $ele.find(".eleCover");
            // 相片缩略图横纵序号
            var position ;
            if(aspectRatio>1){
                position= { // x:0-n,y:0-3
                    y:index%verticalCount,
                    x:Math.ceil((index+1)/verticalCount)-1
                };
            }else{
                position= { // x:0-n,y:0-3
                    x:index%verticalCount,
                    y:Math.ceil((index+1)/verticalCount)-1
                };
            }




            // 缩略图位置
            var frameTop = position.y*albumHeight;
            var frameLeft = position.x*albumWidth;

            // 缩略图样式
            $ele.css({
                top:frameTop+margin,
                left:frameLeft+margin,
                width:albumWidth-margin,
                height:albumHeight-margin
            });

            // 加载
            var _thumbImg = document.createElement("img");
            var $_thumbImg = $(_thumbImg);
            _thumbImg.src = $ele.attr('data-path');
            $_thumbImg.load(function () {
                // 设置缩略图
                $ele.css({
                    backgroundImage:"url('"+$ele.attr('data-path')+"')"
                });

                // 遮罩归零
                $eleCover.css({
                    width:0
                })

            });

        });
    }

    // 相片展示器
    function PhotoWin(){
        var self = this;
        var nowIndex = 0;
        var photoObjs;
        // 初始化一个相册
        var cover = document.createElement("div");// 遮罩
        var $cover = $(cover);
        var imageBox = document.createElement("div");// 图片容器
        var $imageBox = $(imageBox);
        var image = document.createElement("div"); // 图片本体
        var $image = $(image);
        var _image = document.createElement("img");// 临时图片,只用来载入
        var $_image = $(_image);
        var leftButton = document.createElement("div"); // 上一张图按钮
        var rightButton = document.createElement("div");// 下一张图按钮
        var $leftButton = $(leftButton);
        var $rightButton = $(rightButton);
        // 初始图片框序号
        var number_index = document.createElement("span");
        var $number_index = $(number_index);


        $cover.css({
            width:"100%",
            height:"100%",
            position:"fixed",
            backgroundColor:"#15192F",
            opacity:.7
        });

        $imageBox.css({
            height:"100%",
            width:"100%",
            position:"fixed",
            top:0,
            left:0,
            margin:"auto",
            backgroundImage:"url(img/ui/loading.gif)",
            backgroundRepeat:"no-repeat",
            backgroundPosition:"center"
        });
        // 临时图片
        $_image.css({
            display:"none"
        });

        // 左右按钮
        $leftButton.css({
            width:"40%",
            height:"100%",
            float:"left"
        });
        $rightButton.css({
            width:"40%",
            height:"100%",
            float:"right"
        });

        // 图片序号
        $number_index.css({
            position:"absolute",
            top:0,
            left:0,
            fontSize:"24px",
            color:"#fff"
        });

        // 注册点击事件
        $leftButton.click(function (e) {
            e.stopPropagation();
            self.showPrev();
        });

        $rightButton.click(function (e) {
            e.stopPropagation();
            self.showNext();
        });

        // 注册关闭事件
        $cover.click(function () {
            self.hidden();
        });
        $imageBox.click(function () {
            self.hidden();
        });

        // 临时图片注册加载事件
        $_image.load(function () {
            // 放入临时图片算出宽高比再移走
            imageBox.appendChild(_image);
            var IMG_WH_rate = $_image.width()/$_image.height(); // 原图宽高比
            _image.remove();

            var SRC_WH_rate = $imageBox.width()/$imageBox.height(); // 屏幕宽高比
            var isVertical = (IMG_WH_rate/SRC_WH_rate)>1; // 根据屏幕比例与图片比例,确定横向参照还是竖向参照
            var maxWidth = $imageBox.width()*0.8;
            var maxHeight = $imageBox.height()*0.8;

            var width = isVertical?maxWidth:maxHeight*IMG_WH_rate;
            var height = isVertical?maxWidth/IMG_WH_rate:maxHeight;

            $image.css({
                width:width,
                height:height,
                border:"none",
                backgroundImage:"url('"+_image.src+"')",
                backgroundRepeat:"no-repeat",
                backgroundPosition:"center",
                backgroundSize:"contain"
            });

            // 固定图片位置
            $image.css({
                top:$imageBox.height()/2-$image.height()/2,
                left:$imageBox.width()/2-$image.width()/2
            });
        });


        $_image.error(function () {
            $image.css({
                backgroundImage:"url('img/ui/logo.png')",
                backgroundColor:"#ccc",
                color:"#000",
                textAlign:"center"
            });
            // $image.html("图片加载失败");
        });

        this.show = function(photos,index){
            nowIndex = index;
            photoObjs = photos;
            // 展示
            self.visable();

            // 初始图片框大小
            $image.css({
                width:"60%",
                height:"60%",
                position:"absolute",
                border:"1px solid #fff"
            });

            // 初始图片框位置
            $image.css({
                left:$imageBox.width()/2-$image.width()/2,
                top:$imageBox.height()/2-$image.height()/2
            });
            image.appendChild(number_index);


            var photoObj = photos[nowIndex];
            if(!photoObj) return (console.log("找不到原图"));

            var id = photoObj["id"];
            var desc = photoObj["DESC"];
            var name = photoObj["name"];
            var url = photoObj["PATH"];
            _image.src = url;
            $number_index.text(nowIndex);
        };

        // 展示下一张
        this.showNext = function(){
            if(nowIndex>=photoObjs.length-1)return;
            nowIndex = nowIndex+1;
            var photoObj = photoObjs[nowIndex];
            var id = photoObj["id"];
            var desc = photoObj["DESC"];
            var name = photoObj["name"];
            var url = photoObj["PATH"];
            $number_index.text(nowIndex);
            _image.src = url;
        };

        // 展示前一张

        this.showPrev = function(){
            if(nowIndex<1)return;
            nowIndex = nowIndex-1;
            var photoObj = photoObjs[nowIndex];
            var id = photoObj["id"];
            var desc = photoObj["DESC"];
            var name = photoObj["name"];
            var url = photoObj["PATH"];
            _image.src = url;
            $number_index.text(nowIndex);
        };

        // 移开大图
        this.hidden = function () {
            cover.remove();
            imageBox.remove();
        };

        // 加入大图
        this.visable = function () {
            $image.css({
                backgroundImage:"url(img/ui/loading.gif)",
                backgroundRepeat:"no-repeat",
                backgroundPosition:"center"
            });
            // 加到页面上
            document.body.appendChild(cover);
            document.body.appendChild(imageBox);
            // 插入图片并绑定左右按键
            imageBox.appendChild(image);
            image.appendChild(leftButton);
            image.appendChild(rightButton);
        };

    }

})($);