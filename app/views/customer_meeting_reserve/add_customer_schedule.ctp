<script type='text/javascript'>
$(function(){
    $("input:submit").button();  
    //入力マスク
    $("#meeting_date").mask("9999/99/99");

    //日付入力補助のプラグイン 
    $( ".datepicker" ).datepicker({      
        dateFormat: 'yy/mm/dd',
        showOtherMonths: true,
        selectOtherMonths: true,       
        numberOfMonths:3,     
        beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' );}       
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

	   $.post(<?php echo "'".$html->url('addCustomerSchedule')."'" ?>,formData , function(result) {
		
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

   <form id="formID" class="content" method="post" name="customerSchedule" action="">

		<table class="form" cellspacing="0">		 
		   <tr>
                <th>担当者<span class="necessary">(必須)</span></th>
                <td>
                <select name='data[CustomerScheduleTrn][attend_id]'>
                  <?php
                    for($i=0;$i < count($user_list);$i++)
                    {
                     $atr = $user_list[$i]; 
                     echo "<option value='{$atr['User']['id']}' >{$atr['User']['username']}</option>";   
                    } 
                  ?>
                </select>
                </td>               
           </tr>
           <tr>
              <th>日付<span class="necessary">(必須)</span></th>
              <td><input type="text" id="meeting_date" name="data[CustomerScheduleTrn][start_dt]" class="validate[optional,custom[date]] inputdate datepicker" value="" /></td>	
           </tr>
           <tr>
                <th>予定時間<span class="necessary">(必須)</span></th>
                <td>
                <select name='data[Tmp][start_hour]'>
                  <?php
                    for($i=0;$i < 24;$i++)
                    {
                     echo "<option value='{$i}' >{$i}</option>";   
                    } 
                  ?>
                </select>時
                 <select name='data[Tmp][start_min]'>
                  <?php
                    for($i=0;$i < 60;$i++)
                    {
                     echo "<option value='{$i}' >{$i}</option>";   
                    } 
                  ?>
                </select>分&nbsp;～&nbsp;
                 <select name='data[Tmp][end_hour]'>
                  <?php
                    for($i=0;$i < 24;$i++)
                    {
                     echo "<option value='{$i}' >{$i}</option>";   
                    } 
                  ?>
                </select>時
                 <select name='data[Tmp][end_min]'>
                  <?php
                    for($i=0;$i < 60;$i++)
                    {
                     echo "<option value='{$i}' >{$i}</option>";   
                    } 
                  ?>
                </select>分
                </td>         
           </tr>
	   	   <tr>
                <th>タイトル<span class="necessary">(必須)</span></th>
                <td><input type="text" id="title" name="data[CustomerScheduleTrn][title]" class="validate[required,maxSize[20]] inputvalue" value="" /></td>		       
           </tr>
            <tr>
                <th>内容</th>
                <td><textarea name='data[CustomerScheduleTrn][note]' class='inputcomment' rows='5'></textarea></td>              
           </tr>
	</table>
    
	<div class="submit">
		<input type="submit" class="inputbutton" value="追加" />     
	</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
