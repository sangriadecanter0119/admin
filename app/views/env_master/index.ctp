    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('editEnv') ?>">編集</a></li>
    </ul>
     <table class="list" cellspacing="0">

		<tr>
		  <th>ハワイ州税率</th>
		  <th>サービス料名</th>
		  <th>サービス料率</th>
		  <th>割引率名</th>
		  <th>割引率</th>
		  <th>割引額名</th>
		  <th>為替レート</th>
		  <th>送金為替レート</th>
		  <th>割引為替レート</th>
		  <th>割引AWシェア</th>
		  <th>割引RWシェア</th>
		  <th>MAILホスト</th>
		  <th>MAILポート</th>
		  <th>MAILプロトコル</th>
		  <th>ステータス更新日</th>
		  <th>更新者</th>
		  <th>更新日</th>
		</tr>

		<?php
	  	echo "<tr>".
	  	         "<td>".($data['EnvMst']['hawaii_tax_rate']*100)."%</td>".
	  	         "<td>".($data['EnvMst']['service_rate_nm'])."</td>".
	  	         "<td>".($data['EnvMst']['service_rate']*100)."%</td>".
	  	         "<td>".($data['EnvMst']['discount_rate_nm'])."</td>".
	  	         "<td>".($data['EnvMst']['discount_rate']*100)."%</td>".
	  	         "<td>".($data['EnvMst']['discount_nm'])."</td>".
	  	         "<td>{$data['EnvMst']['exchange_rate']}</td>".
	  	         "<td>{$data['EnvMst']['remittance_exchange_rate']}</td>".
	          	 "<td>{$data['EnvMst']['discount_exchange_rate']}</td>".
	  	         "<td>".($data['EnvMst']['discount_aw_share']*100)."%</td>".
	      	     "<td>".($data['EnvMst']['discount_rw_share']*100)."%</td>".
	  	         "<td>{$data['EnvMst']['mail_host']}</td>".
	  	         "<td>{$data['EnvMst']['mail_port']}</td>".
	  	         "<td>{$data['EnvMst']['mail_protocol']}</td>".
	  	         "<td>".$common->evalNbspForLongDate($data['EnvMst']['status_upd_dt'])."</td>".
	  	         "<td>".$common->evalNbsp($data['EnvMst']['upd_nm'])."</td>".
	  	         "<td>".$common->evalNbspForShortDate($data['EnvMst']['upd_dt'])."</td>".
	  	      "</tr>";
		?>
    </table>


