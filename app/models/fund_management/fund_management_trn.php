<?php
class FundManagementTrn extends AppModel {
   var $name = 'FundManagementTrn';

   /**
    *
    * 資金管理データを新規作成する
    * @param $contract_id
    * @param $user_name
    * @return 正常：　 新規データのID　
    *         異常:
    */
   function createNew($contract_id,$wedding_deposit,$wedding_deposit_dt,$user_name){

     $fund_data = array(
 	                    "contract_id"=>$contract_id,
     		            "wedding_deposit"=>$wedding_deposit,
     		            "wedding_deposit_dt"=>$wedding_deposit_dt,
 	                    "reg_nm"=>$user_name,
 	                    "reg_dt"=>date('Y-m-d H:i:s')
 	                    );
 	 $this->create();
     if($this->save($fund_data)==false){
     	return array('result'=>false,'message'=>"資金管理データ更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
    return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

  /**
   * 資金管理の内金データを更新する
   * @param unknown $customer_id
   * @param unknown $wedding_deposit
   * @param unknown $wedding_deposit_dt
   * @return multitype:boolean string |multitype:boolean
   */
  function updatePrepaiedAmount($contract_id,$wedding_deposit,$wedding_deposit_dt){

  	$fields = array( "wedding_deposit"=>$wedding_deposit,"wedding_deposit_dt"=>"'".$wedding_deposit_dt."'" );

  	if($this->updateAll($fields,array("contract_id"=>$contract_id))==false){
  		return array('result'=>false,'message'=>"資金管理データの更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);

  }
}
?>