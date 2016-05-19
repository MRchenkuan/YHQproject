<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 15/6/16
 * Time: 下午5:21
 */
$pageID = 'photoLib';
require($_SERVER['DOCUMENT_ROOT'] . '/backstage/definitions.php');
include(WIDGETS_DIR.'/head.php');
$id = $_GET['id'];


if(strtoupper(DB_TYPE)=='FILE'){
    include KODBC_PATH;
    $photobase = new Kodbc('T_TABLE_PHOTOBASE');
    $photoalbum = new Kodbc('T_TABLE_PHOTO_ALBUM');

    $thisalbum = $photoalbum->getById($id) or null;
    $photos = $photobase->getByAttr('albumid',$id) or null;
    $albums = $photoalbum->getAllItems();
}else{
    require_once(DATABASE_DAO_DIR."/photoAlbumDAO.php");
    $data = new photoAlbumDAO();
    $thisalbum = $data->getAlbumInfoById($id) or null;
    $photos = $data->getPhotoInfoByAlbumId($id) or null;
    $albums = $data->getAllAlbums();
}



?>
<!--album-->
<div class="panel panel-default"  style="width: 960px;margin: 60px auto 0 auto">

    <div class="panel-heading">
        <h3 class="panel-title">相册《<?php echo $thisalbum['remark'] ?>》下的图片</h3>
    </div>

    <!-- 文件上传控制按钮 -->
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#fileuploadpannel" style="margin: 20px 0 10px 30px;">
        <span class="glyphicon glyphicon-level-up"></span>上传图片
    </button>

    <div class="panel-body">
    <?php
    /*首先检查是否维护相册统计数*/
    if($thisalbum['count']!=count($photos)){
        $photoalbum->updateItem($id,array('count'=>count($photos)));
    }

    /*****************
     * 查看未绑定的图片
     *****************/
    if(!$id||$id=='0'||$id==''){
        require_once(WIDGETS_DIR.'getUnbindedImg.php');
        $unbindedimgs = getUnbindedImg(STATIC_DIR.'images/');
        foreach($unbindedimgs as $key=>$unbindeimg){
            if($photobase->getByAttr('imgsrc',$unbindeimg)){
                unset($unbindedimgs[$key]);
            }
        }
        /**更新未分类的图片数*/
        if($thisalbum['count']!=count($unbindedimgs)){
            $photoalbum->updateItem($id,array('count'=>(count($unbindedimgs)+count($photos))));
        }
        foreach($unbindedimgs as $photo){
            ?>
            <div class="col-xs-6 col-md-3">
                <span onclick="if(confirm('继续操作将删除此图片！'))delImg(this);" data-imgsrc="<?php echo $photo?>" class="glyphicon glyphicon-remove-sign" data-toggle="tooltip" data-placement="top" title="删除图片" style="float: right;margin: 5px;color: #e94513"></span>
                <span style="float: right;margin: 5px;color: #05c133" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-placement="top" title="编辑图片" data-target="#imageEditor"></span>
                <div class="dropdown" style="float: right;margin: 4px;color: #5f6297">
                    <span class="glyphicon glyphicon-globe dropdown-toggle" data-toggle="dropdown" id="dropdownMenu2" aria-haspopup="true" aria-expanded="false" data-placement="top" title="移动图片"></span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li class="dropdown-header">绑定图片到相册</li>
                        <?php foreach($albums as $album){ ?>
                            <li><a onclick="moveImgToAlbum(this)" data-imgsrc="<?php echo $photo?>" data-albumid="<?php echo $album['id']?>" href="javascript:void(0)"><?php echo $album['remark']?></a></li>
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
        foreach($photos as $photo){?>
            <div class="col-xs-6 col-md-3">
                <span onclick="if(confirm('继续操作将删除此图片！'))delImg(this);" data-imgsrc="<?php echo $photo['imgsrc']?>" data-imgid="<?php echo $photo['id']?>" class="glyphicon glyphicon-remove-sign" data-toggle="tooltip" data-placement="top" title="删除图片" style="float: right;margin: 5px;color: #e94513"></span>
                <span style="float: right;margin: 5px;color: #05c133" class="glyphicon glyphicon-info-sign" data-toggle="modal" data-placement="top" title="编辑图片" data-target="#imageEditor"></span>
                <div class="dropdown" style="float: right;margin: 4px;color: #5f6297">
                    <span class="glyphicon glyphicon-circle-arrow-right dropdown-toggle" data-toggle="dropdown" id="dropdownMenu2" aria-haspopup="true" aria-expanded="false" data-placement="top" title="移动图片"></span>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <li class="dropdown-header">移动图片到相册</li>
                        <?php
                        foreach($albums as $album) { ?>
                            <li><a onclick="moveImgToAlbum(this)" data-imgid="<?php echo $photo['id']?>" data-albumid="<?php echo $album['id']?>" href="javascript:void(0)"><?php echo $album['remark']?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                <a href="<?php echo $photo['imgsrc']?>" class="thumbnail">
                    <img src="<?php echo $photo['imgsrc']?>" alt="<?php echo $photo['remark']?>">
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
                data = eval('(' + data + ')');
                console.log(data);
                if (data.stat == 200) {
                    alert(data.msg);
                    location.reload();
                }
            },
            error:function(data){
                data = eval('(' + data + ')');
                console.log(data);
            }
        })
    }

    function delImg(node){
        $.ajax({
            url:'Data.php?id=removeImage',
            type:'GET',
            data:{
                'imgid':node.getAttribute('data-imgid')||'',
                'imgsrc':node.getAttribute('data-imgsrc')||''
            },
            success:function(data){
                data = eval('(' + data + ')');
                console.log(data);
                if (data.stat == 200) {
                    alert(data.msg);
                    location.reload();
                }
            },
            error:function(data){
                data = eval('(' + data + ')');
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
                <div class="col-xs-6 col-md-10" style="position: relative;float: none;margin: 0 auto">
                    <a href="#" class="thumbnail">
                        <img id="imguploadpreview" name="forupload" data-selected="0" src="../PUBLIC/UI/area-add.png" alt="添加图片">
                    </a>
                    <input id="imageupload" type="file" value="选择图片" accept="image/*" style="opacity:0;width:100%;height:100%;position: absolute;top:0;left: 0;">
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
                var uploadbtn = document.getElementById('imageupload');
                var uploadprv = document.getElementById('imguploadpreview');
                var savebtn = document.getElementById('saveimg');
                var albumid = savebtn.getAttribute('data-albumid');

                uploadbtn.addEventListener('click',function(){
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



                savebtn.addEventListener('click',function(){
                    var imgs = document.getElementsByName('forupload');
                    var remark = document.getElementById('remark').value;
                    var onlineurl = document.getElementById('onlineurl').value;
                    Array.prototype.some.call(imgs,function(it,id,ar){
                        if(it.getAttribute('data-selected')!=0||onlineurl){
                            savebtn.setAttribute('disabled','disabled');
                            savebtn.innerHTML = '上传中请稍等……';
                            $.ajax({
                                url:'Data.php?id=uploadImgAjax',
                                type:'POST',
                                data:{
                                    'imgDataString':it.src||'',
                                    'albumid':albumid,
                                    'remark':remark,
                                    'onlineurl':onlineurl
                                },
                                success:function(data){
                                    data = eval('(' + data + ')');
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
                                    data = eval('(' + data + ')');
                                    console.log(data);
                                    savebtn.removeAttribute('disabled');
                                    savebtn.innerHTML = 'data.msg';
                                }
                            });
                        }else{
                            alert('图片没上传或者没有填写网络图片地址！');
                        }
                    });
                })
            </script>
        </div>
    </div>
</div>
<?php
include WIDGETS_DIR.'foot.php';
?>