<script type='text/javascript'>
$(function(){

   $("#wedding_dt").change(function(){

      $("#GoodsMstViewWeddingPlannedDt").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });

   $("#attendant").change(function(){

      $("#GoodsMstViewAttendant").val($(this).val());
      $("#CustomerMstIndexForm").submit();
   });

   ColorNameSake(".contract_customer_nm");
   ColorNameSake(".wedding_customer_nm");

   $(window).resize(function(){  ResizeTable(); });
   ResizeTable();
});

/* コンテンツの高さ調整
------------------------------------------------*/
function ResizeTable(){
	$("#customers_contract_list").height($(window).height()-200);
}

/* 同姓同名の顧客をハイライトする
------------------------------------------------*/
function ColorNameSake(obj){

	$(obj).each(function(mainIndex){

		   var target = $(this).text().split('.')[1];
	       var found = false
		   $(obj).each(function(subIndex){

			   if(subIndex > mainIndex){
	                if(target == $(this).text().split('.')[1]){
	                    $(this).css("color","red");
	                    found=true;
	                }
			   }
		   });
		   if(found){ $(this).css("color","red"); }
	   });
}
</script>

<ul class="operate">
   <li><a href="<?php echo $html->url('export') ?>">EXCEL出力</a></li>
</ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null);
         echo $form->text('GoodsMstView.wedding_planned_dt' ,array('value' => $wedding_dt));
         echo $form->text('GoodsMstView.attendant' ,array('value' => $attendant));
         echo $form->end(); ?>
 </div>

 <div class='notation'>
   <label style='font-size:1.2em'>表示スタート年月(以降<?php echo $showing_month_count ?>ヶ月分)：</label>
   <select id='wedding_dt'>
		<?php
		  $found = false;
		  for($i=0;$i < count($wedding_dt_list);$i++)
          {
		           if($wedding_dt == $wedding_dt_list[$i]){
		         	 echo "<option value='".$wedding_dt_list[$i]."' selected>{$wedding_dt_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$wedding_dt_list[$i]."'>{$wedding_dt_list[$i]}</option>";
		           }
		   }
		   /* 初期表示はログイン年月を基にするがログイン年月に挙式がない場合はもあるので、その場合は項目のみ作成しておく  */
		   if($found==false){
		     	 echo "<option value='".$wedding_dt."' selected>{$wedding_dt}</option>";
		   }
	    ?>
   </select>

   <label style='font-size:1.2em'>担当者：</label>
   <select id='attendant'>
		<?php
		  $found = false;
		  for($i=0;$i < count($attendant_list);$i++)
          {
		           if($attendant == $attendant_list[$i]){
		         	 echo "<option value='".$attendant_list[$i]."' selected>{$attendant_list[$i]}</option>";
                     $found = true;
		           }else{
		         	 echo "<option value='".$attendant_list[$i]."'>{$attendant_list[$i]}</option>";
		           }
		   }
		   if($found==false){
		     	 echo "<option value='".$attendant."' selected>{$attendant}</option>";
		   }
	    ?>
   </select>
 </div>

<div id="customers_contract_list" style="width:100%;overflow-x:scroll;overflow-y:scroll;" >
    <table id="customers_table" class="list" cellspacing="0" style="width:100%;height:50%">

	<tr>
	<?php
      for($i=0;$i < $showing_month_count ; $i++){
         echo "<th colspan='4' style='text-align: center;'>".date('Y-m',strtotime($wedding_dt." +".$i." month"))."</th>";
      }
    ?>
	</tr>
	<tr><td colspan="48">【挙式一覧】</td></tr>
<?php

    for($i=0;;$i++)
    {
        $count = 0;
        for($j=0 ; $j < $showing_month_count;$j++){
           if(count($wedding_data[$j]) <= $i){ $count++; }
        }
        if($count == $showing_month_count){break;}

    	$customers = array();
    	for($j=0 ; $j < $showing_month_count ;$j++){
    		$customers[$j]['name'] = count($wedding_data[$j]) <= $i ? "&nbsp;" : $wedding_data[$j][$i]['customer_nm'];
    		$customers[$j]['code'] = count($wedding_data[$j]) <= $i ? "&nbsp;" : $wedding_data[$j][$i]['customer_cd'];
    		$customers[$j]['id'] = count($wedding_data[$j]) <= $i ? "&nbsp;" : $wedding_data[$j][$i]['customer_id'];
    		$customers[$j]['first_contact_person_nm'] = count($wedding_data[$j]) <= $i ? "&nbsp;" : $wedding_data[$j][$i]['first_contact_person_nm'];
    		$customers[$j]['process_person_nm'] = count($wedding_data[$j]) <= $i ? "&nbsp;" : $wedding_data[$j][$i]['process_person_nm'];
    	}

    	echo "<tr>";
    	for($j=0 ; $j < $showing_month_count ;$j++){
           if($customers[$j]['name'] == "&nbsp;"){
             	echo "<td style='text-align: left; width: 100px;'><a href='".$html->url('/customersList/goToCustomerInfo/'.$customers[$j]['id'])."'>".$customers[$j]['name']."</a></td>";
           }else{
             	echo "<td style='text-align: left; width: 100px;'><a href='".$html->url('/customersList/goToCustomerInfo/'.$customers[$j]['id'])."' class='wedding_customer_nm'>".($i+1).'.'.$customers[$j]['name']."</a></td>";
           }
           echo "<td style='text-align: center; width: 100px;'>".$customers[$j]['code']."</td>";
           echo "<td style='text-align: center; width: 100px;'>".$customers[$j]['first_contact_person_nm']."</td>";
           echo "<td style='text-align: center; width: 100px;'>".$customers[$j]['process_person_nm']."</td>";
    	}
    	echo "</tr>";
    }

?>
    <tr>
	<?php
      for($i=0;$i < $showing_month_count ; $i++){
         echo "<th colspan='4' style='text-align: center'>".date('Y-m',strtotime($wedding_dt." +".$i." month"))."</th>";
      }
    ?>

	</tr>
	<tr><td colspan="48">【約定一覧】</td></tr>
	<?php

    for($i=0;;$i++)
    {
        $count = 0;
        for($j=0 ; $j < $showing_month_count;$j++){
           if(count($contract_data[$j]) <= $i){ $count++; }
        }
        if($count == $showing_month_count){break;}

    	$customers = array();
    	for($j=0 ; $j < $showing_month_count;$j++){
    		$customers[$j]['name'] = count($contract_data[$j]) <= $i ? "&nbsp;" : $contract_data[$j][$i]['customer_nm'];
    		$customers[$j]['code'] = count($contract_data[$j]) <= $i ? "&nbsp;" : $contract_data[$j][$i]['customer_cd'];
    		$customers[$j]['id']   = count($contract_data[$j]) <= $i ? "&nbsp;" : $contract_data[$j][$i]['customer_id'];
    		$customers[$j]['first_contact_person_nm'] = count($contract_data[$j]) <= $i ? "&nbsp;" : $contract_data[$j][$i]['first_contact_person_nm'];
    		$customers[$j]['process_person_nm']       = count($contract_data[$j]) <= $i ? "&nbsp;" : $contract_data[$j][$i]['process_person_nm'];
    	}

    	echo "<tr>";
    	for($j=0 ; $j < $showing_month_count ;$j++){
           if($customers[$j]['name'] == "&nbsp;"){
	           echo "<td style='text-align: left; width: 100px;'><a href='".$html->url('/customersList/goToCustomerInfo/'.$customers[$j]['id'])."'>".$customers[$j]['name']."</a></td>";
           }else{
	           echo "<td style='text-align: left; width: 100px;'><a href='".$html->url('/customersList/goToCustomerInfo/'.$customers[$j]['id'])."' class='contract_customer_nm'>".($i+1).'.'.$customers[$j]['name']."</a></td>";
           }
           echo "<td style='text-align: center; width:100px'>".$customers[$j]['code']."</td>";
           echo "<td style='text-align: center; width:100px'>".$customers[$j]['first_contact_person_nm']."</td>";
           echo "<td style='text-align: center; width:100px'>".$customers[$j]['process_person_nm']."</td>";
    	}
    	echo "</tr>";
    }
?>
    <tr>
      <?php
       for($i=0;$i < $showing_month_count ; $i++){
    	 echo "<th colspan='4' style='text-align: center'>".date('Y-m',strtotime($wedding_dt." +".$i." month"))."</th>";
       }
      ?>
    </tr>
    </table>
</div>
