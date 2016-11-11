<?php
$edit_estimate_url = $html->url('editEstimate');
$tmp_url = $html->url('updateSetGoodsEstimate');
$confirm_image_path = $html->webroot("/images/confirm_result.png");
$error_image_path = $html->webroot("/images/error_result.png");

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
   $("input:submit").button();
   //$(".cost").mask("999,999,999.99");

   /* 選択された挙式年月の顧客の見積データを取得する */
   $("#wedding_dt").change(function(){

      $("#GoodsMstViewWeddingPlannedDt").val($(this).val());
      $("#EstimateDtlTrnViewIndexForm").submit();
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

	/* 更新開始
    ------------------------------------*/
	$("#formID").submit(function(){

		$(this).simpleLoading('show');
		var formData = $("#formID").serialize();
//alert( formData);
		$.post("$edit_estimate_url",formData , function(result) {

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

   //価格及び原価がオリジナルと違う時は表示
	$("#formID .cost").each(CostChanged);
	$("#formID .cost").change(CostChanged);

	//var customer_count = -4;
	//$("#formID th").each(function(){ customer_count++; });

	//$("#formID tr").each(function(){
    //   alert($("td:nth-child(4) input:nth-child(1)",this).val());
   // });


   $("#indicator").css("display","none");

   //[開発用]構成商品価格の更新
   $("#update_set_goods_link").click(function(){

      $(this).simpleLoading('show');
      $.get("$tmp_url",function(data){
		$(this).simpleLoading('hide');
		alert(data);
	   });
   });

   $("td").css("height","25px");

   $("#data").scroll(function(){
        $("#move_col").scrollLeft($(this).scrollLeft());
		$("#move_row").scrollTop($(this).scrollTop());
   });

	$(window).resize(function(){
       ResizeTable();
    });
       ResizeTable();
});

/* 原価とオリジナル原価が違うフィールドをハイライトにする
-------------------------------------------------------------*/
function CostChanged(){

	 if(Common.removeComma($(this).val()) != Common.removeComma($(this).next().val())){
	       $(this).removeClass("focusField");
	       $(this).addClass("changedField");
	 }else{
		   $(this).removeClass("focusField");
           $(this).removeClass("changedField");
     }
}

/* テーブルのサイズ変更
-------------------------------------------------------------*/
function ResizeTable(){

     var thCount = 0;
	$("#move_col table th").each(function(){ thCount++; });

	 $("#move_row").height($(window).height()-320);
     $("#move_row table").height($(window).height()-320);
     $("#data").height($(window).height()-300);

	 var tableWidth = 0;
	 // div > table ならtableの幅の方が小さいのでウィンドウの幅に合わせる
	 if(($(window).width()-720) >= thCount * 100){
         tableWidth = $(window).width()-720;
	 // div < tableならtableの幅の方が大きいので実際のテーブルの横幅に合わせる
     }else{
		 //ヘッダ数 * ヘッダ幅サイズ
         tableWidth = thCount * 100
     }

	 $("#move_col").width($(window).width()-720);
     $("#move_col table").width(tableWidth);
	 $("#data").width($(window).width()-700);
}
JSPROG
)) ?>

<ul class="operate">
   <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
  <!--  <li><a id="update_set_goods_link" href="#">[開発用]セット構成商品の価格更新</a></li> -->
</ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null); ?>
   <?php echo $form->text('GoodsMstView.wedding_planned_dt' ,array('value' => $wedding_dt)); ?>
   <?php echo $form->end(); ?>
 </div>

 <div class='notation'>
   <label>表示年月：</label>
   <select id='wedding_dt'>
		<?php
		  $found = false;
		  for($i=0;$i < count($wedding_dt_list);$i++)
          {
		           if($wedding_dt == $wedding_dt_list[$i]){
		         	 echo "<option value='".$wedding_dt_list[$i]."' selected>{$wedding_dt_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$wedding_dt_list[$i]."'>{$wedding_dt_list[$i]}</option>";
		           }
		   }
		   /* 初期表示はログイン年月を基にするがログイン年月に挙式がない場合はもあるので、その場合は項目のみ作成しておく  */
		   if($found==false && $wedding_dt != -1){
		     	 echo "<option value='".$wedding_dt."' selected>{$wedding_dt}</option>";
		   }
	    ?>
   </select>
 </div>

 <form id="formID" class="content" method="post"  action="" style="margin-bottom:30px;">
   <input type="submit"  value="更新" name="update"  style="margin-bottom:10px;" />

<div id="main" style="position:relative;width: 100%;height:100%;">

<!-- 固定ヘッダ -->
 <div id="fix_col" style="position:absolute;left:0px;top:0px">
   <table class="list" cellspacing="0">
   <tr>
      <th style='text-align:center;width:200px;'>カテゴリ名</th>
      <th style='text-align:center;width:200px;'>ベンダー名</th>
      <th style='text-align:center;width:200px;'>商品名</th>
   </tr></table>
 </div>

 <!-- 変動ヘッダ -->
 <div id="move_col" style="position:absolute;left:646px;top:0px;width:200px;overflow:hidden">
   <table class="list" cellspacing="0" style="width:410px;">
   <tr>
   <?php
      /* ヘッダ作成  */
      $estimate_id = -1;
      $customer_pos = array();

      for($i=0;$i < count($data);$i++){
      	/* 重複して顧客が取得されているのでユニークな顧客名のみを抽出してヘッダとする */
      	if(in_array($data[$i]["EstimateDtlTrnView"]['customer_id'],$customer_pos)==false){
      	   echo "<th style='text-align:center;width:100px'><a href='".$html->url('/customersList/goToCustomerInfo/'.$data[$i]["EstimateDtlTrnView"]['customer_id'])."'>{$data[$i]["EstimateDtlTrnView"]['grmls_kj']}様 {$common->evalNbspForDayOnly($data[$i]["EstimateDtlTrnView"]['wedding_dt'])}</a></th>";
           $estimate_id = $data[$i]["EstimateDtlTrnView"]['estimate_id'];
           array_push($customer_pos,$data[$i]["EstimateDtlTrnView"]['customer_id']);
      	}
      }
     echo "<th style='text-align:left;width:100px'>合計</th>";
     ?>
   </tr></table>
 </div>

 <!-- 変動行 -->
 <div id="move_row" style="position:absolute;top:25px;left:0;overflow:hidden;">
  <table class="list" cellspacing="0">
   <?php
    /* コンテンツ作成 */
     $customer_sales = array();
     for($index=0;$index < count($customer_pos);$index++){
     	$customer_sales[$customer_pos[$index]] = 0;
     }

      $goods_ctg_id = -1;
      $goods_id = -1;
      $total_of_goods_price = 0;
      $counter = 0;
      for($i=0;$i < count($data_by_category);$i++){

      	if($data_by_category[$i]["EstimateDtlTrnView"]['payment_kbn_id'] == PC_INDIRECT_ABOARD_PAY){

          /* 商品カテゴリ、商品ID順にソートしてあるので商品IDが前回と変われば新規の行に移る */
      	  if($data_by_category[$i]["EstimateDtlTrnView"]['goods_id'] != $goods_id){

      	  /* 新しい商品カテゴリに入ったらカテゴリ名を設定する */
      	  if($data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_id'] != $goods_ctg_id){
      	    echo "<tr><td style='width:200px;'>{$data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_nm']}</td>";
      	    $goods_ctg_id = $data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_id'];
      	  }else{
      	   echo "<tr><td style='width:200px;'>&nbsp;</td>";
      	  }

      	  $goods_id = $data_by_category[$i]["EstimateDtlTrnView"]['goods_id'];
      	  echo "<td style='width:200px;'><div class='vendor'>{$data_by_category[$i]["EstimateDtlTrnView"]['vendor_nm']}</div></td>";
          echo "<td style='width:200px;'><div class='goods'> {$data_by_category[$i]["EstimateDtlTrnView"]['goods_nm']} </div></td>";
      	}
      }
     }

   /* フッター作成  */
   echo "<tr>".
            "<td style='width:200px;'>&nbsp;</td>".
            "<td style='width:200px;'>&nbsp;</td>".
            "<td style='width:200px;'>合計</td>";
   ?>
   </table>
 </div>

<!-- データ部 -->
  <div id="data" style="position:absolute;top:25px;left:646px;width:200px;overflow:scroll">
  <table class="list" cellspacing="0">
   <?php
    /* コンテンツ作成 */
     $customer_sales = array();
     for($index=0;$index < count($customer_pos);$index++){
     	$customer_sales[$customer_pos[$index]] = 0;
     }

      $goods_ctg_id = -1;
      $goods_id = -1;
      $total_of_goods_price = 0;
      $counter = 0;
      for($i=0;$i < count($data_by_category);$i++){

      	if($data_by_category[$i]["EstimateDtlTrnView"]['payment_kbn_id'] == PC_INDIRECT_ABOARD_PAY){

          /* 商品カテゴリ、商品ID順にソートしてあるので商品IDが前回と変われば新規の行に移る */
      	  if($data_by_category[$i]["EstimateDtlTrnView"]['goods_id'] != $goods_id){

      	  /* 新しい商品カテゴリに入ったらカテゴリ名を設定する */
      	  if($data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_id'] != $goods_ctg_id){
      	   // echo "<tr><td style='width:200px;'>{$data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_nm']}</td>";
      	    $goods_ctg_id = $data_by_category[$i]["EstimateDtlTrnView"]['goods_ctg_id'];
      	  }else{
      	  // echo "<tr><td style='width:200px;'>&nbsp;</td>";
      	  }

      	  $goods_id = $data_by_category[$i]["EstimateDtlTrnView"]['goods_id'];
      	  //echo "<td style='width:200px;'><div class='vendor'>{$data_by_category[$i]["EstimateDtlTrnView"]['vendor_nm']}</div></td>";
          //echo "<td style='width:300px;'><div class='goods'> {$data_by_category[$i]["EstimateDtlTrnView"]['goods_nm']} </div></td>";

          /* ヘッダの顧客名順に配列に顧客IDが設定されているので同一顧客同一商品があれば金額を設定する */
          for($j=0;$j < count($customer_pos);$j++){
             $found = false;
             /* 新たに注文明細データを調べる */
          	 for($sub_index=0;$sub_index < count($data_by_category);$sub_index++){

          	 	   if($customer_pos[$j] == $data_by_category[$sub_index]["EstimateDtlTrnView"]['customer_id'] &&
          	 	      $goods_id         == $data_by_category[$sub_index]["EstimateDtlTrnView"]['goods_id']){

          	 	      $estimate_dtl_id = $data_by_category[$sub_index]["EstimateDtlTrnView"]['estimate_dtl_id'];
          	 	   	  $set_estimate_dtl_id = $data_by_category[$sub_index]["EstimateDtlTrnView"]['set_estimate_dtl_id'];
                     // $sales_cost = number_format($data_by_category[$sub_index]["EstimateDtlTrnView"]['sales_cost']*$data_by_category[$sub_index]["EstimateDtlTrnView"]['num'],2);
                     // $original_sales_cost = number_format($data_by_category[$sub_index]["EstimateDtlTrnView"]['original_sales_cost']*$data_by_category[$sub_index]["EstimateDtlTrnView"]['num'],2);
                      $sales_cost = number_format($data_by_category[$sub_index]["EstimateDtlTrnView"]['total_sales_cost'],2);
                      $original_sales_cost = number_format($data_by_category[$sub_index]["EstimateDtlTrnView"]['total_original_sales_cost'],2);


                      if($data_by_category[$sub_index]["EstimateDtlTrnView"]['money_received_flg']==0){
                      	 echo "<td style='width:100px;'><input type='checkbox' class='chk' name='data[EstimateDtlTrn][".($counter)."][money_received_flg]' value='1' />";
                      }else{
                      	 echo "<td style='width:100px;'><input type='checkbox' class='chk' name='data[EstimateDtlTrn][".($counter)."][money_received_flg]' value='1' checked />";
                      }
        	          echo     "<input type='text'     name='data[EstimateDtlTrn][".($counter)."][sales_cost]' class='cost' style='text-align:right;width:60px' value='".$sales_cost."'/>".
        	                   "<input type='hidden'                                                                                               value='".$original_sales_cost."' />".
        	                   "<input type='hidden'   name='data[EstimateDtlTrn][".($counter)."][estimate_dtl_id]'                                value='".$estimate_dtl_id."' />".
        	                   "<input type='hidden'   name='data[EstimateDtlTrn][".($counter)."][set_estimate_dtl_id]'                            value='".$set_estimate_dtl_id."' />".
        	               "</td>";
        	          //顧客別の合計値を抽出するために金額を加算する
        	          //$customer_sales[$customer_pos[$j]] += $data_by_category[$sub_index]["EstimateDtlTrnView"]['sales_cost']*$data_by_category[$sub_index]["EstimateDtlTrnView"]['num'];
        	          //$total_of_goods_price += $data_by_category[$sub_index]["EstimateDtlTrnView"]['sales_cost']*$data_by_category[$sub_index]["EstimateDtlTrnView"]['num'];
                      $customer_sales[$customer_pos[$j]] += $data_by_category[$sub_index]["EstimateDtlTrnView"]['total_sales_cost'];
                      $total_of_goods_price += $data_by_category[$sub_index]["EstimateDtlTrnView"]['total_sales_cost'];

                      $found = true;
        	          $counter++;
        	          break;
        	      }
             }
             /* 同一顧客同一商品がなかった */
             if($found ==false){
             	 echo "<td style='width:100px;'>&nbsp;</td>";
             }
          }
            echo "<td align='right' style='width:100px;'>".number_format($total_of_goods_price,2)."</td></tr>";
            $total_of_goods_price = 0;
      	}
      }
     }

   /* フッター作成  */
   echo "<tr>";
          $total = 0;
          for($j=0;$j < count($customer_pos);$j++){
        	echo "<td align='right'>".number_format($customer_sales[$customer_pos[$j]],2)."</td>";
        	$total += $customer_sales[$customer_pos[$j]];
        }
   echo "<td align='right'>".number_format($total,2)."</td></tr>";
   ?>
   </table>
 </div>
</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>