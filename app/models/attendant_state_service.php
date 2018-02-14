<?php
class AttendantStateService extends AppModel {
     var $useTable = false;

     /**
      * 成約ベースの担当者リストを取得する
      * @param unknown $contract_dt
      * @return unknown
      */
    function GetAllAttendantStateOfContract($contract_dt){

    	App::import("Model", "ContractTrnView");
  	    $contract = new ContractTrnView();

  	    App::import("Model", "EstimateService");
  	    $estimate = new EstimateService();

  	    $data = $contract->find("all",array("fields"=>array("customer_id","status_nm","first_contact_person_nm","contract_dt","wedding_dt","grmls_kj","grmfs_kj","brdls_kj","brdfs_kj"),
  	    		                            "conditions"=>array("SUBSTR(contract_dt,1,7)"=>$contract_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("first_contact_person_nm","wedding_dt")));

  	    for($i=0;$i < count($data);$i++){
  	    	$estimate_amount = $estimate->getCustomerEstimateSummary($data[$i]["ContractTrnView"]["customer_id"]);
  	    	$data[$i]["ContractTrnView"]["estimate_amount"] = $estimate_amount["total"];
  	    	$data[$i]["ContractTrnView"]["estimate_profit"] = $estimate_amount["profit"];
    		$data[$i]["ContractTrnView"]["estimate_profit_rate"] = $estimate_amount["profit_rate"];
  	    }
  	    return $data;
    }

    /**
     * 挙式日ベースの担当者リストを取得する
     * @param unknown $contract_dt
     * @return unknown
     */
    function GetAllAttendantStateOfWedding($wedding_dt){

    	App::import("Model", "ContractTrnView");
    	$contract = new ContractTrnView();

    	App::import("Model", "EstimateService");
    	$estimate = new EstimateService();

    	$data = $contract->find("all",array("fields"=>array("customer_id","status_nm","process_person_nm","contract_dt","wedding_dt","grmls_kj","grmfs_kj","brdls_kj","brdfs_kj"),
    			"conditions"=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED)),"order"=>array("process_person_nm","wedding_dt")));

    	for($i=0;$i < count($data);$i++){
    		$estimate_amount = $estimate->getCustomerEstimateSummary($data[$i]["ContractTrnView"]["customer_id"]);
    		$data[$i]["ContractTrnView"]["estimate_amount"] = $estimate_amount["total"];
    		$data[$i]["ContractTrnView"]["estimate_profit"] = $estimate_amount["profit"];
    		$data[$i]["ContractTrnView"]["estimate_profit_rate"] = $estimate_amount["profit_rate"];
    	}
    	return $data;
    }

    /**
     *
     * ユニークな全成約年月を取得する
     * @return 正常：成約年月の配列
     *         異常：NULL
     */
    function getGroupOfContractMonth(){
    	$sql = "SELECT SUBSTR(contract_dt,1,7) contract_dt FROM contract_trns GROUP BY SUBSTR(contract_dt,1,7) Order by SUBSTR(contract_dt,1,7) desc";
    	$data = $this->query($sql);

    	$months = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($months, $data[$i][0]['contract_dt']);
    	}
    	return $months;
    }

    /**
     *
     * ユニークな全挙式年月を取得する
     * @return 正常：挙式年月の配列
     *         異常：NULL
     */
    function getGroupOfWeddingMonth(){
    	$sql = "SELECT SUBSTR(wedding_dt,1,7) wedding_dt FROM contract_trns GROUP BY SUBSTR(wedding_dt,1,7) Order by SUBSTR(wedding_dt,1,7) desc";
    	$data = $this->query($sql);

    	$months = array();
    	for($i=0;$i < count($data);$i++){
    		array_push($months, $data[$i][0]['wedding_dt']);
    	}
    	return $months;
    }
}
?>