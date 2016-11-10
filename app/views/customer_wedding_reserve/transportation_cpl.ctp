 <input type="hidden" name="data[Category][id]"      value="<?php echo GC_TRANS_CPL ?>" />

         <?php
           //CPL
           if(count($trans_cpl) > 0)
           {
           	 $head_id = -1;
         	 $head_counter = 0;
         	 for($head_index=0;$head_index < count($trans_cpl);$head_index++){

         	    /* メインテーブル作成 */
         	 	if($head_id != $trans_cpl[$head_index]['TransCplTrnView']['id']){
         		 	$head_id  = $trans_cpl[$head_index]['TransCplTrnView']['id'];

                echo  "<input type='hidden' name='data[TransCplTrn][{$head_counter}][id]' value='{$trans_cpl[$head_index]['TransCplTrnView']['id']}' />".
                      "<table id='transCpl_{$head_counter}_table' class='list' >".
    	                "<tr>".
    	                   "<th colspan='13' width='403'>Company</th>".
    	                   "<th colspan='8'  width='248'>Name</th>".
    	     			   "<th colspan='5'  width='155'>Office No</th>".
    	     			   "<th colspan='5'  width='155'>Cell Phone No</th>".
    	     			   "<th colspan='9'  width='279'>E-Mail</th>".
    	    			"</tr>".
    	    			"<tr>".
    	     			   "<td colspan='13'>{$trans_cpl[$head_index]['TransCplTrnView']['vendor_nm']}</td>".
    	     			   "<td colspan='8'><input  class='inputableField'  type='text' name='data[TransCplTrn][{$head_counter}][attend_nm]' value='{$trans_cpl[$head_index]['TransCplTrnView']['vendor_attend_nm']}' style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[TransCplTrn][{$head_counter}][phone_no]'  value='{$trans_cpl[$head_index]['TransCplTrnView']['vendor_phone_no']}'  style='width:100%' /></td>".
    	     			   "<td colspan='5'><input  class='inputableField'  type='text' name='data[TransCplTrn][{$head_counter}][cell_no]'   value='{$trans_cpl[$head_index]['TransCplTrnView']['vendor_cell_no']}'   style='width:100%' /></td>".
    	     			   "<td colspan='9'><input  class='inputableField'  type='text' name='data[TransCplTrn][{$head_counter}][email]'     value='{$trans_cpl[$head_index]['TransCplTrnView']['vendor_email']}'     style='width:99%' /></td>".
    	    			"</tr>".
                      "</table>";

                        /* サブテーブル作成 */
               echo "<div id='transCpl_{$head_counter}_div' >";
                     $sub_id = -1;
                     $sub_counter = 0;

                     for($sub_index=0;$sub_index < count($trans_cpl);$sub_index++){
                       //サブテーブルの外部キーとヘッダの主キーが同値
    	               if($head_id == $trans_cpl[$sub_index]['TransCplTrnView']['id']){
    	     	            if($sub_id != $trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_id']){
    	       	               $sub_id  = $trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_id'];

    	         echo "<table id='transCpl_{$head_counter}_{$sub_counter}_table' class='list' >".
    	       	       "<tr>".
    	     			   "<th colspan='11' width='341'>Plan</th>".
    	     			   "<th colspan='4'  width='124'>Vihicular Type</th>".
    	                   "<th colspan='5'  width='155'>Departure Place</th>".
    	     			   "<th colspan='5'  width='155'>Final Destination</th>".
    	     			   "<th colspan='6'  width='186'>Working Start Time</th>".
    	     			   "<th colspan='6'  width='186'>Working End Time</th>".
    	     			   "<th colspan='3'  width='93'>Working Total</th>".
    	    			"</tr>".
    	    			"<tr>".
    	     			   "<td colspan='11'><input  class=''  type='hidden' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][id]'    value='{$trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_id']}' />".
                                            "{$trans_cpl[$sub_index]['TransCplTrnView']['menu']}".
    	     			   "</td>".
    	     			   "<td colspan='4'><input  class=''                     type='hidden'                                                     name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][vihicular_type]'     value='{$trans_cpl[$sub_index]['TransCplTrnView']['vihicular_type']}'     style='width:99%' />".
    	                                    "{$trans_cpl[$sub_index]['TransCplTrnView']['vihicular_type']}".
    	     			   "</td>".
    	                   "<td colspan='5'><input  class='inputableField'                      type='text'                                                       name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][dep_place]'          value='{$trans_cpl[$sub_index]['TransCplTrnView']['dep_place']}'          style='width:99%' /></td>".
    	                   "<td colspan='5'><input  class='inputableField'                      type='text'                                                       name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][final_dest]'         value='{$trans_cpl[$sub_index]['TransCplTrnView']['final_dest']}'         style='width:99%' /></td>".
    	     			   "<td colspan='6'><input  class='time_mask work_time inputableField'  type='text' id='transCpl{$head_counter}{$sub_counter}_start_time' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][working_start_time]' value='{$trans_cpl[$sub_index]['TransCplTrnView']['working_start_time']}' style='width:99%' /></td>".
    	     			   "<td colspan='6'><input  class='time_mask work_time inputableField'  type='text' id='transCpl{$head_counter}{$sub_counter}_end_time'   name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][working_end_time]'   value='{$trans_cpl[$sub_index]['TransCplTrnView']['working_end_time']}'   style='width:99%' /></td>".
    	     			   "<td colspan='3'><input  class=''                     type='text' id='transCpl{$head_counter}{$sub_counter}_total_time' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][working_total]'      value='{$trans_cpl[$sub_index]['TransCplTrnView']['fm_working_total']}'   style='width:98%' readonly /></td>".
    	    			"</tr>".
    	    			"<tr>".
    	     			    "<th colspan='4'  rowspan='2' width='124'>Number Of Passenger</th>".
    	     				"<th colspan='4'  width='124'>B&G</th>".
    	     				"<th colspan='4'  width='124'>Guest</th>".
    	     				"<th colspan='4'  width='124'>PH</th>".
    	     				"<th colspan='4'  width='124'>H&M</th>".
    	     				"<th colspan='4'  width='124'>ATT</th>".
    	                    "<th colspan='4'  width='124'>VH</th>".
    	    				"<th colspan='4'  width='124'>Total</th>".
    	     				"<th colspan='8'  width='248'>Other Request</th>".
    	    			"</tr>".
    	                "<tr>".
    	                    "<td colspan='4'>".
    	                            "<select class='transCpl inputableField' id='transCpl{$head_counter}{$sub_counter}_bg' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][passenger_bg]' style='width:100%'>";
    	                                for($i=0;$i < 100;$i++)
    	                                {
    	     	                           if($trans_cpl[$sub_index]['TransCplTrnView']['passenger_bg'] == $i){
    	     	                               echo "<option value='$i' selected>$i</option>";
    	     	                           }else{
    	                                       echo "<option value='$i' >$i</option>";
    	     	                           }
    	                                }
    	                     echo "</select></td>".
    	          	      "<td colspan='4'>".
    	          	               "<select class='transCpl inputableField' id='transCpl{$head_counter}{$sub_counter}_guest' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][passenger_guest]' style='width:100%'>";
    	                                for($i=0;$i < 100;$i++)
    	                                {
    	     	                           if($trans_cpl[$sub_index]['TransCplTrnView']['passenger_guest'] == $i){
    	     	                               echo "<option value='$i' selected>$i</option>";
    	     	                           }else{
    	                                       echo "<option value='$i' >$i</option>";
    	      	                           }
    	                                }
    	                     echo "</select></td>".
    	          	    "<td colspan='4'>".
    	          	              "<select class='transCpl inputableField' id='transCpl{$head_counter}{$sub_counter}_ph' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][passenger_ph]' style='width:100%'>";
    	                               for($i=0;$i < 100;$i++)
    	                               {
    	                          	      if($trans_cpl[$sub_index]['TransCplTrnView']['passenger_ph'] == $i){
    	     	                              echo "<option value='$i' selected>$i</option>";
    	     	                          }else{
    	                                      echo "<option value='$i' >$i</option>";
    	     	                          }
    	                               }
    	                    echo "</select></td>".
    	         	   "<td colspan='4'>".
    	         	             "<select class='transCpl inputableField' id='transCpl{$head_counter}{$sub_counter}_hm' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][passenger_hm]' style='width:100%'>";
    	                              for($i=0;$i < 100;$i++)
    	                              {
    	                                  if($trans_cpl[$sub_index]['TransCplTrnView']['passenger_hm'] == $i){
    	     	                              echo "<option value='$i' selected>$i</option>";
    	                  	              }else{
    	                                      echo "<option value='$i' >$i</option>";
    	      	                          }
    	                              }
    	                   echo "</select></td>".
    	             "<td colspan='4'>".
    	                        "<select class='transCpl inputableField' id='transCpl{$head_counter}{$sub_counter}_att' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][passenger_att]' style='width:100%'>";
    	                             for($i=0;$i < 100;$i++)
    	                             {
    	     	                         if($trans_cpl[$sub_index]['TransCplTrnView']['passenger_att'] == $i){
    	     	                             echo "<option value='$i' selected>$i</option>";
    	     	                         }else{
    	                                     echo "<option value='$i' >$i</option>";
    	     	                         }
    	                             }
    	                 echo "</select></td>".
    	            "<td colspan='4'>".
    	                        "<select class='transCpl inputableField' id='transCpl{$head_counter}{$sub_counter}_vh' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][passenger_vh]' style='width:100%'>";
    	                             for($i=0;$i < 100;$i++)
    	                             {
    	     	                         if($trans_cpl[$sub_index]['TransCplTrnView']['passenger_vh'] == $i){
    	     	                             echo "<option value='$i' selected>$i</option>";
    	     	                         }else{
    	                                     echo "<option value='$i' >$i</option>";
    	     	                         }
    	                             }
    	                 echo "</select></td>".
    	            "<td colspan='4'>".
    	                "<input  class='inputdisable'  type='text' id='transCpl{$head_counter}{$sub_counter}_total_passenger' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][total_passenger]'  value='{$trans_cpl[$sub_index]['TransCplTrnView']['total_passenger']}'  style='width:98%' readonly />".
    	            "</td>".
    	    	    "<td colspan='8'><input class='inputableField' type='text' name='data[TransCplSubTrn][{$head_counter}][{$sub_counter}][note]' value='{$trans_cpl[$sub_index]['TransCplTrnView']['trans_cpl_sub_note']}' style='width:99%' /></td>".
    	          "</tr>".
    	          "<tr>".
    	     	   	   "<th colspan='4'  width='84'><a href='#' class='add rowUnit' name='transCpl_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}' style='width:100%'>Add Location</a></th>".
    	     		   "<th colspan='2'  width='62'>No</th>".
    	     		   "<th colspan='4'  width='124'>Dep Time</th>".
    	     		   "<th colspan='10' width='310'>Departure Place</th>".
    	     		   "<th colspan='4'  width='124'>Drop Time</th>".
    	     		   "<th colspan='10' width='310'>Drop Off Place</th>".
    	     		   "<th colspan='6'  width='186'>Other Request(RW)</th>".
    	   		  "</tr>";

    	        $dtl_id = -1;
                $dtl_counter =0;
           		for($dtl_index=0;$dtl_index < count($trans_cpl);$dtl_index++){
    	      		//詳細テーブルの外部キーとサブテーブルの主キーが同値
           	  	    if($sub_id == $trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_sub_id']){
                 	  	 if($dtl_id != $trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_dtl_id']){
                      	    $dtl_id  = $trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_dtl_id'];

    	    		       echo "<tr id='transCpl_{$head_counter}_{$sub_counter}_{$dtl_counter}_row'>".
    	   	                       "<td colspan='4'><input type='hidden' name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][id]' value='{$trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_dtl_id']}' />";
    	    		                   //１行目は削除負荷とする
    	                               if($dtl_counter == 0){
                                          echo  "<a href='#' class='delete rowUnit' name='transCpl_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' disabled></a>&nbsp;</td>";
                                       }else{
                 	                      echo  "<a href='#' class='delete rowUnit' name='transCpl_".GC_TRANS_CPL."_{$head_counter}_{$sub_counter}_{$dtl_counter}' style='width:100%' >delete</a></td>";
                                       }
    	    		          echo "</td>".
    	     		   	           "<td colspan='2'>".
    	                               "<select name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][no]' style='width:100%'>";
    	                                  for($j=1;$j < 21;$j++)
    	                                  {
    	     	                            if($trans_cpl[$dtl_index]['TransCplTrnView']['no'] == $j){
    	     	                               echo "<option value='$j' selected>$j</option>";
    	     	                            }else{
    	                                       echo "<option value='$j' >$j</option>";
    	     	                            }
    	                                  }
    	                         echo "</select></td>".
    	                         "<td colspan='4'> <input class='time_mask inputableField'  type='text' name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_time]'  value='{$trans_cpl[$dtl_index]['TransCplTrnView']['departure_time']}'     style='width:100%' /></td>".
    	                         "<td colspan='10'><input class='inputableField'            type='text' name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][departure_place]' value='{$trans_cpl[$dtl_index]['TransCplTrnView']['departure_place']}'    style='width:100%' /></td>".
    	                         "<td colspan='4'> <input class='time_mask inputableField'  type='text' name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_time]'    value='{$trans_cpl[$dtl_index]['TransCplTrnView']['arrival_time']}'       style='width:100%' /></td>".
    	                         "<td colspan='10'><input class='inputableField'            type='text' name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][arrival_place]'   value='{$trans_cpl[$dtl_index]['TransCplTrnView']['arrival_place']}'      style='width:100%' /></td>".
    	                         "<td colspan='6'> <input class='inputableField'            type='text' name='data[TransCplDtlTrn][{$head_counter}][{$sub_counter}][{$dtl_counter}][note]'            value='{$trans_cpl[$dtl_index]['TransCplTrnView']['trans_cpl_dtl_note']}' style='width:99%' /></td>".
    	                       "</tr>";
    	                       $dtl_counter++;
    	    	          }
           	            }  //サブIDと一致する詳細テーブル外部キー判定のIF文の締め
    	           }   //詳細テーブルのデータ数だけLOOPするFOR文の締め
    	       $sub_counter++;
    	   echo "</table>";
                        }
   	                   }    //ヘッダIDと一致するサブテーブル外部キー判定のIF文の締め
                     }  //サブテーブルのデータ数だけLOOPするFOR文の締め
                 echo "</div>".
                      "<br /><br />";
                 $head_counter++;
                 } //一意のヘッダID判定のIF文の締め
              }    //trans_viewのデータ数だけLOOPするFOR文の締め
            }  //transデータ存在チェックIF文の締め
       ?>
