<script type="text/javascript">

    $(function () {
    	 var isUploading = false;

        /*  File Uplaodダイアログ
        --------------------------------------------------------*/
        $("#file_upload_dialog").dialog({
            buttons: [
               {
                   text: "アップロード開始",
                   id: "upload_button",
                   click: function () {
                       $(this).simpleLoading('show');
                       isUploading = true;
                       $("#FileForm").submit();
                   }
               },
               {
                   text: "キャンセル",
                   click: function () { $("#file_upload_dialog").dialog("close"); }
               }],
            beforeClose: function (event, ui) {
                $("#file_upload_dialog").remove();
            },
            draggable: false,
            autoOpen: true,
            resizable: false,
            zIndex: 4000,
            width: 480,
            modal: true,
            title: "File Uplaod"
        });

        $("#UploadTarget").load(function(){

                if (isUploading == true) {
                    $(this).simpleLoading('hide');
                    isUploading=false;

                    try{
                       var result = $.parseJSON($("#UploadTarget").contents().find("#upload_result")[0].innerHTML);
                       alert(result.data.isSuccess+":"+result.data.message);

                    }catch(ex){
                        alert(ex);
                    }
                }
        });
    });


</script>
<div id="file_upload_dialog">
    <form id="FileForm" class="content" method="post" name="FileForm" enctype="multipart/form-data" target="UploadTarget" action="<?php echo $html->url('uploadWeddingFile') ?>">
      <input type="file" name="data[ImgForm][ImgFile]" style="width:450px;" />
    </form>
 <iframe id="UploadTarget" name="UploadTarget"  style="display:none" ></iframe>
</div>
