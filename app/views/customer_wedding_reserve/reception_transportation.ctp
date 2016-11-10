 <input type="hidden" name="data[Category][id]"      value="<?php echo GC_RECEPTION_TRANS ?>" />               
       
       <?php      
           if(count($trans_recep) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 for($head_index=0;$head_index < count($trans_recep);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $trans_recep[$head_index]['TransRecepTrnView']['id']){
         		 	$head_id  = $trans_recep[$head_index]['TransRecepTrnView']['id'];
         		 	
                echo  "<input type='hidden' name='data[TransRecepTrn][{$head_counter}][id]' value='{$trans_recep[$head_index]['TransRecepTrnView']['id']}' />". 
                      "<table id='transRecep_{$head_counter}_table' class='list' >".
    	                "<tr>".     	   
    	                   "<th colspan='13' width='403'>Company</th>".
    	                   "<th colspan='8'  width='248'>Name</th>".
    	     			   "<th colspan='5'  width='155'>Office No</th>".
    	     			   "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			   "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    			"</tr>".  
    	    			"<tr>".         	     
    	     			   "<td colspan='13'>{$trans_recep[$head_index]['TransRecepTrnView']['vendor_nm']}</td>".
    	     			   "<td colspan='8'><input  class='inputableField'  type='text' name='data[TransRecepTrn][{$head_counter}][attend_nm]' value='{$trans_recep[$head_index]['TransRecepTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[TransRecepTrn][{$head_counter}][phone_no]'  value='{$trans_recep[$head_index]['TransRecepTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[TransRecepTrn][{$head_counter}][cell_no]'   value='{$trans_recep[$head_index]['TransRecepTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			   "<td colspan='9'><input  class='inputableField'  type='text' name='data[TransRecepTrn][{$head_counter}][email]'     value='{$trans_recep[$head_index]['TransRecepTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    			"</tr>".      	      	    			
                      "</table>";
                
                     /* サブテーブル作成 */
               echo "<div id='transRecep_{$head_counter}_div' >";
                     $sub_id = -1;
                     $sub_counter = 0;
            
                     for($sub_index=0;$sub_index < count($trans_recep);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	               if($head_id == $trans_recep[$sub_index]['TransRecepTrnView']['id']){
    	     	            if($sub_id != $trans_recep[$sub_index]['TransRecepTrnView']['trans_recep_sub_id']){	    	     
    	       	               $sub_id  = $trans_recep[$sub_index]['TransRecepTrnView']['trans_recep_sub_id'];
    	       	    
    	         echo "<table id='transRecep_{$head_counter}_{$sub_counter}_table' class='list' >".    
    	       	       "<tr>".     	   
    	     			   "<th colspan='12' width='372'>Plan</th>".
    	     			   "<th colspan='4'  width='124'>Vihicular Type</th>".
    	     			   "<th colspan='9'  width='279'>Final Destination</th>".
    	     			   "<th colspan='6'  width='186'>Working Start Time</th>".
    	     			   "<th colspan='6'  width='186'>Working End Time</th>".    	         	
    	     			   "<th colspan='3'  width='93'>Working Total</th>".           	    
    	    			"</tr>".
    	    			"<tr>".     	   
    	     			   "<td colspan='12'><input  class=''  type='hidden' name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][id]'    value='{$trans_recep[$sub_index]['TransRecepTrnView']['trans_recep_sub_id']}' />".
                                            "{$trans_recep[$sub_index]['TransRecepTrnView']['menu']}".
    	     			   "</td>".
    	     			   "<td colspan='4'><input  class=''                     type='hidden'                                                       name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][vihicular_type]'     value='{$trans_recep[$sub_index]['TransRecepTrnView']['vihicular_type']}'     style='width:99%' />".
    	                                    "{$trans_recep[$sub_index]['TransRecepTrnView']['vihicular_type']}".
    	     			   "</td>".
    	                   "<td colspan='9'><input  class='inputableField'                      type='text'                                                         name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][final_dest]'         value='{$trans_recep[$sub_index]['TransRecepTrnView']['final_dest']}'         style='width:99%' /></td>".
    	     			   "<td colspan='6'><input  class='time_mask work_time inputableField'  type='text' id='transRecep{$head_counter}{$sub_counter}_start_time' name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][working_start_time]' value='{$trans_recep[$sub_index]['TransRecepTrnView']['working_start_time']}' style='width:99%' /></td>".
    	     			   "<td colspan='6'><input  class='time_mask work_time inputableField'  type='text' id='transRecep{$head_counter}{$sub_counter}_end_time'   name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][working_end_time]'   value='{$trans_recep[$sub_index]['TransRecepTrnView']['working_end_time']}'   style='width:99%' /></td>".   	  
    	     			   "<td colspan='3'><input  class=''                     type='text' id='transRecep{$head_counter}{$sub_counter}_total_time' name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][working_total]'      value='{$trans_recep[$sub_index]['TransRecepTrnView']['fm_working_total']}'   style='width:98%' readonly /></td>".  	    
    	    			"</tr>".      	
    	    			"<tr>".     	 	
    	     				"<th colspan='40' width='1240'>Other Request(RW)</th>".   	          	    
    	    			"</tr>".     	     
    	                "<tr>".     
    	                    "<td colspan='40'><input class='inputableField' type='text' name='data[TransRecepSubTrn][{$head_counter}][{$sub_counter}][note]'              value='{$trans_recep[$sub_index]['TransRecepTrnView']['trans_recep_sub_note']}' style='width:99%' /></td>".     	    
    	                "</tr>".    	                    
    	               "<tr>".     	   
    	     	   	       "<th colspan='4'  width='124'><a href='#' class='add rowUnit' name='transRecep_".GC_RECEPTION_TRANS."_{$head_counter}_{$sub_counter}' style='width:100%'>Add Location</a></th>".    	  
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
           		for($dtl_index=0;$dtl_index < count($trans_recep);$dtl_index++){      	 
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値 
           	  	    if($sub_id == $trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_sub_id']){
                 	  	 if($dtl_id != $trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_id']){
                      	    $dtl_id  = $trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_id'];     
    	    
    	    		       echo "<tr id='transRecep_{$head_counter}_{$sub_counter}_{$dtl_counter}_row'>".      	     
    	   	                       "<td colspan='4'><input type='hidden' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][id]' value='{$trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_id']}' />";
    	    		                   //１行目は削除負荷とする
    	                               if($dtl_counter == 0){
                                          echo  "<a href='#' class='delete rowUnit' name='transRecep_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' disabled></a>&nbsp;</td>";	
                                       }else{
                 	                      echo  "<a href='#' class='delete rowUnit' name='transRecep_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' >delete</a></td>";
                                       }    	
    	    		          echo "</td>".        
    	     		   	           "<td colspan='2'>".
    	                               "<select name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][no]' style='width:100%'>";	      
    	                                  for($j=1;$j < 21;$j++)
    	                                  {
    	     	                            if($trans_recep[$dtl_index]['TransRecepTrnView']['no'] == $j){
    	     	                               echo "<option value='$j' selected>$j</option>";	
    	     	                            }else{
    	                                       echo "<option value='$j' >$j</option>";	
    	     	                            }
    	                                  }    	     
    	                         echo "</select></td>".
    	                          "<td colspan='2'>".
    	                            "<select name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][total_departure_passenger]' style='width:100%'>";
    	                                for($i=1;$i < 100;$i++)
    	                                {
    	     	                           if($trans_recep[$dtl_index]['TransRecepTrnView']['total_departure_passenger'] == $i){
    	     	                               echo "<option value='$i' selected>$i</option>";	
    	     	                           }else{
    	                                       echo "<option value='$i' >$i</option>";	
    	     	                           }
    	                                }    	     
    	                     echo "</select></td>". 
    	                         "<td colspan='6'><input                   class='inputableField' type='text' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][representative_nm]'  value='{$trans_recep[$dtl_index]['TransRecepTrnView']['representative_nm']}'  style='width:100%' /></td>".  	
    	                         "<td colspan='2'><input class='time_mask' class='inputableField' type='text' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_time]'     value='{$trans_recep[$dtl_index]['TransRecepTrnView']['departure_time']}'     style='width:100%' /></td>".  	  
    	                         "<td colspan='6'><input class=''          class='inputableField' type='text' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_place]'    value='{$trans_recep[$dtl_index]['TransRecepTrnView']['departure_place']}'    style='width:100%' /></td>".   
    	                        
    	                     "<td colspan='2'>".
    	                            "<select name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][total_arrival_passenger]' style='width:100%'>";
    	                                for($i=1;$i < 100;$i++)
    	                                {
    	     	                           if($trans_recep[$dtl_index]['TransRecepTrnView']['total_arrival_passenger'] == $i){
    	     	                               echo "<option value='$i' selected>$i</option>";	
    	     	                           }else{
    	                                       echo "<option value='$i' >$i</option>";	
    	     	                           }
    	                                }    	     
    	                     echo "</select></td>". 
    	                         
    	                         "<td colspan='2'><input  class='time_mask inputableField'  type='text' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_time]'    value='{$trans_recep[$dtl_index]['TransRecepTrnView']['arrival_time']}'       style='width:100%' /></td>".
    	                         "<td colspan='6'><input  class='inputableField'            type='text' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_place]'   value='{$trans_recep[$dtl_index]['TransRecepTrnView']['arrival_place']}'      style='width:100%' /></td>".    
    	                         "<td colspan='10'><input class='inputableField'            type='text' name='data[TransRecepDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][note]'            value='{$trans_recep[$dtl_index]['TransRecepTrnView']['trans_recep_dtl_note']}' style='width:99%' /></td>". 
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