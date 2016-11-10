<script type='text/javascript'>
$(function(){

      //入力マスク
	   $(".datepicker").mask("9999/99/99");
	   $(".inputtime").mask("99:99");

	   //日付入力補助のプラグイン
	   $( ".datepicker" ).datepicker({
	       dateFormat: 'yy/mm/dd',
	       showOtherMonths: true,
	       selectOtherMonths: true,
	       numberOfMonths:3,
	       beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' ) ;}
	   });

    /* 基本情報編集ダイアログ */
	 $("#basic_info_edit_dialog").dialog({
	             buttons: [{
                   text:"更新",
                   id:"update_button",
                   click:function(){
                	   StartSubmit();
	               }
		         }],
	             beforeClose:function(){
	                $("#basic_info_edit_dialog").remove();
	             },
	             draggable: false,
	             autoOpen: true,
	             resizable: false,
	             zIndex: 2000,
	             width:800,
	             height:450,
	          //   position:[($(window).width() / 2) -  (650 / 2) ,($(window).height() / 2) -  (488 / 2)],
	             modal: true,
	             title: "顧客基本情報編集画面"
	 });
	 //延期やキャンセルの場合は更新不可
	 if($("#status_id").val() == <?php echo CS_CANCEL  ?> || $("#status_id").val() == <?php echo CS_POSTPONE  ?>){
	    $("#update_button").css('display','none');
	 }

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

     /* ステータスが挙式完了・未払の時のみステータスを変更可とする
     --------------------------------------------------------*/
    if($("#status_id").val() == <?php echo CS_UNPAIED ?> ){
       $("#status_id_select").css("display","inline");
       $("#status_nm").css("display","none");
    }else{
       $("#status_id_select").css("display","none");
       $("#status_nm").css("display","inline");
    }

    /* ステータス変更時の設定
    ----------------------------------------------------------*/
    $("#status_id_select").change(function(){
       $("#status_id").val($(this).val());
       $("#status_nm_hidden").val($("#status_id_select option:selected").text());
    });

    setFieldDisableOrAble();
});

function StartSubmit(){

	$(this).simpleLoading('show');

	$("#formID input[type=text]").each(function(){
        $(this).attr("disabled",false);
    });

	var formData = $("#formID").serialize();

    $.post(<?php echo "'".$html->url('basicInfoEditForm')."'" ?>, formData , function(result) {

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

           //当画面に更新内容を設定
           /*
           if(obj.status_id == <?php echo CS_UNPAIED ?>){
        	   $("#status_id_select").css("display","inline");
               $("#status_nm").css("display","none");
               $("#status_id_select").val(obj.status_id);
           }else{
               $("#status_id_select").css("display","none");
               $("#status_nm").css("display","inline");
           }
           */

	       $("#status_nm").text(obj.status_nm);
	       $("#status_nm_hidden").val(obj.status_nm);
	       $("#status_id").val(obj.status_id);
	       $("#customer_cd").text(obj.customer_cd);
	       $("#customer_cd_hidden").val(obj.customer_cd);
	       $("#contracting_dt").text(obj.contracting_dt == null ? "" : obj.contracting_dt.substr(0,10));
	       $("#contracting_dt_hidden").val(obj.contracting_dt == null ? "" : obj.contracting_dt);

	       //INDEX画面にの項目に更新内容を設定
	       $("#customer_cd_view").text($("#customer_cd_hidden").val());
	       $("#status_view").text($("#status_nm_hidden").val());
	       $("#wedding_dt_view").text($("#wedding_dt").val());
	       $("#wedding_place_view").text($("#wedding_place").val());
	       $("#wedding_time_view").text($("#wedding_time").val());
	       $("#reception_place_view").text($("#recep_place").val());
	       $("#reception_time_view").text($("#recep_time").val());

	       setFieldDisableOrAble();

	     }else{
	       $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
	     }
           $("#result_message span").text(obj.message);
           $("#error_reason").text(obj.reason);
           $("#result_dialog").dialog('open');
   });
}


/*
-------------------------------------------------*/
function setFieldDisableOrAble(){

	var status_id = $("#status_id").val();

	if(status_id >= <?php echo CS_INVOICED ?>){
        $("#first_contact_dt").attr('disabled',true);
       // $("#first_visited_dt").attr('disabled',true);
       // $("#estimate_issued_dt").attr('disabled',true);
        $("#wedding_dt").attr('disabled',true);
        $("#wedding_place").attr('disabled',true);
        $("#wedding_time").attr('disabled',true);
        $("#recep_place").attr('disabled',true);
        $("#recep_time").attr('disabled',true);

	}
	else if(status_id == <?php echo CS_CONTRACTED ?>){
       // $("#first_contact_dt").attr('disabled',true);
        // $("#first_visited_dt").attr('disabled',true);
        // $("#estimate_issued_dt").attr('disabled',true);
         $("#wedding_dt").attr('disabled',false);
         $("#wedding_place").attr('disabled',false);
         $("#wedding_time").attr('disabled',false);
         $("#recep_place").attr('disabled',false);
         $("#recep_time").attr('disabled',false);

 	}
	else if(status_id == <?php echo CS_CONTRACTING ?>){
       // $("#first_contact_dt").attr('disabled',true);
       // $("#first_visited_dt").attr('disabled',true);
       // $("#estimate_issued_dt").attr('disabled',true);
        $("#wedding_dt").attr('disabled',false);
        $("#wedding_place").attr('disabled',false);
        $("#wedding_time").attr('disabled',false);
        $("#recep_place").attr('disabled',false);
        $("#recep_time").attr('disabled',false);

	}else if(status_id == <?php echo CS_ESTIMATED ?>){
      //  $("#first_contact_dt").attr('disabled',true);
      //  $("#first_visited_dt").attr('disabled',true);
      //  $("#estimate_issued_dt").attr('disabled',false);
        $("#wedding_dt").attr('disabled',false);
        $("#wedding_place").attr('disabled',false);
        $("#wedding_time").attr('disabled',false);
        $("#recep_place").attr('disabled',false);
        $("#recep_time").attr('disabled',false);

    }else{
       //  $("#first_contact_dt").attr('disabled',false);
         $("#first_visited_dt").attr('disabled',false);
      //   $("#estimate_issued_dt").attr('disabled',false);
         $("#wedding_dt").attr('disabled',true);
         $("#wedding_place").attr('disabled',true);
         $("#wedding_time").attr('disabled',true);
         $("#recep_place").attr('disabled',true);
         $("#recep_time").attr('disabled',true);
	}
}
</script>

<div id="basic_info_edit_dialog">

  <form id="formID" class="content" >
   <table class="form">
	    <tr>
	       <th>顧客番号</th>
	       <td colspan="5">
	           <span id="customer_cd"><?php echo $common->evalNbsp($data['CustomerMstView']['customer_cd']) ?></span>
	           <input type="hidden" id="customer_cd_hidden" name="data[CustomerMst][customer_cd]"  value='<?php echo $common->evalNbsp($data['CustomerMstView']['customer_cd']) ?>' />
	       </td>
	    </tr>
	    <tr>
	       <th>顧客名</th>
	       <td colspan="5">
	           <?php
	               echo $common->evalNbsp($data['CustomerMstView']['grmls_kj']).' '.$common->evalNbsp($data['CustomerMstView']['grmfs_kj']);
	           ?>
	           <input type="hidden"  name="data[CustomerMst][id]"  class="inputname" value='<?php echo $common->evalNbsp($data['CustomerMstView']['id']) ?>' />
	       </td>
	    </tr>
	    <tr>
	       <th>ステータス</th>
	       <td colspan="5">
	        <?php
	           if($data['CustomerMstView']['status_id'] == CS_UNPAIED){
                 	echo "<select id='status_id_select' style='width:200px;display:none'>";
               		for($i=0;$i < count($status_list);$i++){
               			if($status_list[$i]['CustomerStatusMst']['id'] == $data['CustomerMstView']['status_id']){
               				echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' selected>{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";

               			}else if($status_list[$i]['CustomerStatusMst']['id'] == CS_PAIED){
               		    	echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' >{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";
               			}
               		 }
                     echo "</select>";
               }
             ?>
                <span id='status_nm'><?php echo $common->evalNbsp($data['CustomerMstView']['status_nm']) ?></span>
		        <input type='hidden' id='status_id' name='data[CustomerMst][status_id]'  value='<?php echo $common->evalNbsp($data['CustomerMstView']['status_id']) ?>' />
		        <input type='hidden' id='status_nm_hidden' name='data[CustomerMst][status_nm]'  value='<?php echo $common->evalNbsp($data['CustomerMstView']['status_nm']) ?>' />
	       </td>
	    </tr>
	    <tr>
	       <th>新規担当者</th>
	       <td>
	       <select id="first_contact_person_nm" name="data[CustomerMst][first_contact_person_nm]" style="width:100px">
	       <option value=''></option>
	       <?php
		           for($i=0;$i < count($attendant_list);$i++){
                      if($data['CustomerMstView']['first_contact_person_nm'] == $attendant_list[$i]){
                      	echo "<option value='$attendant_list[$i]' selected>{$attendant_list[$i]}</option>";
                      }else{
                      	echo "<option value='$attendant_list[$i]'>{$attendant_list[$i]}</option>";
                      }
		           }
		   ?>
		   </select>
           </td>

	       <th>プラン担当者</th>
	       <td>
	       <select id="process_person_nm" name="data[CustomerMst][process_person_nm]" style="width:100px">
	       <option value=''></option>
	       <?php
		           for($i=0;$i < count($attendant_list);$i++){
                      if($data['CustomerMstView']['process_person_nm'] == $attendant_list[$i]){
                      	echo "<option value='$attendant_list[$i]' selected>{$attendant_list[$i]}</option>";
                      }else{
                      	echo "<option value='$attendant_list[$i]'>{$attendant_list[$i]}</option>";
                      }
		           }
		   ?>
		   </select>
	       </td>
	    </tr>
	    <tr>
	       <th>問い合わせ日</th>
	       <td><span style='font-size:0.8em'><?php echo $common->evalForShortDate($data['CustomerMstView']['first_contact_dt']) ?></span>
	           <input type='hidden' name='data[CustomerMst][first_contact_dt]' value='<?php echo $data['CustomerMstView']['first_contact_dt'] ?>' />
	       </td>
	       <th>新規接客日</th>
	       <td>
	      	  <input type='text'  id='first_visited_dt' name='data[CustomerMst][first_visited_dt]' class='validate[required,custom[date]] inputdate datepicker' style='font-size:0.8em' value='<?php echo $common->evalForShortDate($data['CustomerMstView']['first_visited_dt']) ?>' />
	        </td>
	       <th>見積提示日</th>
	       <td><span style='font-size:0.8em'><?php echo $common->evalForShortDate($data['CustomerMstView']['estimate_issued_dt']) ?></span></td>
	    </tr>
	    <tr>
	       <th>仮約定日</th>
	       <td><span id="contracting_dt" style='font-size:0.8em'><?php echo $common->evalForShortDate($data['CustomerMstView']['contracting_dt']) ?></span>
	           <input type='hidden' id="contracting_dt_hidden" name='data[CustomerMst][contracting_dt]' value='<?php echo $common->evalForShortDate($data['CustomerMstView']['contracting_dt']) ?>' />
	       </td>
	       <th>成約日</th><td><span style='font-size:0.8em'><?php echo $common->evalForShortDate($contracted_dt) ?></span></td>
	       <th>請求書発行日</th><td><span style='font-size:0.8em'><?php echo $common->evalForShortDate($invoice_issued_dt) ?></span></td>
	    </tr>
	    <tr>
	       <th>挙式日</th>
	       <td class="inputdate" colspan="5">
	       <?php
	          if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	             echo "<input type='text' id='wedding_dt' name='data[CustomerMst][wedding_planned_dt]' class='validate[optional,custom[date]] inputdate datepicker' style='font-size:0.8em' value='{$common->evalForShortDate($data['CustomerMstView']['wedding_dt'])}' />";
	          }else{
	          	 echo "<input type='text' id='wedding_dt' name='data[CustomerMst][wedding_planned_dt]' class='validate[optional,custom[date]] inputdate datepicker' style='font-size:0.8em' value='{$common->evalForShortDate($data['CustomerMstView']['wedding_planned_dt'])}' />";
	          }
	       ?>
	       </td>
	    </tr>
	    <tr>
	       <th>挙式会場</th>
	       <td colspan="3">
	       <?php
	          if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	             echo "<input type='text' id='wedding_place' name='data[CustomerMst][wedding_planned_place]' class='inputtitle'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_place'])}' />";
	          }else{
	          	 echo "<input type='text' id='wedding_place' name='data[CustomerMst][wedding_planned_place]' class='inputtitle'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_planned_place'])}' />";
	          }
	       ?>
           </td>
	       <th>時間</th>
	       <td>
	       <?php
	          if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	             echo "<input type='text' id='wedding_time' name='data[CustomerMst][wedding_planned_time]' class='inputtime'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_time'])}' />";
	          }else{
	          	 echo "<input type='text' id='wedding_time' name='data[CustomerMst][wedding_planned_time]' class='inputtime'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_planned_time'])}' />";
	          }
	       ?>
	       </td>
	    </tr>
	    <tr>
	       <th>レセプション会場</th>
	       <td colspan="3">
	       <?php
	          if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	             echo "<input type='text' id='recep_place' name='data[CustomerMst][reception_planned_place]' class='inputtitle'  value='{$common->evalNbsp($data['CustomerMstView']['reception_place'])}' />";
	          }else{
	          	 echo "<input type='text' id='recep_place' name='data[CustomerMst][reception_planned_place]' class='inputtitle'  value='{$common->evalNbsp($data['CustomerMstView']['reception_planned_place'])}' />";
	          }
	       ?>
           </td>
	       <th>時間</th>
	       <td>
	        <?php
	          if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	             echo "<input type='text' id='recep_time' name='data[CustomerMst][reception_planned_time]' class='inputtime'  value='{$common->evalNbsp($data['CustomerMstView']['reception_time'])}' />";
	          }else{
	          	 echo "<input type='text' id='recep_time' name='data[CustomerMst][reception_planned_time]' class='inputtime'  value='{$common->evalNbsp($data['CustomerMstView']['reception_planned_time'])}' />";
	          }
	       ?>
          </td>
	    </tr>
	  </table>
  </form>

  <div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
  <div id="critical_error"></div>
</div>










