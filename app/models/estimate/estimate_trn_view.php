<?php
class EstimateTrnView extends AppModel {
  var $name = 'EstimateTrnView';

  /**
   * 採用見積の合計金額を取得する
   * @param unknown $customer_id
   * @return NULL
   */
  function getTotalAmountByCustomer($customer_id){
  	$data = $this->find('all',array('fields'=>array('total_yen'),'conditions'=>array('customer_id'=>$customer_id,'adopt_flg'=>ESTIMATE_ADOPTED)));
  	if(count($data)==0){ return 0;}

  	return ceil($data[0]['EstimateTrnView']['total_yen']);
  }
}
?>