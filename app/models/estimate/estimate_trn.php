<?php
class EstimateTrn extends AppModel {
    var $name = 'EstimateTrn';

  /**
   *
   * 見積ヘッダのデータを新規登録する
   * @param $estimate_data
   * @param $user_name
   * @return 正常：　 新規データのID　
   *         異常: False
   */
  function createNew($estimate_data,$user_name)
  {
    $estimate_data['reg_nm'] = $user_name;
    $estimate_data['reg_dt'] = date('Y-m-d H:i:s');
 	$estimate_data['id'] = null;
    //フィールドの初期化
 	$this->create();
 	if($this->save($estimate_data)==false){
 	   return array('result'=>false,'message'=>"見積ヘッダ登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	 return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

  /**
   *
   * クローンデータ作成
   * @param $estimate_id
   */
  function createClone($id)
  {
  	 $data = $this->findById($id);
  	 $data['EstimateTrn']['id'] = null;
  	 $data['EstimateTrn']['adopt_flg'] = ESTIMATE_UNADOPTED;
     //フィールドの初期化
 	 $this->create();
 	 if($this->save($data['EstimateTrn'])==false){
 	 	return array('result'=>false,'message'=>"見積ヘッダ登録に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
 	 return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

  /**
   *
   * 見積ヘッダのデータを更新する
   * @param $estimate_data
   * @param $estimate_id
   * @param $user_name
   * @return 正常：　 True　
   *         異常:
   */
  function update($estimate_data,$estimate_id,$user_name)
  {
    $estimate_data['upd_nm'] = $user_name;
    $estimate_data['upd_dt'] = date('Y-m-d H:i:s');
    $this->id = $estimate_id;

  	if($this->save($estimate_data)==false){
  		return array('result'=>false,'message'=>"見積ヘッダ更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
 	return array('result'=>true);
  }

 /**
   *
   * 見積ヘッダの送金為替レートを更新する
   * @param $estimate_data
   * @param $estimate_id
   * @param $user_name
   * @return 正常：　 True　
   *         異常:
   */
  function updateRemittanceExchangeRate($estimate_data,$user_name)
  {
  	$est_fields = array('remittance_exchange_rate','upd_nm','upd_dt');

    $estimate_data['upd_nm'] = $user_name;
    $estimate_data['upd_dt'] = date('Y-m-d H:i:s');
    $this->id = $estimate_data['id'];
 	if($this->save($estimate_data,false,$est_fields)==false){
 	  	return array('result'=>false,'message'=>"見積ヘッダの送金為替レート更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	}
 	return array('result'=>true);
  }

  /**
   * 採用済み見積の請求書発行日を設定する
   * @param unknown $customer_id
   * @param unknown $invoice_issued_dt
   * @return multitype:boolean string |multitype:boolean
   */
  function setInvoiceDate($customer_id,$invoice_issued_dt){

  	if($this->updateAll(array("invoice_issued_dt"=>"'".$invoice_issued_dt."'"),array("customer_id"=>$customer_id,"adopt_flg"=>ESTIMATE_ADOPTED))==false){
  		return array('result'=>false,'message'=>"請求書発行日の更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);
  }

  /**
   * 採用済み見積の請求書発行日をクリアする
   * @param unknown $customer_id
   */
  function clearInvoiceDate($customer_id){

  	if($this->updateAll(array("invoice_issued_dt"=>null),array("customer_id"=>$customer_id,"adopt_flg"=>ESTIMATE_ADOPTED))==false){
  		return array('result'=>false,'message'=>"請求書発行日のクリアにに失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);
  }

  /**
   *
   * 正式採用されている顧客の見積が存在するかチェック
   * @param $customer_id
   * @return 正常：　TRUE　
   *         異常: FALSE
   */
  function isAdoptedEstimateHadByCustomer($customer_id){
  	if($this->find('count',array('conditions'=>array('customer_id'=>$customer_id,'adopt_flg'=>ESTIMATE_ADOPTED)))==0){
  		return false;
  	}
  	return true;
  }

  /**
   * 見積が作成されているかチェック
   * @param unknown $customer_id
   * @return boolean
   */
  function DoesEstimateExists($customer_id){
  	if($this->find('count',array('conditions'=>array('customer_id'=>$customer_id)))==0){
  		return false;
  	}
  	return true;
  }

  /**
   * 仮採用に戻す
   * @param unknown $customer_id
   * @return multitype:boolean string
   */
  function setUnadopt($customer_id){

  	if($this->updateAll(array('adopt_flg'=>ESTIMATE_UNADOPTED),array('customer_id'=>$customer_id))==false){
  		return array('result'=>false,'message'=>"見積仮採用更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  }
 /**
   *
   * 引数の見積自身が正式採用されているかチェック
   * @param $estimate_id
   * @return 正常：　TRUE　
   *         異常: FALSE
   */
  function isAdopted($estimate_id){
  	if($this->find('count',array('conditions'=>array('id'=>$estimate_id,'adopt_flg'=>ESTIMATE_ADOPTED)))==0){
  		return false;
  	}
  	return true;
  }

  /**
   * 請求書発行日を取得する
   * @param unknown $customer_id
   * @return NULL
   */
  function getInvoiceIssuedDateByCustomer($customer_id){
  	$data = $this->find('all',array('fields'=>array('invoice_issued_dt'),'conditions'=>array('customer_id'=>$customer_id,'adopt_flg'=>ESTIMATE_ADOPTED)));
  	if(count($data)==0){ return null;}

  	return $data[0]['EstimateTrn']['invoice_issued_dt'];
  }

  /**
   *
   * 見積ヘッダの送金為替レートを更新する
   * @param $estimate_data
   * @param $estimate_id
   * @param $user_name
   * @return 正常：　 True　
   *         異常:
   */
  function updatePdfNote($estimate_id,$pdf_note)
  {
  	$est_fields = array('pdf_note');
  	$this->id = $estimate_id;
  	if($this->save(array('pdf_note'=>$pdf_note),false,$est_fields)==false){
  		return array('result'=>false,'message'=>"見積ヘッダの送金為替レート更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);
  }
 }
?>