    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('addSetGoods') ?>">追加</a></li>
    </ul>

     <table class="list" cellspacing="0">

		<tr>
		<th><a href="">セット構成ID</a></th>
		<th><a href="">セット商品ID</a></th>
		<th><a href="">セット商品コード</a></th>
		<th><a href="">セット商品名</a></th>
		<th><a href="">セット商品内容</a></th>
		<th><a href="">構成商品ID</a></th>
		<th><a href="">構成商品コード</a></th>
		<th><a href="">構成商品名</a></th>
		<th><a href="">登録者</a></th>
		<th><a href="">登録日時</a></th>
		<th><a href="">更新者</a></th>
		<th><a href="">更新日時</a></th>
		</tr>		
		
		<?php  
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['SetGoodsMstView'];
		  	echo "<tr>".
		  	         "<td>{$atr['id']}</td>".
		  	         "<td><a href='".$html->url('editSetGoods')."/{$atr['id']}'>{$atr['set_goods_id']}</a></td>".		  	   
		  	         "<td>".$common->evalNbsp($atr['set_goods_cd'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['set_goods_nm'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['note'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['goods_id'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['goods_cd'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['goods_nm'])."</td>".		  	        
		  	         "<td>".$common->evalNbsp($atr['reg_nm'])."</td>".
                     "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  } 	
		?>
    </table>        

