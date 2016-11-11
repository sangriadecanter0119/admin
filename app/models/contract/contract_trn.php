<?php
class ContractTrn extends AppModel {
    var $name = 'ContractTrn';

  /**
   *
   * 契約テーブルにデータを新規追加する
   * @param $estimate_id
   * @param $customer_id
   * @param $wedding_dt
   * @param $wedding_place
   * @param $reception_place
   * @param $user_name
   * @return 正常：新規データのID　
   *         異常：
   */
  function createNew($estimate_id,$customer_id,$contract_dt=null,$wedding_date=null,$wedding_place=null,$wedding_time=null,$reception_place=null,$reception_time=null,$user_name){

  	//シングルクオートのエスケープ処理
  	$wedding_place = str_replace("'","''",$wedding_place);
  	$reception_place = str_replace("'","''",$reception_place);

 	$contract_data = array(
 	                       "customer_id"=>$customer_id,
 	                       "estimate_id"=>$estimate_id,
 			               "contract_dt"=>$contract_dt,
 	                       "wedding_dt"=>$wedding_date,
 	                       "wedding_place"=>$wedding_place,
 			               "wedding_time"=>$wedding_time,
 	                       "reception_place"=>$reception_place,
                  		   "reception_time"=>$reception_time,
 	                       "wedding_bg"=>2,
 						   "wedding_ad"=>0,
 						   "wedding_ch"=>0,
 						   "wedding_inf"=>0,
 	                       "reception_bg"=>2,
 						   "reception_ad"=>0,
 						   "reception_ch"=>0,
 						   "reception_inf"=>0,
 	                       "wedding_gst_total"=>2,
 	                       "reception_gst_total"=>2,
 	                       "reg_nm"=>$user_name,
 	                       "reg_dt"=>date('Y-m-d H:i:s')
 	                       );
 	//フィールドの初期化
    $this->create();
    if($this->save($contract_data)==false){
    	return array('result'=>false,'message'=>"契約テーブル作成に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true,'newID'=>$this->getLastInsertID());
  }

 /**
   *
   * 契約テーブルの挙式関連のデータを更新する
   * @param $estimate_id
   * @param $wedding_dt
   * @param $wedding_place
   * @param $reception_place
   * @param $user_name
   * @return 正常：TRUE　
   *         異常：
   */
  function updateWeddingInfo($estimate_id,$wedding_date=null,$wedding_place=null,$reception_place=null,$user_name){

  	//シングルクオートのエスケープ処理
  	$wedding_place = str_replace("'","''",$wedding_place);
  	$reception_place = str_replace("'","''",$reception_place);

 	$contract_fields = array(
 	                       "wedding_dt"=>   empty($wedding_date) ? null : "'$wedding_date'",
 	                       "wedding_place"=> "'{$wedding_place}'",
 	                       "reception_place"=>"'{$reception_place}'",
 	                       "upd_nm"=>"'{$user_name}'",
 	                       "upd_dt"=>"'".date('Y-m-d H:i:s')."'"
 	                       );

    if($this->updateAll($contract_fields,array("estimate_id"=>$estimate_id))==false){
    	return array('result'=>false,'message'=>"契約テーブル更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    }
    return array('result'=>true);
  }

  /**
   * 顧客ＩＤで 契約テーブルの挙式関連のデータを更新する
   * @param unknown $wedding_date
   * @param unknown $wedding_place
   * @param unknown $wedding_time
   * @param unknown $reception_place
   * @param unknown $reception_time
   * @param unknown $customer_id
   * @param unknown $user_name
   * @return multitype:boolean string |multitype:boolean
   */
  function updateWeddingInfoByCustomerId($wedding_date,$wedding_place,$wedding_time,$reception_place,$reception_time,$customer_id,$user_name){

  	//シングルクオートのエスケープ処理
  	$wedding_place = str_replace("'","''",$wedding_place);
  	$reception_place = str_replace("'","''",$reception_place);

  	$contract_fields = array(
  			"wedding_dt"=>    empty($wedding_date) ? null : "'{$wedding_date}'",
  			"wedding_place"=> "'{$wedding_place}'",
  			"wedding_time"=> "'{$wedding_time}'",
  			"reception_place"=>"'{$reception_place}'",
  			"reception_time"=>"'{$reception_time}'",
  			"upd_nm"=>"'{$user_name}'",
  			"upd_dt"=>"'".date('Y-m-d H:i:s')."'"
  					);

  	if($this->updateAll($contract_fields,array("customer_id"=>$customer_id))==false){
  		return array('result'=>false,'message'=>"契約テーブル更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);
  }

 /**
   *
   * フィナルシート用のフィールド項目を更新する
   * @param $contract_data
   * @param $username
   * @return 正常：TRUE　
   *         異常：FALSE
   */
  function updateForFinalSheet($contract_data,$username){

  	//シングルクオートのエスケープ処理
  	$contract_data['reception_place'] = str_replace("'","''",$contract_data['reception_place']);

   	 $fields = array('wedding_bg'  ,'wedding_ad'  ,'wedding_ch'    ,'wedding_inf'  ,'wedding_gst_total',
	                 'reception_bg','reception_ad','reception_ch'  ,'reception_inf','reception_gst_total',
	                 'wedding_time','reception_place','reception_time','upd_nm','upd_dt');

     if(empty($contract_data['wedding_dt'])){$contract_data['wedding_dt'] = null;}
   	 $contract_data['upd_nm'] = $username;
   	 $contract_data['upd_dt'] = date('Y-m-d H:i:s');

	 $this->id = $contract_data['id'];
     if($this->save($contract_data,false,$fields)==false){
     	return array('result'=>false,'message'=>"契約テーブル更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
     }
     return array('result'=>true);
  }

  /**
   * 成約日を更新する
   * @param unknown $contract_dt
   * @param unknown $customer_id
   * @param unknown $user_name
   * @return multitype:boolean string |multitype:boolean
   */
  function setContractedDate($contract_dt,$customer_id,$user_name){

  	if($this->updateAll(array('contract_dt'=>"'".$contract_dt."'",
 	                          'upd_dt'=>"'".date('Y-m-d H:i:s')."'",
 	                          'upd_nm'=>"'".$user_name."'"),array("customer_id"=>$customer_id))==false){
  		return array('result'=>false,'message'=>"仮約定日更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);
  }

  /**
   * 成約日をクリアする
   * @param unknown $customer_id
   * @param unknown $user_name
   * @return multitype:boolean string |multitype:boolean
   */
  function clearContractedDate($customer_id,$user_name){

  	if($this->updateAll(array('contract_dt'=>null,
  			'upd_dt'=>"'".date('Y-m-d H:i:s')."'",
  			'upd_nm'=>"'".$user_name."'"),array("customer_id"=>$customer_id))==false){
  			return array('result'=>false,'message'=>"仮約定日更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  	}
  	return array('result'=>true);
  }

  /**
   * 成約日を取得する
   * @param unknown $customer_id
   * @return NULL
   */
  function getContractedDateByCustomer($customer_id){

  	$data = $this->find('all',array('fields'=>array('contract_dt'),'conditions'=>array("customer_id"=>$customer_id)));
  	return count($data) > 0 ? $data[0]["ContractTrn"]["contract_dt"] : null;
  }
}
?>