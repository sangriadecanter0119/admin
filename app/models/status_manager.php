<?php
class StatusManager extends AppModel {
	var $useTable = false;

	/**
	 * 「仮成約」ステータスの条件を満たしていることを確認
	 * @param unknown $customer_id
	 * @return boolean
	 */
	function IsFilledContractingStatus($customer_id){

		App::import("Model", "CustomerMst");
		$customer = new CustomerMst();

		App::import("Model", "ContractTrn");
		$contract = new ContractTrn();

		/* 新規担当者、プラン担当者、挙式会場及び挙式時間またはレセプション時間が入力されている  */
		$customer = $customer->findById($customer_id);
		$contract = $contract->find('all',array('conditions'=>array('customer_id'=>$customer_id)));

		if(empty($customer['CustomerMst']['first_contact_person_nm']) || empty($customer['CustomerMst']['process_person_nm'])){
			return false;
		}

		if(count($contract) > 0){

		  if(empty($contract[0]['ContractTrn']['wedding_dt'])   ||
		    (empty($contract[0]['ContractTrn']['wedding_time']) && empty($contract[0]['ContractTrn']['reception_time']))){
			  return false;
		  }else{
		  	return true;
		  }
		}

		if(empty($customer['CustomerMst']['wedding_planned_dt']) ||
		  (empty($customer['CustomerMst']['wedding_planned_time']) && empty($customer['CustomerMst']['reception_planned_time']))){
			return  false;
		}
	  return  true;
	}

}