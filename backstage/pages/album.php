<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 15/6/16
 * Time: 下午5:21
 */
$pageID = 'photoLib';
require_once('../definitions.php');
include_once(WIDGETS_DIR.'/head.php');
$id = $_GET['id'];
require_once(DATABASE_DAO_DIR."/photoAlbumDAO.php");

$data = new photoAlbumDAO();
$thisalbum = $data->getAlbumInfoById($id);
$photos = $data->getPhotoInfoByAlbumId($id);
$albums = $data->getAllAlbums();

if($id=="0")$thisalbum['NAME']="未绑定相册";

?>
<!--album-->
<div class="panel panel-default"  style="width: 960px;margin: 60px auto 0 auto">

    <div class="panel-heading">
        <h3 class="panel-title">相册《<?php echo $thisalbum['NAME'] ?>》下的图片</h3>
    </div>

    <!-- 文件上传控制按钮 -->
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#fileuploadpannel" style="margin: 20px 0 10px 30px;">
        <span class="glyphicon glyphicon-level-up"></span>上传图片
    </button>

    <div class="panel-body">
    <?php

    /*****************
     * 查看未绑定的图片
     *****************/
    if(!$id||$id=='0'||$id==''){
        require_once(WIDGETS_DIR.'getUnbindedImg.php');
        $unbindedimgs = getUnbindedImg(STATIC_DIR.'images/');
        foreach($unbindedimgs as $key=>$unbindeimg){
            if('在数据库中找到图片地址'){
                unset($unbindedimgs[$key]);
            }
        }
        // 对于没找到的图片
        foreach($unbindedimgs as $photo){
            if($photo['THUMB'])$photo['PATH'] = $photo['THUMB'];
            ?>
            <div class="col-xs-6 col-md-3">
                <span onclick="if(confirm('继续操作将删除此图片！'))delImg(this);" data-imgsrc="<?php echo $photo?>" class="glyphicon glyphicon-remove-sign" data-toggle="tooltip" data-placement="top" title="删除图片" style="float: right;margin: 5px;color: #e94513"></span>
                <span style="float: right;margin: 5px;color: #05c133" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-placement="top" title="编辑图片" data-target="#imageEditor"></span>
                <span style="float: right;margin: 5px;color: #05c133" class="glyphicon glyphicon-picture" title="设为封面"></span>
                <div class="dropdown" style="float: right;margin: 4px;color: #5f6297">
                    <span class="glyphicon glyphicon-globe dropdown-toggle" data-toggle="dropdown" id="dropdownMenu2" aria-haspopup="true" aria-expanded="false" data-placement="top" title="移动图片"></span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li class="dropdown-header">绑定图片到相册</li>
                        <?php foreach($albums as $album){ ?>
                            <li><a onclick="moveImgToAlbum(this)" data-imgsrc="<?php echo $photo?>" data-albumid="<?php echo $album['id']?>" href="javascript:void(0)"><?php echo $album['NAME']?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <a href="<?php echo $photo?>" class="thumbnail">
                    <img src="<?php echo $photo?>" alt="<?php echo '未绑定的图片'?>">
                </a>
            </div>
        <?php }
    }

    /***************
     * 根据ID查看图片
     ***************/
    if(!$photos||count($photos)==0){
        echo '相册中没有图片';
    }else{
        foreach($photos as $photo){
            if($photo['THUMB'])$photo['PATH'] = $photo['THUMB'];
            ?>
            <div class="col-xs-6 col-md-3">
                <span onclick="if(confirm('继续操作将删除此图片！'))delImg(this);" data-imgsrc="<?php echo $photo['PATH']?>" data-imgid="<?php echo $photo['id']?>" class="glyphicon glyphicon-remove-sign" data-toggle="tooltip" data-placement="top" title="删除图片" style="float: right;margin: 5px;color: #e94513"></span>
                <span style="float: right;margin: 5px;color: #05c133" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-placement="top" title="编辑图片" data-target="#imageEditor"></span>
                <div class="dropdown" style="float: right;margin: 4px;color: #5f6297">
                    <span class="glyphicon glyphicon-circle-arrow-right dropdown-toggle" data-toggle="dropdown" id="dropdownMenu2" aria-haspopup="true" aria-expanded="false" data-placement="top" title="移动图片"></span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li class="dropdown-header">移动图片到相册</li>
                        <?php
                        foreach($albums as $album) { ?>
                            <li><a onclick="moveImgToAlbum(this)" data-imgid="<?php echo $photo['id']?>" data-albumid="<?php echo $album['id']?>" href="javascript:void(0)"><?php echo $album['NAME']?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <span data-photoid="<?php echo $photo['id']?>" class="setCover glyphicon glyphicon-picture" data-toggle="tooltip" data-placement="top" title="设为封面" style="float: right;margin: 5px;color: #5f6297"></span>
                <a href="<?php echo $photo['PATH']?>" class="thumbnail">
                    <img src="<?php echo $photo['PATH']?>" alt="<?php echo $photo['NAME']?>">
                </a>
            </div>
        <?php }
    } ?>
    </div>
</div>

<script>
    /*鼠标移入提示*/
    $(function () {
        $('[data-toggle="dropdown"]').tooltip();
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="modal"]').tooltip();
    });

    $(".setCover").click(function(){
        if(confirm('确定设置为封面?！')){
            var $this = $(this);
            var photoid = $this.attr("data-photoid");
            if(!photoid){alert("相册id不存在");return;}
            $.ajax({
                url:'Data.php?id=setCover',
                type:'POST',
                data:{
                    'photoId':photoid
                },
                success:function(data){
                    data = JSON.parse(data);
                    if (data.stat == 200) {
                        alert(data.msg);
                        location.reload();
                    }
                },
                error:function(data){
                    data = JSON.parse(data);
                    console.log(data);
                }
            })
        }
    });

    function moveImgToAlbum(node){
        $.ajax({
            url:'Data.php?id=moveImage',
            type:'GET',
            data:{
                'imgid':node.getAttribute('data-imgid')||'',
                'albumid':node.getAttribute('data-albumid')||'',
                'imgsrc':node.getAttribute('data-imgsrc')||''
            },
            success:function(data){
                data = JSON.parse(data);
                console.log(data);
                if (data.stat == 200) {
                    alert(data.msg);
                    location.reload();
                }
            },
            error:function(data){
                data = JSON.parse(data);
                console.log(data);
            }
        })
    }

    function delImg(node){
        $.ajax({
            url:'Data.php?id=removeImage',
            type:'POST',
            data:{
                'imgid':node.getAttribute('data-imgid')||''
            },
            success:function(data){
                data = JSON.parse(data);
                console.log(data);
                if (data.stat == 200) {
                    alert(data.msg);
                    location.reload();
                }
            },
            error:function(data){
                data = JSON.parse(data);
                console.log(data);
            }
        })
    }
</script>

<!-- ************* 图片编辑模块 ************* -->
<div class="modal fade" id="imageEditor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
                <h4 class="modal-title" id="myModalLabel">编辑图片信息</h4>
            </div>
            <div class="modal-body" style="color:red;">
                由于暂时不需要进行图片信息维护，所以该功能暂不提供，尽请期待...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button id="editimg" type="button" class="btn btn-success" data-albumid="<?php echo $id?>">保存</button>
            </div>
        </div>
    </div>
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
                <div style="overflow: hidden;position: relative;float: none;margin: 0 auto">
                    <div id="imageUpload" data-selected="0">
                    </div>
                </div>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-tags"></span></span>
                    <input type="text" class="form-control" placeholder="添加该图片的描述" id="remark" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button id="saveimg" type="button" class="btn btn-success" data-albumid="<?php echo $id?>">保存</button>
            </div>

            <script>
                var albumid = document.getElementById("saveimg").getAttribute('data-albumid');
                if(albumid)
                $("#imageUpload").imageUploads(albumid,"#saveimg");
            </script>
        </div>
    </div>
</div>
<?php
include WIDGETS_DIR.'foot.php';
?>