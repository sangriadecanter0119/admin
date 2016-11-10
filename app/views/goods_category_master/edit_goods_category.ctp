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

	   $.post(<?php echo "'".$html->url('editGoodsCategory')."'" ?>,formData , function(result) {
		
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

    <form id="formID" class="content" method="post" name="GoodsCategory"" action="" >
        <div class="message warning_message"><img src="<?php echo $html->webroot("/images/warning.png") ?>" alt="" /><span>商品分類はファイナルシートに連動していますので、名称を変更する時は同義のものを選択して下さい。</span></div>
		<table class="form" cellspacing="0">		 
	  	  <tr>
	  	     <th>商品分類ID</th>
             <td><input type="hidden" name="data[GoodsCtgMst][id]" value="<?php echo $data['GoodsCtgMst']['id'] ?>" /><?php echo $data['GoodsCtgMst']['id'] ?></td>
          </tr>
          <tr>   
             <th>商品分類名<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsCtgMst][goods_ctg_nm]" id="goods_ctg" class="validate[required,maxSize[60]] inputvalue" value="<?php echo $data['GoodsCtgMst']['goods_ctg_nm'] ?>" /></td>
          </tr>		  
	    </table>     
     
	<div class="submit">
	    <input type="submit" class='inputbutton' name='update' value="更新" />  
	    <!-- 削除不可(見積作成時やファイナルシート作成時にIDを使用しているため変更されると不都合になる) -->
	    <!-- <input type='submit' class='inputbutton' name='delete' value='削除'  />  -->
    </div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
