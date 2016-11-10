 <input type="hidden" name="data[Category][id]" value="<?php echo GC_WEDDING ?>" />          
                    
         <?php           
           if(count($ceremony) > 0)
           {
     echo  "<input type='hidden' name='data[CeremonyTrn][id]' value='{$ceremony[0]['CeremonyTrn']['id']}' />". 
           "<table id='CeremonyHeader_table' class='list' >".
    	     "<tr>".     	   
    	       "<th colspan='13' width='403'>Company</th>".
    	       "<th colspan='8'  width='248'>Name</th>".
    	       "<th colspan='5'  width='155'>Office No</th>".
    	       "<th colspan='5'  width='155'>Cell Phone No</th>".
    	       "<th colspan='9'  width='279'>E-Mail</th>".    	         	       	    
    	     "</tr>".  
    	     "<tr>".         	     
    	       "<td colspan='13'>{$ceremony[0]['CeremonyTrn']['vendor_nm']}</td>".
    	       "<td colspan='8'><input  class='inputableField'  type='text' name='data[CeremonyTrn][attend_nm]' value='{$ceremony[0]['CeremonyTrn']['attend_nm']}' style='width:100%' /></td>".
    	       "<td colspan='5'><input  class='inputableField'  type='text' name='data[CeremonyTrn][phone_no]'  value='{$ceremony[0]['CeremonyTrn']['phone_no']}'  style='width:100%' /></td>".
    	       "<td colspan='5'><input  class='inputableField'  type='text' name='data[CeremonyTrn][cell_no]'   value='{$ceremony[0]['CeremonyTrn']['cell_no']}'   style='width:100%' /></td>".
    	       "<td colspan='9'><input  class='inputableField'  type='text' name='data[CeremonyTrn][email]'     value='{$ceremony[0]['CeremonyTrn']['email']}'     style='width:99%' /></td>".  
    	     "</tr>".
           "</table>".

           /*  セレモニーリング  */   
          "<table id='ceremonyRing_0_0_table' class='list'>".
    	    "<tr>".   	   
    	     "<th colspan='5'   width='155'><a href='#' class='add rowUnit' name='ceremonyRing_".GC_CEREMONY_RING."_0_0_0' style='width:100%'>Add Ring B/G</a></th>".
    	     "<th colspan='2'   width='62'>No</th>".
    	     "<th colspan='12'  width='372'>Name</th>".
    	     "<th colspan='2'   width='62'>Age</th>".
    	     "<th colspan='19'  width='589'>Other Request</th>".    	         	       	    
    	   "</tr>";    	
                          
           for($i=0;$i < count($ceremony_ring);$i++){
           	echo "<tr id='ceremonyRing_0_0_{$i}_row'>".
                    "<td colspan='5'><input type='hidden' name='data[CeremonyRingTrn][".$i."][id]' value='{$ceremony_ring[$i]['CeremonyRingTrn']['id']}' />";
                     if($i == 0){
               	      echo  "<a href='#' class='delete rowUnit' name='ceremonyRing_".GC_CEREMONY_RING."_0_0_".$i."' style='width:100%' disabled>&nbsp;</a>";
                     }
                     else{
              	      echo  "<a href='#' class='delete rowUnit' name='ceremonyRing_".GC_CEREMONY_RING."_0_0_".$i."' style='width:100%' >delete</a>";
                     }   	   
              echo "</td>".
    	           
                   "<td colspan='2'><select class='inputableField' name='data[CeremonyRingTrn][".$i."][no]' style='width:100%' >";                 
                   for($j=0;$j < 100;$j++){
                     if($ceremony_ring[$i]['CeremonyRingTrn']['no'] == $j){
                    	 echo "<option value='$j' selected>$j</option>";                  	     
                     }else{
                  	     echo "<option value='$j'         >$j</option>";
                     }
                   }
            echo "</select></td>".   
            
    	          "<td colspan='12'><input class='inputableField' type='text' name='data[CeremonyRingTrn][".$i."][ring_bg_nm]' value='{$ceremony_ring[$i]['CeremonyRingTrn']['ring_bg_nm']}' style='width:100%' /></td>".

                  "<td colspan='2'><select class='inputableField' name='data[CeremonyRingTrn][".$i."][age]' style='width:100%' >";                 
                  for($k=0;$k < 100;$k++){
                    if($ceremony_ring[$i]['CeremonyRingTrn']['age'] == $k){
                  	  echo "<option value='$k' selected>$k</option>";                  	     
                    }else{
                  	  echo "<option value='$k'         >$k</option>";
                    }
                  }
           echo "</select></td>".  
                  "<td colspan='19'><input class='inputableField' type='text' name='data[CeremonyRingTrn][".$i."][note]'  value='{$ceremony_ring[$i]['CeremonyRingTrn']['note']}' style='width:99%' /></td>".
    	        "</tr>";     
           }
         echo "</table>".
    	  
         /*  セレモニーフラワー  */ 
         "<table id='ceremonyFlower_0_0_table' class='list'>".
    	    "<tr>".   	   
    	     "<th colspan='5'   width='155'><a href='#' class='add rowUnit' name='ceremonyFlower_".GC_CEREMONY_FLOWER."_0_0_0' style='width:100%'>Add Flower B/G</a></th>".
    	     "<th colspan='2'   width='62'>No</th>".
    	     "<th colspan='12'  width='372'>Name</th>".
    	     "<th colspan='2'   width='62'>Age</th>".
    	     "<th colspan='19'  width='589'>Other Request</th>".    	         	       	    
    	   "</tr>";    
                              
           for($i=0;$i < count($ceremony_flower);$i++){
           	echo "<tr id='ceremonyFlower_0_0_{$i}_row'>".
                    "<td colspan='5'><input type='hidden' name='data[CeremonyFlowerTrn][".$i."][id]' value='{$ceremony_flower[$i]['CeremonyFlowerTrn']['id']}' />";
                     if($i == 0){
               	      echo  "<a href='#' class='delete rowUnit' name='ceremonyFlower_".GC_CEREMONY_FLOWER."_0_0_".$i."' style='width:100%' disabled>&nbsp;</a>";
                     }
                     else{
              	      echo  "<a href='#' class='delete rowUnit' name='ceremonyFlower_".GC_CEREMONY_FLOWER."_0_0_".$i."' style='width:100%' >delete</a>";
                     }   	   
             echo "</td>".

                  "<td colspan='2'><select class='inputableField' name='data[CeremonyFlowerTrn][".$i."][no]' style='width:100%' >";                 
                   for($j=0;$j < 100;$j++){
                     if($ceremony_flower[$i]['CeremonyFlowerTrn']['no'] == $j){
                    	 echo "<option value='$j' selected>$j</option>";                  	     
                     }else{
                  	     echo "<option value='$j'         >$j</option>";
                     }
                   }
            echo "</select></td>".   
            
    	          "<td colspan='12'><input class='inputableField' type='text' name='data[CeremonyFlowerTrn][".$i."][flower_bg_nm]' value='{$ceremony_flower[$i]['CeremonyFlowerTrn']['flower_bg_nm']}' style='width:100%' /></td>".

                  "<td colspan='2'><select class='inputableField' name='data[CeremonyFlowerTrn][".$i."][age]' style='width:100%' >";                 
                  for($k=0;$k < 100;$k++){
                    if($ceremony_flower[$i]['CeremonyFlowerTrn']['age'] == $k){
                  	  echo "<option value='$k' selected>$k</option>";                  	     
                    }else{
                  	  echo "<option value='$k'         >$k</option>";
                    }
                  }
           echo "</select></td>".  
                "<td colspan='19'><input class='inputableField' type='text' name='data[CeremonyFlowerTrn][".$i."][note]'  value='{$ceremony_flower[$i]['CeremonyFlowerTrn']['note']}'  style='width:99%' /></td>".
    	        "</tr>";      
           }
         echo "</table>".

         /*  ブライドメイド  */   
         "<table id='ceremonyBride_0_0_table' class='list'>".
    	    "<tr>".   	   
    	     "<th colspan='3'   width='155'><a href='#' class='add rowUnit' name='ceremonyBride_".GC_CEREMONY_BRIDE."_0_0_0' style='width:100%'>Add Bride's Made</a></th>".
    	     "<th colspan='2'   width='62'>No</th>".
    	     "<th colspan='12'  width='372'>Name</th>".
    	     "<th colspan='2'   width='62'>Number</th>".
    	     "<th colspan='19'  width='589'>Other Request</th>".    	         	       	    
    	   "</tr>";    	
                          
           for($i=0;$i < count($ceremony_bride_made);$i++){
           	echo "<tr id='ceremonyBride_0_0_{$i}_row'>".
                    "<td colspan='3'><input type='hidden' name='data[CeremonyBrideMadeTrn][".$i."][id]' value='{$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['id']}' />";
                     if($i == 0){
               	      echo  "<a href='#' class='delete rowUnit' name='ceremonyBride_".GC_CEREMONY_BRIDE."_0_0_".$i."' style='width:100%' disabled>&nbsp;</a>";
                     }
                     else{
              	      echo  "<a href='#' class='delete rowUnit' name='ceremonyBride_".GC_CEREMONY_BRIDE."_0_0_".$i."' style='width:100%' >delete</a>";
                     }   	   
             echo "</td>".
             
    	           "<td colspan='2'><select class='inputableField' name='data[CeremonyBrideMadeTrn][".$i."][no]' style='width:100%' >";                 
                   for($j=0;$j < 100;$j++){
                     if($ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['no'] == $j){
                    	 echo "<option value='$j' selected>$j</option>";                  	     
                     }else{
                  	     echo "<option value='$j'         >$j</option>";
                     }
                   }
            echo "</select></td>".   
    	          "<td colspan='12'><input class='inputableField' type='text' name='data[CeremonyBrideMadeTrn][".$i."][bride_made_nm]' value='{$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['bride_made_nm']}' style='width:100%' /></td>".

                  "<td colspan='2'><select class='inputableField' name='data[CeremonyBrideMadeTrn][".$i."][count]' style='width:100%' >";                 
                  for($k=0;$k < 100;$k++){
                    if($ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['count'] == $k){
                  	  echo "<option value='$k' selected>$k</option>";                  	     
                    }else{
                  	  echo "<option value='$k'         >$k</option>";
                    }
                  }
           echo "</select></td>".  
                "<td colspan='19'><input class='inputableField' type='text' name='data[CeremonyBrideMadeTrn][".$i."][note]' value='{$ceremony_bride_made[$i]['CeremonyBrideMadeTrn']['note']}'  style='width:99%' /></td>".
    	        "</tr>";       
           }
         echo "</table>".
         
          /*  グルームメイド  */    
         "<table id='ceremonyGroom_0_0_table' class='list'>".
    	    "<tr>".   	   
    	     "<th colspan='5'   width='155'><a href='#' class='add rowUnit' name='ceremonyGroom_".GC_CEREMONY_GROOM."_0_0_0' style='width:100%'>Add Groom's Made</a></th>".
    	     "<th colspan='2'   width='62'>No</th>".
    	     "<th colspan='12'  width='372'>Name</th>".
    	     "<th colspan='2'   width='62'>Number</th>".
    	     "<th colspan='19'  width='589'>Other Request</th>".    	         	       	    
    	   "</tr>"; 
                         
           for($i=0;$i < count($ceremony_groom_made);$i++){
           	echo "<tr id='ceremonyGroom_0_0_{$i}_row'>".
                    "<td colspan='5'><input type='hidden' name='data[CeremonyGroomMadeTrn][".$i."][id]' value='{$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['id']}' />";
                     if($i == 0){
               	      echo  "<a href='#' class='delete rowUnit' name='ceremonyGroom_".GC_CEREMONY_GROOM."_0_0_".$i."' style='width:100%' disabled>&nbsp;</a>";
                     }
                     else{
              	      echo  "<a href='#' class='delete rowUnit' name='ceremonyGroom_".GC_CEREMONY_GROOM."_0_0_".$i."' style='width:100%' >delete</a>";
                     }   	   
             echo "</td>".
             
    	         "<td colspan='2'><select class='inputableField' name='data[CeremonyGroomMadeTrn][".$i."][no]' style='width:100%' >";                 
                  for($j=0;$j < 100;$j++){
                   if($ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['no'] == $j){
                  	 echo "<option value='$j' selected>$j</option>";                  	     
                   }else{
                  	 echo "<option value='$j'         >$j</option>";
                   }
                   }
               echo "</select></td>".   

                  "<td colspan='12'><input class='inputableField' type='text' name='data[CeremonyGroomMadeTrn][".$i."][groom_made_nm]' value='{$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['groom_made_nm']}' style='width:100%' /></td>".
    	          
                  "<td colspan='2'><select class='inputableField' name='data[CeremonyGroomMadeTrn][".$i."][count]' style='width:100%' >";                 
                  for($k=0;$k < 100;$k++){
                   if($ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['count'] == $k){
                  	 echo "<option value='$k' selected>$k</option>";                  	     
                   }else{
                  	 echo "<option value='$k'         >$k</option>";
                   }
                   }
               echo "</select></td>".   
                  
                  "<td colspan='19'><input class='inputableField' type='text' name='data[CeremonyGroomMadeTrn][".$i."][note]' value='{$ceremony_groom_made[$i]['CeremonyGroomMadeTrn']['note']}'   style='width:99%' /></td>".
    	        "</tr>";         
           }
         echo "</table>".
            "<table class='list' >".
              "<tr>".  
    	        "<th colspan='30'  width='930'>Ceremony Plan</th>".
    	        "<th colspan='10'  width='310'>Ceremony Rehearsal</th>".    	         	       	    
    	      "</tr>".  
              "<tr>". 
                 "<td colspan='30'>".$ceremony[0]['CeremonyTrn']['menu']."</td>".
    	         "<td colspan='10'><input class='inputableField' type='text' name='data[CeremonyTrn][rehearsal]' value='{$ceremony[0]['CeremonyTrn']['rehearsal']}' style='width:99%' /></td>".
              "</tr>".
              "<tr>". 
    	         "<th colspan='5'  width='155'>Bride Escorted</th>".
    	         "<th colspan='4'  width='124'>Ring Pollow</th>".    	 
    	         "<th colspan='4'  width='124'>Champagne</th>".
    	         "<th colspan='9'  width='279'>Toasting Speech Name</th>".
    	         "<th colspan='5'  width='155'>Legal Wedding</th>".
    	         "<th colspan='9'  width='279'>Procedure Person</th>".   
    	         "<th colspan='4'  width='124'>Procedure Date</th>".         	       	    
    	      "</tr>".
              "<tr>". 
                 "<td colspan='5'><input class='inputableField' type='text' name='data[CeremonyTrn][bride_escorted]'       value='{$ceremony[0]['CeremonyTrn']['bride_escorted']}'       style='width:100%' /></td>".
    	         "<td colspan='4'><input class='inputableField' type='text' name='data[CeremonyTrn][ring_pollow]'          value='{$ceremony[0]['CeremonyTrn']['ring_pollow']}'          style='width:100%' /></td>".
                 "<td colspan='4'><select class='inputableField' name='data[CeremonyTrn][champagne]' style='width:100%' >";                 
                  for($i=0;$i < 100;$i++){
                   if($ceremony[0]['CeremonyTrn']['champagne'] == $i){
                  	 echo "<option value='$i' selected>$i</option>";                  	     
                   }else{
                  	 echo "<option value='$i'         >$i</option>";
                   }
                   }
               echo "</select></td>".   
    	         "<td colspan='9'><input class='inputableField' type='text' name='data[CeremonyTrn][toasting_speech_nm]' value='{$ceremony[0]['CeremonyTrn']['toasting_speech_nm']}' style='width:100%' /></td>".
                 "<td colspan='5'><select class='inputableField' name='data[CeremonyTrn][legal_wedding_kbn]' style='width:100%' >";
                  if($ceremony[0]['CeremonyTrn']['legal_wedding_kbn'] == 0){
                  	 echo "<option value='0' selected>NO</option>".
                  	      "<option value='1'         >YES</option>";
                  }else{
                  	 echo "<option value='0'         >NO</option>".
                  	      "<option value='1' selected >YES</option>";
                  }
               echo "</select></td>". 
    	         "<td colspan='9'><input class='inputableField'           type='text' name='data[CeremonyTrn][procedure_nm]' value='{$ceremony[0]['CeremonyTrn']['procedure_nm']}'                                style='width:100%' /></td>".
                 "<td colspan='4'><input class='date_mask inputableField' type='text' name='data[CeremonyTrn][procedure_dt]' value='{$common->evalNbspForShortDate($ceremony[0]['CeremonyTrn']['procedure_dt'])}' style='width:99%' /></td>".    	       
              "</tr>".
              "<tr>". 
    	         "<th colspan='5'  width='155'>Bouquet Toss</th>".
    	         "<th colspan='5'  width='155'>Flower Shower</th>".    	 
    	         "<th colspan='5'  width='155'>Bubble Shower</th>".
    	         "<th colspan='5'  width='155'>Lie Ceremony</th>".
    	         "<th colspan='5'  width='155'>Count</th>".
    	         "<th colspan='7'  width='217'>Lie Ceremony Place</th>".      	
                 "<th colspan='8'  width='248'>Other Request</th>".            	       	    
    	      "</tr>".
              "<tr>". 
                 "<td colspan='5'><select class='inputableField' name='data[CeremonyTrn][bouquet_toss_kbn]' style='width:100%' >";
                  if($ceremony[0]['CeremonyTrn']['bouquet_toss_kbn'] == 0){
                  	 echo "<option value='0' selected>NO</option>".
                  	      "<option value='1'         >YES</option>";
                  }else{
                  	 echo "<option value='0'         >NO</option>".
                  	      "<option value='1' selected >YES</option>";
                  }
               echo "</select></td>".
                 
    	         "<td colspan='5'><select class='inputableField' name='data[CeremonyTrn][flower_shower_kbn]' style='width:100%' >";
                  if($ceremony[0]['CeremonyTrn']['flower_shower_kbn'] == 0){
                  	 echo "<option value='0' selected>NO</option>".
                  	      "<option value='1'         >YES</option>";
                  }else{
                  	 echo "<option value='0'         >NO</option>".
                  	      "<option value='1' selected >YES</option>";
                  }
               echo "</select></td>".
                  
                  "<td colspan='5'><select class='inputableField' name='data[CeremonyTrn][bubble_shower_kbn]' style='width:100%' >";
                  if($ceremony[0]['CeremonyTrn']['bubble_shower_kbn'] == 0){
                  	 echo "<option value='0' selected>NO</option>".
                  	      "<option value='1'         >YES</option>";
                  }else{
                  	 echo "<option value='0'         >NO</option>".
                  	      "<option value='1' selected >YES</option>";
                  }
               echo "</select></td>".   
               
    	          "<td colspan='5'><select class='inputableField' name='data[CeremonyTrn][lei_ceremony_kbn]' style='width:100%' >";
                  if($ceremony[0]['CeremonyTrn']['lei_ceremony_kbn'] == 0){
                  	 echo "<option value='0' selected>NO</option>".
                  	      "<option value='1'         >YES</option>";
                  }else{
                  	 echo "<option value='0'         >NO</option>".
                  	      "<option value='1' selected >YES</option>";
                  }
               echo "</select></td>".
               
                "<td colspan='5'><select class='inputableField' name='data[CeremonyTrn][lei_ceremony_count]' style='width:100%' >";
                 for($i=0;$i < 100;$i++){
                  if($ceremony[0]['CeremonyTrn']['lei_ceremony_count'] == $i){
                  	 echo "<option value='$i' selected>$i</option>";                  	     
                  }else{
                  	 echo "<option value='$i'         >$i</option>";
                  }
                 }
               echo "</select></td>".               
    	         "<td colspan='7'><input class='inputableField' type='text' name='data[CeremonyTrn][lei_ceremony_place]'  value='{$ceremony[0]['CeremonyTrn']['lei_ceremony_place']}' style='width:99%' /></td>".
                 "<td colspan='8'><input class='inputableField' type='text' name='data[CeremonyTrn][note]'                value='{$ceremony[0]['CeremonyTrn']['note']}'               style='width:99%' /></td>".
               "</tr>".
               "<tr>".    	       
                 "<th colspan='40'  width='1240'>Other Request(RW)</th>".            	       	    
    	       "</tr>".
               "<tr>".    	       
                 "<td colspan='40'><input class='inputableField' type='text' name='data[CeremonyTrn][rw_note]'            value='{$ceremony[0]['CeremonyTrn']['rw_note']}'             style='width:99%' /></td>".            	       	    
    	       "</tr>".
             "</table>";
           }
        ?>