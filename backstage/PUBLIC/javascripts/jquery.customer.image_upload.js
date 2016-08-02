/**
 * Created by chenkuan on 16/8/2.
 */
;(function () {
    /**
     * @param albumId
    * @param saveBtn selector
     */
    $.fn.imageUploads = function (albumId,saveBtn) {
        var limit = 1.5*1000*1000; // 1.5 MB
        var $weigetBox = this;
        $weigetBox.successCount = 0;

        // 上传按钮
        var inputBtn = document.createElement("a");
        var _inputBtn = document.createElement("input");

        var $_inputBtn = $(_inputBtn).attr({
            type:"file",
            multiple:"multiple",
            accept:"image/*",
            value:"请上传图片"
        }).css({
            opacity:"0",
            width:"100%",
            height:"100%",
            position: "absolute",
            top:"0",
            left: "0"
        });
        inputBtn.appendChild(_inputBtn);
        var $inputBtn = $(inputBtn).css({
            backgroundSize: "80%",
            backgroundRepeat:"no-repeat",
            backgroundPosition: "center",
            border:"1px solid grey",
            width:"20%",
            position:"relative",
            float:"left",
            borderRadius: "10px",
            height: "100px",
            backgroundImage: "url('../PUBLIC/UI/area-add.png')"
        });


        // 上传盒子
        var imagesPrevBox = document.createElement("div");
        imagesPrevBox.successCount = 0;
        var $imagesPrevBox = $(imagesPrevBox).css({
            width:"100%",
            overflow:"hidden"
        }).attr("data-selected",0);

        // 挂载盒子
        $weigetBox.append($imagesPrevBox);
        $weigetBox.append($inputBtn);


        /**
         * 选图事件
         */
        $_inputBtn.change(function () {
           var self = this;
           var files = self.files;
           console.log(files);
           for(var i=0;i<files.length;i++){
               var file = self.files[i];
               /*上传多张图片*/
               var reader = new FileReader();
               reader.onload = function(){
                   var dataStr = this.result;
                   var imgbox = new ImgBox(dataStr);
                   imgbox.progress(0);
                   imagesPrevBox.appendChild(imgbox);
                   if(this.isOverSize){
                       imgbox.disAble();
                   }
                   /*设置为保存标记*/
                   var imgCount = parseInt($imagesPrevBox.attr("data-selected"))||0;
                   $imagesPrevBox.attr("data-selected",imgCount+1);
               };
               if(file.size > limit){// less than 1,000,000
                   reader.isOverSize = true;
               }
               reader.readAsDataURL(file);
           }
        });

        /**
         * 上传事件
         */
        var $saveBtn = $(saveBtn);
        $saveBtn.click(function () {

            if($imagesPrevBox.attr('data-selected')<=0){
                alert("没有上传任何图片");
                return;
            }

            var imgBoxes = imagesPrevBox.childNodes;
            Array.prototype.some.call(imgBoxes,function (imgBox) {
                imgBox.setMsg("准备上传");
                $saveBtn.attr('disabled','disabled');
                $saveBtn.text('上传中请稍等……');
                uploadImg(imgBox);
            });
        });

        // 单图上传方法
        function uploadImg(imgBox) {
            imgBox.setMsg("上传中...");
            var src = imgBox.getSrc();
            $.ajax({
                url:'Data.php?id=uploadImgAjax',
                type:'POST',
                data:{
                   'imgDataString':src||'',
                   'albumid':albumId,
                   'remark':"批量上传",
                   'onlineurl':""
                },
                beforeSend:function () {
                  imgBox.progress(25);
                },
                success:function(data){
                    data = JSON.parse(data);
                    imgBox.progress(100);
                    imgBox.setMsg(data.msg);
                    if(data.stat==200){
                        imagesPrevBox.successCount = imagesPrevBox.successCount + 1;
                        var faileCount = parseInt(imagesPrevBox.getAttribute("data-selected")) - imagesPrevBox.successCount;
                    }else{
                        return;
                    }

                    if(faileCount<=0){
                        location.reload();
                    }else{
                        console.log(imagesPrevBox.successCount+"张上传完成,"+faileCount+"张上传失败,请检查")
                    }
                },
                error:function(data){
                    imgBox.setMsg(data.statusText);
                }
            });
        }

        // 图片盒子
        function ImgBox(dataStr){
            var img = document.createElement("img");
            var div = document.createElement("div");
            var msgBox = document.createElement("div");
            var progress = new Progress();
            var $progress = $(progress);
            var closeBtn = document.createElement("span");
            var $div = $(div),$img = $(img),$closeBtn = $(closeBtn),$msgBox=$(msgBox);
            $div.css({
                width:"20%",
                float:"left",
                position:"relative",
                border:"1px solid #ccc",
                margin:"5px",
                overflow:"hidden"
            });
            $img.css({
                width:"100%"
            });
            $closeBtn.css({
                position:"absolute",
                width:20,
                height:20,
                top:0,
                right:0,
                background:"#d9534f",
                color:"#fff",
                textAlign:"center"
            }).text("X");

            $progress.css({
                height:15,
                width:"100%",
                position:"absolute",
                opacity:".8",
                bottom:0
            });
            $msgBox.css({
               position:"absolute",
                height:"100%",
                width:"100%",
                textAlign:"center",
                top:0
            }).css({
                lineHeight:$msgBox.height()+"px"
            });

            div.appendChild(img);
            img.src = dataStr;
            div.appendChild(msgBox);
            div.appendChild(progress);
            div.appendChild(closeBtn);

            // // 移除操作
            // $div.remove = function () {
            //     $div.remove();
            // };
            $closeBtn.click(function () {
                if(confirm("是否取消上传"))
                $div.remove();
            });

            // 进度更新操作
            div.progress = function (prog) {
                progress.setProgress(prog);
            };
            // 置灰操作
            div.disAble = function () {
                $(img).css({
                    opacity:.5
                });
                $div.isAvaliable = false
            };
            // 展示信息
            div.setMsg = function (msgs) {
                $msgBox.text(msgs)
            };
            // 是否有效
            div.isAvaliable = true;

            // 获取图片数据
            div.getSrc = function () {
                return img.src;
            }

            return div
        }

        // 进度盒子
        function Progress() {
            var self = this;
            var frame = document.createElement("div");
            var $frame = $(frame);
            var value = document.createElement("div");
            var $value = $(value);
            self.prog = 0;
            $frame.css({
                backgroundColor:"#ccc"
            });
            $value.css({
                width:0,
                position:"absolute",
                left:0,
                height:"100%",
                backgroundColor:"green",
                color:"#fff",
                textAlign:"right"
            });
            frame.appendChild(value);

            frame.setProgress = function (prog) {
                self.prog = prog;
                $value.stop().animate({
                    width:prog+"%"
                });
                $value.text(prog+"% ")
            };

            frame.getProgress = function () {
                return self.prog;
            };

            return frame;
        }

    }
})();