<?php
   //商品更新URL
  $edit_goods_url = $html->url('editGoods');
  $payment_list_url = $html->url('paymentKbnList');
  $main_url = $html->url('.');
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

    $(".inputdate").mask("9999-99-99");

    $("#internal_pay_flg").change(function() {
          var val = $(this).prop("checked") ? 1:0;
		  $.get("$payment_list_url" + "/" + val, function(data) {
		  $("#payment_kbn_list").html(data);
		});
	});

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');

                     if($("#result_dialog").data("action").toUpperCase() == "DELETE" ){
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = "$main_url";
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
	        StartSubmit();
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
       CalculateCost();
	   var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");

	   $.post("$edit_goods_url",formData , function(result) {

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

		      $("#goods_id").val(obj.newId);
		      $("#revision").text(obj.newRevision);
		      $("#hidden_revision").val(obj.newRevision);

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
	}

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
JSPROG
)) ?>

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

    <form id="formID" class="content" method="post" action="" >

        <input type="hidden" id='goods_id' name="data[GoodsMst][id]" value="<?php echo $data['LatestGoodsMstView']['id'] ?>" />
		<table class="form" cellspacing="0">
		   <tr>
             <th>見積非表示</th>
             <td>
              <?php
   			     if($data['LatestGoodsMstView']['non_display_flg'] == 1){
   			     	echo  "<input type='checkbox' name='data[GoodsMst][non_display_flg]' checked />";
   			     }else{
   			   	    echo  "<input type='checkbox' name='data[GoodsMst][non_display_flg]' />";
   			     }
   			  ?>
             </td>
          </tr>
          <tr>
             <th>商品コード</th>
             <td><?php echo $data['LatestGoodsMstView']['goods_cd'] ?><input type="hidden" name="data[GoodsMst][goods_cd]" value="<?php echo $data['LatestGoodsMstView']['goods_cd'] ?>" /></td>
          </tr>
           <tr>
             <th>リビジョンNO</th>
             <td><span id='revision'><?php echo $data['LatestGoodsMstView']['revision'] ?></span><input type="hidden" id='hidden_revision' name="data[GoodsMst][revision]" value="<?php echo $data['LatestGoodsMstView']['revision'] ?>" /></td>
          </tr>
           <tr>
             <th>有効期限</th>
             <td>
                 <?php
                    if($data['LatestGoodsMstView']['start_valid_dt'] == "1000-01-01"){
                      echo "<input type='text' name='data[GoodsMst][start_valid_dt]' id='start_valid_dt' class='inputdate' style='text-align:center' value='' />";
                    }else{
                      echo "<input type='text' name='data[GoodsMst][start_valid_dt]' id='start_valid_dt' class='inputdate' style='text-align:center' value='{$data['LatestGoodsMstView']['start_valid_dt']}' />";
                    }
                 ?>
                 <span>～</span>
                 <?php
                    if($data['LatestGoodsMstView']['end_valid_dt'] == "9999-12-31"){
                      echo "<input type='text' name='data[GoodsMst][end_valid_dt]' id='end_valid_dt' class='inputdate' style='text-align:center' value='' />";
                    }else{
                      echo "<input type='text' name='data[GoodsMst][end_valid_dt]' id='end_valid_dt' class='inputdate' style='text-align:center' value='{$data['LatestGoodsMstView']['end_valid_dt']}' />";
                    }
                 ?>
              </td>
          </tr>
          <tr>
             <th>商品分類</th>
             <td><?php echo $data['LatestGoodsMstView']['goods_ctg_nm'] ?></td>
          </tr>
          <tr>
             <th>商品区分</th>
             <td><?php echo $data['LatestGoodsMstView']['goods_kbn_nm'] ?><input type="hidden" name="data[GoodsMst][goods_kbn_id]" value="<?php echo $data['LatestGoodsMstView']['goods_kbn_id'] ?>" /></td>
          </tr>
          <tr>
             <th>商品名<span class="necessary">(必須)</span></th>
             <td><textarea name="data[GoodsMst][goods_nm]" id="goods_nm" class="validate[required,maxSize[500]] large-inputcomment" rows="8"><?php echo $data['LatestGoodsMstView']['goods_nm'] ?></textarea></td>
          </tr>
          <tr>
             <th>ベンダー名</th>
             <td>
                 <select  name="data[GoodsMst][vendor_id]">
   			        <?php
   			           for($i=0;$i < count($vendor_list);$i++)
   			           {
   			           	 $atr = $vendor_list[$i]['VendorMst'];

   			           	 if($atr['id'] == $data['LatestGoodsMstView']['vendor_id'])
   			             {
   			               echo "<option value='{$atr['id']}' selected='selected'>{$atr['vendor_nm']}</option>";
   			             }
   			             else
   			            {
   			           	   echo "<option value='{$atr['id']}'>{$atr['vendor_nm']}</option>";
   			             }
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
            <tr>
             <th>国内払い</th>
             <td>
              <?php
   			     if($data['LatestGoodsMstView']['internal_pay_flg'] == 1)
   			     {
   			     	echo  "<input type='checkbox' id='internal_pay_flg' name='data[GoodsMst][internal_pay_flg]' checked />";
   			     }
   			     else
   			     {
   			   	    echo  "<input type='checkbox' id='internal_pay_flg' name='data[GoodsMst][internal_pay_flg]' />";
   			     }
   			  ?>
             </td>
          </tr>
          <tr>
             <th>支払区分</th>
             <td>
                 <select id="payment_kbn_list" name="data[GoodsMst][payment_kbn_id]">
   			        <?php
   			           for($i=0;$i < count($payment_kbn_list);$i++){

   			             $atr = $payment_kbn_list[$i]['PaymentKbnMst'];
   			             if($data['LatestGoodsMstView']['payment_kbn_id'] == $atr['id']){
                           echo "<option value='{$atr['id']}' selected>{$atr['payment_kbn_nm']}</option>";
   			             }else{
                           echo "<option value='{$atr['id']}'>{$atr['payment_kbn_nm']}</option>";
   			             }
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
          <tr>
             <th>通貨区分</th>
             <td>
               <select name="data[GoodsMst][currency_kbn]">
               <?php
                 if($data['LatestGoodsMstView']['currency_kbn'] == 0)
                 {
                 	echo "<option value='0' selected='selected'>ドルベース</option>";
                 	echo "<option value='1'>円ベース</option>";
                 }
                 else
                 {
                 	echo "<option value='0'>ドルベース</option>";
                 	echo "<option value='1' selected='selected'>円ベース</option>";
                 }
               ?>
               </select>
             </td>
          </tr>
          <tr>
             <th>HIシェア(%)</th>
             <td><input type="text" name="data[GoodsMst][aw_share]" id="aw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[rw_share]] inputnumeric"
                        value="<?php echo $data['LatestGoodsMstView']['aw_share'] *100 ?>" /></td>
          </tr>
          <tr>
             <th>RWシェア(%)</th>
             <td><input type="text" name="data[GoodsMst][rw_share]" id="rw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[aw_share]] inputnumeric"
                        value="<?php echo $data['LatestGoodsMstView']['rw_share'] *100 ?>" /></td>
          </tr>
	    </table>

	    <fieldset>
	      <legend>価格明細</legend>
          <table class="cost_form" cellspacing="0">
          <tr>
             <th>Tax</th>
             <td><input type="text" name="data[GoodsMst][tax]" id="tax" class="validate[custom[number],max[100],maxSize[5]] inputnumeric culculate digit" value="<?php echo $data['LatestGoodsMstView']['tax']*100 ?>" /></td>
             <th>Service Rate(%)</th>
             <td><input type="text" name="data[GoodsMst][service_rate]" id="service_rate" class="validate[custom[number],max[100],maxSize[5]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['service_rate']*100 ?>" /></td>
             <th>Profit Rate(%)</th>
             <td><input type="text" name="data[GoodsMst][profit_rate]" id="profit_rate" class="validate[custom[number],max[100],maxSize[5]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['profit_rate']*100 ?>" /></td>
          </tr>

          <tr>
             <th>原価名1</th>
             <td><input type="text" name="data[GoodsMst][cost_nm1]"  id="cost_nm1" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm1'] ?>" /></td>
             <th>原価名2</th>
             <td><input type="text" name="data[GoodsMst][cost_nm2]"  id="cost_nm2" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm2'] ?>" /></td>
             <th>原価名3</th>
             <td><input type="text" name="data[GoodsMst][cost_nm3]"  id="cost_nm3" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm3'] ?>" /></td>
             <th>原価名4</th>
             <td><input type="text" name="data[GoodsMst][cost_nm4]"  id="cost_nm4" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm4'] ?>" /></td>
             <th>原価名5</th>
             <td><input type="text" name="data[GoodsMst][cost_nm5]"  id="cost_nm5" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm5'] ?>" /></td>
          </tr>
          <tr>
             <th>原価1<span class="necessary">(必須)</span></th>
             <td>
             <?php
                if($data['LatestGoodsMstView']['cost1']==null || $data['LatestGoodsMstView']['cost1']==0 || empty($data['LatestGoodsMstView']['cost1'])){
                   echo "<input type='text' name='data[GoodsMst][cost1]'  id='cost1' class='validate[required,custom[number],max[10000000]] inputnumeric culculate number digit' value={$data['LatestGoodsMstView']['cost']} />";
                }else{
                   echo "<input type='text' name='data[GoodsMst][cost1]'  id='cost1' class='validate[required,custom[number],max[10000000]] inputnumeric culculate number digit' value={$data['LatestGoodsMstView']['cost1']} />";
                }
             ?>
             </td>
             <th>原価2</th>
             <td><input type="text" name="data[GoodsMst][cost2]"  id="cost2" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost2'] ?>" /></td>
             <th>原価3</th>
             <td><input type="text" name="data[GoodsMst][cost3]"  id="cost3" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost3'] ?>" /></td>
             <th>原価4</th>
             <td><input type="text" name="data[GoodsMst][cost4]"  id="cost4" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost4'] ?>" /></td>
             <th>原価5</th>
             <td><input type="text" name="data[GoodsMst][cost5]"  id="cost5" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost5'] ?>" /></td>
          </tr>

          <tr>
             <th>原価名6</th>
             <td><input type="text" name="data[GoodsMst][cost_nm6]"  id="cost_nm6" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm6'] ?>" /></td>
             <th>原価名7</th>
             <td><input type="text" name="data[GoodsMst][cost_nm7]"  id="cost_nm7" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm7'] ?>" /></td>
             <th>原価名8</th>
             <td><input type="text" name="data[GoodsMst][cost_nm8]"  id="cost_nm8" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm8'] ?>" /></td>
             <th>原価名9</th>
             <td><input type="text" name="data[GoodsMst][cost_nm9]"  id="cost_nm9" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm9'] ?>" /></td>
             <th>原価名10</th>
             <td><input type="text" name="data[GoodsMst][cost_nm10]" id="cost_nm10" class="validate[maxSize[50]]" value="<?php echo $data['LatestGoodsMstView']['cost_nm10'] ?>" /></td>
          </tr>
          <tr>
             <th>原価6</th>
             <td><input type="text" name="data[GoodsMst][cost6]"  id="cost6" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost6'] ?>" /></td>
             <th>原価7</th>
             <td><input type="text" name="data[GoodsMst][cost7]"  id="cost7" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost7'] ?>" /></td>
             <th>原価8</th>
             <td><input type="text" name="data[GoodsMst][cost8]"  id="cost8" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost8'] ?>" /></td>
             <th>原価9</th>
             <td><input type="text" name="data[GoodsMst][cost9]"  id="cost9" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost9'] ?>" /></td>
             <th>原価10</th>
             <td><input type="text" name="data[GoodsMst][cost10]" id="cost10" class="validate[custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['cost10'] ?>" /></td>
          </tr>
          <tr>
             <th>Net Tax</th>
             <td id="net_tax"></td>
          </tr>
          <tr>
             <th>サービスチャージ</th>
             <td><span id="service_charge"><?php echo $data['LatestGoodsMstView']['cost'] ?></span><input type="hidden" name="data[GoodsMst][cost]" id="goods_cost" value="<?php echo $data['LatestGoodsMstView']['cost'] ?>" /></td>
          </tr>
          <tr>
             <th>利益率</th>
             <td id="profit_rate_"></td>
          </tr>
           <tr>
             <th>Gross<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][price]" id="goods_price" class="validate[required,custom[number],max[10000000]] inputnumeric culculate number digit" value="<?php echo $data['LatestGoodsMstView']['price'] ?>" /></td>
          </tr>
          <tr>
             <th>利益</th>
             <td id="profit"></td>
          </tr>
          </table>
	    </fieldset>

	<div class="submit">
	    <input type="submit" id="update"  class="inputbutton"  name="update" value="更新" />
	   	<input type='submit' id='delete'  class='inputbutton'  name='delete' value='削除' />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>