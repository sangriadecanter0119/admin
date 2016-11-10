<input type="hidden" name="data[Category][id]"  value="<?php echo GC_COORDINATOR ?>" /> 
        <?php 
         if(count($coordinator > 0))
         {
         	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($coordinator);$head_index++){
           	
         	    /* ヘッダテーブル作成 */
         	 	if($head_id != $coordinator[$head_index]['CoordinatorMenuTrnView']['id']){
         		   $head_id  = $coordinator[$head_index]['CoordinatorMenuTrnView']['id'];
         		   
         		   /* メインテーブル作成  */
         		   if($coordinator[$head_index]['CoordinatorMenuTrnView']['main_attend_kbn'] == CC_MAIN){         		   
                 echo "<table id='coordinator_{$head_counter}_main_table' class='list' >".
    		           "<tr>".     	   
    	                   "<th colspan='6'  width='186'>Total Coordinator #</th>".
    	                   "<th colspan='4'  width='124'>Briefing Date</th>".
    	                   "<th colspan='8'  width='248'>Briefing Start Time</th>".
    	                   "<th colspan='8'  width='248'>Briefing Finish Time</th>".
    	                   "<th colspan='8'  width='248'>Briefing Place</th>".          	  
                           "<th colspan='6'  width='186'>Briefing Name</th>".    
    	               "</tr>".  
    	               "<tr>";
                      echo "<td colspan='6'>".
                                "<select class='inputableField' name='data[CoordinatorTrn][{$head_counter}][total_attend]' style='width:100%'>";	      
    	                           for($j=1;$j < 21;$j++){
    	                	          if($coordinator[$head_index]['CoordinatorMenuTrnView']['total_attend'] == $j){
    	     	                         echo "<option value='$j' selected>$j</option>";	
    	     	                      }else{
    	                                 echo "<option value='$j' >$j</option>";	
    	     	                      }
    	                           }    	     
                          echo "</select></td>".    
    	                  "<td colspan='4'><input  class='date_mask inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][briefing_dt]'         value='{$common->evalForShortDate($coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_dt'])}'         style='width:100%' /></td>".
    	                  "<td colspan='8'><input  class='time_mask inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][briefing_start_time]' value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_start_time']}' style='width:100%' /></td>".
    	                  "<td colspan='8'><input  class='time_mask inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][briefing_end_time]'   value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_end_time']}'   style='width:100%' /></td>".
    	                  "<td colspan='8'><input  class='inputableField'            type='text' name='data[CoordinatorTrn][{$head_counter}][briefing_place]'      value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_place']}'      style='width:99%' /></td>".	   	    
    	                  "<td colspan='6'><input  class='inputableField'            type='text' name='data[CoordinatorTrn][{$head_counter}][briefing_name]'       value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['briefing_name']}'       style='width:99%' /></td>".	   	    
    	               "</tr>".      
                       "</table><br />";
         		    }
      
                echo "<table id='coordinator_{$head_counter}_0_table'' class='list'>".
                     "<tr>".     	   
    	                "<th colspan='13' width='403'>Company</th>".
    	                "<th colspan='8'  width='248'>Name</th>".
    	                "<th colspan='5'  width='155'>Office No</th>".
    	                "<th colspan='5'  width='155'>Cell Phone No</th>".
    	                "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	             "</tr>".  
    	             "<tr>".   
    	                "<td colspan='13'><input type='hidden' name='data[CoordinatorTrn][{$head_counter}][id]'              value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['id']}' />".           
                                         "<input type='hidden' name='data[CoordinatorTrn][{$head_counter}][main_attend_kbn]' value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['main_attend_kbn']}' />". 
    	                                 "{$coordinator[$head_index]['CoordinatorMenuTrnView']['vendor_nm']}</td>".
    	                "<td colspan='8'><input  class='inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][attend_nm]' value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][phone_no]'  value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][cell_no]'   value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	                "<td colspan='9'><input  class='inputableField'  type='text' name='data[CoordinatorTrn][{$head_counter}][email]'     value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['vendor_email']}'     style='width:99%' /></td>".    	   
    	             "</tr>".    	
    	             "<tr>".     	
    	                "<th colspan='6'  width='186'>Working Start Time</th>".    	   
    	                "<th colspan='6'  width='186'>Working End Time</th>".  
    	                "<th colspan='4'  width='124'>Working Total</th>".   
    	                "<th colspan='24' width='744'>Other Request(RW)</th>".   
    	             "</tr>". 
    	             "<tr>".   
    	                "<td colspan='6'><input  class='time_mask work_time inputableField' type='text' id='Coordinator{$head_index}_start_time' name='data[CoordinatorTrn][{$head_counter}][working_start_time]'  value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['working_start_time']}'  style='width:100%' /></td>".    	
    	                "<td colspan='6'><input  class='time_mask work_time inputableField' type='text' id='Coordinator{$head_index}_end_time'   name='data[CoordinatorTrn][{$head_counter}][working_end_time]'    value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['working_end_time']}'    style='width:99%' /></td>".    	           
    	                "<td colspan='4'><input  class=''                    type='text' id='Coordinator{$head_index}_total_time' name='data[CoordinatorTrn][{$head_counter}][working_total]'       value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['fm_working_total']}'    style='width:100%' readonly /></td>".
    	                "<td colspan='24'><input class='inputableField'                    type='text'                                          name='data[CoordinatorTrn][{$head_counter}][note]'                value='{$coordinator[$head_index]['CoordinatorMenuTrnView']['coordinator_note']}'    style='width:99%' /></td>".
    	             "</tr>".
                     "<tr>".     	   
    	                 "<th colspan='20' width='620'>Menu</th>".
    	                 "<th colspan='20' width='620'>Other Request</th>".
    	             "</tr>";  
                
                  /* メニューテーブル作成 */
                   $menu_id = -1;
                   $menu_counter = 0;
                  for($menu_index=0;$menu_index < count($coordinator);$menu_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $coordinator[$menu_index]['CoordinatorMenuTrnView']['id']){
    	             	if($menu_id != $coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_id']){	    	     
    	       	           $menu_id  = $coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_id'];
    	       	           
    	       	           echo "<tr>".   
    	                          "<td colspan='20'><input  class='inputableField'  type='hidden' name='data[CoordinatorMenuTrn][{$head_counter}][{$menu_counter}][id]' value='{$coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_id']}' style='width:100%' />".
    	                                            "{$coordinator[$menu_index]['CoordinatorMenuTrnView']['menu']}</td>".
    	                          "<td colspan='20'><input  class='inputableField'  type='text' name='data[CoordinatorMenuTrn][{$head_counter}][{$menu_counter}][note]' value='{$coordinator[$menu_index]['CoordinatorMenuTrnView']['coordinator_menu_note']}' style='width:100%' /></td>".
    	                        "</tr>";    	 
    	       	            $menu_counter++;      	           
    	             	}
    	             }
                  }                
                   /* 時間テーブル作成 */
                   $time_id = -1;
                   $time_counter = 0;
             
                echo "<tr>".	     	       
                       "<th colspan='4' width='84'><a href='#' class='add rowUnit' name='coordinator_".GC_COORDINATOR."_{$head_counter}_0_{$time_counter}' style='width:100%'>Add Time</a></th>".  
    	     		   "<th colspan='2' width='62'>No</th>".                       
    	     		   "<th colspan='4' width='124'>Start Time</th>".    	   
    	     		   "<th colspan='7' width='217'>Start Place</th>".    
    	     		   "<th colspan='4' width='124'>Finish Time</th>". 
    	     		   "<th colspan='7' width='217'>Finish Place</th>". 
    	     		   "<th colspan='6' width='186'>Transportation</th>". 
    	     		   "<th colspan='6' width='186'>Other Request</th>".           	       	    
    	           "</tr>";
              
                   for($time_index=0;$time_index < count($coordinator_time);$time_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $coordinator_time[$time_index]['CoordinatorTimeTrnView']['id']){
    	             	if($time_id != $coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_id']){	    	     
    	       	           $time_id  = $coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_id'];
            
    	       	          echo  "<tr id='coordinator_{$head_counter}_0_{$time_counter}_row'>".    	             
    	                           "<td colspan='4'><input type='hidden' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][id]' value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_id']}' />";
                                   //１行目は削除負荷とする
    	         					if($time_counter == 0){
                    					echo  "<a href='#' class='delete rowUnit' name='coordinator_".GC_COORDINATOR."_".$head_counter."_0_".$time_counter."'  style='width:100%' disabled>&nbsp;</a></td>";	
                 					}else{
                 						echo  "<a href='#' class='delete rowUnit' name='coordinator_".GC_COORDINATOR."_".$head_counter."_0_".$time_counter."'  style='width:100%' >delete</a></td>";
                 					}    	        
    	         			 echo "<td colspan='2'>".
    	         			        "<select name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][no]' style='width:100%'>";	      
    	      					       for($k=1;$k < 21;$k++)
    	      					       {
    	     						     if($coordinator_time[$time_index]['CoordinatorTimeTrnView']['no'] == $k){
    	     	  						   echo "<option value='$k' selected>$k</option>";	
    	     						     }else{
    	          						   echo "<option value='$k' >$k</option>";	
    	  						         }
    	     					       }    	     
    	                        echo "</select></td>".
    	                          "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][start_time]'     value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['start_time']}'           style='width:100%' /></td>".  	  
    	         				  "<td colspan='7'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][start_place]'    value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['start_place']}'          style='width:100%' /></td>".    	  
    	         				  "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][end_time]'       value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['end_time']}'             style='width:100%' /></td>".  	  
    	         				  "<td colspan='7'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][end_place]'      value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['end_place']}'            style='width:100%' /></td>". 
    	         				  "<td colspan='6'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][transportation]' value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['transportation']}'       style='width:100%' /></td>". 
    	         				  "<td colspan='6'><input class='inputableField'           type='text' name='data[CoordinatorTimeTrn][".$head_counter."][".$time_counter."][note]'           value='{$coordinator_time[$time_index]['CoordinatorTimeTrnView']['coordinator_time_note']}' style='width:99%' /></td>".  	    
    	       			        "</tr>";
    	                    $time_counter++;
                       }            	
                     }
                  }                  
       echo "</table>";
       echo "<br /><br />";
       $head_counter++;
          }
        }   
    }
?>    	