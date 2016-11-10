<?php 
$this->addScript($javascript->codeBlock( <<<JSPROG
$(function(){
   
   /* 選択された挙式年月の顧客の見積データを取得する */
   $("#wedding_dt").change(function(){
      $("#GoodsMstViewWeddingPlannedDt").val($(this).val());    
      $("#FundManagementTrnViewIndexForm").submit();      
   });  
	
   $("#indicator").css("display","none");
});
JSPROG
)) 
?>

<ul class="operate"></ul>

 <!-- フィルター用の条件を保持   -->
 <div style="display:none;">
   <?php echo $form->create(null); ?>
   <?php echo $form->text('GoodsMstView.wedding_planned_dt' ,array('value' => $wedding_dt)); ?>			  
   <?php echo $form->end(); ?>
 </div>
       
 <div class='notation'>
   <label>表示年月：</label>
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
		   if($found==false && $wedding_dt != -1){
		     	 echo "<option value='".$wedding_dt."' selected>{$wedding_dt}</option>";
		   }		         
	    ?>	
   </select>	
 </div>

<div style="overflow:auto; width:100%; height:100%; padding:0px 0px 15px 0px;" >
 <table class="list" cellspacing="0">

	     <tr>
		<th><a href="">成約日</a></th>
		<th><a href="">挙式予定日</a></th>
		<th><a href="">新郎名</a></th>
		<th><a href="">挙式申込金</a></th>
		<th><a href="">教会デポジット</a></th>
		<th><a href="">パーティ申込金</a></th>
		<th><a href="">Visionariデポジット</a></th>		
		<th><a href="">挙式代金</a></th>
		<th><a href="">その他１入金</a></th>
		<th><a href="">その他２入金</a></th>
		<th><a href="">その他３入金</a></th>
		<th><a href="">旅行請求書</a></th>
		<th><a href="">旅行入金</a></th>
		<th><a href="">ドレス請求書</a></th>
		<th><a href="">ドレス入金</a></th>
        <th><a href="">アルバム支払い</a></th>
		<th><a href="">美容KB請求書</a></th>
		<th><a href="">美容KB入金</a></th>
		<th><a href="">エステ請求書</a></th>
		<th><a href="">エステ入金</a></th>
        <th><a href="">歯請求書</a></th>
        <th><a href="">歯入金</a></th>
		<th><a href="">グッズ請求書</a></th>
		<th><a href="">グッズ入金</a></th>
        <th><a href="">ご紹介のお礼</a></th>
	    </tr>
<?php 
    for($i=0;$i < count($data);$i++){
	    echo "<tr>".
  		        "<td><a href='".$html->url('edit/'.$data[$i]['FundManagementTrnView']['id'])."'>".
  		             $common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['contract_dt'])."</td>".
	            "<td>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['wedding_dt'])."</td>". 
		        "<td><a href='".$html->url('/customers_list/goToCustomerInfo/'.$data[$i]['FundManagementTrnView']['customer_id'])."'>".
		             "{$data[$i]['FundManagementTrnView']['grmls_kj']}{$data[$i]['FundManagementTrnView']['grmfs_kj']}</a></td>".		       
		        "<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['wedding_deposit'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['church_deposit'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['party_deposit'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['visionari_deposit'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['wedding_fee'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['etc1_fee'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['etc2_fee'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['etc3_fee'])."</td>";
  		             
  		        if($data[$i]['FundManagementTrnView']['travel_invoice'] ==null)
  		        {
  		          echo "<td class='empty'>-</td>"; 		
  		        }
  		        else 
  		       {
  		      	  echo "<td class='empty'>".$html->image('ok.png')."</td>";;
  		        }
  		             
				
	     echo  "<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['travel_fee'])."</td>";
	     
                if($data[$i]['FundManagementTrnView']['dress_invoice'] ==null)
  		        {
  		          echo "<td class='empty'>-</td>"; 		
  		        }
  		        else 
  		       {
  		      	  echo "<td class='empty'>".$html->image('ok.png')."</td>";;
  		        }  		        
		
		 echo	"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['dress_fee'])."</td>".
				"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['album_fee'])."</td>";
	     
                if($data[$i]['FundManagementTrnView']['beauty_invoice'] ==null)
  		        {
  		          echo "<td class='empty'>-</td>"; 	 	
  		        }
  		        else 
  		       {
  		      	  echo "<td class='empty'>".$html->image('ok.png')."</td>";;
  		        }	     
				
		echo   "<td>".number_format($data[$i]['FundManagementTrnView']['beauty_fee'])."</td>";
		
		         if($data[$i]['FundManagementTrnView']['cosmetic_invoice'] ==null)
  		         {
  		          echo "<td class='empty'>-</td>"; 	 		
  		         }
  		         else 
  		        {
  		      	  echo "<td class='empty'>".$html->image('ok.png')."</td>";;
  		         }	    		
			
		echo   "<td>".number_format($data[$i]['FundManagementTrnView']['cosmetic_fee'])."</td>";
		
		         if($data[$i]['FundManagementTrnView']['dental_invoice'] ==null)
  		         {
  		          echo "<td class='empty'>-</td>"; 		
  		         }
  		         else 
  		        {
  		      	  echo "<td class='empty'>".$html->image('ok.png')."</td>";;
  		         }	    	
  		         
				
		echo	"<td>".number_format($data[$i]['FundManagementTrnView']['dental_fee'])."</td>";
		         
		         if($data[$i]['FundManagementTrnView']['goods_invoice'] ==null)
  		         {
  		          echo "<td class='empty'>-</td>"; 	
  		         }
  		         else 
  		        {
  		      	  echo "<td class='empty'>".$html->image('ok.png')."</td>";
  		         }	
			
		
		echo	"<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['goods_fee'])."</td>".             
  		        "<td class='yen'>".number_format($data[$i]['FundManagementTrnView']['kickback_fee'])."</td>".    
	          "</tr>".

              "<tr>".
  	            "<td>&nbsp;</td>".
	            "<td>&nbsp;</td>". 
		        "<td>&nbsp;</td>".		       
		        "<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['wedding_deposit_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['church_deposit_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['party_deposit_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['visionari_deposit_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['wedding_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['etc1_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['etc2_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['etc3_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['travel_invoice'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['travel_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['dress_invoice'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['dress_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['album_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['beauty_invoice'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['beauty_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['cosmetic_invoice'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['cosmetic_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['dental_invoice'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['dental_fee_dt'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['goods_invoice'])."</td>".
				"<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['goods_fee_dt'])."</td>".             
  		        "<td class='yen'>".$common->evalNbspForShortDate($data[$i]['FundManagementTrnView']['kickback_dt'])."</td>".              
	          "</tr>";
	
    }
?>
</table>
</div>

