<?php
class CreditService extends AppModel {
	var $useTable = false;

	/**
	 * 指定月の入金情報を取得する
	 * @param unknown $credit_dt
	 */
	function GetCreditInfoByCreditMonth($credit_dt){

		App::import("Model", "CreditTrnView");
		$credit_view = new CreditTrnView();

		return $credit_view->find('all',array('conditions'=>array('SUBSTR(credit_dt,1,7)'=>$credit_dt),'order'=>'credit_dt asc'));
	}

	/**
	 * 指定の入金情報を取得する
	 * @param unknown $customer_id
	 */
	function GetCreditInfoOfCustomer($customer_id){

		App::import("Model", "CreditTrnView");
		$credit_view = new CreditTrnView();

		return $credit_view->find('all',array('conditions'=>array('customer_id'=>$customer_id),'order'=>'credit_dt asc'));
	}

	/**
	 *
	 * ユニークな入金年月を取得する
	 * @return 正常： 入金年月の配列
	 *         異常：NULL
	 */
	function getGroupOfCreditMonth(){
		$sql = "SELECT SUBSTR(credit_dt,1,7) credit_dt FROM credit_trns GROUP BY SUBSTR(credit_dt,1,7) Order by SUBSTR(credit_dt,1,7) desc";
		$data = $this->query($sql);

		$months = array();
		for($i=0;$i < count($data);$i++){
			$months[$i] = $data[$i][0]['credit_dt'];
		}
		return $months;
	}

	/**
	 * 入金の種類一覧を取得する
	 * @return multitype:multitype:NULL
	 */
	function getCreditTypeList(){
		App::import("Model", "CreditTypeMst");
		$type = new CreditTypeMst();
		$data = $type->find('all');
		$result = array();

		for($i=0;$i < count($data);$i++){
			$result[] = array("id"=>$data[$i]["CreditTypeMst"]["id"],
			                  "name"=>$data[$i]["CreditTypeMst"]["credit_type_nm"]);
		}
		return $result;
	}

	/**
	 *  挙式日が基準日より先の内金合計金額を取得
     *
	 */
	function getTotalPrepaidAmountAtfer($base_date){

		App::import("Model", "CreditTrnView");
		$credit = new CreditTrnView();

		$sql = "SELECT sum(amount) amount FROM credit_trn_views where SUBSTR(wedding_dt,1,10) > '".$base_date."' AND credit_type_id = ".NC_UCHIKIN;
		//$sql = "SELECT sum(amount) amount FROM credit_trn_views where SUBSTR(wedding_dt,1,10) > '".$base_date."' AND credit_type_id IN (".NC_UCHIKIN.",".NC_ZANKIN.")";
		$data = $this->query($sql);

		return !empty($data[0][0]['amount']) > 0 ? $data[0][0]['amount'] : 0;
	}

	/**
	 *  入金日が基準月の内金合計金額を取得
	 *
	 */
	function getTotalPrepaidAmountOfThisMonth($base_month){

		App::import("Model", "CreditTrn");
		$credit = new CreditTrn();

		$sql = "SELECT sum(amount) amount FROM credit_trns where SUBSTR(credit_dt,1,7) = '".$base_month."' AND credit_type_id = ".NC_UCHIKIN;
		$data = $this->query($sql);

		return !empty($data[0][0]['amount']) > 0 ? $data[0][0]['amount'] : 0;
	}

	/**
	 * 未挙式の請求書発行済み合計入金金額
	 *
	 */
	function getTotalInvoicedCreditAmountBeforeWedding(){

		App::import("Model", "CreditTrnView");
		$credit = new CreditTrnView();
		$sql = "SELECT sum(amount) amount FROM credit_trn_views where SUBSTR(wedding_dt,1,10) > '".date("Y-m-d")."' AND status_id = ".CS_INVOICED." AND credit_type_id IN  (".NC_UCHIKIN.",".NC_ZANKIN.")";
		$data = $this->query($sql);

		return !empty($data[0][0]['amount']) > 0 ? $data[0][0]['amount'] : 0;
	}

	/**
	 * 未挙式の請求書発行前合計入金金額
	 *
	 */
	function getTotalUninvoicedCreditAmountBeforeWedding(){

		App::import("Model", "CreditTrnView");
		$credit = new CreditTrnView();
		$sql = "SELECT sum(amount) amount FROM credit_trn_views where status_id IN (".CS_ESTIMATED.",".CS_CONTRACTING.",".CS_CONTRACTED.") AND credit_type_id IN  (".NC_UCHIKIN.",".NC_ZANKIN.")";
		$data = $this->query($sql);

		return !empty($data[0][0]['amount']) > 0 ? $data[0][0]['amount'] : 0;
	}


	/**
	 * 顧客名を全角かなで検索して、顧客コードを取得する
	 * @param unknown $full_nm
	 * @return multitype:multitype:NULL
	 */
	function getCustomerCodeByKana($full_nm){

		$sql = "SELECT id,customer_cd,status_id FROM customer_msts where concat(ifnull(grmls_kn,''), ifnull(grmfs_kn,'')) = '".$full_nm."' or ".
                                                                        "concat(ifnull(grmls_kn,''), ifnull(brdfs_kn,'')) = '".$full_nm."' or ".
                                                                        "concat(ifnull(brdls_kn,''), ifnull(brdfs_kn,'')) = '".$full_nm."' or ".
                                                                        "concat(ifnull(brdls_kn,''), ifnull(grmfs_kn,'')) = '".$full_nm."' ".
                                                                   "order by wedding_planned_dt desc;";
		$data = $this->query($sql);

		$customers = array();
		for($i=0;$i < count($data);$i++){
			$customers[] = array("customer_id"=> $data[$i]['customer_msts']['id'],
					             "customer_cd"=> $data[$i]['customer_msts']['customer_cd'],
		                         "status_id"=> $data[$i]['customer_msts']['status_id']);
		}
		return $customers;
	}

	/**
	 * CSVファイルを読み込んで入金情報を取得する
	 * @param unknown $filename
	 * @return multitype:boolean string |multitype:boolean multitype:multitype:unknown NULL
	 */
    function getCsvFileInfo($filename){

      	$csv  = array();
    	$data = file_get_contents($filename);
    	$data = mb_convert_encoding($data, 'UTF-8', 'SJIS');
    	$temp = tmpfile();
    	fwrite($temp, $data);
    	rewind($temp);

    	while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {

    		if(count($data) > 4){

    			$customer = $this->getCustomerCodeByKana(mb_convert_kana(preg_replace('/[ ]/', '', $data[4]),"KV"));
    		//	if(count($customer)==0){  return array("isSuccess"=>false , "message"=>"顧客名「".mb_convert_kana($data[4],"KV")."」に該当する顧客情報が見つかりませんでした。");   }

    			$csv[] = array("credit_dt"=>$data[0],
    					"amount"=>$data[2],
    					"customer_nm"=>mb_convert_kana($data[4],"KV"),
    					"customer_info"=>$customer
    			);
    		}
    	}
    	fclose($temp);
    	return array("isSuccess"=>true , "data"=>$csv);
    }

    /**
     * 入金情報の登録
     * @param unknown $data
     * @param unknown $username
     * @return multitype:boolean string |multitype:boolean unknown
     */
    function register($data , $username,$duplicate_acceptted){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	App::import("Model", "ContractTrn");
    	$contract = new ContractTrn();

    	App::import("Model", "CustomerMst");
    	$customer = new CustomerMst();

    	App::import("Model", "FundManagementTrn");
    	$fund = new FundManagementTrn();

    	App::import("Model", "StatusManager");
    	$status_manager = new StatusManager();

    	$customer_id = null;

    	//重複チェック
    	$duplicate_data = array();
    	$duplicate_data_id = array();

    	//ファイル内重複チェック
    	for($i=0; $i < count($data);$i++){
    	  for($j=$i+1; $j < count($data);$j++){
    		if($data[$j]['credit_customer_nm']==$data[$i]['credit_customer_nm'] &&
    		   $data[$j]['amount']==$data[$i]['amount'] &&
    		   $data[$j]['credit_dt']==$data[$i]['credit_dt']){
    			$duplicate_data[] = $data[$i]['credit_customer_nm'];
    			$duplicate_data_id[] = $data[$i]['credit_customer_nm'].$data[$i]['amount'].$data[$i]['credit_dt'];
    		}
    	  }
    	}
        //DB内重複チェック
    	for($i=0; $i < count($data);$i++){
    		if($this->isAlreadyCredittedAtSameDate($data[$i]['credit_customer_nm'],str_replace(',',"",$data[$i]['amount']),date('Ymd',strtotime($data[$i]['credit_dt'])))){
    			$duplicate_data[] = $data[$i]['credit_customer_nm'];
    			$duplicate_data_id[] = $data[$i]['credit_customer_nm'].$data[$i]['amount'].$data[$i]['credit_dt'];
    			}
    	}

    	if(empty($duplicate_acceptted)){
    		if(count($duplicate_data) > 0){
    			return array('result'=>false,'message'=>"duplicate",'reason'=>implode(':',$duplicate_data));
    		}
        }

    	for($i=0; $i < count($data);$i++){

    		//入金データの重複が不許可の場合は登録しない
    		$is_duplicate = false;
    		if($duplicate_acceptted == "false"){
    			for($k=0;$k < count($duplicate_data_id);$k++){

    				if($data[$i]['credit_customer_nm'].$data[$i]['amount'].$data[$i]['credit_dt'] == $duplicate_data_id[$k]){
    					$is_duplicate=true;
    					break;
    				}
    			}
    		}
    		if($duplicate_acceptted == "false" && $is_duplicate == true){ continue; }

    		$customer_data = $customer->find('all',array('fields'=>array('id','status_id'),'conditions'=>array('customer_cd'=>$data[$i]['customer_cd'])));

    		//物販・ＧＩＦＴ・その他用の顧客の場合は顧客情報を自動生成してから入金情報を登録する
    		if($data[$i]['credit_type_id'] == NC_BUPPAN || $data[$i]['credit_type_id'] == NC_GIFT || $data[$i]['credit_type_id'] == NC_EXTRA){

    		  $names = explode(" ",$data[$i]['credit_customer_nm']);
    		  $ls_nm = count($names) > 0 ? $names[0] : null;
    		  $fs_nm = count($names) > 1 ? $names[1] : null;
    		  $credit_dt = substr($data[$i]['credit_dt'],0,4).sprintf("%02d",substr($data[$i]['credit_dt'],5));

    		  $customer_status = null;
    		  if($data[$i]['credit_type_id'] == NC_BUPPAN){
    		  	 $customer_status = CS_BUPPAN;
    		  }else if($data[$i]['credit_type_id'] == NC_GIFT){
    		     $customer_status = CS_GIFT;
    		  }else if($data[$i]['credit_type_id'] == NC_EXTRA){
    		  	 $customer_status = CS_EXTRA;
    		  }

    		  $ret = $customer->regiterCustomerForCredit($customer_status,$ls_nm ,$fs_nm ,$credit_dt,$username);
    		  if($ret['result'] == false){ return $ret; }

    		  $customer_id = $ret['newID'];

    		//通常の登録
    		}else{
    		  if(count($customer_data) == 0){ return array('result'=>false,'message'=>"顧客番号に該当する顧客が存在しません。",'reason'=>$data[$i]['customer_cd']); }
    		  if(count($customer_data) > 1){ return array('result'=>false,'message'=>"顧客番号に該当する顧客が多数存在します。",'reason'=>$data[$i]['customer_cd']); }

    		  if($customer_data[0]['CustomerMst']['status_id'] != CS_CONTRACTING &&
    		     $customer_data[0]['CustomerMst']['status_id'] != CS_CONTRACTED  &&
    		     $data[$i]['credit_type_id'] == NC_UCHIKIN){
    		  	return array('result'=>false,'message'=>"仮約定又は約定以外のステータス時に内金の入金はできません。",'reason'=>'');
    		  }
    		  $customer_id = $customer_data[0]['CustomerMst']['id'];
    		}

    		$credit_data = array(
    				"customer_id"=>$customer_id,
    				"credit_customer_nm"=>$data[$i]['credit_customer_nm'],
    				"amount"=>str_replace(',',"",$data[$i]['amount']),
    				"credit_dt"=>$data[$i]['credit_dt'],
    				"credit_type_id"=>$data[$i]['credit_type_id'],
    				"reg_nm"=>$username,
    				"reg_dt"=>date('Y-m-d H:i:s')
    		);
    		$credit->create();
    		if($credit->save($credit_data)==false){
    			return array('result'=>false,'message'=>"入金情報のの新規作成に失敗しました。",'reason'=>$credit->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    		}

    		if($customer_data != null){
    		//ステータスが仮約定以上の場合は資金管理テーブルの内金情報を更新する
    		if($customer_data[0]['CustomerMst']['status_id'] >= CS_CONTRACTING && $data[$i]['credit_type_id'] == NC_UCHIKIN){
    			$contract_data = $contract->find('all',array('fields'=>array('id'), 'conditions'=>array('customer_id'=>$customer_id)));
                if(count($contract_data) == 0){ return array('result'=>false,'message'=>"契約テーブルが見つかりません。",'reason'=>$data[$i]['customer_cd']); }
    			$depoit = $this->getPrepaidAmount($customer_id);

    			if($fund->updatePrepaiedAmount($contract_data[0]['ContractTrn']['id'],$depoit['amount'],$depoit['credit_dt'])==false){
    				return array('result'=>false,'message'=>"資金管理情報の更新に失敗しました。",'reason'=>$fund->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    			}
    		}

    		/* ステータスが仮約定の場合は条件により成約に移行する */
    		if($customer_data[0]['CustomerMst']['status_id'] == CS_CONTRACTING){

    			//内金入金済み
    			if($this->isPrepaidIncludingZero($customer_id)){
			//お内金入金日を成約日に設定
    				$ret =$contract->setContractedDate($this->getFirstPrepaiedDate($customer_id),$customer_id,$username);
    				if($ret['result']==false){ return $ret; }

    				$ret = $customer->setSeiyaku($customer_id, $username);
    				if($ret['result']==false){ return $ret; }
    			}


    		//請求金額が全額入金されている場合はステータスを「挙式完了・入金済み」に移行する
    		}else if($customer_data[0]['CustomerMst']['status_id'] == CS_UNPAIED){

    		     if($this->isInvoiceMatchForCredit($customer_id)){
    		     	$ret = $customer->setPaied($customer_id, $username);
    		     	if($ret['result']==false){ return $ret; }
    		     }

    		//請求金額以上に入金されている場合はステータスを「挙式完了・未入金」に戻す
    		}else if($customer_data[0]['CustomerMst']['status_id'] == CS_PAIED){

    			if($this->isInvoiceMatchForCredit($customer_id)==false){
    				$ret = $customer->setUnpaied($customer_id, $username);
    				if($ret['result']==false){ return $ret; }
    			}
    		}
    	   }
    	 }

    	$tr->commit();
    	return array('result'=>true);
    }

    /**
     * 入金情報の更新
     * @param unknown $data
     * @param unknown $username
     */
    function update($data , $username){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "ContractTrn");
    	$contract = new ContractTrn();

    	App::import("Model", "CustomerTrn");
    	$customer = new CustomerTrn();

    	App::import("Model", "FundManagementTrn");
    	$fund = new FundManagementTrn();

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();
    	$fields = array('credit_type_id' ,'note' ,'upd_nm','upd_dt');

    	$data['upd_nm'] = $username;
    	$data['upd_dt'] = date('Y-m-d H:i:s');

    	$credit->id = $data['id'];

    	if($credit->save($data,false,$fields)==false){
    		return array('result'=>false,'message'=>"入金情報の更新に失敗しました。",'reason'=>$credit->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}

    	//ステータスが成約以上の場合は資金管理テーブルの内金情報を更新する
    	if($data['status_id'] == CS_CONTRACTED || $data['status_id'] == CS_INVOICED ||
    	   $data['status_id'] == CS_UNPAIED || $data['status_id'] == CS_PAIED){
    	        $contract_data = $contract->find('all',array('fields'=>array('id'), 'conditions'=>array('customer_id'=>$data['customer_id'])));
                if(count($contract_data) == 0){ return array('result'=>false,'message'=>"契約テーブルが見つかりません。",'reason'=>$data['customer_id']); }
    			$depoit = $this->getPrepaidAmount($data['customer_id']);

    			if($fund->updatePrepaiedAmount($contract_data[0]['ContractTrn']['id'],$depoit['amount'],$depoit['credit_dt'])==false){
    				return array('result'=>false,'message'=>"資金管理情報の更新に失敗しました。",'reason'=>$fund->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    			}
    	}

    	//請求金額と入金金額を比較してステータスを変更する
    	if($data['status_id'] == CS_UNPAIED && $this->isInvoiceMatchForCredit($data['customer_id'])){
            $ret = $customer->setPaied($data['customer_id'], $username);
    		if($ret['result']==false){ return $ret; }

    	//請求金額以上に入金されている場合はステータスを「挙式完了・未入金」に戻す
    	}else if($customer_data[0]['CustomerMst']['status_id'] == CS_PAIED){

    			if($this->isInvoiceMatchForCredit($data['customer_id'])==false){
    				$ret = $customer->setUnpaied($data['customer_id'], $username);
    				if($ret['result']==false){ return $ret; }
    			}
    	}

    	$tr->commit();
    	return array('result'=>true);
    }

    /**
     * 入金情報の削除
     * @param unknown $data
     * @param unknown $username
     */
    function delete($id,$customer_id,$customer_status_id,$username){

    	$tr = ClassRegistry::init('TransactionManager');
    	$tr->begin();

    	App::import("Model", "ContractTrn");
    	$contract = new ContractTrn();

    	App::import("Model", "FundManagementTrn");
    	$fund = new FundManagementTrn();

    	App::import("Model", "CustomerMst");
    	$customer = new CustomerMst();

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	App::import("Model", "StatusManager");
    	$status_manager = new StatusManager();

    	if($credit->delete($id)==false){
    		return array('result'=>false,'message'=>"入金情報の削除に失敗しました。",'reason'=>$credit->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    	}

    	//ステータスが成約以上の場合は資金管理テーブルの内金情報を更新する
    	if($customer_status_id == CS_CONTRACTED || $customer_status_id == CS_INVOICED ||
    	   $customer_status_id == CS_UNPAIED || $customer_status_id == CS_PAIED){
    		$contract_data = $contract->find('all',array('fields'=>array('id'), 'conditions'=>array('customer_id'=>$customer_id)));
    		if(count($contract_data) == 0){ return array('result'=>false,'message'=>"契約テーブルが見つかりません。",'reason'=>$data[$i]['customer_cd']); }
    		$depoit = $this->getPrepaidAmount($customer_id);

    		if($fund->updatePrepaiedAmount($contract_data[0]['ContractTrn']['id'],$depoit['amount'],$depoit['credit_dt'])==false){
    			return array('result'=>false,'message'=>"資金管理情報の更新に失敗しました。",'reason'=>$fund->getDbo()->error."[".date('Y-m-d H:i:s')."]");
    		}
    	}

    	//「請求書発行済」以降はお内金を０円にできない
    	if($customer_status_id == CS_INVOICED || $customer_status_id == CS_UNPAIED || $customer_status_id == CS_PAIED){

    		if($this->isPrepaid($customer_id)==false){
    			return array('result'=>false,'message'=>"入金情報の削除に失敗しました。" , 'reason'=>"「請求書発行済」以降はお内金を０円にできません" );
    		}
    	}

        /* ステータスが成約の場合は条件により仮約定に戻す */
    	if($customer_status_id == CS_CONTRACTED){

    		    //内金入金がない
    			if($this->isPrepaidIncludingZero($customer_id)==false){

    				//成約日をクリアする
    				$ret =$contract->clearContractedDate($customer_id,$username);
    				if($ret['result']==false){ return $ret; }

    				$ret = $customer->setContracting($customer_id, $username);
    				if($ret['result']==false){ return $ret; }
    			}

    	//請求金額が全額入金されている場合はステータスを「挙式完了・入金済み」に移行する
    	}else if($customer_status_id == CS_UNPAIED){

    		     if($this->isInvoiceMatchForCredit($customer_id)){
    		     	$ret = $customer->setPaied($customer_id, $username);
    		     	if($ret['result']==false){ return $ret; }
    		     }

    	//請求金額以上に入金されている場合はステータスを「挙式完了・未入金」に戻す
    	}else if($customer_status_id == CS_PAIED){

    			if($this->isInvoiceMatchForCredit($customer_id)==false){
    				$ret = $customer->setUnpaied($customer_id, $username);
    				if($ret['result']==false){ return $ret; }
    			}
    	}

    	$tr->commit();
    	return array('result'=>true);
    }

    /**
     * 内金金額合計を取得
     * @param unknown $customer_id
     */
    function getPrepaidAmount($customer_id){

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	$sql = "SELECT sum(amount) amount , max(credit_dt) credit_dt FROM credit_trns where customer_id = ".$customer_id." AND credit_type_id = ".NC_UCHIKIN;
		$data = $this->query($sql);

		return !empty($data[0][0]['amount']) > 0 ? array('amount'=>$data[0][0]['amount'],'credit_dt'=>$data[0][0]['credit_dt']) :
		                          array('amount'=>0,'credit_dt'=>null);
    }

    /**
     * お内金が入金されていることを確認
     * @param unknown $customer_id
     * @return Ambigous <multitype:NULL , multitype:number NULL >
     */
    function isPrepaid($customer_id){

    	$sql = "SELECT amount FROM credit_trns where customer_id = ".$customer_id." AND credit_type_id = ".NC_UCHIKIN;
    	$data = $this->query($sql);

    	if(empty($data[0]['credit_trns']['amount'])){ return false; }
    	return $data[0]['credit_trns']['amount'] > 0 ? true : false;
    }

    /**
     * ０入金を含むお内金が入金されていることを確認(内金なしでも成約に移行させるための処置)
     * @param unknown $customer_id
     * @return Ambigous <multitype:NULL , multitype:number NULL >
     */
    function isPrepaidIncludingZero($customer_id){

    	$sql = "SELECT amount FROM credit_trns where customer_id = ".$customer_id." AND credit_type_id = ".NC_UCHIKIN;
    	$data = $this->query($sql);

    	return count($data) > 0 && $data[0]['credit_trns']['amount'] >= 0 ? true : false;
    }

    /**
     * 入金金額合計を取得
     * @param unknown $customer_id
     */
    function getCreditAmount($customer_id){

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	$sql = "SELECT sum(amount) amount FROM credit_trns where customer_id = ".$customer_id;
    	$data = $this->query($sql);

    	return !empty($data[0][0]['amount']) > 0 ? $data[0][0]['amount'] : 0;
    }

    /**
     * 内金除外入金を除いた入金金額合計を取得
     * @param unknown $customer_id
     */
    function getCreditAmountWithoutUchikinException($customer_id){

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	$sql = "SELECT sum(amount) amount FROM credit_trns where uchikin_exception_flg=0 and customer_id = ".$customer_id;
    	$data = $this->query($sql);

    	return !empty($data[0][0]['amount']) > 0 ? $data[0][0]['amount'] : 0;
    }

    /**
     * 一番初めのお内金入金日を取得
     * @param unknown $customer_id
     * @return NULL
     */
    function getFirstPrepaiedDate($customer_id){

    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	$sql = "SELECT min(credit_dt) credit_dt FROM credit_trns where customer_id = ".$customer_id;
    	$data = $this->query($sql);

    	return empty($data[0][0]['credit_dt']) ? null : $data[0][0]['credit_dt'];
    }

    /**
     * 請求金額が全て入金されているか確認
     * @param unknown $customer_id
     * @return boolean
     */
    function isInvoiceMatchForCredit($customer_id){

    	App::import("Model", "EstimateService");
        $estimate = new EstimateService();

        $credit = $this->getCreditAmount($customer_id);
        $invoice = $estimate->getCustomerEstimateSummary($customer_id);

        return $credit == $invoice['total'] ? true: false;
    }

    /**
     * 同日、同金額、同振込名義名が存在するか確認
     * @param unknown $credit_nm
     * @param unknown $credit_amount
     * @param unknown $credit_dt
     * @return boolean
     */
    function isAlreadyCredittedAtSameDate($credit_nm,$credit_amount,$credit_dt){
    	App::import("Model", "CreditTrn");
    	$credit = new CreditTrn();

    	if($credit->find("count",array('conditions'=>array('credit_customer_nm'=>$credit_nm,'amount'=>$credit_amount,"DATE_FORMAT(credit_dt, '%Y%m%d' )"=>$credit_dt))) > 0){
    		return true;
    	}else{
    		return false;
    	}
    }
}
?>
