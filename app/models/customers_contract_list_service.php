<?php
class CustomersContractListService extends AppModel {
    var $useTable = false;

  /**
   * 挙式月ベースの顧客情報を月数分取得
   * @param unknown $wedding_dt  スタート年月
   * @param unknown $month_count 取得月数
   * @return multitype:NULL
   */
  function getCustomerListForWedding($wedding_dt,$month_count,$attendant){

     $data = array();
     for($i=0;$i < $month_count;$i++){
     	$data[$i] = $this->_getCustomerListByWeddingDate(date('Y-m',strtotime($wedding_dt." +".$i." month")),$attendant);
     }

     $result = array();
     for($i=0; $i < count($data);$i++){
     	$result[$i] = $this->_getCustomerList($data[$i]);
     }
      return $result;
    }

    /**
     * 契約月ベースの顧客情報を取得
     * @param unknown $contract_dt スタート年月
     * @param unknown $month_count 取得月数
     * @return multitype:NULL
     */
  function getCustomerListForContract($contract_dt,$month_count,$attendant){

    	$data = array();
    	for($i=0;$i < $month_count;$i++){
    		$data[$i] = $this->_getCustomerListByContractDate(date('Y-m',strtotime($contract_dt." +".$i." month")),$attendant);
    	}

    	$result = array();
    	for($i=0; $i < count($data);$i++){
    		$result[$i] = $this->_getCustomerList($data[$i]);
    	}
    	return $result;
    }

   /**
    * 指定の挙式年月の顧客情報を取得
    * @param unknown $wedding_dt
    */
  function _getCustomerListByWeddingDate($wedding_dt,$attendant){

  	App::import("Model", "ContractTrnView");
  	$contract_view = new ContractTrnView();

  	if($attendant == "ALL"){
  		return $contract_view->find('all',array('conditions'=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>array("wedding_dt")));
  	}else{
  		return $contract_view->find('all',array('conditions'=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED),"process_person_nm"=>$attendant),'order'=>array("wedding_dt")));
  	}
  }

  /**
   * 指定の契約年月の顧客情報を取得
   * @param unknown $contract_dt
   */
  function _getCustomerListByContractDate($contract_dt,$attendant){
  	App::import("Model", "ContractTrnView");
  	$contract_view = new ContractTrnView();

  	if($attendant == "ALL"){
  		return $contract_view->find('all',array('conditions'=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>array("contract_dt")));
  	}else{
  		return $contract_view->find('all',array('conditions'=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED),"first_contact_person_nm"=>$attendant),'order'=>array("contract_dt")));
  	}
  }

  /**
   * 顧客名と顧客コードの配列を取得
   * @param unknown $data
   * @return multitype:multitype:NULL string
   */
  function _getCustomerList($data){
  	$result = array();
  	for($i=0; $i < count($data);$i++){
  		$temp = array();
  		$temp['customer_cd'] = $data[$i]['ContractTrnView']['customer_cd'];
  		$temp['customer_nm'] = $data[$i]['ContractTrnView']['grmls_kj']." ".$data[$i]['ContractTrnView']['grmfs_kj'];
  		$temp['customer_id'] = $data[$i]['ContractTrnView']['customer_id'];
  		$temp['first_contact_person_nm'] = $data[$i]['ContractTrnView']['first_contact_person_nm'];
  		$temp['process_person_nm']       = $data[$i]['ContractTrnView']['process_person_nm'];
  		$result[$i] = $temp;
  	}
  	return $result;
  }
}
?>