<input type="hidden" name="data[Category][id]"  value="<?php echo GC_TRAVEL ?>" /> 
        <?php 
         if(count($travel > 0))
         {
         	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($travel);$head_index++){
           	
         	    /* ヘッダテーブル作成 */
         	 	if($head_id != $travel[$head_index]['TravelTrnView']['id']){
         		   $head_id  = $travel[$head_index]['TravelTrnView']['id'];
      
                echo "<table id='travel_{$head_counter}_0_table'' class='list'>".
                     "<tr>".     	   
    	                "<th colspan='13' width='403'>Company</th>".
    	                "<th colspan='8'  width='248'>Name</th>".
    	                "<th colspan='5'  width='155'>Office No</th>".
    	                "<th colspan='5'  width='155'>Cell Phone No</th>".
    	                "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	             "</tr>".  
    	             "<tr>".   
    	                "<td colspan='13'><input type='hidden' name='data[TravelTrn][{$head_counter}][id]'                value='{$travel[$head_index]['TravelTrnView']['id']}' />". 
                                         "<input class='inputableField' type='input'  name='data[TravelTrn][{$head_counter}][vendor_nm]'         value='{$travel[$head_index]['TravelTrnView']['vendor_nm']}'        style='width:100%'/></td>". 
    	                                /* "{$travel[$head_index]['TravelTrnView']['vendor_nm']}</td>". */
    	                "<td colspan='8'><input  class='inputableField'  type='text' name='data[TravelTrn][{$head_counter}][attend_nm]' value='{$travel[$head_index]['TravelTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[TravelTrn][{$head_counter}][phone_no]'  value='{$travel[$head_index]['TravelTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[TravelTrn][{$head_counter}][cell_no]'   value='{$travel[$head_index]['TravelTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	                "<td colspan='9'><input  class='inputableField'  type='text' name='data[TravelTrn][{$head_counter}][email]'     value='{$travel[$head_index]['TravelTrnView']['vendor_email']}'     style='width:99%' /></td>".    	   
    	             "</tr>".    	
    	             "<tr>".     	
    	                "<th colspan='15' width='465'>Arrival Day Time & Flight</th>".    	   
    	                "<th colspan='15' width='465'>Departure Day Time & Flight</th>".  
    	                "<th colspan='7'  width='217'>Hotel(Wedding Day)</th>".   
    	                "<th colspan='3'  width='93'>Room #</th>".   
    	             "</tr>". 
    	             "<tr>".   
    	               "<td colspan='5'><input class='date_mask inputableField'  type='text' name='data[TravelTrn][{$head_counter}][arrival_dt]'          value='{$common->evalForShortDate($travel[$head_index]['TravelTrnView']['arrival_dt'])}'   style='width:100%' /></td>".
    	               "<td colspan='5'><input class='time_mask inputableField'  type='text' name='data[TravelTrn][{$head_counter}][arrival_time]'        value='{$travel[$head_index]['TravelTrnView']['arrival_time']}'                            style='width:100%' /></td>".
    	               "<td colspan='5'><input class='inputableField'            type='text' name='data[TravelTrn][{$head_counter}][arrival_flight_no]'   value='{$travel[$head_index]['TravelTrnView']['arrival_flight_no']}'                       style='width:100%' /></td>".
    	               "<td colspan='5'><input class='date_mask inputableField'  type='text' name='data[TravelTrn][{$head_counter}][departure_dt]'        value='{$common->evalForShortDate($travel[$head_index]['TravelTrnView']['departure_dt'])}' style='width:100%' /></td>".
    	               "<td colspan='5'><input class='time_mask inputableField'  type='text' name='data[TravelTrn][{$head_counter}][departure_time]'      value='{$travel[$head_index]['TravelTrnView']['departure_time']}'                          style='width:100%' /></td>".
    	               "<td colspan='5'><input class='inputableField'            type='text' name='data[TravelTrn][{$head_counter}][departure_flight_no]' value='{$travel[$head_index]['TravelTrnView']['departure_flight_no']}'                     style='width:100%' /></td>".
    	               "<td colspan='7'><input class='inputableField'            type='text' name='data[TravelTrn][{$head_counter}][wedding_day_hotel]'   value='{$travel[$head_index]['TravelTrnView']['wedding_day_hotel']}'                       style='width:100%' /></td>".
    	               "<td colspan='3'><input class='inputableField'            type='text' name='data[TravelTrn][{$head_counter}][wedding_day_room_no]' value='{$travel[$head_index]['TravelTrnView']['wedding_day_room_no']}'                     style='width:99%' /></td>".   
    	             "</tr>".
                     "<tr>".
                       "<th colspan='40' width='1240'>Other Request(RW)</th>".
                     "</tr>".
                     "<tr>".
                       "<td colspan='40'><input class='inputableField' type='text' name='data[TravelTrn][{$head_counter}][note]'  value='{$travel[$head_index]['TravelTrnView']['note']}'  style='width:99%' /></td>".
                     "</tr>";
                  
                     /* 時間テーブル作成 */
                     $time_counter = 0;
                
                echo "<tr>".	     	       
                       "<th colspan='4'  width='84'><a href='#' class='add rowUnit' name='travel_".GC_TRAVEL."_{$head_counter}_0_{$time_counter}' style='width:100%'>Add Time</a></th>".  
    	     		   "<th colspan='2'  width='62'>No</th>".                       
    	     		   "<th colspan='10' width='310'>Hotel</th>".    	   
    	     		   "<th colspan='6'  width='186'>Check In</th>".    
    	     		   "<th colspan='6'  width='186'>Check Out</th>".     	     		
    	     		   "<th colspan='12' width='372'>Special Info</th>".           	       	    
    	           "</tr>";                
                               
                   for($time_index=0;$time_index < count($travel);$time_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $travel[$time_index]['TravelTrnView']['id']){
    	             	            
    	       	          echo  "<tr id='travel_{$head_counter}_0_{$time_counter}_row'>".    	             
    	                           "<td colspan='4'><input type='hidden' name='data[TravelDtlTrn][".$head_counter."][".$time_counter."][id]' value='{$travel[$time_index]['TravelTrnView']['travel_dtl_id']}' />";
                                   //１行目は削除負荷とする
    	         					if($time_counter == 0){
                    					echo  "<a href='#' class='delete rowUnit' name='travel_".GC_TRAVEL."_".$head_counter."_0_".$time_counter."'  style='width:100%' disabled>&nbsp;</a></td>";	
                 					}else{
                 						echo  "<a href='#' class='delete rowUnit' name='travel_".GC_TRAVEL."_".$head_counter."_0_".$time_counter."'  style='width:100%' >delete</a></td>";
                 					}    	        
    	         			 echo "<td colspan='2'>".
    	         			        "<select name='data[TravelDtlTrn][".$head_counter."][".$time_counter."][no]' style='width:100%'>";	      
    	      					       for($k=1;$k < 21;$k++)
    	      					       {
    	     						     if($travel[$time_index]['TravelTrnView']['no'] == $k){
    	     	  						   echo "<option value='$k' selected>$k</option>";	
    	     						     }else{
    	          						   echo "<option value='$k' >$k</option>";	
    	  						         }
    	     					       }    	     
    	                        echo "</select></td>".
    	                          "<td colspan='10'><input class='inputableField'           type='text' name='data[TravelDtlTrn][".$head_counter."][".$time_counter."][hotel_nm]'    value='{$travel[$time_index]['TravelTrnView']['hotel_nm']}'                               style='width:100%' /></td>".  	  
    	         				  "<td colspan='6'><input  class='date_mask inputableField' type='text' name='data[TravelDtlTrn][".$head_counter."][".$time_counter."][checkin_dt]'  value='{$common->evalForShortDate($travel[$time_index]['TravelTrnView']['checkin_dt'])}'  style='width:100%' /></td>".    	  
    	         				  "<td colspan='6'><input  class='date_mask inputableField' type='text' name='data[TravelDtlTrn][".$head_counter."][".$time_counter."][checkout_dt]' value='{$common->evalForShortDate($travel[$time_index]['TravelTrnView']['checkout_dt'])}' style='width:100%' /></td>".  	  
    	         				  "<td colspan='12'><input class='inputableField'           type='text' name='data[TravelDtlTrn][".$head_counter."][".$time_counter."][note]'        value='{$travel[$time_index]['TravelTrnView']['travel_dtl_note']}'                        style='width:100%' /></td>". 
    	         				"</tr>";
    	                    $time_counter++;                                   	
                     }
                  }                  
       echo "</table>";
       echo "<br /><br />";
       $head_counter++;
          }
        }   
    }
?>    	