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
                     $("#file_list_dialog").remove();
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

        /* 追記ボックスの表示有無 */
        if($("#credit_list td").length == 0){$("#postscript_checkbox").attr("disabled",true);}

        $("#UploadTarget").load(function(){

                if (isUploading == true) {
                    $(this).simpleLoading('hide');
                    isUploading=false;

                    try{
                       var result = $.parseJSON($("#UploadTarget").contents().find("#upload_result")[0].innerHTML);

                       if (result.data.isSuccess == "true") {
                          var imgSrc = <?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>;

                          var index = 0;
                          /* 追記モード判定 */
                          if($("#postscript_checkbox").attr("checked")){
                        	 index =  parseInt($("#credit_list tr:last-child td:first-child").text());
                          }else{
                        	  $("#credit_list td").remove();
                          }

                          /* 入金情報の設定 */
                          for(var i=0;  i < result.data.credit_list.length ;i++){

                              /* 顧客コードの候補の設定 */
                        	  var customerCodeList = null;
                        	  if(result.data.credit_list[i]['customer_info'].length == 0){
                        		  customerCodeList = "<input type='input'  name='data[CreditTrn]["+index+"][customer_cd]' value='' />";

                              }else if(result.data.credit_list[i]['customer_info'].length == 1){
                        		  customerCodeList = "<input type='input'  name='data[CreditTrn]["+index+"][customer_cd]' value='" + result.data.credit_list[i]['customer_info'][0].customer_cd + "' />";

                              }else{
                            	  customerCodeList = "<select name='data[CreditTrn]["+index+"][customer_cd]' style='width:150px;'>";

                                  for(var j=0; j < result.data.credit_list[i]['customer_info'].length ;j++){
                                      customerCodeList += "<option value='" + result.data.credit_list[i]['customer_info'][j].customer_cd + "'>" + result.data.credit_list[i]['customer_info'][j].customer_cd + "</option>";
                                   }
                                  customerCodeList += "</select>";
                              }

                        	  var row = "<tr><td style='text-align: center'>"+ (parseInt(index)+1) +"</td>" +
                        	                "<td style='text-align: center'>"+ result.data.credit_list[i].credit_dt   +"<input type='hidden' name='data[CreditTrn]["+index+"][credit_dt]' value='" + result.data.credit_list[i].credit_dt + "' /></td>"+
                        	                "<td style='text-align: center'>"+ customerCodeList + "</td>" +
                                            "<td style='text-align: center'>"+ result.data.credit_list[i].customer_nm +"<input type='hidden' name='data[CreditTrn]["+index+"][credit_customer_nm]' value='" + result.data.credit_list[i].customer_nm + "' /></td>"+
                                            "<td style='text-align: right'>" + result.data.credit_list[i].amount      +"<input type='hidden' name='data[CreditTrn]["+index+"][amount]' value='" + result.data.credit_list[i].amount + "' /></td>";

                        	  /* 入金種別リストの設定 */
                              var creditTypeList = "<select name='data[CreditTrn]["+index+"][credit_type_id]' style='width:150px;'>";

                              for(var j=0; j < result.data.credit_type_list.length;j++){

                            	  if(result.data.credit_list[i]['customer_info'].length == 1){

                            		  if((result.data.credit_list[i]['customer_info'][0].status_id == <?php echo CS_TRIP ?> &&
                            		      result.data.credit_type_list[j].id == <?php echo NC_TRAVEL ?>) ||
                            		     (result.data.credit_list[i]['customer_info'][0].status_id == <?php echo CS_DRESS ?>&&
                                  		 	   result.data.credit_type_list[j].id == <?php echo NC_DRESS ?>) ||
                                  		 (result.data.credit_list[i]['customer_info'][0].status_id == <?php echo CS_VENDOR ?>&&
                                       		 	   result.data.credit_type_list[j].id == <?php echo NC_VENDOR ?>)){

                            			  creditTypeList += "<option value='" + result.data.credit_type_list[j].id + "' selected>" + result.data.credit_type_list[j].name + "</option>";

                                	  }else{
                                		  creditTypeList += "<option value='" + result.data.credit_type_list[j].id + "'>" + result.data.credit_type_list[j].name + "</option>";
                                      }
                                  }else{
                                	  creditTypeList += "<option value='" + result.data.credit_type_list[j].id + "'>" + result.data.credit_type_list[j].name + "</option>";
                                  }
                              }
                              creditTypeList += "</select>";
                              row += "<td style='text-align: center'>"+ creditTypeList + "</td></tr>";

                             $("#credit_list").append(row);

                             /* 登録ボタンの有無 */
                             if($("#credit_list td").length == 0){
                                 $("#register_btn").attr("disabled",true);
                             }else{
                            	 $("#register_btn").attr("disabled",false);
                             }
                             index++;
                          }
                        } else {
                          var imgSrc = <?php echo "'".$html->webroot("/images/error_result.png")."'" ?>;
                        }
                       var tag = "<p id='result_message'><img src='" + imgSrc + "'  />" + result.data.message + "</p>";
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
    <form id="FileForm" class="content" method="post" name="FileForm" enctype="multipart/form-data" target="UploadTarget" action="<?php echo $html->url('uploadCreditFile') ?>">
      <input type="file" name="data[ImgForm][ImgFile]" style="width:450px;" />
    </form>
    <div style="margin-top:5px;"><input id="postscript_checkbox" type="checkbox" />追記</div>
 <iframe id="UploadTarget" name="UploadTarget"  style="display:none" ></iframe>

 <div id="upload_result_dialog"></div>
</div>
