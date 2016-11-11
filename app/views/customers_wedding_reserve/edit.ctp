<script type='text/javascript'>
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
             title: "処理結果"
    });
    //フォーム送信前操作
	$("#update_btn").click(function(){

	    //if($("#formID").validationEngine('validate')==false){ return false; }
	    StartSubmit();
	});
});

/* 更新処理開始  */
	function StartSubmit(){

	   $(this).simpleLoading('show');

	   var formData = $("#formID").serialize();

	   $.post(<?php echo "'".$html->url("edit")."'" ?>,formData , function(result) {

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
		      $("#result_message img").attr('src', <?php echo "'".$html->webroot("/images/confirm_result.png")."'" ?>);
		  }else{
		      $("#result_message img").attr('src', <?php echo "'".$html->webroot("/images/error_result.png")."'"   ?>);
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
	}
</script>

    <ul class="operate">
     <li><a href="<?php echo $html->url('.') ?>">一覧に戻る</a></li>
    </ul>

    <form id="formID" class="content" method="post" action="" >

        <input type="hidden" name="data[WeddingReservingStateTrn][id]" value="<?php echo $data['WeddingReservingStateTrnView']['id'] ?>" />
		<table class="form" cellspacing="0">
		  <tr>
             <th>挙式日</th>
             <td colspan="3"><?php echo $common->evalNbspForShortDate($data['WeddingReservingStateTrnView']['wedding_dt']) ?></td>
          </tr>

		  <tr>
             <th>教会</th>
             <td><?php echo $data['WeddingReservingStateTrnView']['wedding_place'] ?></td>
             <td colspan="2">
                  <select  name="data[WeddingReservingStateTrn][site_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['site_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['site_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['site_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                 </select>
             </td>
          </tr>

          <tr>
             <th>レセプション</th>
             <td><?php echo $data['WeddingReservingStateTrnView']['reception_place'] ?></td>
             <td>
                  <select  name="data[WeddingReservingStateTrn][reception_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['reception_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['reception_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['reception_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                 </select>
             </td>
          </tr>

          <tr>
             <th>Camera</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][camera]" class="validate[max[10000000]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['camera'] ?>" />

                  <select  name="data[WeddingReservingStateTrn][camera_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['camera_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['camera_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['camera_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                 </select>
             </td>
             <th>備考</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][camera_note]" class="validate[max[50]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['camera_note'] ?>" style="width:200px" />
             </td>
          </tr>

          <tr>
             <th>Hair&Make</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][hair_make]" class="validate[max[10000000]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['hair_make'] ?>" />

                  <select  name="data[WeddingReservingStateTrn][hair_make_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['hair_make_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['hair_make_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['hair_make_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                 </select>
             </td>
             <th>備考</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][hair_make_note]" class="validate[max[50]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['hair_make_note'] ?>" style="width:200px" />
             </td>
          </tr>

          <tr>
             <th>Video</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][video]" class="validate[max[10000000]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['video'] ?>" />

                  <select  name="data[WeddingReservingStateTrn][video_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['video_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['video_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['video_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                 </select>
             </td>
             <th>備考</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][video_note]" class="validate[max[50]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['video_note'] ?>" style="width:200px" />
             </td>
          </tr>

          <tr>
             <th>Flower</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][flower]" class="validate[max[10000000]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['flower'] ?>" />

                  <select  name="data[WeddingReservingStateTrn][flower_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['flower_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['flower_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['flower_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                 </select>
             </td>
             <th>備考</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][flower_note]" class="validate[max[50]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['flower_note'] ?>" style="width:200px" />
             </td>
          </tr>

          <tr>
             <th>Attend</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][attend]" class="validate[max[10000000]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['attend'] ?>" />

                  <select  name="data[WeddingReservingStateTrn][attend_status]">
   			        <option value='<?php echo WS_NO_ORDER ?>' <?php echo $data['WeddingReservingStateTrnView']['attend_status'] == WS_NO_ORDER ? "selected=selected":"" ?>>注文なし</option>
   			        <option value='<?php echo WS_ORDERING ?>' <?php echo $data['WeddingReservingStateTrnView']['attend_status'] == WS_ORDERING ? "selected=selected":"" ?>>予約中</option>
   			        <option value='<?php echo WS_ORDERED ?>'  <?php echo $data['WeddingReservingStateTrnView']['attend_status'] == WS_ORDERED ? "selected=selected":"" ?>>予約済み</option>
                  </select>
             </td>
             <th>備考</th>
             <td><input type="text" name="data[WeddingReservingStateTrn][attend_note]" class="validate[max[50]]"
                        value="<?php echo $data['WeddingReservingStateTrnView']['attend_note'] ?>" style="width:200px" />
             </td>
           </tr>
           <tr>
             <th>Briefing Date</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][briefing_dt]" class="validate[max[10000000]]"
                                    value="<?php echo $data['WeddingReservingStateTrnView']['briefing_dt'] ?>" /></td>
           </tr>
           <tr>
             <th>お礼内容</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][gratitude]" class="validate[max[10000000]]" style='width:300px'
                                    value="<?php echo $data['WeddingReservingStateTrnView']['gratitude'] ?>" /></td>
           </tr>

           <tr>
             <th>Slide Show</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][slide_show]" class="validate[max[10000000]]" style='width:400px'
                                    value="<?php echo $data['WeddingReservingStateTrnView']['slide_show'] ?>" /></td>
           </tr>

           <tr>
             <th>Short Film1</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][short_film1]" class="validate[max[10000000]]" style='width:400px'
                                    value="<?php echo $data['WeddingReservingStateTrnView']['short_film1'] ?>" /></td>
           </tr>
           <tr>
             <th>Short Film2</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][short_film2]" class="validate[max[10000000]]" style='width:400px'
                                    value="<?php echo $data['WeddingReservingStateTrnView']['short_film2'] ?>" /></td>
           </tr>

           <tr>
             <th>Visionari SS</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][visionari_ss]" class="validate[max[10000000]]" style='width:300px'
                                    value="<?php echo $data['WeddingReservingStateTrnView']['visionari_ss'] ?>" /></td>
           </tr>
           <tr>
             <th>Visionari Dater</th>
             <td colspan="3"><input type="text" name="data[WeddingReservingStateTrn][visionari_dater]" class="validate[max[10000000]]" style='width:300px'
                                    value="<?php echo $data['WeddingReservingStateTrnView']['visionari_dater'] ?>" /></td>
           </tr>
          </tr>
	    </table>
   </form>

	    <input type="button" id="update_btn"  class="inputbutton" value="更新" />

<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>
