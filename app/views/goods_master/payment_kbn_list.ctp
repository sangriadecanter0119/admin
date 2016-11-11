<?php
 for($i=0;$i < count($payment_kbn_list);$i++){
   $atr = $payment_kbn_list[$i];
   echo "<option value='{$atr['PaymentKbnMst']['id']}'>".$atr['PaymentKbnMst']['payment_kbn_nm']."</option>";
 }
?>
