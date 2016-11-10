 <input type="hidden" name="data[Category][id]"  value="<?php echo GC_PAPER ?>" /> 
           
        <?php           
           if(count($paper) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($paper);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $paper[$head_index]['PaperTrnView']['id']){
         		   $head_id  = $paper[$head_index]['PaperTrnView']['id'];
         		 	
    	     echo "<input type='hidden' name='data[PaperTrn][{$head_counter}][id]' value='{$paper[$head_index]['PaperTrnView']['id']}' />". 
                  "<table id='paper_{$head_counter}_table' class='list' >".    	       
    	           "<tr>".     	   
    	  		    	"<th colspan='13' width='403'>Company</th>".
    	   		    	"<th colspan='8'  width='248'>Name</th>".
    	     	    	"<th colspan='5'  width='155'>Office No</th>".
    	     		    "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		    "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	   "</tr>".  
    	    	   "<tr>".         	     
    	     		   "<td colspan='13'>{$paper[$head_index]['PaperTrnView']['vendor_nm']}</td>".
    	     		   "<td colspan='8'><input  class='inputableField'  type='text' name='data[PaperTrn][{$head_counter}][attend_nm]' value='{$paper[$head_index]['PaperTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     		   "<td colspan='5'><input  class='inputableField'  type='text' name='data[PaperTrn][{$head_counter}][phone_no]'  value='{$paper[$head_index]['PaperTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     		   "<td colspan='5'><input  class='inputableField'  type='text' name='data[PaperTrn][{$head_counter}][cell_no]'   value='{$paper[$head_index]['PaperTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     		   "<td colspan='9'><input  class='inputableField'  type='text' name='data[PaperTrn][{$head_counter}][email]'     value='{$paper[$head_index]['PaperTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    	   "</tr>".
    	           "<tr>".     	   
    	              "<th colspan='6'  width='186'>Delivery Term</th>".
    	     	      "<th colspan='15' width='465'>Delivery Place</th>".
    	     		  "<th colspan='19' width='589'>Other Request(RW)</th>".    	     		 	       	    
    	    	   "</tr>".  
    	           "<tr>".         	     
    	     		  "<td colspan='6'><input  class='inputableField' type='text' name='data[PaperTrn][{$head_counter}][delivery_term]'  value='{$paper[$head_index]['PaperTrnView']['delivery_term']}'   style='width:100%' /></td>".
    	     		  "<td colspan='15'><input class='inputableField' type='text' name='data[PaperTrn][{$head_counter}][delivery_place]' value='{$paper[$head_index]['PaperTrnView']['delivery_place']}'  style='width:100%' /></td>".
    	     		  "<td colspan='19'><input class='inputableField' type='text' name='data[PaperTrn][{$head_counter}][note]'           value='{$paper[$head_index]['PaperTrnView']['note']}'            style='width:100%' /></td>".    	     	
    	     	   "</tr>".
    	          "</table>"; 	    
    	  
    	         /* サブテーブル作成 */
             echo "<div id='paper_{$head_counter}_div' >";
                   
                   $sub_id = -1;
                   $sub_counter = 0;
            
             echo "<table id='paper_{$head_counter}_{$sub_counter}_table' class='list' >".
    	                 "<tr>".   
                             "<th colspan='16' width='496'>Menu</th>".   
                             "<th colspan='10' width='310'>Type</th>".        	               
    	                     "<th colspan='4'  width='124'>Count</th>".  
    	                     "<th colspan='10' width='310'>Other Request</th>".      	       	           	       	    
    	   	            "</tr>";
                   for($sub_index=0;$sub_index < count($paper);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $paper[$sub_index]['PaperTrnView']['id']){
    	             	
    	             	if($sub_id != $paper[$sub_index]['PaperTrnView']['paper_dtl_id']){	    	     
    	       	           $sub_id  = $paper[$sub_index]['PaperTrnView']['paper_dtl_id'];
    	       	     	          
    	      	           echo "<tr>".
    	       	                  "<td colspan='16'><input          type='hidden' name='data[PaperDtlTrn][{$head_counter}][{$sub_counter}][id]'   value='{$paper[$sub_index]['PaperTrnView']['paper_dtl_id']}' />".    	     				       
    	                                          "{$paper[$sub_index]['PaperTrnView']['menu']}".
    	                          "</td>".
    	      	                  "<td colspan='10'>{$paper[$sub_index]['PaperTrnView']['type']}</td>".
    	      	                  "<td colspan='4'>{$paper[$sub_index]['PaperTrnView']['num']}</td>".
    	     				     "<td colspan='10'><input  class='inputableField'  type='text' name='data[PaperDtlTrn][{$head_counter}][{$sub_counter}][note]'   value='{$paper[$sub_index]['PaperTrnView']['paper_dtl_note']}' style='width:99%' /></td>".
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