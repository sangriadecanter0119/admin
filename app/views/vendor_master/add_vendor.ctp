<script type='text/javascript'>
$(function(){
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
             title: "処理結果"
    });

    /* ユーザー登録処理開始 */
    $("#formID").submit(function(){

       if( $("#formID").validationEngine('validate')==false){ return false; }
       $(this).simpleLoading('show');

	   var formData = $("#formID").serialize();

	   $.post(<?php echo "'".$html->url('addVendor')."'" ?>,formData , function(result) {

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
   <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
</ul>

    <form id="formID" class="content" method="post"  action="" >

		<table class="form" cellspacing="0">
	  	  <tr>
             <th>会社名<span class="necessary">(必須)</span></th>
             <td><input type="text" id="vendor_nm" name="data[VendorMst][vendor_nm]" class="validate[required,maxSize[50]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>担当者名</th>
             <td><input type="text" id="attend_nm" name="data[VendorMst][attend_nm]" class="validate[optional,maxSize[40]] inputname" value="" /></td>
          </tr>
          <tr>
             <th>業種区分</th>

             <td>
                 <select id="folder_id" name="data[VendorMst][vendor_kbn_id]">
   			        <?php

   			           for($i=0;$i < count($vendor_kbn_list);$i++)
   			           {
   			             $atr = $vendor_kbn_list[$i]['VendorKbnMst'];
   			             echo "<option value='{$atr['id']}'>{$atr['vendor_kbn_nm']}</option>";
   			           }

   			           //<option value=""selected="selected">成約</option>
   			           //<td><input type="text" name="data[VendorMst][vendor_kbn_nm]" class="inputvalue" value="" /></td>
   			        ?>
                 </select>
             </td>
          </tr>

          <tr>
             <th>国区分</th>
             <td>
                 <select id="folder_id" name="data[VendorMst][nation_kbn]">
   			         <option value="0" selected="selected">国外</option>
   			         <option value="1" >国内</option>
                 </select>
             </td>
          </tr>
          <tr>
             <th>郵便番号</th>
             <td><input type="text" id="zip_cd" name="data[VendorMst][zip_cd]" class="validate[optional,maxSize[10]] inputpostcode" value="" /></td>
          </tr>
          <tr>
             <th>住所</th>
             <td><input type="text" id="address" name="data[VendorMst][address]" class="validate[optional,maxSize[60]] inputtitle" value="" /></td>
          </tr>
          <tr>
             <th>電話番号</th>
             <td><input type="text" id="phone_no" name="data[VendorMst][phone_no]" class="validate[optional,custom[phone],,maxSize[15]] inputphone" value="" /></td>
          </tr>
          <tr>
             <th>携帯番号</th>
             <td><input type="text" id="cell_no" name="data[VendorMst][cell_no]" class="validate[optional,custom[phone],,maxSize[15]] inputphone" value="" /></td>
          </tr>
          <tr>
             <th>FAX番号</th>
             <td><input type="text" id="fax_no" name="data[VendorMst][fax_no]" class="validate[optional,custom[phone],,maxSize[15]] inputphone" value="" /></td>
          </tr>
          <tr>
             <th>E-MAIL</th>
             <td><input type="text" id="email" name="data[VendorMst][email]" class="validate[optional,custom[email],maxSize[50]] inputmailaddress" value="" /></td>
          </tr>
          <tr>
             <th>携帯MAIL</th>
             <td><input type="text" id="phone_mail" name="data[VendorMst][phone_mail]" class="validate[optional,custom[email],maxSize[50]] inputmailaddress" value="" /></td>
          </tr>
          <tr>
             <th>備考</th>
             <td colspan="3"><textarea id="note" name="data[VendorMst][note]"  class="inputcomment" rows="5"></textarea></td>
          </tr>
	    </table>

	<div class="submit">
		<input type="submit" class="inputbutton" value="追加" />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>