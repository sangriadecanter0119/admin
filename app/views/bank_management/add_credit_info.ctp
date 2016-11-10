<script type='text/javascript'>
$(function(){
	var url_normal         = <?php echo "'".$html->url('addCreditInfo')."'" ?>;
	var url_no_duplicate   = <?php echo "'".$html->url('addCreditInfo')."/false'" ?>;
	var url_yes_duplicate  = <?php echo "'".$html->url('addCreditInfo')."/true'" ?>;
	var url = url_normal;

	$("input:submit").button();
	/*  ファイル取り込みフォームの表示開始
    -------------------------------------------------*/
    $("#file_upload_link").click(function(){

         $(this).simpleLoading('show');
         $.post(<?php echo "'".$html->url('fileUploadForm')."'" ?>,function(html){
             $('body').append(html);
             $(this).simpleLoading('hide');
         });
         return false;
       });

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                	 url = url_normal;
                     $("#result_dialog").dialog('close');
                 }
             }],
              beforeClose: function (event, ui) {
                  $("#result_message span").text("");
		          $("#error_reason").text("");
             },
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             modal: true,
             title: "処理結果"
    });

    /* 確認用ダイアログ */
    $("#confirm_dialog").dialog({
             buttons: [{
                 text: "全部登録",
                 click: function () {
                	 url = url_yes_duplicate;
                	 $("#formID").submit();
                     $("#confirm_dialog").dialog('close');
                 }
             },
             {
                 text: "除いて登録",
                 click: function () {
                	 url = url_no_duplicate;
                	 $("#formID").submit();
                     $("#confirm_dialog").dialog('close');
                 }
             }],
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             width:"350px",
             modal: true,
             title: "確認"
         });

    /* 登録処理開始  */
	$("#formID").submit(function(){
		$(this).simpleLoading('show');

		   var formData = $("#formID").serialize();

		   $.post(url,formData , function(result) {

		      $(this).simpleLoading('hide');
	          var obj = null;
		      try {
	             obj = $.parseJSON(result);
	          } catch(e) {
	             obj = {};
	             obj.result = false;
			     obj.message = "致命的なエラーが発生しました。";
			     obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
			     $("#critical_error").text(result);
	          }

			  if(obj.result == true){
			      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);
			  }else{
				  if(obj.message.toUpperCase() == "DUPLICATE"){
					  $("#confirm_dialog_message").text("重複入金データが存在しますが一緒に登録しますか？" + obj.reason);
					  $("#confirm_dialog").dialog('open');
					  return;
			      }else{
			        $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
			      }
			  }
		      $("#result_message span").text(obj.message);
		      $("#error_reason").text(obj.reason);
	          $("#result_dialog").dialog('open');
	        });
	    return false;
	});
    /* 登録ボタンの初期表示*/
	$("#register_btn").attr("disabled",true);
});
</script>

    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
     <li><a href="#" id='file_upload_link'>入金ファイル取り込み(CSV)</a></li>
    </ul>

    <form id="formID" class="content" method="post" action="" >

		<table id="credit_list" class="list" cellspacing="0">
		 <tr><th style="text-align: center">No</th><th style="text-align: center">入金日</th>
		     <th style="text-align: center">顧客番号</th><th style="text-align: center">入金顧客名</th>
		     <th style="text-align: center">金額</th><th style="text-align: center">項目</th></tr>
	    </table>

	<div class="submit">
	    <input type="submit" id="register_btn"  class="inputbutton" value="登録" />
	</div>
   </form>

<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" /><span id="confirm_dialog_message"></span></p></div>
<div id="critical_error"></div>
