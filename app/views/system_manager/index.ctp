<script type='text/javascript'>
$(function(){
/*
    $("#status_date_update").click(function(){
    	StartSubmit(1)
    });
    $("#customer_copy").click(function(){
    	StartSubmit(2)
    });
    $("#update_prepaied").click(function(){
    	StartSubmit(3)
    });
    $("#delete_prepaied").click(function(){
    	StartSubmit(4)
    });
    $("#update_fund").click(function(){
    	StartSubmit(5)
    });
    $("#clear_invoice_dt").click(function(){
    	StartSubmit(6)
    });
 */
    $("#non_adopt_estimate").click(function(){
    	StartSubmit(7)
    });

     $("#non_paied").click(function(){
 	    StartSubmit(8)
     });

     $("#move_estimate").click(function(){
	    StartSubmit(9)
     });

     $("#update_contract_dt").click(function(){
 	    StartSubmit(10)
      });

    /*  ファイル取り込みフォームの表示開始
    -------------------------------------------------*/
    $("#file_upload_link").click(function(){

             $(this).simpleLoading('show');
             $.post(<?php echo "'".$html->url('fileUploadForm')."'" ?>,function(html){
                $('body').append(html);
                $(this).simpleLoading('hide');
             });
          return false;
       });

});

function StartSubmit(type){

	$(this).simpleLoading('show');

	switch(type){
	   case 1:$url = <?php echo "'".$html->url('updateStatusDate')."'" ?>;
		      break;
	   case 2:$url = <?php echo "'".$html->url('copyCustomerBasicInfo')."'" ?>;
		      break;
	   case 3:$url = <?php echo "'".$html->url('updatePrepaied')."'" ?>;
		      break;
	   case 4:$url = <?php echo "'".$html->url('deletePrepaied')."'" ?>;
		      break;
	   case 5:$url = <?php echo "'".$html->url('updateFund')."'" ?>;
	      break;
	   case 6:$url = <?php echo "'".$html->url('clearInvoiceDate')."'" ?>;
	      break;
	   case 7:$url = <?php echo "'".$html->url('getNonAdoptEstimate')."'" ?>;
	      break;
	   case 8:$url = <?php echo "'".$html->url('getNonPrepaied')."'" ?>;
	      break;
	   case 9:$url = <?php echo "'".$html->url('moveToEstimateStatus')."'" ?>;
	      break;
	   case 10:$url = <?php echo "'".$html->url('updateContractDate')."'" ?>;
	      break;
	}

    $.post($url,function(result) {

        $(this).simpleLoading('hide');

	    var obj = null;
        try {
           obj = $.parseJSON(result);
         } catch(e) {
	       $("#critical_error").text(result);
	       return;
         }
	     if(obj.result == true){
	        // alert("成功"+obj.message);
	         $("#critical_error").text(obj.message);
	     }else{
	    	 alert("失敗"+obj.message+":"+obj.reason);
	     }
   });
}
</script>

<ul class="operate">
<!--  <li><a href="<?php echo $html->url('index').'/type=update'?>" >データ移行</a></li>
<li><a id="status_date_update" href="#" >各ステータス日付更新</a></li>
<li><a id="customer_copy" href="#" >挙式情報コピー</a></li>
<li><a href="#" id="update_prepaied">内金1円更新</a></li>
<li><a href="#" id="delete_prepaied">内金1円削除</a></li>
<li><a href="#" id="update_fund">内金と入金日を資金管理テーブルに転記</a></li>
<li><a href="#" id='file_upload_link'>挙式ファイル取り込み(CSV:日付2桁化)</a></li>
<li><a href="#" id='clear_invoice_dt'>請求書日付を消去・成約に戻す</a></li>
<li><a href="#" id='non_adopt_estimate'>未採用見積一覧</a>
<li><a href="#" id='non_paied'>お内金未入金一覧</a>
<li><a href="#" id='move_estimate'>見積提出済みに移行</a>
<li><a href="#" id='update_contract_dt'>成約日を入金日で更新</a>-->
</ul>
<?php
  if(empty($result)==false){
  	print_r($result);
  }
?>

<ul class="itemlink" style="padding-top:20px;">
	    <li><a href="<?php echo $html->url('userMaster')    ?>"><?php echo $html->image('arrownext.gif') ?>ユーザー管理</a></li>
        <li><a href="<?php echo $html->url('/GoodsCategoryMaster') ?>"><?php echo $html->image('arrownext.gif') ?>商品カテゴリ管理</a></li>
        <li><a href="<?php echo $html->url('/GoodsKbnMaster') ?>"><?php echo $html->image('arrownext.gif') ?>商品区分管理</a></li>
        <li><a href="<?php echo $html->url('/GoodsMaster') ?>"><?php echo $html->image('arrownext.gif') ?>商品管理</a></li>
	    <li><a href="<?php echo $html->url('/vendorCategoryMaster')  ?>"><?php echo $html->image('arrownext.gif') ?>ベンダー区分管理</a></li>
	    <li><a href="<?php echo $html->url('/vendorMaster')  ?>"><?php echo $html->image('arrownext.gif') ?>ベンダー管理</a></li>
	    <li><a href="<?php echo $html->url('/creditExceptionMaster')    ?>"><?php echo $html->image('arrownext.gif') ?>入金例外管理</a></li>
	    <li><a href="<?php echo $html->url('/ReportMaster')  ?>"><?php echo $html->image('arrownext.gif') ?>帳票管理</a></li>
	    <li><a href="<?php echo $html->url('/envMaster')    ?>"><?php echo $html->image('arrownext.gif') ?>環境設定管理</a></li>
</ul>

<div id="critical_error"></div>

