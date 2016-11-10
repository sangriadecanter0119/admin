<?php 
 echo "<tr id='ceremonyGroom_0_0_{$dtl_counter}_row'>".
          "<td colspan='5'><input type='hidden' name='data[CeremonyGroomMadeTrn][{$dtl_counter}][id]' value='' />".
                          "<a href='#' class='delete rowUnit' name='ceremonyGroom_".GC_CEREMONY_GROOM."_0_0_".$dtl_counter."' style='width:100%' >delete</a>".
          "</td>".
          "<td colspan='2'>".
                         "<select class='inputableField' name='data[CeremonyGroomMadeTrn][{$dtl_counter}][no]' style='width:100%' >";                 
                            for($j=1;$j < 21;$j++){ 
                               if($j == $no){
  	      	     	              echo "<option value='$j' selected>$j</option>";
                               }else{
                                  echo "<option value='$j' >$j</option>";	
                               }
  	      	                 }
                    echo "</select></td>".   
          "<td colspan='12'><input class='inputableField' type='text' name='data[CeremonyGroomMadeTrn][{$dtl_counter}][groom_made_nm]' value='' style='width:100%' /></td>".
          "<td colspan='2'>".
                         "<select class='inputableField' name='data[CeremonyGroomMadeTrn][{$dtl_counter}][count]' style='width:100%' >";                 
                            for($k=1;$k < 100;$k++){ echo "<option value='$k'>$k</option>";  }
                    echo "</select></td>".   
          "<td colspan='19'><input class='inputableField' type='text' name='data[CeremonyGroomMadeTrn][{$dtl_counter}][note]' value=''   style='width:99%' /></td>".
      "</tr>";         

?>