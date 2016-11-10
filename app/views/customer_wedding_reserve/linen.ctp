 <input type="hidden" name="data[Category][id]"  value="<?php echo GC_LINEN ?>" /> 
           
        <?php           
           if(count($linen) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($linen);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $linen[$head_index]['LinenTrnView']['id']){
         		   $head_id  = $linen[$head_index]['LinenTrnView']['id'];
         		 	
    	     echo "<input type='hidden' name='data[LinenTrn][{$head_counter}][id]' value='{$linen[$head_index]['LinenTrnView']['id']}' />". 
                  "<table id='linen_{$head_counter}_table' class='list' >".    	       
    	           "<tr>".     	   
    	  		    	"<th colspan='13' width='403'>Company</th>".
    	   		    	"<th colspan='8'  width='248'>Name</th>".
    	     	    	"<th colspan='5'  width='155'>Office No</th>".
    	     		    "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		    "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	   "</tr>".  
    	    	   "<tr>".         	     
    	     		   "<td colspan='13'>{$linen[$head_index]['LinenTrnView']['vendor_nm']}</td>".
    	     		   "<td colspan='8'><input  class='inputableField'  type='text' name='data[LinenTrn][{$head_counter}][attend_nm]' value='{$linen[$head_index]['LinenTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     		   "<td colspan='5'><input  class='inputableField'  type='text' name='data[LinenTrn][{$head_counter}][phone_no]'  value='{$linen[$head_index]['LinenTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     		   "<td colspan='5'><input  class='inputableField'  type='text' name='data[LinenTrn][{$head_counter}][cell_no]'   value='{$linen[$head_index]['LinenTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     		   "<td colspan='9'><input  class='inputableField'  type='text' name='data[LinenTrn][{$head_counter}][email]'     value='{$linen[$head_index]['LinenTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    	   "</tr>".
    	           "<tr>".     	   
    	              "<th colspan='6'  width='186'>Delivery Term</th>".
    	     	      "<th colspan='15' width='465'>Delivery Place</th>".
    	     		  "<th colspan='19' width='589'>Other Request(RW)</th>".    	     		 	       	    
    	    	   "</tr>".  
    	           "<tr>".         	     
    	     		  "<td colspan='6'><input  class='inputableField' type='text' name='data[LinenTrn][{$head_counter}][delivery_term]'  value='{$linen[$head_index]['LinenTrnView']['delivery_term']}'   style='width:100%' /></td>".
    	     		  "<td colspan='15'><input class='inputableField' type='text' name='data[LinenTrn][{$head_counter}][delivery_place]' value='{$linen[$head_index]['LinenTrnView']['delivery_place']}'  style='width:100%' /></td>".
    	     		  "<td colspan='19'><input class='inputableField' type='text' name='data[LinenTrn][{$head_counter}][note]'           value='{$linen[$head_index]['LinenTrnView']['note']}'            style='width:100%' /></td>".    	     	
    	     	   "</tr>".
    	          "</table>"; 	    
    	  
    	         /* サブテーブル作成 */
             echo "<div id='linen_{$head_counter}_div' >";
                   
                   $sub_id = -1;
                   $sub_counter = 0;
            
             echo "<table id='linen_{$head_counter}_{$sub_counter}_table' class='list' >".
    	                 "<tr>".   
                             "<th colspan='16' width='496'>Menu</th>".   
    	                     "<th colspan='6'  width='186'>Color</th>".    
    	                     "<th colspan='4'  width='124'>Count</th>".  
    	                     "<th colspan='14' width='434'>Other Request</th>".      	       	           	       	    
    	   	            "</tr>";
                   for($sub_index=0;$sub_index < count($linen);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $linen[$sub_index]['LinenTrnView']['id']){
    	             	
    	             	if($sub_id != $linen[$sub_index]['LinenTrnView']['linen_dtl_id']){	    	     
    	       	           $sub_id  = $linen[$sub_index]['LinenTrnView']['linen_dtl_id'];
    	       	     	          
    	      	           echo "<tr>".
    	       	                  "<td colspan='16'><input          type='hidden' name='data[LinenDtlTrn][{$head_counter}][{$sub_counter}][id]'   value='{$linen[$sub_index]['LinenTrnView']['linen_dtl_id']}' />".    	     				       
    	                                          "{$linen[$sub_index]['LinenTrnView']['menu']}".
    	                          "</td>".
    	      	                  "<td colspan='6'><input   class='inputableField'  type='text' name='data[LinenDtlTrn][{$head_counter}][{$sub_counter}][color]'  value='{$linen[$sub_index]['LinenTrnView']['color']}' style='width:100%' /></td>".
    	     				      "<td colspan='4'>{$linen[$sub_index]['LinenTrnView']['num']}</td>".
    	     				     "<td colspan='14'><input  class='inputableField'   type='text' name='data[LinenDtlTrn][{$head_counter}][{$sub_counter}][note]'   value='{$linen[$sub_index]['LinenTrnView']['linen_dtl_note']}' style='width:99%' /></td>".
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