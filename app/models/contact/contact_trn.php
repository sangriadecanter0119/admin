<?php
class ContactTrn extends AppModel {
    var $name = 'ContactTrn'; 
    
    /**
     * 
     * 問い合わせNOの連番の最大値を取得する
     * @param $customer_id
     */
    function getMaxSequenceNo($customer_id)
    {
       $sql = "SELECT MAX(contact_no) contact_no FROM contact_trns WHERE customer_id = ".$customer_id;
       $data = $this->query($sql);
           
 	   return $data[0][0]['contact_no'] == null ? "1" : ((int)$data[0][0]['contact_no'] + 1); 	
    }
}
?>