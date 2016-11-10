<script type='text/javascript'>
$(function(){

	/* 基本情報編集画面表示 */
    $("#basic_info").click(function(){

       $(this).simpleLoading('show');
	   $.post(<?php echo "'".$html->url('basicInfoEditForm')."'" ?> , function(html) {
		  $(this).simpleLoading('hide');
		  $("body").append(html);
       });
      return false;
    });

    /* 入金状況一覧画面表示 */
    $("#credit_info_form").click(function(){

       $(this).simpleLoading('show');
	   $.post(<?php echo "'".$html->url('creditInfoForm')."'" ?> , function(html) {
		  $(this).simpleLoading('hide');
		  $("body").append(html);
       });
      return false;
    });
});
</script>

<ul class="operate">
   <li><a href="<?php echo $html->url('addEstimate') ?>">追加</a></li>
   <li><a href="#" id="credit_info_form">入金状況一覧</a></li>
</ul>
<table class="content">
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle"><a href="#" id="basic_info">基本事項</a></legend>

	  <table class="viewheader">
	    <tr>
	       <th>顧客番号：</th><td class="short" id="customer_cd_view"><?php echo $customer['CustomerMstView']['customer_cd'] ?></td>
	       <th>ステータス：</th><td class="short" id="status_view"><?php echo $common->evalNbsp($customer['CustomerMstView']['status_nm'])?></td>
	       <th>挙式日：</th><td class="short" id="wedding_dt_view">
	        <?php
	         //ステータスが仮約定以前の場合は挙式予定日を、それ以外は挙式日を表示する
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
	         	echo $common->evalForShortDate($customer['CustomerMstView']['wedding_planned_dt']);
	         }else{
	         	echo $common->evalForShortDate($customer['CustomerMstView']['wedding_dt']);
	         }
	       ?>
	       </td>
	       <th>挙式会場：</th>
	       <td class="long" id="wedding_place_view">
	       <?php
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
	            echo $common->evalNbsp($customer['CustomerMstView']['wedding_planned_place']);
	         }else{
	         	echo $common->evalNbsp($customer['CustomerMstView']['wedding_place']);
	         }
	       ?>
	       </td>
	       <th>時間：</th>
	       <td class="short" id="wedding_time_view">
	       <?php
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
	            echo $common->evalNbsp($customer['CustomerMstView']['wedding_planned_time']);
	         }else{
	         	echo $common->evalNbsp($customer['CustomerMstView']['wedding_time']);
	         }
	       ?>
	       </td>
	       <th>レセプション会場：</th>
	       <td class="long" id="reception_place_view">
	        <?php
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
	            echo $common->evalNbsp($customer['CustomerMstView']['reception_planned_place']);
	         }else{
	         	echo $common->evalNbsp($customer['CustomerMstView']['reception_place']);
	         }
	       ?>
	       </td>
	       <th>時間：</th>
	       <td class="short" id="reception_time_view">
	       <?php
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTING){
	            echo $common->evalNbsp($customer['CustomerMstView']['reception_planned_time']);
	         }else{
	         	echo $common->evalNbsp($customer['CustomerMstView']['reception_time']);
	         }
	       ?>
           </td>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
</table>
<table class="list" cellspacing="0">

		<tr>
		<th>No</th>
		<th>採用</th>
		<th>見積合計<?php echo $html->image('yen.png')?></th>
		<th>見積合計<?php echo $html->image('dollar.png')?></th>
		<th>作成日</th>
		<th>作成者</th>
		<th>更新日</th>
		<th>更新者</th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++)
		    {
		   	  $atr = $data[$i];
		   	  echo "<tr><td><a href='".$html->url('editEstimate')."/{$atr['basic']['estimate_id']}"."' ?>".($i+1)."</a></td>";


		   	  //採用
		   	  if($atr['basic']['adopt_flg'] == 1)
		   	  {
		   	  	 echo "<td>".$html->image('ok.png')."</td>";
		   	  }
		   	  //不採用
		   	  else
		   	  {
		   	  	 echo "<td>&nbsp;</td>";
		   	   }

              echo "<td>".number_format(round($common->evalNbsp($atr['yen']['total'])))."</td>".
		   	        "<td>".number_format(round($common->evalNbsp($atr['dollar']['total']),2))."</td>".
		   	        "<td>{$atr['basic']['reg_dt']}</td>".
		   	        "<td>{$atr['basic']['reg_nm']}</td>".
		   	        "<td>".$common->evalNbsp($atr['basic']['upd_dt'])."</td>".
		   	        "<td>".$common->evalNbsp($atr['basic']['upd_nm'])."</td></tr>";

		    }
		?>
</table>