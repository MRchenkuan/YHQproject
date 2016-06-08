<!--here is head-->
<?php
//error_reporting(0);
session_start();
$pageID='photoLib';
require_once('../definitions.php');
include(WIDGETS_DIR.'head.php');
/*--连接数据库--*/
require_once(DATABASE_DAO_DIR."photoAlbumDAO.php");
$dao = new photoAlbumDAO();
$groups = $dao->getAllGroups();
?>

<!--album-->
<div class="panel panel-default" style="width: 960px;margin: 80px auto 0 auto">
    <div class="panel-heading">
        <h3 class="panel-title">图片相册</h3>
    </div>

    <div class="panel-body">
        <?php foreach($groups as $group) {?>
            <?php $albums = $dao->getAlbumsByGroupId($group['id']); ?>
            <div class="page-header" style="overflow: hidden">
                <h1 class="page-header"> <?php echo $group['NAME']?>  <span class="badge"><?php echo count($albums)?></span></h1>
            <?php foreach($albums as $album) { ?>
                <div class="col-xs-6 col-md-3">
                    <?php if($album['editable']==1){ ?>
                        <span onclick="if(!confirm('继续操作将删除此相册！'))return false;delAlbum(<?php echo $album['id']; ?>);"
                              class="glyphicon glyphicon-remove-sign"
                              data-toggle="tooltip"
                              data-placement="top"
                              title="删除相册"
                              style="float: right;margin: 5px;color: #e94513"></span>
                        <span onclick="fillAlertBoard(
                            '<?php echo $album['id']; ?>',<?php echo $group['id']; ?>,'<?php echo $album['NAME']; ?>','修改相册: <?php echo $group['NAME']; ?>','<?php echo $album['stat']; ?>','')"
                              style="float: right;margin: 5px;color: #05c133" class="glyphicon glyphicon-info-sign"
                              data-toggle="modal"
                              data-placement="top"
                              title="编辑相册"
                              data-target="#alertBoard"></span>
                    <?php } ?>
                    <a href="album.php?id=<?php echo $album['id']; ?>" class="thumbnail">
                        <img style="width: 70%" src="../PUBLIC/UI/<?php echo $album['count']==0?'folder-empty.png':'folder.png';?>" alt="...">
                        <div class="caption" style="text-align: center">
                            <button type="button" class="btn btn-info">
                                <?php echo $album['NAME'].' '; ?><span class="badge"><?php echo $album['count'] ;?></span>
                            </button>
                        </div>
                    </a>
                </div>
            <?php } ?>
                <!-- 新建相册区域 -->
                <div class="newAlbumBtn col-xs-6 col-md-3" data-toggle="modal" data-target="#alertBoard" data-group-id="<?php echo $group['id']; ?>" data-group-name="<?php echo $group['NAME']; ?>" >
                    <a href="#" class="thumbnail">
                        <img style="width: 70%" src="../PUBLIC/UI/area-add.png" alt="...">
                        <div class="caption" style="text-align: center">
                            <!-- 面板控制按钮 -->
                            <button type="button" class="btn btn-success" >
                                <span class="glyphicon glyphicon-plus"></span> 新建相册
                            </button>
                        </div>
                    </a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
        $(".newAlbumBtn").click(function(){
            var $this = $(this);
            /*创建时全填空*/
            fillAlertBoard('',$this.attr("data-group-id"),'','新建相册: '+$this.attr("data-group-name"),"","")
        });

        function fillAlertBoard(albumid,groupid,albumname,groupname,stat,cover){
            document.getElementById('albumid').value = albumid;
            document.getElementById('group_id').value = groupid;
            document.getElementById('album_name').value = albumname;
            document.getElementById('albumstat').setAttribute('albumstat',stat);
            document.getElementById('myModalLabel').innerHTML=groupname||'修改相册信息';
        }

        function delAlbum(id){
            $.ajax({
                url:'Data.php?id=delAlbum',
                data:{
                    'albumid':id
                },
                success:function(data){
                    data = eval('(' + data + ')');
                    console.log(data);
                    if (data.stat == 200) {
                        alert(data.msg);location.reload();
                    }
                },
                error:function(data){
                    data = eval('(' + data + ')');console.log(data);
                }
            })
        }

</script>
<!-- 弹出面板 -->
<div class="modal fade" id="alertBoard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title badge" id="myModalLabel">新建相册</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="albumid">
                <div class="input-group">
                    <span class="input-group-addon" id="groupid">相册组ID</span>
                    <input type="text" readonly id="group_id" class="form-control">
                </div>

                <div class="input-group">
                    <span class="input-group-addon" id="albumname">相册名字</span>
                    <input id="album_name" type="text" class="form-control" placeholder="请输入相册名字" aria-describedby="albumname">
                </div>

                <div class="input-group">
                    <span class="input-group-addon" id="albumdesc">相册描述</span>
                    <input id="album_desc" type="text" class="form-control" placeholder="请输入相册描述" aria-describedby="albumdesc">
                </div>

                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" id="albumstat" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        选择相册状态
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="albumstat">
                        <li><a href="javascript:void(0)" onclick="document.getElementById('albumstat').setAttribute('albumstat', 'enable');document.getElementById('albumstat').innerHTML=this.innerHTML+'<span class=\'caret\'></span>'">激　活</a></li>
                        <li><a href="javascript:void(0)" onclick="document.getElementById('albumstat').setAttribute('albumstat','disable');document.getElementById('albumstat').innerHTML=this.innerHTML+'<span class=\'caret\'></span>'">未激活</a></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-success" id="saveAlbumBtn">保存</button>
                <script>
                    $("#saveAlbumBtn").click(function () {
                        if(!confirm('是否保存相册《'+document.getElementById('album_name').value+'》?'))return false;
                        console.log(1);
                        $.ajax({
                            url:'Data.php?id=createAlbum',
                            type:"POST",
                            data:{
                                'albumid':document.getElementById('albumid').value||'',
                                'albumname':document.getElementById('album_name').value||'未命名',
                                'groupid':document.getElementById('group_id').value||"",
                                'desc':document.getElementById('album_desc').value||"",
                                'stat':document.getElementById('albumstat').getAttribute('albumstat')||'disable'},
                            success:function(data){
                                data = JSON.parse(data);
                                console.log(data);
                                if (data.stat == 200) {alert(data.msg);location.reload();}},
                            error:function(data){
                                data = eval('(' + data + ')');console.log(data);}
                        })
                    });
                </script>
            </div>
        </div>
    </div>
</div>
<!--here this foot-->
<?php
include WIDGETS_DIR."foot.php";
?>