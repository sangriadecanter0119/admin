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

    /* 更新処理開始 */
    $("#ReportForm").submit(function(){
    
       $(this).simpleLoading('show');
    
	   var formData = $("#ReportForm").serialize();

	   $.post(<?php echo "'".$html->url('editReport')."'" ?>,formData , function(result) {
		
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
<style type='text/css'>
	
	div#report_name{clear: both;
	                margin-bottom:5px;
	           }
	div#report { border:1px solid ; 
	             width:550px;
	             height:100%;
	             padding:0px 0px 15px 0px;
	             clear: both;
	             overflow:auto;
	           }
	div#report_title{ text-align:center;
	                  margin-top: 10px;
	                }
	div#report_date{ text-align:right; 
	                 margin-right:10px;
	               }
	div#report_note{ text-align:center; }
	div#report_footer{ text-align:center; }
	
	table#report_table,table#report_table td,table#report_table th{
	            width: 480px;
	            border: 1px gray solid;
	            margin: 10px 10px 0px 10px;
                }
    table#report_table{  margin: 10px 0px 5px 30px;  }
    .price,.title,.num{text-align:right;}
	
</style>

    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>  
    </ul>

<div id="report_name">【見積帳票PDFサンプル】</div>       
<form id="ReportForm" method="post" action="">
<div id="report" >  
     <div id="report_title"><?php echo $html->image('title.bmp') ?></div>
     <div id="report_date"><?php echo date('Y/m/d'); ?></div>  
     <table id="report_table" class="">
		<tr>
		  <th>項目</th><th>商品</th><th>個数</th><th>料金</th>
		</tr>				
		<tr>
	      <td>XXXXXX</td>
	      <td>XXXXXXXXX</td>
          <td class="num">X</td>
          <td class="price">&yen;XXX,XXX</td>
		</tr>
		<tr>
	      <td>XXXXXX</td>
	      <td>XXXXXXXXX</td>
          <td class="num">X</td>
          <td class="price">&yen;XXX,XXX</td>
		</tr>
		<tr>
	      <td>XXXXXX</td>
	      <td>XXXXXXXXX</td>
          <td class="num">X</td>
          <td class="price">&yen;XXX,XXX</td>
		</tr>	
	    <tr>
	      <td>&nbsp;</td>
	      <td class="num">&nbsp;</td>
          <td class="title">ハワイ州税</td>
          <td class="price">&yen;XXX,XXX</td>
        </tr>  
        <tr>
	      <td>&nbsp;</td>
	      <td >&nbsp;</td>
          <td class="title">アレンジメント料</td>
          <td class="price">&yen;XXX,XXX</td>
        </tr>  
        <tr>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
          <td class="title">SUBTOTAL</td>
          <td class="price">&yen;XXX,XXX</td>
        </tr>  
        <tr>
	      <td>&nbsp;</td>
	      <td>&nbsp;</td>
          <td class="title">TOTAL</td>
          <td class="price">&yen;XXX,XXX</td>
        </tr>  
    </table>    
     <div id="report_note"><textarea   name='data[ReportMst][note]'   rows="15" cols="100"><?php echo $data[0]['ReportMst']['note'] ?></textarea></div>
     <div id="report_footer"><textarea name='data[ReportMst][footer]' rows="9" cols="100"><?php echo $data[0]['ReportMst']['footer'] ?></textarea></div>
</div>    
<div class="submit">
        <input type="hidden" name='data[ReportMst][id]' value="<?php echo $data[0]['ReportMst']['id'] ?>" />    
	    <input type="submit" class="inputbutton" value="更新" />     	
</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
