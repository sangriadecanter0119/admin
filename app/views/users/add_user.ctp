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

	   $.post(<?php echo "'".$html->url('addUser')."'" ?>,formData , function(result) {

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
     <li><a href="<?php echo $html->url('/systemManager/userMaster') ?>">一覧に戻る</a></li>
    </ul>
        <?php echo "<div><span style='color:red'>".$form->error('User.username')."</span></div>";  ?>
    <form id="formID" class="content" method="post" name="User" action="" >

		<table id="userform" class="form" cellspacing="0">
	  	  <tr>
             <th>名前<span class="necessary">(必須)</span></th>
             <td><input type="text" id="user_name" name="data[User][username]" class="validate[required,maxSize[30]]  inputname" value="" /></td>
          </tr>
          <tr>
             <th>表示名</th>
             <td><input type="text" id="display_nm" name="data[User][display_nm]" class="inputname" value="" /></td>
          </tr>
		  <tr>
             <th>ログイン パスワード<span class="necessary">(必須)</span></th>
             <td><input type="password" id="user_password" name="data[User][password]" class="validate[required,maxSize[15]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>ログイン パスワード確認<span class="necessary">(必須)</span></th>
             <td><input type="password" id="confirm_password" name="data[User][password]" class="validate[required,equals[user_password]]] inputvalue" value="" /></td>
          </tr>
           <tr>
             <th>ユーザー区分</th>
             <td>
                <select name='data[User][user_kbn_id]'>
                <?php
                   for($i=0;$i < count($user_kbn_list);$i++)
                   {
                   	   $atr = $user_kbn_list[$i]['UserKbnMst'];
                   	   echo "<option value='{$atr['id']}'>{$atr['user_kbn_nm']}</option>";
                   }
                ?>
                </select>
             </td>
          </tr>
           <tr>
             <th>E-MAIL<span class="necessary">(必須)</span></th>
             <td><input type="text" id="email" name="data[User][email]" class="validate[required,custom[email]] inputmailaddress" value="" /></td>
          </tr>
          <tr>
             <th>E-MAIL ユーザー名<span class="necessary">(必須)</span></th>
             <td><input type="text" id="email_user_name" name="data[User][email_username]" class="validate[required,maxSize[30]]  inputmailaddress" value="" /></td>
          </tr>
          <tr>
             <th>E-MAIL パスワード<span class="necessary">(必須)</span></th>
             <td><input type="password" id="email_password" name="data[User][email_password]" class="validate[required,maxSize[15]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>E-MAIL パスワード確認<span class="necessary">(必須)</span></th>
             <td><input type="password" id="confirm_email_password" name="data[User][email_password]" class="validate[required,equals[email_password]]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>備考</th>
             <td><textarea  id="note" name="data[User][note]" class="inputcomment" ></textarea>
          </tr>
	    </table>

	<input type="hidden" name="data[User][reg_id]" value="<?php echo $user['User']['username']; ?>" >
    <input type="hidden" name="data[User][reg_dt]" value="<?php echo date('Y-m-d H:i:s'); ?>" >

	<div class="submit">
	   	<input type="submit" class="inputbutton" value="追加" />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
