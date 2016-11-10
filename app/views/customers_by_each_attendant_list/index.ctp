<script type='text/javascript'>
$(function(){

   $("input:button").button();

   $("#search_btn").click(function(){
      $("#GoodsMstViewEstimateIssuedDt").val($("#estimate_issued_dt").val());
      $("#GoodsMstViewFirstContactPerson").val($("#first_contact_person").val());
      $("#GoodsMstViewProcessPerson").val($("#process_person").val());
      $("#GoodsMstViewShowingMonth").val($("#showing_month").val());
      $("#CustomerMstIndexForm").submit();
   });

   $(window).resize(function(){  ResizeTable(); });
   ResizeTable();
});

/* コンテンツの高さ調整
------------------------------------------------*/
function ResizeTable(){
	$("#customers_list").height($(window).height()-200);
}

</script>

<ul class="operate">
   <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
</ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null);
         echo $form->text('GoodsMstView.estimate_issued_dt' ,array('value' => $estimate_issued_dt));
         echo $form->text('GoodsMstView.first_contact_person' ,array('value' => $first_contact_person));
         echo $form->text('GoodsMstView.process_person' ,array('value' => $process_person));
         echo $form->text('GoodsMstView.showing_month' ,array('value' => $showing_month));
         echo $form->end(); ?>
 </div>

 <div class='notation'>
   <label style='font-size:1.2em'>表示基準年月:</label>
   <select id='estimate_issued_dt'>
		<?php
		  $found = false;
		  for($i=0;$i < count($search_dt_list);$i++){
		      if($estimate_issued_dt == $search_dt_list[$i]){
		       	 echo "<option value='".$search_dt_list[$i]."' selected>{$search_dt_list[$i]}</option>";
                 $found = true;
		      }else{
		      	 echo "<option value='".$search_dt_list[$i]."'>{$search_dt_list[$i]}</option>";
		      }
		   }
		   /* 初期表示はログイン年月を基にするがログイン年月に挙式がない場合はもあるので、その場合は項目のみ作成しておく  */
		   if($found==false){
		     	 echo "<option value='".$search_dt_list."' selected>{$search_dt_list}</option>";
		   }
	    ?>
   </select>

   <label style='font-size:1.2em;margin-left:10px;'>表示月数:</label>
   <select id='showing_month'>
		<?php
		  $found = false;
		  for($i=1;$i <= 12;$i++){
		     if($i == $showing_month){
		       	 echo "<option value='".$i."' selected>".$i."</option>";
		     }else{
		       	 echo "<option value='".$i."'>".$i."</option>";
		     }
		   }
	    ?>
   </select>

   <label style='font-size:1.2em;margin-left:10px;'>新規担当者：</label>
   <select id='first_contact_person'>
		<?php
		  $found = false;
		  for($i=0;$i < count($first_contact_person_list);$i++)
          {
		           if($first_contact_person == $first_contact_person_list[$i]){
		         	 echo "<option value='".$first_contact_person_list[$i]."' selected>{$first_contact_person_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$first_contact_person_list[$i]."'>{$first_contact_person_list[$i]}</option>";
		           }
		   }
		   if($found==false){
		     	 echo "<option value='".$first_contact_person."' selected>{$first_contact_person}</option>";
		   }
	    ?>
   </select>

   <label style='font-size:1.2em;margin-left:10px;'>プラン担当者：</label>
   <select id='process_person'>
		<?php
		  $found = false;
		  for($i=0;$i < count($process_person_list);$i++)
          {
		           if($process_person == $process_person_list[$i]){
		         	 echo "<option value='".$process_person_list[$i]."' selected>{$process_person_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$process_person_list[$i]."'>{$process_person_list[$i]}</option>";
		           }
		   }
		   if($found==false){
		     	 echo "<option value='".$process_person."' selected>{$process_person}</option>";
		   }
	    ?>
   </select>

   <input type="button" id="search_btn" style="margin-left:10px;" value="検索" />
 </div>

<div id="customers_list" style="width:100%;overflow-x:scroll;overflow-y:scroll;" >
    <table class="list" cellspacing="0">

	<tr>
	    <th>No</th>
	    <th>顧客番号</th>
        <th>顧客名</th>
        <th>挙式日</th>
        <th>ステータス</th>
        <th>新規担当者</th>
        <th>プラン担当者</th>
        <th>最新アクション</th>
        <th>初回見積提出日</th>
		<th>問い合わせ日</th>
	</tr>
	<?php
	echo "<tr><td colspan='10' style='font-size:1.5em;font-weight:bold'>【成約候補】</td></tr>";

	$counter = 1;
    $currentDate = "";
    for($i=0;$i < count($estimate_data);$i++){

      $atr = $estimate_data[$i]['CustomerMstView'];
      if($currentDate == "" || $currentDate != date('Y-m',strtotime($atr["estimate_issued_dt"]))){
        echo "<tr><td colspan='10'>【".date('Y-m',strtotime($atr["estimate_issued_dt"]))."】</td></tr>";
        $currentDate = date('Y-m',strtotime($atr["estimate_issued_dt"]));
        $counter=1;
      }
      echo "<tr>";
      echo "<td>".$counter."</td>";
	  echo "<td><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['id'])."'>".$common->evalNbsp($atr['customer_cd'])."</a></td>";

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kj'])."&nbsp".$common->evalNbsp($atr['grmfs_kj'])."</td>";
      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kn'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kn'])."&nbsp".$common->evalNbsp($atr['grmfs_kn'])."</td>";
      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['brdls_kj'])."&nbsp".$common->evalNbsp($atr['brdfs_kj'])."</td>";
      }else{
        echo "<td>".$common->evalNbsp($atr['brdls_kn'])."&nbsp".$common->evalNbsp($atr['brdfs_kn'])."</td>";
      }

      echo "<td>".$common->evalNbspForShortDate($atr['wedding_planned_dt'])."</td>";
      echo "<td>".$atr["status_nm"]."</td>";
      echo "<td>".$atr["first_contact_person_nm"]."</td>";
      echo "<td>".$atr["process_person_nm"]."</td>";
      echo "<td>".$common->evalNbsp(mb_substr($atr['action_nm1'],0,50))."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['estimate_issued_dt'])."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['first_contact_dt'])."</td>";
      echo "</tr>";
      $counter++;
    }

    echo "<tr><td colspan='10' style='font-size:1.5em;font-weight:bold'>【当月成約】</td></tr>";

     $counter = 1;
     for($i=0;$i < count($contract_data);$i++){

      $atr = $contract_data[$i]['ContractTrnView'];
      echo "<tr>";
      echo "<td>".$counter."</td>";
	  echo "<td><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['customer_id'])."'>".$common->evalNbsp($atr['customer_cd'])."</a></td>";

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kj'])."&nbsp".$common->evalNbsp($atr['grmfs_kj'])."</td>";
      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kn'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kn'])."&nbsp".$common->evalNbsp($atr['grmfs_kn'])."</td>";
      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['brdls_kj'])."&nbsp".$common->evalNbsp($atr['brdfs_kj'])."</td>";
      }else{
        echo "<td>".$common->evalNbsp($atr['brdls_kn'])."&nbsp".$common->evalNbsp($atr['brdfs_kn'])."</td>";
      }

      echo "<td>".$common->evalNbspForShortDate($atr['wedding_dt'])."</td>";
      echo "<td>".$atr["status_nm"]."</td>";
      echo "<td>".$atr["first_contact_person_nm"]."</td>";
      echo "<td>".$atr["process_person_nm"]."</td>";
      echo "<td style='overflow:hidden'>".$common->evalNbsp(mb_substr($atr['latest_action'],0,50))."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['estimate_issued_dt'])."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['first_contact_dt'])."</td>";
      echo "</tr>";
      $counter++;
    }
    
    echo "<tr><td colspan='10' style='font-size:1.5em;font-weight:bold'>【当月挙式】</td></tr>";

     $counter = 1;
     for($i=0;$i < count($wedding_data);$i++){

      $atr = $wedding_data[$i]['ContractTrnView'];
      echo "<tr>";
      echo "<td>".$counter."</td>";
	  echo "<td><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['customer_id'])."'>".$common->evalNbsp($atr['customer_cd'])."</a></td>";

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kj'])."&nbsp".$common->evalNbsp($atr['grmfs_kj'])."</td>";
      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kn'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kn'])."&nbsp".$common->evalNbsp($atr['grmfs_kn'])."</td>";
      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['brdls_kj'])."&nbsp".$common->evalNbsp($atr['brdfs_kj'])."</td>";
      }else{
        echo "<td>".$common->evalNbsp($atr['brdls_kn'])."&nbsp".$common->evalNbsp($atr['brdfs_kn'])."</td>";
      }

      echo "<td>".$common->evalNbspForShortDate($atr['wedding_dt'])."</td>";
      echo "<td>".$atr["status_nm"]."</td>";
      echo "<td>".$atr["first_contact_person_nm"]."</td>";
      echo "<td>".$atr["process_person_nm"]."</td>";
      echo "<td style='overflow:hidden'>".$common->evalNbsp(mb_substr($atr['latest_action'],0,50))."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['estimate_issued_dt'])."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['first_contact_dt'])."</td>";
      echo "</tr>";
      $counter++;
    }

    echo "<tr><td colspan='10' style='font-size:1.5em;font-weight:bold'>【翌月挙式】</td></tr>";

     $counter = 1;
     for($i=0;$i < count($next_wedding_data);$i++){

      $atr = $next_wedding_data[$i]['ContractTrnView'];
      echo "<tr>";
      echo "<td>".$counter."</td>";
	  echo "<td><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['customer_id'])."'>".$common->evalNbsp($atr['customer_cd'])."</a></td>";

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kj'])."&nbsp".$common->evalNbsp($atr['grmfs_kj'])."</td>";
      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kn'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kn'])."&nbsp".$common->evalNbsp($atr['grmfs_kn'])."</td>";
      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['brdls_kj'])."&nbsp".$common->evalNbsp($atr['brdfs_kj'])."</td>";
      }else{
        echo "<td>".$common->evalNbsp($atr['brdls_kn'])."&nbsp".$common->evalNbsp($atr['brdfs_kn'])."</td>";
      }

      echo "<td>".$common->evalNbspForShortDate($atr['wedding_dt'])."</td>";
      echo "<td>".$atr["status_nm"]."</td>";
      echo "<td>".$atr["first_contact_person_nm"]."</td>";
      echo "<td>".$atr["process_person_nm"]."</td>";
      echo "<td style='overflow:hidden'>".$common->evalNbsp(mb_substr($atr['latest_action'],0,50))."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['estimate_issued_dt'])."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['first_contact_dt'])."</td>";
      echo "</tr>";
      $counter++;
    }

    echo "<tr><td colspan='10' style='font-size:1.5em;font-weight:bold'>【制作対象】</td></tr>";

     $counter = 1;
     $currentDate = "";
     for($i=0;$i < count($future_wedding_data);$i++){

      $atr = $future_wedding_data[$i]['ContractTrnView'];
      
      if($currentDate == "" || $currentDate != date('Y-m',strtotime($atr["wedding_dt"]))){
        echo "<tr><td colspan='10'>【".date('Y-m',strtotime($atr["wedding_dt"]))."】</td></tr>";
        $currentDate = date('Y-m',strtotime($atr["wedding_dt"]));
        $counter=1;
      }
      
      echo "<tr>";
      echo "<td>".$counter."</td>";
	  echo "<td><a href='".$html->url('/customersList/goToCustomerInfo/'.$atr['customer_id'])."'>".$common->evalNbsp($atr['customer_cd'])."</a></td>";

      if($atr['grmls_kj'] != "" || $atr['grmfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kj'])."&nbsp".$common->evalNbsp($atr['grmfs_kj'])."</td>";
      }else if($atr['grmls_kn'] != "" || $atr['grmfs_kn'] != ""){
       	echo "<td>".$common->evalNbsp($atr['grmls_kn'])."&nbsp".$common->evalNbsp($atr['grmfs_kn'])."</td>";
      }else if($atr['brdls_kj'] != "" || $atr['brdfs_kj'] != ""){
       	echo "<td>".$common->evalNbsp($atr['brdls_kj'])."&nbsp".$common->evalNbsp($atr['brdfs_kj'])."</td>";
      }else{
        echo "<td>".$common->evalNbsp($atr['brdls_kn'])."&nbsp".$common->evalNbsp($atr['brdfs_kn'])."</td>";
      }

      echo "<td>".$common->evalNbspForShortDate($atr['wedding_dt'])."</td>";
      echo "<td>".$atr["status_nm"]."</td>";
      echo "<td>".$atr["first_contact_person_nm"]."</td>";
      echo "<td>".$atr["process_person_nm"]."</td>";
      echo "<td style='overflow:hidden'>".$common->evalNbsp(mb_substr($atr['latest_action'],0,50))."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['estimate_issued_dt'])."</td>";
      echo "<td>".$common->evalNbspForShortDate($atr['first_contact_dt'])."</td>";
      echo "</tr>";
      $counter++;
    }

    ?>
    </table>
</div>






