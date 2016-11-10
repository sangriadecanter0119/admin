 <input type="hidden" name="data[Category][id]"  value="<?php echo GC_PHOTO ?>" /> 
    	 <?php 
         if(count($photographer) > 0)
         {
         	$head_id = -1;
         	$head_counter = 0;
         	for($head_index=0;$head_index < count($photographer);$head_index++){
         		 /* 
         		  *  メインテーブルの作成 
         		  */
         		 if($head_id != $photographer[$head_index]['PhotographerMenuTrnView']['id']){
         		 	$head_id  = $photographer[$head_index]['PhotographerMenuTrnView']['id'];
         		 	
         	    echo  "<input type='hidden' name='data[PhotographerTrn][{$head_counter}][id]' value='{$photographer[$head_index]['PhotographerMenuTrnView']['id']}' />". 
                      "<table id='photographer_{$head_counter}_0_table' class='list' >".
    	              "<tr>".     	   
    	     			"<th colspan='13' width='403'>Company</th>".
    	     			"<th colspan='8'  width='248'>Name</th>".
    	     			"<th colspan='5'  width='155'>Office No</th>".
    	     			"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    		  "</tr>".  
    	    		  "<tr>".   
    	     			"<td colspan='13'>{$photographer[$head_index]['PhotographerMenuTrnView']['vendor_nm']}</td>".
    	     			"<td colspan='8'> <input  class='inputableField'  type='text' name='data[PhotographerTrn][{$head_counter}][attend_nm]' value='{$photographer[$head_index]['PhotographerMenuTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			"<td colspan='5'> <input  class='inputableField'  type='text' name='data[PhotographerTrn][{$head_counter}][phone_no]'  value='{$photographer[$head_index]['PhotographerMenuTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			"<td colspan='5'> <input  class='inputableField'  type='text' name='data[PhotographerTrn][{$head_counter}][cell_no]'   value='{$photographer[$head_index]['PhotographerMenuTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			"<td colspan='9'> <input  class='inputableField'  type='text' name='data[PhotographerTrn][{$head_counter}][email]'     value='{$photographer[$head_index]['PhotographerMenuTrnView']['vendor_email']}'     style='width:99%'  /></td>".    	   
    	    		  "</tr>".      	    	  
         	          "<tr>".       	   
         	            "<th colspan='4'  width='124'>First Meet</th>". 
    	     			"<th colspan='9'  width='279'>First Meet Place</th>". 
    	     			"<th colspan='8'  width='248'>Delivery Term</th>".  	     		
    	     			"<th colspan='10' width='310'>Delivery Place</th>".  
    	     			"<th colspan='9'  width='279'>Recieve Person</th>".     	         	       	    
    	    		  "</tr>".    
    	              "<tr>".      	  
         	            "<td colspan='4'>".
         	               "<select class='inputableField' name='data[PhotographerTrn][{$head_counter}][first_meeting]' style='width:100%'>";
         	                  if($photographer[$head_index]['PhotographerMenuTrnView']['first_meeting'] == 0){
         	                  	  echo "<option value='0' selected>NO</option>".
         	                  	       "<option value='1' >YES</option>";
         	                  }else{
         	                  	  echo "<option value='1' selected>YES</option>".
         	                  	       "<option value='0' >NO</option>";
         	                  }
         	       echo    "</select>".
         	            "</td>". 
         	 			"<td colspan='9'><input class='inputableField'   type='text' name='data[PhotographerTrn][{$head_counter}][first_meeting_place]' value='{$photographer[$head_index]['PhotographerMenuTrnView']['first_meeting_place']}' style='width:100%' /></td>".
    	     			"<td colspan='8'><input class='inputableField'   type='text' name='data[PhotographerTrn][{$head_counter}][delivery_term]'       value='{$photographer[$head_index]['PhotographerMenuTrnView']['delivery_term']}' style='width:100%' /></td>".
    	     			"<td colspan='10'><input class='inputableField'  type='text' name='data[PhotographerTrn][{$head_counter}][delivery_place]'      value='{$photographer[$head_index]['PhotographerMenuTrnView']['delivery_place']}' style='width:100%' /></td>".
    	     			"<td colspan='9'><input class='inputableField'   type='text' name='data[PhotographerTrn][{$head_counter}][reciever_nm]'         value='{$photographer[$head_index]['PhotographerMenuTrnView']['reciever_nm']}'    style='width:99%'  /></td>".
    	    		 "</tr>".
         	         "<tr>".         	     
    	                "<th colspan='6'  width='186'>Working Start Time</th>".
    	                "<th colspan='6'  width='186'>Working End Time</th>".  
    	                "<th colspan='4'  width='124'>Working Total</th>".   
    	                "<th colspan='24' width='744'>Other Request(RW)</th>".          	       	    
    	             "</tr>". 
         	         "<tr>".                                       
    	     		     "<td colspan='6'><input  id='photographer{$head_counter}_start_time' class='time_mask work_time inputableField' type='text' name='data[PhotographerTrn][{$head_counter}][working_start_time]' value='{$photographer[$head_index]['PhotographerMenuTrnView']['working_start_time']}'    style='width:100%' /></td>".
    	     			 "<td colspan='6'><input  id='photographer{$head_counter}_end_time'   class='time_mask work_time inputableField' type='text' name='data[PhotographerTrn][{$head_counter}][working_end_time]'   value='{$photographer[$head_index]['PhotographerMenuTrnView']['working_end_time']}'      style='width:100%' /></td>".
    	     			 "<td colspan='4'><input  id='photographer{$head_counter}_total_time' class=''                    type='text' name='data[PhotographerTrn][{$head_counter}][working_total_time]' value='{$photographer[$head_index]['PhotographerMenuTrnView']['fm_working_total_time']}' style='width:100%' readonly /></td>".
    	     			 "<td colspan='24'><input                                             class='inputableField'                     type='text' name='data[PhotographerTrn][{$head_counter}][note]'               value='{$photographer[$head_index]['PhotographerMenuTrnView']['note']}' style='width:99%'  /></td>".  
            		 "</tr>".
         	         "<tr>".     	   
    	                 "<th colspan='20' width='620'>Menu</th>".
    	                 "<th colspan='20' width='620'>Other Request</th>".
    	             "</tr>";
    	             /* メニューテーブル作成 */
         	         $menu_id = -1;
         	         $menu_counter = 0;
         	         for($menu_index=0;$menu_index < count($photographer);$menu_index++){
                   
                      //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	              if($head_id == $photographer[$menu_index]['PhotographerMenuTrnView']['id']){
    	              	if($menu_id != $photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_id']){	    	     
    	       	           $menu_id  = $photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_id'];
    	       	           
    	       	           echo "<tr>".   
    	                          "<td colspan='20'><input  class=''  type='hidden' name='data[PhotographerMenuTrn][{$head_counter}][{$menu_counter}][id]' value='{$photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_id']}' style='width:100%' />".
    	                                            "{$photographer[$menu_index]['PhotographerMenuTrnView']['menu']}</td>".
    	                          "<td colspan='20'><input  class='inputableField'  type='text' name='data[PhotographerMenuTrn][{$head_counter}][{$menu_counter}][note]' value='{$photographer[$menu_index]['PhotographerMenuTrnView']['photographer_menu_note']}' style='width:100%' /></td>".
    	                        "</tr>";    	 
    	       	            $menu_counter++;      	           
    	             	}
    	              }
                   }              
         	  
                   /* 時間テーブル作成 */
                   $time_id = -1;
                   $time_counter = 0;
             
                echo "<tr>".	     	       
                       "<th colspan='4'  width='124'><a href='#' class='add rowUnit' name='photographer_".GC_PHOTO."_{$head_counter}_0_{$time_counter}' style='width:100%'>Add Time</a></th>".  
    	     		   "<th colspan='2'  width='62'>No</th>".                       
    	     		   "<th colspan='4'  width='124'>Shooting Time</th>".    	   
    	     		   "<th colspan='10' width='310'>Shooting Place</th>".        	     		 
    	     		   "<th colspan='20' width='620'>Other Request</th>".           	       	    
    	           "</tr>";
              
                   for($time_index=0;$time_index < count($photographer_time);$time_index++){
                   
                    //サブテーブルの外部キーとヘッダの主キーが同値 	  
    	             if($head_id == $photographer_time[$time_index]['PhotographerTimeTrnView']['id']){
    	             	if($time_id != $photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_id']){	    	     
    	       	           $time_id  = $photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_id'];
            
    	       	          echo  "<tr id='photographer_{$head_counter}_0_{$time_counter}_row'>".    	             
    	                           "<td colspan='4'><input type='hidden' name='data[PhotographerTimeTrn][".$head_counter."][".$time_counter."][id]' value='{$photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_id']}' />";
                                   //１行目は削除負荷とする
    	         					if($time_counter == 0){
                    					echo  "<a href='#' class='delete rowUnit' name='photographer_".GC_PHOTO."_".$head_counter."_0_".$time_counter."'  style='width:100%' disabled>&nbsp;</a></td>";	
                 					}else{
                 						echo  "<a href='#' class='delete rowUnit' name='photographer_".GC_PHOTO."_".$head_counter."_0_".$time_counter."'  style='width:100%' >delete</a></td>";
                 					}    	        
    	         			 echo "<td colspan='2'>".
    	         			        "<select name='data[PhotographerTimeTrn][".$head_counter."][".$time_counter."][no]' style='width:100%'>";	      
    	      					       for($k=1;$k < 21;$k++)
    	      					       {
    	     						     if($photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_no'] == $k){
    	     	  						   echo "<option value='$k' selected>$k</option>";	
    	     						     }else{
    	          						   echo "<option value='$k' >$k</option>";	
    	  						         }
    	     					       }    	     
    	                        echo "</select></td>".
    	                          "<td colspan='4'><input class='time_mask inputableField'  type='text' name='data[PhotographerTimeTrn][".$head_counter."][".$time_counter."][shooting_time]'  value='{$photographer_time[$time_index]['PhotographerTimeTrnView']['shooting_time']}'             style='width:100%' /></td>".  	  
    	         				  "<td colspan='10'><input class='inputableField'           type='text' name='data[PhotographerTimeTrn][".$head_counter."][".$time_counter."][shooting_place]' value='{$photographer_time[$time_index]['PhotographerTimeTrnView']['shooting_place']}'            style='width:100%' /></td>".    	  
    	         				  "<td colspan='20'><input class='inputableField'           type='text' name='data[PhotographerTimeTrn][".$head_counter."][".$time_counter."][note]'           value='{$photographer_time[$time_index]['PhotographerTimeTrnView']['photographer_time_note']}' style='width:99%' /></td>".  	    
    	       			        "</tr>";
    	                    $time_counter++;
                       }            	
                     }
                  }                  
       echo "</table>";
       echo "<br /><br />";
       $head_counter++;
          }
        }   
    }
?>    	