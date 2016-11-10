<script type='text/javascript'>
$(function(){

	$("#church_code").mask("aa");
    /* カテゴリが以下の場合は削除不可とする(ファイナルシートに関連するため)  */
    if($("#goods_ctg_id").val() == <?php echo GC_LINEN ?>){
       $("#delete").attr("disabled",true);
    }

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');

                     if($("#result_dialog").data("action").toUpperCase() == "DELETE" ){
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = <?php echo "'".$html->url('.')."'" ?>;
                        }
                     }
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

      /* 確認用ダイアログ */
    $("#confirm_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#confirm_dialog").dialog('close');
                     StartSubmit();
                 }
             },
             {
                 text: "CANCEL",
                 click: function () {
                     $("#confirm_dialog").dialog('close');
                 }
             }],
             draggable: false,
             autoOpen: false,
             resizable: false,
             zIndex: 2000,
             width:"350px",
             modal: true,
             title: "確認"
         });

    //フォーム送信前操作
	$("#formID").submit(function(){

	    switch($("#result_dialog").data("action").toUpperCase())
	    {
	     case "DELETE":
	        $("#confirm_dialog").dialog('open');
	        break;
	     case "UPDATE":
	    	if( $("#formID").validationEngine('validate')==false){ return false; }
	        StartSubmit()
	        break;
	    }
		return false;
	});

	$(".inputbutton").click(function(){
	  $("#result_dialog").data("action",$(this).attr("name"));
	});

	SetChurchCode();

	/* 更新処理開始  */
	function StartSubmit(){

	   $(this).simpleLoading('show');

	   var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");

	   $.post(<?php echo "'".$html->url('editGoodsKbn')."'" ?>,formData , function(result) {

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
		      $("#result_dialog").data("status","true");
		  }else{
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
		      $("#result_dialog").data("status","false");
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
	}
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

    <form id="formID" class="content" method="post" action="" >

		<table class="form" cellspacing="0">
	  	  <tr>
	  	     <th>商品区分ID</th>
             <td><input type="hidden" name="data[GoodsKbnMst][id]" value="<?php echo $data['GoodsKbnMst']['id'] ?>" /><?php echo $data['GoodsKbnMst']['id'] ?></td>
          </tr>
          <tr>
             <th>商品分類名</th>
             <td>
                     <?php
   			           for($i=0;$i < count($goods_ctg_list);$i++)
   			           {
   			             $atr = $goods_ctg_list[$i]['GoodsCtgMst'];
   			             if($atr['id'] == $data['GoodsKbnMst']['goods_ctg_id'])
   			             {
   			             	echo "<input type='hidden' id='goods_ctg_id' name='data[GoodsKbnMst][goods_ctg_id]' value='{$atr['id']}' />{$atr['goods_ctg_nm']}</td>";
   			             	break;
   			             }
   			           }
   			        ?>
   			 </td>
   		  </tr>
          <tr>
             <th>商品区分名<span class="necessary">(必須)</span></th>
             <td><input type="text" id="goods_kbn" name="data[GoodsKbnMst][goods_kbn_nm]" class="validate[required,maxSize[60]] inputvalue" value="<?php echo $data['GoodsKbnMst']['goods_kbn_nm'] ?>" /></td>
          </tr>
          <tr id="church_code_tr" style="display:none">
             <th>教会コード<span class="necessary">(必須)</span></th>
             <td><input type="text" name="data[GoodsKbnMst][church_code]" id="church_code" class="validate[required,maxSize[2] inputshortalpha" value="<?php echo $data['GoodsKbnMst']['church_code'] ?>"/></td>
          </tr>
	    </table>

	<div class="submit">
	    <input type="submit" id="update"  class="inputbutton"  name="update" value="更新" />
	    <?php echo "<input type='submit' id='delete'  class='inputbutton'  name='delete' value='削除' />"; ?>
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
