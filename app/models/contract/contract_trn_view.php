<?php
class ContractTrnView extends AppModel {
  var $name = 'ContractTrnView';

  /**
   *
   * ステータスが請求済み移行の挙式年月に該当する見積IDを取得する
   * @param $wedding_dt
   */
  function getEstimateIdsByWeddingDateInInvoiced($wedding_dt){

  	/* 条件の挙式年月の見積もりIDを契約テーブルから取得する */
  	$contract = $this->find('all',array('conditions'=>array("SUBSTR(wedding_dt,1,7)"=>$wedding_dt,"status_id"=>array(CS_INVOICED,CS_PAIED,CS_UNPAIED))));
  	$estimate_ids = array();

  	for($i=0;$i < count($contract);$i++){
  		array_push($estimate_ids, $contract[$i]["ContractTrnView"]["estimate_id"]);
  	}
  	return $estimate_ids;
  }


  /**
   *
   * ステータスが請求済み移行のユニークな全挙式年月を取得する
   * @return 正常： 挙式年月の配列
   *         異常：NULL
   */
  function getGroupOfWeddingMonthInInvoiced(){
  	$sql = "SELECT SUBSTR(wedding_dt,1,7) wedding_dt FROM contract_trn_views WHERE status_id in (".CS_INVOICED.",".CS_PAIED.",".CS_UNPAIED.") ".
  	       "GROUP BY SUBSTR(wedding_dt,1,7) Order by SUBSTR(wedding_dt,1,7) desc";
  	$data = $this->query($sql);

  	$months = array();
  	for($i=0;$i < count($data);$i++){
  		array_push($months, $data[$i][0]['wedding_dt']);
  	}
  	return $months;
  }
}