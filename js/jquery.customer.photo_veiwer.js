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
            backgroundColor:"#ccc",
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

        // 顶部切换为相册组模式
        this.switchToGroupNav = function(){
            backBtn.remove();
            albumInfoBar.remove();
            $navFrame.append($groupNav); // 加入导航条
        };

        // 顶部切换为相册组模式
        this.switchToAlbumInfo = function(){
            $groupNav.remove(); // 移开导航条
            $navFrame.append($backBtn);
            $navFrame.append($albumInfoBar);
            // 定义返回按钮样式
            $backBtn.html("返回");
            $backBtn.addClass("backOff");
            $backBtn.click(self.out);
            // 定义相册信息样式
            $albumInfoBar.addClass("albumInfoBar");
            // 更新相册顶部信息
            $albumInfoBar.children(":first").html(self.albumName);
            $albumInfoBar.children(":last").html(self.albumDesc);

        };

        // 相册浏览器初始化方法
        this.init = function(photos,albumName,albumDesc){
            if(photos&&photos.length>0){
                photos.some(function(it,id,ar){
                    // 组合相片到相册
                    // var thumbBox = document.createElement("div");
                    var thumbBox = document.createElement("img");
                    var $thumbBox = $(thumbBox);
                    $thumbBox.addClass("thumbBox");
                    $thumbBox.attr("data-id",it["id"]);
                    $thumbBox.attr("data-path",it["PATH"]);
                    $thumbBox.attr("data-desc",it["DESC"]);
                    $thumbBox.attr("data-name",it["NAME"]);
                    $frame.append(thumbBox);
                    $thumbBox.click(function(e){
                        e.stopPropagation();
                        // 触发相册展示
                        photoWin.show(photos,id);
                    });
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

    // 相册重排器
    function resizePhotos($frame){
        var $thumbBoxex = $frame.find(".thumbBox");
        var verticalCount=3;
        var aspectRatio = 5/4;
        var frameHeight =$frame.height();
        var frameWidth = $frame.width();
        var framePadding = frameHeight*.05;
        var albumFullHeight =(frameHeight-framePadding*2)*(1/verticalCount);
        var albumFullWidth = albumFullHeight*aspectRatio;
        var albumHeight = albumFullHeight-20;//根据竖向个数计算相册高度-除去边距
        var albumWidth = albumFullWidth-20;

        $thumbBoxex.each(function(index,ele){
            var $ele = $(ele);
            var position = { // x:0-n,y:0-3
                y:index%verticalCount,
                x:Math.ceil((index+1)/verticalCount)-1
            };
            var frameTop = position.y*albumFullHeight+framePadding;
            var frameLeft = position.x*albumFullWidth+framePadding;

            // 相片缩略图
            $ele.attr("src",$ele.attr('data-path'));
            $ele.attr("data-index",index);// 序号
            $ele.css({
                position:"absolute",
                opacity:1,
                top:frameTop,
                left:frameLeft,
                border:"5px solid #fff"
            });
            $ele.load(function () {
                // 判断横竖
                var isImageVertical = $ele.height()>$ele.width();
                var width,height,top,left;


                if(isImageVertical){
                    width = "auto";
                    height = albumHeight;
                }else{
                    width = albumWidth;
                    height = "auto";
                }

                // 设置尺寸
                $ele.css({
                    width:width,
                    height:height
                });

                if(isImageVertical){
                    top= frameTop;
                    left = frameLeft+albumWidth/2-$ele.width()/2;
                }else{
                    top = frameTop+albumHeight/2-$ele.height()/2;
                    left = frameLeft;
                }

                console.log($ele.width());
                // 设置位置
                $ele.css({
                    top:top,
                    left:left
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