<?php
  //商品追加URL
  $add_goods_url = $html->url('addGoods');
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");
  $tax_rate = $env_data['EnvMst']['hawaii_tax_rate']*100;
$script = <<< EOL

$(function(){

    $("input:submit").button();
    $("#church_code").mask("aa");
    $(".inputdate").mask("9999-99-99");
   // $(".number").mask("99.99");
   // $("#tax").mask("9.999");
    $("#tax").val("$tax_rate");

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
             title: "登録結果"
    });

    //商品分類が選択されたらそれに属する商品区分リストを表示
	$("#goods_ctg").change(function() {

		  $.get("goodsKbnList/" + $(this).val(), function(data) {
		  $("#goods_kbn").html(data);
		});
	});

	$("#internal_pay_flg").change(function() {
          var val = $(this).prop("checked") ? 1:0;
		  $.get("paymentKbnList/" + val, function(data) {
		  $("#payment_kbn_list").html(data);
		});
	});

	//フォーム送信前操作
	$("#formID").submit(function(){

	   if( $("#formID").validationEngine('validate')==false){ return false; }

	   /* 登録開始 */
		$(this).simpleLoading('show');
		CalculateCost();
		var formData = $(this).serialize();
		$.post("$add_goods_url",formData , function(result) {

		  $(this).simpleLoading('hide');
          $("#goods_code").text("");

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
		     $("#result_message img").attr('src',"$confirm_image_path");
		     $("#goods_code").text(obj.code);
		  }else{
		     $("#result_message img").attr('src',"$error_image_path");
		  }
		    $("#result_message span").text(obj.message);
		    $("#error_reason").text(obj.reason);
            $("#result_dialog").dialog('open');
        });
		return false;
	});
    CalculateCost();
    $(".culculate").focusout(function(){ CalculateCost(); });

	function CalculateCost(){
	  var price       = isFinite($("#goods_price").val())  && $("#goods_price").val()  != ""  ? parseFloat($("#goods_price").val()) : 0;
	  var tax         = isFinite($("#tax").val())          && $("#tax").val()          != ""  ? new BigNumber($("#tax").val()).div(100).toPrecision() : 0;
	  var serviceRate = isFinite($("#service_rate").val()) && $("#service_rate").val() != ""  ? new BigNumber($("#service_rate").val()).div(100).toPrecision() : 0;
	  var profitRate  = isFinite($("#profit_rate").val())  && $("#profit_rate").val()  != ""  ? new BigNumber($("#profit_rate").val()).div(100).toPrecision() : 0;
	  var cost1  = isFinite($("#cost1").val()) && $("#cost1").val() != ""  ? parseFloat($("#cost1").val())  : 0;
	  var cost2  = isFinite($("#cost2").val()) && $("#cost2").val() != ""  ? parseFloat($("#cost2").val())  : 0;
	  var cost3  = isFinite($("#cost3").val()) && $("#cost3").val() != ""  ? parseFloat($("#cost3").val())  : 0;
	  var cost4  = isFinite($("#cost4").val()) && $("#cost4").val() != ""  ? parseFloat($("#cost4").val())  : 0;
	  var cost5  = isFinite($("#cost5").val()) && $("#cost5").val() != ""  ? parseFloat($("#cost5").val())  : 0;
	  var cost6  = isFinite($("#cost6").val()) && $("#cost6").val() != ""  ? parseFloat($("#cost6").val())  : 0;
	  var cost7  = isFinite($("#cost7").val()) && $("#cost7").val() != ""  ? parseFloat($("#cost7").val())  : 0;
	  var cost8  = isFinite($("#cost8").val()) && $("#cost8").val() != ""  ? parseFloat($("#cost8").val())  : 0;
	  var cost9  = isFinite($("#cost9").val()) && $("#cost9").val() != ""  ? parseFloat($("#cost9").val())  : 0;
	  var cost10 = isFinite($("#cost10").val()) && $("#cost10").val() != "" ? parseFloat($("#cost10").val()) : 0;

	  var costTotal = cost1 + cost2 + cost3 + cost4 + cost5 + cost6 + cost7 + cost8 + cost9 + cost10;
	  var netTax = new BigNumber(tax).plus(1).times(costTotal).round(2).toPrecision();
	  var serviceCharge = new BigNumber(serviceRate).plus(1).times(netTax).round(2).toPrecision();
	  var profitRate_ = new BigNumber(profitRate).plus(1).times(serviceCharge).round(2).toPrecision();
	  var profit = new BigNumber(price).minus(serviceCharge).round(2).toPrecision();

	  $("#net_tax").text(netTax);
	  $("#service_charge").text(serviceCharge);
	  $("#goods_cost").val(serviceCharge);
	  $("#profit_rate_").text(profitRate_);
	  $("#profit").text(profit);
	}
});

EOL;
echo $html->scriptBlock($script,array('inline'=>false,'safe'=>true));
?>
<style>
.cost_form th {
	width: 100px;
	padding: 6px 0px 6px 6px;
	text-align: left;
}

.cost_form td {
	padding: 6px 0px 6px 6px;
}
</style>
    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content" method="post" name="Goods action="<?php echo $html->url('addGoods') ?>" >

		<table class="form" cellspacing="0">
		  <tr>
             <th>見積非表示</th>
             <td><input type="checkbox" value="1"  name="data[GoodsMst][non_display_flg]" /></td>
          </tr>
          <tr>
             <th>商品コード</th>
             <td id="goods_code"></td>
          </tr>
          <tr>
             <th>有効期限</th>
             <td>
                 <input type="text" name="data[GoodsMst][start_valid_dt]" id="start_valid_dt" class="inputdate" value="" style='text-align:center' />
                 <span>～</span>
                 <input type="text" name="data[GoodsMst][end_valid_dt]"   id="end_valid_dt"   class="inputdate" value="" style='text-align:center' />
             </td>
          </tr>
           <tr>
             <th>商品分類<span class="necessary">(必須)</span></th>

             <td>
                 <select id="goods_ctg" class="validate[required]" name="data[GoodsMst][goods_ctg_id]">
                    <option value=""></option>
   			        <?php

   			           for($i=0;$i < count($goods_ctg_list);$i++)
   			           {
   			             $atr = $goods_ctg_list[$i]['GoodsCtgMst'];
   			             echo "<option value='{$atr['id']}'>{$atr['goods_ctg_nm']}</option>";
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
           <tr>
             <th>商品区分<span class="necessary">(必須)</span></th>

             <td>
                 <select id="goods_kbn"  class="validate[required]" name="data[GoodsMst][goods_kbn_id]">
   			        <?php

   			           for($i=0;$i < count($goods_kbn_list);$i++)
   			           {
   			             $atr = $goods_kbn_list[$i]['GoodsKbnMst'];
   			             echo "<option value='{$atr['id']}'>{$atr['goods_kbn_nm']}</option>";
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
          <tr>
             <th>国内払い</th>
             <td><input type="checkbox" value="1"  id="internal_pay_flg" name="data[GoodsMst][internal_pay_flg]" /></td>
          </tr>
          <tr>
             <th>支払区分</th>
             <td>
                 <select id="payment_kbn_list" name="data[GoodsMst][payment_kbn_id]">
   			        <?php
   			           for($i=0;$i < count($payment_kbn_list);$i++){
   			             $atr = $payment_kbn_list[$i]['PaymentKbnMst'];
   			             echo "<option value='{$atr['id']}'>{$atr['payment_kbn_nm']}</option>";
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
          <tr>
             <th>商品名<span class="necessary">(必須)</span></th>
             <td><textarea name="data[GoodsMst][goods_nm]" id="goods_nm" class="validate[required,maxSize[500]] large-inputcomment" rows="8"></textarea></td>
          </tr>
          <!--
          <tr>
             <th>セット商品</th>
             <td><input type="checkbox" value="1" name="data[GoodsMst][set_goods_kbn]" /></td>
          </tr>
          -->
          <tr>
             <th>ベンダー名</th>

             <td>
                 <select  name="data[GoodsMst][vendor_id]">
   			        <?php
   			           for($i=0;$i < count($vendor_list);$i++)
   			           {
   			             $atr = $vendor_list[$i]['VendorMst'];
   			             echo "<option value='{$atr['id']}'>{$atr['vendor_nm']}</option>";
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
          <tr>
             <th>通貨区分</th>
             <td>
               <select name="data[GoodsMst][currency_kbn]">
                 <option value="0" selected="selected">ドルベース</option>
                 <option value="1">円ベース</option>
               </select>
             </td>
          </tr>
          <tr>
             <th>HIシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][aw_share]" id="aw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[rw_share]] inputnumeric number digit" value="" /></td>

          </tr>
          <tr>
             <th>RWシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][rw_share]" id="rw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[aw_share]] inputnumeric number digit" value="" /></td>
          </tr>
	    </table>

	    <fieldset>
	      <legend>価格明細</legend>
          <table class="cost_form" cellspacing="0">
          <tr>
             <th>Tax</th>
             <td><input type="text" name="data[GoodsMst][tax]" id="tax" class="validate[custom[number],max[100],maxSize[5]] inputnumeric culculate digit" value="" /></td>
             <th>Service Rate(%)</th>
             <td><input type="text" name="data[GoodsMst][service_rate]" id="service_rate" class="validate[custom[number],max[100],maxSize[5]] inputnumeric culculate number digit" value="" /></td>
             <th>Profit Rate(%)</th>
             <td><input type="text" name="data[GoodsMst][profit_rate]" id="profit_rate" class="validate[custom[number],max[100],maxSize[5]] inputnumeric culculate number digit" value="" /></td>
          </tr>

          <tr>
             <th>原価名1</th>
             <td><input type="text" name="data[GoodsMst][cost_nm1]"  id="cost_nm1" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名2</th>
             <td><input type="text" name="data[GoodsMst][cost_nm2]"  id="cost_nm2" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名3</th>
             <td><input type="text" name="data[GoodsMst][cost_nm3]"  id="cost_nm3" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名4</th>
             <td><input type="text" name="data[GoodsMst][cost_nm4]"  id="cost_nm4" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名5</th>
             <td><input type="text" name="data[GoodsMst][cost_nm5]"  id="cost_nm5" class="validate[maxSize[50]]" value="" /></td>
          </tr>
          <tr>
             <th>原価1<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][cost1]"  id="cost1" class="validate[required,custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価2</th>
             <td><input type="text" name="data[GoodsMst][cost2]"  id="cost2" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価3</th>
             <td><input type="text" name="data[GoodsMst][cost3]"  id="cost3" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価4</th>
             <td><input type="text" name="data[GoodsMst][cost4]"  id="cost4" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価5</th>
             <td><input type="text" name="data[GoodsMst][cost5]"  id="cost5" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
          </tr>

          <tr>
             <th>原価名6</th>
             <td><input type="text" name="data[GoodsMst][cost_nm6]"  id="cost_nm6" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名7</th>
             <td><input type="text" name="data[GoodsMst][cost_nm7]"  id="cost_nm7" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名8</th>
             <td><input type="text" name="data[GoodsMst][cost_nm8]"  id="cost_nm8" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名9</th>
             <td><input type="text" name="data[GoodsMst][cost_nm9]"  id="cost_nm9" class="validate[maxSize[50]]" value="" /></td>
             <th>原価名10</th>
             <td><input type="text" name="data[GoodsMst][cost_nm10]" id="cost_nm10" class="validate[maxSize[50]]" value="" /></td>
          </tr>
          <tr>
             <th>原価6</th>
             <td><input type="text" name="data[GoodsMst][cost6]"  id="cost6" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価7</th>
             <td><input type="text" name="data[GoodsMst][cost7]"  id="cost7" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価8</th>
             <td><input type="text" name="data[GoodsMst][cost8]"  id="cost8" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価9</th>
             <td><input type="text" name="data[GoodsMst][cost9]"  id="cost9" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
             <th>原価10</th>
             <td><input type="text" name="data[GoodsMst][cost10]" id="cost10" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
          </tr>
          <tr>
             <th>Net Tax</th>
             <td id="net_tax"></td>
          </tr>
          <tr>
             <th>サービスチャージ</th>
             <td><span id="service_charge"></span><input type="hidden" name="data[GoodsMst][cost]" id="goods_cost" value="" /></td>
          </tr>
          <tr>
             <th>利益率</th>
             <td id="profit_rate_"></td>
          </tr>
           <tr>
             <th>Gross<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][price]" id="goods_price" class="validate[required,custom[number],max[10000000]] inputnumeric culculate number digit" value="" /></td>
          </tr>
          <tr>
             <th>利益</th>
             <td id="profit"></td>
          </tr>
          </table>
	    </fieldset>


	<div class="submit">
		<input type="submit" class="inputbutton"  value="追加" />
	</div>
   </form>
<div id="result_dialog" style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>