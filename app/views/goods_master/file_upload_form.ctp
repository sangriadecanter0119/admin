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

        /*  Uplaod結果ダイアログ
        --------------------------------------------------------*/
        $("#upload_result_dialog").dialog({
            buttons: [
               {
                   text: "OK",
                   click: function () {
                     $("#upload_result_dialog").dialog("close");
                   }
               }],
            beforeClose: function (event, ui) {
                $("#upload_result_dialog").remove();
                $("#file_upload_dialog").dialog("close");
            },
            draggable: false,
            autoOpen: false,
            resizable: false,
            zIndex: 5000,
            width: 380,
            modal: true,
            title: "アップロード結果"
        });

        /* ファイルが選択されるまではアップロードボタンを不可にする */
        $("#upload_button").attr("disabled", true);

        $(":input[type='file']").change(function () {
            $("#upload_button").attr("disabled",false);
        });

        $("#UploadTarget").load(function(){

                if (isUploading == true) {
                    $(this).simpleLoading('hide');
                    isUploading=false;

                    try{
                       var result = $.parseJSON($("#UploadTarget").contents().find("#upload_result")[0].innerHTML);

                       if (result.data.isSuccess == "true") {
                          var imgSrc = <?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>;

                        } else {
                          var imgSrc = <?php echo "'".$html->webroot("/images/error_result.png")."'" ?>;
                        }
                       var reasons = "";
                       if(result.data.reasons != null){
                          for(var i=0; i < result.data.reasons.length;i++){
                             reasons += "<br><span>" + result.data.reasons[i] + "</span>"
                          }
                       }

                       var tag = "<p id='result_message'><img src='" + imgSrc + "'  />" + result.data.message + reasons  + "</p>";
                       $("#upload_result_dialog").append(tag);
                       $("#upload_result_dialog").dialog("open");
                    }catch(ex){
                        alert(ex);
                    }
                }
        });
    });


</script>
<div id="file_upload_dialog">
    <form id="FileForm" class="content" method="post" name="FileForm" enctype="multipart/form-data" target="UploadTarget" action="<?php echo $html->url('uploadFile') ?>">
      <input type="file" name="data[ImgForm][ImgFile]" style="width:450px;" />
    </form>
 <iframe id="UploadTarget" name="UploadTarget"  style="display:none" ></iframe>

 <div id="upload_result_dialog"></div>
</div>
