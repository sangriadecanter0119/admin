<?php
$index_url = $html->url('index');

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

   $("#start_date").mask("9999-99");
   $("#end_date").mask("9999-99");

   $(".balloon").hide();
   $("#hi_header_jp").hover(function(){ $("#hi_header_balloon_jp").fadeIn();},
                            function(){ $("#hi_header_balloon_jp").fadeOut();});
   $("#hi_header_en").hover(function(){ $("#hi_header_balloon_en").fadeIn();},
                            function(){ $("#hi_header_balloon_en").fadeOut();});

   $("#wedding_dt").change(function(){

      $("#GoodsMstViewWeddingPlannedDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });

    $("#search_button").click(function(){

      $("#GoodsMstViewStartWeddingPlannedDt").val($("#start_date").val());
      $("#GoodsMstViewEndWeddingPlannedDt").val($("#end_date").val());
      $("#CustomerMstIndexForm").submit();
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
   <input id="search_button" type="image"  src="<?php echo $html->webroot("/images/search.png"); ?>"  style="margin-left:3px;" />

   <!--
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
		   if($found==false){
		     	 echo "<option value='".$wedding_dt."' selected>{$wedding_dt}</option>";
		   }
	    ?>
   </select>
   -->

   <div id="country_selector" style="display:inline;margin-left:10px;">
    <label>【邦貨】</label><input type="radio" name='view' value="yen" checked/>
    <label>【外貨】</label><input type="radio" name='view' value="dollar" />
   </div>
</div>
<!--
<table class="content">
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle"><?php echo $html->image('dollar.png')?>総合計</legend>

	  <table class="viewheader">
	    <tr>
   <?php
	    $foreign_total = 0;
	    $foreign_hawaii_tax = 0;
	    $foreign_service_fee = 0;
	    $foreign_hi_total = 0;
	    $foreign_rw_sub_total = 0;
	    $foreign_rw_total = 0;
	    $foreign_gross_profit_rate = 0;
        for($i=0;$i < count($data);$i++)
        {
          $foreign_total        += $data[$i]['foreign_total'];
	      $foreign_hawaii_tax   += $data[$i]['foreign_hawaii_tax'];
	      $foreign_service_fee  += $data[$i]['foreign_service_fee'];
	      $foreign_hi_total     += $data[$i]['foreign_hi_total'];
	      $foreign_rw_sub_total += $data[$i]['foreign_rw_sub_total'];
	      $foreign_rw_total     += $data[$i]['foreign_rw_total'];
        }
        echo "<th>総合計：</th><td class='long''>".number_format($foreign_total,2)."</td>".
	         "<th>州税総合計：</th> <td class='long'>".number_format($foreign_hawaii_tax,2)."</td>".
	         "<th>アレンジメントフィー総合計：</th>     <td class='long'>".number_format($foreign_service_fee,2)."</td>".
	         "<th>HI手数料総合計：</th>  <td class='long'>".number_format($foreign_hi_total,2)."</td>".
             "<th>RW取り分サブ総合計：</th>  <td class='long'>".number_format($foreign_rw_sub_total,2)."</td>".
             "<th>RW取り分総合計：</th>  <td class='long'>".number_format($foreign_rw_total,2)."</td>".
             "<th>粗利率(RW取り分総合計/総合計)：</th>  <td class='long'>".number_format(($foreign_rw_total/$foreign_total)*100,2)."%</td>";
  ?>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle"><?php echo $html->image('yen.png')?>総合計</legend>

	  <table class="viewheader">
	    <tr>
   <?php
	    $total = 0;
	    $service_fee = 0;

        for($i=0;$i < count($data);$i++){
          $total        += $data[$i]['total'];
	      $service_fee  += $data[$i]['service_fee'];
        }
        echo "<th style='width:50px;'>総合計：</th><td class='long' style='width:120px;'>".number_format($total)."</td>".
	         "<th style='width:50px;'>アレンジメントフィー総合計：</th><td class='long'>".number_format($service_fee)."</td>".
	         "<th></th><td>&nbsp</td>".
	         "<th></th><td>&nbsp</td>".
	         "<th></th><td>&nbsp</td>".
	         "<th></th><td>&nbsp</td>".
	         "<th></th><td>&nbsp</td>";
  ?>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
</table>
-->
 <div style='position:relative;'>
    <div id='hi_header_balloon_en' class='balloon' style='position:absolute;top:-40px;left:550px;'><div>HI負担分の割引料を加味しない金額</div></div>
	<table id="country_us" class="list" cellspacing="0">
	<tr>
	    <th>No</th>
	    <th>挙式日</th>
	    <th>顧客名</th>
	    <th style="text-align:right">全体額<?php echo $html->image('dollar.png')?></th>
	    <th style="text-align:right">クレジット払い額<?php echo $html->image('dollar.png')?></th>
	    <th style="text-align:right">アレンジメントフィー<?php echo $html->image('dollar.png')?></th>
	    <th id="hi_header_en" style="text-align:right">HI取り分<?php echo $html->image('dollar.png')?></th>
  	    <th style="text-align:right">RW取り分<?php echo $html->image('dollar.png')?></th>
	    <th style="text-align:right">税金<?php echo $html->image('dollar.png')?></th>
	    <th style="text-align:right">送金税金<?php echo $html->image('dollar.png')?></th>
	    <th style="text-align:right">RW割引<?php echo $html->image('dollar.png')?></th>
	    <th style="text-align:right">全体割引<?php echo $html->image('dollar.png')?></th>
        <th style="text-align:right">RW Total<?php echo $html->image('dollar.png')?></th>
        <th style="text-align:right">RW Total / 売上<?php echo $html->image('dollar.png')?></th>
        <th style="text-align:right">粗利 Total<?php echo $html->image('dollar.png')?></th>
        <th style="text-align:right">粗利 Total / 売上<?php echo $html->image('dollar.png')?></th>
        <th></th>
        <th></th>
        <th style="text-align:right">販売為替</th>
        <th style="text-align:right">原価為替</th>
	</tr>
<?php

	$sum_foreign_total = 0;
	$sum_foreign_service_fee= 0;
	$sum_foreign_hi_total= 0;
	$sum_foreign_rw_total= 0;
	$sum_foreign_hawaii_tax= 0;
	$sum_foreign_remittance_hawaii_tax= 0;
	$sum_foreign_rw_discount= 0;
	$sum_foreign_total_discount= 0;
	$sum_foreign_rw= 0;
	$sum_foreign_gross_total= 0;
	$sum_foreign_credit= 0;

    for($i=0;$i < count($data);$i++){

	  echo "<tr>".
	           "<td nowrap>".($i+1)."</td>".
	           "<td nowrap>{$common->evalForShortDate($data[$i]['wedding_dt'])}</td>".
	           "<td nowrap><a href='".$html->url('/customersList/goToCustomerInfo/'.$data[$i]['customer_id'])."'>{$data[$i]['grmls_kj']}{$data[$i]['grmfs_kj']}</a></td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_total'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_credit_domestic_pay_amount']+$data[$i]['foreign_credit_aboard_pay_amount'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_service_fee'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_hi_total'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_rw_total'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_hawaii_tax'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_remittance_hawaii_tax'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_rw_discount'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_total_discount'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_rw_sum'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_rw_total_rate'],2)."%</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_gross_total'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['foreign_gross_total_rate'],2)."%</td>".
	           "<td>&nbsp;</td>".
	           "<td>&nbsp;</td>".
	           "<td class='dollar'>".number_format($data[$i]['sales_rate'],2)."</td>".
	           "<td class='dollar'>".number_format($data[$i]['cost_rate'],2)."</td>".
            "</tr>";

    $sum_foreign_total += $data[$i]['foreign_total'];
    $sum_foreign_credit += $data[$i]['foreign_credit_domestic_pay_amount']+$data[$i]['foreign_credit_aboard_pay_amount'];
    $sum_foreign_service_fee += $data[$i]['foreign_service_fee'];
    $sum_foreign_hi_total += $data[$i]['foreign_hi_total'];
    $sum_foreign_rw_total += $data[$i]['foreign_rw_total'];
    $sum_foreign_hawaii_tax += $data[$i]['foreign_hawaii_tax'];
    $sum_foreign_remittance_hawaii_tax += $data[$i]['foreign_remittance_hawaii_tax'];
    $sum_foreign_rw_discount += $data[$i]['foreign_rw_discount'];
    $sum_foreign_total_discount += $data[$i]['foreign_total_discount'];
    $sum_foreign_rw += $data[$i]['foreign_rw_sum'];
    $sum_foreign_gross_total += $data[$i]['foreign_gross_total'];
    }
     echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>合計</td>".
          "<td class='dollar'>".number_format($sum_foreign_total,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_credit,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_service_fee,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_hi_total,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_rw_total,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_hawaii_tax,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_remittance_hawaii_tax,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_rw_discount,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_total_discount,2)."</td>".
          "<td class='dollar'>".number_format($sum_foreign_rw,2)."</td>".
          "<td class='dollar'>".($sum_foreign_total==0 ? 0 : number_format(($sum_foreign_rw/$sum_foreign_total)*100,2))."%</td>".
          "<td class='dollar'>".number_format($sum_foreign_gross_total,2)."</td>".
          "<td class='dollar'>".($sum_foreign_total==0 ? 0 : number_format(($sum_foreign_gross_total/$sum_foreign_total)*100,2))."%</td>".
          "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
          "</tr>";

     $c = count($data);
      echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>平均</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_total/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_credit/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_service_fee/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_hi_total/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_rw_total/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_hawaii_tax/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_remittance_hawaii_tax/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_rw_discount/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_total_discount/$c,2))."</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_rw/$c,2))."</td>".
          "<td class='dollar'>".($sum_foreign_total==0 ? 0 : number_format(($sum_foreign_rw/$sum_foreign_total)*100,2))."%</td>".
          "<td class='dollar'>".($c==0 ? 0 : number_format($sum_foreign_gross_total/$c,2))."</td>".
          "<td class='dollar'>".($sum_foreign_total==0 ? 0 : number_format(($sum_foreign_gross_total/$sum_foreign_total)*100,2))."%</td>".
          "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
          "</tr>";
?>
    </table>
 </div>

 <div style='position:relative;'>
    <div id='hi_header_balloon_jp' class='balloon' style='position:absolute;top:-40px;left:550px;'><div>HI負担分の割引料を加味しない金額</div></div>
    <table id="country_japan" class="list" cellspacing="0">
	<tr>
	    <th>No</th>
	    <th>挙式日</th>
	    <th>顧客名</th>
	    <th style="text-align:right">全体額<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">クレジット払い額<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">アレンジメントフィー<?php echo $html->image('yen.png')?></th>
	    <th id='hi_header_jp' style="text-align:right">HI取り分<?php echo $html->image('yen.png')?></th>
  	    <th style="text-align:right">RW取り分<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">税金<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">送金税金<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">RW割引<?php echo $html->image('yen.png')?></th>
	    <th style="text-align:right">全体割引<?php echo $html->image('yen.png')?></th>
        <th style="text-align:right">RW Total<?php echo $html->image('yen.png')?></th>
        <th style="text-align:right">RW Total / 売上<?php echo $html->image('yen.png')?></th>
        <th style="text-align:right">粗利 Total<?php echo $html->image('yen.png')?></th>
        <th style="text-align:right">粗利 Total / 売上<?php echo $html->image('yen.png')?></th>
        <th></th>
        <th></th>
        <th style="text-align:right">販売為替</th>
        <th style="text-align:right">原価為替</th>
	</tr>
<?php
	$sum_total = 0;
	$sum_service_fee= 0;
	$sum_hi_total= 0;
	$sum_rw_total= 0;
	$sum_hawaii_tax= 0;
	$sum_remittance_hawaii_tax= 0;
	$sum_rw_discount= 0;
	$sum_total_discount= 0;
	$sum_rw= 0;
	$sum_gross_total= 0;
    $sum_credit_pay =0;

    for($i=0;$i < count($data);$i++){

	  echo "<tr>".
	           "<td nowrap>".($i+1)."</td>".
	           "<td nowrap>{$common->evalForShortDate($data[$i]['wedding_dt'])}</td>".
	           "<td nowrap><a href='".$html->url('/customersList/goToCustomerInfo/'.$data[$i]['customer_id'])."'>{$data[$i]['grmls_kj']}{$data[$i]['grmfs_kj']}</a></td>".
	           "<td class='yen'>".number_format($data[$i]['total'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['credit_domestic_pay_amount']+$data[$i]['credit_aboard_pay_amount'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['service_fee'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['hi_total'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['rw_total'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['hawaii_tax'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['remittance_hawaii_tax'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['rw_discount'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['total_discount'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['rw_sum'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['rw_total_rate'],2)."%</td>".
	           "<td class='yen'>".number_format($data[$i]['gross_total'])."</td>".
	           "<td class='yen'>".number_format($data[$i]['gross_total_rate'],2)."%</td>".
	           "<td></td>".
	           "<td></td>".
	           "<td class='yen'>".number_format($data[$i]['sales_rate'],2)."</td>".
	           "<td class='yen'>".number_format($data[$i]['cost_rate'],2)."</td>".
            "</tr>";
    $sum_total += $data[$i]['total'];
    $sum_credit_pay += $data[$i]['credit_domestic_pay_amount']+$data[$i]['credit_aboard_pay_amount'];
    $sum_service_fee += $data[$i]['service_fee'];
    $sum_hi_total += $data[$i]['hi_total'];
    $sum_rw_total += $data[$i]['rw_total'];
    $sum_hawaii_tax += $data[$i]['hawaii_tax'];
    $sum_remittance_hawaii_tax += $data[$i]['remittance_hawaii_tax'];
    $sum_rw_discount += $data[$i]['rw_discount'];
    $sum_total_discount += $data[$i]['total_discount'];
    $sum_rw += $data[$i]['rw_sum'];
    $sum_gross_total += $data[$i]['gross_total'];
    }
     echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>合計</td>".
          "<td class='yen'>".number_format($sum_total)."</td>".
          "<td class='yen'>".number_format($sum_credit_pay)."</td>".
          "<td class='yen'>".number_format($sum_service_fee)."</td>".
          "<td class='yen'>".number_format($sum_hi_total)."</td>".
          "<td class='yen'>".number_format($sum_rw_total)."</td>".
          "<td class='yen'>".number_format($sum_hawaii_tax)."</td>".
          "<td class='yen'>".number_format($sum_remittance_hawaii_tax)."</td>".
          "<td class='yen'>".number_format($sum_rw_discount)."</td>".
          "<td class='yen'>".number_format($sum_total_discount)."</td>".
          "<td class='yen'>".number_format($sum_rw)."</td>".
          "<td class='yen'>".($sum_total== 0 ? 0 : number_format(($sum_rw/$sum_total)*100,2))."%</td>".
          "<td class='yen'>".number_format($sum_gross_total)."</td>".
          "<td class='yen'>".($sum_total== 0 ? 0 :number_format(($sum_gross_total/$sum_total)*100,2)) ."%</td>".
          "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
          "</tr>";

     $c = count($data);
     echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>平均</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_total/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_credit_pay/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_service_fee/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_hi_total/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_rw_total/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_hawaii_tax/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_remittance_hawaii_tax/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_rw_discount/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_total_discount/$c))."</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_rw/$c))."</td>".
          "<td class='yen'>".($sum_total== 0 ? 0 : number_format(($sum_rw/$sum_total)*100,2))."%</td>".
          "<td class='yen'>".($c==0 ? 0 : number_format($sum_gross_total/$c))."</td>".
          "<td class='yen'>".($sum_total== 0 ? 0 :number_format(($sum_gross_total/$sum_total)*100,2)) ."%</td>".
          "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
	      "<td>&nbsp;</td>".
          "</tr>";
?>
    </table>
 </div>


