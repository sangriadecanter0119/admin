<?php
$index_url = $html->url('index');
$customer_cd_update_url = $html->url('updateCustomerCode');
$confirm_image_path = $html->webroot("/images/confirm_result.png");
$error_image_path = $html->webroot("/images/error_result.png");

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

   $("input:submit").button();
   $(".rate").mask("999.99");
   $(".customer_cd").mask("999999-999999");

   $(".balloon").hide();
   $("#hi_header").hover(function(){ $("#hi_header_balloon").fadeIn();},
                         function(){ $("#hi_header_balloon").fadeOut();});

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

                     /* 正常更新なら画面を再度読み込み */
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = "$index_url" + "/" + ($("#remittance_rate").val()=="" ? "0" : $("#remittance_rate").val()) + "/" + $("#cost_rate").val();
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
             title: "更新結果"
    });

    /* 為替レート更新 */
    $("#rate_update_form").submit(function(){

        /* 更新開始 */
        $(this).simpleLoading('show');

		var formData = $("#rate_update_form").serialize();

		$.post("$index_url",formData , function(result) {

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

    /* 顧客コード一括更新 */
    $("#customer_cd_update_form").submit(function(){

        /* 更新開始 */
        $(this).simpleLoading('show');

		var formData = $("#customer_cd_update_form").serialize();

		$.post("$customer_cd_update_url",formData , function(result) {

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

   $("#indicator").css("display","none");
});
JSPROG
)) ?>

<ul class="operate">
   <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
</ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null);
         echo $form->text('GoodsMstView.wedding_planned_dt' ,array('value' => $wedding_dt));
         echo $form->end(); ?>
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
		   if($found==false){
		     	 echo "<option value='".$wedding_dt."' selected>{$wedding_dt}</option>";
		   }
	    ?>
   </select>

   <form id="rate_update_form" method="post" action="" style="display:inline" >
     <label style="margin-left:10px;">送金為替レート</label>
     <input id="remittance_rate" name="data[RateUpdate][remittance_rate]" class="rate" type="text"  style="width:70px;" value="<?php echo $remittance_rate ?>" />
     <label style="margin-left:10px;">原価為替レート</label>
     <input id="cost_rate" name="data[RateUpdate][cost_rate]"  class="rate" type="text"   style="width:70px;" value="<?php echo $cost_rate  ?>" />
     <input name="data[RateUpdate][wedding_dt]" type="hidden" value="<?php echo $wedding_dt ?>" />
     <input id="rate_update_btn"  type="submit" value="一括変換" style="margin-left:10px;"/>
   </form>
 </div>

 <!--
 <table class="content">
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle">総合計</legend>

	  <table class="viewheader">
	    <tr>
   <?php
	    $vendor_total_cost = 0;
	    $aw_total_cost = 0;
	    $total_tax = 0;
	    $remittance_total = 0;
        for($i=0;$i < count($data);$i++)
        {
          $atr = $data[$i]['RemittanceTrnView'];

	      $vendor_total_cost += $atr['vendor_total_cost'];
	      $aw_total_cost += $atr['aw_total_cost'];
	      $total_tax += $atr['total_tax'];
	      $remittance_total += $atr['remittance_total'];
        }
        echo "<th>現地払い料総合計：</th><td class='long'>".number_format($vendor_total_cost,2)."</td>".
	         "<th>HI手配料総合計：</th> <td class='long'>".number_format($aw_total_cost,2)."</td>".
	         "<th>州税総合計：</th>     <td class='long'>".number_format($total_tax,2)."</td>".
	         "<th>振込み額総合計：</th>  <td class='long'>".number_format($remittance_total,2)."</td>";
  ?>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
</table>
-->

<div style='position:relative;'>
 <div id='hi_header_balloon' class='balloon' style='position:absolute;top:-40px;left:550px;'><div>HI負担分の割引料を加味した金額</div></div>
 <form id="customer_cd_update_form" method="post" action="">
	<table class="list" cellspacing="0">

	<tr>
	    <th>No</th>
	    <th>挙式日</th>
	    <th>顧客名</th>
	    <th>顧客番号</th>
	    <th>現地払い料</th>
	    <th id='hi_header'>HI手配料</th>
        <th>州税</th>
  	    <th>振込み額合計</th>
	</tr>
<?php
    $vendor_total_cost = 0;
    $aw_total_cost = 0;
    $total_tax = 0;
    $remittance_total = 0;

    for($i=0;$i < count($data);$i++){
      $atr = $data[$i]['RemittanceTrnView'];
	  echo "<tr>".
	           "<td nowrap><a href='".$html->url('editRemittance/'.$atr['estimate_id'])."'>".($i+1)."</a></td>".
	           "<td nowrap>{$common->evalForShortDate($atr['wedding_dt'])}</td>".
	           "<td nowrap><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['customer_id'])."'>{$atr['grmls_kj']}{$atr['grmfs_kj']}</a></td>".
	           "<td>".$atr['customer_cd']."</td>".
	           "<td>".number_format($atr['vendor_total_cost'],2)."</td>".
	           "<td>".number_format($atr['aw_total_cost'],2)."</td>".
	           "<td>".number_format($atr['total_tax'],2)."</td>".
	           "<td>".number_format($atr['remittance_total'],2)."</td>".
            "</tr>";

	  $vendor_total_cost += $atr['vendor_total_cost'];
	  $aw_total_cost += $atr['aw_total_cost'];
	  $total_tax += $atr['total_tax'];
	  $remittance_total += $atr['remittance_total'];
    }

      echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style='text-align:center'>総合計</td>".
           "<td class='long' style='font-size:1.3em'>".number_format($vendor_total_cost,2)."</td>".
    	   "<td class='long' style='font-size:1.3em'>".number_format($aw_total_cost,2)."</td>".
    	   "<td class='long' style='font-size:1.3em'>".number_format($total_tax,2)."</td>".
    	   "<td class='long' style='font-size:1.3em'>".number_format($remittance_total,2)."</td></tr>";

?>
    </table>
 </form>
</div>
<div id="result_dialog" style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>


