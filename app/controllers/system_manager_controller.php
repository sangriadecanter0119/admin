<?php
set_time_limit(300);
class SystemManagerController extends AppController
{

 public $name = 'SystemManager';
 public $uses = array('UserView','GoodsKbnMst','DataConvertManager','CustomerMst','EstimateTrn','ContractTrn','ContractTrnView','CreditService',
 		              'CreditTrn','CreditTrnView','FundManagementTrn','EstimateTrnView');
 public $layout = 'edit_mode';
 public $components = array('Auth');
 public $helpers = array('common');

 //各マスタ登録用のメインメニュー画面
 function index($type=null)
 {
 	if($type != null){
 	  /* 更新開始 */

      //$result = $this->DataConvertManager->execute();
      $result = array('result'=>false);
 		$this->set("result",$result);
 	}
 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","管理画面");
    $this->set("user",$this->Auth->user());
 }

 //ユーザー一覧画面
 function userMaster()
 {
 	$this->set("data",$this->UserView->find('all'));

 	$this->set("menu_customers","");
 	$this->set("menu_customer","disable");
 	$this->set("menu_fund","");

 	$this->set("sub_title","ユーザー管理");
 	$this->set("user",$this->Auth->user());
 }


 /**
  * [開発用]挙式済みの顧客ステータスの更新
  */
 function updateStatusDate(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$tr = ClassRegistry::init('TransactionManager');
 	$tr->begin();

 	$count1=0;
 	$count2=0;
 	$customer = $this->CustomerMst->find('all',array('fields'=>array('id','status_id'),'conditions'=>array("status_id"=>array(3,7,8))));
 	//$customer = $this->CustomerMst->find('all',array('fields'=>array('id','status_id'),'conditions'=>array("status_id"=>array(CS_CONTRACTED,CS_CANCEL,CS_POSTPONE))));

 	for($i=0; $i < count($customer);$i++){

 		$customer_id = $customer[$i]['CustomerMst']['id'];
 		$estimate = $this->EstimateTrn->find('all',array('fields'=>array('id','reg_dt'),'conditions'=>array("customer_id"=>$customer_id,"adopt_flg"=>1)));

 		if(count($estimate) == 0){ return json_encode(array("result"=>false,"message"=>"採用見積がみつかりません。","reason"=>$customer_id)); }

 		$reg_dt = $estimate[0]['EstimateTrn']['reg_dt'];
 		$estimate_id = $estimate[0]['EstimateTrn']['id'];

 		$count1++;
 		if($this->CustomerMst->updateAll(array('estimate_issued_dt'=>"'".$reg_dt."'",'contracting_dt'=>"'".$reg_dt."'"),array('id'=>$customer_id))==false){
 			return json_encode(array('result'=>false,'message'=>"見積書発行日及び仮約定日の更新に失敗しました。",'reason'=>$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 		}

 		if($customer[$i]['CustomerMst']['status_id'] == 3){
 		  $count2++;
 		  if($this->EstimateTrn->updateAll(array('invoice_issued_dt'=>"'".$reg_dt."'"),array('id'=>$estimate_id))==false){
 			 return json_encode(array('result'=>false,'message'=>"請求書発行日の更新に失敗しました。",'reason'=>$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 		  }
 		}

 	}
 	$tr->commit();
 	return json_encode(array('result'=>true,'message'=>'見積・仮約定:'.$count1.':請求書:'.$count2));
 }

 /**
  * [開発用]挙式情報のコピー
  */
 function copyCustomerBasicInfo(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$tr = ClassRegistry::init('TransactionManager');
 	$tr->begin();

 	$count=0;
 	$contract = $this->ContractTrn->find('all');

 	for($i=0; $i < count($contract);$i++){

 		if(empty($contract[$i]['ContractTrn']['wedding_dt']) || (empty($contract[$i]['ContractTrn']['wedding_time']) &&
 		   empty($contract[$i]['ContractTrn']['reception_time']))){
 			return json_encode(array('result'=>false,'message'=>"挙式情報がNULLです。",'reason'=>$contract[$i]['ContractTrn']['customer_id']));
 		}

 		$count++;
 		if($this->CustomerMst->updateAll(
 				array('wedding_planned_dt'=>"'".$contract[$i]['ContractTrn']['wedding_dt']."'",
 					  'wedding_planned_place'=>"'".$contract[$i]['ContractTrn']['wedding_place']."'",
 				      'wedding_planned_time'=>"'".$contract[$i]['ContractTrn']['wedding_time']."'",
 					  'reception_planned_time'=>"'".$contract[$i]['ContractTrn']['reception_time']."'")
 				,array('id'=>$contract[$i]['ContractTrn']['customer_id']))==false){
 			return json_encode(array('result'=>false,'message'=>"挙式情報の更新に失敗しました。",'reason'=>$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
 		}

 	}
 	$tr->commit();
 	return json_encode(array('result'=>true,'message'=>'挙式:'.$count));
 }

 /**
  * [開発用]内金0円更新
  */
function updatePrepaied(){

	$this->layout = '';
	$this->autoRender =false;
	configure::write('debug', 0);

	$tr = ClassRegistry::init('TransactionManager');
	$tr->begin();

	$count=0;
	$customer = $this->CustomerMst->find('all',array('fields'=>array('id'),'conditions'=>array('status_id'=>array(CS_CONTRACTED,CS_INVOICED,CS_UNPAIED,CS_PAIED,CS_CANCEL,CS_POSTPONE))));

	for($i=0; $i < count($customer);$i++){

		$credit = $this->CreditService->getPrepaidAmount($customer[$i]['CustomerMst']['id']);
		if($credit['amount'] == 0){
			//return json_encode(array('result'=>false,'message'=>"入金情報の更新に失敗しました。",'reason'=>"IN:".$customer[$i]['CustomerMst']['id'].":".($credit['amount'])));
			$credit_data = array(
					"customer_id"=>$customer[$i]['CustomerMst']['id'],
					"credit_customer_nm"=>'仮入金',
					"amount"=>1,
					"credit_type_id"=>NC_UCHIKIN,
					"credit_dt"=>'2000/01/01',
					"reg_nm"=>"admin",
					"reg_dt"=>date('Y-m-d H:i:s')
			);
			$this->CreditTrn->create();
			if($this->CreditTrn->save($credit_data)==false){
				return json_encode(array('result'=>false,'message'=>"入金情報の更新に失敗しました。",'reason'=>$this->CreditTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
			}
		  $count++;
		}
	}
	$tr->commit();
	return json_encode(array('result'=>true,'message'=>'入金更新:'.$count));
}

/**
 * [開発用]内金0円削除
 */
function deletePrepaied(){

	$this->layout = '';
	$this->autoRender =false;
	configure::write('debug', 0);

	$tr = ClassRegistry::init('TransactionManager');
	$tr->begin();

	$count=0;
	$credit = $this->CreditTrn->find('all',array('fields'=>array('id','customer_id'),'conditions'=>array('amount'=>1)));

	for($i=0; $i < count($credit);$i++){

			if($this->CreditTrn->delete($credit[$i]['CreditTrn']['id'])==false){
				return json_encode(array('result'=>false,'message'=>"入金情報の削除に失敗しました。",'reason'=>$this->CreditTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
			}

			//資金管理テーブルの内金情報を更新する
			$contract_data = $this->ContractTrn->find('all',array('fields'=>array('id'), 'conditions'=>array('customer_id'=>$credit[$i]['CreditTrn']['customer_id'])));
			if(count($contract_data) == 0){ return json_encode(array('result'=>false,'message'=>"契約テーブルが見つかりません。",'reason'=>$credit[$i]['CreditTrn']['customer_id'])); }
			$depoit = $this->CreditService->getPrepaidAmount($credit[$i]['CreditTrn']['customer_id']);

			if($this->FundManagementTrn->updatePrepaiedAmount($contract_data[0]['ContractTrn']['id'],$depoit['amount'],$depoit['credit_dt'])==false){
				return json_encode(array('result'=>false,'message'=>"資金管理情報の更新に失敗しました。",'reason'=>$this->FundManagementTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
			}

			//請求金額と入金金額を比較してステータスを変更する
			$ret = $this->CreditService->updateCustomerStatusIfPaymentMatch($credit[$i]['CreditTrn']['customer_id'],$username);
			if($ret['result']==false){ return json_encode($ret); }
		  $count++;
	}
	$tr->commit();
	return json_encode(array('result'=>true,'message'=>'入金削除:'.$count));
}


/**
 * [開発用]内金と入金日を資金管理テーブルに転記
 */
function updateFund(){

	$this->layout = '';
	$this->autoRender =false;
	configure::write('debug', 0);

	$tr = ClassRegistry::init('TransactionManager');
	$tr->begin();

	$count=0;
	$contract_data = $this->ContractTrn->find('all',array('fields'=>array('id','customer_id')));

	for($i=0; $i < count($contract_data);$i++){

			$depoit = $this->CreditService->getPrepaidAmount($contract_data[$i]['ContractTrn']['customer_id']);

			if($this->FundManagementTrn->updatePrepaiedAmount($contract_data[$i]['ContractTrn']['id'],$depoit['amount'],$depoit['credit_dt'])==false){
					return json_encode(array('result'=>false,'message'=>"資金管理情報の更新に失敗しました。",'reason'=>$this->FundManagementTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"));
			}
		  $count++;
	}
	$tr->commit();
	return json_encode(array('result'=>true,'message'=>'資金管理更新:'.$count));
}

/**
 *
 * [開発用]挙式ファイルアップロード画面を表示する
 */
function fileUploadForm()
{
	configure::write('debug', 0);
	$this->layout = '';
}

/**
 * [開発用]挙式ファイルの取り込み
 */
function uploadWeddingFile(){
      $this->layout = "";

 	  if (is_uploaded_file($this->data['ImgForm']['ImgFile']['tmp_name']) && end(explode(".",$this->data['ImgForm']['ImgFile']['name'])) == "csv") {

 	  	$tr = ClassRegistry::init('TransactionManager');
 	  	$tr->begin();

 	  	$filename = $this->data['ImgForm']['ImgFile']['tmp_name'];
 	  	$data = file_get_contents($filename);
 	  	$data = mb_convert_encoding($data, 'UTF-8', 'SJIS');
 	  	$temp = tmpfile();
 	  	fwrite($temp, $data);
 	  	rewind($temp);

 	  	$count = 0;
 	  	while (($data = fgetcsv($temp, 0, ",")) !== FALSE) {

 	  		    $customer_cd = substr($data[1],0,7).substr($data[6],5,2).substr($data[6],8,2).substr($data[6],2,2);
 	  		    $customer_id = $data[0];
 	  			$customer_data = array(
 	  					"customer_cd"=>"'".$customer_cd."'",
 	  					"wedding_planned_dt"=>"'".$data[6]."'",
 	  					"wedding_planned_time"=>"'".$data[7]."'",
 	  					"reception_planned_time"=>"'".$data[8]."'"
 	  			);

 	  			$contract_data = array(
 	  					"wedding_dt"=>"'".$data[6]."'",
 	  					"wedding_time"=>"'".$data[7]."'",
 	  					"reception_time"=>"'".$data[8]."'"
 	  			);
 	  	       if($this->CustomerMst->updateAll($customer_data,array('id'=>$customer_id))==false){
 	  	          $this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false" ,'message'=>"顧客マスタの更新に失敗しました。:".$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]"))));
 	  	          return;
 	    	   }

 	    	   if($this->ContractTrn->updateAll($contract_data,array('customer_id'=>$customer_id))==false){
 	    	   	  $this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false" ,'message'=>"契約マスタの更新に失敗しました。:".$this->ContractTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]"))));
 	    	   	  return;
 	    	   }
 	    	   $count++;
 	  	 }
 	    fclose($temp);
 	  	$tr->commit();

 	  	$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"true" ,'message'=>"ファイル取り込みに成功しました。".$count))));
 	  }else{
 		$this->set("msg",json_encode(array('data'=>array('isSuccess'=>"false", 'message'=> "ファイルの種類が違います。(CSVファイル)　　ファイルサイズの上限は128Mです。"))));
 	  }
 }


 /**
  * [開発用]請求書年月消去してステータスを成約に戻す
  */
 function clearInvoiceDate(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$tr = ClassRegistry::init('TransactionManager');
 	$tr->begin();

 	$count=0;
 	$contract = $this->ContractTrnView->find('all',array('fields'=>array('customer_id'),'conditions'=>array("date_format(wedding_dt,'%Y%m%d') >="=>"20150101","status_id"=>array(CS_INVOICED))));

 	for($i=0; $i < count($contract);$i++){

 		       $customer_id = $contract[$i]['ContractTrnView']['customer_id'];
 	           $contract_data = array(
 	  					"invoice_issued_dt"=>null
 	  			);
 	  	       if($this->EstimateTrn->updateAll($contract_data,array('customer_id'=>$customer_id))==false){
 	  	          return json_encode(array('data'=>array('isSuccess'=>"false" ,'message'=>"見積の更新に失敗しました。:".$this->EstimateTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]")));
 	    	   }
 	    	   $this->CustomerMst->setSeiyaku($customer_id,'admin');
 			$count++;
 	}
 	$tr->commit();
 	return json_encode(array('result'=>true,'message'=>'更新:'.$count));
 }


 /**
  * [開発用]「仮約定」以上で採用見積がない顧客リスト
  */
 function getNonAdoptEstimate(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$customer_cd = "";
 	$customer = $this->CustomerMst->find('all',array('fields'=>array('id','customer_cd','status_id'),
 			  'conditions'=>array("status_id"=>array(CS_CONTRACTING,CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED,CS_CANCEL,CS_POSTPONE))));
 			//'conditions'=>array("status_id"=>array(CS_CONTRACTING,CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED))));

 	for($i=0; $i < count($customer);$i++){
 		$found = false;
 		$estimate = $this->EstimateTrn->find('all',array('fields'=>array('adopt_flg'),
 			    'conditions'=>array("customer_id"=>$customer[$i]['CustomerMst']['id'])));

 		for($j=0; $j < count($estimate);$j++){
 			if($estimate[$j]['EstimateTrn']['adopt_flg'] == 1){
 			  $found = true;
 			  break;
 			}
 		}
 		if($found == false){
 			$customer_cd .= "[".$customer[$i]['CustomerMst']['customer_cd'].":".$customer[$i]['CustomerMst']['status_id']."]";
 			//$customer_cd++;
 		}
 	}
 	return json_encode(array('result'=>true,'message'=>'件数:'.$customer_cd));
 }

 /**
  * [開発用]「成約」以上「キャンセル」以下でお内金がない顧客リスト
  */
 function getNonPrepaied(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$customer_cd = "";
 	$customer = $this->CustomerMst->find('all',array('fields'=>array('id','customer_cd','status_id'),
 			 'conditions'=>array("status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED,CS_CANCEL,CS_POSTPONE))));

 	for($i=0; $i < count($customer);$i++){
 		$found = false;
 		$credit = $this->CreditTrn->find('all',array('fields'=>array('amount','credit_type_id'),
 				'conditions'=>array("customer_id"=>$customer[$i]['CustomerMst']['id'])));

 		for($j=0; $j < count($credit);$j++){
 			if($credit[$j]['CreditTrn']['credit_type_id'] == NC_UCHIKIN && $credit[$j]['CreditTrn']['amount'] > 0){
 				$found = true;
 				break;
 			}
 		}
 		if($found == false){
 			$customer_cd .= "[".$customer[$i]['CustomerMst']['customer_cd'].":".$customer[$i]['CustomerMst']['status_id']."]";
 			//$customer_cd++;
 		}
 	}

 	return json_encode(array('result'=>true,'message'=>'件数:'.$customer_cd));

 }

 /**
  *  [開発用]「問い合わせ」「新規接客」ステータスで見積が作成されている顧客のステータスを変更
  */
 function moveToEstimateStatus(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$tr = ClassRegistry::init('TransactionManager');
 	$tr->begin();

 	$count=0;
 	$estimate = $this->EstimateTrnView->find('all',array('fields'=>array('distinct (customer_id) as customer_id'),'conditions'=>array("status_id"=>array(CS_CONTACT))));

 	for($i=0; $i < count($estimate);$i++){

 		$customer_id = $estimate[$i]['EstimateTrnView']['customer_id'];

 		$estimate_issed_dt = $this->EstimateTrn->find('all',array('fields'=>array('min(reg_dt) as reg_dt'),'conditions'=>array("customer_id"=>$customer_id)));

 		if($this->CustomerMst->updateAll(array("estimate_issued_dt"=>"'".$estimate_issed_dt[0][0]['reg_dt']."'"),array('id'=>$customer_id))==false){
 			return json_encode(array('data'=>array('isSuccess'=>"false" ,'message'=>"見積提出日の更新に失敗しました。:".$this->CustomerMst->getDbo()->error."[".date('Y-m-d H:i:s')."]")));
 		}
 		$this->CustomerMst->setEstimated($customer_id,'admin');
 		$count++;
 	}

 	$tr->commit();
 	return json_encode(array('result'=>true,'message'=>'更新：'.$count.'件'));
 }

 /**
  * 内金入金日で成約日を更新する
  */
 function updateContractDate(){

 	$this->layout = '';
 	$this->autoRender =false;
 	configure::write('debug', 0);

 	$tr = ClassRegistry::init('TransactionManager');
 	$tr->begin();

 	$count=0;
 	$customer = $this->CustomerMst->find('all',array('fields'=>array('id'),
 		                            	 'conditions'=>array("status_id"=>array(CS_CONTRACTED,CS_INVOICED,CS_PAIED,CS_UNPAIED,CS_CANCEL,CS_POSTPONE))));

 	for($i=0; $i < count($customer);$i++){

 		$customer_id = $customer[$i]['CustomerMst']['id'];

 		$credit_dt = $this->CreditTrn->find('all',array('fields'=>array('max(credit_dt) as credit_dt'),'conditions'=>array("NOT"=>array("credit_customer_nm"=>"仮入金"), "credit_type_id"=>NC_UCHIKIN,"customer_id"=>$customer_id)));

  	if(empty($credit_dt[0][0]['credit_dt']) == false){

 		if($this->ContractTrn->updateAll(array("contract_dt"=>"'".$credit_dt[0][0]['credit_dt']."'"),array('customer_id'=>$customer_id))==false){
 			return json_encode(array('data'=>array('isSuccess'=>"false" ,'message'=>"成約日の更新に失敗しました。:".$this->ContractTrn->getDbo()->error."[".date('Y-m-d H:i:s')."]")));
 		}
 		$count++;
 	}

 }

 	$tr->commit();
 	return json_encode(array('result'=>true,'message'=>'更新：'.$count.'件'));
 }
}
?>