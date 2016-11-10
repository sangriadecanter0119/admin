 <input type="hidden" name="data[Category][id]"      value="<?php echo GC_TRANS_GST ?>" />               
       
       <?php 
           //GUEST
           if(count($trans_gst) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 for($head_index=0;$head_index < count($trans_gst);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $trans_gst[$head_index]['TransGuestTrnView']['id']){
         		 	$head_id  = $trans_gst[$head_index]['TransGuestTrnView']['id'];
         		 	
                echo  "<input type='hidden' name='data[TransGuestTrn][{$head_counter}][id]' value='{$trans_gst[$head_index]['TransGuestTrnView']['id']}' />". 
                      "<table id='transGuest_{$head_counter}_table' class='list' >".
    	                "<tr>".     	   
    	                   "<th colspan='13' width='403'>Company</th>".
    	                   "<th colspan='8'  width='248'>Name</th>".
    	     			   "<th colspan='5'  width='155'>Office No</th>".
    	     			   "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			   "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    			"</tr>".  
    	    			"<tr>".         	     
    	     			   "<td colspan='13'>{$trans_gst[$head_index]['TransGuestTrnView']['vendor_nm']}</td>".
    	     			   "<td colspan='8'><input  class='inputableField'  type='text' name='data[TransGuestTrn][{$head_counter}][attend_nm]' value='{$trans_gst[$head_index]['TransGuestTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[TransGuestTrn][{$head_counter}][phone_no]'  value='{$trans_gst[$head_index]['TransGuestTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[TransGuestTrn][{$head_counter}][cell_no]'   value='{$trans_gst[$head_index]['TransGuestTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			   "<td colspan='9'><input  class='inputableField'  type='text' name='data[TransGuestTrn][{$head_counter}][email]'     value='{$trans_gst[$head_index]['TransGuestTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    			"</tr>".      	      	    			
                      "</table>";
                
                     /* サブテーブル作成 */
               echo "<div id='transGuest_{$head_counter}_div' >";
                     $sub_id = -1;
                     $sub_counter = 0;
            
                     for($sub_index=0;$sub_index < count($trans_gst);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	               if($head_id == $trans_gst[$sub_index]['TransGuestTrnView']['id']){
    	     	            if($sub_id != $trans_gst[$sub_index]['TransGuestTrnView']['trans_guest_sub_id']){	    	     
    	       	               $sub_id  = $trans_gst[$sub_index]['TransGuestTrnView']['trans_guest_sub_id'];
    	       	    
    	         echo "<table id='transGuest_{$head_counter}_{$sub_counter}_table' class='list' >".    
    	       	       "<tr>".     	   
    	     			   "<th colspan='12' width='372'>Plan</th>".
    	     			   "<th colspan='4'  width='124'>Vihicular Type</th>".
    	     			   "<th colspan='9'  width='279'>Final Destination</th>".
    	     			   "<th colspan='6'  width='186'>Working Start Time</th>".
    	     			   "<th colspan='6'  width='186'>Working End Time</th>".    	         	
    	     			   "<th colspan='3'  width='93'>Working Total</th>".           	    
    	    			"</tr>".
    	    			"<tr>".     	   
    	     			   "<td colspan='12'><input  class=''  type='hidden' name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][id]'    value='{$trans_gst[$sub_index]['TransGuestTrnView']['trans_guest_sub_id']}' />".
                                            "{$trans_gst[$sub_index]['TransGuestTrnView']['menu']}".
    	     			   "</td>".
    	     			   "<td colspan='4'><input  class=''                     type='hidden'                                                       name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][vihicular_type]'     value='{$trans_gst[$sub_index]['TransGuestTrnView']['vihicular_type']}'     style='width:99%' />".
    	                                    "{$trans_gst[$sub_index]['TransGuestTrnView']['vihicular_type']}".
    	     			   "</td>".
    	                   "<td colspan='9'><input  class='inputableField'                      type='text'                                                         name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][final_dest]'         value='{$trans_gst[$sub_index]['TransGuestTrnView']['final_dest']}'         style='width:99%' /></td>".
    	     			   "<td colspan='6'><input  class='time_mask work_time inputableField'  type='text' id='transGuest{$head_counter}{$sub_counter}_start_time' name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][working_start_time]' value='{$trans_gst[$sub_index]['TransGuestTrnView']['working_start_time']}' style='width:99%' /></td>".
    	     			   "<td colspan='6'><input  class='time_mask work_time inputableField'  type='text' id='transGuest{$head_counter}{$sub_counter}_end_time'   name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][working_end_time]'   value='{$trans_gst[$sub_index]['TransGuestTrnView']['working_end_time']}'   style='width:99%' /></td>".   	  
    	     			   "<td colspan='3'><input  class=''                     type='text' id='transGuest{$head_counter}{$sub_counter}_total_time' name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][working_total]'      value='{$trans_gst[$sub_index]['TransGuestTrnView']['fm_working_total']}'   style='width:98%' readonly /></td>".  	    
    	    			"</tr>".      	
    	    			"<tr>".     	 	
    	     				"<th colspan='40' width='1240'>Other Request(RW)</th>".   	          	    
    	    			"</tr>".     	     
    	                "<tr>".     
    	                    "<td colspan='40'><input class='inputableField' type='text' name='data[TransGuestSubTrn][{$head_counter}][{$sub_counter}][note]'              value='{$trans_gst[$sub_index]['TransGuestTrnView']['trans_guest_sub_note']}' style='width:99%' /></td>".     	    
    	                "</tr>".    	                    
    	               "<tr>".     	   
    	     	   	       "<th colspan='4'  width='124'><a href='#' class='add rowUnit' name='transGuest_".GC_TRANS_GST."_{$head_counter}_{$sub_counter}' style='width:100%'>Add Location</a></th>".    	  
    	     		       "<th colspan='2'  width='62'>No</th>".
                           "<th colspan='2'  width='62'>PAX</th>".
    	                   "<th colspan='6'  width='186'>Representative</th>".
    	     		       "<th colspan='2'  width='62'>Dep Time</th>".    
    	     		       "<th colspan='6'  width='186'>Departure Place</th>".    	                  
    	                   "<th colspan='2'  width='62'>PAX</th>".
    	     		       "<th colspan='2'  width='62'>Drop Time</th>".    
    	     		       "<th colspan='6'  width='186'>Drop Off Place</th>".
    	                   "<th colspan='8' width='248'>Other Request</th>".      		            	          	    
    	   		       "</tr>";

    	        $dtl_id = -1;
                $dtl_counter =0;
           		for($dtl_index=0;$dtl_index < count($trans_gst);$dtl_index++){      	 
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値 
           	  	    if($sub_id == $trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_sub_id']){
                 	  	 if($dtl_id != $trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_id']){
                      	    $dtl_id  = $trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_id'];     
    	    
    	    		       echo "<tr id='transGuest_{$head_counter}_{$sub_counter}_{$dtl_counter}_row'>".      	     
    	   	                       "<td colspan='4'><input type='hidden' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][id]' value='{$trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_id']}' />";
    	    		                   //１行目は削除負荷とする
    	                               if($dtl_counter == 0){
                                          echo  "<a href='#' class='delete rowUnit' name='transGuest_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' disabled></a>&nbsp;</td>";	
                                       }else{
                 	                      echo  "<a href='#' class='delete rowUnit' name='transGuest_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' >delete</a></td>";
                                       }    	
    	    		          echo "</td>".        
    	     		   	           "<td colspan='2'>".
    	                               "<select name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][no]' style='width:100%'>";	      
    	                                  for($j=1;$j < 21;$j++)
    	                                  {
    	     	                            if($trans_gst[$dtl_index]['TransGuestTrnView']['no'] == $j){
    	     	                               echo "<option value='$j' selected>$j</option>";	
    	     	                            }else{
    	                                       echo "<option value='$j' >$j</option>";	
    	     	                            }
    	                                  }    	     
    	                         echo "</select></td>".
    	                          "<td colspan='2'>".
    	                            "<select class='inputableField' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][total_departure_passenger]' style='width:100%'>";
    	                                for($i=1;$i < 100;$i++)
    	                                {
    	     	                           if($trans_gst[$dtl_index]['TransGuestTrnView']['total_departure_passenger'] == $i){
    	     	                               echo "<option value='$i' selected>$i</option>";	
    	     	                           }else{
    	                                       echo "<option value='$i' >$i</option>";	
    	     	                           }
    	                                }    	     
    	                     echo "</select></td>". 
    	                         "<td colspan='6'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][representative_nm]'  value='{$trans_gst[$dtl_index]['TransGuestTrnView']['representative_nm']}'  style='width:100%' /></td>".  	
    	                         "<td colspan='2'><input class='time_mask inputableField'  type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_time]'     value='{$trans_gst[$dtl_index]['TransGuestTrnView']['departure_time']}'     style='width:100%' /></td>".  	  
    	                         "<td colspan='6'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_place]'    value='{$trans_gst[$dtl_index]['TransGuestTrnView']['departure_place']}'    style='width:100%' /></td>".   
    	                        
    	                     "<td colspan='2'>".
    	                            "<select class='inputableField' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][total_arrival_passenger]' style='width:100%'>";
    	                                for($i=1;$i < 100;$i++)
    	                                {
    	     	                           if($trans_gst[$dtl_index]['TransGuestTrnView']['total_arrival_passenger'] == $i){
    	     	                               echo "<option value='$i' selected>$i</option>";	
    	     	                           }else{
    	                                       echo "<option value='$i' >$i</option>";	
    	     	                           }
    	                                }    	     
    	                     echo "</select></td>". 
    	                         
    	                         "<td colspan='2'><input class='time_mask inputableField'   type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_time]'    value='{$trans_gst[$dtl_index]['TransGuestTrnView']['arrival_time']}'       style='width:100%' /></td>".
    	                         "<td colspan='6'><input class='inputableField'             type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_place]'   value='{$trans_gst[$dtl_index]['TransGuestTrnView']['arrival_place']}'      style='width:100%' /></td>".    
    	                         "<td colspan='10'><input class='inputableField'            type='text' name='data[TransGuestDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][note]'            value='{$trans_gst[$dtl_index]['TransGuestTrnView']['trans_guest_dtl_note']}' style='width:99%' /></td>". 
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
              }    //trans_viewのデータ数だけLOOPするFOR文の締め          
            }  //transデータ存在チェックIF文の締め
       ?>