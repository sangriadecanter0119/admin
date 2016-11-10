<input type="hidden" name="data[Category][id]"  value="<?php echo GC_AV ?>" /> 
           
        <?php           
           if(count($av) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($av);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $av[$head_index]['AvTrnView']['id']){
         	       $head_id  = $av[$head_index]['AvTrnView']['id'];

         	echo "<input type='hidden' name='data[AvTrn][{$head_counter}][id]' value='{$av[$head_index]['AvTrnView']['id']}' />". 
                 "<table id='av_{$head_counter}_table' class='list' >".    	       
    	          "<tr>".     	   
    	  		   	"<th colspan='13' width='403'>Company</th>".
    	   		   	"<th colspan='8'  width='248'>Name</th>".
    	     	   	"<th colspan='5'  width='155'>Office No</th>".
    	     		"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	 "</tr>".  
    	    	 "<tr>".         	     
    	     		"<td colspan='13'>{$av[$head_index]['AvTrnView']['vendor_nm']}</td>".
    	     		"<td colspan='8'><input  class='inputableField'  type='text' name='data[AvTrn][{$head_counter}][attend_nm]' value='{$av[$head_index]['AvTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[AvTrn][{$head_counter}][phone_no]'  value='{$av[$head_index]['AvTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[AvTrn][{$head_counter}][cell_no]'   value='{$av[$head_index]['AvTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     		"<td colspan='9'><input  class='inputableField'  type='text' name='data[AvTrn][{$head_counter}][email]'     value='{$av[$head_index]['AvTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    	 "</tr>".
    	         "<tr>".     	   
    	            "<th colspan='40'  width='1240'>Other Request(RW)</th>".    	     	         	       	    
    	    	 "</tr>".  
    	         "<tr>".         	     
    	     		"<td colspan='40'><input  class='inputableField' type='text' name='data[AvTrn][{$head_counter}][note]' value='{$av[$head_index]['AvTrnView']['note']}' style='width:99%' /></td>".    	     		
    	     	 "</tr>".
         	    "</table>";
         	        	             	          	       
    	           /* サブテーブル作成 */
              echo "<div id='av_{$head_counter}_div' >";
                   $sub_id = -1;
                   $sub_counter = 0;
              
              echo "<table id='av_{$head_counter}_{$sub_counter}_table' class='list' >".
                   "<tr>".     	       	         
    	     	      "<th colspan='14' width='434'>Menu</th>".
    	              "<th colspan='3'  width='93'>AV Count</th>".
    	              "<th colspan='4'  width='124'>Setting Time</th>".
    	              "<th colspan='4'  width='124'>Finish Time</th>".
    	              "<th colspan='6'  width='186'>Setting Place</th>".
    	              "<th colspan='9'  width='279'>Other Request</th>".    	         	       	    
    	           "</tr>";
              
                   for($sub_index=0;$sub_index < count($av);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $av[$sub_index]['AvTrnView']['id']){
    	             	if($sub_id != $av[$sub_index]['AvTrnView']['av_menu_id']){	    	     
    	       	           $sub_id  = $av[$sub_index]['AvTrnView']['av_menu_id'];
    	       	           
    	       	       echo  "<tr>".         	     
    	                        "<td colspan='14'><input type='hidden' name='data[AvMenuTrn][{$head_counter}][{$sub_counter}][id]' value='{$av[$sub_index]['AvTrnView']['av_menu_id']}' />".
                                                "{$av[$sub_index]['AvTrnView']['menu']}".
    	     			       "<td colspan='3'>{$av[$sub_index]['AvTrnView']['av_count']}</td>".
    	     			       "<td colspan='4'><input  class='time_mask inputableField'  type='text' name='data[AvMenuTrn][{$head_counter}][{$sub_counter}][setting_start_time]' value='{$av[$sub_index]['AvTrnView']['setting_start_time']}' style='width:100%' /></td>".
    	     			       "<td colspan='4'><input  class='time_mask inputableField'  type='text' name='data[AvMenuTrn][{$head_counter}][{$sub_counter}][setting_end_time]'   value='{$av[$sub_index]['AvTrnView']['setting_end_time']}'   style='width:100%' /></td>".  
    	       	               "<td colspan='6'><input  class='inputableField'           type='text' name='data[AvMenuTrn][{$head_counter}][{$sub_counter}][setting_place]'      value='{$av[$sub_index]['AvTrnView']['setting_place']}'      style='width:100%' /></td>".
    	     			       "<td colspan='9'><input  class='inputableField'           type='text' name='data[AvMenuTrn][{$head_counter}][{$sub_counter}][note]'               value='{$av[$sub_index]['AvTrnView']['av_menu_note']}'       style='width:99%' /></td>".  
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