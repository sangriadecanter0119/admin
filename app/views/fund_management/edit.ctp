<script type="text/javascript">
$(function(){
   $( ".datepicker" ).datepicker({
       dateFormat: 'yy/mm/dd',
       showOtherMonths: true,
       selectOtherMonths: true,
       numberOfMonths:3,
       beforeShow : function(){
             $('#ui-datepicker-div').css( 'font-size', '90%' );
       }
   });

   $("input:submit").button();

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
            title: "更新結果"
   });

    /* 更新 */
    $("#formID").submit(function(){

        if( $("#formID").validationEngine('validate')==false){ return false; }

        $("#formID input[type=text]").each(function(){
           $(this).attr("disabled",false);
        });

	   /* 更新開始 */
		$(this).simpleLoading('show');
		var formData = $("#formID").serialize();
		$.post("<?php echo $html->url('edit').'/'.$data['FundManagementTrnView']['id'] ?>",formData , function(result) {

		  $(this).simpleLoading('hide');

		  var obj = null;
	      try {
            obj = $.parseJSON(result);
          } catch(e) {
            obj = {};
            obj.result = false;
		    obj.message = "致命的なエラーが発生しました。";
		    obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
		    $("#result_dialog").data("status","false");
		    $("#critical_error").text(result);
          }

		  if(obj.result == true){
		     $("#result_message img").attr('src',"<?php echo $html->webroot("/images/confirm_result.png") ?>");
		      $("#result_dialog").data("status","true");
		  }else{
		     $("#result_message img").attr('src',"<?php echo $html->webroot("/images/error_result.png") ?>");
		      $("#result_dialog").data("status","false");
		  }
		    $("#result_message span").text(obj.message);
		    $("#error_reason").text(obj.reason);
            $("#result_dialog").dialog('open');
        });
		return false;
    });

});
</script>
<style type="text/css">
.FundManagement fieldset{ margin-bottom:15px; }
.FundManagement input   { margin-right:10px; }
.FundManagement p       { margin-top:10px; }
</style>

    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content FundManagement" method="post"  action="" >

        <fieldset>
		<legend>挙式</legend>
		  <p>
		           挙式申込金額：         <input type="text" name="data[FundManagementTrn][wedding_deposit]"    class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['wedding_deposit']) ?>" disabled />
                              挙式申込金受取日： <input type="text" name="data[FundManagementTrn][wedding_deposit_dt]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['wedding_deposit_dt']) ?>" disabled />
                              挙式代金：                 <input type="text" name="data[FundManagementTrn][wedding_fee]"        class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['wedding_fee']) ?>" />
                              挙式代金受取日：     <input type="text" name="data[FundManagementTrn][wedding_fee_dt]"     class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['wedding_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
		<legend>教会</legend>
		  <p>
		            教会デポジット：           <input type="text" name="data[FundManagementTrn][church_deposit]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['church_deposit']) ?>" />
                              教会デポジット受取日：<input type="text" name="data[FundManagementTrn][church_deposit_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['church_deposit_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
		<legend>パーティ</legend>
		  <p>
		           パーティ申込金額：       <input type="text" name="data[FundManagementTrn][party_deposit]"    class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['party_deposit']) ?>" />
                             パーティ申込金受取日：<input type="text" name="data[FundManagementTrn][party_deposit_dt]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['party_deposit_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>VISIONARI</legend>
		  <p>
		     VISIONARIデポジット：           <input type="text" name="data[FundManagementTrn][visionari_deposit]" class="inputnumeric"  value="<?php echo number_format($data['FundManagementTrnView']['visionari_deposit']) ?>" />
             VISIONARIデポジット受取日：<input type="text" name="data[FundManagementTrn][visionari_deposit_dt]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['visionari_deposit_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>旅行</legend>
		  <p>
		             旅行請求書発行日：<input type="text" name="data[FundManagementTrn][travel_invoice]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['travel_invoice']) ?>" />
                               旅行入金額：            <input type="text" name="data[FundManagementTrn][travel_fee]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['travel_fee']) ?>" />
                               旅行入金日：            <input type="text" name="data[FundManagementTrn][travel_fee_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['travel_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>ドレス</legend>
		  <p>
		            ドレス請求書発行日：<input type="text" name="data[FundManagementTrn][dress_invoice]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['dress_invoice']) ?>" />
                               ドレス入金額：           <input type="text" name="data[FundManagementTrn][dress_fee]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['dress_fee']) ?>" />
                               ドレス入金日：           <input type="text" name="data[FundManagementTrn][dress_fee_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['dress_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>アルバム</legend>
		  <p>
		            アルバム金額：            <input type="text" name="data[FundManagementTrn][album_fee]"    class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['album_fee']) ?>" />
                               アルバム金額受取日：<input type="text" name="data[FundManagementTrn][album_fee_dt]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['album_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>美容</legend>
		  <p>
		            美容KB請求書発行日：  <input type="text" name="data[FundManagementTrn][beauty_invoice]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['beauty_invoice']) ?>" />
                              美容KB入金額：               <input type="text" name="data[FundManagementTrn][beauty_fee]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['beauty_fee']) ?>" />
                              美容KB入金日：               <input type="text" name="data[FundManagementTrn][beauty_fee_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['beauty_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>エステ</legend>
		  <p>
		           エステ請求書発行日：<input type="text" name="data[FundManagementTrn][cosmetic_invoice]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['cosmetic_invoice']) ?>" />
                             エステ入金額：            <input type="text" name="data[FundManagementTrn][cosmetic_fee]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['cosmetic_fee']) ?>" />
                             エステ入金日：            <input type="text" name="data[FundManagementTrn][cosmetic_fee_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['cosmetic_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>歯</legend>
		  <p>
		           歯請求書発行日：<input type="text" name="data[FundManagementTrn][dental_invoice]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['dental_invoice']) ?>" />
                             歯入金額：            <input type="text" name="data[FundManagementTrn][dental_fee]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['dental_fee']) ?>" />
                             歯入金日：            <input type="text" name="data[FundManagementTrn][dental_fee_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['dental_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>グッズ</legend>
		  <p>
		         グッズ請求書発行日：<input type="text" name="data[FundManagementTrn][goods_invoice]" class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['goods_invoice']) ?>" />
                           グッズ入金額：            <input type="text" name="data[FundManagementTrn][goods_fee]"     class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['goods_fee']) ?>" />
                           グッズ入金日：            <input type="text" name="data[FundManagementTrn][goods_fee_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['goods_fee_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>お礼</legend>
		  <p>
		          ご紹介のお礼金額：        <input type="text" name="data[FundManagementTrn][kickback_fee]" class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['kickback_fee']) ?>" />
                            ご紹介のお礼支払い日：<input type="text" name="data[FundManagementTrn][kickback_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['kickback_dt']) ?>" />
          </p>
	   </fieldset>

	   <fieldset>
	   <legend>その他</legend>
		  <p>
		           その他1入金額：  <input type="text" name="data[FundManagementTrn][etc1_fee]" class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['etc1_fee']) ?>" />
                              その他1入金日：  <input type="text" name="data[FundManagementTrn][etc1_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['etc1_dt']) ?>" />
                              その他2入金額：  <input type="text" name="data[FundManagementTrn][etc2_fee]" class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['etc2_fee']) ?>" />
                              その他2入金日：  <input type="text" name="data[FundManagementTrn][etc2_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['etc2_dt']) ?>" />
                              その他3入金額：  <input type="text" name="data[FundManagementTrn][etc3_fee]" class="inputnumeric"             value="<?php echo number_format($data['FundManagementTrnView']['etc3_fee']) ?>" />
                              その他3入金日：  <input type="text" name="data[FundManagementTrn][etc3_dt]"  class="inputnumeric datepicker"  value="<?php echo $common->evalForShortDate($data['FundManagementTrnView']['etc3_dt']) ?>" />
          </p>
	   </fieldset>

    <input type="hidden" name="data[FundManagementTrn[id]" class="inputvalue" value="<?php echo $data['FundManagementTrnView']['id'] ?>" />

	<div class="submit">
	    <input type="submit" value="更新" />
	</div>
   </form>

<div id="result_dialog" style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>

