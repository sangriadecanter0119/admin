<script type="text/javascript">

   $(".inputdate").mask("9999-99-99");

   /* 処理結果用ダイアログ */
   $("#partial_result_dialog").dialog({
           buttons: [{
               text: "OK",
               click: function () {
                   /* 商品コードが存在すれば新規商品が正常に追加されたので見積情報を更新する */
                   if($("#partial_goods_code").text() != ""){
                	   updateGoodsDetail($("#new_goods_id").val() ,<?php echo $current_line_no ?>);
                   }
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
           zIndex: 9000,
           modal: true,
           title: "登録結果"
   });

  /* 商品追加ダイアログ */
	 $("#goods_addition_dialog").dialog({
	             buttons: [{
	                 text: "OK",
	                 id:"goods_addition_button",
	                 click: function () {

	            	// if( $("#formIDD").validationEngine('validate')==false){ return false; }
	            	 /* 登録開始 */
	        		   $(this).simpleLoading('show');
	        		   var formData = $("#formIDD").serialize();

	        	       $.post(<?php echo "'".$html->url('addGoods')."'" ?>,formData , function(result) {

	        	    	   $(this).simpleLoading('hide');

		        		   var obj = null;
		        	       try {
		                     obj = $.parseJSON(result);
		                   } catch(e) {
		                     obj = {};
		                     obj.result = false;
		        		     obj.message = "致命的なエラーが発生しました。";
		        		     obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
		        		     $("#partial_critical_error").text(result);
		        		     $("#goods_addition_button").attr('disabled',true);
		                  }

		        		  if(obj.result == true){
		        		     $("#partial_result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);
		        		     $("#partial_goods_code").text(obj.code);
		    				 $("#new_goods_id").val(obj.newId);

		    				 $("#goods_addition_button").attr('disabled',true);

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
	            	     $("#goods_addition_dialog").dialog('close');
	                 }
	             }],
	             beforeClose:function(){
		           // $("#formIDD").validationEngine('hideAll');
	            	 /* 商品追加ボタンが非アクティブの場合は 1.商品追加済み 2.致命的エラー のどちらかなので商品リストページも閉じる */
                     if($("#goods_addition_button").attr('disabled')){
                    	$("#goods_list_dialog").dialog('close');
 	                 }
                    $("#goods_addition_dialog").remove();
	             },
	             draggable: false,
	             autoOpen: true,
	             resizable: false,
	             zIndex: 2500,
	             width:600,
	             height:500,
	             modal: true,
	             title: "商品追加"
	 });
</script>
<div id="goods_addition_dialog">
<form id="formIDD"  method="post"  action="" >
        <input type="hidden" id="new_goods_id" />
		<table class="viewheader" cellspacing="0">
          <tr>
             <th>商品コード</th>
             <td id="partial_goods_code"></td>
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
             <th>商品分類</th>
             <td><input type="hidden"  name="data[GoodsMst][goods_ctg_id]" class="validate[required,maxSize[3]] inputnumeric"
                       value="<?php echo $goods_ctg['GoodsCtgMst']['id'] ?>" />
                 <?php  echo $goods_ctg['GoodsCtgMst']['goods_ctg_nm']; ?>
             </td>
          </tr>
          <tr>
             <th>商品区分</th>
            <td><input type="hidden"  name="data[GoodsMst][goods_kbn_id]" class="validate[required,maxSize[3]] inputnumeric"
                       value="<?php echo $goods_kbn['GoodsKbnMst']['id'] ?>" />
                 <?php  echo $goods_kbn['GoodsKbnMst']['goods_kbn_nm']; ?>
             </td>
          </tr>
          <tr>
             <th>国内払い</th>
             <td>
                 <input type='checkbox' name="data[GoodsMst][internal_pay_flg]"  />
             </td>
          </tr>
          <tr>
             <th>商品名<span class="necessary">(必須)</span></th>
             <td><textarea name="data[GoodsMst][goods_nm]"  id="partial_goods_nm" class="validate[required,maxSize[500]] inputcomment" wrap="soft"></textarea></td>
          </tr>
          <tr>
             <th>ベンダー名</th>
             <td>
                 <select name="data[GoodsMst][vendor_id]">
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
             <td><input type="text" id="partial_price" name="data[GoodsMst][price]"  class="validate[required,custom[number],max[10000000]] inputnumeric"  value="" /></td>
          </tr>
          <tr>
             <th>原価<span class="necessary">(必須)</span></th>
             <td><input type="text" id="partial_cost" name="data[GoodsMst][cost]"    class="validate[required,custom[number],max[10000000]] inputnumeric"  value="" /></td>
          </tr>
          <tr>
             <th>HIシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" id="partial_aw_share" name="data[GoodsMst][aw_share]"  class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[aw_share]] inputnumeric" value="" /></td>
          </tr>
          <tr>
             <th>RWシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" id="partial_rw_share" name="data[GoodsMst][rw_share]"  class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[rw_share]] inputnumeric" value="" /></td>
          </tr>
	    </table>
</form>
<!-- 商品追加処理結果用 -->
<div id="partial_result_dialog" style="display:none"><p id="partial_result_message"><img src="#" alt="" /><span></span></p><p id="partial_error_reason"></p></div>
<div id="partial_critical_error"></div>
</div>

