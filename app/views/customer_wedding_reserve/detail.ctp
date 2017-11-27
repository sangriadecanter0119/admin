<?php
  //画像閲覧
  echo $html->css('prettyPhoto.css',false);
  echo $html->script('jquery/jquery.prettyPhoto.js',false);
  //ajax更新URL
  $edit_url = $html->url('edit');
  $feed_url = $html->url('feed');
  $snapshot_url = $html->url('snapshot')."/".$final_sheet_id;
  //表示用画像
  $arrownext = $html->webroot('images/arrownext.gif');
  $arrowdown = $html->webroot('images/arrowdown.gif');
  $confirm_result = $html->webroot("/images/confirm_result.png");
  $error_result = $html->webroot("/images/error_result.png");
  /* 商品カテゴリ区分 */
  $GC_WEDDING = GC_WEDDING;
  $GC_CEREMONY_OPTION = GC_CEREMONY_OPTION;
  $GC_HAIR_MAKE = GC_HAIR_MAKE;
  $GC_TRANS_CPL = GC_TRANS_CPL;
  $GC_TRANS_GST = GC_TRANS_GST;
  $GC_COORDINATOR = GC_COORDINATOR;
  $GC_FLOWER = GC_FLOWER;
  $GC_ALBUM = GC_ALBUM;
  $GC_PHOTO = GC_PHOTO;
  $GC_VIDEO = GC_VIDEO;
  $GC_ENTERTAINMENT = GC_ENTERTAINMENT;
  $GC_MINISTER = GC_MINISTER;
  $GC_RECEPTION = GC_RECEPTION;
  $GC_RECEPTION_TRANS = GC_RECEPTION_TRANS;
  $GC_PARTY_OPTION = GC_PARTY_OPTION;
  $GC_LINEN = GC_LINEN;
  $GC_AV = GC_AV;
  $GC_CAKE = GC_CAKE;
  $GC_PAPER = GC_PAPER;
  $GC_MC = GC_MC;
  $GC_HOUSE_WEDDING = GC_HOUSE_WEDDING;
  $GC_CEREMONY_OPTION = GC_CEREMONY_OPTION;
  $GC_FLOWER_MAIN = GC_FLOWER_MAIN;
  $GC_FLOWER_CEREMONY = GC_FLOWER_CEREMONY;
  $GC_FLOWER_RECEPTION = GC_FLOWER_RECEPTION;
  $GC_TRAVEL = GC_TRAVEL;

$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){

  /* 入力補助プラグイン設定
  ----------------------------------------------------------*/
     //入力マスク
     $(".date_mask").mask("9999/99/99");
     $(".time_mask").mask("99:99");
     $(".cell_mask").mask("999-9999-9999");

     //日付入力補助のプラグイン
     $( ".datepicker" ).datepicker({
       dateFormat: 'yy/mm/dd',
       showOtherMonths: true,
       selectOtherMonths: true,
       numberOfMonths:3,
       beforeShow : function(){ $('#ui-datepicker-div').css( 'font-size', '90%' );}
   });
    //フォームvalidation
    // 各フィールドにＩＤが必要
    // 動作が重くなる
    //$("#transportation_form").validationEngine();

    //フィールドフォーカス
 	$('input[type="text"],input[type="password"],textarea,select').focus(function() {
 			$(this).addClass("focusField");
    		});
  	$('input[type="text"],input[type="password"],textarea,select').blur(function() {
    			$(this).removeClass("focusField");
    		});

   /* アコーディオンプラグインの設定
   -------------------------------------------------------*/
    $('div.toggle_container').hide();

    $('p.trigger').click(function() {
        if ($(this).hasClass('active')) {
          $(this).removeClass('active');
          $('img',this).attr('src',"$arrownext");
        }
        else {
          $(this).addClass('active');
          $('img',this).attr('src',"$arrowdown");

          /* 既に一度サーバーからデータを取得した場合はNAME属性をNULLにしてあるので,未取得の場合は取得する */
          if($(this).attr("name") != "retrieved"){
           var category_id = $(this).attr("name");
           //取得済みに設定
           $(this).attr("name","retrieved");

		   //インディケーター表示
		   //$("#entertainment_indicator").css("display","inline");

           $.get("$feed_url/all/"+ category_id+"/"+$final_sheet_id,function(data){

              switch (category_id){

     	   	   case "$GC_WEDDING":    $("#ceremony_form").html(data);
     	   	                          resetEvent($("#ceremony_form"));
     	   		                      break;
     	   	   case "$GC_CEREMONY_OPTION": $("#ceremonyOption_form").html(data);
     	   	                                resetEvent($("#ceremonyOption_form"));
     	   		                            break;
     	   	   case "$GC_TRAVEL":      $("#travel_form").html(data);
     	   	                           resetEvent($("#travel_form"));
     	   		                       break;
     	   	   case "$GC_HAIR_MAKE":  $("#hairmake_form").html(data);
     	   	                          resetEvent($("#hairmake_form"));
     	   		                      break;
     	       case "$GC_TRANS_CPL": $("#transportationCpl_form").html(data);
     	                                  resetEvent($("#transportationCpl_form"));
     	   		                          break;
     	   	   case "$GC_TRANS_GST": $("#transportationGst_form").html(data);
     	                                  resetEvent($("#transportationGst_form"));
     	   		                          break;
     	   	   case "$GC_COORDINATOR": $("#coordinator_form").html(data);
     	   	                           resetEvent($("#coordinator_form"));
     	   		                       break;
     	   	   case "$GC_FLOWER":      $("#flower_form").html(data);
     	   	                           resetEvent($("#flower_form"));
     	   		                       break;
     	   	   case "$GC_PHOTO":       $("#photographer_form").html(data);
     	   	                           resetEvent($("#photographer_form"));
     	   	   	                       break;
     	   	   case "$GC_VIDEO":       $("#videographer_form").html(data);
     	   	                           resetEvent($("#videographer_form"));
     	   	                           break;
     	       case "$GC_ENTERTAINMENT": $("#entertainment_form").html(data);
     	                                 resetEvent($("#entertainment_form"));
     	   		                         break;
     	   	   case "$GC_MINISTER":    $("#minister_form").html(data);
     	   	                           resetEvent($("#minister_form"));
     	   		                       break;
     	   	   case "$GC_MC":          $("#mc_form").html(data);
     	   	                           resetEvent($("#mc_form"));
     	   		                       break;
     	   	   case "$GC_HOUSE_WEDDING" :$("#houseWedding_form").html(data);
     	   	                             resetEvent($("#houseWedding_form"));
     	   		                         break;
     	   	   case "$GC_RECEPTION":     $("#reception_form").html(data);
     	   	                             resetEvent($("#reception_form"));
     	   		                         break;
     	   	   case "$GC_RECEPTION_TRANS": $("#receptionTransportation_form").html(data);
     	   	                               resetEvent($("#receptionTransportation_form"));
     	   		                           break;
     	   	   case "$GC_PARTY_OPTION":  $("#partyOption_form").html(data);
     	   	                             resetEvent($("#partyOption_form"));
     	   		                         break;
     	   	   case "$GC_CAKE":          $("#cake_form").html(data);
     	   	                             resetEvent($("#cake_form"));
     	   		                         break;
     	   	   case "$GC_LINEN":         $("#linen_form").html(data);
     	   	                             resetEvent($("#linen_form"));
     	   		                         break;
     	   	   case "$GC_AV":            $("#av_form").html(data);
     	   	                             resetEvent($("#av_form"));
     	   		                         break;
     	   	   case "$GC_ALBUM":         $("#album_form").html(data);
     	   	                             resetEvent($("#album_form"));
     	   		                         break;
     	   	   case "$GC_PAPER":         $("#paper_form").html(data);
     	   	                             resetEvent($("#paper_form"));
     	   		                         break;
     	   	   case "$GC_FLOWER_MAIN":   $("#flowerMain_form").html(data);
     	   	                             resetEvent($("#flowerMain_form"));
     	   		                         break;
     	   	   case "$GC_FLOWER_CEREMONY":
     	   	   	                         $("#flowerCeremony_form").html(data);
     	   	                             resetEvent($("#flowerCeremony_form"));
     	   		                         break;
     	   	   case "$GC_FLOWER_RECEPTION":
     	   	   	                         $("#flowerReception_form").html(data);
     	   	                             resetEvent($("#flowerReception_form"));
     	   		                         break;
              }
              //インディケーター非表示
		      //$("#entertainment_indicator").css("display","none");
            });
           }
        }
		$(this).next().toggle('slow');
		return false;
	}).next().hide();
		/*
		$(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'facebook',slideshow:3000, autoplay_slideshow: false});
		$(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});
		*/

     /* 処理結果用ダイアログ
     ------------------------------------------------------*/
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

	 /* Utility関数
	 -----------------------------------------------------------------*/
		function SplitKeyName(str,index)
        {
           var arr = str.split('_');
           return arr[index];
        }

        //時間の差を計算する
        function CalcTotalTime(start,end){
          s1=start.split(":");
          s2=end.split(":");
          v1= 60*parseInt(s1[0])+parseInt(s1[1]);
          v2= 60*parseInt(s2[0])+parseInt(s2[1]);
          sabun=Math.abs( v1-v2 )
          xhh=Math.floor( sabun/60 );
          xmm=sabun%60 ;
          return ""+xhh+"Hour "+xmm+"Minutes";
       }

       //商品ﾃｷｽﾄエリアの高さ自動調整
	    function resizeTextarea(e){
	       var lines = $(e).val().split("\\n").length + 1;
           $(e).attr("rows", lines);
        }

        //イベントの再設定
        function resetEvent(new_tag){
           $(".time_mask",new_tag).mask("99:99");
           $(".date_mask",new_tag).mask("9999/99/99");
           $(".work_time",new_tag).bind("change",function(){ ChangeWorkTime($(this));});
		   $(".delete",new_tag).bind("click",function(){
		       Delete($(this));
		       return false;   //scrollさせないためClickイベントをキャンセルする
		   });
		   $(".add",new_tag).bind("click",function(){
		      Add($(this));
		      return false;   //scrollさせないためClickイベントをキャンセルする
		   });
		   $(".transCpl",new_tag).bind("change",function(){ ChangePassengerNumber($(this));});
        }

   /* 入力補助関連関数
   ---------------------------------------------------------------*/
       //開始時間や終了時間に入力がされたら合計時間を算出する
        $(".work_time").change(function(){
             ChangeWorkTime($(this))
        });

        function ChangeWorkTime(e){
           var key = $(e).attr("id").split('_')[0];
           var start = $("#" + key + "_start_time").val();
           var end   = $("#" + key + "_end_time").val();

          if(start == "" || end == "")
          {
             $("#" + key + "_total_time").val(0);
          }
          else
          {
             $("#" + key + "_total_time").val(CalcTotalTime(start,end));
          }
        }

        //Basic Infoカテゴリの挙式とレセプションのゲスト人数が変更されたら合計人数を算出する
        $(".guest").change(function(){

            var key = $(this).attr("id").split('_')[0];
            var total = parseInt($("#" + key + "_bg").val()) + parseInt($("#" + key + "_ad").val()) +
                        parseInt($("#" + key + "_ch").val()) + parseInt($("#" + key + "_inf").val());
            $("#" + key + "_gst_total").val(total);
        });

        //trasnportation cplカテゴリの乗客人数が変更されたら合計人数を算出する
         function ChangePassengerNumber(e){

           var key = $(e).attr("id").split('_')[0];
           var total = parseInt($("#" + key + "_bg").val())  +  parseInt($("#" + key + "_guest").val()) +
                       parseInt($("#" + key + "_ph").val())  +  parseInt($("#" + key + "_hm").val())    +
                       parseInt($("#" + key + "_att").val()) +  parseInt($("#" + key + "_vh").val());
           $("#" + key + "_total_passenger").val(total);
         }

  /* 行追加関連関数
  -------------------------------------------------------------*/
	function Add(e){

	      var sub_type;
	      var category_id;
	      var category_name;
	      var head_counter;
	      var sub_counter;
	      var dtl_counter;
	      var no;

	      /* テーブル単位の複製 */
	      if($(e).hasClass("tableUnit")){
            sub_type = "tableUnit";
            category_name= SplitKeyName($(e).attr("name"),0);
            category_id  = SplitKeyName($(e).attr("name"),1);
	        head_counter = SplitKeyName($(e).attr("name"),2);
	        sub_counter  = getNextSubCounter($(e).attr("name"));

	        //alert(category_id + ":" + head_counter + ":" + sub_counter);
	      }
	      /* 行単位の複製 */
	      else if($(e).hasClass("rowUnit")){
	        sub_type="rowUnit";
	        category_name= SplitKeyName($(e).attr("name"),0);
	        category_id  = SplitKeyName($(e).attr("name"),1);
	        head_counter = SplitKeyName($(e).attr("name"),2);
	        sub_counter  = SplitKeyName($(e).attr("name"),3);

	        //alert($("#" + category_name + "_" + head_counter + "_" + sub_counter + "_table tr:last-child").attr("id"));

	        var tag = $("#" + category_name + "_" + head_counter + "_" + sub_counter + "_table tr:last-child");
	        dtl_counter  = parseInt(SplitKeyName($(tag).attr("id"),3)) + 1;
	        //現在のNoを取得
	        no = $("select",tag).val();

	       // alert(dtl_counter);

	      }else{return;}

	    //alert("$feed_url/part/" + category_id + "/" + sub_type + "/" + head_counter + "/" + sub_counter + "/" + dtl_counter);

	      /* HTMLデータの取得 */
          $.get("$feed_url/part/" + category_id + "/dummy/" + sub_type + "/" + head_counter + "/" + sub_counter + "/" + dtl_counter + "/" + no, function(data) {

              /* テーブル単位の複製 */
	          if(sub_type == "tableUnit"){
	             //alert(data);
                 $("#" + category_name + "_" + head_counter + "_div").append(data);
                 resetEvent($("#" + category_name + "_" + head_counter + "_" + sub_counter + "_table"));
              }
	          /* 行単位の複製 */
	          else if(sub_type == "rowUnit"){
	           //alert(data);
                 $("#" + category_name + "_" + head_counter + "_" + sub_counter + "_table").append(data);
                 resetEvent($("#" + category_name + "_" + head_counter + "_" + sub_counter + "_" + dtl_counter + "_row"));
	          }
          });
          return;
	   }

     /*  行削除関連関数
     ---------------------------------------------------------*/
		function Delete(e)
		{
		   var sub_type;
	       var category_id;
	       var category_name;
	       var head_counter;
	       var sub_counter;
	       var dtl_counter;

	      /* テーブル単位の削除 */
	      if($(e).hasClass("tableUnit")){
            sub_type = "tableUnit";
            category_name= SplitKeyName($(e).attr("name"),0);
            category_id  = SplitKeyName($(e).attr("name"),1);
	        head_counter = SplitKeyName($(e).attr("name"),2);
	        sub_counter  = SplitKeyName($(e).attr("name"),3);

	        $("#" + category_name + "_" + head_counter + "_" + sub_counter + "_table").remove();
	      }
	      /* 行単位の削除 */
	      else if($(e).hasClass("rowUnit")){
	        sub_type="rowUnit";
	        category_name= SplitKeyName($(e).attr("name"),0);
	        category_id  = SplitKeyName($(e).attr("name"),1);
	        head_counter = SplitKeyName($(e).attr("name"),2);
	        sub_counter  = SplitKeyName($(e).attr("name"),3);
	        dtl_counter  = SplitKeyName($(e).attr("name"),4);

	        //alert("#" + category_name + "_" + head_counter + "_" + sub_counter + "_" + dtl_counter + "_row");

	        $("#" + category_name + "_" + head_counter + "_" + sub_counter + "_" + dtl_counter + "_row").remove();
	      }else{return;}

	      return;
        }

	 /* 更新関連関数
	 -----------------------------------------------------------*/
		$(".save").click(function(){

		    $(this).simpleLoading('show');

		    var category = SplitKeyName($(this).attr("id"),0);
		    var indicator_tag = "#" + category + "_indicator";
		    var form_tag = "#" + category + "_form";

		    //フォームデータの設定
		    /*
		    var postData = {};
		    $(form_tag).find(':input').each(function(){
             postData[$(this).attr('name')] = $(this).val();
            });
            */
             var formData = $(form_tag).serialize();

      // alert(postData["data[Category][id]"]);

            //ajax更新開始
            $.post("$edit_url" , formData,function(result){

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
	     		    $("#result_message img").attr('src',"$confirm_result");
	     		}else{
	     		    $("#result_message img").attr('src',"$error_result");
	     		}
	     	        $("#result_message span").text(obj.message);
	     	        $("#error_reason").text(obj.reason);
	                $("#result_dialog").dialog('open');
            });
          return false;
		})

     /*  画像フォームの表示開始
     -------------------------------------------------*/
        $(".file_edit_link").click(function(){

           if($(this).hasClass("disable")==false){

              $(this).simpleLoading('show');
              $.post($(this).attr('href'),function(html){
                 $('body').append(html);
                 $(this).simpleLoading('hide');
              });
           }
           return false;
        });

     /*  スナップショット開始
     -------------------------------------------------*/
        $("#snapshot").click(function(){

           $(this).simpleLoading('show');

            //ajax更新開始
            $.post("$snapshot_url" ,null,function(result){

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
	     		    $("#result_message img").attr('src',"$confirm_result");
	     		}else{
	     		    $("#result_message img").attr('src',"$error_result");
	     		}
	     	        $("#result_message span").text(obj.message);
	     	        $("#error_reason").text(obj.reason);
	                $("#result_dialog").dialog('open');
            });
          return false;
        });
});

JSPROG
)) ?>

<style type="text/css">
  #test{color:red;}
</style>

<ul class="operate">
<li><a href="<?php echo $html->url('.') ?>">戻る</a></li>
<li><a href="<?php echo $html->url('export/excel_business/').$final_sheet_id ?>" id="export_final_sheet">EXCEL出力【業務用】</a></li>
<li><a href="<?php echo $html->url('export/excel_customer/').$final_sheet_id ?>" id="">EXCEL出力【顧客用】</a></li>
<li><a href="<?php echo $html->url('export/excel_business_test/').$final_sheet_id ?>" id="">EXCEL出力【TEST】</a></li>
<li><a href="<?php echo $html->url('fileForm').'/common'    ?>" id='exedit_allFile' class='file_edit_link'>ALL FILE</a></li>
<li><a href="#" id="snapshot">スナップショット</a></li>
</ul>

<div class="content" >

  <!-- Basic Info -->
  <div id="Basic" style="display:inline">
  <p class="trigger red"><?php echo $html->image('arrownext.gif') ?>Basic Info</p>
  <div class="toggle_container" >

		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/basicInfo' id='exedit_basicInfo' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="basicInfo_save" class="save" >Save</a></li>
        </ul>

     <form id="basicInfo_form" action="POST">
        <input type="hidden" name="data[Category][id]"     value="<?php echo GC_BASIC_INFO ?>" />
        <input type="hidden" name="data[ContractTrn][id]"  value="<?php echo $contract['ContractTrnView']['id'] ?>" />
        <table class="list" >
    	    <tr>
    	     <th colspan="4"  width="124">Date</th>
    	     <th colspan="2"  width="62">Day</th>
    	     <th colspan="13" width="403">Groom</th>
    	     <th colspan="13" width="403">Bride</th>
    	     <th colspan="4"  width="124">Cell Phone(G)</th>
    	     <th colspan="4"  width="124">Cell Phone(B)</th>
    	    </tr>
    	    <tr>
    	     <td colspan="4"><?php echo $common->evalNbspForShortDate($contract['ContractTrnView']['wedding_dt']) ?></td>
    	     <td colspan="2">
    	          <?php
    	             /* 曜日の抽出 */
    	             $wedding_dt = $common->evalNbspForShortDate($contract['ContractTrnView']['wedding_dt']);
    	             $arr = split("/",$wedding_dt);
    	             if(count($arr) == 3){
    	                echo  date("l", mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]));
    	             }else{
    	                echo "&nbsp;";
    	           	 }
    	          ?>
    	     </td>
    	     <td colspan="7"><?php echo $contract['ContractTrnView']['grmls_kj']." ".$contract['ContractTrnView']['grmfs_kj']."様" ?></td>
    	     <td colspan="6">Mr. <?php echo $contract['ContractTrnView']['grmfs_rm']." ".$contract['ContractTrnView']['grmls_rm'] ?></td>
    	     <td colspan="7"><?php echo $contract['ContractTrnView']['brdls_kj']." ".$contract['ContractTrnView']['brdfs_kj']."様" ?></td>
    	     <td colspan="6">Ms. <?php echo $contract['ContractTrnView']['brdfs_rm']." ".$contract['ContractTrnView']['brdls_rm'] ?></td>
    	     <td colspan="4"><?php echo $common->evalNbsp($customer['CustomerMst']['grm_cell_no']) ?></td>
    	     <td colspan="4"><?php echo $common->evalNbsp($customer['CustomerMst']['brd_cell_no']) ?></td>
    	    </tr>
    	    <tr>
    	     <th colspan="40"  width="1240">Ceremony</th>
    	    </tr>
    	    <tr>
    	     <th colspan="2"   width="62">Time</th>
    	     <th colspan="15"  width="465">Site</th>
    	     <th colspan="8"  rowspan="2" width="248">Number Of Guests</th>
    	     <th colspan="3"  width="93">B&G</th>
    	     <th colspan="3"  width="93">AD</th>
    	     <th colspan="3"  width="93">CH</th>
    	     <th colspan="3"  width="93">INF</th>
    	     <th colspan="3"  width="93">TOTAL</th>
    	    </tr>
    	    <tr>
    	     <td colspan="2" ><input class="time_mask inputableField" type="text" name="data[ContractTrn][wedding_time]"  value="<?php echo $contract['ContractTrnView']['wedding_time'] ?>" style="width:100%" /></td>
    	     <td colspan="15" ><?php echo $common->evalNbsp($contract['ContractTrnView']['wedding_place']) ?></td>
    	     <td colspan="3" >
    	       <select id="wedding_bg" class="guest inputableField"" name = "data[ContractTrn][wedding_bg]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['wedding_bg']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3">
    	       <select id="wedding_ad" class="guest inputableField"" name = "data[ContractTrn][wedding_ad]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['wedding_ad']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3" >
    	       <select id="wedding_ch" class="guest inputableField"" name = "data[ContractTrn][wedding_ch]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['wedding_ch']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3" >
    	       <select id="wedding_inf" class="guest inputableField"" name = "data[ContractTrn][wedding_inf]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['wedding_inf']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3" ><input id="wedding_gst_total" class="inputdisable"  type="text" name="data[ContractTrn][wedding_gst_total]" value="<?php echo $contract['ContractTrnView']['wedding_gst_total'] ?>"  style="width:98%" readonly/></td>
    	    </tr>
    	    <tr>
    	     <th colspan="40" >Reception</th>
    	    </tr>
    	    <tr>
    	     <th colspan="2"  width="62">Time</th>
    	     <th colspan="15" width="465">Site</th>
    	     <th colspan="8"  rowspan="2" width="248">Number Of Guests</th>
    	     <th colspan="3"  width="93">B&G</th>
    	     <th colspan="3"  width="93">AD</th>
    	     <th colspan="3"  width="93">CH</th>
    	     <th colspan="3"  width="93">INF</th>
    	     <th colspan="3"  width="93">TOTAL</th>
    	    </tr>
    	    <tr>
    	      <td colspan="2" ><input class="time_mask inputableField" type="text" name="data[ContractTrn][reception_time]" value="<?php echo $contract['ContractTrnView']['reception_time'] ?>" style="width:100%" /></td>
    	     <td colspan="15"><input class="inputableField"" type="text" name="data[ContractTrn][reception_place]" value="<?php echo $contract['ContractTrnView']['reception_place'] ?>" style="width:100%" /></td>
    	     <td colspan="3">
    	       <select id="reception_bg" class="guest inputableField"" name = "data[ContractTrn][reception_bg]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['reception_bg']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3">
    	       <select id="reception_ad" class="guest inputableField"" name = "data[ContractTrn][reception_ad]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['reception_ad']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3">
    	       <select id="reception_ch" class="guest inputableField"" name = "data[ContractTrn][reception_ch]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['reception_ch']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3">
    	       <select id="reception_inf" class="guest inputableField"" name = "data[ContractTrn][reception_inf]" style="width:100%" >
    	        <?php
    	           for($i=0;$i < 100;$i++){
    	           	 if($i == $contract['ContractTrnView']['reception_inf']){
    	           	 	echo "<option value='$i' selected>$i</option>";
    	           	 }else{
    	           	 	echo "<option value='$i'         >$i</option>";
    	           	 }
    	           }
    	        ?>
    	        </select>
    	     </td>
    	     <td colspan="3" ><input id="reception_gst_total" class="inputdisable"  type="text" name="data[ContractTrn][reception_gst_total]" value="<?php echo $contract['ContractTrnView']['reception_gst_total'] ?>"  style="width:98%" readonly/></td>
    	   </tr>
    	</table>
     </form>
  </div>
  </div>

  <!-- PersonalInfo -->
  <div id="PersonalInfo" style="display:inline">
  <p class="trigger red"><?php echo $html->image('arrownext.gif') ?>Personal Info</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/personalInfo' id='exedit_personalInfo' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="personalInfo_save" class="save" >Save</a></li>
        </ul>

      <form id="personalInfo_form" action="POST">
        <input type="hidden" name="data[Category][id]"     value="<?php echo GC_PERSONAL_INFO ?>" />
        <input type="hidden" name="data[CustomerMst][id]"  value="<?php echo $customer['CustomerMst']['id'] ?>" />
    	<table class="list" >
    	    <tr>
    	     <th colspan="16"  width="496">Representative Address</th>
    	     <th colspan="12"  width="372">E-Mail(G)</th>
    	     <th colspan="12"  width="372">E-Mail(B)</th>
    	    </tr>
    	    <tr>
    	     <td colspan="16">
    	       <?php
    	          /* メインに選択されている方の住所を表示 */
    	            if(GROOM == $customer['CustomerMst']['prm_address_flg']){
    	            	 echo $common->evalNbsp($customer['CustomerMst']['grm_pref'].$customer['CustomerMst']['grm_city'].$customer['CustomerMst']['grm_street'].$customer['CustomerMst']['grm_apart']);
    	            }else{
    	            	 echo $common->evalNbsp($customer['CustomerMst']['brd_pref'].$customer['CustomerMst']['brd_city'].$customer['CustomerMst']['brd_street'].$customer['CustomerMst']['brd_apart']);
    	            }
    	        ?>
    	     </td>
    	     <td colspan="12"><?php echo $common->evalNbsp($customer['CustomerMst']['grm_email']) ?></td>
    	     <td colspan="12"><?php echo $common->evalNbsp($customer['CustomerMst']['brd_email']) ?></td>
    	    </tr>
    	    <tr>
    	     <th colspan="4"  width="124">Groom's BD</th>
    	     <th colspan="4"  width="124">Bride's BD</th>
    	     <th colspan="4"  width="124">Cell Phone(G)</th>
    	     <th colspan="4"  width="124">Cell Phone(B)</th>
    	     <th colspan="24" width="744">Special Info</th>
    	    </tr>
    	    <tr>
    	     <td colspan="4"><?php echo $common->evalNbspForShortDate($customer['CustomerMst']['grmbirth_dt']) ?></td>
    	     <td colspan="4"><?php echo $common->evalNbspForShortDate($customer['CustomerMst']['brdbirth_dt']) ?></td>
    	     <td colspan="4"><?php echo $common->evalNbsp($customer['CustomerMst']['grm_cell_no']) ?></td>
    	     <td colspan="4"><?php echo $common->evalNbsp($customer['CustomerMst']['brd_cell_no']) ?></td>
    	     <td colspan="27"><input class="inputableField"  type="text" name="data[CustomerMst][note]"        value='<?php echo $customer['CustomerMst']['note'] ?>'        style="width:99%;" /></td>
    	    </tr>
    	</table>
     </form>
  </div>
  </div>

  <!-- Travel -->
  <div id="Travel" style="<?php echo count($travel)==0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_TRAVEL ?>"><?php echo $html->image('arrownext.gif') ?>Travel</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/travel' id='exedit_travel' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="travel_save" class="save" >Save</a></li>
        </ul>

        <form id="travel_form" action="POST"></form>
  </div>
  </div>

  <!-- Coordinator -->
  <div id="Coordinator" style="<?php echo $coordinator == 0 ? 'display:none' : 'display:inline'  ?>" >
  <p class="trigger red" name="<?php echo GC_COORDINATOR ?>"><?php echo $html->image('arrownext.gif') ?>Coordinator</p>
  <div class="toggle_container" >

		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/coordinator' id='exedit_coordinator' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="coordinator_save" class="save" >Save</a></li>
        </ul>

        <form id="coordinator_form" action="POST"></form>
  </div>
  </div>

  <!-- Ceremony -->
  <div id="Ceremony" style="<?php echo $ceremony == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_WEDDING ?>"><?php echo $html->image('arrownext.gif') ?>Ceremony</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/ceremony' id='exedit_ceremony' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="ceremony_save" class="save" >Save</a></li>
        </ul>
        <form id="ceremony_form" action="POST"></form>
  </div>
  </div>

  <!-- CEREMONY_OPTION-->
  <div id="CeremonyOption" style="<?php echo $ceremony_option == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_CEREMONY_OPTION ?>"><?php echo $html->image('arrownext.gif') ?>Ceremony Option</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/ceremonyOption' id='exedit_ceremonyOption' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="ceremonyOption_save" class="save" >Save</a></li>
        </ul>
        <form id="ceremonyOption_form" action="POST"></form>
  </div>
  </div>

  <!-- Transpotation Cpl -->
  <div id="TranspotationCpl" style="<?php echo $trans_cpl == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_TRANS_CPL ?>"><?php echo $html->image('arrownext.gif') ?>Transpotation Cpl</p>
  <div class="toggle_container" >
         <Ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/transportationCpl' id='exedit_transportationCpl' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="transportationCpl_save" class="save" >Save</a></li>
        </ul>
        <form id="transportationCpl_form" action="POST"></form>
  </div>
  </div>

   <!-- Transpotation Gst-->
  <div id="TranspotationGst" style="<?php echo $trans_gst == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_TRANS_GST ?>"><?php echo $html->image('arrownext.gif') ?>Transpotation Gst</p>
  <div class="toggle_container" >
         <Ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/transportationGst' id='exedit_transportationGst' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="transportationGst_save" class="save" >Save</a></li>
        </ul>
         <form id="transportationGst_form" action="POST"></form>
  </div>
  </div>

  <!-- Hair Make -->
  <div id="HairMake" style="<?php echo $hairmake_cpl == 0 && $hairmake_gst == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_HAIR_MAKE ?>"><?php echo $html->image('arrownext.gif') ?>Hair Make</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/hairmake' id='exedit_hairmake' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="hairmake_save" class="save" >Save</a></li>
        </ul>
        <form id="hairmake_form" action="POST"></form>
  </div>
  </div>

  <!-- photo -->
  <div id="photo" style="<?php echo $photographer == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_PHOTO ?>"><?php echo $html->image('arrownext.gif') ?>Photographer</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/photo' id='exedit_photo' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="photographer_save" class="save" >Save</a></li>
        </ul>
        <form id="photographer_form" action="POST"></form>
   </div>
  </div>

  <!-- video -->
  <div id="Video" style="<?php echo $videographer == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_VIDEO ?>"><?php echo $html->image('arrownext.gif') ?>Videographer</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/video' id='exedit_video' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="videographer_save" class="save" >Save</a></li>
        </ul>
    	<form id="videographer_form" action="POST"></form>
       </div>
    </div>

  <!-- Album  -->
  <div id="album" style="<?php echo $album == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_ALBUM ?>"><?php echo $html->image('arrownext.gif') ?>Album</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/album' id='exedit_album' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="album_save" class="save" >Save</a></li>
        </ul>
        <form id="album_form" action="POST"></form>
   </div>
  </div>

  <!-- Flower -->
  <div id="Flower" style="<?php echo $flower == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_FLOWER ?>"><?php echo $html->image('arrownext.gif') ?>Flower</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/flower' id='exedit_flower' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="flower_save" class="save" >Save</a></li>
        </ul>
        <form id="flower_form" action="POST"></form>
  </div>
  </div>

  <!-- Reception -->
  <div id="Reception" style="<?php echo $reception == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_RECEPTION ?>"><?php echo $html->image('arrownext.gif') ?>Reception</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/reception' id='exedit_reception' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="reception_save" class="save" >Save</a></li>
        </ul>
        <form id="reception_form" action="POST"></form>
  </div>
  </div>

  <!-- Reception Transportation-->
  <div id="ReceptionTransportation" style="<?php echo $trans_recep == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_RECEPTION_TRANS ?>"><?php echo $html->image('arrownext.gif') ?>Reception Transportation</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/receptionTransportation' id='exedit_receptionTransportation' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="receptionTransportation_save" class="save" >Save</a></li>
        </ul>
        <form id="receptionTransportation_form" action="POST"></form>
  </div>
  </div>

  <!-- Cake -->
  <div id="Cake" style="<?php echo $cake == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_CAKE ?>"><?php echo $html->image('arrownext.gif') ?>Cake</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/cake' id='exedit_cake' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="cake_save" class="save" >Save</a></li>
        </ul>
        <form id="cake_form" action="POST"></form>
    </div>
    </div>

  <!-- Entertainment -->
  <div id="Entertainment" style="<?php echo $entertainment == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_ENTERTAINMENT ?>"><?php echo $html->image('arrownext.gif') ?>Entertainment</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/entertainment' id='exedit_entertainment' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="entertainment_save" class="save" >Save</a></li>
        </ul>
        <form id="entertainment_form" action="POST"></form>
    </div>
    </div>

  <!-- AV -->
  <div id="AV" style="<?php echo $av == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_AV ?>"><?php echo $html->image('arrownext.gif') ?>AV</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/av' id='exedit_av' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="av_save" class="save" >Save</a></li>
        </ul>
        <form id="av_form" action="POST"></form>
  </div>
  </div>

  <!-- Linen -->
  <div id="Linen" style="<?php echo $linen == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_LINEN ?>"><?php echo $html->image('arrownext.gif') ?>Linen</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/linen' id='exedit_linen' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="linen_save" class="save" >Save</a></li>
        </ul>
        <form id="linen_form" action="POST"></form>
    </div>
    </div>

  <!--　Wedding Item-->
  <div id="Paper" style="<?php echo $paper == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_PAPER ?>"><?php echo $html->image('arrownext.gif') ?>Wedding Item</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/paper' id='exedit_paper' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="paper_save" class="save" >Save</a></li>
        </ul>
        <form id="paper_form" action="POST"></form>
  </div>
  </div>

  <!-- MC -->
  <div id="MC" style="<?php echo $mc == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_MC ?>"><?php echo $html->image('arrownext.gif') ?>MC</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/mc' id='exedit_mc' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="mc_save" class="save" >Save</a></li>
        </ul>
        <form id="mc_form" action="POST"></form>
  </div>
  </div>

   <!-- Minister -->
  <div id="Minister" style="<?php echo $minister == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_MINISTER ?>"><?php echo $html->image('arrownext.gif') ?>Minister</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/minister' id='exedit_minister' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="minister_save" class="save" >Save</a></li>
        </ul>
        <form id="minister_form" action="POST"></form>
  </div>
  </div>

  <!-- Party Option-->
  <div id="PartyOption" style="<?php echo $party_option == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_PARTY_OPTION ?>"><?php echo $html->image('arrownext.gif') ?>Party Option</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/partyOption' id='exedit_partyOption' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="partyOption_save" class="save" >Save</a></li>
        </ul>
        <form id="partyOption_form" action="POST"></form>
    </div>
    </div>

  <!-- House Wedding -->
  <div id="HouseWedding" style="<?php echo $house_wedding == 0 ? 'display:none' : 'display:inline'  ?>">
  <p class="trigger red" name="<?php echo GC_HOUSE_WEDDING ?>"><?php echo $html->image('arrownext.gif') ?>House Wedding</p>
  <div class="toggle_container" >
		<ul class="operate">
           <?php  echo "<li><a href='{$html->url('fileForm')}/houseWedding' id='exedit_houseWedding' class='file_edit_link'>File</a></li>"; ?>
           <li><a href="#" id="houseWedding_save" class="save" >Save</a></li>
        </ul>
        <form id="houseWedding_form" action="POST"></form>
  </div>
  </div>

<div><input type="hidden" id="callback_argument" value=""></div>
 <div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
 <div id="critical_error"></div>
</div>