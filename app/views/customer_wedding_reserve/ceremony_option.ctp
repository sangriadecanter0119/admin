 <input type="hidden" name="data[Category][id]" value="<?php echo GC_CEREMONY_OPTION ?>" />          
                    
         <?php           
         if(count($ceremony_option) > 0)
         {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 
         	 for($head_index=0;$head_index < count($ceremony_option);$head_index++){
           	
         	    /* メインテーブル作成 */
         	 	if($head_id != $ceremony_option[$head_index]['CeremonyOptionTrnView']['id']){
         		   $head_id  = $ceremony_option[$head_index]['CeremonyOptionTrnView']['id'];
         		   
             echo  "<input type='hidden' name='data[CeremonyOptionTrn][{$head_counter}][id]' value='{$ceremony_option[$head_index]['CeremonyOptionTrnView']['id']}' />". 
                      "<table id='CeremonyOption_{$head_counter}_table' class='list' >".
    	              "<tr>".     	   
    	                "<th colspan='13' width='403'>Company</th>".
    	                "<th colspan='8'  width='248'>Name</th>".
    	                "<th colspan='5'  width='155'>Office No</th>".
    	                "<th colspan='5'  width='155'>Cell Phone No</th>".
    	                "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	              "</tr>".  
    	              "<tr>".         	     
    	                "<td colspan='13'>{$ceremony_option[$head_index]['CeremonyOptionTrnView']['vendor_nm']}</td>".
    	                "<td colspan='8'><input  class='inputableField'  type='text' name='data[CeremonyOptionTrn][{$head_counter}][attend_nm]' value='{$ceremony_option[$head_index]['CeremonyOptionTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[CeremonyOptionTrn][{$head_counter}][phone_no]'  value='{$ceremony_option[$head_index]['CeremonyOptionTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	                "<td colspan='5'><input  class='inputableField'  type='text' name='data[CeremonyOptionTrn][{$head_counter}][cell_no]'   value='{$ceremony_option[$head_index]['CeremonyOptionTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	                "<td colspan='9'><input  class='inputableField'  type='text' name='data[CeremonyOptionTrn][{$head_counter}][email]'     value='{$ceremony_option[$head_index]['CeremonyOptionTrnView']['vendor_email']}'     style='width:99%' /></td>".  
    	              "</tr>".
                      "</table>";

                       /* サブテーブル作成 */
                 echo "<div id='CeremonyOption_{$head_counter}_div' >";
                   
                       $sub_id = -1;
                       $sub_counter = 0;
            
                   echo "<table id='CeremonyOption_{$head_counter}_{$sub_counter}_table' class='list' >".
    	                 "<tr>".   
    	                    "<th colspan='15' width='465'>Ceremony Option Menu</th>".    
    	                    "<th colspan='4'  width='124'>Count</th>".
    	                    "<th colspan='17' width='527'>Other Request</th>".         	           	       	    
    	   	            "</tr>";

                        for($sub_index=0;$sub_index < count($ceremony_option);$sub_index++){
                   
                          //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	                  if($head_id == $ceremony_option[$sub_index]['CeremonyOptionTrnView']['id']){
    	             	
    	             	      if($sub_id != $ceremony_option[$sub_index]['CeremonyOptionTrnView']['ceremony_option_dtl_id']){	    	     
    	       	                 $sub_id  = $ceremony_option[$sub_index]['CeremonyOptionTrnView']['ceremony_option_dtl_id'];   
                                       
                                  echo "<tr>".
                                          "<td colspan='15'>".
                                             "<input type='hidden' name='data[CeremonyOptionDtlTrn][{$head_counter}][{$sub_counter}][id]' value='{$ceremony_option[$sub_index]['CeremonyOptionTrnView']['ceremony_option_dtl_id']}' />".
                                             "{$ceremony_option[$sub_index]['CeremonyOptionTrnView']['ceremony_option_nm']}".
    	                                  "<td colspan='4'>{$ceremony_option[$sub_index]['CeremonyOptionTrnView']['ceremony_option_count']}</td>".   
    	                                 "<td colspan='17'><input class='inputableField' type='text' name='data[CeremonyOptionDtlTrn][{$head_counter}][{$sub_counter}][note]'  value='{$ceremony_option[$sub_index]['CeremonyOptionTrnView']['ceremony_option_note']}'  style='width:99%' /></td>".     
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