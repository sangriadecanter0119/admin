<?php
class RemittanceService extends AppModel {
    var $useTable = false;

  /**
   * 
   * 原価為替レートと送金為替レートを一括更新する
   * @param unknown_type $data
   * @param unknown_type $user_name
   */
  function UpdateAllRate($data,$username){
  	
  	App::import("Model", "EstimateTrn");       
    $estimate = new EstimateTrn();
    App::import("Model", "EstimateDtlTrn");       
    $estimate_dtl = new EstimateDtlTrn();
    App::import("Model", "RemittanceTrn");
    $remittance = new RemittanceTrn();  
    App::import("Model", "RemittanceTrnView");
    $remittance_view = new RemittanceTrnView();
            
    $tr = ClassRegistry::init('TransactionManager');
	$tr->begin();     

	  /* 指定挙式年月の送金一覧リストを取得 */
 	  $search = array("SUBSTR(wedding_dt,1,7)"=>$data['wedding_dt']);    	 	 
 	  $remittance_list = $remittance_view->find('all',array('conditions'=>$search,'order'=>'wedding_dt'));
	
 	  /* 送金リストの顧客の原価為替レートと送金為替レートを更新する */
      for($i=0;$i < count($remittance_list);$i++)
      { 	
         $atr = $remittance_list[$i]['RemittanceTrnView'];
	     /* 送金為替レート更新 */
         if($data['remittance_rate'] != null){
           $estimate_data['id'] = $atr['estimate_id'];
           $estimate_data['remittance_exchange_rate'] = $data['remittance_rate'];
	       if($estimate->UpdateRemittanceExchangeRate($estimate_data, $username) == false){
	       	 return array('result'=>false,'message'=>"見積の送金為替レートの更新に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
	       } 	  
         }
	     
	     /* 原価為替レート更新 */
	     if($data['cost_rate'] != null){
	       $ret = $estimate_dtl->UpdateAllCostExchangeRateByEstimateId($atr['estimate_id'], $data['cost_rate'], $username);
	       if($ret['result']==false){return $ret;}  
	     }
 	     /* 送金金額の再計算 */   
	     if($data['remittance_rate'] != null || $data['cost_rate'] != null){ 	    	
    	   $ret = $remittance->calculate($atr['estimate_id'],$username);
           if($ret['result']==false){return $ret;}  
	     }
      }
    $tr->commit();
    return array('result'=>true);
  }
}  
?>