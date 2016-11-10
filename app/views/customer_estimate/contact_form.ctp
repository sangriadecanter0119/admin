<script type='text/javascript'>
$(function(){
    $("input:submit").button();  

    /* 問い合わせダイアログ */
	 $("#contact_dialog").dialog({
	             buttons: [{
	                 text: "OK",
	                 id:"ConfirmAddressListBotton",
	                 click: function () {        

	            	    //if( $("#formIDD").validationEngine('validate')==false){ return false; }
	            	    /* 入力チェック */
	                    if($("#receiver_mail").val() == ""){ $("#message").text("*宛先メールアドレスは必須です。"); return false; }
	                    if($("#title").val() == ""){ $("#message").text("*件名は必須です。"); return false; }
	                    
	                    $(this).simpleLoading('show');
	              
	          	        var formData = $("#formIDD").serialize();
	          	        $.post(<?php echo "'".$html->url('contactForm')."'" ?>,formData , function(result) {
	          		
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
	          		          $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);	 
	          		          //問い合わせ登録成功時のOK表示
	          		    	  $(".list tr:nth-child("+(parseInt(<?php echo $current_line_no ?>) + 1)+") td:nth-child(2) img").attr("style","display:inline");		
	          		       }else{
	          		          $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);	  
	          		       }    
	          	           $("#partial_result_message span").text(obj.message);    
	          	           $("#partial_error_reason").text(obj.reason);     
	                       $("#partial_result_dialog").dialog('open');          
	                    });	
	                 }
	             },
	             {
	                 text: "CANCEL",
	                 click: function () {                     
	                     $("#contact_dialog").dialog('close');
	                 }
	             }],
	             beforeClose:function(){
	                $("#contact_dialog").remove();
	             },
	             draggable: false,
	             autoOpen: true,
	             resizable: false,
	             zIndex: 2000,
	             width:810,
	             height:710,
	             position:[($(window).width() / 2) -  (810 / 2) ,($(window).height() / 2) -  (710 / 2)],
	             modal: true,
	             title: "問い合わせフォーム"
	 });
	 
     /* 処理結果用ダイアログ */
    $("#partial_result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {                     
                     $("#partial_result_dialog").dialog('close');                 
                 }
             }],            
              beforeClose: function (event, ui) {
                  $("#partial_result_message span").text("");    
		          $("#partial_error_reason").text("");    
             },
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             modal: true,
             title: "処理結果"
    });

    /* アドレス帳の表示 */
    $(".address_list").click(function(){

    	$(this).simpleLoading('show');
    	$("#" + $(this).attr("id") +"_mail").addClass("selected");
    	$.post("<?php echo $html->url('addressListForm') ?>" , function(data) {
    		$(this).simpleLoading('hide');
            $("#address_list_dialog_wrapper").html(data);
        });
    });
});

</script>

<div id="contact_dialog">
    <form id="formIDD" class="content" method="post" name="contact" action="">
        <input type="hidden" id="sender_mail" name="data[ContactTrn][sender_email]"    value="<?php echo $user['User']['email']; ?>" />
        <input type="hidden" id="sender_mail" name="data[ContactTrn][sender_nm]"       value="<?php echo $user['User']['username']; ?>" />
        <input type="hidden" id="sender_mail" name="data[ContactTrn][estimate_dtl_id]"  value="<?php echo $estimate_dtl_data['EstimateDtlTrnView']['id']; ?>" />
		<table class="form" cellspacing="0">		
		<tr>
            <th><a href='#'  id="receiver" class='address_list' style="text-decoration:underline;color:#57d">宛先</a><span class="necessary">(必須)</span></th>
            <td><input type="text" id="receiver_mail" name="data[ContactReceiverTrn][receiver_mail]"  class="validate[required] inputlongmailaddress"  value="" /></td>     
        </tr>
        <tr>
            <th><a href='#'  id="cc"       class='address_list' style="text-decoration:underline;color:#57d">CC</a></th>
            <td><input type="text" id="cc_mail" name="data[ContactReceiverTrn][cc_mail]"  class="inputlongmailaddress" /></td>    
        </tr>
        <tr>
            <th><a href='#'  id="bcc"      class='address_list' style="text-decoration:underline;color:#57d">BCC</a></th>
            <td><input type="text" id="bcc_mail" name="data[ContactReceiverTrn][bcc_mail]" class="inputlongmailaddress" /></td>         
        </tr>		
        <tr>
            <th>件名<span class="necessary">(必須)</span></th>
            <td><input type="text" id="title" name="data[ContactTrn][title]" class="validate[required,maxSize[40]] inputlongtitle" value="" /></td>                 
        </tr>
		<tr>
            <th>担当</th> 
	   	    <td><?php echo $user['User']['username'] ?>
	   	        <input type="hidden" name="data[ContactTrn][sender_nm]" value="<?php echo $user['User']['username'] ?>" />
	   	    </td>                               
        </tr>    
        <tr>
            <th>手配区分</th> 
	   	    <td><select name="data[ContactTrn][contact_kbn]">
                 	 <option value="0" selected="selected">間接手配</option>
                     <option value="1">直接手配</option> 
 		          </select>
		    </td>                                   
        </tr>       
        <tr>
            <th>予約項目</th>
            <td>
              <?php 
                   echo $estimate_dtl_data['EstimateDtlTrnView']['goods_ctg_nm'];
                   echo "<input type='hidden' name='data[ContactTrn][goods_ctg_id]' value='{$estimate_dtl_data['EstimateDtlTrnView']['goods_ctg_id']}'>"; 
              ?>
		     </td>             
        </tr>
		<tr>
             <th>ベンダー</th>
             <td><select  name='data[ContactTrn][vendor_id]'>
            <?php 
                   for($i=0;$i < count($vendor_list);$i++)
                   {
                      $atr = $vendor_list[$i];
                      if($atr['VendorMst']['id'] == $estimate_dtl_data['EstimateDtlTrnView']['vendor_id']){
                      	 echo "<option value='{$atr['VendorMst']['id']}' selected>{$atr['VendorMst']['vendor_nm']}</option>";     
                      }else{
                         echo "<option value='{$atr['VendorMst']['id']}' >{$atr['VendorMst']['vendor_nm']}</option>";           
                      }
                    }
             ?>
                  </select>
		     </td>                   
        </tr>
		<tr>
              <th>内容区分</th>
              <td><select name="data[ContactTrn][content_kbn]">
                 	 <option value="0" selected="selected">質問事項</option>
                     <option value="1">重要事項</option> 
 		          </select>
		      </td>                     
        </tr>
    	<tr>
              <th>内容</th>
              <td><textarea id="content" name="data[ContactTrn][content]" class="inputlongcomment" rows="10"></textarea></td>                     
        </tr>
		<tr>
              <th>依頼事項</th>
              <td><select name="data[ContactTrn][question_kbn]">
                	  <option value="0">空き</option>
                      <option value="1" selected="selected">予約</option>
			          <option value="2">CXL</option>
                      <option value="3">MAIL</option> 
		           </select>
		       </td>                                    
        </tr>
		<tr>
              <th>備考</th>
              <td><textarea id="note" name="data[ContactTrn][note]" class="inputlongcomment" rows="5"></textarea></td>           
        </tr>  
	    </table>
</form>
<div id="address_list_dialog_wrapper"></div>
<div id="partial_result_dialog"  style="display:none"><p id="partial_result_message"><img src="#" alt="" /><span></span></p><p id="partial_error_reason"></p></div>
<div id="partial_critical_error"></div>
</div>