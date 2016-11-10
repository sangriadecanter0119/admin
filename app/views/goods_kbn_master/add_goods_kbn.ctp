
<script type='text/javascript'>
$(function(){
    $("input:submit").button();
    $("#church_code").mask("aa");

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
             title: "処理結果"
    });

    $("#goods_ctg_id").change(function(){SetChurchCode();});

    /* 商品区分登録処理開始 */
    $("#formID").submit(function(){

       if( $("#formID").validationEngine('validate')==false){ return false; }
       $(this).simpleLoading('show');

	   var formData = $("#formID").serialize();

	   $.post(<?php echo "'".$html->url('addGoodsKbn')."'" ?>,formData , function(result) {

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

		  if(obj.result == true){
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);
		  }else{
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
      return false;
    });

    SetChurchCode();
});

function SetChurchCode(){
	if($("#goods_ctg_id").val() == "<?php echo GC_WEDDING; ?>"){
	    $("#church_code_tr").css("display","block");
    }else{
        $("#church_code_tr").css("display","none");
    }
}
</script>

    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content" method="post"  action="" >

		<table class="form" cellspacing="0">
		   <tr>
             <th>商品分類名</th>
             <td>
                <select id="goods_ctg_id" name="data[GoodsKbnMst][goods_ctg_id]">
   			        <?php

   			           for($i=0;$i < count($goods_ctg_list);$i++)
   			           {
   			             $atr = $goods_ctg_list[$i]['GoodsCtgMst'];
   			             echo "<option value='{$atr['id']}'>{$atr['goods_ctg_nm']}</option>";
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
	  	  <tr>
             <th>商品区分名<span class="necessary">(必須)</span></th>
             <td><input type="text" id="goods_kbn"  class="validate[required,maxSize[60]] inputvalue"      name="data[GoodsKbnMst][goods_kbn_nm]" value="" /></td>
          </tr>
          <tr id="church_code_tr" style="display:none">
             <th>教会コード<span class="necessary">(必須)</span></th>
             <td><input type="text" id="church_code"  class="validate[required,maxSize[2] inputshortalpha" name="data[GoodsKbnMst][church_code]" value="" /></td>
          </tr>
	    </table>

	<div class="submit">
		<input type="submit" class="inputbutton" value="追加" />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
