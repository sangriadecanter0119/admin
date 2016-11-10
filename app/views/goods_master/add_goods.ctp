<?php
  //商品追加URL
  $add_goods_url = $html->url('addGoods');
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");
$script = <<< EOL

$(function(){

    $("input:submit").button();
    $("#church_code").mask("aa");
    $(".inputdate").mask("9999-99-99");

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

	//フォーム送信前操作
	$("#formID").submit(function(){

	   if( $("#formID").validationEngine('validate')==false){ return false; }

	   /* 登録開始 */
		$(this).simpleLoading('show');
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
});

EOL;
echo $html->scriptBlock($script,array('inline'=>false,'safe'=>true));
?>

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
             <td><input type="checkbox" value="1"  name="data[GoodsMst][internal_pay_flg]" /></td>
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
             <th>価格<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][price]" id="goods_price" class="validate[required,custom[number],max[10000000]] inputnumeric" value="" /></td>
          </tr>
          <tr>
             <th>原価<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][cost]" id="goods_cost" class="validate[required,custom[number],max[10000000]] inputnumeric" value="" /></td>
          </tr>
          <tr>
             <th>HIシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][aw_share]" id="aw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[rw_share]] inputnumeric" value="" /></td>

          </tr>
          <tr>
             <th>RWシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][rw_share]" id="rw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[aw_share]] inputnumeric" value="" /></td>
          </tr>
	    </table>


	<div class="submit">
		<input type="submit" class="inputbutton"  value="追加" />
	</div>
   </form>
<div id="result_dialog" style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>