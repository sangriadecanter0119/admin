<ul class="operate"></ul>

<table class="content">
<tr>
  <td style="padding:0px 0px 8px 0px;">
    <fieldset class="headerlegend">
      <legend class="legendtitle">基本事項</legend>

	  <table class="viewheader">
	    <tr>
	       <th>顧客番号：</th><td class="short"><?php echo $customer['CustomerMstView']['customer_cd'] ?></td>
	       <th>ステータス：</th><td class="short"><?php echo $common->evalNbsp($customer['CustomerMstView']['status_nm'])?></td>
	       <th>挙式日：</th><td class="short">
	        <?php
	         if($customer['CustomerMstView']['status_id'] < CS_CONTRACTED)
	         {
	         	echo $common->evalForShortDate($customer['CustomerMstView']['wedding_planned_dt']);
	         }
	         else
	         {
	         	echo $common->evalForShortDate($customer['CustomerMstView']['wedding_dt']);
	         }
	       ?>
	       </td>
	       <th>挙式会場：</th><td class="long"><?php echo $common->evalNbsp($customer['CustomerMstView']['wedding_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalNbsp($customer['CustomerMstView']['wedding_time']) ?></td>
	       <th>レセプション会場：</th><td class="long"><?php echo $common->evalNbsp($customer['CustomerMstView']['reception_place']) ?></td>
	       <th>時間：</th><td class="short"><?php echo $common->evalNbsp($customer['CustomerMstView']['reception_time']) ?></td>
	    </tr>
	  </table>
	</fieldset>
  </td>
</tr>
</table>
<table class="list" cellspacing="0">
		<tr>
		<th>NO</th>
		<th>コメント</th>
		<th>スナップ作成日</th>
		<th>スナップ作成者</th>
		</tr>
		<?php
		  	for($i=0;$i < count($data);$i++)
		    {
		   	  $atr = $data[$i]['FinalSheetTrn'];
		   	  echo "<tr><td><a href='".$html->url('detail')."/{$atr['id']}"."' ?>".($i+1)."</a></td>";

              echo "<td>{$atr['note']}</td>".
		   	       "<td>{$atr['reg_dt']}</td>".
		   	       "<td>{$atr['reg_nm']}</td>";
		    }
		?>
</table>