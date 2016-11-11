<?php
$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

   $("#start_date").mask("9999-99");
   $("#end_date").mask("9999-99");
   $("input:submit").button();

   $("#wedding_dt").change(function(){

      $("#GoodsMstViewWeddingPlannedDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });

    $("#search_button").click(function(){

      if($("#start_date").val() == "" || $("#end_date").val() == ""){
         alert("年月を指定して下さい。");
         return;
      }

      $("#GoodsMstViewStartWeddingPlannedDt").val($("#start_date").val());
      $("#GoodsMstViewEndWeddingPlannedDt").val($("#end_date").val());
      $("#EnvMstIndexForm").submit();
   });

   $("#indicator").css("display","none");

    // 表示形式の選択
	$("#country_selector [name='view']").click(function(){
        ChangeDisplayStyle($(this).val());
	});
	    ChangeDisplayStyle("yen");
});

/* 表示形式の選択  [display='']は初期値に戻すという意味なのでブラウザ毎の表示差異を吸収できる
-------------------------------------------------------------------------*/
function ChangeDisplayStyle(type){

	switch(type.toUpperCase()){
      case "YEN": $("#country_japan").css("display","");
	              $("#country_us").css("display","none");
		          break;
	  case "DOLLAR": $("#country_us").css("display","");
	                 $("#country_japan").css("display","none");
		             break;
      default: $("#country_japan").css("display","");
	           $("#country_us").css("display","none");
    }
}

JSPROG
)) ?>

<ul class="operate">
  <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
</ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null);
         echo $form->text('GoodsMstView.start_wedding_planned_dt' ,array('value' => $start_wedding_dt));
         echo $form->text('GoodsMstView.end_wedding_planned_dt'   ,array('value' => $end_wedding_dt));
         echo $form->end(); ?>
 </div>

 <div class='notation'>
   <label>表示年月：</label>
   <input type="text" id="start_date" name="start_date" class="inputdate" value="<?php echo $start_wedding_dt ?>" />
   <span>～</span>
   <input type="text" id="end_date"   name="end_date" class="inputdate" value="<?php echo $end_wedding_dt ?>" />
   <input type='submit' value='検索'  class='inputbutton' id='search_button'  style="margin-left:3px;" />

   <!--
   <div id="country_selector" style="display:inline;margin-left:10px;">
    <label>【邦貨】</label><input type="radio" name='view' value="yen" checked/>
    <label>【外貨】</label><input type="radio" name='view' value="dollar" />
   </div>
    -->
 </div>

<table class="list" cellspacing="0">

	<tr>
	    <th>No</th>
	    <th>商品分類</th>
	    <th>ベンダー</th>
	    <th style="text-align:right">顧客数</th>
	    <th style="text-align:right">数量合計</th>
	    <th style="text-align:right">売価計<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">原価計<?php echo $html->image('yen.png')?></th>
	</tr>

	<?php
    //print_r($detail);

	$sum_sales_num = 0;
	$sum_sales_customer= 0;
	$sum_sales = 0;
	$sum_cost = 0;

    for($i=0;$i < count($header);$i++){

      $atr = $header[$i][0];
	  echo "<tr>".
	           "<td nowrap>".($i+1)."</td>".
	           "<td nowrap>{$atr['goods_ctg_nm']}</td>".
	           "<td nowrap>{$atr['vendor_nm']}</td>".
	           "<td class='yen'>".number_format($atr['customer_num'])."</td>".
	           "<td class='yen'>".number_format($atr['sales_num'])   ."</td>".
	           "<td class='yen'>".number_format($atr['sales_price']) ."</td>".
	           "<td class='yen'>".number_format($atr['sales_cost'])  ."</td>".
            "</tr>";

    $sum_sales_num += $atr['sales_num'];
	$sum_sales_customer += $atr['customer_num'];
	$sum_sales += $atr['sales_price'];
	$sum_cost += $atr['sales_cost'];
    }
     echo "<tr>".
              "<td>&nbsp;</td><td>&nbsp;</td><td>合計</td>".
              "<td class='yen'>".number_format($sum_sales_customer)."</td>".
              "<td class='yen'>".number_format($sum_sales_num)."</td>".
              "<td class='yen'>".number_format($sum_sales)."</td>".
              "<td class='yen'>".number_format($sum_cost)."</td>".
          "</tr>";
?>

 </table>

