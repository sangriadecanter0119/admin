<?php
//郵便番号から自動住所入力のライブラリ[ajaxzip3]
//echo $html->script("http://ajaxzip3.googlecode.com/svn/trunk/ajaxzip3/ajaxzip3.js",false);
echo $html->script("https://ajaxzip3.github.io/ajaxzip3.js",false);
?>
<script type='text/javascript'>
$(function(){
	$("input:submit").button();

	   //入力マスク
	   $("#grm_birth,#brd_birth,#wedding_dt,#first_visited_dt,#first_contact_dt").mask("9999/99/99");
	   $("#grm_postcode,#brd_postcode").mask("999-9999");
	   $("#grm_cell,#brd_cell").mask("999-9999-9999");
	   $("#customer_cd").mask("999999-999999");

	   //日付入力補助のプラグイン
	   $( ".datepicker" ).datepicker({
	       dateFormat: 'yy/mm/dd',
	       showOtherMonths: true,
	       selectOtherMonths: true,
	       numberOfMonths:3,
	       beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' ) ; }
	   });

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

	   $.post(<?php echo "'".$html->url('addCustomer')."'" ?>,formData , function(result) {

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
		      $("#customer_code").text(obj.code);
		  }else{
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
      return false;
    });

    $("#leading2").change(function(){ SetIntroducerField();});
	SetIntroducerField();
});

/* 導線2の選択により紹介者フィールドを可・不可にする
----------------------------------------------------------*/
function SetIntroducerField(){

  if($("#leading2").val() == <?php echo LD2_INTRODUCING ?>){
    $("#introducer").css("display","inline");
    $("#introducer_label").css("display","inline");
  }else{
    $("#introducer").css("display","none");
    $("#introducer_label").css("display","none");
    $("#introducer").val("");
  }
}
</script>

<ul class="operate">
	<li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
</ul>

<form id="formID" class="content" method="post" name="customer" action="">
<fieldset class="headerlegend">
      <legend class="legendtitle">基本事項</legend>
	  <table class="viewheader">
	    <tr>
	       <th>顧客番号：</th><td id="customer_code">&nbsp;</td>
	       <th>ステータス：</th>
	       <td>  <?php
		           for($i=0;$i < count($status_list);$i++){
                       if($status_list[$i]['CustomerStatusMst']['id'] == CS_CONTACT){
                          echo "<span>{$status_list[$i]['CustomerStatusMst']['status_nm']}</span>";
                       }
		           }
		           echo "<input type='hidden' id='status' name='data[CustomerMst][status_id]'  value='".CS_CONTACT."'>";
		         ?>
	       </td>
	       <th>挙式日：</th><td class="inputdate"><input type='text' id='wedding_dt' name='data[CustomerMst][wedding_planned_dt]' class='validate[optional,custom[date]] inputdate datepicker'  /></td>
	       <th>問い合わせ日：<span class="necessary">【必須】</span></th><td class="inputdate"><input type="text"  id="first_contact_dt" name="data[CustomerMst][first_contact_dt]" class="validate[required,custom[date]] inputdate datepicker"  /></td>
	       <th>新規接客日：</th><td class="inputdate"><input type="text"   id="first_visited_dt" name="data[CustomerMst][first_visited_dt]" class="validate[optional,custom[date]] inputdate datepicker"  /></td>
	    </tr>
	    <tr>
	       <th>新規担当者：</th><td>
	       <select id="first_contact_person_nm" name="data[CustomerMst][first_contact_person_nm]" style="width:100px">
	       <option value=''></option>
	       <?php
		           for($i=0;$i < count($attendant_list);$i++){
                      echo "<option value='$attendant_list[$i]'>{$attendant_list[$i]}</option>";
		           }
		   ?>
		   </select>
	       </td>
	       <th>プラン担当者：</th><td>
	       <select id="process_person_nm" name="data[CustomerMst][process_person_nm]" style="width:100px">
	       <option value=''></option>
	       <?php
		           for($i=0;$i < count($attendant_list);$i++){
                      echo "<option value='$attendant_list[$i]'>{$attendant_list[$i]}</option>";
		           }
		   ?>
		   </select>
		   </td>
	     <!--  <th>見積担当者：</th>  <td><input type="text"   id="estimate_created_person_nm" name="data[CustomerMst][estimate_created_person_nm]" class="inputname" value='' /></td>-->
	       <th>&nbsp;</th><td>&nbsp;</td>
	    </tr>
	    <tr>
	       <th>導線1：</th>
	       <td>
	       <select name="data[CustomerMst][leading1]" style="width:100px">
	       <?php
		           for($i=0;$i < count($leading1_list);$i++){
                      	echo "<option value='$i'>{$leading1_list[$i]}</option>";
		           }
		   ?>
		   </select>
	       </td>

	       <th>導線2：</th>
	       <td>
	       <select id="leading2" name="data[CustomerMst][leading2]" style="width:100px">
	       <option value=''></option>
	       <?php
		           for($i=0;$i < count($leading2_list);$i++){
                      	echo "<option value='$i'>{$leading2_list[$i]}</option>";
		           }
		   ?>
		   </select>
		   </td>
           <th id="introducer_label">紹介者</th>
           <td colspan="2"><input type="text" id="introducer" name="data[CustomerMst][introducer]" value='' style="width:130px" /></td>
	       <td colspan="2"></td>
	    </tr>
	  </table>
	</fieldset>
<table border="0" cellpadding="0" cellspacing="15">
<tr>
<td>
   <table class="form" cellspacing="0">
		   <tr>
                <th>【新郎】</th>
                <td colspan="3"></td>
           </tr>
		   <tr>
                <th>姓漢字</th>
                <td><input type="text"  id="grmls_kj" name="data[CustomerMst][grmls_kj]" class="validate[optional,maxSize[20]] inputname" value="" />
                    <input type="radio" value="0" name="data[CustomerMst][prm_lastname_flg]" checked />代表
                </td>
                <th style="text-align: center">名漢字</th>
                <td><input type="text" id="grmfs_kj" name="data[CustomerMst][grmfs_kj]" class="validate[optional,maxSize[20]] inputname" value="" /></td>
           </tr>
           <tr>
                <th>姓カナ</th>
                <td><input type="text" id="grmls_kn" name="data[CustomerMst][grmls_kn]" class="validate[optional,maxSize[20]] inputname" value="" /></td>
                <th style="text-align: center">名カナ</th>
                <td><input type="text" id="grmfs_kn" name="data[CustomerMst][grmfs_kn]" class="validate[optional,maxSize[20]] inputname" value="" /></td>
           </tr>
	   	   <tr>
                <th>姓ローマ字</th>
                <td><input type="text" id="grmls_rm" name="data[CustomerMst][grmls_rm]" class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="" /></td>
                <th style="text-align: center">名ローマ字</th>
                <td><input type="text" id="grmfs_rm" name="data[CustomerMst][grmfs_rm]" class="validate[optional,custom[onlyLetterSp],maxSize[20]] text-input inputname" value="" /></td>
           </tr>
           <tr>
                <th>誕生日</th>
                <td colspan="3"><input type="text" id="grm_birth" name="data[CustomerMst][grmbirth_dt]" class="validate[optional,custom[date]] text-input inputdate" value="" /></td>
           </tr>
		   <tr>
                <th>郵便番号</th>
                <td colspan="3">
			    <input type="text" name="data[CustomerMst][grm_zip_cd]" id="grm_postcode" class="validate[optional,custom[postcode]] inputpostcode" value=""
			           onKeyUp="AjaxZip3.zip2addr(this,'','data[CustomerMst][grm_pref]','data[CustomerMst][grm_city]','data[CustomerMst][grm_address]','data[CustomerMst][grm_address]');">
                <input type="radio" value="0" name="data[CustomerMst][prm_address_flg]" checked/>代表
                </td>
		   </tr>
		   <tr>
                 <th>都道府県</th>
                 <td colspan="3">
                  <select name="data[CustomerMst][grm_pref]">
                    <option value="" selected></option>
			     	<option value="北海道">北海道</option><option value="青森県">青森県</option>
					<option value="岩手県">岩手県</option><option value="宮城県">宮城県</option>
					<option value="秋田県">秋田県</option><option value="山形県">山形県</option>
					<option value="福島県">福島県</option><option value="茨城県">茨城県</option>
					<option value="栃木県">栃木県</option><option value="群馬県">群馬県</option>
					<option value="埼玉県">埼玉県</option><option value="千葉県">千葉県</option>
					<option value="東京都">東京都</option><option value="神奈川県">神奈川県</option>
					<option value="新潟県">新潟県</option><option value="富山県">富山県</option>
					<option value="石川県">石川県</option><option value="福井県">福井県</option>
					<option value="山梨県">山梨県</option><option value="長野県">長野県</option>
					<option value="岐阜県">岐阜県</option><option value="静岡県">静岡県</option>
					<option value="愛知県">愛知県</option><option value="三重県">三重県</option>
					<option value="滋賀県">滋賀県</option><option value="京都府">京都府</option>
					<option value="大阪府">大阪府</option><option value="兵庫県">兵庫県</option>
					<option value="奈良県">奈良県</option><option value="和歌山県">和歌山県</option>
					<option value="鳥取県">鳥取県</option><option value="島根県">島根県</option>
					<option value="岡山県">岡山県</option><option value="広島県">広島県</option>
					<option value="山口県">山口県</option><option value="徳島県">徳島県</option>
					<option value="香川県">香川県</option><option value="愛媛県">愛媛県</option>
					<option value="高知県">高知県</option><option value="福岡県">福岡県</option>
					<option value="佐賀県">佐賀県</option><option value="長崎県">長崎県</option>
					<option value="熊本県">熊本県</option><option value="大分県">大分県</option>
					<option value="宮崎県">宮崎県</option><option value="鹿児島県">鹿児島県</option>
					<option value="沖縄県">沖縄県</option>
				 </select>
		    	</td>
           </tr>
           <tr>
                 <th>市町村区</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_city]" id="grm_city_kj" class="validate[optional,maxSize[20]] inputname" value="" />
                 </td>
           </tr>
           <tr>
                 <th>住所番地</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_street]" id="grm_street_kj" class="validate[optional,maxSize[60]] inputtitle" value="" />
                 </td>
           </tr>
		   <tr>
                 <th>アパート・マンション</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_apart]" id="grm_part_kj" class="validate[optional,maxSize[20]] inputtitle" value="" />
			     </td>
           </tr>
           <tr>
                 <th>住所(ローマ字)</th><td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_address_rm]" id="grM_address_rm" class="validate[optional,maxSize[120],custom[onlyLetterNumber]] inputtitle" value="" /></td>
           </tr>
		   <tr>
                 <th>電話番号</th>
                 <td  colspan="3"><input type="text" id="grm_phone" name="data[CustomerMst][grm_phone_no]" class="validate[optional,custom[phone]] inputphone" value="" />
                 <input type="radio" value="0" name="data[CustomerMst][prm_phone_no_flg]" />代表</td>
            </tr>
		    <tr>
                 <th>携帯電話番号</th>
                 <td  colspan="3"><input type="text" id="grm_cell" name="data[CustomerMst][grm_cell_no]" class="validate[optional,custom[phone]] inputphone" value="" /></td>
            </tr>
  		    <tr>
                 <th>E-MAILアドレス</th>
                 <td colspan="3"><input type="text" id="grm_email" name="data[CustomerMst][grm_email]" class="validate[optional,custom[email]] inputmailaddress" value="" />
                 <input type="radio" value="0" name="data[CustomerMst][prm_email_flg]" />代表</td>
            </tr>
		    <tr>
                 <th>携帯メールアドレス</th>
                 <td colspan="3"><input type="text" id="grm_phonemail" name="data[CustomerMst][grm_phone_mail]" class="validate[optional,custom[email]] inputmailaddress" value="" /></td>
            </tr>
		    <tr>
                  <th>備考</th><td colspan="3"><textarea id="note" name="data[CustomerMst][note]" class="inputcomment" rows="5"></textarea></td>
             </tr>
	</table>
</td>
<td>
   <table class="form" cellspacing="0">
		   <tr>
                <th>【新婦】</th>
                <td colspan="3"></td>
           </tr>
		   <tr>
                <th>姓漢字</th>
                <td><input type="text" id="brdls_kj" name="data[CustomerMst][brdls_kj]" class="validate[optional,maxSize[20]] inputname" value="" />
                    <input type="radio" value="1" name="data[CustomerMst][prm_lastname_flg]" />代表
                </td>
                <th style="text-align: center">名漢字</th>
                <td><input type="text" id="brdfs_kj" name="data[CustomerMst][brdfs_kj]" class="validate[optional,maxSize[20]] inputname" value="" /></td>
           </tr>
           <tr>
                <th>姓カナ</th>
                <td><input type="text" id="brdls_kn" name="data[CustomerMst][brdls_kn]" class="validate[optional,maxSize[20]] inputname" value="" /></td>
                <th style="text-align: center">名カナ</th>
                <td><input type="text" id="brdfs_kn" name="data[CustomerMst][brdfs_kn]" class="validate[optional,maxSize[20]] inputname" value="" /></td>
           </tr>
	   	   <tr>
		        <th>姓ローマ字</th>
                <td><input type="text" id="brdls_rm" name="data[CustomerMst][brdls_rm]"  class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="" /></td>
                <th style="text-align: center">名ローマ字</th>
                <td><input type="text" id="brdfs_rm" name="data[CustomerMst][brdfs_rm]"  class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="" /></td>
           </tr>
           <tr>
		        <th>誕生日</th>
                <td colspan="3"><input type="text" id="brd_birth" name="data[CustomerMst][brdbirth_dt]"  class="validate[optional,custom[date]] inputdate" value="" /></td>
           </tr>
		   <tr>
                <th>郵便番号</th>
                <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_zip_cd]" id="brd_postcode" class="validate[optional,custom[postcode]] inputpostcode" value=""
			           onKeyUp="AjaxZip3.zip2addr(this,'','data[CustomerMst][brd_pref]','data[CustomerMst][brd_city]','data[CustomerMst][brd_address]','data[CustomerMst][brd_address]');">
                 <input type="radio" value="1" name="data[CustomerMst][prm_address_flg]" />代表
                </td>
		   </tr>
		   <tr>
                 <th>都道府県</th>
                 <td colspan="3">
                  <select name="data[CustomerMst][brd_pref]">
                    <option value="" selected></option>
			     	<option value="北海道">北海道</option><option value="青森県">青森県</option>
					<option value="岩手県">岩手県</option><option value="宮城県">宮城県</option>
					<option value="秋田県">秋田県</option><option value="山形県">山形県</option>
					<option value="福島県">福島県</option><option value="茨城県">茨城県</option>
					<option value="栃木県">栃木県</option><option value="群馬県">群馬県</option>
					<option value="埼玉県">埼玉県</option><option value="千葉県">千葉県</option>
					<option value="東京都">東京都</option><option value="神奈川県">神奈川県</option>
					<option value="新潟県">新潟県</option><option value="富山県">富山県</option>
					<option value="石川県">石川県</option><option value="福井県">福井県</option>
					<option value="山梨県">山梨県</option><option value="長野県">長野県</option>
					<option value="岐阜県">岐阜県</option><option value="静岡県">静岡県</option>
					<option value="愛知県">愛知県</option><option value="三重県">三重県</option>
					<option value="滋賀県">滋賀県</option><option value="京都府">京都府</option>
					<option value="大阪府">大阪府</option><option value="兵庫県">兵庫県</option>
					<option value="奈良県">奈良県</option><option value="和歌山県">和歌山県</option>
					<option value="鳥取県">鳥取県</option><option value="島根県">島根県</option>
					<option value="岡山県">岡山県</option><option value="広島県">広島県</option>
					<option value="山口県">山口県</option><option value="徳島県">徳島県</option>
					<option value="香川県">香川県</option><option value="愛媛県">愛媛県</option>
					<option value="高知県">高知県</option><option value="福岡県">福岡県</option>
					<option value="佐賀県">佐賀県</option><option value="長崎県">長崎県</option>
					<option value="熊本県">熊本県</option><option value="大分県">大分県</option>
					<option value="宮崎県">宮崎県</option><option value="鹿児島県">鹿児島県</option>
					<option value="沖縄県">沖縄県</option>
				 </select>
		    	</td>
           </tr>
           <tr>
                 <th>市町村区</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_city]" id="brd_city_kj" class="validate[optional,maxSize[20]] inputname" value="" />
                 </td>
           </tr>
           <tr>
                 <th>住所番地</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_street]" id="brd_street_kj" class="validate[optional,maxSize[60]] inputtitle" value="" />
                 </td>
           </tr>
		   <tr>
                 <th>アパート・マンション</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_apart]" id="brd_part_kj" class="validate[optional,maxSize[20]] inputtitle" value="" />
			     </td>
           </tr>
           <tr>
                 <th>住所(ローマ字)</th><td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_address_rm]"  id="brd_address_rm" class="validate[optional,maxSize[120],custom[onlyLetterNumber]] inputtitle" value="" /></td>
           </tr>
		   <tr>
                 <th>電話番号</th>
                 <td colspan="3"><input type="text" id="brd_phone" name="data[CustomerMst][brd_phone_no]" class="validate[optional,custom[phone]] inputphone" value="" />
                 <input type="radio" value="1" name="data[CustomerMst][prm_phone_no_flg]" checked />代表</td>
            </tr>
		    <tr>
                 <th>携帯電話番号</th>
                 <td colspan="3"><input type="text" id="brd_cell" name="data[CustomerMst][brd_cell_no]"  class="validate[optional,custom[phone]] inputphone" value="" /></td>
            </tr>
  		    <tr>
                 <th>E-MAILアドレス</th>
                 <td colspan="3"><input type="text" id="brd_email" name="data[CustomerMst][brd_email]"  class="validate[optional,custom[email]] inputmailaddress" value="" />
                 <input type="radio" value="1" name="data[CustomerMst][prm_email_flg]" checked />代表</td>
            </tr>
		    <tr>
                 <th>携帯メールアドレス</th>
                 <td colspan="3"><input type="text" id="brd_phonemail" name="data[CustomerMst][brd_phone_mail]"  class="validate[optional,custom[email]] inputmailaddress" value="" /></td>
            </tr>
		    <tr>
                 <td colspan="3">&nbsp;</td>
             </tr>
	</table>
</td>
</tr>
</table>
    <div class="submit">
		<input type="submit" class="inputbutton" value="追加" />
        <input type="hidden" name="customer_type" value="0" />
	</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
