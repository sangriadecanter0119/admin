<input type="hidden" name="data[Category][id]"  value="<?php echo GC_ENTERTAINMENT ?>" /> 
           
        <?php           
           if(count($entertainment) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($entertainment);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $entertainment[$head_index]['EntertainmentTrnView']['id']){
         		 	$head_id  = $entertainment[$head_index]['EntertainmentTrnView']['id'];
         		 	
    	       echo "<input type='hidden' name='data[EntertainmentTrn][{$head_counter}][id]' value='{$entertainment[$head_index]['EntertainmentTrnView']['id']}' />". 
                    "<table id='entertainment_{$head_counter}_table' class='list' >".    	       
    	             "<tr>".     	   
    	  		     	"<th colspan='13' width='403'>Company</th>".
    	   		   	    "<th colspan='8'  width='248'>Name</th>".
    	     	      	"<th colspan='5'  width='155'>Office No</th>".
    	     		    "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		    "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	    "</tr>".  
    	    	    "<tr>".         	     
    	     	   	    "<td colspan='13'>{$entertainment[$head_index]['EntertainmentTrnView']['vendor_nm']}</td>".
    	     		    "<td colspan='8'><input  class='inputableField'  type='text' name='data[EntertainmentTrn][{$head_counter}][attend_nm]' value='{$entertainment[$head_index]['EntertainmentTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     		    "<td colspan='5'><input  class='inputableField'  type='text' name='data[EntertainmentTrn][{$head_counter}][phone_no]'  value='{$entertainment[$head_index]['EntertainmentTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     		    "<td colspan='5'><input  class='inputableField'  type='text' name='data[EntertainmentTrn][{$head_counter}][cell_no]'   value='{$entertainment[$head_index]['EntertainmentTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     		    "<td colspan='9'><input  class='inputableField'  type='text' name='data[EntertainmentTrn][{$head_counter}][email]'     value='{$entertainment[$head_index]['EntertainmentTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    	    "</tr>".
    	            "<tr>".     	   
    	               "<th colspan='40' width='1240'>Other Request(RW)</th>". 
    	    	    "</tr>".  
    	            "<tr>".         	     
    	     	  	   "<td colspan='40'><input class='inputableField' type='text' name='data[EntertainmentTrn][{$head_counter}][note]' value='{$entertainment[$head_index]['EntertainmentTrnView']['note']}' style='width:99%' /></td>".    	     		
    	     	    "</tr>".
    	           "</table>";
    	       
    	           /* サブテーブル作成 */
              echo "<div id='entertainment_{$head_counter}_div' >";
                   $sub_id = -1;
                   $sub_counter = 0;
            
                   for($sub_index=0;$sub_index < count($entertainment);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $entertainment[$sub_index]['EntertainmentTrnView']['id']){
    	             	if($sub_id != $entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_id']){	    	     
    	       	           $sub_id  = $entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_id'];
    	   
    	                 	echo "<table id='entertainment_{$head_counter}_{$sub_counter}_table' class='list' >".
    	      	                 "<tr>".     	   
    	     			      	    "<th colspan='10' width='310'>Menu</th>".
    	                 	        "<th colspan='6'  width='186'>Type</th>".
    	     				        "<th colspan='5'  width='155'>Artist Number</th>".
    	                            "<th colspan='5'  width='155'>Working Start Time</th>".
    	                            "<th colspan='5'  width='155'>Working End Time</th>".
    	                            "<th colspan='5'  width='155'>Working Total</th>".    	           	       	    
    	   	 		             "</tr>".  
    	      	                 "<tr>".         	 
    	     				       "<td colspan='10'>{$entertainment[$sub_index]['EntertainmentTrnView']['menu']}".
    	     				          "<input          type='hidden' name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][id]'   value='{$entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_id']}' />".    	     				       
    	     				       "</td>".
    	                 	       "<td colspan='6'>{$entertainment[$sub_index]['EntertainmentTrnView']['type']}</td>". 
    	     				  	   "<td colspan='5'>{$entertainment[$sub_index]['EntertainmentTrnView']['artist_count']}</td>".   
                             	     				
    	      	                  "<td colspan='5'><input id='entertainment{$head_counter}{$sub_counter}_start_time' class='time_mask work_time inputableField' type='text'   name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][working_start_time]' value='{$entertainment[$sub_index]['EntertainmentTrnView']['working_start_time']}'    style='width:100%' /></td>".
    	      	                  "<td colspan='5'><input id='entertainment{$head_counter}{$sub_counter}_end_time'   class='time_mask work_time inputableField' type='text'   name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][working_end_time]'   value='{$entertainment[$sub_index]['EntertainmentTrnView']['working_end_time']}'      style='width:100%' /></td>".
    	     				      "<td colspan='5'><input id='entertainment{$head_counter}{$sub_counter}_total_time' class=''                    type='text'   name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][working_total_time]' value='{$entertainment[$sub_index]['EntertainmentTrnView']['fm_working_total']}'      style='width:99%' readonly /></td>".
    	     	 		       "</tr>".
    	      	               "<tr>".     	       	     
    	     			  	     "<th colspan='11'  width='341'>Start Place</th>".
    	     			  	     "<th colspan='11'  width='341'>Finish Place</th>".
    	     				     "<th colspan='18'  width='558'>Other Request</th>".      	     			       	    
    	    			       "</tr>".  
    	      			       "<tr>".         	     
    	     				     "<td colspan='11'><input class='inputableField' type='text' name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][start_place]'  value='{$entertainment[$sub_index]['EntertainmentTrnView']['start_place']}'             style='width:100%' /></td>".
    	     			  	     "<td colspan='11'><input class='inputableField' type='text' name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][end_place]'    value='{$entertainment[$sub_index]['EntertainmentTrnView']['end_place']}'               style='width:100%' /></td>".
    	     				     "<td colspan='18'><input class='inputableField' type='text' name='data[EntertainmentMenuTrn][{$head_counter}][{$sub_counter}][note]'         value='{$entertainment[$sub_index]['EntertainmentTrnView']['entertainment_menu_note']}' style='width:99%' /></td>".
    	     	 		      "</tr>".
    	      	              "</table>";
                              $sub_counter++;
    	                }
    	              }
                    }
                 echo "</div>";
                 echo "<br /><br />";
                 $head_counter++;
               }
             }
           } 	 
      ?>