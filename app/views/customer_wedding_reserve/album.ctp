 <input type="hidden" name="data[Category][id]"  value="<?php echo GC_ALBUM ?>" /> 
    	 <?php 
         if(count($album) > 0)
         {
         	$head_id = -1;
         	$head_counter = 0;
         	for($head_index=0;$head_index < count($album);$head_index++){
         		 
         		 if($head_id != $album[$head_index]['AlbumTrnView']['id']){
         		 	$head_id  = $album[$head_index]['AlbumTrnView']['id'];
         		 	
         	    echo  "<input type='hidden' name='data[AlbumTrn][{$head_counter}][id]' value='{$album[$head_index]['AlbumTrnView']['id']}' />". 
                      "<table id='album_{$head_counter}_0_table' class='list' >".
    	              "<tr>".     	   
    	     			"<th colspan='13' width='403'>Company</th>".
    	     			"<th colspan='8'  width='248'>Name</th>".
    	     			"<th colspan='5'  width='155'>Office No</th>".
    	     			"<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			"<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	    		  "</tr>".  
    	    		  "<tr>".   
    	     			"<td colspan='13'>{$album[$head_index]['AlbumTrnView']['vendor_nm']}</td>".
    	     			"<td colspan='8'> <input  class='inputableField'  type='text' name='data[AlbumTrn][{$head_counter}][attend_nm]' value='{$album[$head_index]['AlbumTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			"<td colspan='5'> <input  class='inputableField'  type='text' name='data[AlbumTrn][{$head_counter}][phone_no]'  value='{$album[$head_index]['AlbumTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			"<td colspan='5'> <input  class='inputableField'  type='text' name='data[AlbumTrn][{$head_counter}][cell_no]'   value='{$album[$head_index]['AlbumTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			"<td colspan='9'> <input  class='inputableField'  type='text' name='data[AlbumTrn][{$head_counter}][email]'     value='{$album[$head_index]['AlbumTrnView']['vendor_email']}'     style='width:99%'  /></td>".    	   
    	    		  "</tr>".
         	          "<tr>".      	        	             
         	            "<th colspan='10' width='310'>Type</th>".
    	       			"<th colspan='6'  width='186'>Album Delivery Term</th>".
    	       			"<th colspan='24' width='704'>Other Request</th>".    	    	         	       	    
    	    	   	  "</tr>";
         	       
         	       $dtl_id = -1;
                   $dtl_counter =0;
           		   for($dtl_index=0;$dtl_index < count($album);$dtl_index++){      	 
    	      		   //ヘッダテーブルの外部キーとサブテーブルの主キーが同値 
           	  	       if($head_id == $album[$dtl_index]['AlbumTrnView']['id']){
                 	  	 if($dtl_id != $album[$dtl_index]['AlbumTrnView']['album_dtl_id']){
                      	    $dtl_id  = $album[$dtl_index]['AlbumTrnView']['album_dtl_id'];     
    	    
    	    		       echo "<tr id='album_{$head_counter}_0_{$dtl_counter}_row'>".  
    	       			 			"<td colspan='10'><input  class='inputableField'  type='hidden' name='data[AlbumDtlTrn][{$head_counter}][{$dtl_counter}][id]'  value='{$album[$dtl_index]['AlbumTrnView']['album_dtl_id']}'/>".
    	    		                                 "{$album[$dtl_index]['AlbumTrnView']['type']}</td>".
    	       			 			"<td colspan='6'> <input  class='inputableField'  type='text' name='data[AlbumDtlTrn][{$head_counter}][{$dtl_counter}][delivery_term]'  value='{$album[$dtl_index]['AlbumTrnView']['delivery_term']}' style='width:100%'  /></td>".
    	  			     			"<td colspan='24'><input  class='inputableField'  type='text' name='data[AlbumDtlTrn][{$head_counter}][{$dtl_counter}][note]'           value='{$album[$dtl_index]['AlbumTrnView']['album_note']}'          style='width:99%'  /> </td>".	    	  	   	    
    	    		  			"</tr>";
    	    		       $dtl_counter++;
                 	  	 }
           	  	       }
           		    } 
           	  echo "<tr>".
           	           "<th colspan='40' width='1240'>Other Request(RW)</th>".
           	       "</th>".
           	       "<tr>".
           	           "<td colspan='40'><input class='inputableField' type='text' name='data[AlbumTrn][{$head_counter}][note]'  value='{$album[$head_index]['AlbumTrnView']['note']}'  style='width:99%' /></td>".
           	       "<tr>";
           		   $head_counter++;
         	  echo "</table>".
         	       "<br /><br />";
         		}
         	}
         }
   	 ?>