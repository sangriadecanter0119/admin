<?php
 echo "<option value=''></option>";          
 for($i=0;$i < count($goods);$i++)
 {
   $atr = $goods[$i];
   echo "<option value='{$atr['GoodsMstView']['id']}'>".$atr['GoodsMstView']['goods_nm']."</option>";           
 }  
?>
