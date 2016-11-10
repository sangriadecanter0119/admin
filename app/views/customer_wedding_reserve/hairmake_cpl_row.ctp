<?php 
 echo  "<tr id='hairmakeCpl_{$head_counter}_0_{$dtl_counter}_row'>".    	             
    	    "<td colspan='4'><input type='hidden' name='data[HairmakeCplTimeTrn][".$head_counter."][".$dtl_counter."][id]' value='' />".                                  
             			    "<a href='#' class='delete rowUnit' name='hairmakeCpl_".GC_HAIR_MAKE_CPL."_".$head_counter."_0_".$dtl_counter."'  style='width:100%' >delete</a></td>".                 					    	        
    	    "<td colspan='2'>".
    	    		        "<select name='data[HairmakeCplTimeTrn][".$head_counter."][".$dtl_counter."][no]' style='width:100%'>";	      
    	    			       for($k=1;$k < 21;$k++){ 
                                 if($k == $no){
  	      	     	               echo "<option value='$k' selected>$k</option>";
                                 }else{
                                   echo "<option value='$k' >$k</option>";	
                                 }
  	      	                   }    	 	     
    	               echo "</select></td>".
            "<td colspan='10'><input class='time_mask inputableField' type='text' name='data[HairmakeCplTimeTrn][".$head_counter."][".$dtl_counter."][make_start_time]'  value='' style='width:100%' /></td>".  	  
            "<td colspan='10'><input class='inputableField'           type='text' name='data[HairmakeCplTimeTrn][".$head_counter."][".$dtl_counter."][make_start_place]' value='' style='width:100%' /></td>". 
    	    "<td colspan='14'><input class='inputableField'           type='text' name='data[HairmakeCplTimeTrn][".$head_counter."][".$dtl_counter."][note]'             value='' style='width:99%' /></td>".  	    
      "</tr>";
?>