    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager/userMaster') ?>">一覧に戻る</a></li>
    </ul>

     <!--  ページネーション  -->
     <?php
     echo $paginator->counter(array('format' => '%count%件中%start% ~ %end%件表示中 '));
     echo $paginator->numbers (
	     array (
	   	         'before' => $paginator->hasPrev() ? $paginator->first('<<').' | ' : '',
		         'after' => $paginator->hasNext() ? ' | '.$paginator->last('>>') : '',
	           )
      );
    ?>

   <div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
    <table class="list" cellspacing="0">
        <tr>
		  <th>ユーザ名</th>
		  <th>ユーザ表示名</th>
		  <th>ログイン日時</th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++){

		  	$atr = $data[$i]['LoginHistoryTrn'];
		  	echo "<tr>".
		  	         "<td>".$common->evalNbsp($atr['username'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['display_nm'])."</td>".
	                 "<td>".$common->evalNbspForLongDate($atr['login_dt'])."</td>".
		  	      "</tr>";
		  }
		?>
     </table>
   </div>

