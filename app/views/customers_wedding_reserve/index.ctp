<script type='text/javascript'>
$(function(){

   $("input:button").button();
   $(".wedding_date").mask("9999-99");

   //表示件数の変更
   $("#limit").change(function(){
     location.href = <?php echo "'".$html->url("index")."'" ?> + "/" + $("#limit").val();
   });

   //表示件数にマッチした件数表示にする
   $("#limit option").each(function(){
     if($(this).val() == <?php echo $page_limit ?>){
       $(this).attr("selected","selected");
     }
   });

   //選択カテゴリが変わったら検索して再表示
   $(".filter").change(function(){  Search();});
   $("#filter_wedding_btn").click(function(){ Search();});

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

   /* データ取り込み */
   $("#import_link").click(function(){

      	$(this).simpleLoading('show');

		$.get(<?php echo "'".$html->url("import")."'" ?>, function(result) {

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
			      $("#result_message img").attr('src',<?php echo "'".$html->webroot('/images/confirm_result.png')."'" ?>);
			  }else{
			      $("#result_message img").attr('src',<?php echo "'".$html->webroot('/images/error_result.png')."'" ?>);
			  }
		      $("#result_message span").text(obj.message);
		      $("#error_reason").text(obj.reason);
	          $("#result_dialog").dialog('open');
	   });
   });

	   /*  ファイル取り込みフォームの表示開始
	   -------------------------------------------------*/
	   $("#file_upload_link").click(function(){

		   $(this).simpleLoading('show');
		   $.post(<?php echo "'".$html->url('fileUploadForm')."'" ?>,function(html){
		      $('body').append(html);
		      $(this).simpleLoading('hide');
	       });
		   return false;
	   });
   $("td.ws_status_" + <?php echo WS_ORDERING ?>).each(function(){ $(this).addClass("ws_ordering"); });
   $("td.ws_status_" + <?php echo WS_ORDERED  ?>).each(function(){ $(this).addClass("ws_ordered");  });
   $("tr.ws_status_" + <?php echo CS_POSTPONE ?>).each(function(){ $(this).addClass("ws_cancel");   });
   $("tr.ws_status_" + <?php echo CS_CANCEL   ?>).each(function(){ $(this).addClass("ws_cancel");   });

   //$(window).resize(function(){ ResizeTable(); });
   // ResizeTable();
});

/* テーブルのサイズ変更
-------------------------------------------------------------*/
function ResizeTable(){
	 $("#content").height($(window).height()-220);
}

/* 検索
-------------------------------------------------------------*/
function Search(){
     $("#WeddingReservingStateTrnViewWeddingPlace").val($("#filter_wedding_place").val());
     $("#WeddingReservingStateTrnViewFirstContactPersonNm").val($("#filter_first_contact_person").val());
     $("#WeddingReservingStateTrnViewProcessPersonNm").val($("#filter_process_person").val());
     $("#WeddingReservingStateTrnViewWeddingDayHotel").val($("#filter_hotel").val());
     $("#WeddingReservingStateTrnViewReceptionPlace").val($("#filter_reception_place").val());
     $("#WeddingReservingStateTrnViewCamera").val($("#filter_camera").val());
     $("#WeddingReservingStateTrnViewHairMake").val($("#filter_hairmake").val());
     $("#WeddingReservingStateTrnViewVideo").val($("#filter_video").val());
     $("#WeddingReservingStateTrnViewFlower").val($("#filter_flower").val());
     $("#WeddingReservingStateTrnViewAttend").val($("#filter_attend").val());
     $("#WeddingReservingStateTrnViewWeddingDtFrom").val($("#filter_wedding_dt_from").val());
     $("#WeddingReservingStateTrnViewWeddingDtTo").val($("#filter_wedding_dt_to").val());

     $("#WeddingReservingStateServiceIndexForm").submit();
}
</script>
<style type="text/css">
.ws_test {
	border-color: #80d047;
	background-color: #dcfcc4;
	color: #222;
	}

.ws_no_order{
	border-color: #c66;
	background-color: #fcc;
	color: #222;
	}

.ws_ordering {
	border-color: #f7c455;
	background-color: #ffffbc;
	color: #222;
	}

.ws_ordered {
	border-color: #6295d6;
	background-color: #d8eaff;
	color: #222;
	}

.ws_cancel {
	background-color: lightgray;
	color: #222;
	}
</style>

<ul class="operate">
    <!--<li><a href="<?php echo $html->url('export/excel_customer_list') ?>" >EXCEL出力</a></li>-->
    <!--<li><a href="#" id="import_link">IMPORT</a></li>-->
	<!--<li><a href="#" id='file_upload_link'>ファイル取り込み(CSV)</a></li>-->
</ul>

<span style='margin-right:5px'>挙式年月：</span>
<input id='filter_wedding_dt_from' class='wedding_date' type='text' value='<?php echo $filter_ws_wedding_dt_from ?>' style='width:80px;margin-right:5px' /><span>～</span>
<input id='filter_wedding_dt_to'   class='wedding_date' type='text' value='<?php echo $filter_ws_wedding_dt_to ?>'   style='width:80px;margin-left:5px;margin-right:5px'/>
<input id="filter_wedding_btn" type="button" class="inputbutton" value="検索"  />

<table style='clear:both'>
 <tr>
   <!-- <td style='vertical-align:baseline;'><div class="ws_no_order" style="width:30px;height:10px;margin-right:5px"></div></td><td style='vertical-align:baseline;'>注文なし</td> -->
   <td style='vertical-align:baseline;'><div class="ws_ordering" style="width:30px;height:10px;margin-right:5px"></div></td><td style='vertical-align:baseline;'>予約中</td>
   <td style='vertical-align:baseline;'><div class="ws_ordered"  style="width:30px;height:10px;margin-right:5px"></div></td><td style='vertical-align:baseline;'>予約済み</td>
   <td style='vertical-align:baseline;'><div class="ws_cancel"   style="width:30px;height:10px;margin-right:5px"></div></td><td style='vertical-align:baseline;'>キャンセル・延期</td>
 </tr>
</table>

       <!-- フィルター用の条件を保持 -->
       <div style="display:none;">
       <?php echo $form->create(null); ?>
  	   <?php echo $form->text('WeddingReservingStateTrnView.wedding_place'           ,array('value' => $filter_ws_wedding_place)); ?>
  	   <?php echo $form->text('WeddingReservingStateTrnView.first_contact_person_nm' ,array('value' => $filter_ws_first_contact_person)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.process_person_nm'       ,array('value' => $filter_ws_process_person)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.wedding_day_hotel'       ,array('value' => $filter_ws_hotel)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.reception_place'         ,array('value' => $filter_ws_reception_place)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.camera'                  ,array('value' => $filter_ws_camera)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.hair_make'               ,array('value' => $filter_ws_hairmake)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.video'                   ,array('value' => $filter_ws_video)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.flower'                  ,array('value' => $filter_ws_flower)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.attend'                  ,array('value' => $filter_ws_attend)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.wedding_dt_from'         ,array('value' => $filter_ws_wedding_dt_from)); ?>
	   <?php echo $form->text('WeddingReservingStateTrnView.wedding_dt_to'           ,array('value' => $filter_ws_wedding_dt_to)); ?>
       <?php echo $form->end(); ?>
       </div>

<div id="content" style="width:100%;" >
	  <table class="filterlist" cellspacing="0" >
		<tr>
		    <!-- NO -->
		    <td></td>

		    <!-- 挙式日フィルター -->
		    <td></td>

		    <!-- 挙式会場フィルター -->
		    <td>
		      <select id='filter_wedding_place' class="filter">
				  <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_wedding_place);$i++){
		  	         $val = $list_wedding_place[$i]['WeddingReservingStateTrnView']['wedding_place'];

		            if($filter_ws_wedding_place == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				      }else{
				         echo "<option value='".$val."'>{$val}</option>";
				      }
				  }
				  ?>
		      </select>
		    </td>

			<!-- 挙式時間 -->
			<td></td>

            <!-- 新郎・新婦 -->
		    <td></td>
		    <td></td>

            <!-- 成約担当者フィルター -->
		    <td>
		      <select id='filter_first_contact_person' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_first_contact_person);$i++){
		  	         $val = $list_first_contact_person[$i]['WeddingReservingStateTrnView']['first_contact_person_nm'];

		            if($filter_ws_first_contact_person == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>

		    <!-- 製作担当者フィルター -->
		    <td>
		      <select id='filter_process_person' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_process_person);$i++){
		  	         $val = $list_process_person[$i]['WeddingReservingStateTrnView']['process_person_nm'];

		            if($filter_ws_process_person == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>

		    <!-- Hotelフィルター -->
		    <td>
		      <select id='filter_hotel' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_hotel);$i++){
		  	         $val = $list_hotel[$i]['WeddingReservingStateTrnView']['wedding_day_hotel'];

		            if($filter_ws_hotel == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>

		    <!-- レセプション会場フィルター -->
		    <td>
		      <select id='filter_reception_place' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_reception_place);$i++){
		  	         $val = $list_reception_place[$i]['WeddingReservingStateTrnView']['reception_place'];

		            if($filter_ws_reception_place == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>

            <!-- Rec/H -->
		    <td></td>
		    <!-- MaxPax -->
		    <td></td>

		    <!-- Cameraフィルター -->
		    <td>
		      <select id='filter_camera' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_camera);$i++){
		  	         $val = $list_camera[$i]['WeddingReservingStateTrnView']['camera'];

		            if($filter_ws_camera == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>
		    <td></td>

		    <!-- HariMakeフィルター -->
		    <td>
		      <select id='filter_hairmake' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_hairmake);$i++){
		  	         $val = $list_hairmake[$i]['WeddingReservingStateTrnView']['hair_make'];

		            if($filter_ws_hairmake == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>
		    <td></td>

            <!-- RE H/M -->
		    <td></td>

		    <!-- Videoフィルター -->
		    <td>
		      <select id='filter_video' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_video);$i++){
		  	         $val = $list_video[$i]['WeddingReservingStateTrnView']['video'];

		            if($filter_ws_video == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>
		    <td></td>

		    <!-- FLowerフィルター -->
		    <td>
		      <select id='filter_flower' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_flower);$i++){
		  	         $val = $list_flower[$i]['WeddingReservingStateTrnView']['flower'];

		            if($filter_ws_flower == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>
		    <td></td>

		    <!-- Attendフィルター -->
		    <td>
		      <select id='filter_attend' class="filter">
		        <option value='-1' selected>ALL</option>
				  <?php
		  	     for($i=0;$i < count($list_attend);$i++){
		  	         $val = $list_attend[$i]['WeddingReservingStateTrnView']['attend'];
		            if($filter_ws_attend == $val){
		           	 echo "<option value='".$val."' selected>{$val}</option>";
				  }else{
				  echo "<option value='".$val."'>{$val}</option>";
				  }
				  }
				  ?>
		      </select>
		    </td>
		    <td></td>

            <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		    <td></td>
		</tr>
		<tr>
		    <th>No</th>
		    <th><a href="#" >挙式日</a></th>
		    <th>場所</th>
			<th>時間</th>
		    <th><a href="">新郎</a></th>
		    <th><a href="">新婦名</a></th>
		    <th>成約担当者</th>
		    <th>製作担当者</th>
		    <th>HOTEL</th>
		    <th>レセプション会場</th>
		    <th>Rec/H</th>
		    <th>Max Pax</th>
		    <th>Camera</th>
		    <th>Camera備考</th>
		    <th>Hair Make</th>
		    <th>Hair Make備考</th>
		    <th>RE H/M</th>
		    <th>Video</th>
		    <th>Video備考</th>
		    <th>Flower</th>
		    <th>Flower備考</th>
		    <th>Attend</th>
		    <th>Attend備考</th>
		    <th>Briefing  DateTime</th>
		    <th>導線1</th>
		    <th>導線2</th>
		    <th>紹介者</th>
		    <th>Slideshow</th>
		    <th>ShortFilm1</th>
		    <th>ShortFilm2</th>
		    <th>VisionarySS</th>
		    <th>Visionary Dater</th>
		    <th>お礼内容</th>
		    <th>ステータス</th>
		</tr>

<?php
		  	for($i=0;$i < count($data);$i++){

		   	 $atr = $data[$i]['WeddingReservingStateTrnView'];
		  	  echo "<tr class='ws_status_{$atr['status_id']}'>".
		  	         "<td><a href='".$html->url('edit')."/{$atr['id']}'>".($i+1)."</a></td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['wedding_dt'])."</td>";

		             if($atr['church_code'] == ""){
		               echo "<td class='ws_status_{$atr['site_status']}'>".$common->evalNbsp($atr['wedding_place'])."</td>";
		             }else{
		               echo "<td class='ws_status_{$atr['site_status']}'>".$common->evalNbsp($atr['church_code'])."</td>";
		             }

		      echo  "<td>".$atr['wedding_time']."</td>";

		  	         if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
		  	         	echo "<td>".$common->evalNbsp($atr['grmls_kj'])."&nbsp".$common->evalNbsp($atr['grmfs_kj'])."</td>";
		  	         }else{
		  	         	echo "<td>".$common->evalNbsp($atr['grmls_kn'])."&nbsp".$common->evalNbsp($atr['grmfs_kn'])."</td>";
		  	         }

		             if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
		  	         	echo "<td>".$common->evalNbsp($atr['brdls_kj'])."&nbsp".$common->evalNbsp($atr['brdfs_kj'])."</td>";
		  	         }else{
		  	         	echo "<td>".$common->evalNbsp($atr['brdls_kn'])."&nbsp".$common->evalNbsp($atr['brdfs_kn'])."</td>";
		  	         }

		  	  echo   "<td>".$common->evalNbsp($atr['first_contact_person_nm'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['process_person_nm'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['wedding_day_hotel'])."</td>".
		  	         "<td class='ws_status_{$atr['reception_status']}'>".$common->evalNbsp($atr['reception_place'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['reception_time'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['max_pax'])."</td>".
		  	         "<td class='ws_status_{$atr['camera_status']}'>".$common->evalNbsp($atr['camera'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['camera_note'])."</td>".

		  	         "<td class='ws_status_{$atr['hair_make_status']}'>".$common->evalNbsp($atr['hair_make'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['hair_make_note'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['hair_make_dt'])."</td>".

		  	         "<td class='ws_status_{$atr['video_status']}'>".$common->evalNbsp($atr['video'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['video_note'])."</td>".

		  	         "<td class='ws_status_{$atr['flower_status']}'>".$common->evalNbsp($atr['flower'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['flower_note'])."</td>".

		  	         "<td class='ws_status_{$atr['attend_status']}'>".$common->evalNbsp($atr['attend'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['attend_note'])."</td>".

		  	         "<td>".$atr['briefing_dt']."</td>".


		  	         "<td>".$leading1_list[$atr['leading1']]."</td>".
		  	         "<td>".$leading2_list[$atr['leading2']]."</td>".

		  	         "<td>".$common->evalNbsp($atr['introducer'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['slide_show'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['short_film1'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['short_film2'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['visionari_ss'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['visionari_dater'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['gratitude'])."</td>".
		  	         "<td>".$common->evalNbsp($atr['status_nm'])."</td>";
		  	  echo "</tr>";
		  }
		?>
      </table>
</div>
<div class="pagination">
      <?php
        echo $paginator->prev('前ページ');
        echo $paginator->numbers();
        echo $paginator->next('次ページ');
      ?>
        <span>表示件数:</span>
        <select id="limit" name="limit">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="50">50</option>
            <option value="80">80</option>
            <option value="100">100</option>
        </select>
</div>

<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>

