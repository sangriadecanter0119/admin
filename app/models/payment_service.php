<?php
class PaymentService extends AppModel {
    var $useTable = false;

    /**
     *
     * 指定挙式年月の顧客の現地ベンダーへの支払一覧を取得する
     * @param $wedding_dt
     * @param $sort
     */
    function getEachCustomerPaymentByWeddingDate($wedding_dt,$sort=null){

     App::import("Model", "ContractTrnView");
  	 $contract = new ContractTrnView();
   	 App::import("Model", "EstimateDtlTrnView");
  	 $estimate_dtl = new EstimateDtlTrnView();

    //条件の挙式年月の見積もりIDを契約テーブルから取得する
 	$estimate_ids = $contract->getEstimateIdsByWeddingDateInInvoiced($wedding_dt);
    /* 該当する見積IDの見積もりを全て取得する */
   	$estimate_dtl_data = null;
 	switch ($sort) {
   		case "category":
   			  $estimate_dtl_data = $estimate_dtl->find("all",array("conditions"=>array("estimate_id"=>$estimate_ids),'order'=>'goods_ctg_id,goods_id'));
   			  $estimate_dtl_data = $this->_combineSetPayment($estimate_dtl_data);
   			   foreach($estimate_dtl_data as $cnt => $val){
                  $sort_key1[$cnt] = $val['EstimateDtlTrnView']['goods_ctg_id'];
               }
 	           foreach($estimate_dtl_data as $cnt => $val){
                  $sort_key2[$cnt] = $val['EstimateDtlTrnView']['goods_id'];
               }
               if(count($estimate_dtl_data) != 0){ array_multisort($sort_key1,SORT_ASC,$sort_key2, SORT_ASC, $estimate_dtl_data); }
   			   return $estimate_dtl_data;
   		default:
   			  $estimate_dtl_data = $estimate_dtl->find("all",array("conditions"=>array("estimate_id"=>$estimate_ids),'order'=>'estimate_id,no'));
   			  $estimate_dtl_data = $this->_combineSetPayment($estimate_dtl_data);

   			  foreach($estimate_dtl_data as $cnt => $val){
   			  	$sort_key1[$cnt] = $val['EstimateDtlTrnView']['wedding_dt'];
   			  }

   			  if(count($estimate_dtl_data) != 0){ array_multisort($sort_key1,SORT_ASC, $estimate_dtl_data); }
   			  return $estimate_dtl_data;
   	}
  }

  /**
   *
   * セット商品を組み合わせて配列を再構築する
   * @param $estimate_dtl_data
   */
  function _combineSetPayment($estimate_dtl_data){

  	App::import("Model", "SetGoodsEstimateDtlTrnView");
  	$set_estimate_dtl = new SetGoodsEstimateDtlTrnView();

  	App::import("Model", "ContractTrn");
  	$contract = new ContractTrn();

    $ret = array();
  	$counter=0;
    for($i=0;$i < count($estimate_dtl_data);$i++){

    	if($estimate_dtl_data[$i]["EstimateDtlTrnView"]['set_goods_kbn'] == SET_GOODS){
    		$set_estimate_dtl_data = $set_estimate_dtl->find("all",array("conditions"=>array("estimate_dtl_id"=>$estimate_dtl_data[$i]["EstimateDtlTrnView"]['id']),'order'=>'no'));

    		for($j=0;$j < count($set_estimate_dtl_data);$j++){
    			$contract_data = $contract->find('all',array('fields'=>array('wedding_dt'),'conditions'=>array('customer_id'=>$estimate_dtl_data[$i]["EstimateDtlTrnView"]['customer_id'])));
    			$ret[$counter]["EstimateDtlTrnView"]['wedding_dt']	= $contract_data[0]["ContractTrn"]['wedding_dt'];

    			$ret[$counter]["EstimateDtlTrnView"]['estimate_dtl_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['id'];
    			$ret[$counter]["EstimateDtlTrnView"]['estimate_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['estimate_id'];
                $ret[$counter]["EstimateDtlTrnView"]['customer_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['customer_id'];
                $ret[$counter]["EstimateDtlTrnView"]['grmls_kj']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['grmls_kj'];
                $ret[$counter]["EstimateDtlTrnView"]['payment_kbn_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['payment_kbn_id'];
                $ret[$counter]["EstimateDtlTrnView"]['set_estimate_dtl_id']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['id'];
                $ret[$counter]["EstimateDtlTrnView"]['goods_id']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['goods_id'];
                $ret[$counter]["EstimateDtlTrnView"]['goods_ctg_id']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['goods_ctg_id'];
                $ret[$counter]["EstimateDtlTrnView"]['goods_ctg_nm']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['goods_ctg_nm'];
                $ret[$counter]["EstimateDtlTrnView"]['goods_nm']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['sales_goods_nm'];
                $ret[$counter]["EstimateDtlTrnView"]['sales_cost']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['sales_cost'];
                $ret[$counter]["EstimateDtlTrnView"]['num']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['num'];
                $ret[$counter]["EstimateDtlTrnView"]['original_sales_cost']	=  $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['original_sales_cost'];
                $ret[$counter]["EstimateDtlTrnView"]['vendor_id']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['vendor_id'];
                $ret[$counter]["EstimateDtlTrnView"]['vendor_nm']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['vendor_nm'];
                $ret[$counter]["EstimateDtlTrnView"]['money_received_flg']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['money_received_flg'];

				$ret[$counter]["EstimateDtlTrnView"]['total_sales_cost']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['sales_cost'] * $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['num'];
				$ret[$counter]["EstimateDtlTrnView"]['total_original_sales_cost']	= $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['original_sales_cost'] * $set_estimate_dtl_data[$j]["SetGoodsEstimateDtlTrnView"]['num'];

				$counter++;
    		}
    	}else{

    	  $contract_data = $contract->find('all',array('fields'=>array('wedding_dt'),'conditions'=>array('customer_id'=>$estimate_dtl_data[$i]["EstimateDtlTrnView"]['customer_id'])));
    	  $ret[$counter]["EstimateDtlTrnView"]['wedding_dt']	= $contract_data[0]["ContractTrn"]['wedding_dt'];

      	  $ret[$counter]["EstimateDtlTrnView"]['estimate_dtl_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['id'];
    	  $ret[$counter]["EstimateDtlTrnView"]['estimate_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['estimate_id'];
          $ret[$counter]["EstimateDtlTrnView"]['customer_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['customer_id'];
          $ret[$counter]["EstimateDtlTrnView"]['grmls_kj']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['grmls_kj'];
          $ret[$counter]["EstimateDtlTrnView"]['payment_kbn_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['payment_kbn_id'];
          $ret[$counter]["EstimateDtlTrnView"]['set_estimate_dtl_id'] = null;
          $ret[$counter]["EstimateDtlTrnView"]['goods_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['goods_id'];
          $ret[$counter]["EstimateDtlTrnView"]['goods_ctg_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['goods_ctg_id'];
          $ret[$counter]["EstimateDtlTrnView"]['goods_ctg_nm']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['goods_ctg_nm'];
          $ret[$counter]["EstimateDtlTrnView"]['goods_nm']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['sales_goods_nm'];
          $ret[$counter]["EstimateDtlTrnView"]['sales_cost']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['sales_cost'];
          $ret[$counter]["EstimateDtlTrnView"]['num']	        = $estimate_dtl_data[$i]["EstimateDtlTrnView"]['num'];
          $ret[$counter]["EstimateDtlTrnView"]['original_sales_cost']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['original_sales_cost'];
          $ret[$counter]["EstimateDtlTrnView"]['vendor_id']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['vendor_id'];
          $ret[$counter]["EstimateDtlTrnView"]['vendor_nm']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['vendor_nm'];
          $ret[$counter]["EstimateDtlTrnView"]['money_received_flg']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['money_received_flg'];

			$ret[$counter]["EstimateDtlTrnView"]['total_sales_cost']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['sales_cost'] * $estimate_dtl_data[$i]["EstimateDtlTrnView"]['num'];
			$ret[$counter]["EstimateDtlTrnView"]['total_original_sales_cost']	= $estimate_dtl_data[$i]["EstimateDtlTrnView"]['original_sales_cost'] * $estimate_dtl_data[$i]["EstimateDtlTrnView"]['num'];
    	  $counter++;
    	}
    }
	  $margin = array();
	  for($i=0; $i < count($ret);$i++){

		  $found = false;
		  for($j=0;$j < count($margin);$j++){
			  if($ret[$i]['EstimateDtlTrnView']['customer_id'] == $margin[$j]['EstimateDtlTrnView']['customer_id'] &&
				 $ret[$i]['EstimateDtlTrnView']['goods_id'] == $margin[$j]['EstimateDtlTrnView']['goods_id'] &&
				  $ret[$i]["EstimateDtlTrnView"]['payment_kbn_id'] == $margin[$j]["EstimateDtlTrnView"]['payment_kbn_id']){
				  $margin[$j]["EstimateDtlTrnView"]['total_sales_cost']	+= $ret[$i]["EstimateDtlTrnView"]['total_sales_cost'];
				  $margin[$j]["EstimateDtlTrnView"]['total_original_sales_cost']	+= $ret[$i]["EstimateDtlTrnView"]['total_original_sales_cost'];
				  $found = true;
				  break;
			  }
		  }
		  if($found == false){
			  $index = count($margin);
			  $margin[$index]["EstimateDtlTrnView"]['wedding_dt']	= $ret[$i]["EstimateDtlTrnView"]['wedding_dt'];
			  $margin[$index]["EstimateDtlTrnView"]['estimate_dtl_id']	= $ret[$i]["EstimateDtlTrnView"]['estimate_dtl_id'];
			  $margin[$index]["EstimateDtlTrnView"]['estimate_id']	= $ret[$i]["EstimateDtlTrnView"]['estimate_id'];
			  $margin[$index]["EstimateDtlTrnView"]['customer_id']	= $ret[$i]["EstimateDtlTrnView"]['customer_id'];
			  $margin[$index]["EstimateDtlTrnView"]['grmls_kj']	= $ret[$i]["EstimateDtlTrnView"]['grmls_kj'];
			  $margin[$index]["EstimateDtlTrnView"]['payment_kbn_id']	= $ret[$i]["EstimateDtlTrnView"]['payment_kbn_id'];
			  $margin[$index]["EstimateDtlTrnView"]['set_estimate_dtl_id'] = $ret[$i]["EstimateDtlTrnView"]['set_estimate_dtl_id'];
			  $margin[$index]["EstimateDtlTrnView"]['goods_id']	= $ret[$i]["EstimateDtlTrnView"]['goods_id'];
			  $margin[$index]["EstimateDtlTrnView"]['goods_ctg_id']	= $ret[$i]["EstimateDtlTrnView"]['goods_ctg_id'];
			  $margin[$index]["EstimateDtlTrnView"]['goods_ctg_nm']	= $ret[$i]["EstimateDtlTrnView"]['goods_ctg_nm'];
			  $margin[$index]["EstimateDtlTrnView"]['goods_nm']	= $ret[$i]["EstimateDtlTrnView"]['goods_nm'];
			  $margin[$index]["EstimateDtlTrnView"]['sales_cost']	= $ret[$i]["EstimateDtlTrnView"]['sales_cost'];
			  $margin[$index]["EstimateDtlTrnView"]['num']	        = $ret[$i]["EstimateDtlTrnView"]['num'];
			  $margin[$index]["EstimateDtlTrnView"]['original_sales_cost']	= $ret[$i]["EstimateDtlTrnView"]['original_sales_cost'];
			  $margin[$index]["EstimateDtlTrnView"]['vendor_id']	= $ret[$i]["EstimateDtlTrnView"]['vendor_id'];
			  $margin[$index]["EstimateDtlTrnView"]['vendor_nm']	= $ret[$i]["EstimateDtlTrnView"]['vendor_nm'];
			  $margin[$index]["EstimateDtlTrnView"]['money_received_flg']	= $ret[$i]["EstimateDtlTrnView"]['money_received_flg'];
			  $margin[$index]["EstimateDtlTrnView"]['total_sales_cost']	= $ret[$i]["EstimateDtlTrnView"]['total_sales_cost'];
			  $margin[$index]["EstimateDtlTrnView"]['total_original_sales_cost']	= $ret[$i]["EstimateDtlTrnView"]['total_original_sales_cost'];
		  }
	  }
    return $margin;
  }

  /**
   *
   * @param unknown $data
   * @param unknown $user_name
   * @return multitype:boolean string |multitype:boolean
   */
  function updatePayment($data,$user_name){

  	$tr = ClassRegistry::init('TransactionManager');
  	$tr->begin();

  	App::import("Model", "EstimateDtlTrn");
  	$estimate = new EstimateDtlTrn();

  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_estimate = new SetGoodsEstimateDtlTrn();

 	//見積明細テーブルの更新
    $est_dtl_fields = array('sales_cost','money_received_flg','upd_nm','upd_dt');
    $set_est_dtl_fields = array('sales_cost','money_received_flg','upd_nm','upd_dt');

    for($i=0;$i < count($data);$i++){

       //3桁区切りのカンマを除去
       $data[$i]['sales_cost']  = str_replace(",","",$data[$i]['sales_cost']) / $data[$i]['num'];

       //セット商品見積IDがセットされていない場合は単品商品
       if(empty($data[$i]['set_estimate_dtl_id'])){

 	        $data[$i]['upd_nm'] = $user_name;
 	        $data[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	        $estimate->id = $data[$i]['estimate_dtl_id'];
 	        if($estimate->save($data[$i],false,$est_dtl_fields)==false){
 	          return array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	        }
 	   }else{
 	     	$data[$i]['upd_nm'] = $user_name;
 	     	$data[$i]['upd_dt'] = date('Y-m-d H:i:s');
 	     	$set_estimate->id = $data[$i]['set_estimate_dtl_id'];

 	   	    if($set_estimate->save($data[$i],false,$set_est_dtl_fields)==false){
 	   		  return array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$set_estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	       	}
 	   }
    }

    /*セット商品の原価と入金フラグの再設定
                ①セット商品を構成する商品の原価が変わるとセット商品としての原価も変わるため原価を再計算する
                ②セット商品を構成する商品すべてが入金済みとなった場合はセット商品も入金済みとする。
                   逆に１つでも入金されてない構成商品がある場合はセット商品としては入金済みとしない。
    */
    $ret = $this->_recontructSetGoodsOfSalesCostAndPayment($data);
    if($ret['result']==false){  return $ret; }

    $tr->commit();
    return array('result'=>true,'message'=>"更新に成功しました。");
  }

  /**
   * 見積のセット商品の原価と入金フラグを再設定する
   * @param unknown $data
   * @return multitype:boolean string
   */
  function _recontructSetGoodsOfSalesCostAndPayment($data){

  	App::import("Model", "EstimateDtlTrn");
  	$estimate = new EstimateDtlTrn();

  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_estimate = new SetGoodsEstimateDtlTrn();

  	/* セット商品の見積明細IDのみ抽出  */
  	$estimate_dtl_ids = array();
  	$index = 0;
  	for($i=0;$i < count($data);$i++){
  		//セット商品見積IDがセットされている場合はセット商品
  		if(!empty($data[$i]['set_estimate_dtl_id'])){ $estimate_dtl_ids[$index++] = $data[$i]['estimate_dtl_id']; }
  	}

  	/* ユニークな見積明細IDを取得 */
  	$estimate_dtl_unique_ids = array_unique($estimate_dtl_ids);
  	//このままだとキーが順番に０から振られなていないので振り直す
  	sort($estimate_dtl_unique_ids);

  	/* セット商品の原価のクリア */
    for($i=0;$i < count($estimate_dtl_unique_ids);$i++){

 	   $estimate->id = $estimate_dtl_unique_ids[$i];
       if($estimate->save(array('sales_cost'=>0),false)==false){
 	   	  return array('result'=>false,'message'=>"セット商品の原価の初期化に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	   }
  	}

  	/* セット商品の原価と入金フラグを更新 */
  	for($i=0;$i < count($estimate_dtl_unique_ids);$i++){

  		$total_sales_cost = $this->_getSumOfSalesCostOfSetGoods($estimate_dtl_unique_ids[$i]);
  		$money_received_flg = $this->_isAllSetGoodsPaid($estimate_dtl_unique_ids[$i]) ? 1 : 0;
  		$estimate->id = $estimate_dtl_unique_ids[$i];
  		if($estimate->save(array('sales_cost'=>$total_sales_cost,'money_received_flg'=>$money_received_flg),false)==false){
  			return array('result'=>false,'message'=>"セット商品の更新に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
  		}
  	}
  	return array('result'=>true,'message'=>"セット商品の再構成に成功しました。");
  }

  /**
   * 見積のセット商品の構成商品の原価合計を取得する
   * @param unknown $estimate_dtl_id
   * @return boolean
   */
  function _getSumOfSalesCostOfSetGoods($estimate_dtl_id){

  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_estimate = new SetGoodsEstimateDtlTrn();

  	$data = $set_estimate->find('all',array('conditions'=>array('estimate_dtl_id'=>$estimate_dtl_id)));

  	$total_sales_cost = 0;
  	for($i=0; $i < count($data);$i++){
  		$total_sales_cost += $data[$i]['SetGoodsEstimateDtlTrn']['sales_cost'];
  	}
  	return $total_sales_cost;
  }

  /**
   * 見積のセット商品の構成商品全てが入金済みであるこをチェックする
   * @param unknown $estimate_dtl_id
   * @return boolean
   */
  function _isAllSetGoodsPaid($estimate_dtl_id){

  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_estimate = new SetGoodsEstimateDtlTrn();

  	$data = $set_estimate->find('all',array('conditions'=>array('estimate_dtl_id'=>$estimate_dtl_id)));

  	$payment_count = 0;
  	for($i=0; $i < count($data);$i++){
  		if($data[$i]['SetGoodsEstimateDtlTrn']['money_received_flg'] == 1){
  			$payment_count++;
  		}
  	}
  	return count($data) == $payment_count ? true : false;
  }

  /**
   * [開発用]見積明細でセット商品の構成商品のオリジナル代価と原価を商品マスタの価格でセットしなおす
   * @return multitype:boolean string
   */
  function updateSetGoodsOriginalPrice(){

  	$tr = ClassRegistry::init('TransactionManager');
  	$tr->begin();

  	App::import("Model", "SetGoodsEstimateDtlTrn");
  	$set_estimate = new SetGoodsEstimateDtlTrn();

  	$data = $set_estimate->find('all',array('fields'=>array('id','sales_price','sales_cost')));
  	$counter=0;

  	for($i=0;$i < 1500;$i++){
    //for($i=1500;$i < count($data);$i++){


  		$set_dtl_fields = array('original_sales_price','original_sales_cost');
  		$new_data = array();
  		$new_data['original_sales_price'] = $data[$i]['SetGoodsEstimateDtlTrn']['sales_price'];
  		$new_data['original_sales_cost']  = $data[$i]['SetGoodsEstimateDtlTrn']['sales_cost'];
  	    $set_estimate->id = $data[$i]['SetGoodsEstimateDtlTrn']['id'];

 	    if($set_estimate->save($new_data,false,$set_dtl_fields)==false){
 	        return array('result'=>false,'message'=>"更新に失敗しました。",'reason'=>$set_estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	    }
 	    $counter++;
  	}
  	$tr->commit();
  	return array('result'=>true,'message'=>"更新に成功しました。".$counter);
  }
}
?>
