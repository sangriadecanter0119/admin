<input type="hidden" name="data[Category][id]"  value="<?php echo GC_MC ?>" /> 
           
        <?php           
           if(count($mc) > 0)
           {
           	for($index=0;$index < count($mc);$index++){
    	  
          echo "<input type='hidden' name='data[McTrn][{$index}][id]' value='{$mc[$index]['McTrnView']['id']}' />". 
               "<table id='mc_{$index}_table' class='list' >".    	       
    	          "<tr>".     	   
    	  		   	"<th colspan='13' width='403'>Company</th>".
    	   		   	"<th colspan='8'  width='248'>Name</th>".
    	     	   	"<th colspan='5'  width='155'>Office No</th>".
    	     		"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	 "</tr>".  
    	    	 "<tr>".         	     
    	     		"<td colspan='13'>{$mc[$index]['McTrnView']['vendor_nm']}</td>".
    	     		"<td colspan='8'><input  class='inputableField'  type='text' name='data[McTrn][{$index}][attend_nm]' value='{$mc[$index]['McTrnView']['attend_nm']}' style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[McTrn][{$index}][phone_no]'  value='{$mc[$index]['McTrnView']['phone_no']}'  style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[McTrn][{$index}][cell_no]'   value='{$mc[$index]['McTrnView']['cell_no']}'   style='width:100%' /></td>".
    	     		"<td colspan='9'><input  class='inputableField'  type='text' name='data[McTrn][{$index}][email]'     value='{$mc[$index]['McTrnView']['email']}'     style='width:99%' /></td>".  
    	    	 "</tr>".    	       
    	         "<tr>".     	   
    	          "<th colspan='10' width='310'>Menu</th>".  
    	          "<th colspan='4'  width='124'>MC Number</th>".  
    	          "<th colspan='6'  width='186'>Working Start Time</th>".  
    	          "<th colspan='6'  width='186'>Working End Time</th>".  
    	          "<th colspan='4'  width='124'>Working Total</th>".       	
                  "<th colspan='10' width='310'>Other Request(RW)</th>".         	    
    	         "</tr>".
    	          "<tr>".         	     
    	     		"<td colspan='10'>{$mc[$index]['McTrnView']['menu']}</td>".
    	     		"<td colspan='4'>{$mc[$index]['McTrnView']['mc_num']}</td>".
    	     		"<td colspan='6'><input  id='mc{$index}_start_time' class='time_mask work_time inputableField'  type='text' name='data[McTrn][{$index}][working_start_time]' value='{$mc[$index]['McTrnView']['working_start_time']}' style='width:100%' /></td>".
    	     		"<td colspan='6'><input  id='mc{$index}_end_time'   class='time_mask work_time inputableField'  type='text' name='data[McTrn][{$index}][working_end_time]'   value='{$mc[$index]['McTrnView']['working_end_time']}'   style='width:100%' /></td>".
    	     		"<td colspan='4'><input  id='mc{$index}_total_time' class=''                     type='text' name='data[McTrn][{$index}][working_total]'      value='{$mc[$index]['McTrnView']['fm_working_total']}'   style='width:99%' readonly /></td>".  
                    "<td colspan='10'><input class='inputableField'                                                 type='text' name='data[McTrn][{$index}][rw_note]'            value='{$mc[$index]['McTrnView']['rw_note']}'            style='width:99%' /></td>".   
                 "</tr>".
    	         "<tr>".     	   
    	           "<th colspan='12'  width='372'>Start Place</th>".
    	           "<th colspan='12'  width='372'>Finish Place</th>".
    	           "<th colspan='16'  width='496'>Other Request</th>".    	    
    	         "</tr>".     
    	         "<tr>".         	     
    	     		"<td colspan='12'><input class='inputableField' type='text' name='data[McTrn][{$index}][start_place]' value='{$mc[$index]['McTrnView']['start_place']}'  style='width:100%' /></td>".
    	     		"<td colspan='12'><input class='inputableField' type='text' name='data[McTrn][{$index}][end_place]'   value='{$mc[$index]['McTrnView']['end_place']}'    style='width:100%' /></td>".
    	     		"<td colspan='16'><input class='inputableField' type='text' name='data[McTrn][{$index}][note]'        value='{$mc[$index]['McTrnView']['note']}'         style='width:99%' /></td>".    	     		
    	    	 "</tr>". 	    	    
    	    "</table>".
            "<br /><br />";
           }
        }
  	?>   