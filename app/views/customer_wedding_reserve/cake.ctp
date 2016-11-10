 <input type="hidden" name="data[Category][id]"  value="<?php echo GC_CAKE ?>" /> 
           
        <?php           
           if(count($cake) > 0)
           {           	
           	    $head_id = -1;
         	    $head_counter = 0;
         	 
         	    for($head_index=0;$head_index < count($cake);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $cake[$head_index]['CakeTrnView']['id']){
         	 	   $head_id  = $cake[$head_index]['CakeTrnView']['id'];

             echo "<input type='hidden' name='data[CakeTrn][{$head_counter}][id]' value='{$cake[$head_index]['CakeTrnView']['id']}' />". 
                  "<table id='cake_{$head_counter}_table' class='list' >".    	       
    	          "<tr>".     	   
    	  		   	"<th colspan='13' width='403'>Company</th>".
    	   		   	"<th colspan='8'  width='248'>Name</th>".
    	     	   	"<th colspan='5'  width='155'>Office No</th>".
    	     		"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     		"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    	 "</tr>".  
    	    	 "<tr>".         	     
    	     		"<td colspan='13'>{$cake[$head_index]['CakeTrnView']['vendor_nm']}</td>".
    	     		"<td colspan='8'><input  class='inputableField'  type='text' name='data[CakeTrn][{$head_counter}][attend_nm]'  value='{$cake[$head_index]['CakeTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[CakeTrn][{$head_counter}][phone_no]'  value='{$cake[$head_index]['CakeTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     		"<td colspan='5'><input  class='inputableField'  type='text' name='data[CakeTrn][{$head_counter}][cell_no]'   value='{$cake[$head_index]['CakeTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     		"<td colspan='9'><input  class='inputableField'  type='text' name='data[CakeTrn][{$head_counter}][email]'     value='{$cake[$head_index]['CakeTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    	 "</tr>".
                 "<tr>".     	   
    	            "<th colspan='8'  width='248'>Delivery Person</th>".
    	     	    "<th colspan='4'  width='124'>Delivery Term</th>".
    	     		"<th colspan='13' width='403'>Delivery Place</th>".
    	     		"<th colspan='15' width='465'>Other Request(RW)</th>".    	       	       	    
    	    	 "</tr>".
                 "<tr>".  
             	    "<td colspan='8'><input  class='inputableField' type='text' name='data[CakeTrn][{$head_counter}][delivery_nm]'    value='{$cake[$head_index]['CakeTrnView']['delivery_nm']}'    style='width:100%' /></td>".
    	     		"<td colspan='4'><input  class='inputableField' type='text' name='data[CakeTrn][{$head_counter}][delivery_term]'  value='{$cake[$head_index]['CakeTrnView']['delivery_term']}'  style='width:100%' /></td>".
    	     		"<td colspan='13'><input class='inputableField' type='text' name='data[CakeTrn][{$head_counter}][delivery_place]' value='{$cake[$head_index]['CakeTrnView']['delivery_place']}' style='width:100%' /></td>".
    	     		"<td colspan='15'><input class='inputableField' type='text' name='data[CakeTrn][{$head_counter}][note]'           value='{$cake[$head_index]['CakeTrnView']['note']}'           style='width:99%' /></td>".    	     	
    	     	 "</tr>".
    	        "</table>";
             
    	      /* サブテーブル作成 */
              echo "<div id='cake_{$head_counter}_div' >";
                    $sub_id = -1;
                    $sub_counter = 0;
                          
                   for($sub_index=0;$sub_index < count($cake);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $cake[$sub_index]['CakeTrnView']['id']){
    	             	if($sub_id != $cake[$sub_index]['CakeTrnView']['cake_menu_id']){	    	     
    	       	           $sub_id  = $cake[$sub_index]['CakeTrnView']['cake_menu_id'];
    	       	           
    	           echo "<table id='cake_{$head_counter}_{$sub_counter}_table' class='list' >".
                        "<tr>".     	
    	                   "<th colspan='17' width='527'>Menu</th>".
    	     		       "<th colspan='8'  width='248'>Eating Place</th>".
    	    		       "<th colspan='15' width='465'>Other Request</th>".          	       	    
    	                "</tr>".    	      
    	      	        "<tr>".
    	                    "<td colspan='17'><input          type='hidden' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][id]'           value='{$cake[$sub_index]['CakeTrnView']['cake_menu_id']}' />".
    	                                     "{$cake[$sub_index]['CakeTrnView']['menu']}".
    	                    "</td>".
    	     				"<td colspan='8'><input  class='inputableField' type='text'   name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][eating_place]' value='{$cake[$sub_index]['CakeTrnView']['eating_place']}'   style='width:100%' /></td>".
    	     				"<td colspan='15'><input class='inputableField' type='text'   name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][note]'         value='{$cake[$sub_index]['CakeTrnView']['cake_menu_note']}' style='width:99%' /></td>".
    	     	 		"</tr>".
    	      	        "<tr>".     	       	     
    	     				"<th colspan='4'  width='124'>Size</th>".
    	     				"<th colspan='5'  width='155'>Shape</th>".
    	     				"<th colspan='5'  width='155'>Topping</th>".  
    	     				"<th colspan='5'  width='155'>Name Plate</th>".
    	     				"<th colspan='5'  width='155'>Flavor</th>".
    	     				"<th colspan='4'  width='124'>Filling</th>".   
    	     				"<th colspan='4'  width='124'>Frosting</th>".
    	                    "<th colspan='4'  width='124'>Decoration</th>".   
    	     				"<th colspan='4'  width='124'>Flower</th>".
    	    			"</tr>".  
    	      			"<tr>".         	     
    	     				"<td colspan='4'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][size]'       value='{$cake[$sub_index]['CakeTrnView']['size']}'       style='width:100%' /></td>".
    	     				"<td colspan='5'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][shaping]'    value='{$cake[$sub_index]['CakeTrnView']['shaping']}'   style='width:100%' /></td>".
    	     				"<td colspan='5'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][topping]'    value='{$cake[$sub_index]['CakeTrnView']['topping']}'    style='width:100%' /></td>".
    	     				"<td colspan='5'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][name_plate]' value='{$cake[$sub_index]['CakeTrnView']['name_plate']}' style='width:99%' /></td>".
    	            		"<td colspan='5'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][flavor]'     value='{$cake[$sub_index]['CakeTrnView']['flavor']}'     style='width:100%' /></td>".
    	     				"<td colspan='4'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][filling]'    value='{$cake[$sub_index]['CakeTrnView']['filling']}'    style='width:100%' /></td>".
    	     				"<td colspan='4'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][frosting]'   value='{$cake[$sub_index]['CakeTrnView']['frosting']}'   style='width:100%' /></td>".
    	                    "<td colspan='4'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][decoration]' value='{$cake[$sub_index]['CakeTrnView']['decoration']}' style='width:100%' /></td>".
    	                    "<td colspan='4'><input class='inputableField' type='text' name='data[CakeMenuTrn][{$head_counter}][{$sub_counter}][flower]'     value='{$cake[$sub_index]['CakeTrnView']['flower']}'     style='width:100%' /></td>".
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