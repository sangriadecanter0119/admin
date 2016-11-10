<?php
$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
   //選択ベンダー区分が変わったら検索して再表示
   $("#goods_ctg").change(function(){
      $("#GoodsKbnMstViewGoodsCtgId").val($(this).val());
      $("#GoodsKbnMstIndexForm").submit();
   });
});
JSPROG
)) ?>

    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('addGoodsKbn') ?>">追加</a></li>
    </ul>

    <!-- ページネーションする時にフィルタ条件を引き継ぐためにパラメータを追加 -->
     <?php  $paginator->options(array('url' => array('goods_ctg_id' => $goods_ctg_id)));  ?>

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
	<?php echo $form->text('GoodsKbnMstView.goods_ctg_id',array('value' => $goods_ctg_id)); ?>
    <?php echo $form->end(); ?>
    </div>

<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
     <table class="filterlist" cellspacing="0">
        <tr>
         <td>&nbsp;</td>
         <td>
          <select id='goods_ctg'>
		    <option value='0'>ALL</option>
		   <?php
		  	for($i=0;$i < count($goods_ctg_data);$i++)
		    {
		  	  $atr = $goods_ctg_data[$i]['GoodsCtgMst'];
		  	  if($atr['id'] == $goods_ctg_id){
		           echo "<option value='".$atr['id']."' selected>{$atr['goods_ctg_nm']}</option>";
		  	  }
		  	  else {
		  	  	   echo "<option value='".$atr['id']."'>{$atr['goods_ctg_nm']}</option>";
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
        </tr>

		<tr>
		<th class="left"><?php echo $paginator->sort('商品区分ID' , 'id'); ?></th>
		<th>商品分類名</th>
		<th><?php echo $paginator->sort('商品区分名' , 'goods_kbn_nm'); ?></th>
		<th><?php echo $paginator->sort('登録者'    , 'reg_nm'); ?></th>
		<th><?php echo $paginator->sort('登録日'    , 'reg_dt'); ?></th>
		<th><?php echo $paginator->sort('更新者'    , 'upd_nm'); ?></th>
		<th class="right"><?php echo $paginator->sort('更新日'  , 'upd_dt'); ?></th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['GoodsKbnMstView'];
		  	echo "<tr>".
		  	         "<td class='left'>{$atr['id']}</td>".
		  	         "<td>{$atr['goods_ctg_nm']}</td>".
		  	         "<td><a href='".$html->url('editGoodsKbn')."/{$atr['id']}'>{$atr['goods_kbn_nm']}</a></td>".
		  	         "<td>{$atr['reg_nm']}</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td class='right'>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  }
		?>
    </table>
</div>

