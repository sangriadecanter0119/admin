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
                           location.href = <?php echo "'".$html->url('/systemManager/userMaster')."'" ?>;
                        }
                     }
                     /* 自分自身を更新した場合はログアウトする */
                     if($("#userid").val() == <?php echo $user['User']['id'] ?>){
                    	 location.href = <?php echo "'".$html->url('.')."'" ?>;
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

	   $.post(<?php echo "'".$html->url('editUser')."'" ?>,formData , function(result) {

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
     <li><a href="<?php echo $html->url('/systemManager/userMaster') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content" method="post" name="User" action="" >
        <input type="hidden"  id="userid" name="data[User][id]"  value="<?php echo $data['UserView']['id'] ?>" />
		<table id="userform" class="form" cellspacing="0">
	  	  <tr>
             <th>名前<span class="necessary">(必須)</span></th>
             <td><input type="text" id="user_name" name="data[User][username]" class="validate[required,maxSize[30]]  inputname" value="<?php echo $data['UserView']['username'] ?>" /></td>
          </tr>
           <tr>
             <th>表示名</th>
             <td><input type="text" id="display_nm" name="data[User][display_nm]" class="inputname" value="<?php echo $data['UserView']['display_nm'] ?>" /></td>
          </tr>
		  <tr>
             <th>新規ログイン パスワード</th>
             <td><input type="password" id="user_password" name="data[User][new_password]" class="validate[optional,maxSize[15]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>ログイン パスワード確認</th>
             <td><input type="password" id="confirm_password" name="data[User][new_password]" class="validate[optional,equals[user_password]]] inputvalue" value="" /></td>
          </tr>
           <tr>
             <th>ユーザー区分</th>
             <td>
                <?php
                   /* ユーザー区分変更は管理者のみ */
                   if(UC_ADMIN == $user['User']['user_kbn_id']){
                   	   echo "<select name='data[User][user_kbn_id]'>";
                       for($i=0;$i < count($user_kbn_list);$i++)
                       {
                   	      $atr = $user_kbn_list[$i]['UserKbnMst'];
                   	      if($atr['id'] == $data['UserView']['user_kbn_id']){
                   	     	  echo "<option value='{$atr['id']}' selected>{$atr['user_kbn_nm']}</option>";
                   	      }else{
                   	   	      echo "<option value='{$atr['id']}'>{$atr['user_kbn_nm']}</option>";
                   	      }
                       }
                       echo "</select>";
                   }else{
                   	   echo $data['UserView']['user_kbn_nm'];

                   }
                ?>
             </td>
          </tr>
           <tr>
             <th>E-MAIL<span class="necessary">(必須)</span></th>
             <td><input type="text" id="email" name="data[User][email]" class="validate[required,custom[email]] inputmailaddress" value="<?php echo $data['UserView']['email'] ?>" /></td>
          </tr>
          <tr>
             <th>E-MAIL ユーザー名<span class="necessary">(必須)</span></th>
             <td><input type="text" id="email_user_name" name="data[User][email_username]" class="validate[required,maxSize[30]]  inputmailaddress" value="<?php echo $data['UserView']['email_username'] ?>" /></td>
          </tr>
          <tr>
             <th>新規EMAIL パスワード</th>
             <td><input type="password" id="email_password" name="data[User][new_email_password]" class="validate[optional,maxSize[15]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>EMAIL パスワード確認</th>
             <td><input type="password" id="confirm_email_password" name="data[User][new_email_password]" class="validate[optional,equals[email_password]]] inputvalue" value="" /></td>
          </tr>
          <tr>
             <th>備考</th>
             <td><textarea  id="note" name="data[User][note]" class="inputcomment" ><?php echo $data['UserView']['note'] ?></textarea>
          </tr>
	    </table>

	<div class="submit">
	    <input type="submit" id="update"  class="inputbutton"  name="update" value="更新" />
	    <input type='submit' id='delete'  class='inputbutton'  name="delete" value='削除' />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
