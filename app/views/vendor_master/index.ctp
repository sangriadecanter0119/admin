<?php 
$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
   //選択ベンダー区分が変わったら検索して再表示
   $("#vendor_kbn").change(function(){      
      $("#VendorMstViewVendorKbnId").val($(this).val());
      $("#VendorMstIndexForm").submit();      
   });
});
JSPROG
)) ?>

<ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('addVendor') ?>">追加</a></li>
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
   
   <!-- フィルター用の条件を保持   -->
   <div style="display:none;">
    <?php echo $form->create(null); ?>
	<?php echo $form->text('VendorMstView.vendor_kbn_id'); ?>	
    <?php echo $form->end(); ?>
   </div>
  
   <!-- ページネーションする時にフィルタ条件を引き継ぐためにパラメータを追加 -->
    <?php  $paginator->options(array('url' => array('vendor_kbn_id' => $vendor_kbn_id))); ?>
 
    
<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
     <table class="filterlist" cellspacing="0">
        <tr>
        <td>&nbsp;</td>
        <td>
          <select id='vendor_kbn'>
		    <option value='0'>ALL</option>
		   <?php  
		  	for($i=0;$i < count($vendor_kbn_data);$i++)
		    {
		  	  $atr = $vendor_kbn_data[$i]['VendorKbnMst'];
		  	  if($atr['id'] == $vendor_kbn_id){
		           echo "<option value='".$atr['id']."' selected>{$atr['vendor_kbn_nm']}</option>";
		  	  }
		  	  else {
		  	  	   echo "<option value='".$atr['id']."'>{$atr['vendor_kbn_nm']}</option>";
		  	  }
		  	}		  	   	
		   ?>	
	      </select>	
	    </td>	
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>
	    <td>&nbsp;</td>    
	    <td>&nbsp;</td> 
        </tr>
        
		<tr>
		<th class='left'><?php echo $paginator->sort('ベンダーID'   , 'id'); ?></th>
		<th><?php echo $paginator->sort('ベンダー区分'  , 'vendor_kbn_nm'); ?></th>
		<th><?php echo $paginator->sort('会社名'  , 'vendor_nm'); ?></th>
		<th><?php echo $paginator->sort('担当者名', 'attend_nm'); ?></th>
		<th><?php echo $paginator->sort('国区分'  , 'nation_kbn'); ?></th>
		<th>郵便番号</th>
		<th>住所</th>
		<th>電話番号</th>
		<th>携帯番号</th>
		<th>FAX番号</th>
		<th>E-MAIL</th>
		<th>携帯MAIL</th>
		<th>備考</th>		
		<th><?php echo $paginator->sort('登録者'  , 'reg_nm'); ?></th>
		<th><?php echo $paginator->sort('登録日時', 'reg_dt'); ?></th>
		<th><?php echo $paginator->sort('更新者'  , 'upd_nm'); ?></th>
		<th class='right'><?php echo $paginator->sort('更新日時', 'upd_dt'); ?></th>
		</tr>		
		
		<?php  		
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['VendorMstView'];
		  	echo "<tr>".
		  	         "<td class='left'>{$atr['id']}</td>".
		  	         "<td>".$common->evalNbsp($atr['vendor_kbn_nm'])."</td>";
		  	
		  	/*
		  	 *  ベンダーIDが[1]は特別値として編集不可とする。 
		  	 *    セット商品:セット商品全体としてのベンダーは存在しないため、ID[1]を設定しておく
		  	 *    通常商品:ベンダーが未確定の場合
		  	 */
		  	if($atr['id'] == 1)
		  	{
		  		 echo "<td>{$atr['vendor_nm']}</td>";
		  	}
		  	else
		  	{
		  		 echo "<td><a href='".$html->url('editVendor')."/{$atr['id']}'>{$atr['vendor_nm']}</a></td>";
		  	}
		  	
		  	echo     "<td>".$common->evalNbsp($atr['attend_nm'])."</td>";
		  	
		  	if($atr['nation_kbn'] == 0){
		  		echo  "<td>国外</td>";
		  	}else{
		  		echo  "<td>国内</td>";
		  	}
		  			  	        
		  	echo     "<td>".$common->evalNbsp($atr['zip_cd'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['address'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['phone_no'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['cell_no'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['fax_no'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['email'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['phone_mail'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['note'])."</td>".		  	        
		  	         "<td>".$common->evalNbsp($atr['reg_nm'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td class='right'>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  } 	
		?>
    </table>        
</div>
