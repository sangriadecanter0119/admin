 <input type="hidden" name="data[Category][id]"      value="<?php echo GC_HAIR_MAKE ?>" />          
                    
         <?php 
           //CPL
           if(count($hairmake_cpl) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($hairmake_cpl);$head_index++){
           	
         	    /* ヘッダテーブル作成 */
         	 	if($head_id != $hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['id']){
         		   $head_id  = $hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['id'];
         		   
         		   /* メインテーブル作成  */
         		   if($hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['main_attend_kbn'] == HC_MAIN){         		   
                 echo "<table id='hairmakeCpl_{$head_counter}_main_table' class='list' >".
    		           "<tr>".     	   
    	                   "<th colspan='6'  width='186'>Total Hairmake #</th>".
    	                   "<th colspan='4'  width='124'>Rehasal Date</th>".
    	                   "<th colspan='8'  width='248'>Rehasal Start Time</th>".
    	                   "<th colspan='8'  width='248'>Rehasal Finish Time</th>".
    	                   "<th colspan='8'  width='248'>Rehasal Place</th>".     
                           "<th colspan='6'  width='186'>Rehasal Name</th>".       	     
    	               "</tr>".  
    	               "<tr>";
                      echo "<td colspan='6'>".
                                "<select class='inputableField'' name='data[HairmakeCplTrn][{$head_counter}][total_attend]' style='width:100%'>";	      
    	                           for($j=1;$j < 21;$j++){
    	                	          if($hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['total_attend'] == $j){
    	     	                         echo "<option value='$j' selected>$j</option>";	
    	     	                      }else{
    	                                 echo "<option value='$j' >$j</option>";	
    	     	                      }
    	                           }    	     
                          echo "</select></td>".    
    	                  "<td colspan='4'><input  class='date_mask inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][rehasal_dt]'         value='{$common->evalForShortDate($hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_dt'])}'  style='width:100%' /></td>".
    	                  "<td colspan='8'><input  class='time_mask inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][rehasal_start_time]' value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_start_time']}' style='width:100%' /></td>".
    	                  "<td colspan='8'><input  class='time_mask inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][rehasal_end_time]'   value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_end_time']}'   style='width:100%' /></td>".
    	                  "<td colspan='8'><input class='inputableField'             type='text' name='data[HairmakeCplTrn][{$head_counter}][rehasal_place]'      value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_place']}'      style='width:99%' /></td>".	   	    
    	                  "<td colspan='6'><input class='inputableField'             type='text' name='data[HairmakeCplTrn][{$head_counter}][rehasal_name]'       value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['rehasal_name']}'       style='width:99%' /></td>".	   	    
    	                 "</tr>".      
                       "</table><br />";
         		    }
      
                echo "<table id='hairmakeCpl_{$head_counter}_0_table'' class='list'>".
                     "<tr>".     	   
    	                "<th colspan='13' width='403'>Company</th>".
    	                "<th colspan='8'  width='248'>Name</th>".
    	                "<th colspan='5'  width='155'>Office No</th>".
    	                "<th colspan='5'  width='155'>Cell Phone No</th>".
    	                "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	             "</tr>".  
    	             "<tr>".   
    	                "<td colspan='13'><input type='hidden' name='data[HairmakeCplTrn][{$head_counter}][id]'              value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['id']}' />".           
                                         "<input type='hidden' name='data[HairmakeCplTrn][{$head_counter}][main_attend_kbn]' value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['main_attend_kbn']}' />". 
    	                                 "{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['vendor_nm']}</td>".
    	                "<td colspan='8'><input  class='inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][attend_nm]' value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][phone_no]'  value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][cell_no]'   value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	                "<td colspan='9'><input  class='inputableField'  type='text' name='data[HairmakeCplTrn][{$head_counter}][email]'     value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['vendor_email']}'     style='width:99%' /></td>".    	   
    	             "</tr>".    	
    	             "<tr>".     	
    	                "<th colspan='3' width='93'>Start Time</th>".    	 
                        "<th colspan='8' width='248'>Working Start Place</th>".     
    	                "<th colspan='3' width='93'>End Time</th>".  
                        "<th colspan='8' width='248'>Working End Place</th>".
    	                "<th colspan='4' width='124'>Working Total</th>".   
                        "<th colspan='14' width='434'>Transportation</th>".    	               
    	             "</tr>". 
    	             "<tr>".   
    	                "<td colspan='3'><input class='time_mask work_time inputableField' type='text' id='hairmakeCpl{$head_index}_start_time' name='data[HairmakeCplTrn][{$head_counter}][working_start_time]'  value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_start_time']}'  style='width:100%' /></td>".    	
    	                "<td colspan='8'><input class='inputableField'                     type='text'                                          name='data[HairmakeCplTrn][{$head_counter}][working_start_place]' value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_start_place']}' style='width:99%' /></td>".
    	                "<td colspan='3'><input class='time_mask work_time inputableField' type='text' id='hairmakeCpl{$head_index}_end_time'   name='data[HairmakeCplTrn][{$head_counter}][working_end_time]'    value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_end_time']}'    style='width:99%' /></td>".    	           
    	               "<td colspan='8'><input class='inputableField'                      type='text'                                          name='data[HairmakeCplTrn][{$head_counter}][working_end_place]'   value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['working_end_place']}'   style='width:99%' /></td>".
    	                "<td colspan='4'><input class=''                    type='text' id='hairmakeCpl{$head_index}_total_time' name='data[HairmakeCplTrn][{$head_counter}][working_total]'       value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['fm_working_total']}'    style='width:100%' readonly /></td>".
                        "<td colspan='14'><input class='inputableField'                    type='text'                                          name='data[HairmakeCplTrn][{$head_counter}][transportation]'      value='{$hairmake_cpl[$head_index]['HairmakeCplMenuTrnView']['transportation']}'     style='width:99%' /></td>".    	               
    	             "</tr>".
                     "<tr>".     	   
    	                 "<th colspan='20' width='620'>Menu</th>".
    	                 "<th colspan='20' width='620'>Other Request(RW)</th>".
    	             "</tr>";  
                
                      /* メニューテーブル作成 */
                   $menu_id = -1;
                   $menu_counter = 0;
                   for($menu_index=0;$menu_index < count($hairmake_cpl);$menu_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['id']){
    	             	if($menu_id != $hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['hairmake_cpl_menu_id']){	    	     
    	       	           $menu_id  = $hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['hairmake_cpl_menu_id'];
    	       	           
    	       	           echo "<tr>".   
    	                          "<td colspan='20'><input  class=''  type='hidden' name='data[HairmakeCplMenuTrn][{$head_counter}][{$menu_counter}][id]' value='{$hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['hairmake_cpl_menu_id']}' style='width:100%' />".
    	                                            "{$hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['menu']}</td>".
    	                          "<td colspan='20'><input  class='inputableField'  type='text' name='data[HairmakeCplMenuTrn][{$head_counter}][{$menu_counter}][note]' value='{$hairmake_cpl[$menu_index]['HairmakeCplMenuTrnView']['hairmake_cpl_menu_note']}' style='width:100%' /></td>".
    	                        "</tr>";    	 
    	       	            $menu_counter++;      	           
    	             	}
    	              }
                   } 
                   
                    /* 時間テーブル作成 */
                  $time_id = -1;
                  $time_counter = 0;
             
                echo "<tr>".	     	       
                       "<th colspan='4'  width='124'><a href='#' class='add rowUnit' name='hairmakeCpl_".GC_HAIR_MAKE_CPL."_{$head_counter}_0_{$time_counter}' style='width:100%'>Add Hair Change</a></th>".  
    	     		   "<th colspan='2'  width='64'>No</th>".                       
    	     		   "<th colspan='10' width='310'>Time Hair Change</th>".    	   
    	     		   "<th colspan='10' width='310'>Place Change</th>".   
    	     		   "<th colspan='14' width='434'>Other Request</th>".           	       	    
    	             "</tr>";
                
         	 	for($time_index=0;$time_index < count($hairmake_cpl_time);$time_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['id']){
    	             	if($time_id != $hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_id']){	    	     
    	       	           $time_id  = $hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_id'];
            
    	       	          echo  "<tr id='hairmakeCpl_{$head_counter}_0_{$time_counter}_row'>".    	             
    	                           "<td colspan='4'><input type='hidden' name='data[HairmakeCplTimeTrn][".$head_counter."][".$time_counter."][id]' value='{$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_id']}' />";
                                   //１行目は削除負荷とする
    	         					if($time_counter == 0){
                    					echo  "<a href='#' class='delete rowUnit' name='hairmakeCpl_".GC_HAIR_MAKE_CPL."_".$head_counter."_0_".$time_counter."'  style='width:100%' disabled>&nbsp;</a></td>";	
                 					}else{
                 						echo  "<a href='#' class='delete rowUnit' name='hairmakeCpl_".GC_HAIR_MAKE_CPL."_".$head_counter."_0_".$time_counter."'  style='width:100%' >delete</a></td>";
                 					}    	        
    	         			 echo "<td colspan='2'>".
    	         			        "<select name='data[HairmakeCplTimeTrn][".$head_counter."][".$time_counter."][no]' style='width:100%'>";	      
    	      					       for($k=1;$k < 21;$k++)
    	      					       {
    	     						     if($hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_no'] == $k){
    	     	  						   echo "<option value='$k' selected>$k</option>";	
    	     						     }else{
    	          						   echo "<option value='$k' >$k</option>";	
    	  						         }
    	     					       }    	     
    	                        echo "</select></td>".
    	                          "<td colspan='10'><input class='time_mask inputableField' type='text' name='data[HairmakeCplTimeTrn][".$head_counter."][".$time_counter."][make_start_time]'  value='{$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['make_start_time']}'        style='width:100%' /></td>".  	  
    	         				  "<td colspan='10'><input class='inputableField'           type='text' name='data[HairmakeCplTimeTrn][".$head_counter."][".$time_counter."][make_start_place]' value='{$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['make_start_place']}'       style='width:100%' /></td>". 
    	         				  "<td colspan='14'><input class='inputableField'           type='text' name='data[HairmakeCplTimeTrn][".$head_counter."][".$time_counter."][note]'             value='{$hairmake_cpl_time[$time_index]['HairmakeCplTimeTrnView']['hairmake_cpl_time_note']}' style='width:99%' /></td>".  	    
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
       
       <?php 
           //GUEST
           if(count($hairmake_gst) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 for($head_index=0;$head_index < count($hairmake_gst);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $hairmake_gst[$head_index]['HairmakeGuestTrnView']['id']){
         		 	$head_id  = $hairmake_gst[$head_index]['HairmakeGuestTrnView']['id'];
         		 	
                echo  "<input type='hidden' name='data[HairmakeGuestTrn][{$head_counter}][id]' value='{$hairmake_gst[$head_index]['HairmakeGuestTrnView']['id']}' />". 
                      "<table id='hairmakeGuest_{$head_counter}_table' class='list' >".
    	                "<tr>".     	   
    	                   "<th colspan='13' width='403'>Company</th>".
    	                   "<th colspan='8'  width='248'>Name</th>".
    	     			   "<th colspan='5'  width='155'>Office No</th>".
    	     			   "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			   "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    			"</tr>".  
    	    			"<tr>".         	     
    	     			   "<td colspan='13'>{$hairmake_gst[$head_index]['HairmakeGuestTrnView']['vendor_nm']}</td>".
    	     			   "<td colspan='8'><input  class='inputableField'  type='text' name='data[HairmakeGuestTrn][{$head_counter}][attend_nm]' value='{$hairmake_gst[$head_index]['HairmakeGuestTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[HairmakeGuestTrn][{$head_counter}][phone_no]'  value='{$hairmake_gst[$head_index]['HairmakeGuestTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[HairmakeGuestTrn][{$head_counter}][cell_no]'   value='{$hairmake_gst[$head_index]['HairmakeGuestTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			   "<td colspan='9'><input  class='inputableField'  type='text' name='data[HairmakeGuestTrn][{$head_counter}][email]'     value='{$hairmake_gst[$head_index]['HairmakeGuestTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    			"</tr>".      	      	    			
                      "</table>";
                
                     /* サブテーブル作成 */
               echo "<div id='hairmakeGuest_{$head_counter}_div' >";
                     $sub_id = -1;
                     $sub_counter = 0;
            
                     for($sub_index=0;$sub_index < count($hairmake_gst);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	               if($head_id == $hairmake_gst[$sub_index]['HairmakeGuestTrnView']['id']){
    	     	            if($sub_id != $hairmake_gst[$sub_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id']){	    	     
    	       	               $sub_id  = $hairmake_gst[$sub_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id'];
    	       	    
    	         echo "<table id='hairmakeGuest_{$head_counter}_{$sub_counter}_table' class='list' >".    
    	       	       "<tr>".     	   
    	     			   "<th colspan='20' width='620'>Plan</th>".    	     			          	
    	     			   "<th colspan='20' width='620'>Other Request(RW)</th>".           	    
    	    		   "</tr>".
    	    			"<tr>".     	   
    	     			   "<td colspan='20'><input  class=''  type='hidden' name='data[HairmakeGuestSubTrn][{$head_counter}][{$sub_counter}][id]'  value='{$hairmake_gst[$sub_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id']}' />".
                                            "{$hairmake_gst[$sub_index]['HairmakeGuestTrnView']['menu']}".
    	     			   "</td>".
    	     			   "<td colspan='20'><input  class='inputableField'  type='text'   name='data[HairmakeGuestSubTrn][{$head_counter}][{$sub_counter}][note]' value='{$hairmake_gst[$sub_index]['HairmakeGuestTrnView']['hairmake_guest_sub_note']}'  style='width:99%' /></td>".	    
    	    			"</tr>".      
    	                "<tr>".     	   
    	     	   	       "<th colspan='4' width='84'><a href='#' class='add rowUnit' name='hairmakeGuest_".GC_HAIR_MAKE_GST."_{$head_counter}_{$sub_counter}' style='width:100%'>Add Time</a></th>".    	  
    	     		       "<th colspan='3' width='93'>No</th>".
    	     		       "<th colspan='4' width='124'>Start Time</th>".    
    	     		       "<th colspan='7' width='217'>Start Place</th>".
    	     		       "<th colspan='4' width='124'>Finish Time</th>".    
    	     		       "<th colspan='7' width='217'>Guest Name</th>".
    	                   "<th colspan='7' width='217'>Artist Name</th>".
    	     		       "<th colspan='4' width='124'>Other Request</th>".    	         	          	    
    	   		       "</tr>";

    	        $dtl_id = -1;
                $dtl_counter =0;
           		for($dtl_index=0;$dtl_index < count($hairmake_gst);$dtl_index++){      	 
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値 
           	  	    if($sub_id == $hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_sub_id']){
                 	  	 if($dtl_id != $hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_id']){
                      	    $dtl_id  = $hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_id'];     
    	    
    	    		       echo "<tr id='hairmakeGuest_{$head_counter}_{$sub_counter}_{$dtl_counter}_row'>".      	     
    	   	                       "<td colspan='4'><input type='hidden' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][id]' value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_id']}' />";
    	    		                   //１行目は削除負荷とする
    	                               if($dtl_counter == 0){
                                          echo  "<a href='#' class='delete rowUnit' name='hairmakeGuest_".GC_HAIR_MAKE_GST."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' disabled></a>&nbsp;</td>";	
                                       }else{
                 	                      echo  "<a href='#' class='delete rowUnit' name='hairmakeGuest_".GC_HAIR_MAKE_GST."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' >delete</a></td>";
                                       }    	
    	    		          echo "</td>".        
    	     		   	           "<td colspan='3'>".
    	                               "<select name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][no]' style='width:100%'>";	      
    	                                  for($j=1;$j < 21;$j++)
    	                                  {
    	     	                            if($hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['no'] == $j){
    	     	                               echo "<option value='$j' selected>$j</option>";	
    	     	                            }else{
    	                                       echo "<option value='$j' >$j</option>";	
    	     	                            }
    	                                  }    	     
    	                         echo "</select></td>".
    	                         "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][make_start_time]'  value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['make_start_time']}'         style='width:100%' /></td>".  
    	                         "<td colspan='7'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][make_start_place]' value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['make_start_place']}'        style='width:100%' /></td>".  	  
    	                         "<td colspan='4'><input class='time_mask inputableField' type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][make_end_time]'    value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['make_end_time']}'           style='width:100%' /></td>".   
    	                         "<td colspan='7'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][guest_nm]'         value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['guest_nm']}'                style='width:100%' /></td>".
    	                         "<td colspan='7'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][attend_nm]'        value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['attend_nm']}'               style='width:100%' /></td>".    	  
    	                         "<td colspan='4'><input class='inputableField'           type='text' name='data[HairmakeGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][note]'             value='{$hairmake_gst[$dtl_index]['HairmakeGuestTrnView']['hairmake_guest_dtl_note']}' style='width:99%' /></td>".  	    
    	                       "</tr>";
    	                       $dtl_counter++;    	    	              
    	    	          }    	    	      
           	            }  //サブIDと一致する詳細テーブル外部キー判定のIF文の締め          	             	         
    	           }   //詳細テーブルのデータ数だけLOOPするFOR文の締め     
    	       $sub_counter++; 
    	   echo "</table>";    	          	             
                        }                   
   	                   }    //ヘッダIDと一致するサブテーブル外部キー判定のIF文の締め  	               
                     }  //サブテーブルのデータ数だけLOOPするFOR文の締め                  
                 echo "</div>".
                      "<br /><br />";
                 $head_counter++;               
                 } //一意のヘッダID判定のIF文の締め                        
              }    //hairmake_viewのデータ数だけLOOPするFOR文の締め          
            }  //hairmakeデータ存在チェックIF文の締め
       ?>