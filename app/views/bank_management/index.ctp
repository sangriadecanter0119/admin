<?php
$index_url = $html->url('index');
$credit_url = $html->url('creditInfoForm');

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

   $("input:submit").button();
   $("#credit_dt").mask("9999-99");
   $(".balloon").hide();

   $("#total_credit_amount").hover(function(){ $("#total_credit_amount_balloon").fadeIn();},
                                   function(){ $("#total_credit_amount_balloon").fadeOut();});

   $("#credit_dt").change(function(){
      $("#CreditTrnCreditDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });

   /* 入金状況一覧画面表示 */
    $(".credit_info_link").click(function(){

       $(this).simpleLoading('show');
	   $.get("$credit_url"+"/" +  $(this).attr('id') , function(html) {
		  $(this).simpleLoading('hide');
		  $("body").append(html);
       });
      return false;
    });

   $("#indicator").css("display","none");
});
JSPROG
)) ?>

<ul class="operate">
   <li><a href="<?php echo $html->url('addCreditInfo') ?>">追加</a></li>
   <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
</ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null);
         echo $form->text('CreditTrn.credit_dt' ,array('value' => $credit_dt));
         echo $form->end(); ?>
 </div>

 <div class='notation'>
   <label>表示年月：</label>

   <!--
   <select id='credit_dt'>
		<?php
		/*
		  $found = false;
		  for($i=0;$i < count($credit_dt_list);$i++)
          {
		           if($credit_dt == $credit_dt_list[$i]){
		         	 echo "<option value='".$credit_dt_list[$i]."' selected>{$credit_dt_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$credit_dt_list[$i]."'>{$credit_dt_list[$i]}</option>";
		           }
		   }
		*/
		   /* 初期表示はログイン年月を基にするがログイン年月に挙式がない場合はもあるので、その場合は項目のみ作成しておく  */
		/* if($found==false){
		     	 echo "<option value='".$credit_dt."' selected>{$credit_dt}</option>";
		   }
		 */
	    ?>
   </select>
   -->

   <input type="text" id="credit_dt" name="start_date" class="inputdate" value="<?php echo $credit_dt ?>" />
   <input id="search_button" type="image"  src="<?php echo $html->webroot("/images/search.png"); ?>"  style="margin-left:3px;" />
 </div>

   <?php
	    $total = 0;
        for($i=0;$i < count($data);$i++){
	      $total += $data[$i]['CreditTrnView']['amount'];
        }
        echo "<div style='margin-top:10px;margin-bottom:10px;position:relative;'>".
             "<div id='total_credit_amount_balloon' class='balloon' style='position:absolute;top:-43px;left:0px;'><div>入金日が検索月内のすべての入金合計額を表示</div></div>".
             "<h3  id='total_credit_amount' style='display:inline;padding-right:40px;'>入金額総合計：".number_format($total)."</h3>".
             "<h3  id='invoiced_total_credit_amount' style='display:inline;padding-right:40px;'>請求書発行済み挙式前挙式代金：".number_format($invoiced_total_credit_amount)."</h3>".
             "<h3  id='uninvoiced_total_credit_amoun' style='display:inline;'>請求書発行前挙式前金等：".number_format($uninvoiced_total_credit_amount)."</h3>".
             "</div>";
  ?>


	<table class="list" cellspacing="0">
	<tr>
	    <th style="text-align: center">No</th>
	    <th style="text-align: center">履歴</th>
	    <th style="text-align: center">入金日</th>
	    <th style="text-align: center">顧客番号</th>
	    <th style="text-align: center">顧客名</th>
	    <th style="text-align: center">入金顧客名</th>
	    <th style="text-align: center">金額</th>
        <th style="text-align: center">項目</th>
        <th style="text-align: center">ステータス</th>
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
	 	       "<td style='text-align: center;width:10px;'><a href='".$html->url('editCreditInfo/'.$atr['id'])."'>".($i+1)."</a></td>".
	 	       "<td style='text-align: center;width:10px;'><a href='#' id='{$atr['customer_id']}' class='credit_info_link'><img src='{$html->webroot("/images/yen.png")}' /></a></td>".
	 	       "<td style='text-align: center'>{$common->evalForShortDate($atr['credit_dt'])}</td>".
	 	       "<td style='text-align: center'><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['customer_id'])."'>{$atr['customer_cd']}</a></td>".
	           "<td style='text-align: center'>{$atr['grmls_kj']}{$atr['grmfs_kj']}</td>".
	           "<td style='text-align: center'>".$atr['credit_customer_nm']."</td>".
	           "<td style='text-align: right;width:20px;'>".number_format($atr['amount'])."</td>".
	           "<td style='text-align: center;width:250px;'>".$atr['credit_type_nm']."</td>".
	           "<td style='text-align: center'>".$atr['status_nm']."</td>".
	           "<td style='text-align: center'>".$atr['reg_nm']."</td>".
	           "<td style='text-align: center'>".$common->evalForShortDate($atr['reg_dt'])."</td>".
	           "<td style='text-align: center'>".$atr['upd_nm']."</td>".
	           "<td style='text-align: center'>".$common->evalForShortDate($atr['upd_dt'])."</td>".
            "</tr>";
    }
?>
    </table>



