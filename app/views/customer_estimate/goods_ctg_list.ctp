<?php
 echo "<option value=''></option>";         
 for($i=0;$i < count($goods);$i++)
 {
   $atr = $goods[$i];
   echo "<option value='{$atr['GoodsCtgMst']['id']}'>".$atr['GoodsCtgMst']['goods_ctg_nm']."</option>";           
 }  
?>
