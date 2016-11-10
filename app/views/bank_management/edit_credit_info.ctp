<script type='text/javascript'>
$(function(){

	$("input:submit").button();
    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');

                     if($("#result_dialog").data("action").toUpperCase() == "DELETE" ){
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = <?php echo "'".$html->url('.')."'" ?>;
                        }
                     }
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
                 text: "OK",
                 click: function () {
                     $("#confirm_dialog").dialog('close');
                     StartSubmit();
                 }
             },
             {
                 text: "CANCEL",
                 click: function () {
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

    //フォーム送信前操作
	$("#formID").submit(function(){

	    switch($("#result_dialog").data("action").toUpperCase())
	    {
	     case "DELETE":
	        $("#confirm_dialog").dialog('open');
	        break;
	     case "UPDATE":
	        StartSubmit()
	        break;
	    }
		return false;
	});

	$(".inputbutton").click(function(){
	  $("#result_dialog").data("action",$(this).attr("name"));
	});

	/* 更新処理開始  */
	function StartSubmit(){

	   $(this).simpleLoading('show');

	   var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");

	   $.post(<?php echo "'".$html->url('editCreditInfo')."'" ?>,formData , function(result) {

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
		      $("#result_dialog").data("status","true");
		  }else{
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
		      $("#result_dialog").data("status","false");
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

    <form id="formID" class="content" method="post" action="" >

		<table class="form" cellspacing="0">
		  <tr>
	  	     <th>お内金除外</th>
             <td>
             <?php
   			     if($data['CreditTrnView']['uchikin_exception_flg'] == 0){
   			     	echo  "<input type='checkbox' name='data[CreditTrn][uchikin_exception_flg]' />";
   			     }else{
   			   	    echo  "<input type='checkbox' name='data[CreditTrn][uchikin_exception_flg]' checked />";
   			     }
   	         ?>
             </td>
          </tr>
	  	  <tr>
	  	     <th>入金日</th>
             <td><?php echo $common->evalForShortDate($data['CreditTrnView']['credit_dt']) ?></td>
          </tr>
          <tr>
             <th>顧客番号</th>
             <td><?php echo $data['CreditTrnView']['customer_cd'] ?></td>
          </tr>
          <tr>
             <th>顧客名</th>
             <td><?php echo $data['CreditTrnView']['grmls_kj']." ".$data['CreditTrnView']['grmfs_kj'] ?></td>
          </tr>
           <tr>
             <th>入金顧客名</th>
             <td><?php echo $data['CreditTrnView']['credit_customer_nm'] ?></td>
          </tr>
           <tr>
             <th>入金額</th>
             <td><?php echo number_format($data['CreditTrnView']['amount']) ?></td>
          </tr>
          <tr>
             <th>入金項目</th>
             <td><?php echo $data['CreditTrnView']['credit_type_nm'] ?>
             <!--
                    <select name="data[CreditTrn][credit_type_id]" style="width:100px;">
                     <?php
   			           for($i=0;$i < count($credit_type_list);$i++){

   			             $atr = $credit_type_list[$i]['CreditTypeMst'];
   			             if($atr['id'] == $data['CreditTrnView']['credit_type_id']){
   			             	echo "<option value='{$atr['id']}' selected>{$atr['credit_type_nm']}</option>";
   			             }else{
   			             	echo "<option value='{$atr['id']}'>{$atr['credit_type_nm']}</option>";
   			             }
   			           }
   			        ?>
   			      </select>
   			  -->
   			 </td></tr>
	    </table>

	<div class="submit">
          <?php
             if($data['CreditTrnView']['status_id'] != CS_PAIED && $data['CreditTrnView']['status_id'] != CS_POSTPONE &&
                $data['CreditTrnView']['status_id'] != CS_CANCEL){
               echo "<input type='submit' id='update'  class='inputbutton' style='margin-right:10px'  name='update' value='更新' />";
               echo "<input type='submit' id='delete'  class='inputbutton'  name='delete' value='削除' />";
             }
          ?>

	    <input type='hidden' name='data[CreditTrn][id]' value="<?php echo $data['CreditTrnView']['id'] ?>" />
	    <input type='hidden' name='data[CreditTrn][customer_id]' value="<?php echo $data['CreditTrnView']['customer_id'] ?>" />
	    <input type='hidden' name='data[CreditTrn][status_id]' value="<?php echo $data['CreditTrnView']['status_id'] ?>" />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
