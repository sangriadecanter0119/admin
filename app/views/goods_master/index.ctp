<?php
$url = $html->url('filterData');
$delete_unused_goods_url = $html->url("deleteGoodsUsingLessThan");
$confirm_image_path = $html->webroot("/images/confirm_result.png");
$error_image_path = $html->webroot("/images/error_result.png");
$upload_url = $html->url("fileUploadForm");

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');
                 }
             }],
              beforeClose: function (event, ui) {
                  $("#result_message span").text("");
		          $("#error_reason").text("");
             },
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             modal: true,
             title: "削除結果"
    });

   //選択カテゴリが変わったら検索して再表示
   $("#goods_ctg").change(function(){
      $("#GoodsMstViewGoodsCtgId").val($(this).val());
      $("#GoodsMstViewGoodsKbnId").val(-1);
      $("#GoodsMstViewVendorId").val(-1);
      $("#GoodsServiceIndexForm").submit();
   });
    $("#goods_kbn").change(function(){
      $("#GoodsMstViewGoodsKbnId").val($(this).val());
      $("#GoodsServiceIndexForm").submit();
   });
    $("#vendor_kbn").change(function(){
      $("#GoodsMstViewVendorId").val($(this).val());
      $("#GoodsServiceIndexForm").submit();
   });
    $("#set_goods_kbn").change(function(){
      $("#GoodsMstViewSetGoodsKbn").val($(this).val());
      $("#GoodsServiceIndexForm").submit();
   });
    $("#internal_pay_flg").change(function(){
      $("#GoodsMstViewInternalPayFlg").val($(this).val());
      $("#GoodsServiceIndexForm").submit();
   });

   /*  ファイル取り込みフォームの表示開始
    -------------------------------------------------*/
    $("#file_upload_link").click(function(){

         $(this).simpleLoading('show');
         $.post("$upload_url",function(html){
             $('body').append(html);
             $(this).simpleLoading('hide');
         });
         return false;
       });

   /* 小頻度使用の商品の削除
   ----------------------------------------------------------------------*/
   $("#delete_unused_goods_link").click(function(){

       $(this).simpleLoading('show');
      $.get("$delete_unused_goods_url",function(result){
		 $(this).simpleLoading('hide');

		 var obj = null;
	      try {
            obj = $.parseJSON(result);
          } catch(e) {

            obj = {};
            obj.result = false;
		    obj.message = "致命的なエラーが発生しました。";
		    obj.reason  = "このダイアログを閉じた後、画面のスクリーンショットを保存して管理者にお問い合わせ下さい。";
		    $("#critical_error").text(result);
          }

		  if(obj.result == 'true'){

		     $("#result_message img").attr('src',"$confirm_image_path");
   		     $("#result_message span").text(obj.message);
		     $("#error_reason").text('合計：' + obj.total + ' 小頻度件数:' + obj.less_used_count + ' 未使用件数：' + obj.unused_goods_count);
		  }else{

		     $("#result_message img").attr('src',"$error_image_path");
		     $("#result_message span").text(obj.message);
		     $("#error_reason").text(obj.reason);
		  }
		     $("#result_dialog").dialog('open');
	   });
   });
});
JSPROG
)) ?>

    <ul class="operate">
     <li><a href="<?php echo $html->url('/systemManager') ?>">戻る</a></li>
     <li><a href="<?php echo $html->url('addGoods') ?>">商品追加</a></li>
     <li><a href="<?php echo $html->url('addSetGoods') ?>">セット商品追加</a></li>
     <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
     <li><a href="#" id='file_upload_link'>EXCEL取り込み</a></li>
    <!-- <li><a href="<?php echo $html->url('duplicateGoods') ?>">商品マスタ複製</a></li> -->
    <!-- <li><a href="#" id="delete_unused_goods_link">使用頻度の少ない商品削除</a></li> -->
    </ul>

     <!-- ページネーションする時にフィルタ条件を引き継ぐためにパラメータを追加 -->
     <?php  $paginator->options(array('url' => array('internal_pay_flg' => $internal_pay_flg,
                                                     'set_goods_kbn'    => $set_goods_kbn,
                                                     'goods_ctg_id'     => $goods_ctg_id,
                                                     'goods_kbn_id'     => $goods_kbn_id,
                                                     'vendor_id'        => $vendor_id
                                )));
     ?>

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
	<?php echo $form->text('GoodsMstView.internal_pay_flg',array('value' => $internal_pay_flg)); ?>
	<?php echo $form->text('GoodsMstView.set_goods_kbn'   ,array('value' => $set_goods_kbn)); ?>
	<?php echo $form->text('GoodsMstView.goods_ctg_id'    ,array('value' => $goods_ctg_id)); ?>
	<?php echo $form->text('GoodsMstView.goods_kbn_id'    ,array('value' => $goods_kbn_id)); ?>
	<?php echo $form->text('GoodsMstView.vendor_id'       ,array('value' => $vendor_id )); ?>
    <?php echo $form->end(); ?>
    </div>


<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
     <table class="filterlist" cellspacing="0">
        <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
		<td>
		 <select id='internal_pay_flg'>
		    <?php
		      if($internal_pay_flg == -1){
		      	echo "<option value='-1' selected>ALL</option>";
		        echo "<option value='1'>YES</option>";
		        echo "<option value='0'>NO</option>";
		      }
		      else if($internal_pay_flg == 1){
		      	echo "<option value='-1'>ALL</option>";
		        echo "<option value='1' selected>YES</option>";
		        echo "<option value='0'>NO</option>";
		      }
		      else{
		      	echo "<option value='-1'>ALL</option>";
		        echo "<option value='1'>YES</option>";
		        echo "<option value='0' selected>NO</option>";
		      }
		    ?>
		 </select>
		</td>
		<td>
		 <select id='set_goods_kbn'>
		   <?php
		      if($set_goods_kbn == -1){
		      	echo "<option value='-1' selected>ALL</option>";
		        echo "<option value='1'>YES</option>";
		        echo "<option value='0'>NO</option>";
		      }
		      else if($set_goods_kbn == 1){
		      	echo "<option value='-1'>ALL</option>";
		        echo "<option value='1' selected>YES</option>";
		        echo "<option value='0'>NO</option>";
		      }
		      else{
		      	echo "<option value='-1'>ALL</option>";
		        echo "<option value='1'>YES</option>";
		        echo "<option value='0' selected>NO</option>";
		      }
		    ?>
		  </select>
		</td>
		<td>
		 <select id='goods_ctg'>
		    <option value='-1' selected>ALL</option>
		   <?php
		  	for($i=0;$i < count($goods_ctg_data);$i++)
		    {
		  	  $atr = $goods_ctg_data[$i]['GoodsCtgMst'];
		  	  if($atr['id'] == $goods_ctg_id){
		  	  	 echo "<option value='".$atr['id']."' selected>{$atr['goods_ctg_nm']}</option>";
		  	  }else{
		         echo "<option value='".$atr['id']."'>{$atr['goods_ctg_nm']}</option>";
		  	  }
		    }
		   ?>
		    </select>
		</td>
		<td>
		 <select id='goods_kbn'>
		    <option value='-1' selected>ALL</option>
		   <?php
		  	for($i=0;$i < count($goods_kbn_data);$i++)
		    {
		  	  $atr = $goods_kbn_data[$i]['LatestGoodsMstView'];
		  	  if($atr['goods_kbn_id'] == $goods_kbn_id){
		  	  	 echo "<option value='".$atr['goods_kbn_id']."' selected>{$atr['goods_kbn_nm']}</option>";
		  	  }else{
		  	  	 echo "<option value='".$atr['goods_kbn_id']."'>{$atr['goods_kbn_nm']}</option>";
		  	  }
		    }
		   ?>
		    </select>
		</td>
		<td>&nbsp;</td>
		<td>
		 <select id='vendor_kbn'>
		     <option value='-1' selected>ALL</option>
		   <?php
		  	for($i=0;$i < count($vendor_kbn_data);$i++)
		    {
		  	  $atr = $vendor_kbn_data[$i]['LatestGoodsMstView'];
		  	  if($atr['vendor_id'] == $vendor_id){
		  	  	echo "<option value='".$atr['vendor_id']."' selected>{$atr['vendor_nm']}</option>";
		  	  }else{
		  	  	echo "<option value='".$atr['vendor_id']."'>{$atr['vendor_nm']}</option>";
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
		</tr>

        <tr>
		<th class='left'><?php echo $paginator->sort('商品コード','goods_cd') ?></th>
		<th>Rev</th>
		<th><?php echo $paginator->sort('国内払い'  ,'internal_pay_flg') ?></th>
		<th><?php echo $paginator->sort('セット商品'  ,'set_goods_kbn') ?></th>
		<th><?php echo $paginator->sort('商品分類名','goods_ctg_nm') ?></th>
		<th><?php echo $paginator->sort('商品区分名','goods_kbn_nm') ?></th>
		<th><?php echo $paginator->sort('商品名'   ,'goods_nm')     ?></th>
		<th><?php echo $paginator->sort('ベンダー名' ,'vendor_nm')    ?></th>
		<th>価格</th>
		<th>原価</th>
		<th>利益</th>
		<th>AWシェア</th>
		<th>RWシェア</th>
		<th>AW取り分</th>
		<th>RW取り分</th>
		<th><?php echo $paginator->sort('登録者'    , 'reg_nm'); ?></th>
		<th><?php echo $paginator->sort('登録日'    , 'reg_dt'); ?></th>
		<th><?php echo $paginator->sort('更新者'    , 'upd_nm'); ?></th>
		<th class="right"><?php echo $paginator->sort('更新日'  , 'upd_dt'); ?></th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++)
		  {
		  	$atr = $data[$i]['LatestGoodsMstView'];
		  	echo "<tr>";

		  	//単品商品
		  	if($atr['set_goods_kbn'] == 0){
		  	   echo "<td class='left'><a href='".$html->url('editGoods')."/{$atr['id']}'>{$atr['goods_cd']}</a></td>";
		  	}
		  	//セット商品
		  	else{
		  	   echo"<td class='left'><a href='".$html->url('editSetGoods')."/{$atr['id']}'>{$atr['goods_cd']}</a></td>";
		  	}

		  	echo "<td>".$atr['revision']."</td>";

		    if($atr['internal_pay_flg'] == 0){
		  		echo "<td>&nbsp;</td>";
		  	}else{
		  		echo "<td>".$html->image('ok.png')."</td>";
		  	}

		  	if($atr['set_goods_kbn'] == 0){
		  		echo "<td>&nbsp;</td>";
		  	}else{
		  		echo "<td>".$html->image('ok.png')."</td>";
		  	}

		  	echo    "<td>".$common->evalNbsp($atr['goods_ctg_nm'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['goods_kbn_nm'])."</td>".
		  	         "<td>".nl2br($common->evalNbsp($atr['goods_nm']))."</td>".
		  	         "<td>".$common->evalNbsp($atr['vendor_nm'])."</td>".
                     "<td>".$common->evalNbsp($atr['price'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['cost'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['profit'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['aw_share'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['rw_share'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['aw_net'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['rw_net'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['reg_nm'])."</td>".
                     "<td>".$common->evalNbspForShortDate($atr['reg_dt'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['upd_nm'])."</td>".
		  	         "<td class='right'>".$common->evalNbspForShortDate($atr['upd_dt'])."</td>".
		  	      "</tr>";
		  }
		?>
    </table>
     <div style="display:none;">
       <form id='year_form' name='year' action="<?php echo $html->url('index') ?>" method='post'>
         <input type='hidden' id='year_input'  name='data[GoodsMst][year]' value='' />
         <input type='submit' id='year_submit' name='year_submit'>
       </form>
     </div>

     <div id="result_dialog" style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
     <div id="critical_error"></div>
</div>

