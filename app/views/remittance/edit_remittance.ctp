<?php
  //明細行数
  $count = count($data);
  $edit_remittance_url = $html->url('editRemittance');
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");
  //支払区分
  $aboard_indirect_pay =  PC_INDIRECT_ABOARD_PAY;
  $aboard_direct_pay   =  PC_DIRECT_ABOARD_PAY;
  $aboard_credit_pay   =  PC_CREDIT_ABOARD_PAY;
  $domestic_direct_pay =  PC_DOMESTIC_DIRECT_PAY;
  $domestic_credit_pay =  PC_DOMESTIC_CREDIT_PAY;

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
    var table_counter=$count;
    var current_line_no;
    $("input:submit").button();

     //価格などの合計の計算
    function calculate()
    {
      //邦貨初期化
      $("#sub_total_amount_price").text(0);
      $("#sub_total_amount_price_with_tax").text(0);
      $("#sub_total_amount_price_with_arrange").text(0);
      $("#mid_total_amount_price").text(0);
      $("#total_amount_price_with_discount").text(0);
      $("#total_amount_price_with_discount_currency").text(0);
      $("#total_amount_price").text(0);

      $("#sub_total_cost").text(0);
      $("#mid_total_cost").text(0);
      $("#total_cost").text(0);

      $("#sub_total_net").text(0);
      $("#mid_total_net").text(0);
      $("#total_net").text(0);

      $("#sub_total_aw").text(0);
      $("#mid_total_aw").text(0);
      $("#total_aw").text(0);

      $("#sub_total_rw").text(0);
      $("#sub_total_rw_with_arrange").text(0);
      $("#mid_total_rw").text(0);
      $("#total_rw").text(0);

      //外貨初期化
      $("#sub_total_amount_foreign_price").text(0);
      $("#sub_total_foreign_amount_price_with_tax").text(0);
      $("#sub_total_foreign_amount_price_with_arrange").text(0);
      $("#mid_total_foreign_amount_price").text(0);
      $("#total_foreign_amount_price_with_discount").text(0);
      $("#total_foreign_amount_price_with_discount_currency").text(0);
      $("#total_foreign_amount_price").text(0);

      $("#sub_total_foreign_cost").text(0);
      $("#mid_total_foreign_cost").text(0);
      $("#total_foreign_cost").text(0);

      $("#sub_total_foreign_net").text(0);
      $("#mid_total_foreign_net").text(0);
      $("#total_foreign_net").text(0);

      $("#sub_total_foreign_aw").text(0);
      $("#mid_total_foreign_aw").text(0);
      $("#total_foreign_aw").text(0);

      $("#sub_total_foreign_rw").text(0);
      $("#sub_total_foreign_rw_with_arrange").text(0);
      $("#mid_total_foreign_rw").text(0);
      $("#total_foreign_rw").text(0);

      //国内払い用の商品代価合計
      var internal_total_price=0;
      //国内払い用の商品代価合計(外貨)
      var internal_total_foreign_price=0;
      /* 商品価格と原価の集計 */
      for(i=1;i <= table_counter;i++)
      {
        /* 邦貨計算 */
        //代価合計
        $("#sub_total_amount_price").text(Common.addYenComma(Common.removeComma($("#amount_price" + i).text()) +  Common.removeComma($("#sub_total_amount_price").text())));
        //原価合計
        $("#sub_total_cost").text(Common.addYenComma(Common.removeComma($("#cost" + i).text()) +  Common.removeComma($("#sub_total_cost").text())));
        //業者配分合計
        $("#sub_total_aw").text(Common.addYenComma(Common.removeComma($("#aw_share" + i).text()) + Common.removeComma($("#sub_total_aw").text())));
        //RW配分合計
        $("#sub_total_rw").text(Common.addYenComma(Common.removeComma($("#rw_share" + i).text()) + Common.removeComma($("#sub_total_rw").text())));

        /* 外貨計算 */
        //代価合計
        $("#sub_total_amount_foreign_price").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_amount_price" + i).text()) +  Common.removeDollarComma($("#sub_total_amount_foreign_price").text())));
        //原価合計
        $("#sub_total_foreign_cost").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_cost" + i).text()) +  Common.removeDollarComma($("#sub_total_foreign_cost").text())));
        //業者配分合計
        $("#sub_total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_aw_share" + i).text()) + Common.removeDollarComma($("#sub_total_foreign_aw").text())));
        //RW配分合計
        $("#sub_total_foreign_rw").text(Common.addDollarComma(Common.removeDollarComma($("#foreign_rw_share" + i).text()) + Common.removeDollarComma($("#sub_total_foreign_rw").text())));

        //国内払い用の商品
        if($("#payment_kbn" + i).val() == $domestic_direct_pay || $("#payment_kbn" + i).val() == $domestic_credit_pay ){
           internal_total_price         += Common.removeComma($("#amount_price" + i).text());
           internal_total_foreign_price += Common.removeComma($("#foreign_amount_price" + i).text());
        }
      }
        /* 邦貨合計計算 */
        //TAX(小計から国内支払商品の合計を除いてからTAXを求める)
        $("#sub_total_amount_price_with_tax").text(Common.addYenComma($("#hawaii_tax_rate").text() / 100 *  (Common.removeComma($("#sub_total_amount_price").text())-internal_total_price)));
        //手数料
        $("#sub_total_amount_price_with_arrange").text(Common.addYenComma($("#service_rate").text()/ 100 *  Common.removeComma($("#sub_total_amount_price").text())));
        //代価合計(TAX・手数料込)
        $("#mid_total_amount_price").text(Common.addYenComma(Common.removeComma($("#sub_total_amount_price").text()) +
                                                      Common.removeComma($("#sub_total_amount_price_with_tax").text()) +
                                                      Common.removeComma($("#sub_total_amount_price_with_arrange").text())));
        //割引料
        $("#total_amount_price_with_discount").text(Common.addYenComma(Common.removeComma($("#mid_total_amount_price").text()) *  ($("#discount_rate").text() / 100)));
        $("#total_amount_price_with_discount_currency").text(Common.addYenComma($("#discount").text()));
        //代価合計(TAX・手数料・割引料込)
        $("#total_amount_price").text(Common.addYenComma(Common.removeComma($("#mid_total_amount_price").text()) -  Common.removeComma($("#total_amount_price_with_discount").text()) - Common.removeComma($("#total_amount_price_with_discount_currency").text())));
        //原価小計
        $("#mid_total_cost").text(Common.addYenComma(Common.removeComma($("#sub_total_cost").text())));
        //原価総合計
        $("#total_cost").text(Common.addYenComma(Common.removeComma($("#sub_total_cost").text())));
        //利益（代価-原価)
        $("#sub_total_net").text(Common.addYenComma(Common.removeComma($("#sub_total_amount_price").text()) -  Common.removeComma($("#total_cost").text())));
        //利益（代価(TAX・手数料込)-原価)
        $("#mid_total_net").text(Common.addYenComma(Common.removeComma($("#mid_total_amount_price").text()) -  Common.removeComma($("#total_cost").text())));
        //利益（代価(TAX・手数料・割引料込)
        $("#total_net").text(Common.addYenComma(Common.removeComma($("#total_amount_price").text()) -  Common.removeComma($("#total_cost").text())));
        //業者配分小計
        $("#mid_total_aw").text(Common.addYenComma(Common.removeComma($("#sub_total_aw").text())));
        //業者配分総合計
        $("#total_aw").text(Common.addYenComma(Common.removeComma($("#sub_total_aw").text())));
        //RW手数料
        $("#sub_total_rw_with_arrange").text(Common.addYenComma(Common.removeComma($("#sub_total_amount_price_with_arrange").text())));
        //RW配分合計(手数料込)
        $("#mid_total_rw").text(Common.addYenComma(Common.removeComma($("#sub_total_rw_with_arrange").text()) + Common.removeComma($("#sub_total_rw").text())));
        //AW配分割引料
        $("#total_aw_with_discount").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount").text()) * ($("#discount_aw_share").text() / 100)));
        $("#total_aw_with_discount_currency").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount_currency").text()) * ($("#discount_aw_share").text() / 100)));
        //RW配分割引料
        $("#total_rw_with_discount").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount").text()) * ($("#discount_rw_share").text() / 100)));
        $("#total_rw_with_discount_currency").text(Common.addYenComma(Common.removeComma($("#total_amount_price_with_discount_currency").text()) * ($("#discount_rw_share").text() / 100)));
        //RW配分総合計(手数料・割引料込)
        $("#total_rw").text(Common.addYenComma(Common.removeComma($("#mid_total_rw").text()) -  Common.removeComma($("#total_rw_with_discount").text()) -  Common.removeComma($("#total_rw_with_discount_currency").text())));
        //AW配分総合計(手数料・割引料込)
        $("#total_aw").text(Common.addYenComma(Common.removeComma($("#mid_total_aw").text()) -  Common.removeComma($("#total_aw_with_discount").text()) -  Common.removeComma($("#total_aw_with_discount_currency").text())));
        //小計利益率
        if(Common.removeComma($("#sub_total_amount_price").text()) == 0)
        {
           $("#sub_total_profit_rate").text("0%");
        }
        else
        {
           $("#sub_total_profit_rate").text(Math.round(Common.removeComma($("#sub_total_net").text())  /  Common.removeComma($("#sub_total_amount_price").text())*100) + "%");
        }
        //中計利益率
        if(Common.removeComma($("#mid_total_amount_price").text()) == 0)
        {
           $("#mid_total_profit_rate").text("0%");
        }
        else
        {
           $("#mid_total_profit_rate").text(Math.round(Common.removeComma($("#mid_total_net").text())  /  Common.removeComma($("#mid_total_amount_price").text()) *100)+ "%");
        }
        //総合計利益率
        if(Common.removeComma($("#total_amount_price").text()) == 0)
        {
           $("#total_profit_rate").text("0%");
        }
        else
        {
            $("#total_profit_rate").text(Math.round(Common.removeComma($("#total_net").text())  /  Common.removeComma($("#total_amount_price").text()) *100)+ "%");
        }

     /* 外貨合計計算 */
        //TAX(小計から国内支払商品の合計を除いてからTAXを求める)
        $("#sub_total_amount_foreign_price_with_tax").text(Common.addDollarComma($("#hawaii_tax_rate").text() / 100 *  (Common.removeDollarComma($("#sub_total_amount_foreign_price").text())-internal_total_foreign_price)));
        //手数料
        $("#sub_total_amount_foreign_price_with_arrange").text(Common.addDollarComma($("#service_rate").text()/ 100 *  Common.removeDollarComma($("#sub_total_amount_foreign_price").text())));
        //代価合計(TAX・手数料込)
        $("#mid_total_amount_foreign_price").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_amount_foreign_price").text()) +
                                                      Common.removeDollarComma($("#sub_total_amount_foreign_price_with_tax").text()) +
                                                      Common.removeDollarComma($("#sub_total_amount_foreign_price_with_arrange").text())));
        //割引料
        $("#total_amount_foreign_price_with_discount").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) *  ($("#discount_rate").text()/100)));
        if($("#discount_exchange_rate").text()=="0" || $("#discount_exchange_rate").text()=="0.00"){
          $("#total_amount_foreign_price_with_discount_currency").text("0");
        }else{
          $("#total_amount_foreign_price_with_discount_currency").text(Common.addDollarComma($("#discount").text() / $("#discount_exchange_rate").text()));
        }
        //代価合計(TAX・手数料・割引料込)
        $("#total_amount_foreign_price").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_amount_foreign_price_with_discount").text())- Common.removeDollarComma($("#total_amount_foreign_price_with_discount_currency").text())));
        //原価小計
        $("#mid_total_foreign_cost").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_cost").text())));
        //原価総合計
        $("#total_foreign_cost").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_cost").text())));
        //利益（代価-原価)
        $("#sub_total_foreign_net").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_foreign_cost").text())));
        //利益（代価(TAX・手数料込)-原価)
        $("#mid_total_foreign_net").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_foreign_cost").text())));
        //利益（代価(TAX・手数料・割引料込)
        $("#total_foreign_net").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price").text()) -  Common.removeDollarComma($("#total_foreign_cost").text())));
        //業者配分小計
        $("#mid_total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_aw").text())));
        //RW手数料
        $("#sub_total_foreign_rw_with_arrange").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_amount_foreign_price_with_arrange").text())));
        //RW配分合計(手数料込)
        $("#mid_total_foreign_rw").text(Common.addDollarComma(Common.removeDollarComma($("#sub_total_foreign_rw_with_arrange").text()) + Common.removeDollarComma($("#sub_total_foreign_rw").text())));
        //AW配分割引料
        $("#total_foreign_aw_with_discount").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount").text()) * ($("#discount_aw_share").text() / 100)));
        $("#total_foreign_aw_with_discount_currency").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount_currency").text()) * ($("#discount_aw_share").text() / 100)));
        //RW配分割引料
        $("#total_foreign_rw_with_discount").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount").text()) * ($("#discount_rw_share").text() / 100)));
        $("#total_foreign_rw_with_discount_currency").text(Common.addDollarComma(Common.removeDollarComma($("#total_amount_foreign_price_with_discount_currency").text()) * ($("#discount_rw_share").text() / 100)));
        //RW配分総合計(手数料・割引料込)
        $("#total_foreign_rw").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_foreign_rw").text()) -  Common.removeDollarComma($("#total_foreign_rw_with_discount").text()) - Common.removeDollarComma($("#total_foreign_rw_with_discount_currency").text())));
        //AW配分総合計(手数料・割引料込)
        $("#total_foreign_aw").text(Common.addDollarComma(Common.removeDollarComma($("#mid_total_foreign_aw").text()) -  Common.removeDollarComma($("#total_foreign_aw_with_discount").text()) - Common.removeDollarComma($("#total_foreign_aw_with_discount_currency").text())));

        //小計利益率
        if(Common.removeComma($("#sub_total_amount_price").text()) == 0)
        {
           $("#sub_total_foreign_profit_rate").text("0%");
        }
        else
        {
           $("#sub_total_foreign_profit_rate").text(Math.round(Common.removeDollarComma($("#sub_total_foreign_net").text())  /  Common.removeDollarComma($("#sub_total_amount_foreign_price").text()) * 100)+ "%")
        }
        //中計利益率
        if(Common.removeComma($("#mid_total_amount_price").text()) == 0)
        {
           $("#mid_total_foreign_profit_rate").text("0%");
        }
        else
        {
         $("#mid_total_foreign_profit_rate").text(Math.round(Common.removeDollarComma($("#mid_total_foreign_net").text())  /  Common.removeDollarComma($("#mid_total_amount_foreign_price").text()) * 100)+ "%")
        }
        //総合計利益率
        if(Common.removeComma($("#total_amount_price").text()) == 0)
        {
           $("#total_foreign_profit_rate").text("0%");
        }
        else
        {
             $("#total_foreign_profit_rate").text(Math.round(Common.removeDollarComma($("#total_foreign_net").text())  /  Common.removeDollarComma($("#total_amount_foreign_price").text()) * 100)+ "%")
        }

        //PD手配料合計
        $("#total_pd_payment").text($("#total_foreign_aw").text());
        //現地支払合計
        $("#total_vendor_payment").text($("#total_foreign_cost").text());
        //州税合計
        $("#total_hawaii_tax").text(Common.addDollarComma((Common.removeDollarComma($("#total_pd_payment").text()) + Common.removeDollarComma($("#total_vendor_payment").text()))* $("#hawaii_tax_rate").text() / 100));
        //振込額合計
        $("#total_remittance").text(Common.addDollarComma(Common.removeDollarComma($("#total_pd_payment").text()) + Common.removeDollarComma($("#total_hawaii_tax").text()) + Common.removeDollarComma($("#total_vendor_payment").text())));
    }

    //明細1行の再計算
    function recalculateLine()
    {
      for(i=1;i <= table_counter;i++)
      {
        //国内払い用の商品
        //if($("#internal_pay_flg" + i).val() == 1){
        //   internal_total_price         += Common.removeComma($("#amount_price" + i).text());
       //    internal_total_foreign_price += Common.removeComma($("#foreign_amount_price" + i).text());
       // }

       $("#row"+i).removeClass();
       if($("#payment_kbn"+i).val() == $aboard_credit_pay){
           $("#row"+i).addClass('creditAboardPay');
           $("#foreign_cost"+i).text('0.00');
           $("#cost"+i).text('0.00');
           $("#aw_share"+i).text($("#aw_share"+i).attr('name'));

           if($("#remittanceExchangeRate").val() == "" || $("#remittanceExchangeRate").val() == "0" || $("#remittanceExchangeRate").val() == "0.00"){
              $("#foreign_aw_share"+ i).text("0");
           }else{
              $("#foreign_aw_share"+ i).text(Common.addDollarComma(Common.removeComma($("#aw_share" + i).text()) /  $("#remittanceExchangeRate").val()));
           }

       }else if($("#payment_kbn"+i).val() == $domestic_direct_pay){
           $("#row"+i).addClass('domesticDirectPay');
           $("#foreign_cost"+i).text('0.00');
           $("#aw_share"+i).text('0.00');
           $("#foreign_aw_share"+i).text('0.00');

   	   }else if($("#payment_kbn"+i).val() == $domestic_credit_pay){
           $("#row"+i).addClass('domesticCreditPay');
           $("#foreign_cost"+i).text('0.00');
           $("#aw_share"+i).text('0.00');
           $("#foreign_aw_share"+i).text('0.00');
   	   }else{
   	       $("#cost"+i).text($("#cost"+i).attr('name'));
   	       $("#foreign_cost"+i).text($("#foreign_cost"+i).attr('name'));
   	       $("#aw_share"+i).text($("#aw_share"+i).attr('name'));

   	       if($("#remittanceExchangeRate").val() == "" || $("#remittanceExchangeRate").val() == "0" || $("#remittanceExchangeRate").val() == "0.00"){
              $("#foreign_aw_share"+ i).text("0");
           }else{
              $("#foreign_aw_share"+ i).text(Common.addDollarComma(Common.removeComma($("#aw_share" + i).text()) /  $("#remittanceExchangeRate").val()));
           }
   	   }
     }
       calculate();
    }

    /* 送金為替レートが変わったら再計算 */
    $("#remittanceExchangeRate").change(function(){
        recalculateLine();
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
             title: "更新結果"
    });

    /* 送金データ更新 */
    $("#formID").submit(function(){

    　　        /* 更新開始 */
		$(this).simpleLoading('show');
		var formData = $("#formID").serialize();
		$.post("$edit_remittance_url",formData , function(result) {

		  $(this).simpleLoading('hide');

		  var obj = null;
	      try {
            obj = $.parseJSON(result);
          } catch(e) {
            obj = {};
            obj.result = false;
		    obj.message = "致命的なエラーが発生しました。";
		    obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
		    $("#result_dialog").data("status","false");
		    $("#critical_error").text(result);
          }

		  if(obj.result == true){
		     $("#result_message img").attr('src',"$confirm_image_path");
		      $("#result_dialog").data("status","true");
		  }else{
		     $("#result_message img").attr('src',"$error_image_path");
		      $("#result_dialog").data("status","false");
		  }
		    $("#result_message span").text(obj.message);
		    $("#error_reason").text(obj.reason);
            $("#result_dialog").dialog('open');
        });
		return false;
    });

    /* 支払区分が変更されたら再計算する(国内払いの場合は合計に州税を含めないため） */
    $(".payment_kbn").change(function(){
         recalculateLine();
    });

    //ページを読み込んだ時点で総計の計算
	calculate();
});
JSPROG
)) ?>

<ul class="operate">
	<li><a href="<?php echo $html->url('.') ?>">戻る</a></li>
</ul>

<form id="formID" class="content" method="post" name="remittance" action="<?php echo $html->url('editRemittance')+ "/" + $data[0]['EstimateDtlTrnView']['estimate_id'] ?>">
<table cellspacing="5px">
<tr>
    <td><label for="data[EstimateTrn][remittance_exchange_rate]"> 送金為替レート: </label></td>
    <td>
         <?php
               echo "<input type='text' id='remittanceExchangeRate' class='validate[required,custom[number],max[999],maxSize[6]] inputnumeric' name='data[EstimateTrn][remittance_exchange_rate]' value='{$data[0]['EstimateDtlTrnView']['remittance_exchange_rate']}' />".
                    "<input type='hidden' name='data[EstimateTrn][id]' value='{$data[0]['EstimateDtlTrnView']['estimate_id']}' />";
         ?>
    </td>
    <td>特記事項:</td><td><textarea class="small-inputcomment " name="data[EstimateTrn][note]" ><?php echo $data[0]["EstimateDtlTrnView"]["header_note"] ?></textarea></td>
</tr>
</table>
<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
<table id="invoice_table" class="list" cellspacing="0">

    <tr class="nodrag nodrop">
	    <th>支払区分</th>
	    <th>商品区分</th>
	    <th>商品名</th>
	    <th style='width:80px;'>数量</th>
        <th class="dollar">総代価<?php echo $html->image('dollar.png')?></th>
        <th class="yen">   総代価<?php echo $html->image('yen.png')?></th>
        <th class="dollar">総原価<?php echo $html->image('dollar.png')?></th>
        <th class="yen">   総原価<?php echo $html->image('yen.png')?></th>
        <th class="yen">   利益<?php echo $html->image('yen.png')?></th>
        <th class="yen">   利益率<?php echo $html->image('yen.png')?></th>
        <th class="yen">   HI<?php echo $html->image('yen.png')?></th>
        <th class="dollar">HI<?php echo $html->image('dollar.png')?></th>
        <th class="yen">   RW<?php echo $html->image('yen.png')?></th>
        <th>HI/SH</th>
        <th>RW/SH</th>
        <th>販売為替レート</th>
	    <th>原価為替レート</th>
    </tr>
<?php
   for($i=0;$i < count($data);$i++)
   {
   	if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
   	   echo "<tr id='row".($i+1)."' class='creditAboardPay'>";
   	}else if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY){
   	   echo "<tr id='row".($i+1)."' class='domesticDirectPay'>";
   	}else if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
   	   echo "<tr id='row".($i+1)."' class='domesticCreditPay'>";
   	}else{
   	   echo "<tr id='row".($i+1)."'>";
   	}
  echo "<!--  支払区分 -->".
         "<td>".
             "<select id='payment_kbn".($i+1)."' name='data[EstimateDtlTrn][".($i+1)."][payment_kbn_id]' class='payment_kbn'>";
                 for($payment_kbn_index=0;$payment_kbn_index < count($payment_kbn_list);$payment_kbn_index++){
                   $atr = $payment_kbn_list[$payment_kbn_index];
                   if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == $atr['PaymentKbnMst']['id']){
                      echo "<option value='{$atr['PaymentKbnMst']['id']}' selected>{$atr['PaymentKbnMst']['payment_kbn_nm']}</option>";
                   }else{
                   	  echo "<option value='{$atr['PaymentKbnMst']['id']}'>{$atr['PaymentKbnMst']['payment_kbn_nm']}</option>";
                   }
                 }
   echo       "</select>".
              "<input type='hidden' name='data[EstimateDtlTrn][".($i+1)."][id]' value='{$data[$i]['EstimateDtlTrnView']['id']}' />".
         "</td>".
      "<!--  商品区分 -->".
         "<td>".$data[$i]['EstimateDtlTrnView']['goods_kbn_nm']."</td>".

        "<!--  商品名 -->".
         "<td style='width:500px;'><div class='goods'>{$data[$i]['EstimateDtlTrnView']['sales_goods_nm']}</div></td>".

        "<!--  数量 -->".
        "<td>{$data[$i]['EstimateDtlTrnView']['num']}</td>";

        //価格計算等の準備
         $rate = $data[$i]['EstimateDtlTrnView']['sales_exchange_rate'];
         $cost_rate = $data[$i]['EstimateDtlTrnView']['cost_exchange_rate'];
         $num = $data[$i]['EstimateDtlTrnView']['num'];
         $aw_rate = $data[$i]['EstimateDtlTrnView']['aw_share'];
         $rw_rate = $data[$i]['EstimateDtlTrnView']['rw_share'];
         $foreign_unit_price=0;
         $foreign_amount_price=0;
         $foreign_unit_cost=0;
         $foreign_cost=0;
         $foreign_net=0;
         $foreign_profit_rate=0;
         $foreign_aw_share=0;
         $foreign_rw_share=0;

         $unit_price=0;
         $amount_price=0;
         $unit_cost=0;
         $cost=0;
         $net=0;
         $profit_rate=0;
         $aw_share=0;
         $rw_share=0;

         //ドルベース
         if($data[$i]['EstimateDtlTrnView']['currency_kbn']==0)
         {
         	$foreign_unit_price = $data[$i]['EstimateDtlTrnView']['sales_price'];
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost = $data[$i]['EstimateDtlTrnView']['sales_cost'];
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }

            $unit_price = round($data[$i]['EstimateDtlTrnView']['sales_price'] * $rate);
            $amount_price = $unit_price * $num;
            $unit_cost = round($data[$i]['EstimateDtlTrnView']['sales_cost'] * $cost_rate);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }
         }
         //円ベース
         else
         {
            $unit_price = round($data[$i]['EstimateDtlTrnView']['sales_price']);
            $amount_price = $unit_price * $num;
            $unit_cost = round($data[$i]['EstimateDtlTrnView']['sales_cost']);
            $cost = $unit_cost * $num;
            $net = $amount_price - $cost;
            $aw_share = $net * $aw_rate;
            $rw_share = $net * $rw_rate;
            if($amount_price != 0){
            	$profit_rate = round($net / $amount_price * 100);
            }

            $foreign_unit_price = round($data[$i]['EstimateDtlTrnView']['sales_price'] / $rate,2);
            $foreign_amount_price = $foreign_unit_price * $num;
            $foreign_unit_cost = round($data[$i]['EstimateDtlTrnView']['sales_cost'] / $cost_rate,2);
            $foreign_cost = $foreign_unit_cost * $num;
            $foreign_net = $foreign_amount_price - $foreign_cost;
            $foreign_aw_share = $foreign_net * $aw_rate;
            $foreign_rw_share = $foreign_net * $rw_rate;
            if($foreign_amount_price != 0){
            	$foreign_profit_rate = round($foreign_net / $foreign_amount_price * 100);
            }
         }

echo    "<!-- 総代価 (外貨)-->".
        "<td id='foreign_amount_price".($i+1)."' class='dollar'>".number_format($foreign_amount_price,2)."</td>".
        "<!-- 総代価 (円貨)-->".
        "<td id='amount_price".($i+1)."' class='yen'>".number_format($amount_price)."</td>".

        "<!-- 総原価 (外貨)-->";
          if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
   	          echo "<td id='foreign_cost".($i+1)."' class='dollar emphasis' name='".number_format($foreign_cost,2)."'>0.00</td>";
    	  }else if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
    	           $data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
   	          echo "<td id='foreign_cost".($i+1)."' class='dollar emphasis' name='".number_format($foreign_cost,2)."'>0.00</td>";
   	      }else{
   	          echo "<td id='foreign_cost".($i+1)."' class='dollar emphasis' name='".number_format($foreign_cost,2)."'>".number_format($foreign_cost,2)."</td>";
   	      }

   	    "<!-- 総原価 (円貨)-->";
          if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
   	          echo "<td id='cost".($i+1)."' class='yen' name='".number_format($cost)."'>0.00</td>";
    	  }else{
   	          echo "<td id='cost".($i+1)."' class='yen' name='".number_format($cost)."'>".number_format($cost)."</td>";
   	      }

 echo   "<!-- 利益(円貨)-->".
        "<td id='net".($i+1)."' class='yen'>".number_format($net)."</td>".
        "<!-- 利益率(円貨) -->".
        "<td id='profit_rate".($i+1)."' class='yen'>".number_format($profit_rate)."%</td>".

        "<!-- awシェア (円貨)-->";
         if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
            $data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
   	          echo "<td id='aw_share".($i+1)."' class='yen' name='".number_format($aw_share)."'>0.00</td>";
   	      }else{
   	          echo "<td id='aw_share".($i+1)."' class='yen' name='".number_format($aw_share)."'>".number_format($aw_share)."</td>";
   	      }

echo    "<!-- awシェア (外貨)-->";
         if($data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_DIRECT_PAY ||
            $data[$i]['EstimateDtlTrnView']['payment_kbn_id'] == PC_DOMESTIC_CREDIT_PAY){
              if($data[0]['EstimateDtlTrnView']['remittance_exchange_rate'] == 0 ||
                 $data[0]['EstimateDtlTrnView']['remittance_exchange_rate'] == 0.00 ||
                 $data[0]['EstimateDtlTrnView']['remittance_exchange_rate']==""){
              	echo "<td id='foreign_aw_share".($i+1)."' class='dollar emphasis' name='0.00'>0.00</td>";
              }else{
              	echo "<td id='foreign_aw_share".($i+1)."' class='dollar emphasis' name='".number_format($aw_share / $data[0]['EstimateDtlTrnView']['remittance_exchange_rate'],2)."'>0.00</td>";
              }

   	      }else{
   	      	if($data[0]['EstimateDtlTrnView']['remittance_exchange_rate'] == ""  ||
   	      	   $data[0]['EstimateDtlTrnView']['remittance_exchange_rate'] == "0" ||
   	      	   $data[0]['EstimateDtlTrnView']['remittance_exchange_rate'] == "0.00"){
                   echo "<td id='foreign_aw_share".($i+1)."' class='dollar emphasis' name='0.00'>0.00</td>";
              }else{
              	   echo "<td id='foreign_aw_share".($i+1)."' class='dollar emphasis' name='".number_format($aw_share / $data[0]['EstimateDtlTrnView']['remittance_exchange_rate'],2)."'>".number_format($aw_share / $data[0]['EstimateDtlTrnView']['remittance_exchange_rate'],2)."</td>";
              }
   	      }

echo    "<!-- rwシェア(円貨)-->".
        "<td id='rw_share".($i+1)."' class='yen'>".number_format($rw_share)."</td>".

       "<!-- awレート-->".
        "<td>".($data[$i]['EstimateDtlTrnView']['aw_share']*100)."%</td>".
        "<!-- rwレート-->".
        "<td>".($data[$i]['EstimateDtlTrnView']['rw_share']*100)."%</td>".
       "<!-- 販売為替レート-->".
        "<td>{$data[$i]['EstimateDtlTrnView']['sales_exchange_rate']}</td>".
       "<!-- 原価為替レート-->".
        "<td>{$data[$i]['EstimateDtlTrnView']['cost_exchange_rate']}</td>".
    "</tr>";
  }
?>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">SUBTOTAL</td>
            <td>&nbsp;</td>

            <td class="dollar"          id="sub_total_amount_foreign_price">&nbsp;</td>
            <td class="yen"             id="sub_total_amount_price">&nbsp;</td>
            <td class="dollar emphasis" id="sub_total_foreign_cost" >&nbsp;</td>
            <td class="yen"             id="sub_total_cost" >&nbsp;</td>
            <td class="yen"             id="sub_total_net" >&nbsp;</td>
            <td class="yen"             id="sub_total_profit_rate" >&nbsp;</td>
            <td class="yen"             id="sub_total_aw" >&nbsp;</td>
            <td class="dollar emphasis" id="sub_total_foreign_aw" >&nbsp;</td>
            <td class="yen"             id="sub_total_rw" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right"><span id=fontSizeTest>ハワイ州税</span></td>
            <td align="left"><div id="hawaii_tax_rate" style="display:inline;"><?php echo $data[0]['EstimateDtlTrnView']['hawaii_tax_rate']*100 ?></div>%</td>

            <td class="dollar" id="sub_total_amount_foreign_price_with_tax">&nbsp;</td>
            <td class="yen"    id="sub_total_amount_price_with_tax">&nbsp;</td>
            <td class="emphasis">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="emphasis">&nbsp;</td>
            <td class="">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right"><?php echo $data[0]['EstimateDtlTrnView']['service_rate_nm'] ?>%</td>
            <td align="left"><div id="service_rate" style="display:inline;"><?php echo $data[0]['EstimateDtlTrnView']['service_rate']*100 ?></div>%</td>

            <td class="dollar" id="sub_total_amount_foreign_price_with_arrange">&nbsp;</td>
            <td class="yen"    id="sub_total_amount_price_with_arrange">&nbsp;</td>
            <td class="emphasis">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="emphasis">&nbsp;</td>
            <td class="yen" id="sub_total_rw_with_arrange">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">SUBTOTAL</td>
            <td>&nbsp;</td>

            <td class="dollar" id="mid_total_amount_foreign_price">&nbsp;</td>
            <td class="yen"    id="mid_total_amount_price">&nbsp;</td>
            <td class="dollar emphasis" id="mid_total_foreign_cost" >&nbsp;</td>
            <td class="yen"    id="mid_total_cost" >&nbsp;</td>
            <td class="yen"    id="mid_total_net" >&nbsp;</td>
            <td class="yen"    id="mid_total_profit_rate" >&nbsp;</td>
            <td class="yen"    id="mid_total_aw" >&nbsp;</td>
            <td class="dollar emphasis" id="mid_total_foreign_aw" >&nbsp;</td>
            <td class="yen"    id="mid_total_rw" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right"><?php echo $data[0]['EstimateDtlTrnView']['discount_rate_nm'] ?></td>
            <td align="left"><div id="discount_rate" style="display:inline;"><?php echo $data[0]['EstimateDtlTrnView']['discount_rate']*100 ?></div><span>%</span></td>

            <td class="dollar minus" id="total_amount_foreign_price_with_discount">&nbsp;</td>
            <td class="yen minus"    id="total_amount_price_with_discount">&nbsp;</td>
            <td class="emphasis">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="yen minus"             id="total_aw_with_discount">&nbsp;</td>
            <td class="dollar emphasis minus" id="total_foreign_aw_with_discount">&nbsp;</td>
            <td class="yen minus"             id="total_rw_with_discount" >&nbsp;</td>

            <td><div id="discount_aw_share" style="display:inline;"><?php echo $data[0]['EstimateDtlTrnView']['discount_aw_share']*100 ?></div><span>%</span></td>
            <td><div id="discount_rw_share" style="display:inline;"><?php echo $data[0]['EstimateDtlTrnView']['discount_rw_share']*100 ?></div><span>%</span></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right"><?php echo $data[0]['EstimateDtlTrnView']['discount_nm'] ?></td>
            <td align="left" id="discount"><?php echo $data[0]['EstimateDtlTrnView']['discount'] ?></td>

            <td class="dollar minus" id="total_amount_foreign_price_with_discount_currency">&nbsp;</td>
            <td class="yen minus"    id="total_amount_price_with_discount_currency">&nbsp;</td>
            <td class="yen emphasis" >&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="">&nbsp;</td>
            <td class="yen minus"    id="total_aw_with_discount_currency" >&nbsp;</td>
            <td class="dollar emphasis minus" id="total_foreign_aw_with_discount_currency" >&nbsp;</td>
            <td class="yen minus"    id="total_rw_with_discount_currency" >&nbsp;</td>

            <td><?php echo $data[0]['EstimateDtlTrnView']['discount_aw_share']*100 ?>%</td>
            <td><?php echo $data[0]['EstimateDtlTrnView']['discount_rw_share']*100 ?>%</td>
            <td>割引額為替レート</td>
            <td><div id="discount_exchange_rate"><?php echo $data[0]['EstimateDtlTrnView']['discount_exchange_rate'] ?></div></td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">TOTAL</td>
            <td>&nbsp;</td>

            <td class="dollar" id="total_amount_foreign_price">&nbsp;</td>
            <td class="yen"    id="total_amount_price">&nbsp;</td>
            <td class="dollar emphasis" id="total_foreign_cost">&nbsp;</td>
            <td class="yen"    id="total_cost" >&nbsp;</td>
            <td class="yen" id="total_net" >&nbsp;</td>
            <td class="yen" id="total_profit_rate" >&nbsp;</td>
            <td class="yen" id="total_aw" >&nbsp;</td>
            <td class="yen emphasis" id="total_foreign_aw" >&nbsp;</td>
            <td class="yen" id="total_rw" >&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar" id="" >&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">PD手配料</td>
            <td>&nbsp;</td>

            <td class="dollar" id="total_pd_payment">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">州税</td>
            <td>&nbsp;</td>

            <td class="dollar" id="total_hawaii_tax">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">現地支払い額</td>
            <td>&nbsp;</td>

            <td class="dollar" id="total_vendor_payment">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">ＲＷからPDへの振り込み額</td>
            <td >&nbsp;</td>

            <td class="dollar" id="total_remittance">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>
            <td class="dollar">&nbsp;</td>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
</table>
</div>
<div class="submit">
    <input type="submit" class="inputbutton" name="update" value=" 更新   " />
</div>
</form>
<div id="result_dialog" style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
