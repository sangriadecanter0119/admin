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

	   $.post(<?php echo "'".$html->url('editContact')."'" ?>,formData , function(result) {
		
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

    <form id="formID" class="content" method="post" name="contact"" action="" >

		<table class="form" cellspacing="0">		 
	  	  <tr>
            <th>件名</th>
            <td><?php echo $data[0]['ContactTrnView']['title'] ?></td>                 
        </tr>
		<tr>
            <th>宛先</th>
            <td>
               <?php
                 $receiver_nm = null;
                 for($i=0;$i < count($data);$i++)
                 {
                 	$atr = $data[$i]['ContactTrnView'];
                 	if($atr['receiver_kbn'] == RECEIVER){
                 	  if($atr['receiver_nm'] == null){
                 		 $receiver_nm .= "    ".$atr['receiver_email'].";";
                 	  }else{
                 		 $receiver_nm .= "    ".$atr['receiver_nm'].";";
                 	  }    
                 	}             	
                 }   
                 echo substr($receiver_nm,0,strlen($receiver_nm)-1);          
               ?>            
            </td>    
        </tr>
        <tr>
            <th>CC</th>
            <td>
               <?php
                 $receiver_nm = null;
                 for($i=0;$i < count($data);$i++)
                 {
                 	$atr = $data[$i]['ContactTrnView'];
                 	if($atr['receiver_kbn'] == CC){
                 	  if($atr['receiver_nm'] == null){
                 		 $receiver_nm .= $atr['receiver_email'].";";
                 	  }else{
                 		 $receiver_nm .= $atr['receiver_nm'].";";
                 	  }    
                 	}             	
                 }                    
                 //最後のカンマを取り除く
                 echo substr($receiver_nm,0,strlen($receiver_nm)-1);            
               ?>            
            </td>       
        </tr>
        <tr>
            <th>BCC</th>
            <td>
               <?php
                 $receiver_nm = null;
                 for($i=0;$i < count($data);$i++)
                 {
                 	$atr = $data[$i]['ContactTrnView'];
                 	if($atr['receiver_kbn'] == BCC){
                 	  if($atr['receiver_nm'] == null){
                 		 $receiver_nm .= $atr['receiver_email'].";";
                 	  }else{
                 		 $receiver_nm .= $atr['receiver_nm'].";";
                 	  }    
                 	}             	
                 }   
                echo substr($receiver_nm,0,strlen($receiver_nm)-1);        
               ?>            
            </td>       
        </tr>
		<tr>
            <th>送信元</th>
            <td><?php echo  $data[0]['ContactTrnView']['sender_email'] ?></td>
        </tr>
		<tr>
            <th>担当</th> 
	   	    <td><?php echo  $data[0]['ContactTrnView']['sender_nm'] ?></td>                               
        </tr>        
        <tr>
            <th>予約項目</th>
            <td><?php echo  $data[0]['ContactTrnView']['goods_ctg_nm'] ?></td> 
        </tr>
		<tr>
             <th>ベンダー</th>
             <td><?php echo  $data[0]['ContactTrnView']['vendor_nm'] ?></td> 
        </tr>
		<tr>
              <th>内容区分</th>
              <td><select id="content_kbn" name="data[ContactTrn][content_kbn]">
                  <?php 
                     if($data[0]['ContactTrnView']['content_kbn'] == 0)
                     {
                     	echo "<option value='0' selected='selected'>質問事項</option>";
                     	echo "<option value='1'>重要事項</option>";
                     }
                     else if($data[0]['ContactTrnView']['content_kbn'] == 1)
                     {
                     	echo "<option value='0'>質問事項</option>";
                     	echo "<option value='1' selected='selected'>重要事項</option>";
                     }
                  ?>                 	
 		          </select>
		      </td>                     
        </tr>
    	<tr>
              <th>内容</th>
              <td><?php echo $data[0]['ContactTrnView']['content'] ?></td>                     
        </tr>
		<tr>
              <th>依頼事項</th>
              <td><?php echo $data[0]['ContactTrnView']['question_kbn_nm'] ?></td>                                    
        </tr>
		<tr>
              <th>備考</th>
              <td><textarea name="data[ContactTrn][note]" class="inputcomment" rows="5"><?php echo $data[0]['ContactTrnView']['note'] ?></textarea></td>           
        </tr>  
	    </table>
	    
	<div class="submit">
		<input type="hidden" name="data[ContactTrn][id]" value="<?php echo $data[0]['ContactTrnView']['id'] ?>" />
	    <input type="submit" class="inputbutton" name="update" value="更新" />     
		<input type="submit" class="inputbutton" name="delete" value="削除" />    
	</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
