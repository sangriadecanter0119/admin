<input type="hidden" name="data[Category][id]"  value="<?php echo GC_RECEPTION ?>" />          
                    
         <?php           
           if(count($reception))
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($reception);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $reception[$head_index]['ReceptionTrnView']['id']){
         	       $head_id  = $reception[$head_index]['ReceptionTrnView']['id'];
         	       
         	echo "<table id='reception_{$head_counter}_main_table' class='list' >".
    		       "<tr>".     	   
    	               "<th colspan='10' width='310'>Transportation CPL/From</th>".
    	               "<th colspan='10' width='310'>Transportation CPL/To</th>".
    	               "<th colspan='10' width='310'>Transportation GUEST/From</th>".
    	               "<th colspan='10' width='310'>Transportation GUEST/To</th>".    	                          	     
    	           "</tr>".  
    	           "<tr>".
    	               "<td colspan='10'><input class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][cpl_trans_dep_place]'       value='{$reception[$head_index]['ReceptionTrnView']['cpl_trans_dep_place']}'       style='width:100%' /></td>".
    	               "<td colspan='10'><input class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][cpl_trans_arrival_place]'   value='{$reception[$head_index]['ReceptionTrnView']['cpl_trans_arrival_place']}'   style='width:100%' /></td>".
    	               "<td colspan='10'><input class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][guest_trans_dep_place]'     value='{$reception[$head_index]['ReceptionTrnView']['guest_trans_dep_place']}'     style='width:100%' /></td>".
    	               "<td colspan='10'><input class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][guest_trans_arrival_place]' value='{$reception[$head_index]['ReceptionTrnView']['guest_trans_arrival_place']}' style='width:99%' /></td>".	   	    
    	           "</tr>".      
                 "</table><br />";
         	        
    	    echo  "<input type='hidden' name='data[ReceptionTrn][{$head_counter}][id]' value='{$reception[$head_index]['ReceptionTrnView']['id']}' />". 
                  "<table id='reception_{$head_counter}_table' class='list' >".
    	          "<tr>".     	   
    	              "<th colspan='13' width='403'>Company</th>".
    	     		  "<th colspan='8'  width='248'>Name</th>".
    	              "<th colspan='5'  width='155'>Office No</th>".
    	              "<th colspan='5'  width='155'>Cell Phone No</th>".
    	              "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	          "</tr>".  
    	          "<tr>".         	     
    	              "<td colspan='13'>{$reception[$head_index]['ReceptionTrnView']['vendor_nm']}</td>".
    	              "<td colspan='8'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][attend_nm]' value='{$reception[$head_index]['ReceptionTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	              "<td colspan='5'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][phone_no]'  value='{$reception[$head_index]['ReceptionTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	              "<td colspan='5'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][cell_no]'   value='{$reception[$head_index]['ReceptionTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	              "<td colspan='9'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][email]'     value='{$reception[$head_index]['ReceptionTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	          "</tr>".
    	          "<tr>".     	   
    	              "<th colspan='6'  width='186'>Decoration Staff</th>".
    	              "<th colspan='6'  width='186'>MC</th>".
    	              "<th colspan='7'  width='217'>Toasting Speech Name</th>".
    	              "<th colspan='5'  width='155'>Theme Color</th>".
    	              "<th colspan='6'  width='186'>DK Payment</th>".
    	              "<th colspan='6'  width='186'>FD Payment</th>".    	         	
    	              "<th colspan='4'  width='124'>Glass Count</th>". 	                     	    
    	          "</tr>".  
    	          "<tr>".     	   
    	              "<td colspan='6'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][decoration_staff_nm]'  value='{$reception[$head_index]['ReceptionTrnView']['decoration_staff_nm']}'  style='width:100%' /></td>".
    	              "<td colspan='6'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][mc_nm]'                value='{$reception[$head_index]['ReceptionTrnView']['mc_nm']}'                style='width:100%' /></td>".
    	              "<td colspan='7'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][toasting_speech_nm]'   value='{$reception[$head_index]['ReceptionTrnView']['toasting_speech_nm']}'   style='width:100%' /></td>".  
    	              "<td colspan='5'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][theme_color]'          value='{$reception[$head_index]['ReceptionTrnView']['theme_color']}'          style='width:100%' /></td>".
    	              "<td colspan='6'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][champagne_payment]'    value='{$reception[$head_index]['ReceptionTrnView']['champagne_payment']}'    style='width:100%' /></td>".
    	              "<td colspan='6'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][menu_payment]'         value='{$reception[$head_index]['ReceptionTrnView']['menu_payment']}'         style='width:100%' /></td>". 	  
    	              "<td colspan='4'>".
    	                              "<select class='inputableField'              name='data[ReceptionTrn][{$head_counter}][glass_count]'  style='width:100%' >";
    	     								for($j=0;$j < 100;$j++){
    	     									if($reception[$head_index]['ReceptionTrnView']['glass_count'] == $j){
    	     										echo  "<option value='{$j}' selected>".$j."</option>";
    	     									}else{
    	     	    								echo  "<option value='{$j}'>".$j."</option>";	
    	     									}    	     	
    	     								}
   	                              echo "</select></td>".    	  
    	          "</tr>". 
    	            "<tr>".     	   
    	             "<th colspan='5'  width='155'>Party Program</th>".
    	             "<th colspan='5'  width='155'>BQTToss</th>".
    	             "<th colspan='5'  width='155'>Table Layout</th>".
    	             "<th colspan='5'  width='155'>Bar Type</th>".
    	             "<th colspan='4'  width='124'>High Chair</th>".    	         	
    	             "<th colspan='4'  width='124'>Seating Order</th>".     
    	             "<th colspan='4'  width='124'>Name Card</th>".    
    	             "<th colspan='4'  width='124'>Menu Card</th>".    
    	             "<th colspan='4'  width='124'>Favor</th>".       	    
    	           "</tr>".  
    	          "<tr>".     	   
   	                 "<td colspan='5'>".
   	                       "<select class='inputableField' name='data[ReceptionTrn][{$head_counter}][party_program_kbn]'  style='width:100%' >";
   	                            if($reception[$head_index]['ReceptionTrnView']['party_program_kbn'] == 0){
    	     	                	echo  "<option value='0' selected>NO</option>";
    	     		                echo  "<option value='1' >YES</option>";
    	                        }else{
    	     	                    echo  "<option value='1' selected>YES</option>";
    	     	                    echo  "<option value='0' >NO</option>";	
    	                        }                 
          	         echo  "</select></td>".
          	             	     
    	             "<td colspan='5'>".
    	                   "<select class='inputableField' name='data[ReceptionTrn][{$head_counter}][bouquet_toss_kbn]'  style='width:100%' >";
   	                            if($reception[$head_index]['ReceptionTrnView']['bouquet_toss_kbn'] == 0){
    	     		               echo  "<option value='0' selected>NO</option>";
    	     		               echo  "<option value='1' >YES</option>";
    	                        }else{
    	     	                   echo  "<option value='1' selected>YES</option>";
    	     	                   echo  "<option value='0' >NO</option>";	
    	                        }                 
            	     echo  "</select></td>".
    	
    	            "<td colspan='5'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][table_layout]' value='{$reception[$head_index]['ReceptionTrnView']['table_layout']}'  style='width:100%' /></td>".
    	            "<td colspan='5'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][bar_type]'     value='{$reception[$head_index]['ReceptionTrnView']['bar_type']}'  style='width:100%' /></td>".    	         	    	     
    	    	    "<td colspan='4'>".
    	                    "<select class='inputableField' name='data[ReceptionTrn][{$head_counter}][high_chair]'  style='width:100%' >";
    	     					for($j=0;$j < 100;$j++){
    	     						if($reception[$head_index]['ReceptionTrnView']['high_chair'] == $j){
    	     							echo  "<option value='{$j}' selected>".$j."</option>";
    	     						}else{
    	     	    					echo  "<option value='{$j}'>".$j."</option>";	
    	     						}    	     	
    	     					}
   	               echo "</select></td>".    	  
    	            "<td colspan='4'>".
    	    	           "<select class='inputableField' name='data[ReceptionTrn][{$head_counter}][seating_order_kbn]'  style='width:100%' >";
   	                           if($reception[$head_index]['ReceptionTrnView']['seating_order_kbn'] == 0){
    	     	             	  echo  "<option value='0' selected>NO</option>";
    	     		              echo  "<option value='1' >YES</option>";	
    	                       }else{
    	     	                  echo  "<option value='1' selected>YES</option>";
    	     	                  echo  "<option value='0' >NO</option>";		
    	                       }                 
     	            echo  "</select></td>".
    	     
    	           "<td colspan='4'>".
    	                   "<select class='inputableField' name='data[ReceptionTrn][{$head_counter}][name_card_kbn]'  style='width:100%' >";
   	                           if($reception[$head_index]['ReceptionTrnView']['name_card_kbn'] == 0){
    	     	         	      echo  "<option value='0' selected>NO</option>";
    	     		              echo  "<option value='1' >YES</option>";	
    	                       }else{
    	     	                  echo  "<option value='1' selected>YES</option>";
    	     	                  echo  "<option value='0' >NO</option>";		
    	                       }                 
    	            echo  "</select></td>".
    	
    	           "<td colspan='4'>".
    	                  "<select class='inputableField' name='data[ReceptionTrn][{$head_counter}][menu_card_kbn]'  style='width:100%' >";
   	                          if($reception[$head_index]['ReceptionTrnView']['menu_card_kbn'] == 0){
    	     		             echo  "<option value='0' selected>NO</option>";
    	     		             echo  "<option value='1' >YES</option>";	
    	                      }else{
    	     	                 echo  "<option value='1' selected>YES</option>";
    	     	                 echo  "<option value='0' >NO</option>";		
    	                     }                 
                   echo  "</select></td>".    	         	     
    	     
    	          "<td colspan='4'><input  class='inputableField'  type='text' name='data[ReceptionTrn][{$head_counter}][favor]' value='{$reception[$head_index]['ReceptionTrnView']['favor']}'  style='width:99%' /></td>".    	   
    	        "</tr>". 
    	        "<tr>".     	   
                  "<th colspan='7'  width='217'>Allergie</th>". 
    	          "<th colspan='33' width='1023'>Other Request(RW)</th>".    	           	       	    
    	        "</tr>".  
    	        "<tr>".   
                  "<td colspan='7'><input   class='inputableField' type='text' name='data[ReceptionTrn][{$head_counter}][allergie]' value='{$reception[$head_index]['ReceptionTrnView']['allergie']}'  style='width:100%' /></td>".   	   
    	          "<td colspan='33'><input  class='inputableField' type='text' name='data[ReceptionTrn][{$head_counter}][note]' value='{$reception[$head_index]['ReceptionTrnView']['note']}'  style='width:99%' /></td>". 	       	    
    	        "</tr>".   
               "</table>";
           	  
               /* サブテーブル作成 */
              echo "<div id='reception_{$head_counter}_div' >";
                   $sub_id = -1;
                   $sub_counter = 0;
              
              echo "<table id='reception_{$head_counter}_{$sub_counter}_table' class='list' >".
                   "<tr>".     	       	         
    	     	      "<th colspan='20' width='610'>Menu</th>".
    	              "<th colspan='3'  width='93'>Count</th>".
    	              "<th colspan='17' width='527'>Other Request</th>".    	         	       	    
    	           "</tr>";
              
                   for($sub_index=0;$sub_index < count($reception);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $reception[$sub_index]['ReceptionTrnView']['id']){
    	             	if($sub_id != $reception[$sub_index]['ReceptionTrnView']['reception_menu_id']){	    	     
    	       	           $sub_id  = $reception[$sub_index]['ReceptionTrnView']['reception_menu_id'];
        
                       echo "<tr>".
                              "<td colspan='20'>".
                                  "<input type='hidden' name='data[ReceptionMenuTrn][{$head_counter}][{$sub_counter}][id]' value='{$reception[$sub_index]['ReceptionTrnView']['reception_menu_id']}' />".
    	             	          "{$reception[$sub_index]['ReceptionTrnView']['menu']}".
           	                 "</td>".
    	                     "<td colspan='3'>{$reception[$sub_index]['ReceptionTrnView']['num']}</td>". 
    	                    "<td colspan='17'><input class='inputableField'  type='text' name='data[ReceptionMenuTrn][{$head_counter}][{$sub_counter}][note]' value='{$reception[$sub_index]['ReceptionTrnView']['reception_menu_note']}' style='width:100%' /></td>". 
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