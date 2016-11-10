    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('addVendorCategory') ?>">追加</a></li>
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
		<th><?php echo $paginator->sort('ベンダー区分ID' , 'VendorKbnMst.id'); ?></th>
		<th><?php echo $paginator->sort('ベンダー区分名' , 'VendorKbnMst.vendor_kbn_nm'); ?></th>
		<th><?php echo $paginator->sort('登録者'      , 'VendorKbnMst.reg_nm'); ?></th>
		<th><?php echo $paginator->sort('登録日時'    , 'VendorKbnMst.reg_dt'); ?></th>
		<th><?php echo $paginator->sort('更新者'      , 'VendorKbnMst.upd_nm'); ?></th>
		<th><?php echo $paginator->sort('更新日時'    , 'VendorKbnMst.upd_dt'); ?></th>
		</tr>		
		
		<?php  
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['VendorKbnMst'];
		  	echo "<tr>".
		  	         "<td>{$atr['id']}</td>";
		     /*
		  	 *  ベンダー区分IDが[1]は特別値として編集不可とする。 
		  	 *    セット商品:セット商品全体としてのベンダーは存在しないため、ID[1]を設定しておく
		  	 *    通常商品:ベンダーが未確定の場合
		  	 */
		  	if($atr['id'] == 1)
		  	{
		  		 echo "<td>{$atr['vendor_kbn_nm']}</td>";
		  	}
		  	else
		  	{
		  		 echo "<td><a href='".$html->url('editVendorCategory')."/{$atr['id']}'>{$atr['vendor_kbn_nm']}</a></td>";
		  	}
		  	 echo    "<td>{$atr['reg_nm']}</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  } 	
		?>
    </table>        
</div>

