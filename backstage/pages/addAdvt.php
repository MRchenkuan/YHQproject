<!--this is head-->
<?php
$pageID = 'addAdvt';
require_once('../definitions.php');
require_once(DATABASE_DAO_DIR."newsDAO.php");
include_once(WIDGETS_DIR.'head.php');
?>

<?php

    $pageNow = $_GET['page'];//当前分页
    if(!$pageNow){$pageNow=1;}
    $sliceParam = 'page'; //分页参数
    $pagesize = 5;//页面条数
    $data = new newsDAO();
    $count = $data->getADVTCount();//总共条目数

    $adCollection = $data->getRecentADVTByPage($pageNow,$pagesize);
    $pageCount = ceil($count/$pagesize);//总页数
?>

<!--below is content-->
<div class="panel panel-default" style="width: 960px;margin: 60px auto 0 auto" xmlns="http://www.w3.org/1999/html">
    <!-- Default panel contents -->
    <div class="panel-heading">
        <span class="glyphicon glyphicon-calendar"></span> 已有封面
    </div>
    <div class="panel-body">
        下面表格展示了已经添加了的广告,序号越大,排序越靠前
        <button class="btn btn-success" style="float: right" data-toggle="modal" data-target="#fileuploadpannel"><span class="glyphicon glyphicon-level-up"></span> 增加封面</button>
    </div>
    <!--分页组件-->
    <?php include '../widgets/pageSliceBar.php' ?>
    <!-- Table -->
    <table class="table">
        <tr  style="text-align: left">
            <td><span class="glyphicon glyphicon-picture"></span>图片</td>
            <td><span class="glyphicon glyphicon-sort-by-attributes"></span>排序</td>
            <td><span class="glyphicon glyphicon-cog"></span>操作</td>
        </tr>
        <?php
        foreach($adCollection as $items){
            if(!$items['THUMB'])$items['THUMB'] = $items['COVER'];
            ?>
            <tr>
                <td><img height=100 style="max-width: 250px;height: auto;" src="<?php echo $items['THUMB']?>" alt="缩略图"></td>
                <td><?php echo $items['ORDER']?></td>
                <td><div class="btn-group" role="group" aria-label="...">
                        <button type="button"
                                class="btn btn-default"
                                onclick="if(!confirm('是否删除此条记录?'))return false;delRecord(<?php echo $items['id']?>)">
                            <span class="glyphicon glyphicon-trash"></span>
                        </button>
                    </div>
                </td>
            </tr>

        <?php }?>
        </table>

    <!--分页组件-->
    <?php include '../widgets/pageSliceBar.php' ?>
</div>

<!-- ************* 图片上传模块 ************* -->
<div class="modal fade" id="fileuploadpannel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">文件上传</h4>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-link"></span></span>
                    <input type="text" class="form-control" placeholder="如果不上传，则在此处填写图片url" id="onlineurl" aria-describedby="basic-addon1">
                </div>
                <div class="col-xs-6 col-md-10" style="position: relative;float: none;margin: 0 auto">
                    <a href="#" class="thumbnail">
                        <img id="imguploadpreview" name="forupload" data-selected="0" src="../PUBLIC/UI/area-add.png" alt="添加图片">
                    </a>
                    <input id="imageupload" type="file" value="选择图片" accept="image/*" style="opacity:0;width:100%;height:100%;position: absolute;top:0;left: 0;">
                </div>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-tags"></span></span>
                    <input type="number" class="form-control" placeholder="填写排序号" id="order" aria-describedby="basic-addon1">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button id="saveimg" type="button" class="btn btn-success">保存</button>
            </div>
        </div>
    </div>
</div>


<script>

    var uploadbtn = document.getElementById('imageupload');
    var uploadprv = document.getElementById('imguploadpreview');
    var savebtn = document.getElementById('saveimg');

    uploadbtn.addEventListener("click",function(){
        var self = this;
        var reader = new FileReader();
        reader.onload = function(){
            uploadprv.src = this.result;
            /*设置为保存标记*/
            uploadprv.setAttribute('data-selected','1');
        };
        uploadbtn.addEventListener('change',function(){
            /*进而可考虑上传多张图片*/
            reader.readAsDataURL(self.files[0]);
        });
    });



    savebtn.addEventListener("click",function(){
        var imgs = document.getElementsByName('forupload');
        var order = document.getElementById('order').value;
        var onlineurl = document.getElementById('onlineurl').value;


        Array.prototype.some.call(imgs,function(it,id,ar){
            if(it.getAttribute('data-selected')!=0||onlineurl){
                savebtn.setAttribute('disabled','disabled');
                savebtn.innerHTML = '上传中请稍等……';
                $.ajax({
                    url:'Data.php?id=uploadCover',
                    type:'POST',
                    data:{
                        'imgDataString':it.src||'',
                        'albumid':"-1",
                        'order':order,
                        'onlineurl':onlineurl
                    },
                    success:function(data){
                        data = JSON.parse(data);
                        console.log(data);

                        if (data.stat == 200) {
                            alert(data.msg);
                            location.reload();
                        } else{
                            savebtn.removeAttribute('disabled');
                            savebtn.innerHTML = '上传出错，请调试';
                        }
                    },
                    error:function(data){
                        data = JSON.parse(data);
                        console.log(data);
                        savebtn.removeAttribute('disabled');
                        savebtn.innerHTML = 'data.msg';
                    }
                });
            }else{
                alert('图片没上传或者没有填写网络图片地址！');
            }
        });
    });


    function delRecord(id){
        $.ajax({
            url: './Data.php?id=delAdvt',
            type:"POST",
            data:{
                id: 'delAdvt',
                adid:id
            },
            complete: function (data) {
                console.log(data.responseText);
                var rep = eval("(" + data.responseText + ")");
                if (rep.stat == 200) {
                    alert(rep.msg);
                    location.reload();
                } else{
                    self.innerHTML = '提交出错，请重试';
                    self.removeAttribute('disabled');
                }
            },
            error: function () {
                alert('提交出错');
                self.removeAttribute('disabled');
            }
        })
    }
</script>

<!--this is foot-->
<?php
include WIDGETS_DIR."foot.php";
?>