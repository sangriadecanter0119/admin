<script type='text/javascript'>
$(function(){
	     
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
            
    //フォーム送信前操作
	$("#formID").submit(function(){	
		
		   $(this).simpleLoading('show');
		    
		   var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");

		   $.post(<?php echo "'".$html->url('editEnv')."'" ?>,formData , function(result) {
			
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

    <form id="formID" class="content" method="post" action="" >
        <input type="hidden" name="data[EnvMst][id]"  value="<?php echo $data['EnvMst']['id'] ?>" />
		<table class="form" cellspacing="0">		 
	  	  <tr>
	  	     <th>ハワイ州税率(%)</th>
             <td><input type="text" name="data[EnvMst][hawaii_tax_rate]" id="tax_rate" class="validate[required,custom[number],max[99],maxSize[7]] inputnumeirc" 
                        value="<?php echo $data['EnvMst']['hawaii_tax_rate'] * 100 ?>" /></td>
          </tr>
          <tr>
	  	     <th>サービス税名</th>
	  	     <td><input type="text" name="data[EnvMst][service_rate_nm]" id="service_rate_nm" class="validate[required,maxSize[40]] inputvalue" 
                        value="<?php echo $data['EnvMst']['service_rate_nm'] ?>" /></td>
	  	  </tr>
          <tr>
	  	     <th>サービス税率(%)</th>
             <td><select id="service_rate" class="validate[required]"  name="data[EnvMst][service_rate]">
                 <?php 
                    for($i=0;$i <=100;$i++) 
                    {
                     if($data['EnvMst']['service_rate'] * 100 == $i)
                     {
                       echo "<option value='".($i/100)."' selected='selected'>".$i."</option>";	
                     }
                     else 
                     {
                       echo "<option value='".($i/100)."'>".$i."</option>";	
                     }                     
                    }                 
                 ?>                
                 </select>
               </td>
          </tr>
          <tr>
	  	     <th>割引率名</th>
	  	     <td><input type="text" name="data[EnvMst][discount_rate_nm]" id="discount_rate_nm" class="validate[required,maxSize[40]] inputvalue" 
                        value="<?php echo $data['EnvMst']['discount_rate_nm'] ?>" /></td>
	  	  </tr>
          <tr>
	  	     <th>割引率(%)</th>
	  	      <td><select id="discount_rate" class="validate[required]"  name="data[EnvMst][discount_rate]">
                 <?php 
                    for($i=0;$i <=100;$i++) 
                    {
                     if($data['EnvMst']['discount_rate'] * 100 == $i)
                     {
                       echo "<option value='".($i/100)."' selected='selected'>".$i."</option>";	
                     }
                     else 
                     {
                       echo "<option value='".($i/100)."'>".$i."</option>";	
                     }                     
                    }                 
                 ?>                
                 </select>
             </td>
          </tr>
          <tr>
	  	     <th>割引額名</th>
	  	     <td><input type="text" name="data[EnvMst][discount_nm]" id="discount_nm" class="validate[required,maxSize[40]] inputvalue" 
                        value="<?php echo $data['EnvMst']['discount_nm'] ?>" /></td>
	  	  </tr>
          <tr>
	  	     <th>為替レート</th>
             <td><input type="text" name="data[EnvMst][exchange_rate]" id="exchange_rate" class="validate[required,custom[number],max[999],maxSize[6]] inputnumeirc" 
                        value="<?php echo $data['EnvMst']['exchange_rate'] ?>" /></td>
          </tr>	
          <tr>
	  	     <th>送金為替レート</th>
             <td><input type="text" name="data[EnvMst][remittance_exchange_rate]" id="remittance_exchange_rate" class="validate[required,custom[number],max[999],maxSize[6]] inputnumeirc" 
                        value="<?php echo $data['EnvMst']['remittance_exchange_rate'] ?>" /></td>
          </tr>	
          <tr>
	  	     <th>割引為替レート</th>
             <td><input type="text" name="data[EnvMst][discount_exchange_rate]" id="discount_exchange_rate" class="validate[required,custom[number],max[999],maxSize[6]] inputnumeirc" 
                        value="<?php echo $data['EnvMst']['discount_exchange_rate'] ?>" /></td>
          </tr>	
          <tr>
             <th>割引VDシェア(%)</th>
             <td><input type="text" name="data[EnvMst][discount_aw_share]" id="discount_aw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[discount_rw_share]] inputnumeric" 
                        value="<?php echo $data['EnvMst']['discount_aw_share'] *100 ?>" /></td>
          </tr>
          <tr>
             <th>割引HIシェア(%)</th>
             <td><input type="text" name="data[EnvMst][discount_rw_share]" id="discount_rw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[discount_aw_share]] inputnumeric" 
                        value="<?php echo $data['EnvMst']['discount_rw_share'] *100 ?>" /></td>
          </tr>    
          <tr>
	  	     <th>MAILホスト</th>
             <td><input type="text" name="data[EnvMst][mail_host]" id="mail_host" class="validate[required,maxSize[50]] inputvalue" 
                        value="<?php echo $data['EnvMst']['mail_host'] ?>" /></td>
          </tr>	
          <tr>
	  	     <th>MAILポート</th>
             <td><input type="text" name="data[EnvMst][mail_port]" id="mail_port" class="validate[required,custom[number],max[99999],maxSize[5]] inputnumeirc" 
                        value="<?php echo $data['EnvMst']['mail_port'] ?>" /></td>
          </tr>	
          <tr>
	  	     <th>MAILプロトコル</th>
             <td><input type="text" name="data[EnvMst][mail_protocol]" id="mail_protocol" class="validate[required,maxSize[20]] inputvalue" 
                        value="<?php echo $data['EnvMst']['mail_protocol'] ?>" /></td>
          </tr>	  
	    </table>        
    
	<div class="submit">
	    <input type="submit" class="inputbutton" value="更新" />     	
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
