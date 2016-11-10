<input type="hidden" name="data[Category][id]"  value="<?php echo GC_HOUSE_WEDDING ?>" /> 
           
        <?php           
           if(count($house_wedding) > 0)
           {
           	for($index=0;$index < count($house_wedding);$index++){           		
          
    	  echo "<input type='hidden' name='data[HouseWeddingTrn][{$index}][id]' value='{$house_wedding[$index]['HouseWeddingTrnView']['id']}' />". 
               "<table id='HouseWedding_{$index}_table' class='list' >".    	       
    	          "<tr>".     	   
    	  		   	"<th colspan='13' width='403'>Company</th>".
    	   		   	"<th colspan='8'  width='248'>Name</th>".
    	     	   	"<th colspan='5'  width='155'>Office No</th>".
    	     		"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	 "</tr>".  
    	    	 "<tr>".         	     
    	     		"<td colspan='13'>{$house_wedding[$index]['HouseWeddingTrnView']['vendor_nm']}</td>".
    	     		"<td colspan='8'><input  class='inputableField'  type='text' name='data[HouseWeddingTrn][{$index}][attend_nm]' value='{$house_wedding[$index]['HouseWeddingTrnView']['attend_nm']}' style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[HouseWeddingTrn][{$index}][phone_no]'  value='{$house_wedding[$index]['HouseWeddingTrnView']['phone_no']}'  style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[HouseWeddingTrn][{$index}][cell_no]'   value='{$house_wedding[$index]['HouseWeddingTrnView']['cell_no']}'   style='width:100%' /></td>".
    	     		"<td colspan='9'><input  class='inputableField'  type='text' name='data[HouseWeddingTrn][{$index}][email]'     value='{$house_wedding[$index]['HouseWeddingTrnView']['email']}'     style='width:99%' /></td>".  
    	    	 "</tr>".    	
    	          "<tr>".     	   
    	            "<th colspan='18' width='558'>Site Name</th>".
    	            "<th colspan='22' width='682'>Other Request</th>".    	   
    	         "</tr>".     
    	         "<tr>".         	     
    	     		"<td colspan='18'>{$house_wedding[$index]['HouseWeddingTrnView']['site']}</td>".
    	     		"<td colspan='22'><input class='inputableField' type='text' name='data[HouseWeddingTrn][{$index}][note]' value='{$house_wedding[$index]['HouseWeddingTrnView']['note']}'  style='width:99%' /></td>".    	     		  	     		
    	    	 "</tr>". 	    	          
    	         "<tr>".     
    	           "<th colspan='4' width='124'>Start Time</th>".  
    	           "<th colspan='4' width='124'>End Time</th>".  
    	           "<th colspan='4' width='124'>Total Time</th>".      
    	           "<th colspan='4' width='124'>Deposit Date</th>". 
    	           "<th colspan='6' width='186'>Deposite Payment</th>".  
    	           "<th colspan='6' width='186'>Depoist By</th>". 
    	           "<th colspan='4' width='124'>Insurance Date</th>". 
    	           "<th colspan='8' width='186'>Insurance Company</th>".    	          	       	    
    	         "</tr>".
    	          "<tr>".     	     
    	     	    "<td colspan='4'><input  id='HouseWedding{$index}_start_time' class='time_mask work_time inputableField'  type='text' name='data[HouseWeddingTrn][{$index}][start_time]'        value='{$house_wedding[$index]['HouseWeddingTrnView']['start_time']}'        style='width:100%' /></td>".
    	     		"<td colspan='4'><input  id='HouseWedding{$index}_end_time'   class='time_mask work_time inputableField'  type='text' name='data[HouseWeddingTrn][{$index}][end_time]'          value='{$house_wedding[$index]['HouseWeddingTrnView']['end_time']}'          style='width:100%' /></td>".
    	     		"<td colspan='4'><input  id='HouseWedding{$index}_total_time' class=''                     type='text' name='data[HouseWeddingTrn][{$index}][total_time]'        value='{$house_wedding[$index]['HouseWeddingTrnView']['fm_working_total']}'  style='width:99%' readonly /></td>".  
    	    	    "<td colspan='4'><input                                       class='date_mask inputableField'            type='text' name='data[HouseWeddingTrn][{$index}][deposit_dt]'        value='{$common->evalForShortDate($house_wedding[$index]['HouseWeddingTrnView']['deposit_dt'])}'        style='width:100%' /></td>".    	     		
    	     		"<td colspan='6'><input                                       class='inputableField'                      type='text' name='data[HouseWeddingTrn][{$index}][deposit_payment]'   value='{$house_wedding[$index]['HouseWeddingTrnView']['deposit_payment']}'   style='width:100%' /></td>".    	     		
    	     		"<td colspan='6'><input                                       class='date_mask inputableField'            type='text' name='data[HouseWeddingTrn][{$index}][deposit_by]'        value='{$house_wedding[$index]['HouseWeddingTrnView']['deposit_by']}'        style='width:100%' /></td>".
    	     		"<td colspan='4'><input                                       class='date_mask inputableField'            type='text' name='data[HouseWeddingTrn][{$index}][insurance_dt]'      value='{$common->evalForShortDate($house_wedding[$index]['HouseWeddingTrnView']['insurance_dt'])}'      style='width:100%' /></td>".
    	     		"<td colspan='8'><input                                       class='date_mask inputableField'            type='text' name='data[HouseWeddingTrn][{$index}][insurance_company]' value='{$house_wedding[$index]['HouseWeddingTrnView']['insurance_company']}' style='width:99%'   /></td>".  
    	    	"</tr>".    	    
    	        "<tr>".
    	            "<th colspan='40' width='1240'>Other Request(RW)</th>".
    	        "</tr>".  
    	        "<tr>".
    	            "<td colspan='40'><input class='inputableField' type='text' name='data[HouseWeddingTrn][{$index}][rw_note]' value='{$house_wedding[$index]['HouseWeddingTrnView']['rw_note']}'  style='width:99%' /></td>".
    	       "</tr>".    
    	    "</table>".
           "<br /><br />";
           }
        }
  	?>        