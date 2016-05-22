/**
 * Created by mac on 16/5/22.
 */
(function(w,$){
    w.getAlbumsByGroupId = function(groupId,callback){
        $.ajax({
            url:"./backstage/pages/Data.php?id=getAlbumsByGroupId&groupId="+groupId,
            method:"GET",
            success:function (e) {
                callback(e);
            }
        });
    }
})(window,$);