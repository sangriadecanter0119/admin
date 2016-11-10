<?php 
echo  "<tr id='coordinator_{$head_counter}_0_{$dtl_counter}_row'>".    	             
        "<td colspan='4'><input type='hidden' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][id]' value='' />".
                        "<a href='#' class='delete rowUnit' name='coordinator_".GC_COORDINATOR."_".$head_counter."_0_".$dtl_counter."'  style='width:100%' >delete</a></td>".
        "<td colspan='2'>".
  	        "<select name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][no]' style='width:100%'>";	      
  	      	     for($k=1;$k < 21;$k++){ 
                    if($k == $no){
  	      	     	   echo "<option value='$k' selected>$k</option>";
                    }else{
                       echo "<option value='$k' >$k</option>";	
                    }
  	      	     }    	     
       echo "</select></td>".
    	 "<td colspan='4'><input class='time_mask inputableField'  type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][start_time]'     value='' style='width:100%' /></td>".  	  
    	 "<td colspan='7'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][start_place]'    value='' style='width:100%' /></td>".    	  
    	 "<td colspan='4'><input class='time_mask inputableField'  type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][end_time]'       value='' style='width:100%' /></td>".  	  
    	 "<td colspan='7'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][end_place]'      value='' style='width:100%' /></td>". 
    	 "<td colspan='6'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][transportation]' value='' style='width:100%' /></td>". 
    	 "<td colspan='6'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$dtl_counter."][note]'           value='' style='width:99%' /></td>".  	    
   	  "</tr>";
?>