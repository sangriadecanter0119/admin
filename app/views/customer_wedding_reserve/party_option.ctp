  <input type="hidden" name="data[Category][id]"  value="<?php echo GC_PARTY_OPTION ?>" /> 
           
        <?php           
           if(count($party_option) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($party_option);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $party_option[$head_index]['PartyOptionTrnView']['id']){
         		   $head_id  = $party_option[$head_index]['PartyOptionTrnView']['id'];
         		   
    	  echo "<input type='hidden' name='data[PartyOptionTrn][{$head_counter}][id]' value='{$party_option[$head_index]['PartyOptionTrnView']['id']}' />". 
               "<table id='PartyOptionHeader_table' class='list' >".    	       
    	          "<tr>".     	   
    	  		   	"<th colspan='13' width='403'>Company</th>".
    	   		   	"<th colspan='8'  width='248'>Name</th>".
    	     	   	"<th colspan='5'  width='155'>Office No</th>".
    	     		"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	 "</tr>".  
    	    	 "<tr>".         	     
    	     		"<td colspan='13'>{$party_option[$head_index]['PartyOptionTrnView']['vendor_nm']}</td>".
    	     		"<td colspan='8'><input  class='inputableField'  type='text' name='data[PartyOptionTrn][{$head_counter}][attend_nm]' value='{$party_option[$head_index]['PartyOptionTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[PartyOptionTrn][{$head_counter}][phone_no]'  value='{$party_option[$head_index]['PartyOptionTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[PartyOptionTrn][{$head_counter}][cell_no]'   value='{$party_option[$head_index]['PartyOptionTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     		"<td colspan='9'><input  class='inputableField'  type='text' name='data[PartyOptionTrn][{$head_counter}][email]'     value='{$party_option[$head_index]['PartyOptionTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    	 "</tr>".
    	         "<tr>".     	   
    	            "<th colspan='4'  width='124'>Setting Time</th>". 
    	            "<th colspan='4'  width='124'>Finish Time</th>". 
    	            "<th colspan='13' width='403'>Setting Place</th>". 
    	            "<th colspan='5'  width='155'>Delivery Term</th>". 
    	            "<th colspan='14' width='434'>Delivery Place</th>".    
    	    	 "</tr>".  
    	         "<tr>".         	     
    	     		"<td colspan='4'><input  class='time_mask inputableField' type='text' name='data[PartyOptionTrn][{$head_counter}][setting_start_time]' value='{$party_option[$head_index]['PartyOptionTrnView']['setting_start_time']}' style='width:100%' /></td>".
    	            "<td colspan='4'><input  class='time_mask inputableField' type='text' name='data[PartyOptionTrn][{$head_counter}][setting_end_time]'   value='{$party_option[$head_index]['PartyOptionTrnView']['setting_end_time']}'    style='width:100%' /></td>".    	    
    	            "<td colspan='13'><input class='inputableField'           type='text' name='data[PartyOptionTrn][{$head_counter}][setting_place]'      value='{$party_option[$head_index]['PartyOptionTrnView']['setting_place']}'      style='width:100%' /></td>".    	    
    	     		"<td colspan='5'><input  class='inputableField'           type='text' name='data[PartyOptionTrn][{$head_counter}][delivery_term]'      value='{$party_option[$head_index]['PartyOptionTrnView']['delivery_term']}'      style='width:100%' /></td>".
    	     		"<td colspan='14'><input class='inputableField'           type='text' name='data[PartyOptionTrn][{$head_counter}][delivery_place]'     value='{$party_option[$head_index]['PartyOptionTrnView']['delivery_place']}'     style='width:100%' /></td>".    	     	
    	     	 "</tr>".
    	         "<tr>".
    	            "<th colspan='40' width='1240'>Other Request(RW)</th>".   
    	         "</tr>".
    	         "<tr>".
    	            "<td colspan='40'><input class='inputableField'  type='text' name='data[PartyOptionTrn][{$head_counter}][note]' value='{$party_option[$head_index]['PartyOptionTrnView']['note']}'  style='width:100%' /></td>".    	     	
    	         "</tr>".
    	        "</table>";
    	  
    	      /* サブテーブル作成 */
             echo "<div id='linen_{$head_counter}_div' >";
                   
                   $sub_id = -1;
                   $sub_counter = 0;
            
             echo "<table id='PartyOption_{$head_counter}_{$sub_counter}_table' class='list' >".    	      
    	           "<tr>".  
                 	  "<th colspan='13' width='403'>Menu</th>".  
    	              "<th colspan='11' width='341'>Content</th>".      	                 
    	              "<th colspan='3'  width='93'>Count</th>".      	       
    	              "<th colspan='13' width='403'>Other Request</th>".                   	       	           	       	    
    	   	       "</tr>"; 	    
    	    
                   for($sub_index=0;$sub_index < count($party_option);$sub_index++){
                   
                     //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $party_option[$sub_index]['PartyOptionTrnView']['id']){
    	             	
    	             	if($sub_id != $party_option[$sub_index]['PartyOptionTrnView']['party_option_dtl_id']){	    	     
    	       	           $sub_id  = $party_option[$sub_index]['PartyOptionTrnView']['party_option_dtl_id'];
    	      	
    	          echo	"<tr>".    	                  
    	                    "<td colspan='13'>".
    	                                      "<input  type='hidden' name='data[PartyOptionDtlTrn][{$head_counter}][{$sub_counter}][id]'        value='{$party_option[$sub_index]['PartyOptionTrnView']['party_option_dtl_id']}' />".
    	                                      "{$party_option[$sub_index]['PartyOptionTrnView']['menu']}".
    	                    "</td>".
    	     				"<td colspan='11'>{$party_option[$sub_index]['PartyOptionTrnView']['content']}</td>".
    	     				"<td colspan='3'>{$party_option[$sub_index]['PartyOptionTrnView']['num']}</td>".
    	     				"<td colspan='13'><input  class='inputableField'  type='text' name='data[PartyOptionDtlTrn][{$head_counter}][{$sub_counter}][note]'   value='{$party_option[$sub_index]['PartyOptionTrnView']['party_option_note']}' style='width:99%' /></td>".
    	     	 		  "</tr>";                     
                         $sub_counter++;
    	               }   	
    	            }
                  }
    	     echo "</table>";   
    	     echo "</div>";
    	     echo "<br /><br />";
    	     $head_counter++;
            }
          }
        }
     ?>