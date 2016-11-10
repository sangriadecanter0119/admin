<script type='text/javascript'>
$(function(){

	$("input:submit").button();

	//入力マスク
	$(".year").mask("9999");

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
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

    //フォーム送信前操作
	$("#formID").submit(function(){
	    StartSubmit()
	    return false;
	});

	/* 更新処理開始  */
	function StartSubmit(){

	   $(this).simpleLoading('show');
	   var formData = $("#formID").serialize();

	   $.post(<?php echo "'".$html->url('duplicateGoods')."'" ?>,formData , function(result) {

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
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
		  }

	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
	}
});
</script>

<ul class="operate">
   <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
</ul>

    <form id="formID" class="content" method="post"  action="" >

		<table class="form" cellspacing="0">
	  	  <tr>
             <th>基準年度</th>
             <td>
               <select name="data[GoodsMst][src_year]" style='width:65px;'>
                 <?php
                 for($i=0;$i < count($years);$i++){
                   echo "<option value='{$years[$i]['GoodsMst']['year']}'>{$years[$i]['GoodsMst']['year']}</option>";
                 }
                 ?>
               </select>
               年度
              </td>
          </tr>
          <tr>
             <th>新年度</th>
             <td><input type="text" class="year" name="data[GoodsMst][new_year]" style='width:60px;margin-right:5px' /><span>年度</span></td>
          </tr>
	    </table>

	<div class="submit">
		<input type="submit" class='inputbutton' value="複製" />
	</div>
   </form>

<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
