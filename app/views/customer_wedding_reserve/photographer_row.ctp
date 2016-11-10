<?php 
echo  "<tr id='photographer_{$head_counter}_0_{$dtl_counter}_row'>".    	             
          "<td colspan='4'><input type='hidden' name='data[PhotographerTimeTrn][".$head_counter."][".$dtl_counter."][id]' value='' />".
                          "<a href='#' class='delete rowUnit' name='photographer_".GC_PHOTO."_".$head_counter."_0_".$dtl_counter."'  style='width:100%' >delete</a></td>".
          "<td colspan='2'>".
    	         		  "<select name='data[PhotographerTimeTrn][".$head_counter."][".$dtl_counter."][no]' style='width:100%'>";	      
    	      				for($k=1;$k < 21;$k++){ 
                              if($k == $no){
  	      	     	            echo "<option value='$k' selected>$k</option>";
                              }else{
                                echo "<option value='$k' >$k</option>";	
                              }
  	      	                }    	 	     					     	     
    	             echo "</select></td>".
    	  "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[PhotographerTimeTrn][".$head_counter."][".$dtl_counter."][shooting_time]'  value='' style='width:100%' /></td>".  	  
    	  "<td colspan='10'><input class='inputableField'          type='text' name='data[PhotographerTimeTrn][".$head_counter."][".$dtl_counter."][shooting_place]' value='' style='width:100%' /></td>".    	  
    	  "<td colspan='20'><input class='inputableField'          type='text' name='data[PhotographerTimeTrn][".$head_counter."][".$dtl_counter."][note]'           value='' style='width:99%' /></td>".  	    
      "</tr>";

?>