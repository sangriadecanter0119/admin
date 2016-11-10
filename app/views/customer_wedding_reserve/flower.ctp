<input type="hidden" name="data[Category][id]"  value="<?php echo GC_FLOWER ?>" />          
                    
         <?php           
           if(count($flower))
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($flower);$head_index++){
           	
         	    /* ヘッダ作成 */
         	 	if($head_id != $flower[$head_index]['FlowerTrnView']['id']){
         	       $head_id  = $flower[$head_index]['FlowerTrnView']['id'];
         	       
    	    echo  "<input type='hidden' name='data[FlowerTrn][{$head_counter}][id]' value='{$flower[$head_index]['FlowerTrnView']['id']}' />". 
                  "<table id='flower_{$head_counter}_table' class='list' >".
    	            "<tr>".     	   
    	     			"<th colspan='13' width='403'>Company</th>".
    	     			"<th colspan='8'  width='248'>Name</th>".
    	     			"<th colspan='5'  width='155'>Office No</th>".
    	     			"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    		"</tr>".  
    	    		"<tr>".         	     
    	     			"<td colspan='13'>{$flower[$head_index]['FlowerTrnView']['vendor_nm']}</td>".
    	     			"<td colspan='8'><input  class='inputableField'  type='text' name='data[FlowerTrn][{$head_counter}][attend_nm]' value='{$flower[$head_index]['FlowerTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			"<td colspan='5'><input  class='inputableField'  type='text' name='data[FlowerTrn][{$head_counter}][phone_no]'  value='{$flower[$head_index]['FlowerTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			"<td colspan='5'><input  class='inputableField'  type='text' name='data[FlowerTrn][{$head_counter}][cell_no]'   value='{$flower[$head_index]['FlowerTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			"<td colspan='9'><input  class='inputableField'  type='text' name='data[FlowerTrn][{$head_counter}][email]'     value='{$flower[$head_index]['FlowerTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	    		"</tr>";        
    	     
    	           //Florist Main                        
                   $sub_id = -1;
                   $sub_counter = 0;
              
                   for($sub_index=0;$sub_index < count($flower);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値   
    	             if($head_id == $flower[$sub_index]['FlowerTrnView']['id']){
    	                //フラワー区分がMAIN	   	             
    	             	if($sub_id != $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'] && $flower[$sub_index]['FlowerTrnView']['flower_kbn'] == FC_MAIN){	    	     
    	       	           $sub_id  = $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'];
    	       	    
    	       	           //初回のみヘッダ出力
    	       	           if($sub_counter == 0){
    	       	            echo "<tr>".  	   
    	     					    "<th colspan='7'  width='217'>Florist Name(Main)</th>".
    	     						"<th colspan='6'  width='186'>Delivery Time</th>".
    	     						"<th colspan='13' width='403'>Delivery Place</th>".
    	     						"<th colspan='14' width='434'>Other Request(RW)</th>".    	    	         	       	    
    	    	    			"</tr>". 
    	    	    			"<tr>".  	   
    	     						"<td colspan='7'><input  class='inputableField'           type='text' name='data[FlowerTrn][{$head_counter}][main_florist_nm]'     value='{$flower[$head_index]['FlowerTrnView']['main_florist_nm']}'     style='width:100%' /></td>".
    	     						"<td colspan='6'><input  class='time_mask inputableField' type='text' name='data[FlowerTrn][{$head_counter}][main_delivery_term]'  value='{$flower[$head_index]['FlowerTrnView']['main_delivery_term']}'  style='width:100%' /></td>".
    	     						"<td colspan='13'><input class='inputableField'           type='text' name='data[FlowerTrn][{$head_counter}][main_delivery_place]' value='{$flower[$head_index]['FlowerTrnView']['main_delivery_place']}' style='width:100%' /></td>".
    	     						"<td colspan='14'><input class='inputableField'            type='text' name='data[FlowerTrn][{$head_counter}][main_note]'           value='{$flower[$head_index]['FlowerTrnView']['main_note']}'           style='width:100%' /></td>".    	       	   	    
    	   		    			"</tr>".   
    	    	    			"<tr>".    	       	     			    
    	     			      		"<th colspan='15' width='465'>Content</th>".
    	     			      		"<th colspan='9'  width='279'>Type</th>".
    	     			      		"<th colspan='4'  width='124'>Count</th>".    	
    	     			      		"<th colspan='12' width='372'>Other Request</th>". 
    	     	            	"</tr>"; 	       	           	
    	       	           }
    	       	           
                           echo "<tr>".
                     	            "<td colspan='15'><input  class=''  type='hidden' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][id]'          value='{$flower[$sub_index]['FlowerTrnView']['flower_dtl_id']}'  style='width:100%' />".
                     	                            "{$flower[$sub_index]['FlowerTrnView']['flower_content']}</td>".
    	                            "<td colspan='9'><input  class='inputableField'  type='text' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][flower_type]' value='{$flower[$sub_index]['FlowerTrnView']['flower_type']}'  style='width:100%' /></td>".
    	                            "<td colspan='4'>{$flower[$sub_index]['FlowerTrnView']['num']}</td>". 
    	                           "<td colspan='12'><input class='inputableField'  type='text' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][note]' value='{$flower[$sub_index]['FlowerTrnView']['flower_dtl_note']}' style='width:100%' /></td>".    
    	                       "</tr>";
    	                  $sub_counter++;
                      }     
                    }
                  }
   
                   //Florist Ceremony              
                   $sub_id = -1;
                   $tmp_sub_counter = $sub_counter;
              
                   for($sub_index=0;$sub_index < count($flower);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値   
    	             if($head_id == $flower[$sub_index]['FlowerTrnView']['id']){
    	                //フラワー区分がCEREMONY   	             
    	             	if($sub_id != $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'] && $flower[$sub_index]['FlowerTrnView']['flower_kbn'] == FC_CEREMONY){	    	     
    	       	           $sub_id  = $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'];
    	       	           
    	             	   //初回のみヘッダ出力
    	       	           if($sub_counter == $tmp_sub_counter){
    	       	              echo  "<tr>".  	   
    	     							"<th colspan='7'  width='217'>Florist Name(Ceremony)</th>".
    	     							"<th colspan='6'  width='186'>Delivery Time</th>".
    	     							"<th colspan='13' width='403'>Delivery Place</th>".
    	     							"<th colspan='14' width='434'>Other Request(RW)</th>".    	    	         	       	    
    	    	    				"</tr>". 
         	 	   					"<tr>".  	   
    	     							"<td colspan='7'><input  class='inputableField'           type='text' name='data[FlowerTrn][{$head_counter}][ceremony_florist_nm]'     value='{$flower[$head_index]['FlowerTrnView']['ceremony_florist_nm']}'     style='width:100%' /></td>".
    	     							"<td colspan='6'><input  class='time_mask inputableField' type='text' name='data[FlowerTrn][{$head_counter}][ceremony_delivery_term]'  value='{$flower[$head_index]['FlowerTrnView']['ceremony_delivery_term']}'  style='width:100%' /></td>".
    	     							"<td colspan='13'><input class='inputableField'           type='text' name='data[FlowerTrn][{$head_counter}][ceremony_delivery_place]' value='{$flower[$head_index]['FlowerTrnView']['ceremony_delivery_place']}' style='width:100%' /></td>".
    	     							"<td colspan='14'><input class='inputableField'           type='text' name='data[FlowerTrn][{$head_counter}][ceremony_note]'           value='{$flower[$head_index]['FlowerTrnView']['ceremony_note']}'           style='width:100%' /></td>".    	       	   	    
    	   		   					"</tr>".   
    	    	   					"<tr>".    	       	     			    
    	     							"<th colspan='15' width='465'>Content</th>".
    	     							"<th colspan='9'  width='279'>Type</th>".
    	     							"<th colspan='4'  width='124'>Count</th>".    	
    	     							"<th colspan='12' width='372'>Other Request</th>". 
    	     	  					"</tr>";                 
    	       	           }
    	       	    
                           echo "<tr>".
                     	            "<td colspan='15'><input  class=''  type='hidden' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][id]'       value='{$flower[$sub_index]['FlowerTrnView']['flower_dtl_id']}'  style='width:100%' />".
                     	                            "{$flower[$sub_index]['FlowerTrnView']['flower_content']}</td>".
    	                            "<td colspan='9'><input  class='inputableField'  type='text' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][flower_type]' value='{$flower[$sub_index]['FlowerTrnView']['flower_type']}'  style='width:100%' /></td>".
    	                            "<td colspan='4'>{$flower[$sub_index]['FlowerTrnView']['num']}</td>". 
    	                           "<td colspan='12'><input class='inputableField'  type='text' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][note]' value='{$flower[$sub_index]['FlowerTrnView']['flower_dtl_note']}' style='width:100%' /></td>".    
    	                       "</tr>";
    	                  $sub_counter++;
                      }     
                    }
                  }
                  
                   //Florist Reception
                   $sub_id = -1;
                   $tmp_sub_counter = $sub_counter;
                                 
                   for($sub_index=0;$sub_index < count($flower);$sub_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値   
    	             if($head_id == $flower[$sub_index]['FlowerTrnView']['id']){
    	                //フラワー区分がRECEPTION 	             
    	             	if($sub_id != $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'] && $flower[$sub_index]['FlowerTrnView']['flower_kbn'] == FC_RECEPTION){	    	     
    	       	           $sub_id  = $flower[$sub_index]['FlowerTrnView']['flower_dtl_id'];
    	       	           
    	             	//初回のみヘッダ出力
    	       	           if($sub_counter == $tmp_sub_counter){
    	       	              echo  "<tr>".  	   
    	     							"<th colspan='7'  width='217'>Florist Name(Reception)</th>".
    	     							"<th colspan='6'  width='186'>Delivery Time</th>".
    	     							"<th colspan='13' width='403'>Delivery Place</th>".
    	     							"<th colspan='14' width='434'>Other Request(RW)</th>".    	    	         	       	    
    	    	  					"</tr>". 
         	   	  					"<tr>".  	   
    	     							"<td colspan='7'><input  class='inputableField'          type='text' name='data[FlowerTrn][{$head_counter}][reception_florist_nm]'     value='{$flower[$head_index]['FlowerTrnView']['reception_florist_nm']}'     style='width:100%' /></td>".
    	     							"<td colspan='6'><input  class='time_maskinputableField' type='text' name='data[FlowerTrn][{$head_counter}][reception_delivery_term]'  value='{$flower[$head_index]['FlowerTrnView']['reception_delivery_term']}'  style='width:100%' /></td>".
    	     							"<td colspan='13'><input class='inputableField'          type='text' name='data[FlowerTrn][{$head_counter}][reception_delivery_place]' value='{$flower[$head_index]['FlowerTrnView']['reception_delivery_place']}' style='width:100%' /></td>".
    	     							"<td colspan='14'><input class='inputableField'          type='text' name='data[FlowerTrn][{$head_counter}][reception_note]'           value='{$flower[$head_index]['FlowerTrnView']['reception_note']}'           style='width:100%' /></td>".    	       	   	    
    	   		  					"</tr>".   
    	    	  					"<tr>".    	       	     			    
    	     							"<th colspan='15' width='465'>Content</th>".
    	     							"<th colspan='9'  width='279'>Type</th>".
    	     							"<th colspan='4'  width='124'>Count</th>".    	
    	     							"<th colspan='12' width='372'>Other Request</th>". 
    	     	  					"</tr>";                 
    	       	           }
    	       	    
                           echo "<tr>".
                     	            "<td colspan='15'><input  class='inputableField'  type='hidden' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][id]'      value='{$flower[$sub_index]['FlowerTrnView']['flower_dtl_id']}'  style='width:100%' />".
                     	                            "{$flower[$sub_index]['FlowerTrnView']['flower_content']}</td>".
    	                            "<td colspan='9'><input  class='inputableField'  type='text' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][flower_type]' value='{$flower[$sub_index]['FlowerTrnView']['flower_type']}'  style='width:100%' /></td>".
    	                            "<td colspan='4'>{$flower[$sub_index]['FlowerTrnView']['num']}</td>". 
    	                           "<td colspan='12'><input class='inputableField'  type='text' name='data[FlowerDtlTrn][{$head_counter}][{$sub_counter}][note]' value='{$flower[$sub_index]['FlowerTrnView']['flower_dtl_note']}' style='width:100%' /></td>".    
    	                       "</tr>";
    	                  $sub_counter++;
                      }     
                    }
                  }
                  $head_counter++;
                  echo "</table>".
                       "<br /><br />";
         	 	}
         	 }
           }
        ?>        