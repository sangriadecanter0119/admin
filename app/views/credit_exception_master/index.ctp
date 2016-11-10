    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('addCreditException') ?>">追加</a></li>
    </ul>

<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
     <table class="list" cellspacing="0">
		<tr>
		<th>入金例外ID</th>
		<th>種別</th>
		<th>顧客コード</th>
		<th>銀行入金名</th>
		<th>登録者</th>
		<th>登録日時</th>
		<th>更新者</th>
		<th>更新日時</th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['CreditExceptionMstView'];
		  	echo "<tr>".
		  	         "<td>{$atr['id']}</td>".
		  	         "<td>{$atr['credit_type_nm']}</td>".
		  	         "<td><a href='".$html->url('editCreditException')."/{$atr['id']}'>{$atr['customer_cd']}</a></td>".
		  	         "<td>{$atr['credit_customer_nm']}</td>".
		  	         "<td>{$atr['reg_nm']}</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  }
		?>
    </table>
</div>

