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
<div class="panel panel-default" style="width: 960px;margin: 60px auto 0 auto">
    <!-- Default panel contents -->
    <div class="panel-heading"><span class="glyphicon glyphicon-calendar"></span> 已有广告
        <button type="button" class="btn btn-default" style="float: right" onclick="document.getElementById('ad_id').value='';location='#donewAdvt'"><span class="glyphicon glyphicon-plus"></span>新增一条广告</button>
    </div>
    <div class="panel-body">
        下面表格展示了已经添加了的广告,序号越大,排序越靠前
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
                        <button type="button"
                                class="btn btn-default"
                                onclick="fillForMod(this)"
                                data-id="<?php echo $items['id']?>"
                                data-index="<?php echo $items['ORDER']?>"
                                data-cover="<?php echo $items['THUMB']?>"
                                >
                            <span class="glyphicon glyphicon-list-alt"></span>
                        </button>
                    </div>
                </td>
            </tr>

        <?php }?>
        </table>

    <!--分页组件-->
    <?php include '../widgets/pageSliceBar.php' ?>
</div>

<!--创建新广告-->
<div class="panel panel-default" style="width: 960px;margin: 60px auto 0 auto;" id="donewAdvt">
    <!-- Default panel contents -->
    <div class="panel-heading"><span class="glyphicon glyphicon-calendar"></span> 新建一条广告</div>
    <div class="panel-body">
        下面表格用来添加新的广告
    </div>
    <div  style="margin: 20px;">
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">广告艾迪</span>
            <input id="ad_id" type="number" class="form-control" readonly placeholder="自动填写" aria-describedby="basic-addon1">
        </div>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">广告顺序</span>
            <input id="ad_index" type="number" class="form-control" placeholder="填写广告序号" aria-describedby="basic-addon1">
        </div>
        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">图片上传</span>
            <iframe style="height: 100px;" id="ad_img" src="fileupload.html" aria-describedby="basic-addon1" class="form-control"></iframe>
        </div>
    </div>
    <button style="margin:20px 0;min-width: 100%;" type="button" class="btn btn-default" onclick="submitDate(this)"><span class="glyphicon glyphicon-list-alt"></span>添加一条</button>
</div>
<script>

    function submitDate(self){
        var imgsrcobj = document.getElementById('ad_img').contentWindow.document.getElementById('uploadCallBack-ImgSrc');
        var imgsrc;
        if(imgsrcobj){
            imgsrc = imgsrcobj.getAttribute('src');
        }else{
            imgsrc = 'http://tangweimm.com/img/imgveiw/3.jpg';
        }
        var ad_id     = document.getElementById('ad_id').value;
        var ad_index  = document.getElementById('ad_index').value;
        if(!(imgsrc&&ad_index)){
            alert('信息不全');
            return false;
        }else{
            self.innerHTML = '提交中...';
            $.ajax({
                url: './Data.php',
                data:{
                    id: 'createAdvt',
                    order:ad_index,
                    adid:ad_id,
                    imgsrc:imgsrc
                },
                complete: function (data) {
                    console.log(data.responseText);
                    var rep = JSON.parse(data.responseText);
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
    }

    function delRecord(id){
        $.ajax({
            url: './Data.php',
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
    function fillForMod(node){
        var ad_id  = document.getElementById('ad_id').value=node.getAttribute('data-id')||'';
        var ad_index  = document.getElementById('ad_index').value=node.getAttribute('data-index')||'';
        console.log(node.getAttribute('data-cover'));
        var imgsrcobj = document.getElementById('ad_img').contentWindow.document.getElementById('uploadCallBack-ImgSrc').src=node.getAttribute('data-cover')||'';
    }
</script>

<!--this is foot-->
<?php
include WIDGETS_DIR."foot.php";
?>