<?php
   //商品更新URL
  $edit_goods_url = $html->url('editGoods');
  $main_url = $html->url('.');
  $confirm_image_path = $html->webroot("/images/confirm_result.png");
  $error_image_path = $html->webroot("/images/error_result.png");

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

    $(".inputdate").mask("9999-99-99");

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');

                     if($("#result_dialog").data("action").toUpperCase() == "DELETE" ){
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = "$main_url";
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
	        StartSubmit();
	        break;
	    }
		return false;
	});

	$(".inputbutton").click(function(){
	  $("#result_dialog").data("action",$(this).attr("name"));
	});

	/* 更新処理開始  */
	function StartSubmit(){

	   $(this).simpleLoading('show');

	   var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");

	   $.post("$edit_goods_url",formData , function(result) {

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

		      $("#goods_id").val(obj.newId);
		      $("#revision").text(obj.newRevision);
		      $("#hidden_revision").val(obj.newRevision);

		      $("#result_message img").attr('src',"$confirm_image_path");
		      $("#result_dialog").data("status","true");
		  }else{
		      $("#result_message img").attr('src',"$error_image_path");
		      $("#result_dialog").data("status","false");
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
	}
});
JSPROG
)) ?>

    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content" method="post" action="" >

        <input type="hidden" id='goods_id' name="data[GoodsMst][id]" value="<?php echo $data['LatestGoodsMstView']['id'] ?>" />
		<table class="form" cellspacing="0">
		   <tr>
             <th>見積非表示</th>
             <td>
              <?php
   			     if($data['LatestGoodsMstView']['non_display_flg'] == 1){
   			     	echo  "<input type='checkbox' name='data[GoodsMst][non_display_flg]' checked />";
   			     }else{
   			   	    echo  "<input type='checkbox' name='data[GoodsMst][non_display_flg]' />";
   			     }
   			  ?>
             </td>
          </tr>
          <tr>
             <th>商品コード</th>
             <td><?php echo $data['LatestGoodsMstView']['goods_cd'] ?><input type="hidden" name="data[GoodsMst][goods_cd]" value="<?php echo $data['LatestGoodsMstView']['goods_cd'] ?>" /></td>
          </tr>
           <tr>
             <th>リビジョンNO</th>
             <td><span id='revision'><?php echo $data['LatestGoodsMstView']['revision'] ?></span><input type="hidden" id='hidden_revision' name="data[GoodsMst][revision]" value="<?php echo $data['LatestGoodsMstView']['revision'] ?>" /></td>
          </tr>
           <tr>
             <th>有効期限</th>
             <td>
                 <?php
                    if($data['LatestGoodsMstView']['start_valid_dt'] == "1000-01-01"){
                      echo "<input type='text' name='data[GoodsMst][start_valid_dt]' id='start_valid_dt' class='inputdate' style='text-align:center' value='' />";
                    }else{
                      echo "<input type='text' name='data[GoodsMst][start_valid_dt]' id='start_valid_dt' class='inputdate' style='text-align:center' value='{$data['LatestGoodsMstView']['start_valid_dt']}' />";
                    }
                 ?>
                 <span>～</span>
                 <?php
                    if($data['LatestGoodsMstView']['end_valid_dt'] == "9999-12-31"){
                      echo "<input type='text' name='data[GoodsMst][end_valid_dt]' id='end_valid_dt' class='inputdate' style='text-align:center' value='' />";
                    }else{
                      echo "<input type='text' name='data[GoodsMst][end_valid_dt]' id='end_valid_dt' class='inputdate' style='text-align:center' value='{$data['LatestGoodsMstView']['end_valid_dt']}' />";
                    }
                 ?>
              </td>
          </tr>
          <tr>
             <th>商品分類</th>
             <td><?php echo $data['LatestGoodsMstView']['goods_ctg_nm'] ?></td>
          </tr>
          <tr>
             <th>商品区分</th>
             <td><?php echo $data['LatestGoodsMstView']['goods_kbn_nm'] ?><input type="hidden" name="data[GoodsMst][goods_kbn_id]" value="<?php echo $data['LatestGoodsMstView']['goods_kbn_id'] ?>" /></td>
          </tr>
          <tr>
             <th>商品名<span class="necessary">(必須)</span></th>
             <td><textarea name="data[GoodsMst][goods_nm]" id="goods_nm" class="validate[required,maxSize[500]] large-inputcomment" rows="8"><?php echo $data['LatestGoodsMstView']['goods_nm'] ?></textarea></td>
          </tr>
          <tr>
             <th>ベンダー名</th>
             <td>
                 <select  name="data[GoodsMst][vendor_id]">
   			        <?php
   			           for($i=0;$i < count($vendor_list);$i++)
   			           {
   			           	 $atr = $vendor_list[$i]['VendorMst'];

   			           	 if($atr['id'] == $data['LatestGoodsMstView']['vendor_id'])
   			             {
   			               echo "<option value='{$atr['id']}' selected='selected'>{$atr['vendor_nm']}</option>";
   			             }
   			             else
   			            {
   			           	   echo "<option value='{$atr['id']}'>{$atr['vendor_nm']}</option>";
   			             }
   			           }
   			        ?>
                 </select>
             </td>
          </tr>
            <tr>
             <th>国内払い</th>
             <td>
              <?php
   			     if($data['LatestGoodsMstView']['internal_pay_flg'] == 1)
   			     {
   			     	echo  "<input type='checkbox' name='data[GoodsMst][internal_pay_flg]' checked />";
   			     }
   			     else
   			     {
   			   	    echo  "<input type='checkbox' name='data[GoodsMst][internal_pay_flg]' />";
   			     }
   			  ?>
             </td>
          </tr>
          <tr>
             <th>通貨区分</th>
             <td>
               <select name="data[GoodsMst][currency_kbn]">
               <?php
                 if($data['LatestGoodsMstView']['currency_kbn'] == 0)
                 {
                 	echo "<option value='0' selected='selected'>ドルベース</option>";
                 	echo "<option value='1'>円ベース</option>";
                 }
                 else
                 {
                 	echo "<option value='0'>ドルベース</option>";
                 	echo "<option value='1' selected='selected'>円ベース</option>";
                 }
               ?>
               </select>
             </td>
          </tr>
          <tr>
             <th>価格</th>
             <td><input type="text" name="data[GoodsMst][price]" id="goods_price" class="validate[required,custom[number],max[10000000]] inputnumeric"
                        value="<?php echo $data['LatestGoodsMstView']['price'] ?>" /></td>
          </tr>
          <tr>
             <th>原価</th>
             <td><input type="text" name="data[GoodsMst][cost]" id="goods_cost" class="validate[required,custom[number],max[10000000]] inputnumeric"
                        value= "<?php echo $data['LatestGoodsMstView']['cost'] ?>" /></td>
          </tr>
          <tr>
             <th>HIシェア(%)</th>
             <td><input type="text" name="data[GoodsMst][aw_share]" id="aw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[rw_share]] inputnumeric"
                        value="<?php echo $data['LatestGoodsMstView']['aw_share'] *100 ?>" /></td>
          </tr>
          <tr>
             <th>RWシェア(%)</th>
             <td><input type="text" name="data[GoodsMst][rw_share]" id="rw_share" class="validate[required,custom[number],max[100],maxSize[5],rateSumUp[aw_share]] inputnumeric"
                        value="<?php echo $data['LatestGoodsMstView']['rw_share'] *100 ?>" /></td>
          </tr>
	    </table>

	<div class="submit">
	    <input type="submit" id="update"  class="inputbutton"  name="update" value="更新" />
	   	<input type='submit' id='delete'  class='inputbutton'  name='delete' value='削除' />
	</div>
   </form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />データを削除しますがよろしいですか？</p></div>
<div id="critical_error"></div>
