<script type='text/javascript'>
$(function(){

	　/* 入金情報一覧ダイアログ */
	 $("#credit_info_dialog").dialog({
	             buttons: [{
                  text:"OK",
                  click:function(){
                	  $("#credit_info_dialog").dialog('close');
	               }
		         }],
	             beforeClose:function(){
	                $("#credit_info_dialog").remove();
	             },
	             draggable: false,
	             autoOpen: true,
	             resizable: false,
	             zIndex: 2000,
	             width:800,
	             height:450,
	             modal: true,
	             title: "入金情報一覧画面"
	 });
});
</script>

<div id="credit_info_dialog">

  <table class="viewheader" style="margin-top:10px;margin-bottom:20px;font-weight:bold">
  <?php
        /*
	    $total = 0;
        for($i=0;$i < count($data);$i++){
	      $total += $data[$i]['CreditTrnView']['amount'];
        }
        */
        echo "<tr><td>請求金額：￥".number_format($invoice)."</td>";
        echo "<td>入金額総合計：￥".number_format($credit)."</td>";
        if($credit-$invoice < 0){
           echo "<td style='color:red'>不足金額：￥".number_format($credit-$invoice)."</td></tr>";
        }else if($credit-$invoice > 0){
           echo "<td style='color:blue'>過剰金額：￥".number_format($credit-$invoice)."</td></tr>";
        }else{
           echo "<td>差額：￥".number_format($credit-$invoice)."</td></tr>";
        }
  ?>
  </table>

	<table class="list" cellspacing="0">
	<tr>
	    <th style="text-align: center">No</th>
	    <th style="text-align: center">入金日</th>
	    <th style="text-align: center">顧客名</th>
	    <th style="text-align: center">入金顧客名</th>
	    <th style="text-align: center">金額</th>
        <th style="text-align: center">項目</th>
        <th style="text-align: center">登録者</th>
	    <th style="text-align: center">登録日</th>
	    <th style="text-align: center">更新者</th>
        <th style="text-align: center">更新日</th>
	</tr>
<?php
    for($i=0;$i < count($data);$i++)
    {
      $atr = $data[$i]['CreditTrnView'];
	  echo "<tr>".
	 	       "<td style='text-align: center;width:10px;'>".($i+1)."</td>".
	 	       "<td style='text-align: center'>{$common->evalForShortDate($atr['credit_dt'])}</td>".
	           "<td style='text-align: center'>{$atr['grmls_kj']}{$atr['grmfs_kj']}</td>".
	           "<td style='text-align: center'>".$atr['credit_customer_nm']."</td>".
	           "<td style='text-align: right;width:20px;'>".number_format($atr['amount'])."</td>".
	           "<td style='text-align: center;width:250px;'>".$atr['credit_type_nm']."</td>".
	           "<td style='text-align: center'>".$atr['reg_nm']."</td>".
	           "<td style='text-align: center'>".$common->evalForShortDate($atr['reg_dt'])."</td>".
	           "<td style='text-align: center'>".$atr['upd_nm']."</td>".
	           "<td style='text-align: center'>".$common->evalForShortDate($atr['upd_dt'])."</td>".
            "</tr>";
    }
?>
    </table>
</div>










