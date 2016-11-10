<?php 
 echo "<tr id='hairmakeGuest_{$head_counter}_{$sub_counter}_{$dtl_counter}_row'>".      	     
          "<td colspan='4'><input type='hidden' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][id]' value='' />".
                           "<a href='#' class='delete rowUnit' name='hairmakeGuest_".GC_HAIR_MAKE_GST."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' >delete</a></td>".
          "</td>".        
          "<td colspan='3'>".
                          "<select name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][no]' style='width:100%'>";	      
                              for($k=1;$k < 21;$k++){ 
                                if($k == $no){
  	      	     	             echo "<option value='$k' selected>$k</option>";
                                }else{
                                 echo "<option value='$k' >$k</option>";	
                                }
  	      	                  }    	 	     
                      echo "</select></td>".
          "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][make_start_time]'  value='' style='width:100%' /></td>".  
          "<td colspan='7'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][make_start_place]' value='' style='width:100%' /></td>".  	  
          "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][make_end_time]'    value='' style='width:100%' /></td>".   
          "<td colspan='7'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][guest_nm]'         value='' style='width:100%' /></td>".
          "<td colspan='7'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][attend_nm]'        value='' style='width:100%' /></td>".    	  
          "<td colspan='4'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][note]'             value='' style='width:99%' /></td>".  	    
     "</tr>";
?>