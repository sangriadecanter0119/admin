<?php
//郵便番号から自動住所入力のライブラリ[ajaxzip3]
//echo $html->script("http://ajaxzip3.googlecode.com/svn/trunk/ajaxzip3/ajaxzip3.js",false);
echo $html->script("https://ajaxzip3.github.io/ajaxzip3.js",false);
?>
<script type='text/javascript'>
$(function(){

	$("input:submit").button();

	   //入力マスク
	   $("#grm_birth,#brd_birth,#wedding_dt,#first_visited_dt,#first_contact_dt").mask("9999/99/99");
	   $("#grm_postcode,#brd_postcode").mask("999-9999");
	   $("#recep_time,#wedding_time").mask("99:99");
	   $("#grm_cell,#brd_cell").mask("999-9999-9999");
	   $("#customer_cd").mask("999999-999999");

	   //日付入力補助のプラグイン
	   $( ".datepicker" ).datepicker({
	       dateFormat: 'yy/mm/dd',
	       showOtherMonths: true,
	       selectOtherMonths: true,
	       numberOfMonths:3,
	       beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' ) ;}
	   });

	   //ステータスが成約以前の場合は以下の項目を編集不可にする
	   if($("#status").val() < <?php echo CS_CONTRACTING ?>)
	   {
	     $("#wedding_place").attr("disabled",true);
	     $("#wedding_time").attr("disabled",true);
	     $("#recep_place").attr("disabled",true);
	     $("#recep_time").attr("disabled",true);

	     $("#wedding_place").addClass("inputdisable");
	     $("#wedding_time").addClass("inputdisable");
	     $("#recep_place").addClass("inputdisable");
	     $("#recep_time").addClass("inputdisable");
	   }

	    /* 新郎新婦の各住所の都道府県を検索設定 */
	    $("#grm_pref").children("option").each(function(){
	        if($(this).val() == "<?php echo $data['CustomerMstView']['grm_pref'] ?>"){ $(this).attr("selected",true);   }
	    });
	    $("#brd_pref").children("option").each(function(){
	        if($(this).val() == "<?php echo $data['CustomerMstView']['brd_pref'] ?>"){ $(this).attr("selected",true);   }
	    });

    /* 処理結果用ダイアログ */
    $("#result_dialog").dialog({
             buttons: [{
                 text: "OK",
                 click: function () {
                     $("#result_dialog").dialog('close');

                     if($("#result_dialog").data("action").toUpperCase() == "DELETE" ){
                        if($("#result_dialog").data("status").toUpperCase() == "TRUE"){
                           location.href = <?php echo "'".$html->url('/customersList/index')."'" ?>;
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

    /*  顧客進捗管理用テーブル
    --------------------------------------------------------*/
    var customer_process_id = 100;
    var lastSel = null;
    jQuery("#customer_process_list").jqGrid({
    	url: <?php echo "'".$html->url('feedCustomerProcessList').'/'.$data['CustomerMstView']['id']."'" ?>,
        datatype: 'json',
        mtype: 'POST',
    	//datatype: 'local',
    	//data: mydata,
        colNames: ['日付', 'アクション','メモ','ステータス'],
        colModel: [
                   { name: 'ActionDate' , width: 70 ,formatter:'date',formatoptions:{newformat:"Y/m/d"},editable:true,editoptions:{dataInit:function(elm){
                                                                       $(elm).datepicker({dateFormat:'yy/mm/dd',beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' ) ;}});}}
                   },
                   { name: 'ActionName' , width: 150,editable:true},
                   { name: 'Memo'       , width: 150,editable:true,edittype:'textarea'},
                   { name: 'Status'     , width: 0,hidden:true}
                  ],
        loadError: function(xhr, status, error) {
            alert("データの読み込みに失敗しました。 " + xhr.responseText);
        },
        ondblClickRow:function(id){
            if(id && id!==lastSel){
            	$("#customer_process_list").jqGrid('saveRow',lastSel, false, 'clientArray');
                lastSel=id;
            }
            jQuery('#customer_process_list').editRow(id, true);
        },
        pager: $('#customer_process_list_pager'),
        viewrecords: true,
        loadonce: true,
        rowList: [5,10,20],
        emptyrecords: "NO RECORDS HERE",
        viewrecords: true,
        imgpath: 'themes/basic/images',
        autowidth: true,
        height: '150',
        rownumbers: true
    });

    $(window).resize(function () {
        ResizeGrid();
    });

    /* jqgridを適切なサイズに調整する */
    function ResizeGrid() {
        $("#customer_process_list").setGridWidth($(".headerlegend").width());
      //  $("#customer_process_list").setGridHeight($("#main_content").height() - 70);
        $("table.form").css("width",$(".headerlegend").width() / 2);
    }
    $("table.form").css("width",$(".headerlegend").width() / 2);

    jQuery("#customer_process_list")
    .navGrid('#customer_process_list_pager',{refresh:false,edit:false,add:false,del:false,search:false})
    .navButtonAdd('#customer_process_list_pager',{
       caption:"",
       buttonicon:"ui-icon-plus",
       onClickButton: function(){
    	  customer_process_id += 1;
      	  jQuery("#customer_process_list").jqGrid('addRowData',customer_process_id,{ActionDate:'',ActionName:'',Memo:'',Status:''});
       }
    })
    .navButtonAdd('#customer_process_list_pager',{
        caption:"",
        buttonicon:"ui-icon-trash",
        onClickButton: function(){
    	   var id = $("#customer_process_list").jqGrid("getGridParam", "selrow");
           $("#customer_process_list").jqGrid("delRowData", id);
        }
     });

    //フォーム送信前操作
	$("#formID").submit(function(){

		var ids = $("#customer_process_list").jqGrid("getDataIDs");
		for(i=0;i < ids.length;i++){

			$("#customer_process_list").jqGrid('saveRow',ids[i], false, 'clientArray');
			var data = $("#customer_process_list").jqGrid("getRowData",ids[i]);

			var ActionId     = "<input type='hidden' class='temp_data' name='data[CustomerProcessTrn][" + i + "][id]'        value='" + ids[i] +　"' />";
			var ActionName   = "<input type='hidden' class='temp_data' name='data[CustomerProcessTrn][" + i + "][action_nm]' value='" + data.ActionName +　"' />";
			var ActionDate   = "<input type='hidden' class='temp_data' name='data[CustomerProcessTrn][" + i + "][action_dt]' value='" + data.ActionDate + "' />";
			var ActionMemo   = "<input type='hidden' class='temp_data' name='data[CustomerProcessTrn][" + i + "][note]'      value='" + data.Memo + "' />";
			var ActionStatus = "<input type='hidden' class='temp_data' name='data[CustomerProcessTrn][" + i + "][status]'    value='" + data.Status + "' />";
			$("#formID").append(ActionId);
			$("#formID").append(ActionName);
			$("#formID").append(ActionDate);
			$("#formID").append(ActionMemo);
			$("#formID").append(ActionStatus);
	    }

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

	/* 更新処理開始  */
	function StartSubmit(){

	   $(this).simpleLoading('show');

	   var formData = $("#formID").serialize() + "&submit=" + $("#result_dialog").data("action");

	   $.post(<?php echo "'".$html->url('editCustomer')."'" ?>,formData , function(result) {

	      $(this).simpleLoading('hide');
	      $("#formID .temp_data").remove();
	      //再度読み込む
          $("#customer_process_list").jqGrid("setGridParam", {datatype: 'json'}).trigger("reloadGrid");
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
		      $("#customer_code").text(obj.code);
		      $("#hidden_customer_code").val(obj.code);
		  }else{
		      $("#result_message img").attr('src',<?php echo "'".$html->webroot("/images/error_result.png")."'" ?>);
		      $("#result_dialog").data("status","false");
		  }
	   $("#result_message span").text(obj.message);
	   $("#error_reason").text(obj.reason);
       $("#result_dialog").dialog('open');
     });
	}

	$("#leading2").change(function(){ SetIntroducerField();});
	SetIntroducerField();
});

/* 導線2の選択により紹介者フィールドを可・不可にする
----------------------------------------------------------*/
function SetIntroducerField(){

  if($("#leading2").val() == <?php echo LD2_INTRODUCING ?>){
    $("#introducer").css("display","inline");
    $("#introducer_label").css("display","inline");
  }else{
    $("#introducer").css("display","none");
    $("#introducer_label").css("display","none");
    $("#introducer").val("");
  }
}

</script>

<ul class="operate">
	<li><a href="<?php echo $html->url('index').'/'.$data['CustomerMstView']['id'] ?>">戻る</a></li>
</ul>

<form id="formID" class="content" method="post" name="customer" action="">
     <div style="margin-bottom:10px">
        <?php
   			     if($data['CustomerMstView']['non_display_flg'] == 1){
   			     	echo  "<input type='checkbox' name='data[CustomerMst][non_display_flg]' checked />";
   			     }else{
   			   	    echo  "<input type='checkbox' name='data[CustomerMst][non_display_flg]' />";
   			     }
   	    ?>
   	    <span style="margin-right:10px">非表示</span>

   	    <?php
   			     if($data['CustomerMstView']['contact_prohibition_flg'] == 1){
   			     	echo  "<input type='checkbox' name='data[CustomerMst][contact_prohibition_flg]' checked />";
   			     }else{
   			   	    echo  "<input type='checkbox' name='data[CustomerMst][contact_prohibition_flg]' />";
   			     }
   	    ?>
   	    <span style="margin-right:10px">挙式後の連絡不可</span>

        <span style="margin-left:5px">理由：</span>
        <input type='text' name='data[CustomerMst][contact_prohibition_reason]' value='<?php echo $data['CustomerMstView']['contact_prohibition_reason'] ?>' style="width:250px" />

     </div>

     <fieldset class="headerlegend">
      <legend class="legendtitle">基本事項</legend>
	  <table class="viewheader">
	    <tr>
	       <th>顧客番号： <input type="hidden"  id="hidden_customer_code" name="data[CustomerMst][customer_cd]"  value='<?php echo $data['CustomerMstView']['customer_cd'] ?>' /></th>
	       <td id="customer_code"><?php echo $common->evalNbsp($data['CustomerMstView']['customer_cd']) ?></td>
	       <th>ステータス：</th>
	       <td>
	             <?php
	             //顧客ステータスが「成約」または「請求書発行済」の場合は「延期」や「キャンセル」を選択できるようにする
	             if($data['CustomerMstView']['status_id'] == CS_CONTRACTED || $data['CustomerMstView']['status_id'] == CS_INVOICED){
                   echo "<select name='data[CustomerMst][status_id]' style='width:120px'>";
                   for($i=0;$i < count($status_list);$i++){
	                  if($status_list[$i]['CustomerStatusMst']['id'] == $data['CustomerMstView']['status_id']){
		                 echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' selected>{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";

                      }else if($status_list[$i]['CustomerStatusMst']['id'] == CS_CANCEL || $status_list[$i]['CustomerStatusMst']['id'] == CS_POSTPONE){
		                 echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' >{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";
	                  }
                   }
                   echo "</select>";

                 //顧客ステータスが「キャンセル」の場合は「延期」を選択できるようにする
                 }else if($data['CustomerMstView']['status_id'] == CS_CANCEL){
                   	echo "<select name='data[CustomerMst][status_id]' style='width:120px'>";
                   	for($i=0;$i < count($status_list);$i++){
                   		if($status_list[$i]['CustomerStatusMst']['id'] == $data['CustomerMstView']['status_id']){
                   			echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' selected>{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";

                   		}else if($status_list[$i]['CustomerStatusMst']['id'] == CS_POSTPONE){
                   			echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' >{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";
                   	    }
                   	}
                   	echo "</select>";

                 //顧客ステータスが「延期」の場合は「成約」を選択できるようにする
                 }else if($data['CustomerMstView']['status_id'] == CS_POSTPONE){
                   	echo "<select name='data[CustomerMst][status_id]' style='width:120px'>";
                   	for($i=0;$i < count($status_list);$i++){
                   		if($status_list[$i]['CustomerStatusMst']['id'] == $data['CustomerMstView']['status_id']){
                   			echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' selected>{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";

                   		}else if($status_list[$i]['CustomerStatusMst']['id'] == CS_CONTRACTED){
                   			echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' >{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";
                   	    }
                   	}
                   	echo "</select>";

                 //顧客ステータスが「挙式済み・未入金」の場合は「挙式済・入金完了」を選択できるようにする
                 }else	if($data['CustomerMstView']['status_id'] == CS_UNPAIED){
                   		echo "<select name='data[CustomerMst][status_id]' style='width:120px'>";
                   		for($i=0;$i < count($status_list);$i++){
                   			if($status_list[$i]['CustomerStatusMst']['id'] == $data['CustomerMstView']['status_id']){
                   				echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' selected>{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";

                   			}else if($status_list[$i]['CustomerStatusMst']['id'] == CS_PAIED){
                   		    	echo "<option value='{$status_list[$i]['CustomerStatusMst']['id']}' >{$status_list[$i]['CustomerStatusMst']['status_nm']}</option>";
                   			}
                   		 }
                   echo "</select>";

                 //上記以外の場合はステータスは選択不可とする
                 }else{
		           for($i=0;$i < count($status_list);$i++){
                        if($status_list[$i]['CustomerStatusMst']['id'] == $data['CustomerMstView']['status_id']){
                           echo "<span id='status_".$status_list[$i]['CustomerStatusMst']['id']."' style='display:inline'>{$status_list[$i]['CustomerStatusMst']['status_nm']}</span>";
                           echo "<input type='hidden' id='status' name='data[CustomerMst][status_id]'  value='".$status_list[$i]['CustomerStatusMst']['id']."'>";
                        }else{
                           echo "<span id='status_".$status_list[$i]['CustomerStatusMst']['id']."' style='display:none'>{$status_list[$i]['CustomerStatusMst']['status_nm']}</span>";
                        }
		           }
		         }
		         ?>
	       </td>
	       <th>新規担当者：</th>
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
	     <!--   <th>見積担当者：</th>  <td><input type="text"   id="estimate_created_person_nm" name="data[CustomerMst][estimate_created_person_nm]" class="inputname" value='<?php echo $common->evalNbsp($data['CustomerMstView']['estimate_created_person_nm']) ?>' /></td> -->
	       <th>プラン担当者：</th><td>
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
		   <th>&nbsp;</th><td>&nbsp;</td>
	    </tr>
	    <tr>
	       <th>問い合わせ日：<span class="necessary">【必須】</span></th><td class="inputdate">
	       <?php
	          if($data['CustomerMstView']['status_id'] == CS_CONTACT){
	          	  echo "<input type='text'  id='first_contact_dt' name='data[CustomerMst][first_contact_dt]' class='validate[required,custom[date]] inputdate datepicker' value='{$common->evalForShortDate($data['CustomerMstView']['first_contact_dt'])}' /></td>";
	          }else{
	          	  echo $common->evalForShortDate($data['CustomerMstView']['first_contact_dt']);
	          	  echo "<input type='hidden'  id='first_contact_dt' name='data[CustomerMst][first_contact_dt]' class='validate[required,custom[date]] inputdate datepicker' value='{$common->evalForShortDate($data['CustomerMstView']['first_contact_dt'])}' /></td>";
	          }
	       ?>
           <th>新規接客日：</th><td class="inputdate">
            <?php
	          	  echo "<input type='text'  id='first_visited_dt' name='data[CustomerMst][first_visited_dt]' class='inputdate datepicker' value='{$common->evalForShortDate($data['CustomerMstView']['first_visited_dt'])}' /></td>";
	        ?>
	       <th>見積発行日：</th><td><?php echo $common->evalForShortDate($data['CustomerMstView']['estimate_issued_dt']) ?></td>
           <th>仮約定日：</th><td><?php echo $common->evalForShortDate($data['CustomerMstView']['contracting_dt']) ?></td>
	       <th>成約日：</th><td><?php echo $common->evalForShortDate($contracted_dt) ?></td>
	       <th>請求書発行日：</th><td><?php echo $common->evalForShortDate($invoice_issued_dt) ?></td>
	    </tr>
	    <tr>
	       <th>挙式日：</th>
	       <td class="inputdate">
	          <?php
	               if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	               	  echo "<input type='hidden' name='data[CustomerMst][wedding_planned_dt]'  value='{$common->evalForShortDate($data['CustomerMstView']['wedding_dt'])}' />";
	                  echo $common->evalForShortDate($data['CustomerMstView']['wedding_dt']);
                   }else{
                      echo "<input type='text' name='data[CustomerMst][wedding_planned_dt]' class='inputdate datepicker' value='{$common->evalForShortDate($data['CustomerMstView']['wedding_planned_dt'])}' />";
	               }
	           ?>
	       </td>
	       <th>挙式会場：</th>
	       <td colspan="3">
	           <?php
	               if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	               	  echo "<input type='hidden' name='data[ContractTrn][wedding_place]'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_place'])}' />";
	                  echo $common->evalNbsp($data['CustomerMstView']['wedding_place']);
                   }else{
                      echo "<input type='hidden' name='data[CustomerMst][wedding_planned_place]'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_planned_place'])}' />";
                      echo $common->evalNbsp($data['CustomerMstView']['wedding_planned_place']);
	               }
	           ?>
	       </td>
	       <th>時間：</th>
	       <td class="inputdate">
	            <?php
	               if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	               	  echo "<input type='hidden' name='data[ContractTrn][wedding_time]'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_time'])}' />";
	                  echo $common->evalNbsp($data['CustomerMstView']['wedding_time']);
                   }else{
                      echo "<input type='hidden' name='data[CustomerMst][wedding_planned_time]'  value='{$common->evalNbsp($data['CustomerMstView']['wedding_planned_time'])}' />";
                      echo $common->evalNbsp($data['CustomerMstView']['wedding_planned_time']);
	               }
	            ?>
	       </td>
	    </tr>
	    <tr>
	       <td></td><td></td>
	       <th>レセプション会場：</th>
	       <td colspan="3">
	          <?php
	               if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	               	  echo "<input type='hidden' name='data[ContractTrn][reception_place]'  value='{$common->evalNbsp($data['CustomerMstView']['reception_place'])}' />";
	                  echo $common->evalNbsp($data['CustomerMstView']['reception_place']);
                   }else{
                      echo "<input type='hidden' name='data[CustomerMst][reception_planned_place]'  value='{$common->evalNbsp($data['CustomerMstView']['reception_planned_place'])}' />";
                      echo $common->evalNbsp($data['CustomerMstView']['reception_planned_place']);
	               }
	            ?>
           </td>
	       <th>時間：</th>
	       <td class="inputdate">
	           <?php
	               if($data['CustomerMstView']['status_id'] >= CS_CONTRACTING){
	               	  echo "<input type='hidden' name='data[ContractTrn][reception_time]'  value='{$common->evalNbsp($data['CustomerMstView']['reception_time'])}' />";
	                  echo $common->evalNbsp($data['CustomerMstView']['reception_time']);
                   }else{
                      echo "<input type='hidden' name='data[CustomerMst][reception_planned_time]'  value='{$common->evalNbsp($data['CustomerMstView']['reception_planned_time'])}' />";
                      echo $common->evalNbsp($data['CustomerMstView']['reception_planned_time']);
	               }
	            ?>
          </td>
	    </tr>
	    <tr>
	       <th>導線1：</th>
	       <td>
	       <select name="data[CustomerMst][leading1]" style="width:100px">
	       <?php
		           for($i=0;$i < count($leading1_list);$i++){
                      if($data['CustomerMstView']['leading1'] == $i){
                      	echo "<option value='$i' selected>{$leading1_list[$i]}</option>";
                      }else{
                      	echo "<option value='$i'>{$leading1_list[$i]}</option>";
                      }
		           }
		   ?>
		   </select>
	       </td>

	       <th>導線2：</th>
	       <td>
	       <select id="leading2" name="data[CustomerMst][leading2]" style="width:100px">
	       <option value=''></option>
	       <?php
		           for($i=0;$i < count($leading2_list);$i++){
                      if($data['CustomerMstView']['leading2'] == $i){
                      	echo "<option value='$i' selected>{$leading2_list[$i]}</option>";
                      }else{
                      	echo "<option value='$i'>{$leading2_list[$i]}</option>";
                      }
		           }
		   ?>
		   </select>
		   </td>
           <th id="introducer_label">紹介者</th>
           <td colspan="2"><input type="text" id="introducer" name="data[CustomerMst][introducer]" value='<?php echo $data['CustomerMstView']['introducer'] ?>' style="width:130px" /></td>
	       <td colspan="2"></td>
	    </tr>
	  </table>
	</fieldset>

    <fieldset class="headerlegend" style="padding:0 10px 10px 10px">
       <legend class="legendtitle" style="padding:10px">顧客対応履歴</legend>
       <table id="customer_process_list"></table>
       <div   id="customer_process_list_pager"  style="text-align:center;"></div>
     </fieldset>

<fieldset class="headerlegend" style="padding:0 10px 10px 10px">
       <legend class="legendtitle" style="padding:10px">顧客情報</legend>
<table border="0" cellpadding="0" cellspacing="10">
<tr>
<td>
    <table class="form" cellspacing="0">
           <tr>
                <th>【新郎】</th>
                <td colspan="3"></td>
           </tr>
           <tr>
                <th>姓漢字</th>
                <td><input type="text" name="data[CustomerMst][grmls_kj]" id="grmls_kj" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grmls_kj'] ?>" />
                    <input type="radio" value="0" name="data[CustomerMst][prm_lastname_flg]" <?php if($data['CustomerMstView']['prm_lastname_flg'] == 0){echo "checked";}?>/>代表
                </td>
                <th>名漢字</th>
                <td><input type="text" name="data[CustomerMst][grmfs_kj]" id="grmfs_kj" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grmfs_kj']?>" /></td>
           </tr>
           <tr>
                <th>姓カナ</th>
                <td><input type="text" name="data[CustomerMst][grmls_kn]" id="grmls_kn" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grmls_kn'] ?>" /></td>
                <th>名カナ</th>
                <td><input type="text" name="data[CustomerMst][grmfs_kn]" id="grmfs_kn" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grmfs_kn'] ?>" /></td>
           </tr>
	   	   <tr>
                <th>姓ローマ字</th>
                <td><input type="text" name="data[CustomerMst][grmls_rm]" id="grmls_rm" class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grmls_rm'] ?>" /></td>
                <th>名ローマ字</th>
                <td><input type="text" name="data[CustomerMst][grmfs_rm]" id="grmfs_rm" class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grmfs_rm']?>" /></td>
           </tr>
           <tr>
                <th>誕生日</th>
                <td colspan="3"><input type="text" name="data[CustomerMst][grmbirth_dt]" id="grm_birth" class="validate[optional,custom[date]] inputdate" value="<?php echo $common->evalForShortDate($data['CustomerMstView']['grmbirth_dt'])?>" /></td>
           </tr>
		   <tr>
                <th>郵便番号</th>
                <td colspan="3">
			    <input type="text" name="data[CustomerMst][grm_zip_cd]" id="grm_postcode" class="validate[optional,custom[postcode]] inputpostcode" value="<?php echo $data['CustomerMstView']['grm_zip_cd']?>"
			           onKeyUp="AjaxZip3.zip2addr(this,'','data[CustomerMst][grm_pref]','data[CustomerMst][grm_city]','data[CustomerMst][grm_address]','data[CustomerMst][grm_address]');">
                <input type="radio" value="0" name="data[CustomerMst][prm_address_flg]" <?php if($data['CustomerMstView']['prm_address_flg'] == 0){echo "checked";}?>/>代表
                </td>
		   </tr>
           <tr>
                 <th>都道府県</th>
                 <td colspan="3">
                  <select id="grm_pref" name="data[CustomerMst][grm_pref]">
                    <option value="" selected></option>
			     	<option value="北海道">北海道</option><option value="青森県">青森県</option>
					<option value="岩手県">岩手県</option><option value="宮城県">宮城県</option>
					<option value="秋田県">秋田県</option><option value="山形県">山形県</option>
					<option value="福島県">福島県</option><option value="茨城県">茨城県</option>
					<option value="栃木県">栃木県</option><option value="群馬県">群馬県</option>
					<option value="埼玉県">埼玉県</option><option value="千葉県">千葉県</option>
					<option value="東京都">東京都</option><option value="神奈川県">神奈川県</option>
					<option value="新潟県">新潟県</option><option value="富山県">富山県</option>
					<option value="石川県">石川県</option><option value="福井県">福井県</option>
					<option value="山梨県">山梨県</option><option value="長野県">長野県</option>
					<option value="岐阜県">岐阜県</option><option value="静岡県">静岡県</option>
					<option value="愛知県">愛知県</option><option value="三重県">三重県</option>
					<option value="滋賀県">滋賀県</option><option value="京都府">京都府</option>
					<option value="大阪府">大阪府</option><option value="兵庫県">兵庫県</option>
					<option value="奈良県">奈良県</option><option value="和歌山県">和歌山県</option>
					<option value="鳥取県">鳥取県</option><option value="島根県">島根県</option>
					<option value="岡山県">岡山県</option><option value="広島県">広島県</option>
					<option value="山口県">山口県</option><option value="徳島県">徳島県</option>
					<option value="香川県">香川県</option><option value="愛媛県">愛媛県</option>
					<option value="高知県">高知県</option><option value="福岡県">福岡県</option>
					<option value="佐賀県">佐賀県</option><option value="長崎県">長崎県</option>
					<option value="熊本県">熊本県</option><option value="大分県">大分県</option>
					<option value="宮崎県">宮崎県</option><option value="鹿児島県">鹿児島県</option>
					<option value="沖縄県">沖縄県</option>
				 </select>
		    	</td>
           </tr>
           <tr>
                 <th>市町村区</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_city]" id="grm_city_kj" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['grm_city']?>" />
                 </td>
           </tr>
           <tr>
                 <th>住所番地</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_street]" id="grm_street_kj" class="validate[optional,maxSize[60]] inputtitle" value="<?php echo $data['CustomerMstView']['grm_street']?>" />
                 </td>
           </tr>
		   <tr>
                 <th>アパート・マンション</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_apart]" id="grm_part_kj" class="validate[optional,maxSize[20]] inputtitle" value="<?php echo $data['CustomerMstView']['grm_apart']?>" />
			     </td>
           </tr>
           <tr>
                 <th>住所(ローマ字)</th><td colspan="3">
			     <input type="text" name="data[CustomerMst][grm_address_rm]" id="grm_address_rm" class="validate[optional,maxSize[120],custom[onlyLetterNumber]] inputtitle" value="<?php echo $data['CustomerMstView']['grm_address_rm']?>" /></td>
           </tr>
		   <tr>
                 <th>電話番号</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][grm_phone_no]" id="grm_phone" class="validate[optional,custom[phone]] inputphone" value="<?php echo $data['CustomerMstView']['grm_phone_no']?>" />
                 <input type="radio" value="0" name="data[CustomerMst][prm_phone_no_flg]" <?php if($data['CustomerMstView']['prm_phone_no_flg'] == 0){echo "checked";}?>/>代表</td>
            </tr>
		    <tr>
                 <th>携帯電話番号</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][grm_cell_no]" id="grm_cell" class="validate[optional,custom[phone]] inputphone" value="<?php echo $data['CustomerMstView']['grm_cell_no']?>" /></td>
            </tr>
  		    <tr>
                 <th>E-MAILアドレス</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][grm_email]" id="grm_email" class="validate[optional,custom[email]] inputmailaddress" value="<?php echo $data['CustomerMstView']['grm_email']?>" />
                      <input type="radio" value="0" name="data[CustomerMst][prm_email_flg]"  <?php if($data['CustomerMstView']['prm_email_flg'] == 0){echo "checked";}?> />代表</td>
            </tr>
		    <tr>
                 <th>携帯メールアドレス</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][grm_phone_mail]" id="grm_phonemail" class="validate[optional,custom[email]] inputmailaddress" value="<?php echo $data['CustomerMstView']['grm_phone_mail']?>" /></td>
            </tr>
		    <tr>
                  <th>備考</th><td colspan="3"><textarea name="data[CustomerMst][note]" id="note" class="inputcomment" rows="5"><?php echo $data['CustomerMstView']['note'] ?></textarea></td>
            </tr>
	</table>
</td>
<td>
	<table class="form" cellspacing="0">
		   <tr>
                <th>【新婦】</th>
                <td colspan="3"></td>
           </tr>
		   <tr>
                <th>姓漢字</th>
                <td><input type="text" name="data[CustomerMst][brdls_kj]" id="brdls_kj" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brdls_kj'] ?>" />
                    <input type="radio" value="1" name="data[CustomerMst][prm_lastname_flg]" <?php if($data['CustomerMstView']['prm_lastname_flg'] == 1){echo "checked";}?>/>代表
                </td>
                <th>名漢字</th>
                <td><input type="text" name="data[CustomerMst][brdfs_kj]" id="brdfs_kj" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brdfs_kj']?>" /></td>
           </tr>
           <tr>
                <th>姓カナ</th>
                <td><input type="text" name="data[CustomerMst][brdls_kn]" id="brdls_kn" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brdls_kn'] ?>" /></td>
                <th>名カナ</th>
                <td><input type="text" name="data[CustomerMst][brdfs_kn]" id="brdfs_kn" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brdfs_kn'] ?>" /></td>
           </tr>
	   	   <tr>
		        <th>姓ローマ字</th>
                <td><input type="text" name="data[CustomerMst][brdls_rm]"  id="brdls_rm" class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brdls_rm'] ?>" /></td>
                 <th>名ローマ字</th>
                <td><input type="text" name="data[CustomerMst][brdfs_rm]"  id="brdfs_rm" class="validate[optional,custom[onlyLetterSp],maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brdfs_rm']?>" /></td>
           </tr>
           <tr>
		        <th>誕生日</th>
                <td colspan="3"><input type="text" name="data[CustomerMst][brdbirth_dt]"  id="brd_birth" class="validate[optional,custom[date]] inputdate" value="<?php echo $common->evalForShortDate($data['CustomerMstView']['brdbirth_dt'])?>" /></td>
           </tr>
		   <tr>
                <th>郵便番号</th><td colspan="3">
			    <input type="text" name="data[CustomerMst][brd_zip_cd]" id="brd_postcode" class="validate[optional,custom[postcode]] inputpostcode" value="<?php echo $data['CustomerMstView']['brd_zip_cd']?>"
			           onKeyUp="AjaxZip3.zip2addr(this,'','data[CustomerMst][brd_pref]','data[CustomerMst][brd_city]','data[CustomerMst][brd_address]','data[CustomerMst][brd_address]');">
                <input type="radio" value="1" name="data[CustomerMst][prm_address_flg]" <?php if($data['CustomerMstView']['prm_address_flg'] == 1){echo "checked";}?>/>代表</td>
		   </tr>
		   <tr>
                 <th>都道府県</th>
                 <td colspan="3">
                  <select id="brd_pref" name="data[CustomerMst][brd_pref]">
                    <option value="" selected></option>
			     	<option value="北海道">北海道</option><option value="青森県">青森県</option>
					<option value="岩手県">岩手県</option><option value="宮城県">宮城県</option>
					<option value="秋田県">秋田県</option><option value="山形県">山形県</option>
					<option value="福島県">福島県</option><option value="茨城県">茨城県</option>
					<option value="栃木県">栃木県</option><option value="群馬県">群馬県</option>
					<option value="埼玉県">埼玉県</option><option value="千葉県">千葉県</option>
					<option value="東京都">東京都</option><option value="神奈川県">神奈川県</option>
					<option value="新潟県">新潟県</option><option value="富山県">富山県</option>
					<option value="石川県">石川県</option><option value="福井県">福井県</option>
					<option value="山梨県">山梨県</option><option value="長野県">長野県</option>
					<option value="岐阜県">岐阜県</option><option value="静岡県">静岡県</option>
					<option value="愛知県">愛知県</option><option value="三重県">三重県</option>
					<option value="滋賀県">滋賀県</option><option value="京都府">京都府</option>
					<option value="大阪府">大阪府</option><option value="兵庫県">兵庫県</option>
					<option value="奈良県">奈良県</option><option value="和歌山県">和歌山県</option>
					<option value="鳥取県">鳥取県</option><option value="島根県">島根県</option>
					<option value="岡山県">岡山県</option><option value="広島県">広島県</option>
					<option value="山口県">山口県</option><option value="徳島県">徳島県</option>
					<option value="香川県">香川県</option><option value="愛媛県">愛媛県</option>
					<option value="高知県">高知県</option><option value="福岡県">福岡県</option>
					<option value="佐賀県">佐賀県</option><option value="長崎県">長崎県</option>
					<option value="熊本県">熊本県</option><option value="大分県">大分県</option>
					<option value="宮崎県">宮崎県</option><option value="鹿児島県">鹿児島県</option>
					<option value="沖縄県">沖縄県</option>
				 </select>
		    	</td>
           </tr>
           <tr>
                 <th>市町村区</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_city]" id="brd_city_kj" class="validate[optional,maxSize[20]] inputname" value="<?php echo $data['CustomerMstView']['brd_city']?>" />
                 </td>
           </tr>
           <tr>
                 <th>住所番地</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_street]" id="brd_street_kj" class="validate[optional,maxSize[60]] inputtitle" value="<?php echo $data['CustomerMstView']['brd_street']?>" />
                 </td>
           </tr>
		   <tr>
                 <th>アパート・マンション</th>
                 <td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_apart]" id="brd_part_kj" class="validate[optional,maxSize[20]] inputtitle" value="<?php echo $data['CustomerMstView']['brd_apart']?>" />
			     </td>
           </tr>
           <tr>
                 <th>住所(ローマ字)</th><td colspan="3">
			     <input type="text" name="data[CustomerMst][brd_address_rm]"  id="brd_address_rm" class="validate[optional,maxSize[120],custom[onlyLetterNumber]] inputtitle" value="<?php echo $data['CustomerMstView']['brd_address_rm']?>" /></td>
           </tr>
		   <tr>
                 <th>電話番号</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][brd_phone_no]"  id="brd_phone" class="validate[optional,custom[phone]] inputphone" value="<?php echo $data['CustomerMstView']['brd_phone_no']?>" />
                 <input type="radio" value="1" name="data[CustomerMst][prm_phone_no_flg]" <?php if($data['CustomerMstView']['prm_phone_no_flg'] == 1){echo "checked";}?> />代表</td>
            </tr>
		    <tr>
                 <th>携帯電話番号</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][brd_cell_no]" id="brd_cell" class="validate[optional,custom[phone]] inputphone" value="<?php echo $data['CustomerMstView']['brd_cell_no']?>" /></td>
            </tr>
  		    <tr>
                 <th>E-MAILアドレス</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][brd_email]"  id="brd_email" class="validate[optional,custom[email]] inputmailaddress" value="<?php echo $data['CustomerMstView']['brd_email']?>" />
                     <input type="radio" value="1"  name="data[CustomerMst][prm_email_flg]" <?php if($data['CustomerMstView']['prm_email_flg'] == 1){echo "checked";}?> />代表</td>
            </tr>
		    <tr>
                 <th>携帯メールアドレス</th>
                 <td colspan="3"><input type="text" name="data[CustomerMst][brd_phone_mail]"  id="brd_phone_mail" class="validate[optional,custom[email]] inputmailaddress" value="<?php echo $data['CustomerMstView']['brd_phone_mail']?>" /></td>
            </tr>
	</table>
</td>
</tr>
</table>
</fieldset>


    <div class="submit">
       <input type="hidden" name="data[CustomerMst][id]" class="inputvalue" value="<?php echo $data['CustomerMstView']['id'] ?>" />
       <input type="hidden" name="data[ContractTrn][id]" class="inputvalue" value="<?php echo $data['CustomerMstView']['contract_id'] ?>" />

	    <input type="submit" class="inputbutton" name="update" value="更新" />
	    <?php
	      //成約以前のみ削除可とする
	      if($data['CustomerMstView']['status_id'] <= CS_CONTRACTING){
	      	echo "<input type='submit' class='inputbutton' value='削除' name='delete' />";
	      }
	    ?>
	</div>
</form>
<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="confirm_dialog" style="display:none"><p><img src="<?php echo $html->webroot("/images/warning_result.png") ?>" alt="" />関連する見積などのデータも削除されますがよろしいですか？</p></div>
<div id="critical_error"></div>