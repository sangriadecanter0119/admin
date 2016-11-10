<?php
class FundManagementTrnView extends AppModel {
   var $name = 'FundManagementTrnView';

   /**
    * ステータスが請求済みの指定の挙式日の資金管理情報を取得する
    * @param unknown $wedding_dt
    */
   function findAllByWeddingDateInInvoiced($wedding_dt){

   	 return $this->find('all',array('conditions'=>array('SUBSTR(wedding_dt,1,7)'=>$wedding_dt,'status_id'=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>'wedding_dt'));

   }
}
?>