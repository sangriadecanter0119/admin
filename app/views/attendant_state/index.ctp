<script type='text/javascript'>

$(function(){

  $("#showing_dt").change(function(){
	 var newHref = $("#refresh_data_link").attr("href") + $(this).val();
	 location.href = newHref;
     $("#refresh_data_link").click();
  });

  $("#estimate_total_amount").text($("#_estimate_total_amount").text());
  $("#invoice_total_amount").text($("#_invoice_total_amount").text());

})
/* コンテンツの高さ調整
------------------------------------------------*/
function ResizeTable(){
	$("#customers_contract_list").height($(window).height()-200);
}
</script>

<style type="text/css">
.yen:before { content:"￥";  }
</style>

<ul class="operate"></ul>

 <div class='notation'>
   <label style='font-size:1.2em'>基準年月：</label>
   <select id='showing_dt' style="width:80px">
		<?php
		  for($i=0;$i < count($wedding_dt_list);$i++){

		           if($showing_dt == $wedding_dt_list[$i]){
		         	 echo "<option value='".$wedding_dt_list[$i]."' selected>{$wedding_dt_list[$i]}</option>";
		           }else{
		         	 echo "<option value='".$wedding_dt_list[$i]."'>{$wedding_dt_list[$i]}</option>";
		           }
		   }
	    ?>
   </select>
   <a  id="refresh_data_link" href="<?php echo $html->url('/attendantState/index/') ?>"></a>
 </div>

    <div style="margin-top:10px;margin-bottom:10px;font-size:1.2em">【成約担当】
       <span style="margin-left:30px;"><?php echo "全".count($estimate_data)."件" ?></span>
       <span style="margin-left:15px;margin-right:5px">見積額総合計：</span><span id="estimate_total_amount" class="yen"></span></div>

    <table id="customers_table" class="list" cellspacing="0" style="width:100%;height:90%">
	<tr>
	   <th style="width:100px">担当者</th>
	   <th style="width:300px">担当顧客</th>
	   <th>成約日</th>
	   <th>挙式日</th>
	   <th style="width:80px">見積金額</th>
	   <th style="width:80px">粗利額</th>
	   <th style="width:80px">粗利率</th>
	   <th style="text-align:center">ステータス</th>
	 </tr>
<?php
    $current_person = "";
    $sub_amount = 0;
    $sub_profit = 0;
    $total_amount = 0;
    $sub_count = 0;
    for($i=0;$i < count($estimate_data);$i++){

      if($current_person == $estimate_data[$i]["ContractTrnView"]["first_contact_person_nm"]){
          echo "<tr><td></td>";
      }else{
          if($i != 0){
          echo "<tr style='background-color:#ffd8cc;'>".
                  "<td></td>".
                  "<td></td>".
                  "<td></td>".
                  "<td style='font-weight:bolder'>合計(".$sub_count."件)：</td>".
                  "<td class='yen' style='font-weight:bolder'>".number_format($sub_amount)."</td>".
                  "<td class='yen' style='font-weight:bolder'>".number_format($sub_profit)."</td>".
                  "<td class='digit' style='font-weight:bolder'>".number_format(($sub_profit/$sub_amount)*100)."%</td>".
                  "<td></td>".
                "</tr>";
          }
          $sub_amount = 0;
          $sub_profit = 0;
          $sub_count = 0;
          echo "<tr><td>{$estimate_data[$i]["ContractTrnView"]["first_contact_person_nm"]}</td>";
      }
      echo  "<td><span style='margin-right:10px'>".($sub_count+1)."</span><a href='".$html->url('/customersList/goToCustomerInfo/'.$estimate_data[$i]["ContractTrnView"]['customer_id'])."'>{$estimate_data[$i]["ContractTrnView"]["grmls_kj"]}{$estimate_data[$i]["ContractTrnView"]["grmfs_kj"]}</a></td>".
            "<td>{$common->evalNbspForShortDate($estimate_data[$i]["ContractTrnView"]["contract_dt"])}</td>".
            "<td>{$common->evalNbspForShortDate($estimate_data[$i]["ContractTrnView"]["wedding_dt"])}</td>".
            "<td class='yen'>".number_format($estimate_data[$i]["ContractTrnView"]["estimate_amount"])."</td>".
            "<td class='yen'>".number_format($estimate_data[$i]["ContractTrnView"]["estimate_profit"])."</td>".
            "<td class='digit'>".number_format($estimate_data[$i]["ContractTrnView"]["estimate_profit_rate"])."%</td>".
            "<td style='text-align:center'>{$estimate_data[$i]["ContractTrnView"]["status_nm"]}</td>".
            "</tr>";
      $sub_amount += $estimate_data[$i]["ContractTrnView"]["estimate_amount"];
      $sub_profit += $estimate_data[$i]["ContractTrnView"]["estimate_profit"];
      $total_amount += $estimate_data[$i]["ContractTrnView"]["estimate_amount"];
      $sub_count++;
      $current_person = $estimate_data[$i]["ContractTrnView"]["first_contact_person_nm"];
    }
    echo "<tr style='background-color:#ffd8cc;'>".
         "<td></td>".
         "<td></td>".
         "<td></td>".
         "<td style='font-weight:bolder'>合計(".$sub_count."件)：</td>".
         "<td class='yen' style='font-weight:bolder'>".number_format($sub_amount)."</td>".
         "<td class='yen' style='font-weight:bolder'>".number_format($sub_profit)."</td>".
         "<td class='digit' style='font-weight:bolder'>".number_format(($sub_profit/$sub_amount)*100)."%</td>".
         "<td></td>".
         "</tr>";
    echo "<div id='_estimate_total_amount' style='display:none'>".number_format($total_amount)."</div>";
?>
    <tr>
  </table>

  <div style="margin-top:20px;margin-bottom:10px;font-size:1.2em">【制作担当】
    <span style="margin-left:30px;"><?php echo "全".count($invoice_data)."件" ?></span>
    <span style="margin-left:30px;margin-right:5px">請求額総合計：</span><span id="invoice_total_amount" class="yen"></span></div>

    <table id="customers_table" class="list" cellspacing="0" style="width:100%;height:90%">
    <tr>
      <th style="width:100px">担当者</th>
      <th style="width:300px">担当顧客</th>
      <th>成約日</th>
	  <th>挙式日</th>
	  <th style="width:80px">請求金額</th>
	  <th style="width:80px;text-align:center">粗利額</th>
	  <th style="width:80px;text-align:center">粗利率</th>
	  <th style="text-align:center">ステータス</th>
	</tr>
<?php
    $current_person = "";
    $sub_amount = 0;
    $sub_profit = 0;
    $total_amount = 0;
    $sub_count = 0;
    for($i=0;$i < count($invoice_data);$i++){

      if($current_person == $invoice_data[$i]["ContractTrnView"]["process_person_nm"]){
          echo "<tr><td></td>";
      }else{
         if($i != 0){
            echo "<tr style='background-color:#ffd8cc;'>".
                    "<td></td>".
                    "<td></td>".
                    "<td></td>".
                    "<td style='font-weight:bolder'>合計(".$sub_count."件)：</td>".
                    "<td class='yen' style='font-weight:bolder'>".number_format($sub_amount)."</td>".
                    "<td class='yen' style='font-weight:bolder'>".number_format($sub_profit)."</td>".
                    "<td class='digit' style='font-weight:bolder'>".number_format(($sub_profit/$sub_amount)*100)."%</td>".
                    "<td></td>".
                  "</tr>";
         }
         $sub_amount = 0;
         $sub_profit = 0;
         $sub_count = 0;

         echo "<tr><td>{$invoice_data[$i]["ContractTrnView"]["process_person_nm"]}</td>";
      }
      echo  "<td><span style='margin-right:10px'>".($sub_count+1)."</span><a href='".$html->url('/customersList/goToCustomerInfo/'.$invoice_data[$i]["ContractTrnView"]['customer_id'])."'>{$invoice_data[$i]["ContractTrnView"]["grmls_kj"]}{$invoice_data[$i]["ContractTrnView"]["grmfs_kj"]}</a></td>".
            "<td>{$common->evalNbspForShortDate($invoice_data[$i]["ContractTrnView"]["contract_dt"])}</td>".
            "<td>{$common->evalNbspForShortDate($invoice_data[$i]["ContractTrnView"]["wedding_dt"])}</td>".
            "<td class='yen'>".number_format($invoice_data[$i]["ContractTrnView"]["estimate_amount"])."</td>".
            "<td class='yen'>".number_format($invoice_data[$i]["ContractTrnView"]["estimate_profit"])."</td>".
            "<td class='digit'>".number_format($invoice_data[$i]["ContractTrnView"]["estimate_profit_rate"])."%</td>".
            "<td style='text-align:center'>{$invoice_data[$i]["ContractTrnView"]["status_nm"]}</td>".
            "</tr>";
      $sub_amount += $invoice_data[$i]["ContractTrnView"]["estimate_amount"];
      $sub_profit += $invoice_data[$i]["ContractTrnView"]["estimate_profit"];
      $total_amount += $invoice_data[$i]["ContractTrnView"]["estimate_amount"];
      $sub_count++;
      $current_person = $invoice_data[$i]["ContractTrnView"]["process_person_nm"];
    }
   echo "<tr style='background-color:#ffd8cc;'>".
          "<td></td>".
          "<td></td>".
          "<td></td>".
          "<td style='font-weight:bolder'>合計(".$sub_count."件)：</td>".
          "<td class='yen' style='font-weight:bolder'>".number_format($sub_amount)."</td>".
          "<td class='yen' style='font-weight:bolder'>".number_format($sub_profit)."</td>".
          "<td class='digit' style='font-weight:bolder'>".number_format(($sub_profit/$sub_amount)*100)."%</td>".
          "<td></td>".
         "</tr>";
   echo "<div id='_invoice_total_amount' style='display:none'>".number_format($total_amount)."</div>";
?>
    <tr>
  </table>

