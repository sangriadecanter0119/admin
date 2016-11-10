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
	    	if( $("#formID").validationEngine('validate')==false){ return false; }
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

	   $.post(<?php echo "'".$html->url('editVendor')."'" ?>,formData , function(result) {

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

    <form id="formID" class="content" method="post"  action="" >

		<table class="form" cellspacing="0">
	  	  <tr>
	  	     <th>ベンダーID</th>
             <td><input type="hidden"  name="data[VendorMst][id]" value="<?php echo $data['VendorMst']['id'] ?>"/><?php echo $data['VendorMst']['id'] ?></td>
          </tr>
           <tr>
             <th>会社名<span class="necessary">(必須)</span></th>
             <td><input type="text" id="vendor_nm" name="data[VendorMst][vendor_nm]" class="validate[required,maxSize[50]] inputvalue" value="<?php echo $data['VendorMst']['vendor_nm'] ?>" /></td>
          </tr>
          <tr>
             <th>担当者名</th>
             <td><input type="text" id="attend_nm" name="data[VendorMst][attend_nm]" class="validate[optional,maxSize[40]] inputname" value="<?php echo $data['VendorMst']['attend_nm'] ?>" /></td>
          </tr>

          <tr>
             <th>業種区分</th>
             <td>
             <select name="data[VendorMst][vendor_kbn_id]">
   			        <?php

   			           for($i=0;$i < count($vendor_kbn_list);$i++)
   			           {
   			             $atr = $vendor_kbn_list[$i]['VendorKbnMst'];
   			             if($data['VendorMst']['vendor_kbn_id'] == $atr['id'])
   			             {
   			               echo "<option value='{$atr['id']}' selected='selected'>{$atr['vendor_kbn_nm']}</option>";
   			             }
   			             else
   			           {
   			               echo "<option value='{$atr['id']}'>{$atr['vendor_kbn_nm']}</option>";
   			             }
   			           }
   			        ?>
              </select>
              </td>
          </tr>

          <tr>
             <th>国区分</th>
             <td>
                 <select name="data[VendorMst][nation_kbn]">
   			          <?php
   			             if($data['VendorMst']['nation_kbn'] == 0)
   			             {
   			               echo "<option value='0' selected='selected'>国外</option>";
   			               echo "<option value='1' >国内</option>";
   			             }
   			             else
   			             {
   			               echo "<option value='0'>国外</option>";
   			               echo "<option value='1' selected='selected'>国内</option>";
   			             }
   			        ?>
                 </select>
             </td>
          </tr>
          <tr>
             <th>郵便番号</th>
             <td><input type="text" id="zip_cd" name="data[VendorMst][zip_cd]" class="validate[optional,maxSize[10]] inputpostcode" value="<?php echo $data['VendorMst']['zip_cd'] ?>" /></td>
          </tr>
          <tr>
             <th>住所</th>
             <td><input type="text" id="address" name="data[VendorMst][address]" class="validate[optional,maxSize[60]] inputtitle" value="<?php echo $data['VendorMst']['address'] ?>" /></td>
          </tr>
          <tr>
             <th>電話番号</th>
             <td><input type="text" id="phone_no" name="data[VendorMst][phone_no]" class="validate[optional,custom[phone],,maxSize[15]] inputphone" value="<?php echo $data['VendorMst']['phone_no'] ?>" /></td>
          </tr>
          <tr>
             <th>携帯番号</th>
             <td><input type="text" id="cell_no" name="data[VendorMst][cell_no]" class="validate[optional,custom[phone],,maxSize[15]] inputphone" value="<?php echo $data['VendorMst']['cell_no'] ?>" /></td>
          </tr>
          <tr>
             <th>FAX番号</th>
             <td><input type="text" id="fax_no" name="data[VendorMst][fax_no]" class="validate[optional,custom[phone],,maxSize[15]] inputphone" value="<?php echo $data['VendorMst']['fax_no'] ?>" /></td>
          </tr>
          <tr>
             <th>E-MAIL</th>
             <td><input type="text" id="email" name="data[VendorMst][email]" class="validate[optional,custom[email],maxSize[50]] inputmailaddress" value="<?php echo $data['VendorMst']['email'] ?>" /></td>
          </tr>
          <tr>
             <th>携帯MAIL</th>
             <td><input type="text" id="phone_mail" name="data[VendorMst][phone_mail]" class="validate[optional,custom[email],maxSize[50]] inputmailaddress" value="<?php echo $data['VendorMst']['phone_mail'] ?>" /></td>
          </tr>
          <tr>
             <th>備考</th>
             <td colspan="3"><textarea id="note" name="data[VendorMst][note]"  class="inputcomment" rows="5"><?php echo $data['VendorMst']['note'] ?></textarea></td>
          </tr>
	    </table>

	<div class="submit">
	     <input type="submit" id="update"  class="inputbutton"  name="update" value="更新" />
	     <?php
	       if($hasChild){
	        echo "<input type='submit' id='delete'  class='inputbutton'  name='delete' value='削除' disabled />";
	       }else{
	       	echo "<input type='submit' id='delete'  class='inputbutton'  name='delete' value='削除' />";
	       }
	    ?>
	</div>
   </form>
 <div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
 <div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
 <div id="critical_error"></div>
