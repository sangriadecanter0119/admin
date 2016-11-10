<script type="text/javascript">

    $(function () {
    	
        /*  File Uplaodダイアログ
        --------------------------------------------------------*/
        $("#file_upload_dialog").dialog({
            buttons: [
               {
                   text: "アップロード開始",
                   id: "upload_button",
                   click: function () {
                       $(this).simpleLoading('show');
                       $("#ImgForm").submit();
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
                     $("#file_list_dialog").remove();

                     /* フォームの再読み込み */
                     $(this).simpleLoading('show');                
                     $.post("<?php echo $html->url('fileForm').'/'.$category_nm ?>",function(html){                       
                        $('body').append(html);
                        $(this).simpleLoading('hide');
                     });
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
            modal: true,
            title: "アップロード結果"
        });

        /* ファイルが選択されるまではアップロードボタンを不可にする */
        $("#upload_button").attr("disabled", true);

        $(":input[type='file']").change(function () {
            $("#upload_button").attr("disabled",false);
        });

        var isFirstLoading = true;
        $("#UploadTarget").load(function(){
       
                if (isFirstLoading == false) {
                    $(this).simpleLoading('hide');
                  
                    var result = $.parseJSON($("#UploadTarget").contents().find("#upload_result")[0].innerHTML);           
                    if (result.data.isSuccess == "true") {
                        var imgSrc = <?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>;                  
                    } else {
                        var imgSrc = <?php echo "'".$html->webroot("/images/error_result.png")."'" ?>;
                    }

                  var tag = "<p id='result_message'><img src='" + imgSrc + "'  />" + result.data.message + "</p>";
                  $("#upload_result_dialog").append(tag);
                  $("#upload_result_dialog").dialog("open");            
                }     
        });
        isFirstLoading = false;
    });      

  
</script>
<div id="file_upload_dialog">
    <form id="ImgForm" class="content" method="post" name="ImgForm" enctype="multipart/form-data" target="UploadTarget" action="<?php echo $html->url('uploadImage')."/".$category_nm ?>">          
      <input type="file" name="data[ImgForm][ImgFile]" style="width:450px;" />                     
    </form>      
 
 <iframe id="UploadTarget" name="UploadTarget"  style="display:none" ></iframe> 

 <div id="upload_result_dialog"></div>
</div>
