 <input type="hidden" name="data[Category][id]"  value="<?php echo GC_MINISTER ?>" /> 
           
     <?php           
      if(count($minister) > 0)
      {
    	for($index=0;$index < count($minister);$index++){
           	 /* Ministerデータは複数データは不可で１つのみを想定 */
    	  echo "<input type='hidden' name='data[MinisterTrn][$index][id]' value='{$minister[$index]['MinisterTrnView']['id']}' />". 
               "<table id='Minister_table' class='list' >".    	       
    	          "<tr>".     	   
    	  		   	"<th colspan='13' width='403'>Company</th>".
    	   		   	"<th colspan='8'  width='248'>Name</th>".
    	     	   	"<th colspan='5'  width='155'>Office No</th>".
    	     		"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	 "</tr>".  
    	    	 "<tr>".         	     
    	     		"<td colspan='13'>{$minister[$index]['MinisterTrnView']['vendor_nm']}</td>".
    	     		"<td colspan='8'><input  class='inputableField'  type='text' name='data[MinisterTrn][$index][attend_nm]' value='{$minister[$index]['MinisterTrnView']['attend_nm']}' style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[MinisterTrn][$index][phone_no]'  value='{$minister[$index]['MinisterTrnView']['phone_no']}'  style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[MinisterTrn][$index][cell_no]'   value='{$minister[$index]['MinisterTrnView']['cell_no']}'   style='width:100%' /></td>".
    	     		"<td colspan='9'><input  class='inputableField'  type='text' name='data[MinisterTrn][$index][email]'     value='{$minister[$index]['MinisterTrnView']['email']}'     style='width:99%' /></td>".  
    	    	 "</tr>".    	       
    	         "<tr>".     	   
    	          "<th colspan='14' width='434'>Menu</th>".      	         
    	          "<th colspan='6'  width='186'>Working Start Time</th>".  
    	          "<th colspan='6'  width='186'>Working End Time</th>".  
    	          "<th colspan='4'  width='124'>Working Total</th>".     
    	          "<th colspan='10' width='310'>Other Request(RW)</th>".   	       	    
    	         "</tr>".
    	          "<tr>".         	     
    	     		"<td colspan='14'>{$minister[$index]['MinisterTrnView']['menu']}</td>".    	     		
    	     		"<td colspan='6'><input  id='Minister_start_time' class='time_mask work_time inputableField'  type='text' name='data[MinisterTrn][$index][working_start_time]' value='{$minister[$index]['MinisterTrnView']['working_start_time']}' style='width:100%' /></td>".
    	     		"<td colspan='6'><input  id='Minister_end_time'   class='time_mask work_time inputableField'  type='text' name='data[MinisterTrn][$index][working_end_time]'   value='{$minister[$index]['MinisterTrnView']['working_end_time']}'   style='width:100%' /></td>".
    	     		"<td colspan='4'><input  id='Minister_total_time' class=''                     type='text' name='data[MinisterTrn][$index][working_total]'      value='{$minister[$index]['MinisterTrnView']['fm_working_total']}'   style='width:99%' readonly /></td>".  
    	    	    "<td colspan='10'><input                          class='inputableField'                      type='text' name='data[MinisterTrn][$index][rw_note]'            value='{$minister[$index]['MinisterTrnView']['rw_note']}'            style='width:99%' /></td>". 
    	         "</tr>".
    	         "<tr>".     	   
    	           "<th colspan='12'  width='372'>Start Place</th>".
    	           "<th colspan='12'  width='372'>Finish Place</th>".
    	           "<th colspan='16'  width='496'>Other Request</th>".    	    
    	         "</tr>".     
    	         "<tr>".         	     
    	     		"<td colspan='12'><input  class='inputableField' type='text' name='data[MinisterTrn][$index][start_place]' value='{$minister[$index]['MinisterTrnView']['start_place']}'  style='width:100%' /></td>".
    	     		"<td colspan='12'><input  class='inputableField' type='text' name='data[MinisterTrn][$index][end_place]'   value='{$minister[$index]['MinisterTrnView']['end_place']}'    style='width:100%' /></td>".
    	     		"<td colspan='16'><input  class='inputableField' type='text' name='data[MinisterTrn][$index][note]'        value='{$minister[$index]['MinisterTrnView']['note']}'         style='width:99%' /></td>".    	     		
    	    	 "</tr>". 	    	    
    	    "</table>".
    	    "<br /><br />";
           }
      }
    	?>   