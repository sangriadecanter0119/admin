<?php
class SalesManagementService extends AppModel {
	var $useTable = false;

	/**
	 * 売上一覧を取得する
	 * @param unknown $wedding_dt
	 * @return multitype:unknown
	 */
	function GetSalesList($start_wedding_dt,$end_wedding_dt){

		App::import("Model", "RemittanceTrnView");
		$remittance_trn_view = new RemittanceTrnView();

		App::import("Model", "RemittanceTrn");
		$remittance_trn = new RemittanceTrn();

		if(empty($start_wedding_dt) && empty($end_wedding_dt)){
			$data = $remittance_trn_view->find('all',
					array('conditions'=>
							array('status_id'=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>'wedding_dt'));
		}else if(empty($start_wedding_dt)){
			$data = $remittance_trn_view->find('all',
					array('conditions'=>
							array('SUBSTR(wedding_dt,1,7) <='=>$end_wedding_dt,
									'status_id'=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>'wedding_dt'));
		}else if(empty($end_wedding_dt)){
				$data = $remittance_trn_view->find('all',
						array('conditions'=>
								array('SUBSTR(wedding_dt,1,7) >='=>$start_wedding_dt,
										'status_id'=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>'wedding_dt'));
		}else{
			$data = $remittance_trn_view->find('all',
					array('conditions'=>
							array('SUBSTR(wedding_dt,1,7) >='=>$start_wedding_dt,'SUBSTR(wedding_dt,1,7) <='=>$end_wedding_dt,
									'status_id'=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED)),'order'=>'wedding_dt'));
		}

		$result = array();
		for($i=0; $i < count($data);$i++){

			$cal = $remittance_trn->calculateForSales($data[$i]['RemittanceTrnView']['estimate_id']);

			$cal['estimate_id'] =$data[$i]['RemittanceTrnView']['estimate_id'];
			$cal['customer_id'] =$data[$i]['RemittanceTrnView']['customer_id'];
			$cal['grmls_kj'] =$data[$i]['RemittanceTrnView']['grmls_kj'];
			$cal['grmfs_kj'] =$data[$i]['RemittanceTrnView']['grmfs_kj'];
			$cal['wedding_dt'] =$data[$i]['RemittanceTrnView']['wedding_dt'];

			$result[$i] = $cal;
		}
		return $result;
	}
}