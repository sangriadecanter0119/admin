<?php
/**
 *
 * 見積を管理する
 * @author takano yohei
 *
 */
class EstimateService extends AppModel {
    var $useTable = false;

    /**
     *
     * 正式見積を登録する
     * @param $estimate_data
     * @param $customer_id
     * @param $username
     * @return 正常：TRUE
     *         異常：
     */
    function registerFormally($estimate_data,$customer_id,$username){

    	App::import("Model", "EstimateTrn");
        App::import("Model", "EstimateDtlTrn");
        App::import("Model", "ContractTrn");
        App::import("Model", "FundManagementTrn");
        App::import("Model", "RemittanceTrn");
        App::import("Model", "CustomerMst");
        App::import("Model", "FinalSheetService");
        App::import("Model", "CreditService");
        App::import("Model", "StatusManager");

        $estimate = new EstimateTrn();
        $estimate_dtl = new EstimateDtlTrn();
        $contract = new ContractTrn();
        $fund_management = new FundManagementTrn();
        $remittance = new RemittanceTrn();
        $customer = new CustomerMst();
        $final_sheet = new FinalSheetService();
        $credit = new CreditService();
        $status_manager = new StatusManager();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	     //仮約定時条件を満たしていない場合は採用登録できない
	     if($status_manager->IsFilledContractingStatus($customer_id)==false){
	     	return array('result'=>false,'message'=>"新規担当者、プラン担当者、挙式日及び挙式時間又はレセプション時間が入力されていません。",'reason'=>'');
	     }

	    //複数禁止カテゴリの存在チェック
	    $final_sheet->checkIfDuplicatedCategory($estimate_data['EstimateDtlTrn']);

	    $contract_dt = null;
	    /** 既に正式採用されている場合は関連テーブルのデータを全て削除し、見積採用フラグをリセットする  **/
	    if($estimate->isAdoptedEstimateHadByCustomer($customer_id)){

 	      /** 見積採用フラグリセット **/
 	      if($estimate->updateAll(array('adopt_flg'=>ESTIMATE_UNADOPTED),array('customer_id'=>$customer_id))==false){
 	        return array('result'=>false,'message'=>"見積仮採用更新に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	      }

 	      /* 成約ステータス以降の場合は成約日を保持しておく */
 	      $contract_dt = $contract->getContractedDateByCustomer($customer_id);

	      /**  契約テーブル、資金管理テーブル、送金テーブルのデータを削除(カスケード削除) **/
 	      if($contract->deleteAll(array('customer_id'=>$customer_id),true)==false){
 	         return array('result'=>false,'message'=>"契約テーブルカスケード削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	      }
 	      /** ファイナルシートの全削除 **/
 	      $ret = $final_sheet->deleteAllFinalSheet($customer_id);
 	      if($ret['result']==false){ return $ret; }
	    }

        /** 見積ヘッダの作成 **/
	    //見積正式採用
        $estimate_data['EstimateTrn']['adopt_flg'] =  ESTIMATE_ADOPTED;
        $ret = $estimate->createNew($estimate_data['EstimateTrn'], $username);
         if($ret['result']==false){ return $ret; }
 	     $last_estimate_id = $ret['newID'];

 	    /** 見積明細の作成 **/
 	    $ret =  $estimate_dtl->createNew($estimate_data['EstimateDtlTrn'], $last_estimate_id, $username);
 	    if($ret['result']==false){ return $ret; }

 	    /** ファイナルシート作成 **/
 	    $ret = $final_sheet->createFinalSheetByEstimateId($last_estimate_id, $customer_id, $username);
 	    if($ret['result']==false){ return $ret; }

    	/** 契約の作成 **/
 	    //挙式会場とレセプション会場はファイナルシートに登録されている値を優先する
 	    $goods_weddding_place = $final_sheet->GetWeddingPlace($customer_id);
 	    $goods_reception_place = $final_sheet->GetReceptionPlace($customer_id);
 	    $wedding_basic_info = $customer->getWeddingBasicInfo($customer_id);

 	    $weddding_date = $wedding_basic_info['wedding_planned_dt'];
 	    $weddding_place  = empty($goods_weddding_place) ? $wedding_basic_info['wedding_planned_place'] : $goods_weddding_place;
 	    $weddding_time  = $wedding_basic_info['wedding_planned_time'];
 	    $reception_place =  empty($goods_reception_place) ? $wedding_basic_info['reception_planned_place'] : $goods_reception_place;
 	    $reception_time =  $wedding_basic_info['reception_planned_time'];

 	    $ret = $contract->createNew($last_estimate_id,$customer_id,$contract_dt,$weddding_date,$weddding_place,$weddding_time, $reception_place,$reception_time, $username);
        if($ret['result']==false){ return $ret; }
 	    $last_contract_id = $ret['newID'];

        /** 資金管理作成 **/
 	    $prepaid = $credit->getPrepaidAmount($customer_id);
 	    $ret = $fund_management->createNew($last_contract_id,$prepaid['amount'],$prepaid['credit_dt'], $username);
        if($ret['result']==false){ return $ret; }

        /** 送金管理作成 **/
 	    $ret = $remittance->createNew($last_contract_id, $username);
 	    if($ret['result']==false){ return $ret; }
 	    $remittance->calculate($last_estimate_id, $username);

        /** 顧客ステータスが「見積提出済」なら[仮約定]に移行 **/
 	    if($customer->getCustomerStatus($customer_id) == CS_ESTIMATED){

 	      //仮約定日設定
 	      $ret = $customer->setContractingDate(date('Y-m-d H:i:s'),$customer_id,$username);
 	      if($ret['result']==false){ return $ret; }

     	  $ret = $customer->setContracting($customer_id, $username);
     	  if($ret['result']==false){ return $ret; }
 	    }

      $tr->commit();
 	  return array('result'=>true);
    }

    /**
     *
     * 仮見積を登録する
     * @param $estimate_data
     * @param $username
     * @return 正常：TRUE
     *         異常：例外
     */
    function registerTemporally($estimate_data,$username){

      App::import("Model", "EstimateTrn");
      App::import("Model", "EstimateDtlTrn");
      App::import("Model", "CustomerMst");

      $estimate = new EstimateTrn();
      $estimate_dtl = new EstimateDtlTrn();
      $customer = new CustomerMst();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	  $customer_id = $estimate_data['EstimateTrn']['customer_id'];

      //見積ヘッダの作成
      $estimate_data['EstimateTrn']['adopt_flg'] =  ESTIMATE_UNADOPTED;
      $ret = $estimate->createNew($estimate_data['EstimateTrn'], $username);
 	  if($ret['result'] == false){return $ret;}
 	  //見積明細の作成
 	  $ret = $estimate_dtl->createNew($estimate_data['EstimateDtlTrn'],$ret['newID'], $username);
      if($ret['result'] == false){return $ret;}

      //初回見積作成時の場合はステータスを「見積提示済み」に変更する
      if($customer->getCustomerStatus($customer_id) == CS_CONTACT){
      	 $ret = $customer->setEstimateIssuedDate(date('Y-m-d H:i:s'),$customer_id,$username);
      	 if($ret['result'] == false){return $ret;}

      	 $ret = $customer->setEstimated($customer_id,$username);
      	 if($ret['result'] == false){return $ret;}
      }

 	  $tr->commit();
 	  return array('result'=>true);
    }

    /**
     *
     * 見積テンプレートの追加
     * @param $template_estimate_data
     * @param $username
     */
    function registerTemplate($template_estimate_data,$username){

      App::import("Model", "TemplateEstimateTrn");
      App::import("Model", "TemplateEstimateDtlTrn");

      $template_estimate = new TemplateEstimateTrn();
      $template_estimate_dtl = new TemplateEstimateDtlTrn();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

      //テンプレート見積ヘッダの作成
      $ret = $template_estimate->createNew($template_estimate_data['EstimateTrn'], $username);
      if($ret['result']==false){return $ret;}
 	  //テンプレート見積明細の作成
 	  $ret = $template_estimate_dtl->createNew($template_estimate_data['EstimateDtlTrn'], $ret['newID'], $username);
      if($ret['result']==false){return $ret;}

 	  $tr->commit();
 	  return array('result'=>true);
    }

    /**
     *
     * 見積テンプレートの削除
     * @param $id
     */
    function deleteTemplate($id){

      App::import("Model", "TemplateEstimateTrn");

      $template_estimate = new TemplateEstimateTrn();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

      //テンプレート見積ヘッダ・詳細削除[カスケード削除]
 	  if($template_estimate->delete($id,true)==false){
 	  	return array('result'=>false,'message'=>"見積テンプレートの削除に失敗しました。",'reason'=>$template_estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	  }
 	  $tr->commit();
 	  return array('result'=>true);
    }

    /**
     *
     * 見積テンプレートの更新
     * @param $id
     * @param $template_estimate_data
     * @param $username
     */
    function updateTemplate($id,$template_estimate_data,$username){

       App::import("Model", "TemplateEstimateDtlTrn");
       $template_estimate_dtl = new TemplateEstimateDtlTrn();

       App::import("Model", "TemplateEstimateTrn");
       $template_estimate = new TemplateEstimateTrn();

       $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();

	   //テンプレート見積ヘッダの更新
	   $ret = $template_estimate->update($template_estimate_data['EstimateDtlTrn'],$id, $username);
       if($ret['result']==false){return $ret;}

 	  //テンプレート見積明細の作成
       if($template_estimate_dtl->deleteAll(array("template_estimate_id"=>$id))==false){
       	 return array('result'=>false,'message'=>"見積テンプレート削除に失敗しました。",'reason'=>$template_estimate_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
       }

	   $ret = $template_estimate_dtl->createNew($template_estimate_data['EstimateDtlTrn'], $id, $username);
       if($ret['result']==false){return $ret;}

      $tr->commit();
 	  return array('result'=>true);
    }

    /**
     *
     * 正式見積を更新する
     * @param $estimate_data
     * @param $estimate_id
     * @param $customer_id
     * @param $username
     * @return 正常：TRUE
     *         異常：例外
     */
    function updateFormally($estimate_data,$estimate_id,$customer_id,$username){

      App::import("Model", "EstimateTrn");
      App::import("Model", "EstimateDtlTrn");
      App::import("Model", "ContractTrn");
      App::import("Model", "FundManagementTrn");
      App::import("Model", "RemittanceTrn");
      App::import("Model", "CustomerMst");
      App::import("Model", "FinalSheetService");
      App::import("Model", "CreditService");
      App::import("Model", "StatusManager");

      $estimate = new EstimateTrn();
      $estimate_dtl = new EstimateDtlTrn();
      $contract = new ContractTrn();
      $fund_management = new FundManagementTrn();
      $remittance = new RemittanceTrn();
      $customer = new CustomerMst();
      $final_sheet = new FinalSheetService();
      $credit = new CreditService();
      $status_manager = new StatusManager();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	  //仮約定時条件を満たしていない場合は採用登録できない
	  if($status_manager->IsFilledContractingStatus($customer_id)==false){
	  	return array('result'=>false,'message'=>"新規担当者、プラン担当者、挙式日及び挙式時間又はレセプション時間がが入力されていません。",'reason'=>'');
	  }

	  $customer_status = $customer->getCustomerStatus($customer_id);

	  //複数禁止カテゴリの存在チェック
	  $ret = $final_sheet->checkIfDuplicatedCategory($estimate_data['EstimateDtlTrn']);
      if($ret['result']==false){return $ret;}

      /** 正式採用済み見積データの更新  **/
	  if($estimate->isAdopted($estimate_id)){

	  	 //見積ヘッダの複製登録
	  	 $ret = $estimate->createClone($estimate_id);
	     if($ret['result']==false){return $ret;}
	     $new_estimate_id = $ret['newID'];

	     //見積明細の複製登録
	     $clone_data = $estimate_dtl->find('all',array('conditions'=>array('estimate_id'=>$estimate_id)));
	     for($i=0;$i < count($clone_data);$i++){
	       $ret = $estimate_dtl->createClone($clone_data[$i]['EstimateDtlTrn']['id'],$new_estimate_id);
	       if($ret['result']==false){return $ret;}
	     }

	  	 //見積ヘッダの更新
	     $ret = $estimate->update($estimate_data['EstimateTrn'],$estimate_id, $username);
	     if($ret['result']==false){return $ret;}

 	     //見積明細の作成・更新・削除
 	     $ret = $estimate_dtl->ManuplateAll($estimate_data['EstimateDtlTrn'], $estimate_id, $username);
 	     if($ret['result']==false){return $ret;}
	     $new_ids = $ret['info'];

 	     // ファイナルシート作成
 	     $final_sheet_id = $final_sheet->GetLatestFinalSheetIdBy($customer_id);
 	     if(array_key_exists('NewEstimateDtlId', $new_ids)){
     	     $ret = $final_sheet->createFinalSheetByEstimateDtlIds($new_ids['NewEstimateDtlId'], $customer_id, $username,$final_sheet_id);
     	     if($ret['result']==false){return $ret;}
 	     }

	     if(array_key_exists('UpdateEstimateDtlId', $new_ids)){
	     	$ret = $final_sheet->updateFinalSheetByEstimateDtlIds($new_ids['UpdateEstimateDtlId'], $customer_id, $username,$final_sheet_id);
     	     if($ret['result']==false){return $ret;}
 	     }

     	 if(array_key_exists('DeletedGoodsId', $new_ids)){

     	     $ret = $final_sheet->deleteFinalSheetIfHeaderOnly($new_ids['DeletedGoodsId'], $customer_id);
     	     if($ret['result']==false){return $ret;}
     	 }

     	 /** 契約の更新 **/
     	 //挙式会場とレセプション会場はファイナルシートに登録されている値を優先する
     	 $goods_weddding_place = $final_sheet->GetWeddingPlace($customer_id);
     	 $goods_reception_place = $final_sheet->GetReceptionPlace($customer_id);
     	 $wedding_basic_info = $customer->getWeddingBasicInfo($customer_id);

     	 $weddding_date = $wedding_basic_info['wedding_planned_dt'];
     	 $weddding_place  = empty($goods_weddding_place) ? $wedding_basic_info['wedding_planned_place'] : $goods_weddding_place;
     	 $reception_place =  empty($goods_reception_place) ? $wedding_basic_info['reception_planned_place'] : $goods_reception_place;

	     $ret = $contract->updateWeddingInfo($estimate_id,$weddding_date,$weddding_place,$reception_place, $username);
 	     if($ret['result']==false){return $ret;}

	  }
	  /** 初めて正式採用されるか既存の正式見積からの乗り換え **/
	  else{
	     //正式見積の乗り換えのケースに備えて一旦全ての正式採用フラグを仮採用に戻す
 	     if($estimate->updateAll(array('adopt_flg'=>ESTIMATE_UNADOPTED),array('customer_id'=>$customer_id))==false){
 	     	return array('result'=>false,'message'=>"見積ヘッダの仮採用フラグ更新に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
	     //見積ヘッダの更新
	     $estimate_data['EstimateTrn']['adopt_flg'] =  ESTIMATE_ADOPTED;
 	     $estimate->update($estimate_data['EstimateTrn'],$estimate_id, $username);
 	     //見積明細の作成・更新・削除
 	     $ret = $estimate_dtl->ManuplateAll($estimate_data['EstimateDtlTrn'], $estimate_id, $username);
 	     if($ret['result']==false){return $ret;}

	     // 契約テーブル、資金管理テーブル、送金管理テーブルのデータを削除(カスケード削除)
 	     if($contract->deleteAll(array('customer_id'=>$customer_id),true)==false){
 	     	return array('result'=>false,'message'=>"契約テーブル・資金管理テーブ・送金管理テーブルの削除に失敗しました。",'reason'=>$contract->getDbo()->error."[".date('Y-m-d H:i:s')."]");
 	     }
	     // ファイナルシートの全削除
 	     $ret = $final_sheet->deleteAllFinalSheet($customer_id);
 	     if($ret['result']==false){return $ret;}

 	     // ファイナルシート作成
 	     $ret = $final_sheet->createFinalSheetByEstimateId($estimate_id, $customer_id, $username);
 	     if($ret['result']==false){return $ret;}

    	// 契約の作成
        //挙式会場とレセプション会場はファイナルシートに登録されている値を優先する
 	    $goods_weddding_place = $final_sheet->GetWeddingPlace($customer_id);
 	    $goods_reception_place = $final_sheet->GetReceptionPlace($customer_id);
 	    $wedding_basic_info = $customer->getWeddingBasicInfo($customer_id);

 	    $weddding_date = $wedding_basic_info['wedding_planned_dt'];
 	    $weddding_place  = empty($goods_weddding_place) ? $wedding_basic_info['wedding_planned_place'] : $goods_weddding_place;
 	    $weddding_time  = $wedding_basic_info['wedding_planned_time'];
 	    $reception_place =  empty($goods_reception_place) ? $wedding_basic_info['reception_planned_place'] : $goods_reception_place;
 	    $reception_time =  $wedding_basic_info['reception_planned_time'];

 	    $ret = $contract->createNew($estimate_id,$customer_id, $weddding_date,$weddding_place,$weddding_time,$reception_place,$reception_time,$username);
 	    if($ret['result']==false){return $ret;}
 	    $last_contract_id = $ret['newID'];

        // 資金管理作成
        $ret = $fund_management->createNew($last_contract_id, $username);
        if($ret['result']==false){return $ret;}

        // 送金管理作成
 	    $prepaid = $credit->getPrepaidAmount($customer_id);
 	    $ret = $remittance->createNew($last_contract_id,$prepaid['amount'],$prepaid['credit_dt'], $username);

	     /** 顧客ステータスが「見積提出済」なら[仮約定]に移行 **/
 	    if($customer_status == CS_ESTIMATED){
 	      //仮約定日設定
 	      $ret = $customer->setContractingDate(date('Y-m-d H:i:s'),$customer_id,$username);
 	      if($ret['result']==false){ return $ret; }

     	  $ret = $customer->setContracting($customer_id, $username);
     	  if($ret['result']==false){ return $ret; }
 	    }
	  }

	  /**
	   * 1. 顧客ステータスが「挙式完了・入金済」の場合、商品の追加で請求額と入金額がずれるのでステータスを戻す
	   * 2. 顧客ステータスが「挙式完了・未入金」の場合、商品の削除や値段の変更で請求額と入金額が一致する場合があるのでステータスを更新する
	   **/
	  if($customer_status == CS_UNPAIED || $customer_status == CS_PAIED){

	  	if($credit->isInvoiceMatchForCredit($customer_id)){
	  	  $ret = $customer->setPaied($customer_id, $username);
	  	}else{
	  	  $ret = $customer->setUnpaied($customer_id, $username);
	  	}
	  	if($ret['result']==false){ return $ret; }
	  }


	  $ret = $remittance->calculate($estimate_id, $username);
	  if($ret['result']==false){return $ret;}

	  $tr->commit();
	  return array('result'=>true);
    }

    /**
     *
     * 仮見積を更新する
     * @param $estimate_data
     * @param $estimate_id
     * @param $username
     * @return 正常：TRUE
     *         異常：例外
     */
    function updateTemporally($estimate_data,$estimate_id,$username){

      App::import("Model", "EstimateTrn");
      App::import("Model", "EstimateDtlTrn");
      App::import("Model", "CustomerMst");

      $estimate = new EstimateTrn();
      $estimate_dtl = new EstimateDtlTrn();
      $customer = new CustomerMst();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

      //見積ヘッダの更新
 	  $ret = $estimate->update($estimate_data['EstimateTrn'],$estimate_id, $username);
 	  if($ret['result']==false){return $ret;}

 	  //見積明細の作成・更新・削除
 	  $ret = $estimate_dtl->ManuplateAll($estimate_data['EstimateDtlTrn'], $estimate_id, $username);
 	  if($ret['result']==false){return $ret;}

 	  $tr->commit();
 	  return array('result'=>true);
    }

    /**
     *
     * 見積データを複製して登録する
     * @param $estimate_data
     * @param $username
     * @return 正常：TRUE
     *         異常：
     */
    function copy($estimate_data,$username){
    	$ret = $this->registerTemporally($estimate_data,$username);
    	if($ret['result']==false){return $ret;}
    	return array('result'=>true);
    }

    /**
     * 見積に関連するすべてのテーブルのデータを削除して、正式採用の見積が存在しない場合は
     * 顧客ステータスを[見積提出済]に変更する
     * @param $estimate_id
     * @param $username
     * @return 正常：TRUE
     *         異常：
     */
    function deleteAll($estimate_id,$customer_id, $username){

      	App::import("Model", "EstimateTrn");
        App::import("Model", "CustomerMst");

        $estimate = new EstimateTrn();
        $customer = new CustomerMst();

      $tr = ClassRegistry::init('TransactionManager');
	  $tr->begin();

	    /* 採用済みの見積もりの削除の場合はファイナルシートも全て削除する */
        if($estimate->isAdopted($estimate_id)){
            App::import("Model", "FinalSheetService");
            $final_sheet = new FinalSheetService();
            $ret = $final_sheet->deleteAllFinalSheet($customer_id);
            if($ret['result']==false){return $ret;}

            $ret=$customer->setEstimated($customer_id,$username);
            if($ret['result']==false){return $ret;}
	    }

        /**
         *   見積,見積明細 ,ファイナルシート,契約 ,資金管理 ,送金管理データを削除(カスケード削除)
         *   *問い合わせテーブルは見積もり明細のIDを保持しているがリレーションは結んでいないので削除されない
         *    見積内容と問い合わせデータが必ずしも一致しないため(見積明細IDがない問い合わせもある)
         */
        if($estimate->delete($estimate_id,true)==false){
        	return array('result'=>false,'message'=>"見積削除に失敗しました。",'reason'=>$this->getDbo()->error."[".date('Y-m-d H:i:s')."]");
        }

        //見積がない場合はステータスを「問い合わせに」戻す
        if($estimate->DoesEstimateExists($customer_id)==false){
        	$ret = $customer->clearEstimateIssuedDate($customer_id,$username);
        	if($ret['result']==false){return $ret;}

        	$ret = $customer->setToiawase($customer_id,$username);
        	if($ret['result']==false){return $ret;}
        }
      $tr->commit();
      return array('result'=>true);
    }

    /**
     *
     * 送金画面明細用の更新
     * @param $estimate_data
     * @param $username
     */
    function updateForRemittance($estimate_data,$username){

       App::import("Model", "RemittanceTrn");
       $remittance = new RemittanceTrn();

       $tr = ClassRegistry::init('TransactionManager');
	   $tr->begin();
	    /* 支払区分の更新 */
	    $ret = $this->_updatePaymentKbn($estimate_data,$username);
    	if($ret['result']==false){return $ret;}
    	/* 送金為替レートの更新 */
    	$ret = $this->_updateRemittanceExchangeRate($estimate_data,$username);
    	if($ret['result']==false){return $ret;}
    	/* 送金金額の再計算 */
    	$ret = $remittance->calculate($estimate_data['EstimateTrn']['id'],$username);
        if($ret['result']==false){return $ret;}

       $tr->commit();
 	   return array('result'=>true);
    }

    /**
     *
     * 支払区分の更新
     * @param $estimate_data
     * @param $estimate_id
     * @param $username
     */
    function _updatePaymentKbn($estimate_data,$username){

       App::import("Model", "EstimateDtlTrn");
       $estimate_dtl = new EstimateDtlTrn();

 	  //見積明細の更新
	   if($estimate_dtl->UpdatePaymentKbn($estimate_data['EstimateDtlTrn'], $username)==false){
	     	return array('result'=>false,'message'=>"見積明細の支払区分の更新に失敗しました。",'reason'=>$estimate_dtl->getDbo()->error."[".date('Y-m-d H:i:s')."]");
	   }
 	   return array('result'=>true);
    }

   /**
     *
     * 見積ヘッダの送金為替レートを更新する
     * @param $estimate_data
     * @param $username
     */
    function _updateRemittanceExchangeRate($estimate_data,$username){

       App::import("Model", "EstimateTrn");
       $estimate = new EstimateTrn();

 	  //見積の更新
	   if($estimate->UpdateRemittanceExchangeRate($estimate_data['EstimateTrn'], $username) == false){
	     	return array('result'=>false,'message'=>"見積の送金為替レートの更新に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
	   }
 	   return array('result'=>true);
    }

    /**
     *  採用済み見積の請求書発行日を設定する
     * @param unknown $customer_id
     * @param unknown $invoice_issued_dt
     * @return multitype:boolean string |multitype:boolean
     */
    function setInvoiceDate($customer_id,$invoice_issued_dt){

    	App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();

    	if($estimate->setInvoiceDate($customer_id,$invoice_issued_dt)==false){
    		return array('result'=>false,'message'=>"請求書発行日の更新に失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	return array('result'=>true);
    }

    /**
     * 採用済み見積の請求書発行日をクリアする
     * @param unknown $customer_id
     */
    function clearInvoiceDate($customer_id){

    	App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();

    	if($estimate->clearInvoiceDate($customer_id)==false){
    		return array('result'=>false,'message'=>"請求書発行日のクリアに失敗しました。",'reason'=>$estimate->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}
    	return array('result'=>true);
    }

    /**
     * 請求書発行日を取得する
     * @param unknown $customer_id
     */
    function getInvoiceIssuedDateByCustomer($customer_id){
    	App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();
    	return $estimate->getInvoiceIssuedDateByCustomer($customer_id);
    }

    /**
     *
     * @param unknown $customer_id
     */
    function getContractedDateByCustomer($customer_id){
    	App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();
    	return $estimate->getInvoiceIssuedDateByCustomer($customer_id);
    }

    /**
     * 指定の顧客の全ての見積のサマリー情報を取得する
     * @param unknown $customer_id
     * @return NULL
     */
    function getCustomerAllEstimateSummary($customer_id){

    	App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();

    	App::import("Model", "EstimateDtlTrn");
    	$estimate_dtl = new EstimateDtlTrn();

    	$header = $estimate->find('all',array('conditions'=>array('customer_id'=>$customer_id),'order'=>array('reg_dt desc','upd_dt desc')));

    	$ret = array();
    	for($i=0; $i < count($header);$i++){

    		$line = $estimate_dtl->find('all',array('conditions'=>array('estimate_id'=>$header[$i]['EstimateTrn']['id'])));
    		$ret[$i]['yen'] = $this->_calculateEstimateSummaryOfYen($header[$i],$line);
    		$ret[$i]['dollar'] = $this->_calculateEstimateSummaryOfDollar($header[$i],$line);

    		$ret[$i]['basic'] = array(
    			"estimate_id"=>$header[$i]['EstimateTrn']['id'],
  				"reg_dt"=>$header[$i]['EstimateTrn']['reg_dt'],
   				"reg_nm"=>$header[$i]['EstimateTrn']['reg_nm'],
   				"upd_dt"=>$header[$i]['EstimateTrn']['upd_dt'],
   				"upd_nm"=>$header[$i]['EstimateTrn']['upd_nm'],
   				"adopt_flg"=>$header[$i]['EstimateTrn']['adopt_flg']
    		);
    	}
    	return $ret;
    }


    /**
     * 顧客の見積書の金額サマリーを取得する(円ベース)
     * @param unknown $customer_id
     * @return NULL|multitype:number unknown
     */
    function getCustomerEstimateSummary($customer_id){

        App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();

    	App::import("Model", "EstimateDtlTrn");
    	$estimate_dtl = new EstimateDtlTrn();

    	$header = $estimate->find('all',array('conditions'=>array('customer_id'=>$customer_id,'adopt_flg'=>ESTIMATE_ADOPTED)));

    	if(count($header)==0){ return null; }

    	$line = $estimate_dtl->find('all',array('conditions'=>array('estimate_id'=>$header[0]['EstimateTrn']['id'])));
        return $this->_calculateEstimateSummaryOfYen($header[0],$line);
    }

    /**
     * 顧客の見積書の金額サマリーの計算処理(ドルベース)
     * @param unknown $customer_id
     * @return NULL
     */
    function getCustomerEstimateSummaryOfDollar($customer_id){

    	App::import("Model", "EstimateTrn");
    	$estimate = new EstimateTrn();

    	App::import("Model", "EstimateDtlTrn");
    	$estimate_dtl = new EstimateDtlTrn();

    	$header = $estimate->find('all',array('conditions'=>array('customer_id'=>$customer_id,'adopt_flg'=>ESTIMATE_ADOPTED)));

    	if(count($header)==0){ return null; }

    	$line = $estimate_dtl->find('all',array('conditions'=>array('estimate_id'=>$header[0]['EstimateTrn']['id'])));

    	return $this->_calculateEstimateSummaryOfDollar($header[0],$line);
    }

    /**
     * 顧客の見積書の金額サマリーを計算処理(円ベース)
     * @param unknown $estimate_header_data
     * @param unknown $estimate_line_data
     * @return multitype:number unknown
     */
    function _calculateEstimateSummaryOfYen($estimate_header_data,$estimate_line_data){

    	$subtotal = 0;
    	$subtotal_for_tax = 0;
    	$tax = 0;
    	$service_fee = 0;
    	$discount1 = 0;
    	$discount2 = 0;
    	$total = 0;
    	$costTotal = 0;

    	for($i=0; $i < count($estimate_line_data);$i++){

    		$tmp = 0;

    		if($estimate_line_data[$i]['EstimateDtlTrn']['currency_kbn'] == FOREIGN){
    			$tmp = $estimate_line_data[$i]['EstimateDtlTrn']['num'] * round(($estimate_line_data[$i]['EstimateDtlTrn']['sales_price'] * $estimate_line_data[$i]['EstimateDtlTrn']['sales_exchange_rate']));
    			$costTotal += $estimate_line_data[$i]['EstimateDtlTrn']['num'] * round(($estimate_line_data[$i]['EstimateDtlTrn']['sales_cost'] * $estimate_line_data[$i]['EstimateDtlTrn']['cost_exchange_rate']));
    		}else{
    			$tmp = $estimate_line_data[$i]['EstimateDtlTrn']['num'] * $estimate_line_data[$i]['EstimateDtlTrn']['sales_price'];
    			$costTotal += $estimate_line_data[$i]['EstimateDtlTrn']['num'] * $estimate_line_data[$i]['EstimateDtlTrn']['sales_cost'];
    		}
    		$subtotal += $tmp;

    		if($estimate_line_data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_INDIRECT_ABOARD_PAY ||
    		   $estimate_line_data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_DIRECT_ABOARD_PAY ||
    		   $estimate_line_data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
    		  	 $subtotal_for_tax += $tmp;
    		}
    	}

    	$subtotal = round($subtotal);
    	$costTotal = round($costTotal);
    	$tax = round($subtotal_for_tax * $estimate_header_data['EstimateTrn']['hawaii_tax_rate']);
    	$service_fee = round($subtotal * $estimate_header_data['EstimateTrn']['service_rate']);
    	$discount1 = round(($subtotal + $tax + $service_fee) *  $estimate_header_data['EstimateTrn']['discount_rate']);
    	$discount2 = $estimate_header_data['EstimateTrn']['discount'];

    	$sum = $subtotal + $tax + $service_fee - $discount1 - $discount2;
    	return array('subtotal'=>$subtotal,
    			'tax'=>$tax,
    			'service_fee'=>$service_fee,
    			'discount_rate_fee'=>$discount1,
    			'discount_fee'=>$discount2,
    			'cost_total'=>$costTotal,
    			'total'=> $sum,
    			'profit' =>$subtotal - $costTotal,
    			'profit_rate'=> $subtotal > 0 ? round((($subtotal - $costTotal) / $subtotal) * 100 , 2) : 0
    	);
    }


    /**
     * 顧客の見積書の金額サマリーを計算処理(ドルベース)
     * @param unknown $estimate_header_data
     * @param unknown $estimate_line_data
     * @return multitype:number unknown
     */
    function _calculateEstimateSummaryOfDollar($estimate_header_data,$estimate_line_data){

    	$subtotal = 0;
    	$subtotal_for_tax = 0;
    	$tax = 0;
    	$service_fee = 0;
    	$discount1 = 0;
    	$discount2 = 0;
    	$total = 0;

    	for($i=0; $i < count($estimate_line_data);$i++){

    		$tmp = 0;

    		if($estimate_line_data[$i]['EstimateDtlTrn']['currency_kbn'] == DOMESTIC){
    			if($estimate_line_data[$i]['EstimateDtlTrn']['sales_exchange_rate'] > 0){
    				$tmp = ($estimate_line_data[$i]['EstimateDtlTrn']['num'] * ($estimate_line_data[$i]['EstimateDtlTrn']['sales_price']) / $estimate_line_data[$i]['EstimateDtlTrn']['sales_exchange_rate']);
    			}
    		}else{
    			$tmp = $estimate_line_data[$i]['EstimateDtlTrn']['num'] * $estimate_line_data[$i]['EstimateDtlTrn']['sales_price'];
    		}
    		$subtotal += $tmp;

    		if($estimate_line_data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_INDIRECT_ABOARD_PAY ||
    	   	   $estimate_line_data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_DIRECT_ABOARD_PAY ||
    		   $estimate_line_data[$i]['EstimateDtlTrn']['payment_kbn_id'] == PC_CREDIT_ABOARD_PAY){
    			$subtotal_for_tax += $tmp;
    		}
    	}

    	$subtotal = round($subtotal,2);
    	$tax = round($subtotal_for_tax * $estimate_header_data['EstimateTrn']['hawaii_tax_rate'],2);
    	$service_fee = round($subtotal * $estimate_header_data['EstimateTrn']['service_rate'],2);
    	$discount1 = round(($subtotal + $tax + $service_fee) *  $estimate_header_data['EstimateTrn']['discount_rate'],2);
    	if($estimate_header_data['EstimateTrn']['discount_exchange_rate'] > 0){
    		$discount2 = round($estimate_header_data['EstimateTrn']['discount'] / $estimate_header_data['EstimateTrn']['discount_exchange_rate'],2);
    	}
    	return array('subtotal'=>$subtotal,
    			'tax'=>$tax,
    			'service_fee'=>$service_fee,
    			'discount_rate_fee'=>$discount1,
    			'discount_fee'=>$discount2,
    			'total'=> $subtotal + $tax + $service_fee - $discount1 - $discount2
    	);
    }


    /**
     * 顧客基本情報の更新
     * @param unknown $estimate_data
     * @param unknown $username
     */
    function updateCustomerBasicInfo($customer_data,$username){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "CustomerMst");
    	$customer = new CustomerMst();

    	App::import("Model", "CreditService");
    	$credit = new CreditService();

    	App::import("Model", "ContractTrn");
    	$contract = new ContractTrn();

    	App::import("Model", "StatusManager");
    	$status_manager = new StatusManager();

    	$status_nm = $customer_data['CustomerMst']['status_nm'];
        $status_id = $customer_data['CustomerMst']['status_id'];

        //エラーチェック
        //if($customer_data['CustomerMst']['status_id'] == CS_ESTIMATED && empty($customer_data['CustomerMst']['estimate_issued_dt'])){
        //	return array('result'=>false,'message'=>"見積提出日を空白にすることはできません。",'reason'=>"");
        //}

    	//顧客コード作成
    	$customer_data['CustomerMst']['customer_cd'] = $customer->recreateCustomerCode($customer_data['CustomerMst']['customer_cd'],$customer_data['CustomerMst']['first_contact_dt'],$customer_data['CustomerMst']['wedding_planned_dt']);

    	//顧客情報の更新
    	if($customer->updateCustomerBasicInfo($customer_data['CustomerMst'], $username)==false){
    		return array('result'=>false,'message'=>"顧客基本情報の更新に失敗しました。",'reason'=>$customer->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}

    	/** 契約の作成 **/
    	//仮約定以降なら契約テーブルの挙式関連の情報を更新する
    	if($customer_data['CustomerMst']['status_id'] >= CS_CONTRACTING){

    	   $ret = $contract->updateWeddingInfoByCustomerId($customer_data['CustomerMst']['wedding_planned_dt']  ,
    	   		                                           $customer_data['CustomerMst']['wedding_planned_place']  ,
    	   		                                           $customer_data['CustomerMst']['wedding_planned_time'],
    		   	                                           $customer_data['CustomerMst']['reception_planned_place'],
    	   	                                               $customer_data['CustomerMst']['reception_planned_time'],
    			                                           $customer_data['CustomerMst']['id'], $username);
    	   if($ret['result']==false){ return $ret; }
    	}

    	//顧客ステータスの更新
    	/*
    	if($customer_data['CustomerMst']['status_id'] == CS_CONTACT){

    		if(!empty($customer_data['CustomerMst']['estimate_issued_dt'])){
    			$customer->setEstimated($customer_data['CustomerMst']['id'],$username);
    			$status_id = CS_ESTIMATED;
    			$status_nm ="見積提示済";
    		}
    	}else if($customer_data['CustomerMst']['status_id'] == CS_CONTRACTING){

    		//内金が入金済みで挙式日及び挙式時間またはレセプション時間が設定されていれば成約に移行
    		if($status_manager->IsFilledContractedStatus($customer_data['CustomerMst']['id'])){

    			//成約日を設定する
    			//お内金入金日を成約日に設定
    			$ret =$contract->setContractedDate($credit->getFirstPrepaiedDate($customer_data['CustomerMst']['id']),$customer_data['CustomerMst']['id'],$username);
    			if($ret['result']==false){ return $ret; }

    			$customer->setSeiyaku($customer_data['CustomerMst']['id'],$username);
    			$status_id = CS_CONTRACTED;
    			$status_nm ="成約";
    		}
       	if($customer_data['CustomerMst']['status_id'] == CS_CONTRACTED){

    		//挙式日及び挙式時間またはレセプション時間が設定されていなければ仮約定に戻す
    		if($status_manager->IsFilledContractedStatus($customer_data['CustomerMst']['id']) == false){

    			//成約日をクリアする
    			//$contract->clearContractedDate($customer_data['CustomerMst']['id'],$username);

    			$customer->setContracting($customer_data['CustomerMst']['id'],$username);
    			$status_id = CS_CONTRACTING;
    			$status_nm ="仮成約";
    		}
    	*/
    	if($customer_data['CustomerMst']['status_id'] == CS_INVOICED){

    		$ret = $customer->updateCustomerStatusIfWeddingFinishedByCustomerId($customer_data['CustomerMst']['id'],$username);
    		$status_id = $ret['status_id'];
    		$status_nm = $ret['status_nm'];

        }else if($customer_data['CustomerMst']['status_id'] == CS_UNPAIED){

        	$ret = $customer->updateCustomerStatusIfWeddingFinishedByCustomerId($customer_data['CustomerMst']['id'],$username);
    		$status_id = $ret['status_id'];
    		$status_nm = $ret['status_nm'];
        }

    	$tr->commit();

    	return array('result'=>true,
    			     'status_nm'=>$status_nm,
    			     'status_id'=>$status_id,
    			     'customer_cd'=>$customer_data['CustomerMst']['customer_cd'],
    	             'contracting_dt'=>$customer_data['CustomerMst']['contracting_dt']
    	             );
    }
}
?>