    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <!-- <li><a href="<?php echo $html->url('addGoodsCategory') ?>">追加</a></li>-->
    </ul>
  
<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >    
     <table class="list" cellspacing="0">
		<tr>
		<th>商品分類ID</th>
		<th>商品分類名</th>
		<th>登録者</th>
		<th>登録日時</th>
		<th>更新者</th>
		<th>更新日時</th>
		</tr>		
		
		<?php  
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['GoodsCtgMst'];
		  	echo "<tr>".
		  	         "<td>{$atr['id']}</td>".
		  	         "<td><a href='".$html->url('editGoodsCategory')."/{$atr['id']}'>{$atr['goods_ctg_nm']}</a></td>".
		  	         "<td>{$atr['reg_nm']}</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  } 	
		?>
    </table>    
</div>    

