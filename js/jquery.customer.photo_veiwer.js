/*
* 相册插件
* */
;
(function($){
    // 创建图片浏览器
    var cache = {};
    var photoViewer = new PhotoViewer();
    $.createPhotoViewer = function(albumid){
        photoViewer.show();
        if(cache[albumid]){
            photoViewer.init(cache[albumid]);
        }else{
            photoViewer.showLoading(true);
            $.ajax({
                url:"./backstage/pages/Data.php?id=getPhotosByAlbumId&albumId="+albumid,
                success:function(data){
                    photoViewer.showLoading(false);
                    cache[albumid]=JSON.parse(data);
                    photoViewer.init(cache[albumid]);
                }
            })
        }
    };

    // 创建相册图片浏览器
    function PhotoViewer(){
        var coverEle = document.createElement("div");
        var frame = document.createElement("div");
        var $coverEle = $(coverEle);
        var $frame = $(frame);
        var photoEle = document.createElement("div");
        var $photoEle = $(photoEle);
        var photoWin = new PhotoWin();

        $photoEle.css({
            width:"100%",
            height:"100%",
            position:"fixed",
            backgroundColor:"rgb(21, 25, 47)",
            backgroundImage:"url(img/ui/loading.gif)"
        });
        // 定义遮罩样式
        $coverEle.css({
            width:"100%",
            height:"100%",
            position:"fixed",
            backgroundColor:"rgb(21, 25, 47)",
            opacity:.7,
            backgroundImage:"url(img/ui/loading.gif)",
            backgroundRepeat:"no-repeat",
            backgroundPosition:"center"
            //transition:"all .5s ease"
        });

        // 定义框架样式
        $frame.css({
            width:"100%",
            height:"100%",
            position:"fixed",
            overflowY:"hidden",
            overflowX:"auto",
            display:"none",// !定义为none,后续淡入
            transition:"all .5s ease"

        });


        // 注册关闭事件
        $coverEle.click(function(){
            coverEle.remove();
            frame.remove();
        });
        $frame.click(function(){
            coverEle.remove();
            frame.remove();
        });


        // 展示遮罩方法
        this.show = function(){
            document.body.appendChild(coverEle);
        };

        // 隐藏遮罩方法
        this.out = function(){
            coverEle.remove();
            frame.remove();
        };

        // loading图开关
        this.showLoading = function(isShow){
            $coverEle.css({
                backgroundImage:isShow?"url(img/ui/loading.gif)":"none"
            })
        };

        // 初始化方法
        this.init = function(photos){
            if(photos&&photos.length>0){
                photos.some(function(it,id,ar){
                    // 组合相片到相册
                    var thumbBox = document.createElement("div");
                    var $thumbBox = $(thumbBox);
                    $thumbBox.addClass("thumbBox");
                    $thumbBox.attr("data-id",it["id"]);
                    $thumbBox.attr("data-path",it["PATH"]);
                    $thumbBox.attr("data-desc",it["DESC"]);
                    $thumbBox.attr("data-name",it["NAME"]);
                    frame.appendChild(thumbBox);
                    $thumbBox.click(function(e){
                        e.stopPropagation();
                        // 触发相册展示
                        photoWin.show(photos);
                    });
                });
                document.body.appendChild(frame);
                resizePhotos($frame);
                $frame.fadeIn();
            }else{
                alert("没有图片");
                setTimeout(function(){
                    coverEle.remove();
                    frame.remove();
                },1000)
            }
        };
    }

    // 重拍图片的方法
    function resizePhotos($frame){
        var $thumbBoxex = $frame.find(".thumbBox");
        var verticalCount=3;
        var aspectRatio = 5/4;
        var frameHeight =$frame.height();
        var frameWidth = $frame.width();
        var framePadding = frameHeight*.05;
        var albumFullHeight =(frameHeight-framePadding*2)*(1/verticalCount);
        var albumFullWidth = albumFullHeight*aspectRatio;
        var albumHeight = albumFullHeight*.95;//根据竖向个数计算相册高度-除去边距
        var albumWidth = albumFullWidth*.95;

        $thumbBoxex.each(function(index,ele){
            var $ele = $(ele);
            var position = { // x:0-n,y:0-3
                y:index%verticalCount,
                x:Math.ceil((index+1)/verticalCount)-1
            };

            // 相片缩略图
            $ele.css({
                position:"absolute",
                opacity:1,
                height:albumHeight,
                width:albumWidth,
                top:position.y*albumFullHeight+framePadding,
                left:position.x*albumFullWidth+framePadding,
                backgroundImage:"url("+$ele.attr('data-path')+")",
                backgroundSize:"contain",
                backgroundRepeat:"no-repeat",
                backgroundPosition:"center"
            });

        });
    }

    // 相片展示方法
    function PhotoWin(){
        this.nowIndex = 0;
        // 初始化一个相册
        this.show = function(photos){
            //// 定义相框样式
            //$photoEle.css({
            //    position:"fixed",
            //    height:"100%",
            //    width:"100%",
            //    backgroundPosition:"center",
            //    backgroundRepeat:"no-repeat",
            //    backgroundSize:"contain"
            //});
            //
            //// 照片点击事件
            //$photoEle.click(function(e){
            //    photoEle.remove();
            //});
        };

        // 展示下一张
        this.showNext = function(){

        };

        // 展示前一张

        this.showPrev = function(){

        };
        //document.body.appendChild(photoEle);
        //photoEle.style.backgroundImage = "url("+$ele.attr("data-path")+")"
    }


})($);