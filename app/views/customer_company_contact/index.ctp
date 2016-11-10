<script type='text/javascript'>
$(function(){

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

    //フォーム送信前操作
	$("#formID").submit(function(){

		 $(this).simpleLoading('show');

		 var formData = $("#formID").serialize();

		 $.post(<?php echo "'".$html->url('editAnswer')."'" ?>,formData , function(result) {

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
	   return false;
	});
});
</script>

<ul class="operate">
  <li><a href="<?php echo $html->url('addContact')?>">手配</a></li>
</ul>
<table class="content">
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle">基本事項</legend>

	  <table class="viewheader">
	    <tr>
	       <th>顧客番号：</th><td class="short"><?php echo $customer['CustomerMstView']['customer_cd'] ?></td>
	       <th>ステータス：</th><td class="short"><?php echo $common->evalNbsp($customer['CustomerMstView']['status_nm'])?></td>
	       <th>挙式日：</th><td class="short">
	        <?php
	         //ステータスが成約以前の場合は挙式日は挙式予定日として顧客マスタに登録し、それ以外は契約テーブルに挙式日として登録する
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTED)
	         {
	         	echo $common->evalForShortDate($customer['CustomerMstView']['wedding_planned_dt']);
	         }
	         else
	         {
	         	echo $common->evalForShortDate($customer['CustomerMstView']['wedding_dt']);
	         }
	       ?>
	       </td>
	       <th>挙式会場：</th><td class="long"><?php echo $common->evalNbsp($customer['CustomerMstView']['wedding_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalForTime($customer['CustomerMstView']['wedding_time']) ?></td>
	       <th>レセプション会場：</th><td class="long"><?php echo $common->evalNbsp($customer['CustomerMstView']['reception_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalForTime($customer['CustomerMstView']['reception_time']) ?></td>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
</table>
<form id="formID" class="content" method="post" name="contact"" action="" >
	<table class="list" cellspacing="0">
	    <tr>
		<th>問い合わせ番号</th>
		<th>手配区分</th>
		<th><a href="">依頼日</a></th>
		<th>担当</th>
		<th>予約項目</th>
		<th>ベンダー</th>
        <th>内容区分</th>
		<th>内容</th>
		<th>依頼事項</th>
		<th>返答事項</th>
		<th>メール件名</th>
		<th><a href="">返答日</a></th>
		<th>備考</th>
	    </tr>
<?php
  $header_id = -1;
  $counter = 0;
  for($i=0;$i < count($data);$i++)
  {
  	  $atr = $data[$i]['ContactTrnView'];
  	  if($atr["id"] != $header_id){
  	    $header_id = $atr["id"];
    	echo  "<tr>".
	  		  	"<td><a href='".$html->url('editContact/').$atr['id']."'>{$atr['contact_no']}</a>".
	  	      		"<input type='hidden' name='data[ContactTrn][".$counter."][id]' value='{$atr['id']}'>".
	  	  		"</td>".
          		"<td>".$common->evalNbsp($atr['contact_kbn_nm'])."</td>".
		  		"<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
    	  		"<td>".$common->evalNbsp($atr['sender_nm'])."</td>".
		  		"<td>".$common->evalNbsp($atr['goods_ctg_nm'])."</td>".
		  		"<td>".$common->evalNbsp($atr['vendor_nm'])."</td>".
		  		"<td>".$common->evalNbsp($atr['content_kbn_nm'])."</td>".
		  		"<td><div style='overflow:hidden;width:50px;'>".$common->evalNbsp($atr['content'])."</div></td>".
    	  		"<td>".$common->evalNbsp($atr['question_kbn_nm'])."</td>";

     		echo "<td><select class='answer_kbn' name='data[ContactTrn][". $counter."][answer_kbn]'>";
            	     if($atr['answer_kbn'] == 0)
                	 {
                 		echo "<option value='0' selected='selected'></option>";
                 		echo "<option value='1'>OK</option>";
                 	 }
                 	 else if($atr['answer_kbn'] == 1)
                 	 {
                 		echo "<option value='0'></option>";
                 		echo "<option value='1' selected='selected'>OK</option>";
                 	 }
		       		 "</select>".
		  		"</td>";

		  echo  "<td><div style='overflow:hidden;width:50px;'>".$common->evalNbsp($atr['title'])."</div></td>".
		  		"<td>".$common->evalNbspForShortDate($atr['answer_dt'])."</td>".
		  		"<td><div style='overflow:hidden;width:50px;'>".$common->evalNbsp($atr['note'])."</div></td>".
              "</tr>";
		   $counter++;
  	  }
  }
?>
	</table>
	<div class="submit">
	    <input type="submit" class="inputbutton" value="更新" <?php if(count($data) < 1){ echo "disabled";} ?>  />
	</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>


