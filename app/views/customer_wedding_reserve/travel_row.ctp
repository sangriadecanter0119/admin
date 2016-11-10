<?php 
echo  "<tr id='travel_{$head_counter}_0_{$dtl_counter}_row'>".    	             
         "<td colspan='4'><input type='hidden' name='data[TravelDtlTrn][".$head_counter."][".$dtl_counter."][id]' value='' />".
                          "<a href='#' class='delete rowUnit' name='travel_".GC_TRAVEL."_".$head_counter."_0_".$dtl_counter."'  style='width:100%' >delete</a></td>".
         "<td colspan='2'>".
    	 		         "<select name='data[TravelDtlTrn][".$head_counter."][".$dtl_counter."][no]' style='width:100%'>";	      
    	        	      for($k=1;$k < 21;$k++){ 
                            if($k == $no){
  	      	     	           echo "<option value='$k' selected>$k</option>";
                            }else{
                               echo "<option value='$k' >$k</option>";	
                            }
  	      	              }    	  	     
    	            echo "</select></td>".
         "<td colspan='10'><input class='inputableField'           type='text' name='data[TravelDtlTrn][".$head_counter."][".$dtl_counter."][hotel_nm]'    value=''  style='width:100%' /></td>".  	  
     	 "<td colspan='6'><input  class='date_mask inputableField' type='text' name='data[TravelDtlTrn][".$head_counter."][".$dtl_counter."][checkin_dt]'  value=''  style='width:100%' /></td>".    	  
         "<td colspan='6'><input  class='date_mask inputableField' type='text' name='data[TravelDtlTrn][".$head_counter."][".$dtl_counter."][checkout_dt]' value=''  style='width:100%' /></td>".  	  
    	 "<td colspan='12'><input class='inputableField'           type='text' name='data[TravelDtlTrn][".$head_counter."][".$dtl_counter."][note]'        value=''  style='width:100%' /></td>". 
 	  "</tr>";
?>