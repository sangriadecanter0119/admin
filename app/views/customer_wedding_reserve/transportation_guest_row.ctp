<?php 
  echo "<tr id='transGuest_{$head_counter}_{$sub_counter}_{$dtl_counter}_row'>".      	     
           "<td colspan='4'><input type='hidden' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][id]' value='' />".
              "<a href='#' class='delete rowUnit' name='transGuest_".GC_TRANS_GST."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' >delete</a></td>".
           "</td>".        
    	   "<td colspan='2'>".
    	      "<select name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][no]' style='width:100%'>";	      
    	           for($j=1;$j < 21;$j++)
    	           {
    	               if($j == $no){
    	                    echo "<option value='$j' selected>$j</option>";	
    	               }else{
    	                    echo "<option value='$j' >$j</option>";	
    	               }
    	            }    	     
        echo "</select></td>".
           "<td colspan='2'>".
    	     "<select class='inputableField' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][total_departure_passenger]' style='width:100%'>";
    	          for($i=1;$i < 100;$i++) {  echo "<option value='$i' >$i</option>";	}    	     
        echo "</select></td>".   
           "<td colspan='6'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][representative_nm]'  value='' style='width:100%' /></td>". 
           "<td colspan='2'><input class='time_mask inputableField'  type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_time]'     value='' style='width:100%' /></td>".  	  
           "<td colspan='6'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_place]'    value='' style='width:100%' /></td>". 
           "<td colspan='2'>".
    	     "<select class=='inputableField' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][total_arrival_passenger]' style='width:100%'>";
    	          for($i=1;$i < 100;$i++) {  echo "<option value='$i' >$i</option>";	}    	     
        echo "</select></td>".   
           "<td colspan='2'><input class='time_mask inputableField'  type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_time]'    value='' style='width:100%' /></td>".
           "<td colspan='6'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_place]'   value='' style='width:100%' /></td>".    	  
           "<td colspan='8'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][note]'            value='' style='width:99%' /></td>".  	    
       "</tr>";
?>