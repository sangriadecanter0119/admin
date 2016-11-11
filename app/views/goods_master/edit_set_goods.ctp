<?php
  //テーブル項目入れ替えようプラグイン
  echo $html->script('jquery/jquery.tablednd_0_5.js',false);
  //商品区分取得URL
  $goods_kbn_url = $html->url('goodsKbnList');
  //商品リストフォーム取得URL
  $goodsFormUrl = $html->url('goodsDetailForm');
  //セット商品更新URL
  $edit_set_goods_url = $html->url('editSetGoods');
  $main_url = $html->url('.');
  $payment_list_url = $html->url('paymentKbnList');
  //明細行数
  $count = count($goods);
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
    var table_counter=$count;
    var current_line_no;

    $("input:submit").button();
    $(".inputdate").mask("9999-99-99");

    function getGoodsKbnNo(str)
    {
      var arr = str.split('_');
      return arr[1];
    }

    function getGoodsNo(str)
    {
      var arr = str.split('[');
      return arr[2].split(']')[0];
    }

    $("#internal_pay_flg").change(function() {
          var val = $(this).prop("checked") ? 1:0;
		  $.get("$payment_list_url" + "/" + val, function(data) {
		  $("#payment_kbn_list").html(data);
		});
	});

    //商品分類が選択されたら属する商品区分リストを表示する
    $(".goods_ctg").change(function() {
          $("#goods_ctg_indicator").css("display","inline");
	      current_line_no = getGoodsKbnNo($(this).attr('name'));

		  $.get("$goods_kbn_url/" + $(this).val(), function(data) {

		  $("#goodsKbn_" + current_line_no).html(data);

		   //他の値の初期化
		   $("#goods_nm" + current_line_no).val("");
		   $("#goods_id" + current_line_no).val("");
		   $("#num" + current_line_no).val("1");
	       $("#unit_cost" + current_line_no).text("0");
	       $("#cost" + current_line_no).text("0");

		   recalculate();
		   $("#goods_ctg_indicator").css("display","none");
		});
	});

    //商品区分が選択されたら属する商品リストを表示する
	$(".goods_kbn").change(function() {
	      updateGoodsKbn($(this));
	});

	function updateGoodsKbn(e)
	{
	   $(this).simpleLoading('show');

	   //商品区分selectタグのサイズ調整
	   $("#hidden").text($(e).children(':selected').text());
       $(e).css("width",$("#hidden").width()+7);

	   //現在の処理中の行番号を保存
	   current_line_no = getGoodsKbnNo($(e).attr('name'));
	   $("#current_processing_line_no").val(current_line_no);

	   /* 商品リストフォームデータの取得 */
	   $.get("$goodsFormUrl"+"/" +  $("#goodsCtg_"+current_line_no).val() + "/" +$("#goodsKbn_"+current_line_no).val() + "/" + current_line_no,function(data){

	       $(this).simpleLoading('hide');
	       $("body").append(data);
	   });
	}

	//行追加
	$("#add_row").click(function(){
	   //ヘッダ分のTRがあるのでカウンター+1になる
	   var cloned = $(".list tr:nth-child("+(table_counter+1)+")");

	   $(cloned).clone(true).insertAfter(cloned);
	   table_counter++;

	   var new_row = $(".list tr:nth-child("+(table_counter+1)+")");
	   var new_td1 = $("td:nth-child(1)" ,new_row);
	   var new_td2 = $("td:nth-child(2)" ,new_row);
	   var new_td3 = $("td:nth-child(3)" ,new_row);
	   var new_td4 = $("td:nth-child(4)" ,new_row);
	   var new_td5 = $("td:nth-child(5)" ,new_row);
	   var new_td6 = $("td:nth-child(6)" ,new_row);
	   var new_td7 = $("td:nth-child(7)" ,new_row);

	   //新規作成した行のIDを設定
	   $(new_row).attr("id","row"+table_counter);
	   //削除ボタンの属性[name]を設定
	   $("a",new_td1).attr("name","row"+table_counter);
	   $("input",new_td1).attr("name", "data[SetGoodsMst][" + table_counter + "][id]");
	   $("input",new_td1).val(0);

	   //商品分類の属性[name]を設定
	   $("select",new_td2).attr("name","goodsCtg_" + table_counter);
	   $("select",new_td2).attr("id","goodsCtg_" + table_counter);
	   $("select option:nth-child(1)",new_td2).attr("selected",true);

	   //商品区分のタグを設定(selectタグごと切り替えないと後でoptionを追加しようとしても上手く追加できない)
	   $(new_td3).html( "<select class='goods_kbn' id='goodsKbn_" + table_counter + "' name='goodsKbn_" + table_counter + "'><option></option></select>");
	      //動的にタグを作成したらイベントを追加しないと動作しない
	     $("select",new_td3).bind("change",function(){
		                                             updateGoodsKbn($("select",new_td3));
		                                             });

       //商品の属性を設定
	    $("input:nth-child(1)",new_td4).attr("name", "data[SetGoodsMst][" + table_counter + "][goods_id]");
	    $("input:nth-child(1)",new_td4).attr("id", "goods_id" + table_counter);
	    $("input:nth-child(1)",new_td4).val("");
        $("textarea",new_td4).attr("id", "goods_nm" + table_counter);
        $("textarea",new_td4).attr("rows","1");
        $("textarea",new_td4).val("");

	   //数量の属性[name]を設定
	   $("select",new_td5).attr("id","num" + table_counter);
	   $("select",new_td5).attr("name","data[SetGoodsMst][" + table_counter + "][num]");
	   $("select",new_td5).val(1);

	   //原価の属性[id]を設定
	   $(new_td6).attr("id"  ,"unit_cost" + table_counter);
	   $(new_td6).text("0");

	   //総原価の属性[id]を設定
	   $(new_td7).attr("id","cost" + table_counter);
	   $(new_td7).text("0");

	   return false;
	});

    //行削除
    $(".delete").click(function(){
	    //1行は常に残す
        if(table_counter > 1){

        //name属性が"row"+番号になっているので"row"を除いた文字列を取得する
        var removed_line_no = $(this).attr("name").substr(3);

	       $("#"+$(this).attr("name")).remove();
	       table_counter--;
	       ReorderTable(removed_line_no);
	    }
	});

    //テーブル行の入れ替え
    $('#invoice_table').tableDnD({
        onDrop: function(table, row) {
            ReorderTable(1);
        }
    });

    //テーブル行の再設定
    function ReorderTable(removed_line_no)
    {
      //各行のIDを再設定する
      for(i=removed_line_no;i <= table_counter;i++){

       var td = $("#invoice_table tr:nth-child("+(parseInt(i)+parseInt(1))+")");
	   var td1 = $("td:nth-child(1)",td);
	   var td2 = $("td:nth-child(2)",td);
	   var td3 = $("td:nth-child(3)",td);
	   var td4 = $("td:nth-child(4)",td);
	   var td5 = $("td:nth-child(5)",td);
	   var td6 = $("td:nth-child(6)",td);
	   var td7 = $("td:nth-child(7)",td);

	    //TRのID設定
	    $(td).attr("id","row"+i);

	    //削除ボタンの属性[name]を設定
	    $("a",td1).attr("name","row"+i);

	    //商品分類の属性[name]を設定
	    $("select",td2).attr({name:'goodsCtg_' + i,
	                         id  :'goodsCtg_' + i
	                        });
	    //商品区分の属性[name]を設定
	    $("select",td3).attr({name :'goodsKbn_' + i,
	                          id   :'goodsKbn_' + i
	                         });
	    //商品の属性[name]を設定
	    $("input:nth-child(1)" ,td4).attr("name","data[SetGoodsMst][" + i + "][goods_id]");
	    $("input:nth-child(1)" ,td4).attr("id","goods_id" + i);
	    $("textarea" ,td4).attr("id","goods_nm" + i);

        //数量の属性[id][name]を設定
        $("select",td5).attr({name : 'data[SetGoodsMst][' + i + '][num]',
                              id   : 'num' + i
	                         });

	    //原価の属性[id]を設定
        $(td6).attr("id" ,"unit_cost" + i);

	    //総原価の属性[id]を設定
	    $(td7).attr("id","cost" + i);
	  }
	       recalculate();
    }

	//数量変更
	$(".num").change(function()
	{
	  current_line_no = getGoodsNo($(this).attr('name'));
	  var total_cost = Common.removeDollarComma($("#unit_cost" + current_line_no).text()) * $(this).val();
	  $("#cost" + current_line_no).text(Common.addDollarComma(total_cost));
	  recalculate();
	});

	//テキストエリアの大きさの自動調整
	$("textarea").each(function(){
      resizeTextarea($(this));
	});

	//商品ﾃｷｽﾄエリアの高さ調整
	function resizeTextarea(e){
	  var lines = $(e).val().split("\\n").length;
      if(lines > 5){
         $(e).attr("rows", 5);
      }
      else{
         $(e).attr("rows", lines);
      }
    }

    //ajaxの時の待ち画像
	$("#goods_ctg_indicator").css("display","none");
	$("#goods_kbn_indicator").css("display","none");
	$("#goods_indicator").css("display","none");

	//テーブル行の入れ替え
    $('#invoice_table').tableDnD({
        onDrop: function(table, row) {
            ReorderTable(1);
        }
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
                     }else{
                        $(":input[name='update']").attr('disabled',true);
                        $(":input[name='delete']").attr('disabled',true);
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
	     case "ADD":
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

	   $.post("$edit_set_goods_url",formData , function(result) {

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
		      $("#set_goods_id").val(obj.newId);
		      $("#set_goods_cd").text(obj.newSetGoodsCd);
		      $("#hidden_set_goods_cd").val(obj.newSetGoodsCd);
		      $("#set_revision").text(obj.newSetRevision);
		      $("#hidden_set_revision").val(obj.newSetRevision);

		      $("#result_message img").attr('src',"$confirm_image_path");
		      $("#result_dialog").data("status","true");
		      $("#goods_code").text(obj.code == null ?  $("#goods_code").text() : obj.code);
		  }else{
		      $("#result_message img").attr('src',"$error_image_path");
		      $("#result_dialog").data("status","false");
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
	}

    //商品価格合計計算
    recalculate();
});

/*  構成商品の合計原価の再計算
-------------------------------------------------------*/
function recalculate(){

     var total_cost = 0;
     var counter = 1;
     /* 商品構成テーブルの商品データ行のみを検索して合計原価を計算する */
     $("#invoice_table tr.data").each(function(){
         total_cost += Common.removeDollarComma($("#cost" + counter).text());
         counter++;
     });

     $("#total_cost").text(total_cost);
     $("#set_goods_cost").val(total_cost);
}
JSPROG
)) ?>

    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content" method="post" action="" >

        <input type="hidden" id='set_goods_id' name="data[GoodsMst][id]" value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_id'] ?>" />
		<table class="form" cellspacing="0">
	  	  <tr>
             <th>商品コード</th>
             <td id="goods_code">
                 <span id="set_goods_cd"><?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_cd'] ?></span>
                 <input type="hidden" id='hidden_set_goods_cd' name="data[GoodsMst][goods_cd]" value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_cd'] ?>" />
             </td>
          </tr>
          <tr>
             <th>リビジョンNO</th>
             <td><spna id='set_revision'><?php echo $goods[0]['LatestSetGoodsMstView']['set_revision'] ?></span>
                 <input type="hidden" id='hidden_set_revision' name="data[GoodsMst][revision]" value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_revision'] ?>" />
             </td>
          </tr>
          <tr>
             <th>有効期限</th>
             <td>
                 <?php
                    if($goods[0]['LatestSetGoodsMstView']['start_valid_dt'] == "1000-01-01"){
                      echo "<input type='text' name='data[GoodsMst][start_valid_dt]' id='start_valid_dt' class='inputdate' style='text-align:center' value='' />";
                    }else{
                      echo "<input type='text' name='data[GoodsMst][start_valid_dt]' id='start_valid_dt' class='inputdate' style='text-align:center' value='{$goods[0]['LatestSetGoodsMstView']['start_valid_dt']}' />";
                    }
                 ?>
                 <span>～</span>
                 <?php
                    if($goods[0]['LatestSetGoodsMstView']['end_valid_dt'] == "9999-12-31"){
                      echo "<input type='text' name='data[GoodsMst][end_valid_dt]' id='end_valid_dt' class='inputdate' style='text-align:center' value='' />";
                    }else{
                      echo "<input type='text' name='data[GoodsMst][end_valid_dt]' id='end_valid_dt' class='inputdate' style='text-align:center' value='{$goods[0]['LatestSetGoodsMstView']['end_valid_dt']}' />";
                    }
                 ?>
              </td>
          </tr>
           <tr>
             <th>商品分類</th>
            <td><input type="hidden" name="data[GoodsMst][goods_ctg_id]"  value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_ctg_id'] ?>" /><?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_ctg_nm'] ?></td>
          </tr>
           <tr>
             <th>商品区分</th>
             <td><input type="hidden" name="data[GoodsMst][goods_kbn_id]"  value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_kbn_id'] ?>" /><?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_kbn_nm'] ?></td>
          </tr>
          <tr>
             <th>国内払い</th>
             <?php
               if($goods[0]['LatestSetGoodsMstView']['set_internal_pay_flg'] == 0){
                    echo "<td><input type='checkbox' id='internal_pay_flg' name='data[GoodsMst][internal_pay_flg]' /></td>";
               }else{
                 	echo "<td><input type='checkbox' id='internal_pay_flg' name='data[GoodsMst][internal_pay_flg]' checked /></td>";
               }
             ?>
          </tr>
           <tr>
             <th>支払区分</th>
             <td>
                 <select id="payment_kbn_list" name="data[GoodsMst][payment_kbn_id]">
   			        <?php
   			           for($i=0;$i < count($payment_kbn_list);$i++){

   			             $atr = $payment_kbn_list[$i]['PaymentKbnMst'];
   			             if($goods[0]['LatestSetGoodsMstView']['set_payment_kbn_id'] == $atr['id']){
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
             <th>商品名<span class="necessary">(必須)</span></th>
              <td><textarea name="data[GoodsMst][goods_nm]" id="goods_nm" class="validate[required,maxSize[500]] inputcomment" rows="3"><?php echo $goods[0]['LatestSetGoodsMstView']['set_goods_nm'] ?></textarea></td>
          </tr>
          <tr>
             <th>通貨区分</th>
             <td>
              <select name="data[GoodsMst][currency_kbn]">
               <?php
                 if($goods[0]['LatestSetGoodsMstView']['set_currency_kbn'] == 0)
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
             <th>価格<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][price]" id="set_goods_price" class="validate[required,custom[number],max[10000000]] inputnumeric"  value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_price'] ?>" /></td>
          </tr>
          <tr>
             <th>原価</th>
             <td><input type="text" name="data[GoodsMst][cost]" id="set_goods_cost"  class="inputdisable inputnumeric" value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_cost'] ?>" readonly /></td>
          </tr>
          <tr>
             <th>HIシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][aw_share]" id="aw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[rw_share]] inputnumeric"
                        value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_aw_share'] * 100 ?>" /></td>
          </tr>
          <tr>
             <th>RWシェア(%)<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsMst][rw_share]" id="rw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[aw_share]] inputnumeric"
                        value="<?php echo $goods[0]['LatestSetGoodsMstView']['set_rw_share'] * 100 ?>" /></td>
          </tr>
          <tr>
             <th>構成商品</th>
             <td></td>
          </tr>
          <tr>
             <th><input type="submit"  id="add_row"  class="inputbutton"  value=" 項目追加 " /></th>
          </tr>
	    </table>

<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
<table id="invoice_table" class="list" cellspacing="0">

    <tr class="nodrag nodrop">
	    <th>削除</th>
	    <th>商品分類&nbsp;&nbsp;<?php echo $html->image('loading.gif',array('id'=>'goods_ctg_indicator')); ?></th>
	    <th>商品区分&nbsp;&nbsp;<?php echo $html->image('loading.gif',array('id'=>'goods_kbn_indicator')); ?></th>
	    <th>商品名&nbsp;&nbsp;<?php echo $html->image('loading.gif',array('id'=>'goods_indicator')); ?></th>
	    <th>数量</th>
        <th class="dollar">原価<?php echo $html->image('dollar.png')?></th>
        <th class="dollar">総原価<?php echo $html->image('dollar.png')?></th>
    </tr>
<?php
   for($i=0;$i < count($goods);$i++)
   {
	echo "<tr id='row".($i+1)."'  class='data'>".
	    "<!--  削除ボタン -->".
	    "<td><a href='#'  class='delete' name='row".($i+1)."'>削除</a>".
	        "<input type='hidden' id='id_".($i+1)."' name='data[SetGoodsMst][".($i+1)."][id]' value='{$goods[$i]['LatestSetGoodsMstView']['id']}' />".
	    "</td>";
     echo "<!--  商品分類 -->".
        "<td>".
         "<select id='goodsCtg_".($i+1)."' class='goods_ctg' name='goodsCtg_".($i+1)."'>".
           "<option value=''></option>";
                       for($k=0;$k < count($goods_ctg_list);$k++)
   			           {
   			             $atr = $goods_ctg_list[$k]['GoodsCtgMst'];
   			             if($atr['id'] == $goods[$i]['LatestSetGoodsMstView']['goods_ctg_id'])
   			             {
   			               	echo "<option value='{$atr['id']}' selected='selected'>{$atr['goods_ctg_nm']}</option>";
   			             }
   			             else
			           {
			           	    echo "<option value='{$atr['id']}'>{$atr['goods_ctg_nm']}</option>";
			           	 }
   			           }
      echo   "</select>".
         "</td>".

         "<!--  商品区分 -->".
        "<td>".
         "<select id='goodsKbn_".($i+1)."' class='goods_kbn' name='goodsKbn_".($i+1)."'>".
           "<option value='{$goods[$i]['LatestSetGoodsMstView']['goods_kbn_id']}'>{$goods[$i]['LatestSetGoodsMstView']['goods_kbn_nm']}</option>".
         "</select>".
        "</td>".

        "<!--  商品名 -->".
         "<td>".
             "<input    type='hidden' class='goods'  id='goods_id".($i+1)."' name='data[SetGoodsMst][".($i+1)."][goods_id]' value='{$goods[$i]['LatestSetGoodsMstView']['goods_id']}' />".
             "<textarea  class='small-inputcomment goods_nm' id='goods_nm".($i+1)."' wrap='off' readonly >{$goods[$i]['LatestSetGoodsMstView']['goods_nm']}</textarea>".
        "</td>".

        "<!--  数量 -->".
        "<td>".
         "<select id='num".($i+1)."' class='num' name='data[SetGoodsMst][".($i+1)."][num]'>";
	         for($j=1;$j < 100;$j++)
             {
               if($goods[$i]['LatestSetGoodsMstView']['num']==$j)
               {
              	echo "<option value='$j' selected='selected'>$j</option>";
               }
               else
               {
              	echo "<option value='$j'>$j</option>";
               }
             }
  echo  "</select>".
        "</td>";

 echo   "<!-- 原価 -->".
            "<td id='unit_cost".($i+1)."' class='dollar'>".$goods[$i]['LatestSetGoodsMstView']['cost']."</td>".
         "<!-- 総原価 -->".
            "<td id='cost".($i+1)."' class='dollar'>".($goods[$i]['LatestSetGoodsMstView']['cost'] * $goods[$i]['LatestSetGoodsMstView']['num'])."</td>".
    "</tr>";
  }
?>
        <tr class="nodrag nodrop">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>

            <td align="right">TOTAL</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td class="dollar" id="total_cost" >&nbsp;</td>
        </tr>
</table>
</div>

	<div class="submit">
	    <input type="submit"  class="inputbutton"  value=" 複写登録   "   name="add" />
        <input type='submit'  class="inputbutton"  value=" 更新 "        name='update'/>
        <input type="submit"  class="inputbutton"  value=" 削除 "        name="delete" />
         <!-- 商品区分選択人の文字列幅取得用 -->
        <span id="hidden" style="display:none;"></span>
	</div>
</form>

<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
