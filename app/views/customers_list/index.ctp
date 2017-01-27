<script type='text/javascript'>
$(function(){
   $("input:button").button();

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

   /* ステータスが問い合わせ又は見積提示済みの時のみアクション項目を表示する */
   if($("#customer_status").val() != <?php echo CS_CONTACT ?> && $("#customer_status").val() != <?php echo CS_ESTIMATED ?>){
      $(".action").css("display","none");
   }

   /* ステータスが見積提示済み又は仮約定の時のみ新規担当者項目を表示する */
   if($("#customer_status").val() != <?php echo CS_ESTIMATED ?> && $("#customer_status").val() != <?php echo CS_CONTRACTING ?>){
      $(".first_attendant").css("display","none");
   }

   /* ステータスが成約以降の時のみプラン担当者項目を表示する */
   if($("#customer_status").val() != <?php echo CS_CONTRACTED ?> && $("#customer_status").val() != <?php echo CS_INVOICED ?> &&
      $("#customer_status").val() != <?php echo CS_UNPAIED ?>    && $("#customer_status").val() != <?php echo CS_PAIED ?>){
      $(".process_attendant").css("display","none");
   }

   //選択カテゴリが変わったら検索して再表示
   $("#customer_status").change(function(){
      $("#GoodsMstViewStatusId").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#wedding_dt").change(function(){
      $("#GoodsMstViewWeddingPlannedDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#first_contact_dt").change(function(){
      $("#GoodsMstViewFirstContactDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#estimate_issued_dt").change(function(){
      $("#GoodsMstViewEstimateIssuedDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
    $("#pref").change(function(){
      $("#GoodsMstViewPref").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#search_customer_button").click(function(){
      $("#GoodsMstViewCustomerName").val($("#customer_name").val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#wedding_dt_order_link").click(function(){
      if($(this).attr('order').toUpperCase()=="ASC"){
        $("#GoodsMstViewWeddingPlannedDtOrder").val("DESC");
      }else{
        $("#GoodsMstViewWeddingPlannedDtOrder").val("ASC");
      }
		$("#CustomerMstIndexForm").submit();
   });
    $("#first_contact_person_list").change(function(){
      $("#GoodsMstViewFirstContactPersonName").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#process_person_list").change(function(){
      $("#GoodsMstViewProcessPersonName").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });
   $("#non_display_flg").change(function(){
      if($("#non_display_flg").prop("checked")){
         $("#GoodsMstViewNonDisplayFlg").val(0);
      }else{
          $("#GoodsMstViewNonDisplayFlg").val(1);
      }
      $("#CustomerMstIndexForm").submit();
   });
   $("#search_phone_no_button").click(function(){
      $("#GoodsMstViewPhoneNo").val($("#phone_no").val());
      $("#CustomerMstIndexForm").submit();
   });

   /* フィルターの設定値を全てALLに戻す */
   $("#clear_filter").click(function(){
      $("#GoodsMstViewStatusId").val(-1);
      $("#GoodsMstViewWeddingPlannedDt").val(-1);
      $("#GoodsMstViewFirstContactDt").val(-1);
	  $("#GoodsMstViewEstimateIssuedDt").val(-1);
      $("#GoodsMstViewPref").val(-1);
      $("#GoodsMstViewCustomerName").val("");
      $("#GoodsMstViewFirstContacntPersonName").val("");
      $("#GoodsMstViewProcessPersonName").val("");
      $("#GoodsMstViewNonDisplayFlg").val(0);
      $("#GoodsMstViewPhoneNo").val("");
      $("#CustomerMstIndexForm").submit();
   });

   /* 顧客名検索フィールドでEnter keyが押下されたら実行する */
   $("#customer_name").keydown(function(e){
       if(e.keyCode == 13){
            $("#search_customer_button").click();
       }
   });

   /* 顧客番号検索フィールドでEnter keyが押下されたら実行する */
   $("#phone_no").keydown(function(e){
       if(e.keyCode == 13){
            $("#search_phone_no_button").click();
       }
   });

   //顧客名フィールドにフォーカス
   $("#customer_name").focus();

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

    /* 顧客スタータス更新 */
   $("#update_customer_status_link").click(function(){

      	$(this).simpleLoading('show');

		$.get(<?php echo "'".$html->url("updateWeddingCustomerStatus")."'" ?>, function(result) {

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
});
</script>

<ul class="operate">
    <li><a href="<?php echo $html->url('addCustomer') ?>">顧客追加</a></li>

    <?php
        /* 管理者のみ */
       if(UC_ADMIN == $user['User']['user_kbn_id']){
         echo "<li><a href='{$html->url('export/excel_customer_list')}'  >EXCEL出力</a></li>";
         echo "<li><a href='{$html->url('export/excel_new_years_card')}' >EXCEL出力【年賀状用】</a></li>";
         echo "<li><a href='{$html->url('export/excel_customer_mail')}'  >EXCEL出力【メール用】</a></li>";
       }
    ?>
    <!-- <li><a href="#" id="update_customer_code_link">顧客コード更新</a></li> -->
    <li><a href="#" id="update_customer_status_link">顧客ステータス更新</a></li>
</ul>

       <!-- ページネーションする時にフィルタ条件を引き継ぐためにパラメータを追加 -->
       <?php  $paginator->options(array('url' => array('status_id'          => $status_id,
                                                       'non_display_flg'    => $non_display_flg,
                                                       'wedding_planned_dt' => $wedding_planned_dt,
                                                       'first_contact_dt' => $first_contact_dt,
                                                       'pref' => $pref,
                                                       'customer_name' => $customer_name,
                                                       'first_contact_person_name' => $first_contact_person_name,
                                                       'process_person_name' => $process_person_name,
                                                       'estimate_issued_dt'=>$estimate_issued_dt,
                                                       'phone_no'=>$phone_no
                                  )));
       ?>

       <!-- フィルター用の条件を保持   -->
       <div style="display:none;">
       <?php echo $form->create(null); ?>
  	   <?php echo $form->text('GoodsMstView.status_id',array('value' => $status_id)); ?>
  	   <?php echo $form->text('GoodsMstView.non_display_flg',array('value' => $non_display_flg)); ?>
	   <?php echo $form->text('GoodsMstView.wedding_planned_dt' ,array('value' => $wedding_planned_dt)); ?>
	   <?php echo $form->text('GoodsMstView.customer_name' ,array('value' => $customer_name)); ?>
	   <?php echo $form->text('GoodsMstView.first_contact_dt' ,array('value' => $first_contact_dt)); ?>
	   <?php echo $form->text('GoodsMstView.estimate_issued_dt' ,array('value' => $estimate_issued_dt)); ?>
	   <?php echo $form->text('GoodsMstView.pref' ,array('value' => $pref)); ?>
	   <?php echo $form->text('GoodsMstView.wedding_planned_dt_order' ,array('value' => $wedding_planned_dt_order)); ?>
	   <?php echo $form->text('GoodsMstView.first_contact_person_name' ,array('value' => $first_contact_person_name)); ?>
	   <?php echo $form->text('GoodsMstView.process_person_name' ,array('value' => $process_person_name)); ?>
	   <?php echo $form->text('GoodsMstView.phone_no' ,array('value' => $phone_no)); ?>
       <?php echo $form->end(); ?>
       </div>

<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
	  <table class="filterlist" cellspacing="0" >
		<tr>
		    <td colspan="2"><span style="margin-right:5px">非表示除外</span>
               <?php
   			     if($non_display_flg == 0){
   			     	echo  "<input type='checkbox' id='non_display_flg' checked />";
   			     }else{
   			   	    echo  "<input type='checkbox' id='non_display_flg' />";
   			     }
   	           ?>
           </td>
           <td colspan="3">
             <input id="clear_filter" type="button" class="inputbutton" value="Clear"  />
             <input type="text" id="customer_name" value="<?php echo $customer_name ?>" /><input id="search_customer_button" type="image"  src="<?php echo $html->webroot("/images/search.png"); ?>"  style="margin-left:3px;" />
           </td>
		   <td>
		      <select id='pref'>
		        <option value='-1' selected>ALL</option>
		         <?php
		  	     for($i=0;$i < count($division_list);$i++)
		         {
		            if($pref == $i){
		           	 echo "<option value='".$i."' selected>{$division_list[$i]}</option>";
		           }else{
		         	 echo "<option value='".$i."'>{$division_list[$i]}</option>";
		           }
		         }
		       ?>
		      </select>
		   </td>
		   <td>
		      <input type="text" id="phone_no" value="<?php echo $phone_no ?>" />
		      <input id="search_phone_no_button" type="image"  src="<?php echo $html->webroot("/images/search.png"); ?>"  style="margin-left:3px;" />
		   </td>
           <td>&nbsp;</td>
           <td>
             <select id='wedding_dt'>
		        <option value='-1' selected>ALL</option>
		         <?php
		         $found = false;
		  	     for($i=0;$i < count($wedding_dt_list);$i++)
		         {
		           if($wedding_planned_dt == $wedding_dt_list[$i]){
		         	 echo "<option value='".$wedding_dt_list[$i]."' selected>{$wedding_dt_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$wedding_dt_list[$i]."'>{$wedding_dt_list[$i]}</option>";
		           }
		         }
		         /* 初期表示はログイン年月を基にするがログイン年月に挙式がない場合はもあるので、その場合は項目のみ作成しておく  */
		         if($found==false && $wedding_planned_dt != -1){
		         	 echo "<option value='".$wedding_planned_dt."' selected>{$wedding_planned_dt}</option>";
		         }
		       ?>
		    </select>
		   </td>
           <td>
		      <select id='customer_status'>
		        <option value='-1' selected>ALL</option>
		         <?php
		  	     for($i=0;$i < count($customer_status);$i++)
		         {
		  	       $atr = $customer_status[$i]['CustomerStatusMst'];
		  	       if($status_id == $atr['id']){
		  	       	   echo "<option value='".$atr['id']."' selected>{$atr['status_nm']}</option>";
		  	       }else{
		  	       	  echo "<option value='".$atr['id']."'>{$atr['status_nm']}</option>";
		  	       }
		         }
		       ?>
		    </select>
           </td>
           <td class="first_attendant">
		      <!--
		      <select id='first_contact_person_list'>
		        <option value='' selected>ALL</option>
		        <?php
		  	     for($i=0;$i < count($first_contact_person_list);$i++)
		         {
		  	       $atr = $first_contact_person_list[$i];
		  	       if($first_contact_person_name == $atr['first_contact_person_nm']){
		  	       	   echo "<option value='".$atr['first_contact_person_nm']."' selected>{$atr['first_contact_person_nm']}</option>";
		  	       }else{
		  	       	  echo "<option value='".$atr['first_contact_person_nm']."'>{$atr['first_contact_person_nm']}</option>";
		  	       }
		         }
		       ?>
		    </select>
		    -->
		    <td class="process_attendant">
		     <!--
		      <select id='process_person_list'>
		        <option value='' selected>ALL</option>
		         <?php

		  	     for($i=0;$i < count($process_person_list);$i++)
		         {
		  	       $atr = $process_person_list[$i];
		  	       if($process_person_nm == $atr['process_person_nm']){
		  	       	   echo "<option value='".$atr['process_person_nm']."' selected>{$atr['process_person_nm']}</option>";
		  	       }else{
		  	       	  echo "<option value='".$atr['process_person_nm']."'>{$atr['process_person_nm']}</option>";
		  	       }
		         }
		       ?>
		    </select>
		    -->
           </td>
           <td class='action'>&nbsp;</td>
           <td>
              <select id='estimate_issued_dt'>
		        <option value='-1' selected>ALL</option>
		         <?php
		         $found = false;
		  	     for($i=0;$i < count($estimate_issued_dt_list);$i++)
		         {
		           if($estimate_issued_dt == $estimate_issued_dt_list[$i]){
		         	 echo "<option value='".$estimate_issued_dt_list[$i]."' selected>{$estimate_issued_dt_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$estimate_issued_dt_list[$i]."'>{$estimate_issued_dt_list[$i]}</option>";
		           }
		         }
		       ?>
		      </select>
           </td>
		   <td>
		      <select id='first_contact_dt'>
		        <option value='-1' selected>ALL</option>
		         <?php
		         $found = false;
		  	     for($i=0;$i < count($first_contact_dt_list);$i++)
		         {
		           if($first_contact_dt == $first_contact_dt_list[$i]){
		         	 echo "<option value='".$first_contact_dt_list[$i]."' selected>{$first_contact_dt_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$first_contact_dt_list[$i]."'>{$first_contact_dt_list[$i]}</option>";
		           }
		         }
		       ?>
		    </select>
		   </td>
		</tr>
		<tr>
		   <th>No</th>
           <th>顧客番号</th>
           <th>新郎名</th>
           <th>新婦名</th>
		   <th>郵便番号</th>
		   <th>住所</th>
		   <th>電話番号</th>
           <th>メールアドレス</th>
           <th><a id="wedding_dt_order_link" href="#" order='<?php echo $wedding_planned_dt_order ?>' class="sort">挙式日</a></th>
           <th>ステータス</th>
           <th class="first_attendant">新規担当者</th>
           <th class="process_attendant">プラン担当者</th>
           <th class='action'>最新アクション</th>
           <th>初回見積提出日</th>
		   <th>問い合わせ日</th>
		</tr>

		<?php
		  	for($i=0;$i < count($data);$i++)
		    {
		   	 $atr = $data[$i]['CustomerMstView'];
		  	  echo "<tr>".
		  	         "<td>".($i+1)."</td>".
		  	         "<td><a href='".$html->url('goToCustomerInfo')."/{$atr['id']}'>".$common->evalNbsp($atr['customer_cd'])."</a></td>";

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

		  	         if($atr['prm_address_flg'] == 0)
		  	         {
		  	           echo	 "<td>".$common->evalNbsp($atr['grm_zip_cd'])."</td>".
		  	                 "<td><div class='address'>".$common->evalNbsp($atr['grm_address'])."</div></td>";
		  	         }
		  	         else
		  	        {
		  	         	echo "<td>".$common->evalNbsp($atr['brd_zip_cd'])."</td>".
		  	                 "<td><div class='address'>".$common->evalNbsp($atr['brd_address'])."</div></td>";
		  	         }

		  	         if($atr['prm_phone_no_flg'] == 0){

		  	             if(empty($atr['grm_cell_no'])){
		  	               echo "<td>".$common->evalNbsp($atr['grm_phone_no'])."</td>";
		  	             }else{
		  	               echo "<td>".$common->evalNbsp($atr['grm_cell_no'])."</td>";
		  	             }
		  	         }
		  	         else{
		  	             if(empty($atr['brd_cell_no'])){
		  	               echo "<td>".$common->evalNbsp($atr['brd_phone_no'])."</td>";
		  	             }else{
		  	               echo "<td>".$common->evalNbsp($atr['brd_cell_no'])."</td>";
		  	             }
		  	         }

		            if($atr['prm_email_flg'] == 0)
		  	         {
		  	         	 echo "<td><div class='email'>".$common->evalNbsp($atr['grm_email'])."</div></td>";
		  	         }
		  	         else
		  	        {
		  	         	 echo "<td><div class='email'>".$common->evalNbsp($atr['brd_email'])."</div></td>";
		  	         }

		  	         if($atr['status_id']>=CS_CONTRACTED){
		  	         	echo "<td>".$common->evalNbspForShortDate($atr['wedding_dt'])."</td>";
		  	         }else{
		  	         	echo "<td>".$common->evalNbspForShortDate($atr['wedding_planned_dt'])."</td>";
		  	         }

		  	        echo "<td>".$atr['status_nm']."</td>";
		  	        echo "<td class='first_attendant'>".$atr['first_contact_person_nm']."</td>";
		  	        echo "<td class='process_attendant'>".$atr['process_person_nm']."</td>";

		  	   echo  "<td class='action'>".$common->evalNbspForShortDate($atr['action_dt1'])." ".$common->evalNbsp($atr['action_nm1'])."</td>".
		  	         "<td>".$common->evalNbspForShortDate($atr['estimate_issued_dt'])."</td>".
                     "<td>".$common->evalNbspForShortDate($atr['first_contact_dt'])."</td>".
		  	        "</tr>";
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
                  表示件数:
              <select id="limit" name="limit">
                 <option value="10">10</option>
                 <option value="20">20</option>
                 <option value="30">30</option>
                 <option value="40">40</option>
                 <option value="50">50</option>
                 <option value="60">60</option>
                 <option value="70">70</option>
                 <option value="80">80</option>
                 <option value="90">90</option>
                 <option value="100">100</option>
              </select>
    </div>

<div id="result_dialog"  style="display:none"><p id="result_message"><img src="#" alt="" /><span></span></p><p id="error_reason"></p></div>
<div id="critical_error"></div>

